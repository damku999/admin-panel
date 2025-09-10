<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ReportServiceInterface;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private ReportServiceInterface $reportService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:report-list', ['only' => ['index']]);
    }


    public function index(Request $request)
    {
        $response = $this->reportService->getInitialData();

        if ($request['report_name'] == 'cross_selling') {
            if ($request->has('view')) {
                $crossSellingData = $this->reportService->generateCrossSellingReport($request->all());
                $response = array_merge($response, $crossSellingData);
            } else {
                return $this->reportService->exportCrossSellingReport($request->all());
            }
        } else {
            if ($request->has('download')) {
                $request->validate(['report_name' => 'required']);
                return $this->reportService->exportCustomerInsuranceReport($request->all());
            }
            if ($request->has('view')) {
                $request->validate(['report_name' => 'required']);
                $response['customerInsurances'] = $this->reportService->generateCustomerInsuranceReport($request->all());
            }
        }
        
        return view('reports.index', $response);
    }

    public function export(Request $request)
    {
        // return Excel::download(new CustomerInsurancesExport, 'reports.xlsx');
    }

    public function saveColumns(Request $request)
    {
        $this->reportService->saveUserReportColumns(
            $request->input('report_name'),
            $request->selected_columns,
            auth()->user()->id
        );
    }

    public function loadColumns(Request $request, $report_name)
    {
        $columns = $this->reportService->loadUserReportColumns($report_name, auth()->user()->id);
        $report = (object) ['selected_columns' => $columns];
        return view('reports.table_columns', ['reports' => $report]);
    }
}
