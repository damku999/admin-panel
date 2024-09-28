<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\PremiumType;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CrossSellingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $premiumTypes;

    public function __construct()
    {
        $this->premiumTypes = PremiumType::all(); // Load premium types when class is initialized
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $customers = Customer::with(['insurance.premiumType'])->orderBy('name')->get();
        $oneYearAgo = Carbon::now()->subYear(); // Calculate one year ago from today

        $results = $customers->map(function ($customer) use ($oneYearAgo) {
            // Initialize customer data
            $customerData = ['customer_name' => $customer->name];
            $totalPremium = $customer->insurance
                ->where('start_date', '>=', $oneYearAgo) // Filter insurance from the last year
                ->sum('final_premium_with_gst'); // Sum the 'final_premium_with_gst' column
            $customerData['total_premium_last_year'] = $totalPremium;

            // Loop through each premium type dynamically
            foreach ($this->premiumTypes as $premiumType) {
                $hasPremiumType = $customer->insurance->contains(function ($insurance) use ($premiumType) {
                    return $insurance->premiumType->id === $premiumType->id;
                });

                // Add the premium type status dynamically to the customer data
                $customerData[$premiumType->name] = $hasPremiumType ? 'Yes' : 'No';
            }

            return $customerData;
        });

        return $results;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $header = ['Customer Name', 'Total Premium (Last Year)'];

        // Add dynamic premium type headers
        foreach ($this->premiumTypes as $premiumType) {
            $header[] = $premiumType->name;
        }

        return $header;
    }
/**
 * @param Worksheet $sheet
 * @return array
 */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '58C4A7']],
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Dynamically calculate filter range based on number of premium types
                $totalColumns = 2 + $this->premiumTypes->count(); // 2 for Customer Name and Total Premium
                $filterRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . '1';

                // Apply filter
                $sheet->setAutoFilter($filterRange);

                // Header styles
                $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . '1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => '58C4A7',
                            ],
                        ],
                    ]);

                // Page setup
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
