<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Rules\MatchOldPassword;
use App\Models\CustomerInsurance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected $columns = [
        'final_premium_with_gst',
        'my_commission_amount',
        'transfer_commission_amount',
        'actual_earnings',
        'issue_date'
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = $this->compressionData($request);


        // Get counts for customers based on status
        $total_customer = Customer::count();
        $active_customer = Customer::where('status', 1)->count();
        $inactive_customer = $total_customer - $active_customer;

        // Get counts for customer insurances based on status
        $total_customer_insurance = CustomerInsurance::count();
        $active_customer_insurance = CustomerInsurance::where('status', 1)->count();
        $inactive_customer_insurance = $total_customer_insurance - $active_customer_insurance;

        // Get the current month and last month
        $current_month = Carbon::now()->startOfMonth();
        $last_month = Carbon::now()->subMonth()->startOfMonth();

        // Get the sum of final premium with GST and commission amounts for the entire life
        $life_time_final_premium_with_gst = CustomerInsurance::sum('final_premium_with_gst');
        $life_time_my_commission_amount = CustomerInsurance::sum('my_commission_amount');
        $life_time_transfer_commission_amount = CustomerInsurance::sum('transfer_commission_amount');
        $life_time_actual_earnings = CustomerInsurance::sum('actual_earnings');

        // Calculate sums for customer insurances in the current month
        $current_month_final_premium_with_gst = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('final_premium_with_gst');

        $current_month_my_commission_amount = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('my_commission_amount');

        $current_month_transfer_commission_amount = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('transfer_commission_amount');

        $current_month_actual_earnings = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('actual_earnings');

        // Calculate sums for customer insurances in the last month
        $last_month_final_premium_with_gst = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->subMonth()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('final_premium_with_gst');

        $last_month_my_commission_amount = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->subMonth()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('my_commission_amount');

        $last_month_transfer_commission_amount = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->subMonth()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('transfer_commission_amount');

        $last_month_actual_earnings = CustomerInsurance::whereBetween('issue_date', [
            Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            Carbon::now()->subMonth()->startOfMonth()->endOfMonth()->format('Y-m-d')
        ])->sum('actual_earnings');

        // Calculate count of expiring customer insurances for the current month
        $expiring_customer_insurance = CustomerInsurance::where('status', 0)
            ->whereMonth('expired_date', Carbon::now()->month)
            ->whereYear('expired_date', Carbon::now()->year)
            ->count();


        // Get the financial year start and end dates
        $current_month = Carbon::now()->month;
        $current_year = Carbon::now()->year;

        // Determine the financial year
        if ($current_month >= 4) {
            // Financial year starts from April of the current year
            $financial_year_start = Carbon::create($current_year, 4, 1, 0, 0, 0);
            $financial_year_end = Carbon::create($current_year + 1, 3, 31, 23, 59, 59);
        } else {
            // Financial year starts from April of the previous year
            $financial_year_start = Carbon::create($current_year - 1, 4, 1, 0, 0, 0);
            $financial_year_end = Carbon::create($current_year, 3, 31, 23, 59, 59);
        }

        // Get the data for the financial year
        $financial_year_data = CustomerInsurance::whereBetween('issue_date', [
            $financial_year_start->format('Y-m-d'),
            $financial_year_end->format('Y-m-d')
        ])->get();

        // Group data by month and year for the financial year
        $financial_year_grouped_data = $financial_year_data->groupBy(function ($item) {
            $issue_date = Carbon::parse($item->issue_date);
            return $issue_date->format('Y-m');
        });

        // Loop through each group and calculate sums for each month
        foreach ($financial_year_grouped_data as $month => $grouped_data) {
            // Extract the month and year from the $month variable
            $parsed_month = Carbon::createFromFormat('Y-m', $month)->format('m'); // Format: May, Jun, etc.
            $parsed_year = Carbon::createFromFormat('Y-m', $month)->year;

            // Use the extracted month and year as keys in the arrays
            $result[$parsed_month . '-' . $parsed_year]['final_premium_with_gst'] = $grouped_data->sum('final_premium_with_gst');
            $result[$parsed_month . '-' . $parsed_year]['my_commission_amount'] = $grouped_data->sum('my_commission_amount');
            $result[$parsed_month . '-' . $parsed_year]['transfer_commission_amount'] = $grouped_data->sum('transfer_commission_amount');
            $result[$parsed_month . '-' . $parsed_year]['actual_earnings'] = $grouped_data->sum('actual_earnings');
        }
        ksort($result);
        $json_data = json_encode($result);
        return view('home', compact(
            'total_customer',
            'active_customer',
            'inactive_customer',
            'life_time_final_premium_with_gst',
            'life_time_my_commission_amount',
            'life_time_transfer_commission_amount',
            'life_time_actual_earnings',
            'total_customer_insurance',
            'active_customer_insurance',
            'inactive_customer_insurance',
            'expiring_customer_insurance',
            'current_month_final_premium_with_gst',
            'current_month_my_commission_amount',
            'current_month_transfer_commission_amount',
            'current_month_actual_earnings',
            'last_month_final_premium_with_gst',
            'last_month_my_commission_amount',
            'last_month_transfer_commission_amount',
            'last_month_actual_earnings',
            'json_data',
            'data'
        ));
    }


    function compressionData($request)
    {
        $date = now();
        if ($request->date) {
            $date = Carbon::parse($request->date);
        }

        // Get the financial year start and end dates
        $current_month = $date->month;
        $current_year = $date->year;
        $previous_year = $current_year - 1;
        $response['date'] = $date;
        $response['yesterday'] = $date->copy()->subDay()->format('Y-m-d');
        $response['day_before_yesterday'] = $date->copy()->subDays(2)->format('Y-m-d');

        // Determine the financial year
        if ($current_month >= 4) {
            // Financial year starts from April of the current year
            $response['financial_year_start'] = $financial_year_start = Carbon::create($current_year, 4, 1, 0, 0, 0);
            $response['financial_year_end'] = $financial_year_end = Carbon::create($current_year + 1, 3, 31, 23, 59, 59);
            $response['previous_financial_year_start'] = $previous_financial_year_start = Carbon::create($previous_year, 4, 1, 0, 0, 0);
            $response['previous_financial_year_end'] = $previous_financial_year_end = Carbon::create($previous_year + 1, 3, 31, 23, 59, 59);
        } else {
            // Financial year starts from April of the previous year
            $response['financial_year_start'] = $financial_year_start = Carbon::create($current_year - 1, 4, 1, 0, 0, 0);
            $response['financial_year_end'] = $financial_year_end = Carbon::create($current_year, 3, 31, 23, 59, 59);
            $response['previous_financial_year_start'] = $previous_financial_year_start = Carbon::create($previous_year - 1, 4, 1, 0, 0, 0);
            $response['previous_financial_year_end'] = $previous_financial_year_end = Carbon::create($previous_year, 3, 31, 23, 59, 59);
        }

        $sum_columns = [
            DB::raw('COALESCE(SUM(final_premium_with_gst), 0) AS sum_final_premium'),
            DB::raw('COALESCE(SUM(my_commission_amount), 0) AS sum_my_commission'),
            DB::raw('COALESCE(SUM(transfer_commission_amount), 0) AS sum_transfer_commission'),
            DB::raw('COALESCE(SUM(actual_earnings), 0) AS sum_actual_earnings')
        ];

        // Get the data for the financial year
        $response['current_year_data'] = CustomerInsurance::select($sum_columns)
            ->whereBetween('issue_date', [
                $financial_year_start->format('Y-m-d'),
                $financial_year_end->format('Y-m-d')
            ])->first()->toArray();

        // Calculate the sum of columns for the previous financial year
        $response['last_year_data'] = CustomerInsurance::select($sum_columns)
            ->whereBetween('issue_date', [
                $previous_financial_year_start->format('Y-m-d'),
                $previous_financial_year_end->format('Y-m-d')
            ])->first()->toArray();

        // Calculate the sum of columns for the today
        $response['today_data'] = CustomerInsurance::select($sum_columns)->where('issue_date', $date->format('Y-m-d'))->first()->toArray();
        $response['yesterday_data'] = CustomerInsurance::select($sum_columns)->where('issue_date', $date->copy()->subDay()->format('Y-m-d'))->first()->toArray();
        $response['day_before_yesterday_data'] = CustomerInsurance::select($sum_columns)->where('issue_date', $date->copy()->subDays(2)->format('Y-m-d'))->first()->toArray();

        $quarters = [];
        $quarter_date = [];
        for ($i = 0; $i < 4; $i++) {
            $quarter_start = $financial_year_start->copy()->addMonths($i * 3);
            $quarter_end = $quarter_start->copy()->addMonths(2);
            $quarter_date[] = [
                'quarter_start' => $quarter_start,
                'quarter_end' => $quarter_end
            ];

            $quarters[] = CustomerInsurance::select($sum_columns)->whereBetween('issue_date', [
                $quarter_start->format('Y-m-d'),
                $quarter_end->format('Y-m-d')
            ])->first()->toArray();
        }
        $response['quarters_data'] = $quarters;
        $response['quarter_date'] = $quarter_date;
        return $response;
    }


    /**
     * User Profile
     * @param Nill
     * @return View Profile
     * @author Darshan Baraiya
     */
    public function getProfile()
    {
        return view('profile');
    }

    /**
     * Update Profile
     * @param $profileData
     * @return Boolean With Success Message
     * @author Darshan Baraiya
     */
    public function updateProfile(Request $request)
    {
        #Validations
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'mobile_number' => 'required|numeric|digits:10',
        ]);

        try {
            DB::beginTransaction();

            #Update Profile Data
            User::whereId(auth()->user()->id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile_number' => $request->mobile_number,
            ]);

            #Commit Transaction
            DB::commit();

            #Return To Profile page with success
            return back()->with('success', 'Profile Updated Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Change Password
     * @param Old Password, New Password, Confirm New Password
     * @return Boolean With Success Message
     * @author Darshan Baraiya
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        try {
            DB::beginTransaction();

            #Update Password
            User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

            #Commit Transaction
            DB::commit();

            #Return To Profile page with success
            return back()->with('success', 'Password Changed Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
