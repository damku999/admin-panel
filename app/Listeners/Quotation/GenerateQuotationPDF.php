<?php

namespace App\Listeners\Quotation;

use App\Events\Document\PDFGenerationRequested;
use App\Events\Quotation\QuotationGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateQuotationPDF implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(QuotationGenerated $event): void
    {
        $quotation = $event->quotation;
        
        // Generate PDF filename
        $fileName = "quotation_{$quotation->quotation_number}_" . date('YmdHis') . '.pdf';
        
        // Request PDF generation
        PDFGenerationRequested::dispatch(
            documentType: 'quotation',
            templateView: 'pdfs.quotation',
            templateData: [
                'quotation' => $quotation->load(['customer', 'policyType', 'quotationCompanies.insuranceCompany']),
                'generated_date' => now()->format('d/m/Y'),
                'best_premium' => $event->bestPremium,
                'company_count' => $event->companyCount,
                'generated_by' => $event->generatedBy,
            ],
            fileName: $fileName,
            storagePath: 'pdfs/quotations',
            pdfOptions: [
                'format' => 'A4',
                'orientation' => 'portrait',
                'margin_top' => 15,
                'margin_bottom' => 15,
            ],
            priority: $event->isHighValueQuotation() ? 3 : 5,
            referenceId: "quotation_{$quotation->id}",
            customerId: $quotation->customer_id,
            callbackEvent: 'QuotationPDFGenerated'
        );
    }

    public function failed(QuotationGenerated $event, \Throwable $exception): void
    {
        \Log::error('Failed to generate quotation PDF', [
            'quotation_id' => $event->quotation->id,
            'quotation_number' => $event->quotation->quotation_number,
            'error' => $exception->getMessage(),
        ]);
    }
}