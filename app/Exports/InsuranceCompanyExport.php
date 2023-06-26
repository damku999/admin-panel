<?php

namespace App\Exports;

use App\Models\InsuranceCompany;
use App\Models\RelationshipManager;
use Maatwebsite\Excel\Concerns\FromCollection;

class InsuranceCompanyExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return InsuranceCompany::all();
    }
}
