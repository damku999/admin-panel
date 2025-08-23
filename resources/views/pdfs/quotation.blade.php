<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Insurance Quotation - {{ $quotation->getQuoteReference() }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2b9eb3;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 40%;
            vertical-align: top;
        }

        .company-cell {
            width: 60%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .advisor-name {
            font-size: 16px;
            font-weight: bold;
            color: #666;
            margin-bottom: 8px;
        }

        .contact-info {
            font-size: 10px;
            color: #555;
            line-height: 1.3;
        }

        /* Document Title */
        .document-title {
            background-color: #2b9eb3;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }

        /* Simple Table Styles */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th {
            background-color: #f5f5f5;
            padding: 8px 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ccc;
            font-size: 11px;
        }

        .info-table td {
            padding: 8px 12px;
            border: 1px solid #ccc;
            font-size: 11px;
        }

        .section-header {
            background-color: #2b9eb3;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            margin: 25px 0 10px 0;
        }

        /* Premium Comparison */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .premium-table th {
            background-color: #2b9eb3;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #2b9eb3;
            font-size: 10px;
        }

        .premium-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ccc;
            font-size: 10px;
        }

        .company-cell-data {
            text-align: left;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .recommended-company {
            background-color: #fff3cd;
        }

        .best-price-company {
            background-color: #d1ecf1;
        }

        .premium-amount {
            font-weight: bold;
        }

        /* Add-on Lists */
        .addon-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .addon-list th {
            background-color: #f5f5f5;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ccc;
            font-size: 11px;
        }

        .addon-list td {
            padding: 6px 8px;
            border: 1px solid #ccc;
            font-size: 10px;
            vertical-align: top;
        }

        /* Summary Box */
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            color: #000;
        }

        /* Terms and Conditions */
        .terms-section {
            background-color: #f8f9fa;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 20px 0;
        }

        .terms-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .terms-content {
            font-size: 9px;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #2b9eb3;
            text-align: center;
        }

        .footer-content {
            background-color: #2b9eb3;
            color: white;
            padding: 15px;
            font-size: 11px;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>

<body>
    <!-- Simple Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <!-- Parth Logo -->
                    <img src="{{ public_path('images/parth_logo.png') }}" alt="Parth Rawal - Insurance Advisor" style="width: 200px; height: auto; max-height: 60px;" />
                </td>
                <td class="company-cell">
                    <div class="company-name" style="color: #2b9eb3;">MIDAS INSURANCE SERVICES</div>
                    <div class="advisor-name" style="color: #2b9eb3;">Professional Insurance Solutions</div>
                    <div class="contact-info">
                        Your Trusted Insurance Partner<br>
                        Contact: +91-8000071314<br>
                        Email: info@midastech.in<br>
                        Generated: {{ $generatedDate }} {{ $generatedTime }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        MOTOR INSURANCE QUOTATION COMPARISON
    </div>

    <!-- Quotation Information -->
    <div class="section-header">QUOTATION DETAILS</div>
    <table class="info-table">
        <tr>
            <th style="width: 25%;">Quote Reference</th>
            <td style="width: 25%;">{{ $quotation->getQuoteReference() }}</td>
            <th style="width: 25%;">Customer Name</th>
            <td style="width: 25%;">{{ $customer->name }}</td>
        </tr>
        <tr>
            <th>Mobile Number</th>
            <td>{{ $customer->mobile_number }}</td>
            <th>WhatsApp Number</th>
            <td>{{ $quotation->whatsapp_number ?? $customer->mobile_number }}</td>
        </tr>
    </table>

    <!-- Vehicle Information -->
    <div class="section-header">VEHICLE DETAILS</div>
    <table class="info-table">
        <tr>
            <th style="width: 25%;">Make, Model & Variant</th>
            <td style="width: 25%;">{{ $quotation->make_model_variant }}</td>
            <th style="width: 25%;">Vehicle Number</th>
            <td style="width: 25%;">{{ $quotation->vehicle_number ?? 'To be registered' }}</td>
        </tr>
        <tr>
            <th>RTO Location</th>
            <td>{{ $quotation->rto_location }}</td>
            <th>Manufacturing Year</th>
            <td>{{ $quotation->manufacturing_year }}</td>
        </tr>
        <tr>
            <th>Registration Date</th>
            <td>{{ \Carbon\Carbon::parse($quotation->date_of_registration)->format('d/m/Y') }}</td>
            <th>Fuel Type</th>
            <td>{{ $quotation->fuel_type }}</td>
        </tr>
        <tr>
            <th>Engine Capacity</th>
            <td>{{ number_format($quotation->cubic_capacity_kw) }} CC/KW</td>
            <th>Seating Capacity</th>
            <td>{{ $quotation->seating_capacity }} seats</td>
        </tr>
        <tr>
            <th>Policy Type</th>
            <td>{{ $quotation->policy_type }}</td>
            <th>Policy Tenure</th>
            <td>{{ $quotation->policy_tenure_years }} Year(s)</td>
        </tr>
    </table>

    <!-- IDV Breakdown -->
    <div class="section-header">INSURED DECLARED VALUE (IDV) BREAKDOWN</div>
    <table class="info-table">
        <tr>
            <th style="width: 50%;">Component</th>
            <th style="width: 50%; text-align: right;">Amount (Rs.)</th>
        </tr>
        <tr>
            <td>Vehicle IDV</td>
            <td class="text-right">{{ number_format($quotation->idv_vehicle ?? 0) }}</td>
        </tr>
        @if ($quotation->idv_trailer > 0)
            <tr>
                <td>Trailer IDV</td>
                <td class="text-right">{{ number_format($quotation->idv_trailer) }}</td>
            </tr>
        @endif
        @if ($quotation->idv_cng_lpg_kit > 0)
            <tr>
                <td>CNG/LPG Kit IDV</td>
                <td class="text-right">{{ number_format($quotation->idv_cng_lpg_kit) }}</td>
            </tr>
        @endif
        @if ($quotation->idv_electrical_accessories > 0)
            <tr>
                <td>Electrical Accessories</td>
                <td class="text-right">{{ number_format($quotation->idv_electrical_accessories) }}</td>
            </tr>
        @endif
        @if ($quotation->idv_non_electrical_accessories > 0)
            <tr>
                <td>Non-Electrical Accessories</td>
                <td class="text-right">{{ number_format($quotation->idv_non_electrical_accessories) }}</td>
            </tr>
        @endif
        <tr style="background-color: #2b9eb3; color: white;">
            <td class="text-bold">TOTAL IDV</td>
            <td class="text-right text-bold">Rs.{{ number_format($quotation->total_idv) }}</td>
        </tr>
    </table>

    <!-- Premium Comparison Table -->
    <div class="section-header">PREMIUM COMPARISON - {{ $companies->count() }} INSURANCE COMPANIES</div>
    <table class="premium-table">
        <thead>
            <tr>
                <th style="width: 25%;">Insurance Company</th>
                <th style="width: 12%;">Basic OD</th>
                <th style="width: 12%;">Add-on</th>
                <th style="width: 10%;">CNG/LPG</th>
                <th style="width: 12%;">Net Premium</th>
                <th style="width: 10%;">GST (18%)</th>
                <th style="width: 13%;">Total Premium</th>
                <th style="width: 6%;">Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies->sortBy('final_premium') as $index => $company)
                <tr
                    class="{{ $company->is_recommended ? 'recommended-company' : '' }} {{ $loop->first ? 'best-price-company' : '' }}">
                    <td class="company-cell-data">
                        <strong>{{ $company->insuranceCompany->name }}</strong>
                        @if ($company->plan_name)
                            <br><small style="font-size: 8px;">{{ $company->plan_name }}</small>
                        @endif
                        @if ($company->quote_number)
                            <br><small style="font-size: 8px;">Quote: {{ $company->quote_number }}</small>
                        @endif
                        @if ($company->is_recommended)
                            <br><small style="font-size: 8px;">RECOMMENDED</small>
                        @endif
                        @if ($loop->first)
                            <br><small style="font-size: 8px;">BEST PRICE</small>
                        @endif
                    </td>
                    <td>{{ number_format($company->basic_od_premium, 0) }}</td>
                    <td>{{ number_format($company->total_addon_premium, 0) }}</td>
                    <td>{{ number_format($company->cng_lpg_premium ?? 0, 0) }}</td>
                    <td>{{ number_format($company->net_premium, 0) }}</td>
                    <td>{{ number_format($company->sgst_amount + $company->cgst_amount, 0) }}</td>
                    <td class="premium-amount">{{ number_format($company->final_premium, 0) }}</td>
                    <td class="text-bold">{{ $index + 1 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Add-on Coverage Details -->
    @if ($quotation->addon_covers && count($quotation->addon_covers) > 0)
        <div class="section-header">SELECTED ADD-ON COVERS</div>
        <table class="addon-list">
            <tr>
                <th colspan="3">Selected Coverage Options</th>
            </tr>
            @php
                $chunkedAddons = array_chunk($quotation->addon_covers, 3);
            @endphp
            @foreach ($chunkedAddons as $addonChunk)
                <tr>
                    @foreach ($addonChunk as $addon)
                        <td>{{ $addon }}</td>
                    @endforeach
                    @if (count($addonChunk) < 3)
                        @for ($i = count($addonChunk); $i < 3; $i++)
                            <td>&nbsp;</td>
                        @endfor
                    @endif
                </tr>
            @endforeach
        </table>
    @endif

    <!-- Detailed Add-on Breakdown -->
    @if ($companies->where('total_addon_premium', '>', 0)->count() > 0)
        <div class="section-header">DETAILED ADD-ON PREMIUM BREAKDOWN</div>
        @foreach ($companies->where('total_addon_premium', '>', 0) as $company)
            <div style="margin-bottom: 20px;">
                <div
                    style="background-color: #e8f7f9; padding: 8px; font-weight: bold; font-size: 11px; border: 1px solid #2b9eb3; color: #2b9eb3;">
                    {{ $company->insuranceCompany->name }} - Total Add-on Premium:
                    Rs.{{ number_format($company->total_addon_premium) }}
                </div>
                @if ($company->addon_covers_breakdown)
                    <table class="addon-list">
                        <tr>
                            <th style="width: 33%;">Add-on Cover</th>
                            <th style="width: 33%;">Add-on Cover</th>
                            <th style="width: 34%;">Add-on Cover</th>
                        </tr>
                        @php
                            $addonDetails = [];
                            foreach ($company->addon_covers_breakdown as $addon => $data) {
                                if (is_array($data) && isset($data['price']) && $data['price'] > 0) {
                                    $note = !empty($data['note']) ? ' (' . $data['note'] . ')' : '';
                                    $addonDetails[] = $addon . ': ' . number_format($data['price']) . $note;
                                } elseif (is_numeric($data) && $data > 0) {
                                    $addonDetails[] = $addon . ': ' . number_format($data);
                                }
                            }
                            $chunkedDetails = array_chunk($addonDetails, 3);
                        @endphp
                        @foreach ($chunkedDetails as $detailChunk)
                            <tr>
                                @foreach ($detailChunk as $detail)
                                    <td style="font-size: 9px;">{{ $detail }}</td>
                                @endforeach
                                @if (count($detailChunk) < 3)
                                    @for ($i = count($detailChunk); $i < 3; $i++)
                                        <td>&nbsp;</td>
                                    @endfor
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        @endforeach
    @endif

    <!-- Summary Highlights -->
    <div class="summary-box">
        <div class="summary-title">QUOTATION SUMMARY</div>
        <table class="info-table">
            @if ($bestQuote)
                <tr>
                    <th style="width: 40%;">Lowest Premium Option</th>
                    <td>{{ $bestQuote->insuranceCompany->name }} - {{ number_format($bestQuote->final_premium) }}</td>
                </tr>
            @endif
            @if ($recommendedQuote)
                <tr>
                    <th>Recommended Option</th>
                    <td>{{ $recommendedQuote->insuranceCompany->name }} -
                        {{ number_format($recommendedQuote->final_premium) }} (RECOMMENDED)</td>
                </tr>
            @endif
            <tr>
                <th>Total Companies Compared</th>
                <td>{{ $companies->count() }}</td>
            </tr>
            <tr>
                <th>Quote Validity</th>
                <td>15 days from generation date</td>
            </tr>
            <tr>
                <th>IDV (Insured Declared Value)</th>
                <td>{{ number_format($quotation->total_idv) }}</td>
            </tr>
        </table>
    </div>

    @if ($quotation->notes)
        <!-- Additional Notes -->
        <div class="summary-box">
            <div class="summary-title">SPECIAL NOTES</div>
            <div style="font-size: 10px; line-height: 1.4; padding: 5px 0;">
                {{ $quotation->notes }}
            </div>
        </div>
    @endif

    <!-- Important Terms and Conditions -->
    <div class="terms-section">
        <div class="terms-title">IMPORTANT TERMS & CONDITIONS</div>
        <div class="terms-content">
            <strong>VALIDITY:</strong> This quotation is valid for 15 days from the date of generation.<br><br>
            <strong>INSPECTION:</strong> Premium mentioned is subject to positive vehicle inspection, if required as per
            Underwriting Guidelines.<br><br>
            <strong>SUBJECT MATTER:</strong> Insurance is subject matter of solicitation. For complete details on
            benefits/exclusions, risk factors, terms and conditions, please refer to policy wordings carefully before
            concluding the sale.<br><br>
            <strong>PREMIUM VARIATION:</strong> Premium and risk period may change at the time of policy issuance based
            on final underwriting assessment.<br><br>
            <strong>DOCUMENTATION:</strong> All required documents must be submitted before policy issuance.<br><br>
            <strong>PAYMENT:</strong> Premium payment must be made through authorized channels only.
        </div>
    </div>

    <!-- Professional Footer -->
    <div class="footer">
        <div class="footer-content">
            <strong>MIDAS INSURANCE SERVICES</strong><br>
            <strong>PARTH RAWAL - INSURANCE ADVISOR</strong><br>
            Your Trusted Insurance Partner | Professional Solutions for All Your Insurance Needs<br>
            Phone: +91-8000071314 | Email: info@midastech.in<br>
            <small style="font-size: 8px;">This document is system generated and does not require signature</small>
        </div>
    </div>
</body>

</html>
