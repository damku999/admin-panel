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
        
        // Debug: Log all request parameters
        \Log::info('Reports index called', [
            'has_view' => $request->has('view'),
            'has_download' => $request->has('download'),
            'report_name' => $request->get('report_name'),
            'all_params' => $request->all()
        ]);
        
        if ($request['report_name'] == 'cross_selling') {
            if ($request->has('view')) {
                $crossSellingData = $this->reportService->generateCrossSellingReport($request->all());
                $response = array_merge($response, $crossSellingData);
            } else {
                return $this->reportService->exportCrossSellingReport($request->all());
            }
        } elseif ($request['report_name'] == 'insurance_detail') {
            if ($request->has('download')) {
                $request->validate(['report_name' => 'required']);
                \Log::info('Insurance Detail Export requested', $request->all());
                return $this->reportService->exportCustomerInsuranceReport($request->all());
            }
            if ($request->has('view')) {
                $request->validate(['report_name' => 'required']);
                $customerInsurances = $this->reportService->generateCustomerInsuranceReport($request->all());
                $response['customerInsurances'] = $customerInsurances;

                // Debug: Log the count and filters for troubleshooting
                \Log::info('Insurance Detail Report generated', [
                    'report_name' => $request['report_name'],
                    'filters' => $request->all(),
                    'count' => $customerInsurances ? count($customerInsurances) : 0
                ]);
            }
        } elseif ($request['report_name'] == 'due_policy_detail') {
            \Log::info('Due Policy Detail Report requested', [
                'has_download' => $request->has('download'),
                'has_view' => $request->has('view'),
                'all_params' => $request->all()
            ]);
            
            if ($request->has('download')) {
                $request->validate(['report_name' => 'required']);
                \Log::info('Due Policy Detail Export requested', $request->all());
                return $this->reportService->exportCustomerInsuranceReport($request->all());
            }
            if ($request->has('view')) {
                $request->validate(['report_name' => 'required']);
                $customerInsurances = $this->reportService->generateCustomerInsuranceReport($request->all());
                $response['customerInsurances'] = $customerInsurances;

                // Debug: Log the count and filters for troubleshooting
                \Log::info('Due Policy Detail Report generated', [
                    'report_name' => $request['report_name'],
                    'filters' => $request->all(),
                    'count' => $customerInsurances ? count($customerInsurances) : 0,
                    'sample_data' => $customerInsurances ? $customerInsurances->take(2)->toArray() : []
                ]);
            }
        }

        return view('reports.index', $response);
    }

    public function export(Request $request)
    {
        $request->validate(['report_name' => 'required']);

        if ($request['report_name'] == 'cross_selling') {
            return $this->reportService->exportCrossSellingReport($request->all());
        } elseif ($request['report_name'] == 'insurance_detail') {
            return $this->reportService->exportCustomerInsuranceReport($request->all());
        } elseif ($request['report_name'] == 'due_policy_detail') {
            return $this->reportService->exportCustomerInsuranceReport($request->all());
        } else {
            throw new \Exception('Invalid report type: ' . $request['report_name']);
        }
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