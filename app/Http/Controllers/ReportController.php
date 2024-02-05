<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\Report;
use App\Models\Customer;
use App\Models\FuelType;
use App\Models\PolicyType;
use App\Models\PremiumType;
use Illuminate\Http\Request;
use App\Models\ReferenceUser;
use App\Models\InsuranceCompany;
use App\Models\RelationshipManager;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerInsurancesExport1;

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
        $response  = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
            'premium_types' => PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get(),
            'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
        ];
        if ($request->has('download')) {
            return Excel::download(new CustomerInsurancesExport1($request->all()), 'customer_insurances.xlsx');
        }

        return view('reports.index', $response);
    }


    public function export(Request $request)
    {
        // return Excel::download(new CustomerInsurancesExport, 'reports.xlsx');
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
