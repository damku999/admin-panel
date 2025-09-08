<?php

namespace App\Http\Controllers;

use App\Contracts\Services\QuotationServiceInterface;
use App\Http\Requests\CreateQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Quotation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(private QuotationServiceInterface $quotationService)
    {
        $this->middleware('auth');
        $this->middleware('permission:quotation-list|quotation-create|quotation-edit|quotation-delete', ['only' => ['index']]);
        $this->middleware('permission:quotation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:quotation-edit', ['only' => ['show', 'edit', 'update']]);
        $this->middleware('permission:quotation-generate', ['only' => ['generateQuotes']]);
        $this->middleware('permission:quotation-send-whatsapp', ['only' => ['sendToWhatsApp']]);
        $this->middleware('permission:quotation-download-pdf', ['only' => ['downloadPdf']]);
        $this->middleware('permission:quotation-delete', ['only' => ['delete']]);
    }

    public function index(Request $request): View
    {
        $quotations = $this->quotationService->getQuotations($request);
        return view('quotations.index', compact('quotations'));
    }

    public function create(): View
    {
        $formData = $this->quotationService->getQuotationFormData();
        return view('quotations.create', $formData);
    }

    public function store(CreateQuotationRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        info($request->validated());
        try {
            $quotation = $this->quotationService->createQuotation($request->validated());

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation created successfully. Generating quotes from multiple companies...');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quotation: ' . $th->getMessage());
        }
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['customer', 'quotationCompanies.insuranceCompany']);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View
    {
        $formData = $this->quotationService->getQuotationFormData();
        $formData['quotation'] = $quotation;
        
        return view('quotations.edit', $formData);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Update quotation with manual company data
            $this->quotationService->updateQuotationWithCompanies($quotation, $data);

            DB::commit();

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation updated successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation: ' . $th->getMessage());
        }
    }


    public function generateQuotes(Quotation $quotation): RedirectResponse
    {
        try {
            $this->quotationService->generateCompanyQuotes($quotation);

            return redirect()->back()
                ->with('success', 'Quotes generated successfully from all companies!');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', 'Failed to generate quotes: ' . $th->getMessage());
        }
    }

    public function sendToWhatsApp(Quotation $quotation): RedirectResponse
    {
        try {
            $this->quotationService->sendQuotationViaWhatsApp($quotation);

            return redirect()->back()
                ->with('success', 'Quotation sent via WhatsApp successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', 'Failed to send quotation: ' . $th->getMessage());
        }
    }

    public function downloadPdf(Quotation $quotation)
    {
        try {
            return $this->quotationService->generatePdf($quotation);
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('error', 'Failed to generate PDF: ' . $th->getMessage());
        }
    }

    public function getQuoteFormHtml(Request $request)
    {
        $formData = $this->quotationService->getQuotationFormData();
        $formData['currentIndex'] = $request->input('index', 0);
        
        return view('quotations.partials.quote-form', $formData)->render();
    }

    public function delete(Quotation $quotation): RedirectResponse
    {
        try {
            $quoteReference = $quotation->getQuoteReference();
            $companiesCount = $quotation->quotationCompanies()->count();
            
            $deleted = $this->quotationService->deleteQuotation($quotation);
            
            if ($deleted) {
                $message = "Quotation {$quoteReference} deleted successfully!";
                if ($companiesCount > 0) {
                    $message .= " ({$companiesCount} company quote(s) also removed)";
                }
                
                return redirect()->route('quotations.index')->with('success', $message);
            }
            
            return redirect()->back()->with('error', 'Failed to delete quotation.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete quotation: ' . $th->getMessage());
        }
    }
}
