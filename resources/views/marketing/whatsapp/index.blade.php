@extends('layouts.app')

@section('title', 'Marketing WhatsApp')

@section('content')
    <div class="container-fluid">
        <!-- Main Header Card -->
        <div class="card shadow mt-3 mb-4">
            <x-list-header 
                    title="WhatsApp Marketing Management"
                    subtitle="Send marketing messages to customers"
            />
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle mr-3 text-success" style="font-size: 1.2rem;"></i>
                    <div class="flex-grow-1">
                        <strong>{{ session('success') }}</strong>
                        
                        @if (session('marketing_result'))
                            @php $result = session('marketing_result'); @endphp
                            <div class="mt-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border-right">
                                            <h5 class="mb-1 text-primary">{{ $result['total_customers'] }}</h5>
                                            <small class="text-muted">Total Recipients</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border-right">
                                            <h5 class="mb-1 text-success">{{ $result['success_count'] }}</h5>
                                            <small class="text-muted">Sent Successfully</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <h5 class="mb-1 text-danger">{{ $result['failed_count'] }}</h5>
                                        <small class="text-muted">Failed</small>
                                    </div>
                                </div>
                                
                                @if ($result['failed_count'] > 0 && count($result['failed_customers']) > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        <h6 class="text-danger mb-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Failed Recipients:
                                        </h6>
                                        <div class="small">
                                            @foreach (array_slice($result['failed_customers'], 0, 5) as $customer)
                                                <span class="badge badge-light mr-1 mb-1">{{ $customer }}</span>
                                            @endforeach
                                            @if (count($result['failed_customers']) > 5)
                                                <span class="text-muted">... and {{ count($result['failed_customers']) - 5 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-link text-success p-1" 
                        onclick="$(this).closest('.alert').fadeOut()" aria-label="Close" 
                        style="position: absolute; top: 8px; right: 8px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('error') }}
                <button type="button" class="btn btn-sm btn-link text-danger p-1" 
                        onclick="$(this).closest('.alert').fadeOut()" aria-label="Close" 
                        style="position: absolute; top: 8px; right: 8px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                {{ session('info') }}
                <button type="button" class="btn btn-sm btn-link text-info p-1" 
                        onclick="$(this).closest('.alert').fadeOut()" aria-label="Close" 
                        style="position: absolute; top: 8px; right: 8px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Marketing Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bullhorn mr-2"></i>Send Marketing Message
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('marketing.whatsapp.send') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Message Type Selection -->
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-envelope mr-1"></i>Message Type <span class="text-danger">*</span>
                                </label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="message_type_text" name="message_type" value="text" 
                                           class="custom-control-input" {{ old('message_type', 'text') === 'text' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="message_type_text">
                                        <i class="fas fa-comment mr-1"></i>Text Only
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="message_type_image" name="message_type" value="image" 
                                           class="custom-control-input" {{ old('message_type') === 'image' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="message_type_image">
                                        <i class="fas fa-paperclip mr-1"></i>Text with Attachment
                                    </label>
                                </div>
                                @error('message_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Message Text -->
                            <div class="form-group">
                                <label for="message_text" class="font-weight-bold">
                                    <i class="fas fa-pen mr-1"></i>Message Text <span class="text-danger">*</span>
                                </label>
                                <textarea name="message_text" id="message_text" 
                                          class="form-control @error('message_text') is-invalid @enderror" 
                                          rows="4" maxlength="1000" 
                                          placeholder="Enter your marketing message (max 1000 characters)...">{{ old('message_text') }}</textarea>
                                <small class="text-muted">
                                    <span id="char_count">0</span>/1000 characters
                                </small>
                                @error('message_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Image Upload (hidden by default) -->
                            <div class="form-group" id="image_upload_section" style="display: none;">
                                <label for="image" class="font-weight-bold">
                                    <i class="fas fa-upload mr-1"></i>Upload File <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="image" id="image" 
                                       class="form-control-file @error('image') is-invalid @enderror" 
                                       accept="image/*,.pdf">
                                <small class="text-muted">
                                    Supported formats: JPEG, PNG, JPG, GIF, PDF. Max size: 5MB
                                </small>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Recipient Selection -->
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-users mr-1"></i>Recipients <span class="text-danger">*</span>
                                </label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="recipients_all" name="recipients" value="all" 
                                           class="custom-control-input recipient-option" {{ old('recipients') === 'all' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="recipients_all">
                                        <i class="fas fa-globe mr-1"></i>Send to All Active Customers
                                        <small class="d-block text-muted">Send to all customers with valid mobile numbers</small>
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="recipients_selected" name="recipients" value="selected" 
                                           class="custom-control-input recipient-option" {{ old('recipients', 'selected') === 'selected' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="recipients_selected">
                                        <i class="fas fa-user-check mr-1"></i>Send to Selected Customers
                                        <small class="d-block text-muted">Choose specific customers to send to</small>
                                    </label>
                                </div>
                                @error('recipients')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Customer Selection (hidden by default) -->
                            <div class="form-group" id="customer_selection_section" style="display: none;">
                                <label class="font-weight-bold">
                                    <i class="fas fa-check-square mr-1"></i>Select Customers
                                    <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="select_all_customers">
                                        Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="deselect_all_customers">
                                        Deselect All
                                    </button>
                                </label>
                                <div class="row" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($customers as $customer)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input customer-checkbox" 
                                                       id="customer_{{ $customer->id }}" name="selected_customers[]" 
                                                       value="{{ $customer->id }}"
                                                       {{ in_array($customer->id, old('selected_customers', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customer_{{ $customer->id }}">
                                                    <strong>{{ $customer->name }}</strong>
                                                    <br><small class="text-muted">{{ $customer->mobile_number }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selected_customers')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Preview Section -->
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-sm" id="preview_recipients">
                                    <i class="fas fa-eye mr-1"></i>Preview Recipients
                                </button>
                                <div id="recipient_preview" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <strong>Preview:</strong> <span id="recipient_count">0</span> customer(s) will receive this message
                                        <div id="recipient_list" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fab fa-whatsapp mr-2"></i>Send Marketing Messages
                                </button>
                                <small class="d-block text-muted mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Please review your message and recipients before sending
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help & Guidelines -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-lightbulb mr-2"></i>Marketing Guidelines
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-primary">
                                <i class="fas fa-check-circle mr-1"></i>Best Practices
                            </h6>
                            <ul class="small">
                                <li>Keep messages concise and valuable</li>
                                <li>Include clear call-to-action</li>
                                <li>Personalize when possible</li>
                                <li>Respect customer preferences</li>
                                <li>Send at appropriate times</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Important Notes
                            </h6>
                            <ul class="small">
                                <li>Only active customers with mobile numbers will receive messages</li>
                                <li>Images should be under 5MB</li>
                                <li>Messages are limited to 1000 characters</li>
                                <li>All sends are logged for tracking</li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted">
                                <i class="fas fa-users mr-1"></i>
                                Total Active Customers: <strong>{{ $customers->count() }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Character count for message text
    $('#message_text').on('input', function() {
        const length = $(this).val().length;
        $('#char_count').text(length);
        
        if (length > 800) {
            $('#char_count').addClass('text-danger');
        } else {
            $('#char_count').removeClass('text-danger');
        }
    });

    // Show/hide image upload based on message type
    $('input[name="message_type"]').on('change', function() {
        if ($(this).val() === 'image') {
            $('#image_upload_section').show();
        } else {
            $('#image_upload_section').hide();
        }
    });

    // Show/hide customer selection based on recipients
    $('.recipient-option').on('change', function() {
        if ($(this).val() === 'selected') {
            $('#customer_selection_section').show();
        } else {
            $('#customer_selection_section').hide();
        }
    });

    // Select/Deselect all customers
    $('#select_all_customers').on('click', function() {
        $('.customer-checkbox').prop('checked', true);
    });

    $('#deselect_all_customers').on('click', function() {
        $('.customer-checkbox').prop('checked', false);
    });

    // Preview recipients
    $('#preview_recipients').on('click', function() {
        const recipients = $('input[name="recipients"]:checked').val();
        const selectedCustomers = $('.customer-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        const data = {
            recipients: recipients,
            selected_customers: selectedCustomers,
            _token: '{{ csrf_token() }}'
        };

        showLoading('Loading preview...');

        $.post('{{ route('marketing.whatsapp.preview') }}', data)
            .done(function(response) {
                if (response.status === 'success') {
                    $('#recipient_count').text(response.count);
                    
                    let listHtml = '';
                    if (response.customers.length <= 10) {
                        listHtml = response.customers.map(customer => 
                            `<small class="d-block">${customer.name} - ${customer.mobile_number}</small>`
                        ).join('');
                    } else {
                        listHtml = response.customers.slice(0, 10).map(customer => 
                            `<small class="d-block">${customer.name} - ${customer.mobile_number}</small>`
                        ).join('') + `<small class="d-block text-muted">... and ${response.count - 10} more</small>`;
                    }
                    
                    $('#recipient_list').html(listHtml);
                    $('#recipient_preview').show();
                }
            })
            .fail(function() {
                show_notification('error', 'Failed to load preview');
            })
            .always(function() {
                hideLoading();
            });
    });

    // Initialize displays based on default/old input
    // Show customer selection section on page load since "selected" is now default
    if ($('input[name="recipients"]:checked').val() === 'selected') {
        $('#customer_selection_section').show();
    }
    $('input[name="message_type"]:checked').trigger('change');
    $('.recipient-option:checked').trigger('change');
    $('#message_text').trigger('input');
});
</script>
@endsection