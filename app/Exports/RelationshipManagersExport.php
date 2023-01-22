<?php

namespace App\Exports;

use App\Models\RelationshipManager;
use Maatwebsite\Excel\Concerns\FromCollection;

class RelationshipManagersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return RelationshipManager::all();
    }
}
