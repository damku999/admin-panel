<?php

namespace App\Exports;

use App\Models\Broker;
use Maatwebsite\Excel\Concerns\FromCollection;

class BrokersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Broker::all();
    }
}
