<?php

namespace App\Services;

use App\Models\InsuranceCompany;
use App\Models\Quotation;
use App\Models\QuotationCompany;
use App\Services\PdfGenerationService;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    use WhatsAppApiTrait;

    public function __construct(
        private PdfGenerationService $pdfService
    ) {
    }

    public function createQuotation(array $data): Quotation
    {
        $data['total_idv'] = $this->calculateTotalIdv($data);
        $quotation = Quotation::create($data);
        // Auto-generate quotes from companies if enabled
        $this->generateCompanyQuotes($quotation);

        return $quotation;
    }

    public function generateCompanyQuotes(Quotation $quotation): void
    {
        $companies = InsuranceCompany::where('status', 1)
            ->limit(5) // Maximum 5 companies as requested
            ->get();

        foreach ($companies as $company) {
            $this->generateCompanyQuote($quotation, $company);
        }

        // Set recommendations
        $this->setRecommendations($quotation);
    }

    private function generateCompanyQuote(Quotation $quotation, InsuranceCompany $company): QuotationCompany
    {
        $baseData = $this->calculateBasePremium($quotation, $company);
        $addonData = $this->calculateAddonPremiums($quotation, $company);

        $netPremium = $baseData['total_od_premium'] + $addonData['total_addon_premium'];
        $sgstAmount = $netPremium * 0.09; // 9% SGST
        $cgstAmount = $netPremium * 0.09; // 9% CGST
        $totalPremium = $netPremium + $sgstAmount + $cgstAmount;
        $roadsideAssistance = $this->calculateRoadsideAssistance($company);
        $finalPremium = $totalPremium + $roadsideAssistance;

        return QuotationCompany::create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $company->id,
            'quote_number' => $this->generateQuoteNumber($quotation, $company),
            'plan_name' => $this->getCompanyPlanName($company),
            'basic_od_premium' => $baseData['basic_od_premium'],
            'cng_lpg_premium' => $baseData['cng_lpg_premium'],
            'total_od_premium' => $baseData['total_od_premium'],
            'addon_covers_breakdown' => $addonData['breakdown'],
            'total_addon_premium' => $addonData['total_addon_premium'],
            'net_premium' => $netPremium,
            'sgst_amount' => $sgstAmount,
            'cgst_amount' => $cgstAmount,
            'total_premium' => $totalPremium,
            'roadside_assistance' => $roadsideAssistance,
            'final_premium' => $finalPremium,
            'benefits' => $this->getCompanyBenefits($company),
            'exclusions' => $this->getCompanyExclusions($company),
        ]);
    }

    private function calculateBasePremium(Quotation $quotation, InsuranceCompany $company): array
    {
        // Base premium calculation based on IDV and company factors
        $idv = $quotation->total_idv;
        $companyFactor = $this->getCompanyRatingFactor($company);

        // Basic OD premium calculation (simplified)
        $basicRate = $this->getBasicOdRate($quotation);
        $basicOdPremium = ($idv * $basicRate / 100) * $companyFactor;

        // CNG/LPG kit premium
        $cngLpgPremium = 0;
        if (in_array($quotation->fuel_type, ['CNG', 'Hybrid']) && $quotation->idv_cng_lpg_kit > 0) {
            $cngLpgPremium = ($quotation->idv_cng_lpg_kit * 0.05) * $companyFactor;
        }

        return [
            'basic_od_premium' => round($basicOdPremium, 2),
            'cng_lpg_premium' => round($cngLpgPremium, 2),
            'total_od_premium' => round($basicOdPremium + $cngLpgPremium, 2),
        ];
    }

    private function calculateAddonPremiums(Quotation $quotation, InsuranceCompany $company): array
    {
        $addons = $quotation->addon_covers ?? [];
        $breakdown = [];
        $totalAddonPremium = 0;
        $companyFactor = $this->getCompanyRatingFactor($company);

        $addonRates = $this->getAddonRates($company);

        foreach ($addons as $addon) {
            $premium = $this->calculateAddonPremium($addon, $quotation, $addonRates, $companyFactor);
            if ($premium > 0) {
                $breakdown[$addon] = $premium;
                $totalAddonPremium += $premium;
            }
        }

        return [
            'breakdown' => $breakdown,
            'total_addon_premium' => round($totalAddonPremium, 2),
        ];
    }

    private function calculateAddonPremium(string $addon, Quotation $quotation, array $rates, float $companyFactor): float
    {
        $idv = $quotation->total_idv;

        return match ($addon) {
            'depreciation_reimbursement' => ($idv * ($rates['depreciation'] ?? 0.4) / 100) * $companyFactor,
            'emergency_transport' => 180 * $companyFactor,
            'consumables' => ($idv * ($rates['consumables'] ?? 0.06) / 100) * $companyFactor,
            'key_replacement' => 425 * $companyFactor,
            'personal_belongings' => 180 * $companyFactor,
            'tyre_secure' => ($idv * ($rates['tyre_secure'] ?? 0.18) / 100) * $companyFactor,
            'engine_secure' => ($idv * ($rates['engine_secure'] ?? 0.1) / 100) * $companyFactor,
            'glass_repair' => 0, // Usually included
            'return_to_invoice' => ($idv * ($rates['return_to_invoice'] ?? 0.23) / 100) * $companyFactor,
            'emergency_medical' => 450 * $companyFactor,
            default => 0,
        };
    }

    private function setRecommendations(Quotation $quotation): void
    {
        $quotes = $quotation->quotationCompanies()->orderBy('final_premium')->get();

        // Set ranking
        foreach ($quotes as $index => $quote) {
            $quote->update(['ranking' => $index + 1]);
        }

        // Mark best value quote as recommended (lowest premium with good coverage)
        $recommended = $quotes->first();
        if ($recommended) {
            $recommended->update(['is_recommended' => true]);
        }
    }

    public function sendQuotationViaWhatsApp(Quotation $quotation): void
    {
        $message = $this->generateWhatsAppMessage($quotation);
        $this->whatsAppSendMessage($message, $quotation->whatsapp_number);

        $quotation->update([
            'status' => 'Sent',
            'sent_at' => now(),
        ]);
    }

    private function generateWhatsAppMessage(Quotation $quotation): string
    {
        $customer = $quotation->customer;
        $recommendedQuote = $quotation->recommendedQuote();
        $bestQuote = $quotation->bestQuote();

        $message = "🚗 *MIDAS Insurance Quotation*\n\n";
        $message .= "Dear {$customer->name},\n\n";
        $message .= "Your insurance quotation is ready!\n\n";
        $message .= "📋 *Vehicle Details:*\n";
        $message .= "• Vehicle: {$quotation->make_model_variant}\n";
        $message .= "• Registration: {$quotation->vehicle_number}\n";
        $message .= "• IDV: ₹" . number_format($quotation->total_idv) . "\n\n";

        $message .= "💰 *Best Quote:*\n";
        if ($bestQuote) {
            $message .= "• Company: {$bestQuote->insuranceCompany->name}\n";
            $message .= "• Premium: {$bestQuote->getFormattedPremium()}\n";
            $message .= "• Plan: {$bestQuote->plan_name}\n\n";
        }

        $message .= "📊 *All Quotes:*\n";
        foreach ($quotation->quotationCompanies as $quote) {
            $icon = $quote->is_recommended ? '⭐' : '•';
            $message .= "{$icon} {$quote->insuranceCompany->name}: {$quote->getFormattedPremium()}\n";
        }

        $message .= "\n🔗 View detailed comparison: [Link to PDF]\n";
        $message .= "\n📞 Contact us for more details!\n";
        $message .= "\n*MIDAS Insurance Services*";

        return $message;
    }

    public function generatePdf(Quotation $quotation)
    {
        return $this->pdfService->generateQuotationPdf($quotation);
    }

    // Helper methods for calculations
    private function calculateTotalIdv(array $data): float
    {
        return ($data['idv_vehicle'] ?? 0) +
               ($data['idv_trailer'] ?? 0) +
               ($data['idv_cng_lpg_kit'] ?? 0) +
               ($data['idv_electrical_accessories'] ?? 0) +
               ($data['idv_non_electrical_accessories'] ?? 0);
    }

    private function generateQuoteNumber(Quotation $quotation, InsuranceCompany $company): string
    {
        return 'QT/' . date('y') . '/' . str_pad($quotation->id, 6, '0', STR_PAD_LEFT) .
               str_pad($company->id, 4, '0', STR_PAD_LEFT);
    }

    private function getCompanyRatingFactor(InsuranceCompany $company): float
    {
        // Different companies have different rating factors
        return match ($company->name) {
            'TATA AIG' => 1.0,
            'HDFC ERGO' => 0.95,
            'ICICI Lombard' => 1.05,
            'Bajaj Allianz' => 0.98,
            'Reliance General' => 0.92,
            default => 1.0,
        };
    }

    private function getBasicOdRate(Quotation $quotation): float
    {
        // Basic OD rate based on vehicle age and IDV
        $vehicleAge = date('Y') - $quotation->manufacturing_year;

        if ($vehicleAge <= 1) {
            return 1.2;
        }
        if ($vehicleAge <= 3) {
            return 1.8;
        }
        if ($vehicleAge <= 5) {
            return 2.4;
        }
        return 3.0;
    }

    private function getAddonRates(InsuranceCompany $company): array
    {
        // Company-specific addon rates
        return [
            'depreciation' => 0.4,
            'consumables' => 0.06,
            'tyre_secure' => 0.18,
            'engine_secure' => 0.1,
            'return_to_invoice' => 0.23,
        ];
    }

    private function calculateRoadsideAssistance(InsuranceCompany $company): float
    {
        return 136.88; // Standard rate
    }

    private function getCompanyPlanName(InsuranceCompany $company): string
    {
        return match ($company->name) {
            'TATA AIG' => 'SAPPHIRE PLUS',
            'HDFC ERGO' => 'COMPLETE CARE',
            'ICICI Lombard' => 'COMPREHENSIVE PLUS',
            'Bajaj Allianz' => 'COMPLETE PROTECTION',
            'Reliance General' => 'TOTAL SECURE',
            default => 'COMPREHENSIVE PLAN',
        };
    }

    private function getCompanyBenefits(InsuranceCompany $company): string
    {
        return "Comprehensive coverage with add-on benefits, 24/7 customer support, quick claim settlement, nationwide network of garages.";
    }

    private function getCompanyExclusions(InsuranceCompany $company): string
    {
        return "Pre-existing damages, wear and tear, consequential damages, driving under influence, use for commercial purposes.";
    }
}