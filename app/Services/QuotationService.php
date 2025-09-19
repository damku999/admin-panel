<?php

namespace App\Services;

use App\Contracts\Repositories\QuotationRepositoryInterface;
use App\Contracts\Services\QuotationServiceInterface;
use App\Events\Quotation\QuotationGenerated;
use App\Models\Customer;
use App\Models\InsuranceCompany;
use App\Models\Quotation;
use App\Models\QuotationCompany;
use App\Services\PdfGenerationService;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationService extends BaseService implements QuotationServiceInterface
{
    use WhatsAppApiTrait;

    public function __construct(
        private PdfGenerationService $pdfService,
        private QuotationRepositoryInterface $quotationRepository
    ) {
    }

    public function createQuotation(array $data): Quotation
    {
        return $this->createInTransaction(function () use ($data) {
            // Note: QuotationRequested event disabled - policy types handled at company level

            $data['total_idv'] = $this->calculateTotalIdv($data);

            // Extract company data before creating quotation
            $companies = $data['companies'] ?? [];
            unset($data['companies']);

            $quotation = Quotation::create($data);

            // Create company quotes manually
            if (!empty($companies)) {
                $this->createManualCompanyQuotes($quotation, $companies);
            }

            // Fire QuotationGenerated event after successful creation
            QuotationGenerated::dispatch($quotation);

            return $quotation;
        });
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

    public function generateQuotesForSelectedCompanies(Quotation $quotation, array $companyIds): void
    {
        $companies = InsuranceCompany::whereIn('id', $companyIds)
            ->where('status', 1)
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
            'Zero Depreciation' => ($idv * ($rates['depreciation'] ?? 0.4) / 100) * $companyFactor,
            'Engine Protection' => ($idv * ($rates['engine_secure'] ?? 0.1) / 100) * $companyFactor,
            'Road Side Assistance' => 180 * $companyFactor,
            'NCB Protection' => ($idv * 0.05 / 100) * $companyFactor,
            'Invoice Protection' => ($idv * ($rates['return_to_invoice'] ?? 0.23) / 100) * $companyFactor,
            'Key Replacement' => 425 * $companyFactor,
            'Personal Accident' => 450 * $companyFactor,
            'Tyre Protection' => ($idv * ($rates['tyre_secure'] ?? 0.18) / 100) * $companyFactor,
            'Consumables' => ($idv * ($rates['consumables'] ?? 0.06) / 100) * $companyFactor,
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
        \Log::info('Starting WhatsApp quotation send', [
            'quotation_id' => $quotation->id,
            'quotation_number' => $quotation->quotation_number,
            'customer_name' => $quotation->customer->name ?? 'N/A',
            'whatsapp_number' => $quotation->whatsapp_number,
            'user_id' => auth()->user()->id ?? 'System'
        ]);

        $message = $this->generateWhatsAppMessageWithAttachment($quotation);
        $pdfPath = $this->pdfService->generateQuotationPdfForWhatsApp($quotation);

        try {
            $response = $this->whatsAppSendMessageWithAttachment($message, $quotation->whatsapp_number, $pdfPath);

            \Log::info('WhatsApp quotation sent successfully', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'whatsapp_number' => $quotation->whatsapp_number,
                'response' => $response,
                'user_id' => auth()->user()->id ?? 'System'
            ]);

            $quotation->update([
                'status' => 'Sent',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp quotation send failed', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'customer_name' => $quotation->customer->name ?? 'N/A',
                'whatsapp_number' => $quotation->whatsapp_number,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'System',
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to maintain the error flow
        } finally {
            // Clean up temporary PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
                \Log::debug('Temporary PDF file cleaned up', ['path' => $pdfPath]);
            }
        }
    }

    private function generateWhatsAppMessageWithAttachment(Quotation $quotation): string
    {
        $customer = $quotation->customer;
        $quotes = $quotation->quotationCompanies()->orderBy('final_premium')->get();
        $bestQuote = $quotes->first();

        $message = "🚗 *Insurance Quotation*\n\n";
        $message .= "Dear *{$customer->name}*,\n\n";
        $message .= "Your insurance quotation is ready! We have compared *{$quotes->count()} insurance companies* for you.\n\n";

        $message .= "🚙 *Vehicle Details:*\n";
        $message .= "• Vehicle: *{$quotation->make_model_variant}*\n";
        $message .= "• Registration: *{$quotation->vehicle_number}*\n";
        $message .= "• IDV: *₹" . number_format($quotation->total_idv) . "*\n";
        $message .= "• Policy: *{$quotation->policy_type}* - {$quotation->policy_tenure_years} Year(s)\n\n";

        if ($bestQuote) {
            $message .= "💰 *Best Premium:*\n";
            $message .= "• *{$bestQuote->insuranceCompany->name}*\n";
            $message .= "• Premium: *{$bestQuote->getFormattedPremium()}*";
            $message .= "\n\n";
        }

        $message .= "📊 *Premium Comparison:*\n";
        foreach ($quotes as $index => $quote) {
            $icon = $quote->is_recommended ? '⭐' : ($index + 1);
            $ranking = is_numeric($icon) ? "{$icon}." : $icon;
            $message .= "{$ranking} *{$quote->insuranceCompany->name}*: {$quote->getFormattedPremium()}";
            if ($quote->is_recommended) {
                $message .= " _(Recommended)_";
            }
            $message .= "\n";
        }

        // Calculate savings if more than one quote
        if ($quotes->count() > 1) {
            $highestQuote = $quotes->last();
            $savings = $highestQuote->final_premium - $bestQuote->final_premium;
            if ($savings > 0) {
                $message .= "\n💵 *You can save up to ₹" . number_format($savings) . "*\n";
            }
        }

        $message .= "\n📎 *Detailed PDF comparison attached*";
        $message .= "\n\n📞 For any queries or to proceed with purchase:";
        $message .= "\n\nBest regards,";
        $message .= "\nParth Rawal";
        $message .= "\nhttps://parthrawal.in";
        $message .= "\nYour Trusted Insurance Advisor";
        $message .= "\n\"Think of Insurance, Think of Us.\"";

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

    private function generateQuoteNumber(Quotation $quotation, $companyId): string
    {
        // Generate unique quote number using microsecond timestamp to avoid duplicates
        $microtime = (string) microtime(true);
        $uniqueId = str_replace('.', '', $microtime); // Remove decimal point
        $uniqueId = substr($uniqueId, -8); // Take last 8 digits for uniqueness

        return 'QT/' . date('y') . '/' . str_pad($quotation->id, 4, '0', STR_PAD_LEFT) .
               str_pad($companyId, 2, '0', STR_PAD_LEFT) .
               $uniqueId;
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


    private function getCompanyBenefits(InsuranceCompany $company): string
    {
        return "Comprehensive coverage with add-on benefits, 24/7 customer support, quick claim settlement, nationwide network of garages.";
    }

    private function getCompanyExclusions(InsuranceCompany $company): string
    {
        return "Pre-existing damages, wear and tear, consequential damages, driving under influence, use for commercial purposes.";
    }

    public function createManualCompanyQuotes(Quotation $quotation, array $companies): void
    {
        $processedQuotes = []; // Track processed quotes to avoid exact duplicates

        foreach ($companies as $index => $companyData) {
            // Create unique key based on multiple fields to allow same company with different plans
            $quoteKey = $companyData['insurance_company_id'] . '_' .
                       ($companyData['quote_number'] ?? '') . '_' .
                       ($companyData['basic_od_premium'] ?? '') . '_' .
                       $index; // Include index to ensure each form entry is processed

            // Skip only if this exact quote has already been processed
            if (in_array($quoteKey, $processedQuotes)) {
                continue;
            }
            $processedQuotes[] = $quoteKey;

            // Process addon breakdown to calculate total if needed
            $companyData = $this->processAddonBreakdown($companyData);
            $this->createManualCompanyQuote($quotation, $companyData);
        }

        // Set rankings if not provided
        $this->setRankings($quotation);
    }

    private function processAddonBreakdown(array $data): array
    {
        if (!isset($data['addon_covers_breakdown'])) {
            $data['addon_covers_breakdown'] = [];
            return $data;
        }

        // Calculate total addon premium from breakdown
        $totalAddon = 0;
        foreach ($data['addon_covers_breakdown'] as $addon) {
            if (is_array($addon) && isset($addon['price'])) {
                $totalAddon += floatval($addon['price']);
            } else {
                $totalAddon += floatval($addon);
            }
        }

        // Update total if not set or if breakdown total differs
        if (!isset($data['total_addon_premium']) || $data['total_addon_premium'] != $totalAddon) {
            $data['total_addon_premium'] = $totalAddon;
        }

        return $data;
    }

    private function createManualCompanyQuote(Quotation $quotation, array $data): QuotationCompany
    {
        // Process individual addon fields into breakdown (dynamic based on addon_covers table)
        $addonBreakdown = [];
        $addonCovers = \App\Models\AddonCover::getOrdered(1);
        
        foreach ($addonCovers as $addonCover) {
            $slug = \Str::slug($addonCover->name, '_');
            $field = "addon_{$slug}";
            
            if (isset($data[$field]) && $data[$field] > 0) {
                $noteField = $field . '_note';
                $addonBreakdown[$addonCover->name] = [
                    'price' => floatval($data[$field]),
                    'field' => $field,
                    'note' => $data[$noteField] ?? ''
                ];
            }
        }

        // Set the addon breakdown (always override with individual fields if they exist)
        if (!empty($addonBreakdown)) {
            $data['addon_covers_breakdown'] = $addonBreakdown;
        } elseif (!isset($data['addon_covers_breakdown'])) {
            $data['addon_covers_breakdown'] = [];
        }

        return QuotationCompany::create([
            'quotation_id' => $quotation->id,
            'insurance_company_id' => $data['insurance_company_id'],
            'quote_number' => $data['quote_number'] ?? $this->generateQuoteNumber($quotation, $data['insurance_company_id']),
            // Policy and coverage fields
            'policy_type' => $data['policy_type'] ?? 'Comprehensive',
            'policy_tenure_years' => $data['policy_tenure_years'] ?? 1,
            // IDV fields
            'idv_vehicle' => $data['idv_vehicle'] ?? 0,
            'idv_trailer' => $data['idv_trailer'] ?? 0,
            'idv_cng_lpg_kit' => $data['idv_cng_lpg_kit'] ?? 0,
            'idv_electrical_accessories' => $data['idv_electrical_accessories'] ?? 0,
            'idv_non_electrical_accessories' => $data['idv_non_electrical_accessories'] ?? 0,
            'total_idv' => $data['total_idv'] ?? 0,
            // Premium fields
            'basic_od_premium' => $data['basic_od_premium'],
            'tp_premium' => $data['tp_premium'] ?? 0,
            'cng_lpg_premium' => $data['cng_lpg_premium'] ?? 0,
            'total_od_premium' => $data['total_od_premium'] ?? $data['basic_od_premium'],
            'addon_covers_breakdown' => $data['addon_covers_breakdown'] ?? [],
            'total_addon_premium' => $data['total_addon_premium'] ?? 0,
            'net_premium' => $data['net_premium'] ?? 0,
            'sgst_amount' => $data['sgst_amount'] ?? 0,
            'cgst_amount' => $data['cgst_amount'] ?? 0,
            'total_premium' => $data['total_premium'] ?? 0,
            'roadside_assistance' => $data['roadside_assistance'] ?? 0,
            'final_premium' => $data['final_premium'] ?? 0,
            'is_recommended' => $data['is_recommended'] ?? false,
            'recommendation_note' => $data['recommendation_note'] ?? null,
            'ranking' => $data['ranking'] ?? 1,
            'benefits' => $data['benefits'] ?? null,
            'exclusions' => $data['exclusions'] ?? null,
        ]);
    }

    public function updateQuotationWithCompanies(Quotation $quotation, array $data): void
    {
        // Update quotation data
        $quotationData = $data;
        $companies = $quotationData['companies'] ?? [];
        unset($quotationData['companies']);

        $quotationData['total_idv'] = $this->calculateTotalIdv($quotationData);
        $quotation->update($quotationData);

        // Delete existing company quotes and create new ones
        $quotation->quotationCompanies()->delete();
        if (!empty($companies)) {
            $this->createManualCompanyQuotes($quotation, $companies);
        }
    }

    private function setRankings(Quotation $quotation): void
    {
        $quotes = $quotation->quotationCompanies()->orderBy('final_premium')->get();

        foreach ($quotes as $index => $quote) {
            if (!$quote->ranking || $quote->ranking === 1) {
                $quote->update(['ranking' => $index + 1]);
            }
        }
    }

    // Interface implementation methods

    public function getQuotations(Request $request): LengthAwarePaginator
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'customer_id' => $request->input('customer_id'),
        ];

        return $this->quotationRepository->getPaginated($filters, 15);
    }

    public function deleteQuotation(Quotation $quotation): bool
    {
        return $this->deleteInTransaction(
            fn() => $this->quotationRepository->delete($quotation->id)
        );
    }

    public function calculatePremium(array $data): float
    {
        return $this->calculateTotalIdv($data);
    }

    public function getQuotationFormData(): array
    {
        return [
            'customers' => Customer::where('status', 1)->orderBy('name')->get(),
            'insuranceCompanies' => InsuranceCompany::where('status', 1)->orderBy('name')->get(),
            'addonCovers' => \App\Models\AddonCover::getOrdered(1),
        ];
    }
}