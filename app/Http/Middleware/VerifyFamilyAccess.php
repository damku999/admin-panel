<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyFamilyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            Log::warning('VerifyFamilyAccess: No authenticated customer found');
            return redirect()->route('customer.login')->with('error', 'Please login to access this page.');
        }

        if (!$customer->status) {
            Log::warning('VerifyFamilyAccess: Inactive customer attempted access', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email
            ]);
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Your account has been deactivated.');
        }

        if (!$customer->hasFamily()) {
            Log::info('VerifyFamilyAccess: Customer without family group attempted access', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'route' => $request->route()->getName()
            ]);
            return redirect()->route('customer.dashboard')
                ->with('warning', 'You need to be part of a family group to access this feature.');
        }

        if (!$customer->familyGroup->status) {
            Log::warning('VerifyFamilyAccess: Customer with inactive family group attempted access', [
                'customer_id' => $customer->id,
                'family_group_id' => $customer->familyGroup->id,
                'family_group_name' => $customer->familyGroup->name
            ]);
            return redirect()->route('customer.dashboard')
                ->with('error', 'Your family group is currently inactive.');
        }

        Log::info('VerifyFamilyAccess: Access granted', [
            'customer_id' => $customer->id,
            'family_group_id' => $customer->familyGroup->id,
            'route' => $request->route()->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $next($request);
    }
}
