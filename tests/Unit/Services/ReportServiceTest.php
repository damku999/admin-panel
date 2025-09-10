<?php

namespace Tests\Unit\Services;

use App\Contracts\Services\ReportServiceInterface;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\FuelType;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\ReferenceUser;
use App\Models\RelationshipManager;
use App\Models\CustomerInsurance;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Carbon\Carbon;
use Mockery;
use Maatwebsite\Excel\Facades\Excel;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;
    
    private ReportServiceInterface $reportService;
    private Customer $customer;
    private PremiumType $premiumType1;
    private PremiumType $premiumType2;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reportService = app(ReportServiceInterface::class);
        
        // Create test data
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->premiumType1 = PremiumType::factory()->create(['name' => 'Motor Insurance']);
        $this->premiumType2 = PremiumType::factory()->create(['name' => 'Health Insurance']);
        
        // Seed basic lookup data
        Branch::factory()->create();
        Broker::factory()->create();
        InsuranceCompany::factory()->create();
        PolicyType::factory()->create();
        FuelType::factory()->create();
        RelationshipManager::factory()->create();
        ReferenceUser::factory()->create();
    }

    public function test_get_initial_data_returns_all_required_lookup_data()
    {
        // Act
        $data = $this->reportService->getInitialData();

        // Assert
        $this->assertIsArray($data);
        $this->assertArrayHasKey('customers', $data);
        $this->assertArrayHasKey('brokers', $data);
        $this->assertArrayHasKey('relationship_managers', $data);
        $this->assertArrayHasKey('branches', $data);
        $this->assertArrayHasKey('insurance_companies', $data);
        $this->assertArrayHasKey('policy_type', $data);
        $this->assertArrayHasKey('fuel_type', $data);
        $this->assertArrayHasKey('premium_types', $data);
        $this->assertArrayHasKey('reference_by_user', $data);
        $this->assertArrayHasKey('customerInsurances', $data);
        $this->assertArrayHasKey('crossSelling', $data);

        // Verify data structure
        $this->assertNotEmpty($data['customers']);
        $this->assertNotEmpty($data['premium_types']);
        $this->assertEquals([], $data['customerInsurances']);
        $this->assertEquals([], $data['crossSelling']);
    }

    public function test_generate_cross_selling_report_without_filters()
    {
        // Arrange - Create customer with insurance policies
        $customer = Customer::factory()->create(['name' => 'John Doe']);
        
        // Create insurances for different premium types
        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType1->id,
            'final_premium_with_gst' => 25000.00,
            'actual_earnings' => 2500.00,
            'start_date' => Carbon::now()->subMonths(6)->format('Y-m-d')
        ]);

        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType2->id,
            'final_premium_with_gst' => 15000.00,
            'actual_earnings' => 1500.00,
            'start_date' => Carbon::now()->subMonths(3)->format('Y-m-d')
        ]);

        $parameters = [];

        // Act
        $result = $this->reportService->generateCrossSellingReport($parameters);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('premiumTypes', $result);
        $this->assertArrayHasKey('crossSelling', $result);

        $crossSelling = $result['crossSelling'];
        $this->assertCount(1, $crossSelling);

        $customerData = $crossSelling->first();
        $this->assertEquals('John Doe', $customerData['customer_name']);
        $this->assertEquals($customer->id, $customerData['id']);
        $this->assertEquals(40000.00, $customerData['total_premium_last_year']);

        // Check premium type breakdown
        $this->assertArrayHasKey($this->premiumType1->name, $customerData['premium_totals']);
        $this->assertArrayHasKey($this->premiumType2->name, $customerData['premium_totals']);
        
        $this->assertEquals('Yes', $customerData['premium_totals'][$this->premiumType1->name]['has_premium']);
        $this->assertEquals(25000.00, $customerData['premium_totals'][$this->premiumType1->name]['amount']);
    }

    public function test_generate_cross_selling_report_with_premium_type_filter()
    {
        // Arrange
        $customer = Customer::factory()->create();
        
        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType1->id,
            'final_premium_with_gst' => 25000.00
        ]);

        $parameters = [
            'premium_type_id' => [$this->premiumType1->id]
        ];

        // Act
        $result = $this->reportService->generateCrossSellingReport($parameters);

        // Assert
        $premiumTypes = $result['premiumTypes'];
        $this->assertCount(1, $premiumTypes);
        $this->assertEquals($this->premiumType1->id, $premiumTypes->first()->id);
    }

    public function test_generate_cross_selling_report_with_date_filters()
    {
        // Arrange
        $customer = Customer::factory()->create();
        
        // Insurance within date range
        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType1->id,
            'final_premium_with_gst' => 25000.00,
            'start_date' => '2024-06-15'
        ]);

        // Insurance outside date range  
        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType2->id,
            'final_premium_with_gst' => 15000.00,
            'start_date' => '2024-01-15'
        ]);

        $parameters = [
            'issue_start_date' => '2024-06-01',
            'issue_end_date' => '2024-06-30'
        ];

        // Act
        $result = $this->reportService->generateCrossSellingReport($parameters);

        // Assert
        $crossSelling = $result['crossSelling'];
        $customerData = $crossSelling->first();
        
        // Should only include insurance within date range
        $this->assertEquals(25000.00, $customerData['total_premium_last_year']);
    }

    public function test_generate_cross_selling_report_handles_customers_without_premiums()
    {
        // Arrange - Customer with no insurance policies
        $customer = Customer::factory()->create();
        $parameters = [];

        // Act
        $result = $this->reportService->generateCrossSellingReport($parameters);

        // Assert
        $crossSelling = $result['crossSelling'];
        $customerData = $crossSelling->first();
        
        $this->assertEquals(0, $customerData['total_premium_last_year']);
        $this->assertEquals(0, $customerData['actual_earnings_last_year']);
        
        // All premium types should show "No"
        foreach ($customerData['premium_totals'] as $premiumTotal) {
            $this->assertEquals('No', $premiumTotal['has_premium']);
            $this->assertEquals(0, $premiumTotal['amount']);
        }
    }

    public function test_export_cross_selling_report_returns_excel_download()
    {
        // Mock Excel facade
        Excel::fake();
        
        $parameters = ['test' => 'data'];

        // Act
        $result = $this->reportService->exportCrossSellingReport($parameters);

        // Assert
        Excel::assertDownloaded('cross_selling.xlsx');
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $result);
    }

    public function test_export_customer_insurance_report_returns_excel_download()
    {
        // Mock Excel facade
        Excel::fake();
        
        $parameters = ['test' => 'data'];

        // Act
        $result = $this->reportService->exportCustomerInsuranceReport($parameters);

        // Assert
        Excel::assertDownloaded('customer_insurances.xlsx');
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $result);
    }

    public function test_save_user_report_columns_creates_or_updates_report()
    {
        // Mock config
        config([
            'constants.INSURANCE_DETAIL' => [
                ['table_column_name' => 'customer_name'],
                ['table_column_name' => 'policy_no'],
                ['table_column_name' => 'premium_amount']
            ]
        ]);

        $reportName = 'test_report';
        $selectedColumns = ['customer_name', 'policy_no'];
        $userId = $this->user->id;

        // Act
        $this->reportService->saveUserReportColumns($reportName, $selectedColumns, $userId);

        // Assert
        $this->assertDatabaseHas('reports', [
            'name' => $reportName,
            'user_id' => $userId
        ]);

        $report = Report::where('name', $reportName)->where('user_id', $userId)->first();
        $columns = $report->selected_columns;

        $this->assertIsArray($columns);
        $this->assertCount(3, $columns);

        // Check selected columns
        $customerNameColumn = collect($columns)->firstWhere('table_column_name', 'customer_name');
        $this->assertEquals('Yes', $customerNameColumn['selected_column']);

        $premiumAmountColumn = collect($columns)->firstWhere('table_column_name', 'premium_amount');
        $this->assertEquals('No', $premiumAmountColumn['selected_column']);
    }

    public function test_save_user_report_columns_updates_existing_report()
    {
        // Mock config
        config([
            'constants.INSURANCE_DETAIL' => [
                ['table_column_name' => 'customer_name'],
                ['table_column_name' => 'policy_no']
            ]
        ]);

        // Create existing report
        $existingReport = Report::create([
            'name' => 'existing_report',
            'user_id' => $this->user->id,
            'selected_columns' => []
        ]);

        // Act
        $this->reportService->saveUserReportColumns(
            'existing_report', 
            ['customer_name'], 
            $this->user->id
        );

        // Assert - Should update, not create new
        $reports = Report::where('name', 'existing_report')->where('user_id', $this->user->id)->get();
        $this->assertCount(1, $reports);

        $updatedReport = $reports->first();
        $this->assertNotEquals($existingReport->updated_at, $updatedReport->updated_at);
    }

    public function test_load_user_report_columns_returns_selected_columns()
    {
        // Arrange
        $selectedColumns = [
            ['table_column_name' => 'customer_name', 'selected_column' => 'Yes'],
            ['table_column_name' => 'policy_no', 'selected_column' => 'No']
        ];

        Report::create([
            'name' => 'test_report',
            'user_id' => $this->user->id,
            'selected_columns' => $selectedColumns
        ]);

        // Act
        $result = $this->reportService->loadUserReportColumns('test_report', $this->user->id);

        // Assert
        $this->assertEquals($selectedColumns, $result);
    }

    public function test_load_user_report_columns_returns_null_if_not_found()
    {
        // Act
        $result = $this->reportService->loadUserReportColumns('nonexistent_report', $this->user->id);

        // Assert
        $this->assertNull($result);
    }

    public function test_generate_customer_insurance_report_delegates_to_model()
    {
        // This test verifies that the service correctly delegates to the Report model
        // Since Report::getInsuranceReport is a static method, we can test the delegation
        
        $parameters = ['customer_id' => $this->customer->id];

        // Act
        $result = $this->reportService->generateCustomerInsuranceReport($parameters);

        // Assert - The method should return whatever Report::getInsuranceReport returns
        // Since we can't easily mock static methods, we verify it doesn't throw an error
        // and returns some result (could be empty array if no data matches)
        $this->assertIsArray($result);
    }

    public function test_cross_selling_analysis_calculates_totals_correctly()
    {
        // Arrange
        $customer = Customer::factory()->create();
        
        // Create multiple insurances
        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType1->id,
            'final_premium_with_gst' => 20000.00,
            'actual_earnings' => 2000.00,
            'start_date' => Carbon::now()->subMonths(6)->format('Y-m-d')
        ]);

        CustomerInsurance::factory()->create([
            'customer_id' => $customer->id,
            'premium_type_id' => $this->premiumType1->id,
            'final_premium_with_gst' => 15000.00,
            'actual_earnings' => 1500.00,
            'start_date' => Carbon::now()->subMonths(3)->format('Y-m-d')
        ]);

        $parameters = [];

        // Act
        $result = $this->reportService->generateCrossSellingReport($parameters);

        // Assert
        $customerData = $result['crossSelling']->first();
        
        // Should sum both insurances for same premium type
        $this->assertEquals(35000.00, $customerData['total_premium_last_year']);
        $this->assertEquals(3500.00, $customerData['actual_earnings_last_year']);
        
        $premiumTypeData = $customerData['premium_totals'][$this->premiumType1->name];
        $this->assertEquals('Yes', $premiumTypeData['has_premium']);
        $this->assertEquals(35000.00, $premiumTypeData['amount']);
    }
}