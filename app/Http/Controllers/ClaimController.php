<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Models\Claim;
use App\Services\ClaimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClaimsExport;

class ClaimController extends Controller
{
    public function __construct(private ClaimService $claimService)
    {
        $this->middleware('auth:web'); // Explicitly use web guard for admin
        $this->middleware('permission:claim-list|claim-create|claim-edit|claim-delete', ['only' => ['index']]);
        $this->middleware('permission:claim-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:claim-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:claim-delete', ['only' => ['delete']]);
    }

    /**
     * Display a listing of claims.
     */
    public function index(Request $request): View
    {
        try {
            $claims = $this->claimService->getClaims($request);

            return view('claims.index', [
                'claims' => $claims,
                'sortField' => $request->input('sort_field', 'created_at'),
                'sortOrder' => $request->input('sort_order', 'desc'),
                'request' => $request->all()
            ]);
        } catch (\Throwable $th) {
            // Create empty paginated result to maintain view compatibility
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), // empty collection
                0, // total count
                15, // per page
                1, // current page
                ['path' => request()->url(), 'pageName' => 'page']
            );

            return view('claims.index', [
                'claims' => $emptyPaginator,
                'sortField' => 'created_at',
                'sortOrder' => 'desc',
                'request' => $request->all(),
                'error' => 'Failed to load claims: ' . $th->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new claim.
     */
    public function create(): View
    {
        // Get customer insurances without eager loading to avoid auth conflicts
        // The view will need to handle relationships individually if needed
        $customerInsurances = \App\Models\CustomerInsurance::where('status', true)
            ->orderBy('id', 'desc')
            ->get();

        return view('claims.create', compact('customerInsurances'));
    }

    /**
     * Store a newly created claim in storage.
     */
    public function store(StoreClaimRequest $request): RedirectResponse
    {
        try {
            $claim = $this->claimService->createClaim($request);
            return redirect()->route('claims.index')
                           ->with('success', 'Claim created successfully. Claim Number: ' . $claim->claim_number);
        } catch (\Throwable $th) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create claim: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified claim.
     */
    public function show(Claim $claim): View
    {
        $claim->load([
            'customer',
            'customerInsurance.insuranceCompany',
            'customerInsurance.policyType',
            'stages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'documents',
            'liabilityDetail'
        ]);

        return view('claims.show', compact('claim'));
    }

    /**
     * Show the form for editing the specified claim.
     */
    public function edit(Claim $claim): View
    {
        $claim->load(['customer', 'customerInsurance']);
        return view('claims.edit', compact('claim'));
    }

    /**
     * Update the specified claim in storage.
     */
    public function update(UpdateClaimRequest $request, Claim $claim): RedirectResponse
    {
        try {
            $updated = $this->claimService->updateClaim($request, $claim);

            if ($updated) {
                return redirect()->route('claims.index')
                               ->with('success', 'Claim updated successfully.');
            }

            return redirect()->back()->with('error', 'Failed to update claim.');
        } catch (\Throwable $th) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update claim: ' . $th->getMessage());
        }
    }

    /**
     * Update the status of the specified claim.
     */
    public function updateStatus(int $claimId, int $status): RedirectResponse
    {
        try {
            $updated = $this->claimService->updateClaimStatus($claimId, (bool) $status);

            if ($updated) {
                return redirect()->back()->with('success', 'Claim status updated successfully!');
            }

            return redirect()->back()->with('error', 'Failed to update claim status.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update claim status: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified claim from storage (soft delete).
     */
    public function delete(Claim $claim): RedirectResponse
    {
        try {
            $deleted = $this->claimService->deleteClaim($claim);

            if ($deleted) {
                return redirect()->back()->with('success', 'Claim deleted successfully!');
            }

            return redirect()->back()->with('error', 'Failed to delete claim.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete claim: ' . $th->getMessage());
        }
    }

    /**
     * Export claims to Excel.
     */
    public function export(Request $request)
    {
        try {
            return Excel::download(new ClaimsExport($request->all()), 'claims.xlsx');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to export claims: ' . $th->getMessage());
        }
    }

    /**
     * Search for policies/insurances (AJAX endpoint for wildcard search).
     */
    public function searchPolicies(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->input('search', '');

            // Debug logging
            \Log::info('Search Policies Request', [
                'search_term' => $searchTerm,
                'search_length' => strlen($searchTerm)
            ]);

            if (strlen($searchTerm) < 3) {
                return response()->json([
                    'results' => []
                ]);
            }

            $policies = $this->claimService->searchPolicies($searchTerm);

            \Log::info('Search Policies Results', [
                'search_term' => $searchTerm,
                'results_count' => count($policies)
            ]);

            return response()->json([
                'results' => $policies
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Failed to search policies: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get claim statistics (AJAX endpoint).
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->claimService->getClaimStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get statistics: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Send document list WhatsApp message (AJAX endpoint).
     */
    public function sendDocumentListWhatsApp(Claim $claim): JsonResponse
    {
        try {
            $result = $claim->sendDocumentListWhatsApp();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'preview' => $claim->insurance_type === 'Health'
                    ? $claim->getHealthInsuranceDocumentListMessage()
                    : $claim->getVehicleInsuranceDocumentListMessage()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Send pending documents WhatsApp message (AJAX endpoint).
     */
    public function sendPendingDocumentsWhatsApp(Claim $claim): JsonResponse
    {
        try {
            $result = $claim->sendPendingDocumentsWhatsApp();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'preview' => $claim->getPendingDocumentsMessage()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Send claim number WhatsApp message (AJAX endpoint).
     */
    public function sendClaimNumberWhatsApp(Claim $claim): JsonResponse
    {
        try {
            $result = $claim->sendClaimNumberWhatsApp();

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'preview' => $claim->getClaimNumberNotificationMessage()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get WhatsApp message preview (AJAX endpoint).
     */
    public function getWhatsAppPreview(Claim $claim, string $type): JsonResponse
    {
        try {
            $preview = '';

            switch ($type) {
                case 'document_list':
                    $preview = $claim->insurance_type === 'Health'
                        ? $claim->getHealthInsuranceDocumentListMessage()
                        : $claim->getVehicleInsuranceDocumentListMessage();
                    break;
                case 'pending_documents':
                    $preview = $claim->getPendingDocumentsMessage();
                    break;
                case 'claim_number':
                    $preview = $claim->getClaimNumberNotificationMessage();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid message type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'whatsapp_number' => $claim->getWhatsAppNumber()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get message preview: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update document status (AJAX endpoint).
     */
    public function updateDocumentStatus(Request $request, Claim $claim, int $documentId): JsonResponse
    {
        try {
            $document = $claim->documents()->findOrFail($documentId);
            $isSubmitted = $request->boolean('is_submitted');

            if ($isSubmitted) {
                $document->markAsSubmitted();
            } else {
                $document->markAsNotSubmitted();
            }

            return response()->json([
                'success' => true,
                'message' => 'Document status updated successfully',
                'document_completion' => $claim->getDocumentCompletionPercentage(),
                'required_completion' => $claim->getRequiredDocumentCompletionPercentage()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update document status: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Add new claim stage (AJAX endpoint).
     */
    public function addStage(Request $request, Claim $claim): JsonResponse
    {
        try {
            $request->validate([
                'stage_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'notes' => 'nullable|string',
                'send_whatsapp' => 'boolean'
            ]);

            // Mark current stage as not current
            $claim->stages()->where('is_current', true)->update(['is_current' => false]);

            // Create new stage
            $stage = $claim->stages()->create([
                'stage_name' => $request->stage_name,
                'description' => $request->description,
                'notes' => $request->notes,
                'is_current' => true,
                'is_completed' => false,
                'stage_date' => now(),
            ]);

            // Send WhatsApp if requested
            $whatsappResult = null;
            if ($request->boolean('send_whatsapp')) {
                $whatsappResult = $claim->sendStageUpdateWhatsApp($request->stage_name, $request->notes);
            }

            // Send email notification if enabled
            $claim->sendStageUpdateNotification($request->stage_name, $request->description, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Stage added successfully',
                'stage' => $stage,
                'whatsapp_result' => $whatsappResult
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stage: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update claim number (AJAX endpoint).
     */
    public function updateClaimNumber(Request $request, Claim $claim): JsonResponse
    {
        try {
            $request->validate([
                'claim_number' => 'required|string|max:255',
                'send_whatsapp' => 'boolean'
            ]);

            $claim->update([
                'claim_number' => $request->claim_number
            ]);

            // Send WhatsApp if requested
            $whatsappResult = null;
            if ($request->boolean('send_whatsapp')) {
                $whatsappResult = $claim->sendClaimNumberWhatsApp();
            }

            // Send email notification if enabled
            $claim->sendClaimNumberAssignedNotification();

            return response()->json([
                'success' => true,
                'message' => 'Claim number updated successfully',
                'whatsapp_result' => $whatsappResult
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update claim number: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update liability details (AJAX endpoint).
     */
    public function updateLiabilityDetails(Request $request, Claim $claim): JsonResponse
    {
        try {
            $request->validate([
                'claim_type' => 'required|in:Cashless,Reimbursement',
                'claim_amount' => 'nullable|numeric|min:0',
                'salvage_amount' => 'nullable|numeric|min:0',
                'less_claim_charge' => 'nullable|numeric|min:0',
                'amount_to_be_paid' => 'nullable|numeric|min:0',
                'less_salvage_amount' => 'nullable|numeric|min:0',
                'less_deductions' => 'nullable|numeric|min:0',
                'claim_amount_received' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string'
            ]);

            $liabilityDetail = $claim->liabilityDetail ?: $claim->liabilityDetail()->create([]);

            $liabilityDetail->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Liability details updated successfully',
                'liability_detail' => $liabilityDetail->fresh()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update liability details: ' . $th->getMessage()
            ], 500);
        }
    }
}