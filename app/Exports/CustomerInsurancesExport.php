<?php

namespace App\Exports;

use App\Models\CustomerInsurance;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerInsurancesExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CustomerInsurance::all();
    }
}
