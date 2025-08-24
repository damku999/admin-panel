<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Motor Insurance Quote Comparison</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .customer-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .customer-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            background: #f5f5f5;
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        .info-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .info-row {
            display: table-row;
        }

        .info-item {
            display: table-cell;
            padding: 3px 5px;
            border-bottom: 1px solid #eee;
            width: 25%;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .info-value {
            margin-left: 10px;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }

        .comparison-table th {
            background: #333;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #333;
        }

        .comparison-table td {
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .section-header {
            background: #666 !important;
            color: white !important;
            font-weight: bold;
            text-align: left !important;
        }

        .row-header {
            background: #f5f5f5;
            font-weight: bold;
            text-align: left !important;
            padding-left: 10px !important;
        }

        .currency {
            text-align: right;
            font-weight: bold;
            color: #2c5f2d;
        }

        .total-row {
            background: #e8e8e8 !important;
            font-weight: bold;
        }

        .final-total {
            background: #2c5f2d !important;
            color: white !important;
            font-weight: bold;
            font-size: 12px;
        }

        .ranking {
            background: #d4862a !important;
            color: white !important;
            font-weight: bold;
            font-size: 12px;
        }

        .rank-1 {
            background: #d4862a !important;
        }

        .rank-2 {
            background: #95a5a6 !important;
        }

        .rank-3 {
            background: #e67e22 !important;
        }

        .company-column {
            width: 15%;
        }

        .description-column {
            width: 40%;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1><a href="https://midastech.in"> MIDAS </a> | Insurance Quote Comparison</h1>
        {{-- <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p> --}}
    </div>

    <div class="customer-info">
        <h3>Customer & Vehicle Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $quotation->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mobile:</span>
                    <span class="info-value">{{ $quotation->customer->mobile_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehicle No:</span>
                    <span class="info-value">{{ $quotation->vehicle_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Policy Type:</span>
                    <span class="info-value">{{ $quotation->policy_type ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Make Model:</span>
                    <span class="info-value">{{ $quotation->make_model_variant ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">RTO:</span>
                    <span class="info-value">{{ $quotation->rto_location ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">MFG Year:</span>
                    <span class="info-value">{{ $quotation->manufacturing_year ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fuel Type:</span>
                    <span class="info-value">{{ $quotation->fuel_type ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">NCB Percentage:</span>
                    <span class="info-value"
                        style="font-weight: bold; color: #2c5f2d;">{{ $quotation->ncb_percentage ?? 0 }}%</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total IDV:</span>
                    <span class="info-value">₹{{ number_format($quotation->total_idv ?? 0) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Seating Capacity:</span>
                    <span class="info-value">{{ $quotation->seating_capacity ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Policy Tenure:</span>
                    <span class="info-value">{{ $quotation->policy_tenure_years ?? 'N/A' }} Years</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Date:</span>
                    <span
                        class="info-value">{{ $quotation->date_of_registration ? \Carbon\Carbon::parse($quotation->date_of_registration)->format('d/m/Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Insurance Coverage Details Section -->
    {{-- <div class="customer-info" style="margin-top: 15px;">
        <h3>Insurance Coverage Details</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Policy Type:</span>
                    <span class="info-value">{{ $quotation->policy_type ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Policy Tenure:</span>
                    <span class="info-value">{{ $quotation->policy_tenure_years ?? 'N/A' }} Year(s)</span>
                </div>
                <div class="info-item">
                    <span class="info-label">NCB Discount:</span>
                    <span class="info-value" style="font-weight: bold; color: #2c5f2d;">{{ $quotation->ncb_percentage ?? 0 }}%</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ $quotation->status ?? 'Draft' }}</span>
                </div>
            </div>
            @if ($quotation->addon_covers && count($quotation->addon_covers) > 0)
            <div class="info-row" style="border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                <div class="info-item" style="width: 100%;">
                    <span class="info-label">Add-on Covers Selected:</span>
                    <span class="info-value">{{ implode(', ', $quotation->addon_covers) }}</span>
                </div>
            </div>
            @endif
        </div>
    </div> --}}

    <table class="comparison-table">
        <thead>
            <tr>
                <th class="description-column">Description</th>
                @foreach ($quotation->quotationCompanies as $company)
                    <th class="company-column">{{ strtoupper($company->insuranceCompany->name) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- Quote Information -->
            <tr>
                <td class="row-header">Quote Number</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td>{{ $company->quote_number ?? 'N/A' }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">Plan Name</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td>{{ $company->plan_name ?? 'N/A' }}</td>
                @endforeach
            </tr>

            <!-- Basic Premium Section -->
            <tr>
                <td class="section-header" colspan="{{ count($quotation->quotationCompanies) + 1 }}">Premium Breakdown
                </td>
            </tr>
            <tr>
                <td class="row-header">Basic OD Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->basic_od_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">Third Party Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->tp_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">CNG/LPG Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->cng_lpg_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">Total OD Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->total_od_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>

            <!-- Add On Covers Section -->
            <tr>
                <td class="section-header" colspan="{{ count($quotation->quotationCompanies) + 1 }}">Add On Covers</td>
            </tr>
            @php
                $allAddons = [];
                foreach ($quotation->quotationCompanies as $company) {
                    if (isset($company->addon_covers_breakdown)) {
                        $breakdown = is_string($company->addon_covers_breakdown)
                            ? json_decode($company->addon_covers_breakdown, true)
                            : $company->addon_covers_breakdown;
                        if ($breakdown) {
                            foreach ($breakdown as $addonName => $addonData) {
                                if ($addonName !== 'Others' && isset($addonData['price']) && $addonData['price'] > 0) {
                                    $allAddons[$addonName] = true;
                                }
                            }
                        }
                    }
                }
            @endphp

            @foreach (array_keys($allAddons) as $addonName)
                <tr>
                    <td class="row-header">{{ $addonName }}</td>
                    @foreach ($quotation->quotationCompanies as $company)
                        @php
                            $breakdown = is_string($company->addon_covers_breakdown)
                                ? json_decode($company->addon_covers_breakdown, true)
                                : $company->addon_covers_breakdown;
                            $price = isset($breakdown[$addonName]['price']) ? $breakdown[$addonName]['price'] : 0;
                        @endphp
                        <td class="currency">₹{{ number_format($price, 2) }}</td>
                    @endforeach
                </tr>
            @endforeach

            <tr>
                <td class="row-header">Total Add on Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->total_addon_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>

            <!-- Net Premium -->
            <tr>
                <td class="row-header total-row">Net Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency total-row">₹{{ number_format($company->net_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>

            <!-- GST Section -->
            <tr>
                <td class="section-header" colspan="{{ count($quotation->quotationCompanies) + 1 }}">GST & Final
                    Premium</td>
            </tr>
            <tr>
                <td class="row-header">SGST</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->sgst_amount ?? 0, 2) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">CGST</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->cgst_amount ?? 0, 2) }}</td>
                @endforeach
            </tr>
            <tr>
                <td class="row-header">Total Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="currency">₹{{ number_format($company->total_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>

            <!-- Final Premium -->
            <tr>
                <td class="final-total">Final Premium</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td class="final-total">₹{{ number_format($company->final_premium ?? 0, 2) }}</td>
                @endforeach
            </tr>

            <!-- Ranking -->
            <tr>
                <td class="ranking">RANKING</td>
                @foreach ($quotation->quotationCompanies as $company)
                    @php
                        $rank = $company->ranking ?? 1;
                        $rankClass = match ($rank) {
                            1 => 'rank-1',
                            2 => 'rank-2',
                            3 => 'rank-3',
                            default => 'ranking',
                        };
                    @endphp
                    <td class="ranking {{ $rankClass }}">{{ $rank }}</td>
                @endforeach
            </tr>

            <!-- Recommendation -->
            <tr>
                <td class="row-header">Recommended</td>
                @foreach ($quotation->quotationCompanies as $company)
                    <td
                        style="text-align: center; {{ $company->is_recommended ? 'background: #27ae60; color: white; font-weight: bold;' : '' }}">
                        {{ $company->is_recommended ? 'YES' : 'NO' }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This quotation comparison is generated automatically. Please verify all details before making any decisions.
        </p>
        <p>Generated by {{ config('app.name') }} - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
