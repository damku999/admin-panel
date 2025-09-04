<div class="card border-left-info mb-3 quote-entry" data-index="{{ $currentIndex }}">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="m-0"><i class="fas fa-quote-left"></i> Quote #{{ $currentIndex + 1 }}</h6>
        <button type="button" class="btn btn-sm btn-danger removeQuoteBtn">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="card-body">
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
                            <input type="text" name="companies[{{ $currentIndex }}][quote_number]" class="form-control form-control-sm @error("companies.{$currentIndex}.quote_number") is-invalid @enderror" placeholder="Company quote reference number">
                            @error("companies.{$currentIndex}.quote_number")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Basic OD Premium (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="companies[{{ $currentIndex }}][basic_od_premium]" class="form-control form-control-sm premium-field @error("companies.{$currentIndex}.basic_od_premium") is-invalid @enderror" step="1" required>
                            @error("companies.{$currentIndex}.basic_od_premium")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">TP Premium (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="companies[{{ $currentIndex }}][tp_premium]" class="form-control form-control-sm premium-field @error("companies.{$currentIndex}.tp_premium") is-invalid @enderror" step="1" required>
                            @error("companies.{$currentIndex}.tp_premium")
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
                            <input type="number" name="companies[{{ $currentIndex }}][cng_lpg_premium]" class="form-control form-control-sm premium-field @error("companies.{$currentIndex}.cng_lpg_premium") is-invalid @enderror" step="1" placeholder="0">
                            @error("companies.{$currentIndex}.cng_lpg_premium")
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
                            <select name="companies[{{ $currentIndex }}][insurance_company_id]" class="form-control form-control-sm company-select @error("companies.{$currentIndex}.insurance_company_id") is-invalid @enderror" required>
                                <option value="">Select Company</option>
                                @foreach ($insuranceCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error("companies.{$currentIndex}.insurance_company_id")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Policy Type <span class="text-danger">*</span></label>
                            <select name="companies[{{ $currentIndex }}][policy_type]" class="form-control form-control-sm @error("companies.{$currentIndex}.policy_type") is-invalid @enderror" required>
                                <option value="">Select Policy Type</option>
                                <option value="Comprehensive">Comprehensive</option>
                                <option value="Own Damage">Own Damage</option>
                                <option value="Third Party">Third Party</option>
                            </select>
                            @error("companies.{$currentIndex}.policy_type")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Policy Tenure <span class="text-danger">*</span></label>
                            <select name="companies[{{ $currentIndex }}][policy_tenure_years]" class="form-control form-control-sm @error("companies.{$currentIndex}.policy_tenure_years") is-invalid @enderror" required>
                                <option value="">Select Tenure</option>
                                <option value="1">1 Year</option>
                                <option value="2">2 Years</option>
                                <option value="3">3 Years</option>
                            </select>
                            @error("companies.{$currentIndex}.policy_tenure_years")
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
                            <input type="number" name="companies[{{ $currentIndex }}][idv_vehicle]" class="form-control form-control-sm idv-field @error("companies.{$currentIndex}.idv_vehicle") is-invalid @enderror" step="1" required>
                            @error("companies.{$currentIndex}.idv_vehicle")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV Trailer (₹)</label>
                            <input type="number" name="companies[{{ $currentIndex }}][idv_trailer]" class="form-control form-control-sm idv-field @error("companies.{$currentIndex}.idv_trailer") is-invalid @enderror" step="1" placeholder="0">
                            @error("companies.{$currentIndex}.idv_trailer")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV CNG/LPG Kit (₹)</label>
                            <input type="number" name="companies[{{ $currentIndex }}][idv_cng_lpg_kit]" class="form-control form-control-sm idv-field @error("companies.{$currentIndex}.idv_cng_lpg_kit") is-invalid @enderror" step="1" placeholder="0">
                            @error("companies.{$currentIndex}.idv_cng_lpg_kit")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Row 3: IDV Electrical Accessories, IDV Non-Electrical Accessories, Total IDV -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV Electrical Accessories (₹)</label>
                            <input type="number" name="companies[{{ $currentIndex }}][idv_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$currentIndex}.idv_electrical_accessories") is-invalid @enderror" step="1" placeholder="0">
                            @error("companies.{$currentIndex}.idv_electrical_accessories")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">IDV Non-Electrical Accessories (₹)</label>
                            <input type="number" name="companies[{{ $currentIndex }}][idv_non_electrical_accessories]" class="form-control form-control-sm idv-field @error("companies.{$currentIndex}.idv_non_electrical_accessories") is-invalid @enderror" step="1" placeholder="0">
                            @error("companies.{$currentIndex}.idv_non_electrical_accessories")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Total IDV (₹)</label>
                            <input type="number" name="companies[{{ $currentIndex }}][total_idv]" class="form-control form-control-sm total-idv @error("companies.{$currentIndex}.total_idv") is-invalid @enderror" step="1" readonly style="background: #f8f9fa;">
                            @error("companies.{$currentIndex}.total_idv")
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
                            @php
                                $columns = $addonCovers->chunk(ceil($addonCovers->count() / 3));
                            @endphp
                            @foreach ($columns as $columnCovers)
                                <div class="col-md-4">
                                    @foreach ($columnCovers as $addonCover)
                                        @php
                                            $slug = \Str::slug($addonCover->name, '_');
                                        @endphp
                                        <div class="form-group mb-2 addon-field-container" data-addon="{{ $slug }}">
                                            <div class="form-check">
                                                <input class="form-check-input addon-checkbox" type="checkbox" id="addon_{{ $slug }}_{{ $currentIndex }}" data-slug="{{ $slug }}">
                                                <label class="form-check-label small" for="addon_{{ $slug }}_{{ $currentIndex }}">
                                                    <strong>{{ $addonCover->name }}</strong>
                                                    @if ($addonCover->description)
                                                        <br><small class="text-muted">{{ $addonCover->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="addon-fields" id="fields_{{ $slug }}_{{ $currentIndex }}" style="display: none;">
                                                <label class="small">{{ $addonCover->name }} (₹)</label>
                                                <input type="number" name="companies[{{ $currentIndex }}][addon_{{ $slug }}]" class="form-control form-control-sm addon-field @error("companies.{$currentIndex}.addon_{$slug}") is-invalid @enderror" step="1" placeholder="Enter premium">
                                                @error("companies.{$currentIndex}.addon_{$slug}")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <input type="text" name="companies[{{ $currentIndex }}][addon_{{ $slug }}_note]" class="form-control form-control-sm mt-1 addon-note @error("companies.{$currentIndex}.addon_{$slug}_note") is-invalid @enderror" maxlength="100" placeholder="Add note (coverage details, limits etc.)">
                                                @error("companies.{$currentIndex}.addon_{$slug}_note")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Coverage details, limits etc. (Max 100 chars)</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-success">Total Add-on Premium (₹)</label>
                                    <input type="number" name="companies[{{ $currentIndex }}][total_addon_premium]" class="form-control form-control-sm total-addon-premium font-weight-bold @error("companies.{$currentIndex}.total_addon_premium") is-invalid @enderror" step="1" readonly style="background: #d1ecf1;" placeholder="0">
                                    @error("companies.{$currentIndex}.total_addon_premium")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Net Premium (₹)</label>
                    <input type="number" name="companies[{{ $currentIndex }}][net_premium]" class="form-control net-premium @error("companies.{$currentIndex}.net_premium") is-invalid @enderror" step="1" readonly>
                    @error("companies.{$currentIndex}.net_premium")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>SGST Amount (₹)</label>
                    <input type="number" name="companies[{{ $currentIndex }}][sgst_amount]" class="form-control sgst-amount @error("companies.{$currentIndex}.sgst_amount") is-invalid @enderror" step="1" readonly>
                    @error("companies.{$currentIndex}.sgst_amount")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>CGST Amount (₹)</label>
                    <input type="number" name="companies[{{ $currentIndex }}][cgst_amount]" class="form-control cgst-amount @error("companies.{$currentIndex}.cgst_amount") is-invalid @enderror" step="1" readonly>
                    @error("companies.{$currentIndex}.cgst_amount")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Final Premium (₹)</strong></label>
                    <input type="number" name="companies[{{ $currentIndex }}][final_premium]" class="form-control final-premium font-weight-bold @error("companies.{$currentIndex}.final_premium") is-invalid @enderror" step="1" readonly style="background: #d4edda;">
                    @error("companies.{$currentIndex}.final_premium")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Mark as Recommended Section -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="companies[{{ $currentIndex }}][is_recommended]" value="1" 
                               class="custom-control-input recommendation-checkbox @error("companies.{$currentIndex}.is_recommended") is-invalid @enderror" 
                               id="is_recommended_{{ $currentIndex }}"
                               data-index="{{ $currentIndex }}"
                               {{ old("companies.{$currentIndex}.is_recommended") ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold text-success" for="is_recommended_{{ $currentIndex }}">
                            <i class="fas fa-star"></i> Mark as Recommended
                        </label>
                        @error("companies.{$currentIndex}.is_recommended")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-muted">Check this box to recommend this quote to the customer</small>
                    
                    <!-- Recommendation Note Field (initially hidden) -->
                    <div class="recommendation-note-field mt-3" id="recommendation_note_field_{{ $currentIndex }}" style="display: {{ old("companies.{$currentIndex}.is_recommended") ? 'block' : 'none' }};">
                        <label class="small font-weight-bold text-success">
                            <i class="fas fa-edit"></i> Recommendation Note <span class="text-danger">*</span>
                        </label>
                        <textarea name="companies[{{ $currentIndex }}][recommendation_note]" 
                                  class="form-control form-control-sm @error("companies.{$currentIndex}.recommendation_note") is-invalid @enderror" 
                                  rows="2" maxlength="500"
                                  placeholder="Explain why this quote is recommended (e.g., best price, good coverage, trusted insurer...)"
                                  >{{ old("companies.{$currentIndex}.recommendation_note") }}</textarea>
                        @error("companies.{$currentIndex}.recommendation_note")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Explain why you recommend this quote (Max 500 characters)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>