<?php

namespace App\Exports;

use App\Models\PolicyType;
use Maatwebsite\Excel\Concerns\FromCollection;

class PolicyTypesExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PolicyType::all();
    }
}
