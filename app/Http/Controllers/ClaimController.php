<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\ClaimStage;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use App\Traits\ExportableTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClaimController extends Controller
{
    use WhatsAppApiTrait, ExportableTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:claim-list|claim-create|claim-edit|claim-delete', ['only' => ['index']]);
        $this->middleware('permission:claim-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:claim-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:claim-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request): View
    {
        $claims = Claim::with(['customer', 'customerInsurance'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('claim_number', 'LIKE', '%' . trim($request->search) . '%')
                    ->orWhere('policy_no', 'LIKE', '%' . trim($request->search) . '%')
                    ->orWhere('vehicle_number', 'LIKE', '%' . trim($request->search) . '%')
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . trim($request->search) . '%');
                    });
            })
            ->when($request->insurance_type, function ($query) use ($request) {
                $query->where('insurance_type', $request->insurance_type);
            })
            ->when($request->claim_status, function ($query) use ($request) {
                $query->where('claim_status', $request->claim_status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('claims.index', compact('claims'));
    }

    public function create(): View
    {
        $customers = Customer::where('status', true)->get();
        $customerInsurances = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
            ->where('status', true)
            ->get();

        return view('claims.create', compact('customers', 'customerInsurances'));
    }

    public function store(StoreClaimRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $claim = Claim::create($request->validated());

            // Create initial stage
            ClaimStage::create([
                'claim_id' => $claim->id,
                'stage_name' => 'Claim Initiated',
                'stage_description' => 'Initial claim intimation received',
                'stage_date' => now(),
                'is_current' => true,
                'stage_order' => 1,
                'stage_status' => 'In Progress',
                'status' => true,
            ]);

            // Create required documents based on insurance type
            $this->createRequiredDocuments($claim);

            DB::commit();

            return redirect()->route('claims.show', $claim)
                ->with('success', 'Claim created successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $th->getMessage());
        }
    }

    public function show(Claim $claim): View
    {
        $claim->load([
            'customer',
            'customerInsurance.insuranceCompany',
            'documents' => function ($query) {
                $query->ordered();
            },
            'stages' => function ($query) {
                $query->ordered();
            },
            'liability'
        ]);

        return view('claims.show', compact('claim'));
    }

    public function edit(Claim $claim): View
    {
        $customers = Customer::where('status', true)->get();
        $customerInsurances = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
            ->where('status', true)
            ->get();

        return view('claims.edit', compact('claim', 'customers', 'customerInsurances'));
    }

    public function update(UpdateClaimRequest $request, Claim $claim): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $claim->update($request->validated());

            DB::commit();

            return redirect()->route('claims.show', $claim)
                ->with('success', 'Claim updated successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $th->getMessage());
        }
    }

    public function destroy(Claim $claim): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $claim->delete();

            DB::commit();

            return redirect()->route('claims.index')
                ->with('success', 'Claim deleted successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $th->getMessage());
        }
    }

    public function intimateDocument(Request $request, Claim $claim): RedirectResponse
    {
        try {
            $message = $this->getDocumentListMessage($claim);
            $this->whatsAppSendMessage($message, $claim->customer->mobile_number);

            $claim->update([
                'document_request_sent' => true,
                'document_request_sent_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Document list sent via WhatsApp successfully.');

        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', $th->getMessage());
        }
    }


    public function assignClaimNumber(Request $request, Claim $claim)
    {
        $request->validate([
            'insurance_claim_number' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Update claim with insurance claim number
            $claim->update([
                'insurance_claim_number' => $request->insurance_claim_number,
            ]);

            // Send WhatsApp notification using trait method
            $whatsappSent = false;
            if ($claim->customer->mobile_number) {
                try {
                    $message = $this->claimNumberAssigned($claim);
                    $response = $this->whatsAppSendMessage($message, $claim->customer->mobile_number);
                    $whatsappSent = true;
                } catch (\Exception $e) {
                    // WhatsApp failed but continue with claim assignment
                }
            }

            DB::commit();

            $successMessage = 'Claim number assigned successfully.';
            if ($whatsappSent) {
                $successMessage .= ' Customer notified via WhatsApp.';
            }

            // Return JSON for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'whatsapp_sent' => $whatsappSent
                ]);
            }

            return redirect()->back()
                ->with('success', $successMessage);

        } catch (\Throwable $th) {
            DB::rollBack();

            $errorMessage = 'Failed to assign claim number: ' . $th->getMessage();

            // Return JSON for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }

            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    public function resendClaimNumber(Claim $claim): RedirectResponse
    {
        try {
            if (!$claim->insurance_claim_number) {
                return redirect()->back()
                    ->with('error', 'No claim number assigned yet to send.');
            }

            // Send WhatsApp notification using trait method
            $message = $this->claimNumberAssigned($claim);
            $this->whatsAppSendMessage($message, $claim->customer->mobile_number);

            return redirect()->back()
                ->with('success', 'Claim number details resent to customer via WhatsApp successfully.');

        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', 'Failed to resend claim number: ' . $th->getMessage());
        }
    }

    public function closeClaim(Request $request, Claim $claim)
    {
        DB::beginTransaction();

        try {
            $claim->update([
                'claim_status' => 'Closed',
                'closed_at' => now(),
                'closure_reason' => $request->closure_reason,
            ]);

            // Mark current stage as completed and create closure stage
            $claim->stages()->current()->update([
                'is_current' => false,
                'stage_status' => 'Completed',
            ]);

            ClaimStage::create([
                'claim_id' => $claim->id,
                'stage_name' => 'Completed',
                'stage_description' => 'Claim process completed',
                'stage_date' => now(),
                'is_current' => true,
                'stage_order' => 9,
                'stage_status' => 'Completed',
                'status' => true,
            ]);

            // Send WhatsApp message if requested
            $whatsappSent = false;
            if ($request->send_whatsapp && $claim->customer->mobile_number) {
                try {
                    $message = $this->claimClosed($claim, $request->closure_reason);
                    $response = $this->sendMessage($claim->customer->mobile_number, $message);
                    $whatsappSent = true;
                    \Log::info('Claim closure WhatsApp sent', [
                        'claim_id' => $claim->id,
                        'mobile' => $claim->customer->mobile_number,
                        'response' => $response
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send claim closure WhatsApp', [
                        'claim_id' => $claim->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with success even if WhatsApp fails
                }
            }

            DB::commit();
            
            $successMessage = 'Claim closed successfully.';
            if ($whatsappSent) {
                $successMessage .= ' WhatsApp notification sent to customer.';
            }

            // Return JSON for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'whatsapp_sent' => $whatsappSent
                ]);
            }

            return redirect()->back()
                ->with('success', $successMessage);

        } catch (\Throwable $th) {
            DB::rollBack();
            
            // Return JSON error for AJAX requests, redirect for regular requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to close claim: ' . $th->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to close claim: ' . $th->getMessage());
        }
    }

    private function createRequiredDocuments(Claim $claim): void
    {
        $documents = $claim->isHealthInsurance() 
            ? ClaimDocument::getHealthInsuranceDocuments()
            : ClaimDocument::getTruckInsuranceDocuments();

        foreach ($documents as $doc) {
            ClaimDocument::create(array_merge($doc, [
                'claim_id' => $claim->id,
                'insurance_type' => $claim->insurance_type,
                'document_status' => 'Required',
                'status' => true,
            ]));
        }
    }

    private function getDocumentListMessage(Claim $claim): string
    {
        $documents = $claim->documents()->required()->ordered()->get();
        $docList = $documents->pluck('document_name')->implode("\n");

        if ($claim->isHealthInsurance()) {
            return "For health Insurance - Kindly provide below mention details for Claim intimation\n\n{$docList}";
        } else {
            return "For Truck Insurance - Kindly provide below mention details for Claim intimation\n\n{$docList}";
        }
    }

    // Export method is now provided by ExportableTrait with advanced features
    
    protected function getExportRelations(): array
    {
        return ['customer', 'customerInsurance.insuranceCompany'];
    }
    
    protected function getSearchableFields(): array
    {
        return ['claim_number', 'insurance_claim_number', 'policy_no', 'vehicle_number', 'patient_name', 'hospital_name'];
    }
    
    protected function getDateFilterField(): string
    {
        return 'incident_date';
    }
    
    protected function getExportConfig(Request $request): array
    {
        return array_merge($this->getBaseExportConfig($request), [
            'headings' => [
                'Claim Number', 'Insurance Claim Number', 'Customer Name', 'Customer Mobile', 'Insurance Type',
                'Policy Number', 'Vehicle Number', 'Incident Date', 'Claim Amount',
                'Claim Status', 'Insurance Company', 'Patient Name', 'Hospital Name',
                'Driver Name', 'Accident Location', 'Intimation Date', 'Created Date', 'Updated Date'
            ],
            'mapping' => function($claim) {
                return [
                    $claim->claim_number,
                    $claim->insurance_claim_number,
                    $claim->customer ? $claim->customer->name : '',
                    $claim->customer ? $claim->customer->mobile_number : '',
                    $claim->insurance_type,
                    $claim->policy_no,
                    $claim->vehicle_number,
                    $claim->incident_date ? $claim->incident_date->format('d-m-Y') : '',
                    $claim->claim_amount ? number_format($claim->claim_amount, 2) : '',
                    ucfirst($claim->claim_status),
                    $claim->customerInsurance && $claim->customerInsurance->insuranceCompany 
                        ? $claim->customerInsurance->insuranceCompany->name : '',
                    $claim->patient_name,
                    $claim->hospital_name,
                    $claim->driver_name,
                    $claim->accident_location,
                    $claim->intimation_date ? $claim->intimation_date->format('d-m-Y') : '',
                    $claim->created_at->format('d-m-Y H:i:s'),
                    $claim->updated_at->format('d-m-Y H:i:s')
                ];
            },
            'with_headings' => true,
            'with_mapping' => true
        ]);
    }

    public function policyLookup($type, $value)
    {
        try {
            $policy = null;

            if ($type === 'policy') {
                // Lookup by policy number
                $policy = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                    ->where('policy_number', $value)
                    ->where('status', true)
                    ->first();
            } elseif ($type === 'vehicle') {
                // Lookup by vehicle number
                $policy = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                    ->where('vehicle_number', $value)
                    ->where('status', true)
                    ->first();
            }

            if (!$policy) {
                return response()->json([
                    'success' => false,
                    'message' => 'No policy found for the provided ' . $type . ' number.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'customer_id' => $policy->customer_id,
                    'customer_name' => $policy->customer->name,
                    'customer_mobile' => $policy->customer->mobile_number,
                    'customer_email' => $policy->customer->email,
                    'policy_id' => $policy->id,
                    'policy_number' => $policy->policy_number,
                    'vehicle_number' => $policy->vehicle_number,
                    'insurance_company' => $policy->insuranceCompany->name,
                    'policy_type' => $policy->policyType ? $policy->policyType->name : '',
                    'premium_amount' => $policy->premium_amount,
                    'sum_insured' => $policy->sum_insured,
                    'policy_start_date' => $policy->policy_start_date ? $policy->policy_start_date->format('Y-m-d') : '',
                    'policy_end_date' => $policy->policy_end_date ? $policy->policy_end_date->format('Y-m-d') : '',
                    'vehicle_make' => $policy->vehicle_make,
                    'vehicle_model' => $policy->vehicle_model,
                    'vehicle_year' => $policy->vehicle_year,
                    'engine_number' => $policy->engine_number,
                    'chassis_number' => $policy->chassis_number
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while looking up policy details.'
            ]);
        }
    }

    public function lookup(Request $request, $type)
    {
        try {
            $query = $request->get('q');
            $value = $request->get('value');
            
            \Log::info('Lookup called', ['type' => $type, 'query' => $query, 'value' => $value, 'auth' => auth()->check()]);
            
            // If 'q' parameter exists, it's a search request (auto-complete)
            if ($query) {
                if (strlen($query) < 3) {
                    return response()->json(['success' => false, 'message' => 'Query too short']);
                }

                // Universal Search - Search ALL fields: Policy No, Registration No, Customer Name, Mobile
                $results = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                    ->where(function($q) use ($query) {
                        $q->where('policy_no', 'LIKE', '%' . $query . '%')
                          ->orWhere('registration_no', 'LIKE', '%' . strtoupper($query) . '%')
                          ->orWhereHas('customer', function($cq) use ($query) {
                              $cq->where('name', 'LIKE', '%' . $query . '%')
                                 ->orWhere('mobile_number', 'LIKE', '%' . $query . '%');
                          });
                    })
                    ->where('status', true)
                    ->whereHas('customer', function($q) {
                        $q->where('status', true); // Only active customers
                    })
                    ->limit(15)
                    ->get()
                    ->map(function($policy) use ($query) {
                        // Determine what field matched the search
                        $matchedField = 'policy';
                        if (stripos($policy->registration_no, $query) !== false) {
                            $matchedField = 'vehicle';
                        } elseif (stripos($policy->customer->name, $query) !== false) {
                            $matchedField = 'customer_name';
                        } elseif (stripos($policy->customer->mobile_number, $query) !== false) {
                            $matchedField = 'mobile';
                        }

                        return [
                            'policy_number' => $policy->policy_no,
                            'vehicle_number' => $policy->registration_no,
                            'customer_name' => $policy->customer->name,
                            'customer_mobile' => $policy->customer->mobile_number,
                            'customer_id' => $policy->customer_id,
                            'policy_id' => $policy->id,
                            'insurance_company' => $policy->insuranceCompany->name,
                            'policy_type' => $policy->policyType ? $policy->policyType->name : '',
                            'insurance_type' => $this->detectInsuranceType($policy->policyType ? $policy->policyType->name : ''),
                            'matched_field' => $matchedField,
                            'sum_insured' => $policy->sum_insured,
                            'premium_amount' => $policy->premium_amount
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $results
                ]);
            }
            
            // If 'value' parameter exists, it's a detailed lookup request
            if ($value) {
                $policy = null;

                if ($type === 'policy') {
                    $policy = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                        ->where('policy_no', $value)
                        ->where('status', true)
                        ->whereHas('customer', function($q) {
                            $q->where('status', true); // Only active customers
                        })
                        ->first();
                } else {
                    $policy = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                        ->where('registration_no', $value)
                        ->where('status', true)
                        ->whereHas('customer', function($q) {
                            $q->where('status', true); // Only active customers
                        })
                        ->first();
                }

                if (!$policy) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No policy found for the provided ' . $type . '.'
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'customer_id' => $policy->customer_id,
                        'customer_name' => $policy->customer->name,
                        'customer_mobile' => $policy->customer->mobile_number,
                        'customer_email' => $policy->customer->email,
                        'policy_id' => $policy->id,
                        'policy_number' => $policy->policy_no,
                        'vehicle_number' => $policy->registration_no,
                        'insurance_company' => $policy->insuranceCompany->name,
                        'policy_type' => $policy->policyType ? $policy->policyType->name : '',
                        'premium_amount' => $policy->premium_amount,
                        'sum_insured' => $policy->sum_insured,
                        'policy_start_date' => $policy->start_date ? date('Y-m-d', strtotime($policy->start_date)) : '',
                        'policy_end_date' => $policy->expired_date ? date('Y-m-d', strtotime($policy->expired_date)) : '',
                        'vehicle_make' => $policy->make_model ?? '',
                        'vehicle_model' => '',
                        'vehicle_year' => $policy->mfg_year ?? '',
                        'engine_number' => '',
                        'chassis_number' => ''
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Missing query or value parameter.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Claims lookup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while processing request: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Universal search API - Search ALL fields in one endpoint
     */
    public function universalSearch(Request $request)
    {
        try {
            $query = $request->get('q');
            
            if (!$query || strlen($query) < 3) {
                return response()->json(['success' => false, 'message' => 'Query too short, minimum 3 characters required']);
            }

            // Universal Search - Search ALL fields: Policy No, Registration No, Customer Name, Mobile
            $results = CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType'])
                ->where(function($q) use ($query) {
                    $q->where('policy_no', 'LIKE', '%' . $query . '%')
                      ->orWhere('registration_no', 'LIKE', '%' . strtoupper($query) . '%')
                      ->orWhereHas('customer', function($cq) use ($query) {
                          $cq->where('name', 'LIKE', '%' . $query . '%')
                             ->orWhere('mobile_number', 'LIKE', '%' . $query . '%');
                      });
                })
                ->where('status', true)
                ->whereHas('customer', function($q) {
                    $q->where('status', true); // Only active customers
                })
                ->limit(15)
                ->get()
                ->map(function($policy) use ($query) {
                    // Determine what field matched the search
                    $matchedField = 'policy';
                    if ($policy->registration_no && stripos($policy->registration_no, $query) !== false) {
                        $matchedField = 'vehicle';
                    } elseif (stripos($policy->customer->name, $query) !== false) {
                        $matchedField = 'customer_name';
                    } elseif (stripos($policy->customer->mobile_number, $query) !== false) {
                        $matchedField = 'mobile';
                    }

                    return [
                        'policy_number' => $policy->policy_no,
                        'vehicle_number' => $policy->registration_no,
                        'customer_name' => $policy->customer->name,
                        'customer_mobile' => $policy->customer->mobile_number,
                        'customer_id' => $policy->customer_id,
                        'policy_id' => $policy->id,
                        'insurance_company' => $policy->insuranceCompany->name,
                        'policy_type' => $policy->policyType ? $policy->policyType->name : '',
                        'insurance_type' => $this->detectInsuranceType($policy->policyType ? $policy->policyType->name : ''),
                        'matched_field' => $matchedField,
                        'sum_insured' => $policy->sum_insured,
                        'premium_amount' => $policy->premium_amount,
                        'policy_start_date' => $policy->start_date ? date('Y-m-d', strtotime($policy->start_date)) : null,
                        'policy_end_date' => $policy->expired_date ? date('Y-m-d', strtotime($policy->expired_date)) : null,
                        'vehicle_make' => $policy->make_model ?? '',
                        'vehicle_year' => $policy->mfg_year ?? ''
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Universal search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while searching'
            ]);
        }
    }

    /**
     * Detect insurance type from policy type name
     */
    private function detectInsuranceType($policyTypeName): string
    {
        $policyTypeLower = strtolower($policyTypeName);
        
        if (str_contains($policyTypeLower, 'truck') || 
            str_contains($policyTypeLower, 'motor') ||
            str_contains($policyTypeLower, 'vehicle') ||
            str_contains($policyTypeLower, 'commercial')) {
            return 'Truck';
        }
        
        return 'Health';
    }
}
