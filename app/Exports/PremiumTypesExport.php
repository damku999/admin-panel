<?php

namespace App\Exports;

use App\Models\PremiumType;
use Maatwebsite\Excel\Concerns\FromCollection;

class PremiumTypesExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PremiumType::all();
    }
}
