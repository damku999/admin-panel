<?php

namespace App\Exports;

use App\Models\ReferenceUser;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReferenceUsersExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ReferenceUser::all();
    }
}
