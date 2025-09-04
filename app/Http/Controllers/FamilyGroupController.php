<?php

namespace App\Http\Controllers;

use App\Models\FamilyGroup;
use App\Models\Customer;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FamilyGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of family groups.
     */
    public function index(Request $request)
    {
        $query = FamilyGroup::with(['familyHead', 'familyMembers.customer']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('familyHead', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $familyGroups = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.family_groups.index', compact('familyGroups'));
    }

    /**
     * Show the form for creating a new family group.
     */
    public function create()
    {
        // Clean up any orphaned family member records first
        $this->cleanupOrphanedRecords();
        
        $availableCustomers = Customer::where('status', true)
            ->whereNull('family_group_id')
            ->whereDoesntHave('familyMember') // Ensure no family_members record exists
            ->orderBy('name')
            ->get();

        return view('admin.family_groups.create', compact('availableCustomers'));
    }

    /**
     * Store a newly created family group.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:family_groups,name,NULL,id,deleted_at,NULL',
            'family_head_id' => 'required|exists:customers,id',
            'member_ids' => 'sometimes|array',
            'member_ids.*' => 'exists:customers,id',
            'relationships' => 'sometimes|array',
            'relationships.*' => 'nullable|string|max:50',
            'status' => 'boolean',
            'family_head_password' => 'nullable|string|min:8|confirmed',
            'force_password_change' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Create family group
            $familyGroup = FamilyGroup::create([
                'name' => $request->name,
                'family_head_id' => $request->family_head_id,
                'status' => $request->status ?? true,
                'created_by' => auth()->id(),
            ]);

            // Update family head's family_group_id
            Customer::where('id', $request->family_head_id)
                ->update(['family_group_id' => $familyGroup->id]);

            // Set up passwords for all family members
            $passwordNotifications = [];
            
            // Setup family head password
            $familyHead = Customer::find($request->family_head_id);
            
            // Use admin-provided password or generate one
            $customPassword = $request->filled('family_head_password') ? $request->family_head_password : null;
            $forcePasswordChange = $request->boolean('force_password_change', true);
            
            if ($customPassword || !$familyHead->hasVerifiedEmail() || $familyHead->needsPasswordChange()) {
                $password = $customPassword ? $familyHead->setCustomPassword($customPassword, $forcePasswordChange) : $familyHead->setDefaultPassword();
                $passwordNotifications[] = [
                    'customer' => $familyHead,
                    'password' => $password,
                    'is_head' => true,
                    'admin_set' => !empty($customPassword)
                ];
            }

            // Add family head as member
            FamilyMember::create([
                'family_group_id' => $familyGroup->id,
                'customer_id' => $request->family_head_id,
                'relationship' => 'head',
                'is_head' => true,
                'status' => true,
            ]);

            // Add other family members if provided
            if ($request->filled('member_ids')) {
                foreach ($request->member_ids as $index => $memberId) {
                    if ($memberId != $request->family_head_id) {
                        Customer::where('id', $memberId)
                            ->update(['family_group_id' => $familyGroup->id]);

                        // Setup password for family member
                        $familyMember = Customer::find($memberId);
                        if (!$familyMember->hasVerifiedEmail() || $familyMember->needsPasswordChange()) {
                            $password = $familyMember->setDefaultPassword();
                            $passwordNotifications[] = [
                                'customer' => $familyMember,
                                'password' => $password,
                                'is_head' => false
                            ];
                        }

                        FamilyMember::create([
                            'family_group_id' => $familyGroup->id,
                            'customer_id' => $memberId,
                            'relationship' => $request->relationships[$index] ?? null,
                            'is_head' => false,
                            'status' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            // Send password notifications after successful commit
            $this->sendPasswordNotifications($passwordNotifications, $familyGroup);

            return redirect()->route('family_groups.index')
                ->with('success', 'Family group created successfully. Login credentials sent to family head only. Family head will manage family member access.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating family group: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified family group.
     */
    public function show(FamilyGroup $familyGroup)
    {
        $familyGroup->load(['familyHead', 'familyMembers.customer']);
        
        return view('admin.family_groups.show', compact('familyGroup'));
    }

    /**
     * Show the form for editing the specified family group.
     */
    public function edit(FamilyGroup $familyGroup)
    {
        // Clean up any orphaned family member records first
        $this->cleanupOrphanedRecords();
        
        $familyGroup->load(['familyHead', 'familyMembers.customer']);
        
        $availableCustomers = Customer::where('status', true)
            ->where(function ($query) use ($familyGroup) {
                $query->whereNull('family_group_id')
                      ->orWhere('family_group_id', $familyGroup->id);
            })
            ->whereDoesntHave('familyMember', function ($query) use ($familyGroup) {
                $query->where('family_group_id', '!=', $familyGroup->id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.family_groups.edit', compact('familyGroup', 'availableCustomers'));
    }

    /**
     * Update the specified family group.
     */
    public function update(Request $request, FamilyGroup $familyGroup)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:family_groups,name,' . $familyGroup->id . ',id,deleted_at,NULL',
            'family_head_id' => 'required|exists:customers,id',
            'member_ids' => 'sometimes|array',
            'member_ids.*' => 'exists:customers,id',
            'relationships' => 'sometimes|array',
            'relationships.*' => 'nullable|string|max:50',
            'status' => 'boolean',
            'family_head_password' => 'nullable|string|min:8|confirmed',
            'force_password_change' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update family group
            $familyGroup->update([
                'name' => $request->name,
                'family_head_id' => $request->family_head_id,
                'status' => $request->status ?? true,
            ]);

            // Remove old family members
            $oldMemberIds = $familyGroup->familyMembers->pluck('customer_id')->toArray();
            Customer::whereIn('id', $oldMemberIds)->update([
                'family_group_id' => null,
                'password' => null,
                'email_verified_at' => null,
                'password_reset_sent_at' => null
            ]);
            FamilyMember::where('family_group_id', $familyGroup->id)->delete();

            // Update new family head's family_group_id
            Customer::where('id', $request->family_head_id)
                ->update(['family_group_id' => $familyGroup->id]);

            // Handle family head password update if provided
            if ($request->filled('family_head_password')) {
                $familyHead = Customer::find($request->family_head_id);
                $forcePasswordChange = $request->boolean('force_password_change', false);
                $familyHead->setCustomPassword($request->family_head_password, $forcePasswordChange);
            }

            // Add family head as member
            FamilyMember::create([
                'family_group_id' => $familyGroup->id,
                'customer_id' => $request->family_head_id,
                'relationship' => 'head',
                'is_head' => true,
                'status' => true,
            ]);

            // Add other family members if provided
            if ($request->filled('member_ids')) {
                foreach ($request->member_ids as $index => $memberId) {
                    if ($memberId != $request->family_head_id) {
                        Customer::where('id', $memberId)
                            ->update(['family_group_id' => $familyGroup->id]);

                        FamilyMember::create([
                            'family_group_id' => $familyGroup->id,
                            'customer_id' => $memberId,
                            'relationship' => $request->relationships[$index] ?? null,
                            'is_head' => false,
                            'status' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            $message = 'Family group updated successfully.';
            if ($request->filled('family_head_password')) {
                $message .= ' Family head password has been updated.';
            }

            return redirect()->route('family_groups.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating family group: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified family group.
     */
    public function destroy(FamilyGroup $familyGroup)
    {
        DB::beginTransaction();
        try {
            $familyName = $familyGroup->name;
            
            // Get member IDs before deletion
            $memberIds = $familyGroup->familyMembers->pluck('customer_id')->toArray();
            
            // Delete family members FIRST
            FamilyMember::where('family_group_id', $familyGroup->id)->delete();
            
            // Reset all customer data related to family group
            Customer::whereIn('id', $memberIds)->update([
                'family_group_id' => null,
                'password' => null,
                'email_verified_at' => null,
                'password_reset_sent_at' => null
            ]);

            // Finally delete family group
            $familyGroup->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Family group '{$familyName}' deleted successfully."
                ]);
            }

            return redirect()->route('family_groups.index')
                ->with('success', "Family group '{$familyName}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error deleting family group: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error deleting family group: ' . $e->getMessage());
        }
    }

    /**
     * Update family group status.
     */
    public function updateStatus($familyGroupId, $status)
    {
        $familyGroup = FamilyGroup::findOrFail($familyGroupId);
        $familyGroup->update(['status' => $status]);

        $message = $status ? 'Family group activated successfully.' : 'Family group deactivated successfully.';
        
        return back()->with('success', $message);
    }

    /**
     * Export family groups.
     */
    public function export()
    {
        $familyGroups = FamilyGroup::with(['familyHead', 'familyMembers.customer'])->get();
        
        // Simple CSV export
        $filename = 'family_groups_' . date('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($familyGroups) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Family Name', 'Family Head', 'Members Count', 'Status', 'Created Date']);

            foreach ($familyGroups as $group) {
                fputcsv($file, [
                    $group->id,
                    $group->name,
                    $group->familyHead->name ?? 'N/A',
                    $group->familyMembers->count(),
                    $group->status ? 'Active' : 'Inactive',
                    $group->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clean up orphaned family member records.
     * This removes family_members records where the family_group no longer exists.
     */
    private function cleanupOrphanedRecords()
    {
        try {
            // Find family_members records where family_group doesn't exist
            $orphanedMemberIds = FamilyMember::whereDoesntHave('familyGroup')->pluck('customer_id');
            
            if ($orphanedMemberIds->count() > 0) {
                // Reset customer data for customers with orphaned family_members records
                Customer::whereIn('id', $orphanedMemberIds)->update([
                    'family_group_id' => null,
                    'password' => null,
                    'email_verified_at' => null,
                    'password_reset_sent_at' => null
                ]);
                
                // Delete orphaned family_members records
                FamilyMember::whereDoesntHave('familyGroup')->delete();
                
                \Log::info('Cleaned up orphaned family member records', [
                    'customer_ids' => $orphanedMemberIds->toArray(),
                    'count' => $orphanedMemberIds->count()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error cleaning up orphaned family member records: ' . $e->getMessage());
        }
    }

    /**
     * Send password notifications only to family head.
     * Family members' credentials will be managed by the family head.
     */
    private function sendPasswordNotifications(array $notifications, FamilyGroup $familyGroup): void
    {
        // Filter notifications to send only to family head
        $familyHeadNotifications = array_filter($notifications, function ($notification) {
            return $notification['is_head'] === true;
        });

        foreach ($familyHeadNotifications as $notification) {
            try {
                $customer = $notification['customer'];
                $password = $notification['password'];
                $isHead = $notification['is_head'];

                // Send actual email notification only to family head
                \Mail::to($customer->email)->send(new \App\Mail\FamilyLoginCredentialsMail(
                    customer: $customer,
                    password: $password,
                    familyGroup: $familyGroup,
                    isHead: $isHead
                ));

                \Log::info('Family group login credentials email sent to family head only', [
                    'family_group' => $familyGroup->name,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'is_family_head' => $isHead,
                    'login_url' => route('customer.login'),
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to send family login credentials email to family head', [
                    'family_group' => $familyGroup->name ?? null,
                    'customer_id' => $customer->id ?? null,
                    'customer_email' => $customer->email ?? null,
                    'is_family_head' => $isHead ?? false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Continue with other notifications even if one fails
                continue;
            }
        }

        // Log information about family members who won't receive email notifications
        $memberNotifications = array_filter($notifications, function ($notification) {
            return $notification['is_head'] === false;
        });

        if (!empty($memberNotifications)) {
            $memberEmails = array_map(function ($notification) {
                return $notification['customer']->email;
            }, $memberNotifications);

            \Log::info('Family member credentials generated but no email sent (family head will manage)', [
                'family_group' => $familyGroup->name,
                'member_emails' => $memberEmails,
                'member_count' => count($memberNotifications),
                'note' => 'Family head will manage family member login credentials'
            ]);
        }
    }

    /**
     * Remove a specific family member from their family group.
     */
    public function removeMember(FamilyMember $familyMember)
    {
        DB::beginTransaction();
        try {
            $familyGroupName = $familyMember->familyGroup->name ?? 'Unknown';
            $customerName = $familyMember->customer->name ?? 'Unknown';
            $customerId = $familyMember->customer_id;
            
            // Prevent removing family head
            if ($familyMember->is_head) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot remove family head. Please change family head first or delete the entire family group.'
                    ], 400);
                }
                
                return back()->with('error', 'Cannot remove family head. Please change family head first or delete the entire family group.');
            }

            // Reset customer data when removing from family
            Customer::where('id', $customerId)->update([
                'family_group_id' => null,
                'password' => null,
                'email_verified_at' => null,
                'password_reset_sent_at' => null
            ]);

            // Delete the family member record
            $familyMember->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => "'{$customerName}' has been removed from '{$familyGroupName}' successfully."
                ]);
            }

            return redirect()->back()
                ->with('success', "'{$customerName}' has been removed from '{$familyGroupName}' successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error removing family member: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error removing family member: ' . $e->getMessage());
        }
    }
}
