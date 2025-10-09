<?php

namespace App\Services\Notification;

use App\Models\AppSetting;
use Carbon\Carbon;

/**
 * Variable Resolver Service
 *
 * Resolves template variables to actual values from database
 */
class VariableResolverService
{
    public function __construct(
        protected VariableRegistryService $registry
    ) {}

    /**
     * Resolve all variables in template
     *
     * @param  string  $template  Template content with {{variables}}
     * @param  NotificationContext  $context  Context with customer, insurance, etc.
     * @return string Template with variables replaced
     */
    public function resolveTemplate(string $template, NotificationContext $context): string
    {
        // Extract all variables from template
        $variables = $this->registry->extractVariablesFromTemplate($template);

        // Resolve each variable
        $resolved = $template;
        foreach ($variables as $varKey) {
            $value = $this->resolveVariable($varKey, $context);

            // Replace in template (support both {{var}} format)
            $resolved = str_replace('{{'.$varKey.'}}', $value ?? '', $resolved);
        }

        return $resolved;
    }

    /**
     * Resolve single variable to its value
     *
     * @param  string  $variableKey  Variable key (e.g., 'customer_name')
     * @param  NotificationContext  $context  Context with data
     * @return mixed Resolved value or null
     */
    public function resolveVariable(string $variableKey, NotificationContext $context): mixed
    {
        // Get variable metadata from registry
        $metadata = $this->registry->getVariableMetadata($variableKey);

        if (! $metadata) {
            // Unknown variable, return placeholder with double braces
            return "{{{$variableKey}}}";
        }

        // Resolve based on source
        $value = $this->resolveBySource($metadata['source'], $context, $metadata);

        // Format value if needed
        if ($value !== null && isset($metadata['format'])) {
            $value = $this->formatValue($value, $metadata['format'], $metadata['type']);
        }

        return $value;
    }

    /**
     * Resolve value by source definition
     *
     * @param  string  $source  Source definition from config
     * @param  NotificationContext  $context  Context with data
     * @param  array  $metadata  Variable metadata
     */
    protected function resolveBySource(string $source, NotificationContext $context, array $metadata): mixed
    {
        // Parse source type
        if (str_starts_with($source, 'setting:')) {
            return $this->resolveFromSettings($source, $context);
        }

        if (str_starts_with($source, 'computed:')) {
            return $this->resolveComputed($source, $context, $metadata);
        }

        if (str_starts_with($source, 'system:')) {
            return $this->resolveSystem($source, $context);
        }

        // Regular entity.property format
        return $this->resolveFromEntity($source, $context);
    }

    /**
     * Resolve from entity property (e.g., 'customer.name')
     *
     * @param  string  $source  Source path
     * @param  NotificationContext  $context  Context
     */
    protected function resolveFromEntity(string $source, NotificationContext $context): mixed
    {
        $parts = explode('.', $source);

        if (count($parts) < 2) {
            return null;
        }

        $entityName = $parts[0];
        $propertyPath = array_slice($parts, 1);

        // Get entity from context
        $entity = match ($entityName) {
            'customer' => $context->customer,
            'insurance' => $context->insurance,
            'quotation' => $context->quotation,
            'claim' => $context->claim,
            default => null,
        };

        if (! $entity) {
            return null;
        }

        // Navigate property path
        $value = $entity;
        foreach ($propertyPath as $property) {
            if (is_object($value)) {
                // For Laravel models, try direct access first (uses __get magic method)
                if ($value instanceof \Illuminate\Database\Eloquent\Model) {
                    // Try attribute access
                    if (isset($value->$property)) {
                        $value = $value->$property;
                    } elseif (method_exists($value, $property)) {
                        // Try relationship method
                        $value = $value->$property;
                    } else {
                        return null;
                    }
                } else {
                    // For regular objects
                    if (property_exists($value, $property)) {
                        $value = $value->$property;
                    } elseif (method_exists($value, $property)) {
                        $value = $value->$property;
                    } else {
                        return null;
                    }
                }
            } elseif (is_array($value)) {
                $value = $value[$property] ?? null;
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Resolve from app settings
     *
     * @param  string  $source  Source definition (e.g., 'setting:company.name')
     * @param  NotificationContext  $context  Context
     */
    protected function resolveFromSettings(string $source, NotificationContext $context): mixed
    {
        // Remove 'setting:' prefix
        $settingKey = substr($source, 8);

        // Check if already loaded in context
        $value = $context->getSetting($settingKey);

        if ($value !== null) {
            return $value;
        }

        // Load from database
        // Format: category.key (e.g., 'company.name')
        [$category, $key] = explode('.', $settingKey, 2);

        $setting = AppSetting::where('category', $category)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting?->value;
    }

    /**
     * Resolve computed values
     *
     * @param  string  $source  Source definition (e.g., 'computed:days_remaining')
     * @param  NotificationContext  $context  Context
     * @param  array  $metadata  Variable metadata
     */
    protected function resolveComputed(string $source, NotificationContext $context, array $metadata): mixed
    {
        // Remove 'computed:' prefix
        $computation = substr($source, 9);

        return match ($computation) {
            'days_remaining' => $this->computeDaysRemaining($context),
            'policy_tenure' => $this->computePolicyTenure($context),
            'best_company' => $this->computeBestCompany($context),
            'best_premium' => $this->computeBestPremium($context),
            'comparison_list' => $this->computeComparisonList($context),
            'pending_documents' => $this->computePendingDocuments($context),
            default => null,
        };
    }

    /**
     * Resolve system values
     *
     * @param  string  $source  Source definition (e.g., 'system:current_date')
     * @param  NotificationContext  $context  Context
     */
    protected function resolveSystem(string $source, NotificationContext $context): mixed
    {
        // Remove 'system:' prefix
        $systemKey = substr($source, 7);

        return match ($systemKey) {
            'current_date' => Carbon::now(),
            'current_year' => Carbon::now()->year,
            default => null,
        };
    }

    /**
     * Format value based on type and format specification
     *
     * @param  mixed  $value  Value to format
     * @param  string  $format  Format type (date, currency, percentage, html)
     * @param  string  $type  Value type
     * @return mixed Formatted value
     */
    protected function formatValue(mixed $value, string $format, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($format) {
            'date' => $this->formatDate($value),
            'currency' => $this->formatCurrency($value),
            'percentage' => $this->formatPercentage($value),
            'd-M-Y' => $this->formatDate($value, 'd-M-Y'),
            'html' => $value, // Already formatted
            default => $value,
        };
    }

    /**
     * Format date value
     *
     * @param  mixed  $value  Date value
     * @param  string  $format  Date format
     */
    protected function formatDate(mixed $value, string $format = 'd-M-Y'): string
    {
        if ($value instanceof \DateTime || $value instanceof Carbon) {
            return $value->format($format);
        }

        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format($format);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return (string) $value;
    }

    /**
     * Format currency value in Indian numbering system
     *
     * @param  mixed  $value  Numeric value
     */
    protected function formatCurrency(mixed $value): string
    {
        if (! is_numeric($value)) {
            return (string) $value;
        }

        $number = (float) $value;

        // Format using Indian numbering system (lakhs and crores)
        if ($number < 0) {
            return '-₹' . $this->formatIndianNumber(abs($number));
        }

        return '₹' . $this->formatIndianNumber($number);
    }

    /**
     * Format number in Indian numbering system
     *
     * @param float $number
     * @return string
     */
    protected function formatIndianNumber(float $number): string
    {
        $number = number_format($number, 0, '.', '');

        // If less than 1000, just return as is
        if ($number < 1000) {
            return $number;
        }

        // Convert to string and reverse for easier processing
        $numStr = strrev($number);

        // First 3 digits (rightmost)
        $result = substr($numStr, 0, 3);

        // Remaining digits in groups of 2
        $remaining = substr($numStr, 3);
        if ($remaining) {
            $result .= ',' . implode(',', str_split($remaining, 2));
        }

        // Reverse back
        return strrev($result);
    }

    /**
     * Format percentage value
     *
     * @param  mixed  $value  Numeric value
     */
    protected function formatPercentage(mixed $value): string
    {
        if (! is_numeric($value)) {
            return (string) $value;
        }

        return number_format((float) $value, 1).'%';
    }

    // =======================================================
    // COMPUTED VALUE METHODS
    // =======================================================

    /**
     * Compute days remaining until policy expiry
     */
    protected function computeDaysRemaining(NotificationContext $context): ?string
    {
        if (! $context->hasInsurance() || ! $context->insurance->expired_date) {
            return null;
        }

        $expiry = Carbon::parse($context->insurance->expired_date);
        $now = Carbon::now();

        if ($expiry < $now) {
            return '0'; // Expired
        }

        return (string) $now->diffInDays($expiry);
    }

    /**
     * Compute policy tenure in years
     */
    protected function computePolicyTenure(NotificationContext $context): ?string
    {
        if (! $context->hasInsurance() ||
            ! $context->insurance->start_date ||
            ! $context->insurance->expired_date) {
            return null;
        }

        $start = Carbon::parse($context->insurance->start_date);
        $end = Carbon::parse($context->insurance->expired_date);

        $years = $start->diffInYears($end);

        if ($years == 1) {
            return '1 Year';
        }

        return "{$years} Years";
    }

    /**
     * Compute best company from quotation
     */
    protected function computeBestCompany(NotificationContext $context): ?string
    {
        if (! $context->hasQuotation() || ! $context->quotation->quotationCompanies) {
            return null;
        }

        $bestQuote = $context->quotation->quotationCompanies
            ->sortBy('final_premium')
            ->first();

        return $bestQuote?->insuranceCompany?->name;
    }

    /**
     * Compute best premium from quotation (returns formatted currency string)
     */
    protected function computeBestPremium(NotificationContext $context): ?string
    {
        if (! $context->hasQuotation() || ! $context->quotation->quotationCompanies) {
            return null;
        }

        $bestQuote = $context->quotation->quotationCompanies
            ->sortBy('final_premium')
            ->first();

        $premium = $bestQuote?->final_premium;

        return $premium !== null ? $this->formatCurrency($premium) : null;
    }

    /**
     * Compute comparison list from quotation
     */
    protected function computeComparisonList(NotificationContext $context): ?string
    {
        if (! $context->hasQuotation() || ! $context->quotation->quotationCompanies) {
            return null;
        }

        $quotes = $context->quotation->quotationCompanies
            ->sortBy('final_premium');

        $lines = [];
        $index = 1;

        foreach ($quotes as $quote) {
            $company = $quote->insuranceCompany?->name ?? 'Unknown';
            $premium = $this->formatCurrency($quote->final_premium ?? 0);
            $lines[] = "{$index}. {$company} - {$premium}";
            $index++;
        }

        return implode("\n", $lines);
    }

    /**
     * Compute pending documents list from claim
     */
    protected function computePendingDocuments(NotificationContext $context): ?string
    {
        if (! $context->hasClaim()) {
            return null;
        }

        // Get pending documents from claim->documents relationship
        $pendingDocuments = $context->claim->documents()
            ->where('is_submitted', false)
            ->get();

        if ($pendingDocuments->isEmpty()) {
            return 'No pending documents';
        }

        // Build numbered list of pending documents
        $lines = [];
        $counter = 1;
        foreach ($pendingDocuments as $document) {
            $lines[] = $counter.'. '.$document->document_name;
            $counter++;
        }

        return implode("\n", $lines);
    }

    /**
     * Resolve all variables and return as array
     *
     * @param  NotificationContext  $context  Context
     * @param  array|null  $variableKeys  Specific variables to resolve, or null for all
     * @return array Associative array of variable => value
     */
    public function resolveAllVariables(NotificationContext $context, ?array $variableKeys = null): array
    {
        if ($variableKeys === null) {
            $variableKeys = $this->registry->getAllVariables()->pluck('key')->toArray();
        }

        $resolved = [];

        foreach ($variableKeys as $varKey) {
            $resolved[$varKey] = $this->resolveVariable($varKey, $context);
        }

        return $resolved;
    }

    /**
     * Validate if all required variables can be resolved
     *
     * @param  string  $template  Template content
     * @param  NotificationContext  $context  Context
     * @return array ['valid' => bool, 'unresolved' => array]
     */
    public function validateTemplateResolution(string $template, NotificationContext $context): array
    {
        $variables = $this->registry->extractVariablesFromTemplate($template);
        $unresolved = [];

        foreach ($variables as $varKey) {
            $value = $this->resolveVariable($varKey, $context);

            // Check if variable was actually resolved (not null or placeholder)
            if ($value === null || $value === "{{{$varKey}}}") {
                $unresolved[] = $varKey;
            }
        }

        return [
            'valid' => empty($unresolved),
            'unresolved' => $unresolved,
        ];
    }
}
