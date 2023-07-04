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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $customers = Customer::all();
        $total_customer = $customers->count();
        $active_customer = $customers->where('status', 1)->count();
        $inactive_customer =  $customers->where('status', 0)->count();

        $customer_insurances = CustomerInsurance::all();
        $total_customer_insurance = $customer_insurances->count();
        $active_customer_insurance = $customer_insurances->where('status', 1)->count();
        $inactive_customer_insurance =  $customer_insurances->where('status', 0)->count();
        $expiring_customer_insurance =  $customer_insurances->where('status', 0)->filter(function ($user) {
            $date = Carbon::parse($user->expired_date);
            $date1 = Carbon::now();
            return $date->month == $date1->month && $date->year == $date1->year;
        })->count();

        return view('home', [
            'total_customer' => $total_customer,
            'active_customer' => $active_customer,
            'inactive_customer' => $inactive_customer,

            'total_customer_insurance' => $total_customer_insurance,
            'active_customer_insurance' => $active_customer_insurance,
            'inactive_customer_insurance' => $inactive_customer_insurance,
            'expiring_customer_insurance' => $expiring_customer_insurance,
        ]);
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
