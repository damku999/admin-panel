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
        // dd('DEBUG: Index method called', [
        //     'method' => $request->method(),
        //     'all_data' => $request->all(),
        //     'has_view' => $request->has('view'),
        //     'view_value' => $request->input('view'),
        //     'report_name' => $request->input('report_name'),
        //     'url' => $request->url(),
        //     'full_url' => $request->fullUrl(),
        //     'raw_input' => $request->input()
        // ]);

        $response = $this->reportService->getInitialData();

        if (($request->has('view') || $request->isMethod('post')) && !empty($request->input('report_name'))) {
            $request->validate([
                'report_name' => 'required|in:cross_selling,insurance_detail,due_policy_detail'
            ]);

            \Log::info('Processing report request', [
                'report_name' => $request['report_name'],
                'request_data' => $request->all()
            ]);

            if ($request['report_name'] == 'cross_selling') {
                $crossSellingData = $this->reportService->generateCrossSellingReport($request->all());
                $response['cross_selling_report'] = $crossSellingData['crossSelling'] ?? [];
                $response['premiumTypes'] = $crossSellingData['premiumTypes'] ?? [];
            } elseif ($request['report_name'] == 'insurance_detail') {
                $insuranceData = $this->reportService->generateCustomerInsuranceReport($request->all());
                \Log::info('Insurance data received', ['count' => count($insuranceData)]);
                $response['insurance_reports'] = $insuranceData;
            } elseif ($request['report_name'] == 'due_policy_detail') {
                $duePolicyData = $this->reportService->generateCustomerInsuranceReport($request->all());
                \Log::info('Due policy data received', ['count' => count($duePolicyData)]);
                $response['due_policy_reports'] = $duePolicyData;
            }
        }

        \Log::info('Final response data', ['response_keys' => array_keys($response)]);
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