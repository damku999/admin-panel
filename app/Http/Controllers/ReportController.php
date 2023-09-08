<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Customer;
use Illuminate\Http\Request;

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
     * List RelationshipManager 
     * @param Nill
     * @return Array $report
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        // $columns = Schema::getColumnListing('customer_insurances');
        // dd($columns);
        $customers = Customer::select('id', 'name')->get();

        return view('reports.index', ['reports' => [], 'customers' => $customers]);
    }


    public function export(Request $request)
    {
        // return Excel::download(new RelationshipManagersExport, 'reports.xlsx');
    }
    public function saveColumns(Request $request)
    {
        $updatedColumns = [];
        foreach (config('constants.INSURANCE_DETAIL') as $column) {
            $selected = 'No';
            if (in_array($column['table_column_name'], $request->selected_columns))
                $selected = 'Yes';
            $column['selected_column'] = $selected;
            $updatedColumns[] = $column;
        }

        Report::updateOrCreate([
            'name' =>  $request->input('report_name'),
            'user_id' =>  auth()->user()->id,
        ], [
            'name' =>  $request->input('report_name'),
            'user_id' =>  auth()->user()->id,
            'selected_columns' =>  $updatedColumns,
        ]);
    }

    public function loadColumns(Request $request, $report_name)
    {
        $report = Report::where([
            'name' =>  $report_name,
            'user_id' =>  auth()->user()->id,
        ])->first();
        return view('reports.table_columns', ['reports' => $report,]);
    }
}
