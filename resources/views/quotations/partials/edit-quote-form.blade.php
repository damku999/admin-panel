<div class="card border-left-info mb-3 quote-entry existing-quote"
    data-index="{{ $quoteIndex }}" data-company-id="{{ $company->id }}">
    <div
        class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="m-0">
            <i class="fas fa-quote-left"></i> Quote #{{ $quoteIndex + 1 }}
            <span class="badge badge-info ml-2">Existing</span>
            @if($company->is_recommended)
                <span class="badge badge-success ml-1"><i class="fas fa-star"></i> Recommended</span>
            @endif
        </h6>
        <button type="button" class="btn btn-sm btn-danger removeQuoteBtn">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="card-body">
        <!-- Hidden field to track existing company -->
        <input type="hidden" name="companies[{{ $quoteIndex }}][id]" value="{{ $company->id }}">

        <!-- Premium Details Section -->
        <div class="card border-left-primary mb-3">
            <div class="card-header bg-primary text-white py-1">
                <h6 class="m-0 small"><i class="fas fa-money-bill-wave"></i> Premium Details</h6>
            </div>
            <div class="card-body p-3">
                <!-- Row 1: Quote Number, Basic OD Premium, TP Premium -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Quote Number</label>
                            <input type="text" name="companies[{{ $quoteIndex }}][quote_number]" 
                                class="form-control form-control-sm @error("companies.{$quoteIndex}.quote_number") is-invalid @enderror" 
                                placeholder="Company quote reference number" 
                                value="{{ old("companies.{$quoteIndex}.quote_number") ?? $company->quote_number }}">
                            @error("companies.{$quoteIndex}.quote_number")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Basic OD Premium (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="companies[{{ $quoteIndex }}][basic_od_premium]" 
                                class="form-control form-control-sm premium-field @error("companies.{$quoteIndex}.basic_od_premium") is-invalid @enderror" 
                                step="1" required 
                                value="{{ old("companies.{$quoteIndex}.basic_od_premium") ?? $company->basic_od_premium }}">
                            @error("companies.{$quoteIndex}.basic_od_premium")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">TP Premium (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="companies[{{ $quoteIndex }}][tp_premium]" 
                                class="form-control form-control-sm premium-field @error("companies.{$quoteIndex}.tp_premium") is-invalid @enderror" 
                                step="1" required 
                                value="{{ old("companies.{$quoteIndex}.tp_premium") ?? $company->tp_premium ?? 0 }}">
                            @error("companies.{$quoteIndex}.tp_premium")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- Row 2: CNG/LPG Premium -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">CNG/LPG Premium (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][cng_lpg_premium]" 
                                class="form-control form-control-sm premium-field @error("companies.{$quoteIndex}.cng_lpg_premium") is-invalid @enderror" 
                                step="1" 
                                value="{{ old("companies.{$quoteIndex}.cng_lpg_premium") ?? $company->cng_lpg_premium ?? 0 }}" 
                                placeholder="0">
                            @error("companies.{$quoteIndex}.cng_lpg_premium")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coverage Details Section -->
        <div class="card border-left-success mb-3">
            <div class="card-header bg-success text-white py-1">
                <h6 class="m-0 small"><i class="fas fa-shield-alt"></i> Coverage Details</h6>
            </div>
            <div class="card-body p-3">
                <!-- Row 1: Insurance Company, Policy Type, Policy Tenure -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Insurance Company <span class="text-danger">*</span></label>
                            <select name="companies[{{ $quoteIndex }}][insurance_company_id]" 
                                class="form-control form-control-sm company-select @error("companies.{$quoteIndex}.insurance_company_id") is-invalid @enderror" 
                                required>
                                <option value="">Select Company</option>
                                @foreach ($insuranceCompanies as $insuranceCompany)
                                    <option value="{{ $insuranceCompany->id }}"
                                        {{ (old("companies.{$quoteIndex}.insurance_company_id") ?? $company->insurance_company_id) == $insuranceCompany->id ? 'selected' : '' }}>
                                        {{ $insuranceCompany->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error("companies.{$quoteIndex}.insurance_company_id")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Policy Type <span class="text-danger">*</span></label>
                            <select name="companies[{{ $quoteIndex }}][policy_type]" 
                                class="form-control form-control-sm @error("companies.{$quoteIndex}.policy_type") is-invalid @enderror" 
                                required>
                                <option value="">Select Policy Type</option>
                                @php
                                    $policyType = old("companies.{$quoteIndex}.policy_type") ?? $company->policy_type ?? $company->quotation->policy_type ?? 'Comprehensive';
                                @endphp
                                <option value="Comprehensive" {{ $policyType == 'Comprehensive' ? 'selected' : '' }}>Comprehensive</option>
                                <option value="Own Damage" {{ $policyType == 'Own Damage' ? 'selected' : '' }}>Own Damage</option>
                                <option value="Third Party" {{ $policyType == 'Third Party' ? 'selected' : '' }}>Third Party</option>
                            </select>
                            @error("companies.{$quoteIndex}.policy_type")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Policy Tenure <span class="text-danger">*</span></label>
                            <select name="companies[{{ $quoteIndex }}][policy_tenure_years]" 
                                class="form-control form-control-sm @error("companies.{$quoteIndex}.policy_tenure_years") is-invalid @enderror" 
                                required>
                                <option value="">Select Tenure</option>
                                @php
                                    $policyTenure = old("companies.{$quoteIndex}.policy_tenure_years") ?? $company->policy_tenure_years ?? $company->quotation->policy_tenure_years ?? '1';
                                @endphp
                                <option value="1" {{ $policyTenure == '1' ? 'selected' : '' }}>1 Year</option>
                                <option value="2" {{ $policyTenure == '2' ? 'selected' : '' }}>2 Years</option>
                                <option value="3" {{ $policyTenure == '3' ? 'selected' : '' }}>3 Years</option>
                            </select>
                            @error("companies.{$quoteIndex}.policy_tenure_years")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Row 2: IDV Vehicle, IDV Trailer, IDV CNG/LPG Kit -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV Vehicle (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="companies[{{ $quoteIndex }}][idv_vehicle]" 
                                class="form-control form-control-sm idv-field @error("companies.{$quoteIndex}.idv_vehicle") is-invalid @enderror" 
                                step="1" required placeholder="e.g., 500000" 
                                value="{{ old("companies.{$quoteIndex}.idv_vehicle") ?? $company->idv_vehicle ?? $company->quotation->idv_vehicle ?? 0 }}">
                            @error("companies.{$quoteIndex}.idv_vehicle")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV Trailer (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][idv_trailer]" 
                                class="form-control form-control-sm idv-field @error("companies.{$quoteIndex}.idv_trailer") is-invalid @enderror" 
                                step="1" placeholder="0" 
                                value="{{ old("companies.{$quoteIndex}.idv_trailer") ?? $company->idv_trailer ?? $company->quotation->idv_trailer ?? 0 }}">
                            @error("companies.{$quoteIndex}.idv_trailer")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV CNG/LPG Kit (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][idv_cng_lpg_kit]" 
                                class="form-control form-control-sm idv-field @error("companies.{$quoteIndex}.idv_cng_lpg_kit") is-invalid @enderror" 
                                step="1" placeholder="0" 
                                value="{{ old("companies.{$quoteIndex}.idv_cng_lpg_kit") ?? $company->idv_cng_lpg_kit ?? $company->quotation->idv_cng_lpg_kit ?? 0 }}">
                            @error("companies.{$quoteIndex}.idv_cng_lpg_kit")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Row 3: IDV Electrical Acc., IDV Non-Elec. Acc., Total IDV -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">IDV Electrical Acc. (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][idv_electrical_accessories]" 
                                class="form-control form-control-sm idv-field @error("companies.{$quoteIndex}.idv_electrical_accessories") is-invalid @enderror" 
                                step="1" placeholder="0" 
                                value="{{ old("companies.{$quoteIndex}.idv_electrical_accessories") ?? $company->idv_electrical_accessories ?? $company->quotation->idv_electrical_accessories ?? 0 }}">
                            @error("companies.{$quoteIndex}.idv_electrical_accessories")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">IDV Non-Elec. Acc. (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][idv_non_electrical_accessories]" 
                                class="form-control form-control-sm idv-field @error("companies.{$quoteIndex}.idv_non_electrical_accessories") is-invalid @enderror" 
                                step="1" placeholder="0" 
                                value="{{ old("companies.{$quoteIndex}.idv_non_electrical_accessories") ?? $company->idv_non_electrical_accessories ?? $company->quotation->idv_non_electrical_accessories ?? 0 }}">
                            @error("companies.{$quoteIndex}.idv_non_electrical_accessories")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-success">Total IDV (₹)</label>
                            <input type="number" name="companies[{{ $quoteIndex }}][total_idv]" 
                                class="form-control form-control-sm total-idv font-weight-bold text-success @error("companies.{$quoteIndex}.total_idv") is-invalid @enderror" 
                                step="1" readonly style="background: #d1ecf1; border-color: #28a745;" 
                                value="{{ old("companies.{$quoteIndex}.total_idv") ?? $company->total_idv ?? $company->quotation->total_idv ?? 0 }}">
                            @error("companies.{$quoteIndex}.total_idv")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add-on Covers Breakdown Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-left-success mb-3">
                    <div class="card-header bg-success text-white py-1">
                        <h6 class="m-0 small"><i class="fas fa-plus-circle"></i> Add-on Covers Breakdown</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            @if(isset($addonCovers) && $addonCovers->count() > 0)
                                @php
                                    $columns = $addonCovers->chunk(ceil($addonCovers->count() / 3));
                                    $addonBreakdown = $company->addon_covers_breakdown ?? [];
                                @endphp
                                @foreach ($columns as $columnCovers)
                                    <div class="col-md-4">
                                        @foreach ($columnCovers as $addonCover)
                                            @php
                                                $slug = \Str::slug($addonCover->name, '_');
                                                // Check if this addon exists in the breakdown (means it was selected)
                                                $addonExists = isset($addonBreakdown[$addonCover->name]);
                                                $addonData = $addonBreakdown[$addonCover->name] ?? ['price' => 0, 'note' => ''];
                                                $price = is_array($addonData) ? ($addonData['price'] ?? 0) : $addonData;
                                                $note = is_array($addonData) ? ($addonData['note'] ?? '') : '';
                                                // Selected if it exists in breakdown OR has price OR has note
                                                $isSelected = $addonExists || ($price > 0) || !empty($note);
                                            @endphp
                                            <div class="form-group mb-2 addon-field-container" data-addon="{{ $slug }}">
                                                <div class="form-check">
                                                    <input class="form-check-input addon-checkbox" type="checkbox"
                                                        id="addon_{{ $slug }}_{{ $quoteIndex }}"
                                                        data-slug="{{ $slug }}"
                                                        {{ $isSelected ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="addon_{{ $slug }}_{{ $quoteIndex }}">
                                                        <strong>{{ $addonCover->name }}</strong>
                                                        @if ($addonCover->description)
                                                            <br><small class="text-muted">{{ $addonCover->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                                <!-- Hidden input to track addon selection when checked -->
                                                <input type="hidden" name="companies[{{ $quoteIndex }}][addon_{{ $slug }}_selected]" class="addon-selected-flag" value="{{ $isSelected ? '1' : '0' }}">
                                                <div class="addon-fields" id="fields_{{ $slug }}_{{ $quoteIndex }}"
                                                    style="{{ $isSelected ? 'display: block;' : 'display: none;' }}">
                                                    <label class="small">{{ $addonCover->name }} (₹) <small class="text-muted">(Optional)</small></label>
                                                    <input type="number" name="companies[{{ $quoteIndex }}][addon_{{ $slug }}]"
                                                        class="form-control form-control-sm addon-field @error("companies.{$quoteIndex}.addon_{$slug}") is-invalid @enderror"
                                                        step="1" value="{{ $price }}" placeholder="Enter premium (optional)">
                                                    @error("companies.{$quoteIndex}.addon_{$slug}")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <input type="text" name="companies[{{ $quoteIndex }}][addon_{{ $slug }}_note]"
                                                        class="form-control form-control-sm mt-1 addon-note @error("companies.{$quoteIndex}.addon_{$slug}_note") is-invalid @enderror"
                                                        maxlength="100" placeholder="Add note (coverage details, limits etc.)"
                                                        value="{{ $note }}">
                                                    @error("companies.{$quoteIndex}.addon_{$slug}_note")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">No addon covers available. Please add addon covers from the admin panel.</p>
                                </div>
                            @endif
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-success">Total Add-on Premium (₹)</label>
                                    <input type="number" name="companies[{{ $quoteIndex }}][total_addon_premium]"
                                        class="form-control form-control-sm total-addon-premium font-weight-bold @error("companies.{$quoteIndex}.total_addon_premium") is-invalid @enderror"
                                        step="1" readonly style="background: #d1ecf1;"
                                        value="{{ old("companies.{$quoteIndex}.total_addon_premium") ?? $company->total_addon_premium ?? 0 }}" 
                                        placeholder="0">
                                    @error("companies.{$quoteIndex}.total_addon_premium")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Calculations -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Net Premium (₹)</label>
                    <input type="number" name="companies[{{ $quoteIndex }}][net_premium]"
                        class="form-control net-premium" step="1" readonly
                        value="{{ old("companies.{$quoteIndex}.net_premium") ?? $company->net_premium ?? 0 }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>GST Amount (₹)</label>
                    <input type="number" name="companies[{{ $quoteIndex }}][gst_amount]"
                        class="form-control gst-amount" step="1" readonly
                        value="{{ old("companies.{$quoteIndex}.gst_amount") ?? ($company->sgst_amount + $company->cgst_amount) ?? 0 }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Final Premium (₹)</strong></label>
                    <input type="number" name="companies[{{ $quoteIndex }}][final_premium]"
                        class="form-control final-premium font-weight-bold" step="1" readonly
                        style="background: #d4edda;"
                        value="{{ old("companies.{$quoteIndex}.final_premium") ?? $company->final_premium ?? 0 }}">
                </div>
            </div>
        </div>

        <!-- Recommendation Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input type="checkbox" name="companies[{{ $quoteIndex }}][is_recommended]" value="1" 
                        class="form-check-input recommendation-checkbox" data-index="{{ $quoteIndex }}"
                        {{ (old("companies.{$quoteIndex}.is_recommended") ?? $company->is_recommended) ? 'checked' : '' }}>
                    <label class="form-check-label font-weight-bold text-success">
                        <i class="fas fa-star"></i> Mark as Recommended
                    </label>
                </div>
                
                <!-- Recommendation Note Field -->
                <div class="recommendation-note-field mt-3" id="recommendation_note_field_{{ $quoteIndex }}" 
                    style="display: {{ (old("companies.{$quoteIndex}.is_recommended") ?? $company->is_recommended) ? 'block' : 'none' }};">
                    <label class="small font-weight-bold text-success">
                        <i class="fas fa-edit"></i> Recommendation Note <span class="text-danger">*</span>
                    </label>
                    <textarea name="companies[{{ $quoteIndex }}][recommendation_note]" 
                              class="form-control form-control-sm @error("companies.{$quoteIndex}.recommendation_note") is-invalid @enderror" 
                              rows="2" maxlength="500"
                              placeholder="Explain why this quote is recommended (e.g., best price, good coverage, trusted insurer...)"
                              >{{ old("companies.{$quoteIndex}.recommendation_note") ?? $company->recommendation_note ?? '' }}</textarea>
                    @error("companies.{$quoteIndex}.recommendation_note")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Explain why you recommend this quote (Max 500 characters)</small>
                </div>
            </div>
        </div>

        <!-- Hidden fields for backend processing -->
        <input type="hidden" name="companies[{{ $quoteIndex }}][sgst_amount]" 
            value="{{ old("companies.{$quoteIndex}.sgst_amount") ?? $company->sgst_amount ?? 0 }}">
        <input type="hidden" name="companies[{{ $quoteIndex }}][cgst_amount]" 
            value="{{ old("companies.{$quoteIndex}.cgst_amount") ?? $company->cgst_amount ?? 0 }}">
        <input type="hidden" name="companies[{{ $quoteIndex }}][total_od_premium]" 
            value="{{ old("companies.{$quoteIndex}.total_od_premium") ?? $company->total_od_premium ?? 0 }}">
        <input type="hidden" name="companies[{{ $quoteIndex }}][total_premium]" 
            value="{{ old("companies.{$quoteIndex}.total_premium") ?? $company->total_premium ?? 0 }}">
    </div>
</div>