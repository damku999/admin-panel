<?php

namespace App\Exports;

use App\Models\AddonCover;
use Maatwebsite\Excel\Concerns\FromCollection;

class AddonCoversExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return AddonCover::all();
    }
}