<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\Report;
use App\Models\Customer;
use App\Models\FuelType;
use App\Models\PolicyType;
use App\Models\PremiumType;
use Illuminate\Http\Request;
use App\Models\ReferenceUser;
use Illuminate\Support\Carbon;
use App\Models\InsuranceCompany;
use App\Exports\CrossSellingExport;
use App\Models\RelationshipManager;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerInsurancesExport1;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:report-list', ['only' => ['index']]);
    }

    /**
     * List Insurance report
     * @param Nill
     * @return Array $report
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $premiumTypes= PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get();
        $response = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
            'premium_types' =>$premiumTypes,
            'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
            'customerInsurances' => [],
            'crossSelling' => [],
        ];
        if ($request['report_name'] == 'cross_selling') {

            if ($request->has('view')) {
                $premiumTypes= PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get();

                $customers = Customer::with(['insurance.premiumType'])->orderBy('name')->get();
                $oneYearAgo = Carbon::now()->subYear(); // Calculate one year ago from today

                $results = $customers->map(function ($customer) use ($premiumTypes, $oneYearAgo) {
                    // Initialize customer data
                    $customerData = ['customer_name' => $customer->name];
                    // Loop through each premium type dynamically
                    foreach ($premiumTypes as $premiumType) {
                        $hasPremiumType = $customer->insurance->contains(function ($insurance) use ($premiumType) {
                            return $insurance->premiumType->id === $premiumType->id;
                        });
                         // Calculate total premium collected in the last year
                        $totalPremium = $customer->insurance
                        ->where('start_date', '>=', $oneYearAgo) // Filter insurances from the last year
                        ->sum('final_premium_with_gst'); // Sum the 'final_premium_with_gst' column

                    // Add the total premium to the customer data
                    $customerData['total_premium_last_year'] = $totalPremium;

                        // Add the premium type status dynamically to the customer data
                        $customerData[$premiumType->name] = $hasPremiumType ? 'Yes' : 'No';
                    }
                    return $customerData;
                });
                $response['crossSelling'] = $results;
            } else {
                return Excel::download(new CrossSellingExport($request->all()), 'cross_selling.xlsx');
            }
        } else {
            if ($request->has('download')) {
                $validation_array = [
                    'report_name' => 'required',
                ];
                $request->validate($validation_array);
                return Excel::download(new CustomerInsurancesExport1($request->all()), 'customer_insurances.xlsx');
            }
            if ($request->has('view')) {
                $validation_array = [
                    'report_name' => 'required',
                ];
                $request->validate($validation_array);
                $response['customerInsurances'] = Report::getInsuranceReport($request->all());
            }
        }
        return view('reports.index', $response);
    }

    public function export(Request $request)
    {
        // return Excel::download(new CustomerInsurancesExport, 'reports.xlsx');
    }

    /**
     * List Insurance report
     * @param Nill
     * @return Array $report
     * @author Darshan Baraiya
     */
    public function saveColumns(Request $request)
    {
        $updatedColumns = [];
        foreach (config('constants.INSURANCE_DETAIL') as $column) {
            $selected = 'No';
            if (in_array($column['table_column_name'], $request->selected_columns)) {
                $selected = 'Yes';
            }

            $column['selected_column'] = $selected;
            $updatedColumns[] = $column;
        }

        Report::updateOrCreate([
            'name' => $request->input('report_name'),
            'user_id' => auth()->user()->id,
        ], [
            'name' => $request->input('report_name'),
            'user_id' => auth()->user()->id,
            'selected_columns' => $updatedColumns,
        ]);
    }

    public function loadColumns(Request $request, $report_name)
    {
        $report = Report::where([
            'name' => $report_name,
            'user_id' => auth()->user()->id,
        ])->first();
        return view('reports.table_columns', ['reports' => $report]);
    }
}
