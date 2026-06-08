@extends('user.layout.page-app')
@section('page_title', 'KYC Verification')
@section('tab_title', 'KYC Verification')

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <h1 class="page-title-sm">✅ KYC Verification</h1>

            @if (!$kyc || $kyc->status === 'not_started' || $kyc->status === 'rejected')
                {{-- JAILAOI: KYC Form --}}
                <div class="card">
                    <div class="card-body">
                        @if ($kyc && $kyc->status === 'rejected')
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                Your KYC was rejected. {{ $kyc->admin_note ? 'Reason: ' . $kyc->admin_note : '' }}
                                Please correct the issues and resubmit.
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fa-solid fa-shield-halved me-2"></i>
                            We need to verify your identity before you can withdraw earnings.
                            Your information is kept secure and private.
                        </div>

                        <form id="kycForm" enctype="multipart/form-data">
                            @csrf

                            {{-- Step indicators --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between" id="stepIndicators">
                                    <div class="step-indicator active" data-step="1">1. Identity</div>
                                    <div class="step-indicator" data-step="2">2. Address</div>
                                    <div class="step-indicator" data-step="3">3. Payment</div>
                                    <div class="step-indicator" data-step="4">4. Submit</div>
                                </div>
                            </div>

                            {{-- Step 1: Identity --}}
                            <div class="kyc-step" id="step1">
                                <h5 class="mb-3">Identity Information</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Legal First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="legal_first_name" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Legal Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="legal_last_name" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Nationality <span class="text-danger">*</span></label>
                                        <input type="text" name="nationality" class="form-control" placeholder="e.g. Malaysian" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>ID Type <span class="text-danger">*</span></label>
                                        <select name="id_type" class="form-control" required>
                                            @foreach ($settings['allowed_id_types'] as $idType)
                                                <option value="{{ $idType }}">{{ ucwords(str_replace('_', ' ', $idType)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>ID Number <span class="text-danger">*</span></label>
                                        <input type="text" name="id_number" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>ID Front Photo <span class="text-danger">*</span></label>
                                        <input type="file" name="id_front_img" class="form-control-file" accept="image/*" required>
                                        <small class="text-muted">Max 5MB, JPG/PNG</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>ID Back Photo <span class="text-danger">*</span></label>
                                        <input type="file" name="id_back_img" class="form-control-file" accept="image/*" required>
                                        <small class="text-muted">Max 5MB, JPG/PNG</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next &rarr;</button>
                            </div>

                            {{-- Step 2: Address --}}
                            <div class="kyc-step d-none" id="step2">
                                <h5 class="mb-3">Address Information</h5>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Street Address <span class="text-danger">*</span></label>
                                        <input type="text" name="address" class="form-control" placeholder="Street address, P.O. box" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Country <span class="text-danger">*</span></label>
                                        <input type="text" name="country" class="form-control" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary" onclick="prevStep(1)">&larr; Back</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next &rarr;</button>
                            </div>

                            {{-- Step 3: Payment --}}
                            <div class="kyc-step d-none" id="step3">
                                <h5 class="mb-3">Payment Details</h5>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="form-control" id="paymentMethod" onchange="togglePaymentFields()" required>
                                            @foreach ($settings['allowed_payment_methods'] as $pm)
                                                <option value="{{ $pm }}">{{ ucwords(str_replace('_', ' ', $pm)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- PayPal --}}
                                <div class="payment-fields d-none" id="paypalFields">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>PayPal Email <span class="text-danger">*</span></label>
                                            <input type="email" name="payment_details_paypal" class="form-control" placeholder="your@paypal.com">
                                        </div>
                                    </div>
                                </div>

                                {{-- Bank --}}
                                <div class="payment-fields d-none" id="bankFields">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Bank Name <span class="text-danger">*</span></label>
                                            <input type="text" name="payment_details_bank_name" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Account Name <span class="text-danger">*</span></label>
                                            <input type="text" name="payment_details_acct_name" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Account Number <span class="text-danger">*</span></label>
                                            <input type="text" name="payment_details_acct_number" class="form-control">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>SWIFT / Routing Code</label>
                                            <input type="text" name="payment_details_swift" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                {{-- Mobile Money --}}
                                <div class="payment-fields d-none" id="mobileMoneyFields">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Provider Name <span class="text-danger">*</span></label>
                                            <input type="text" name="payment_details_provider" class="form-control" placeholder="e.g. GCash, PayMaya, M-Pesa">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" name="payment_details_mobile" class="form-control" placeholder="+60123456789">
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="payment_details" id="paymentDetailsJson">

                                <button type="button" class="btn btn-secondary" onclick="prevStep(2)">&larr; Back</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(4)">Next &rarr;</button>
                            </div>

                            {{-- Step 4: Agreement & Submit --}}
                            <div class="kyc-step d-none" id="step4">
                                <h5 class="mb-3">Review & Submit</h5>
                                <p class="text-muted">Please review your information before submitting.</p>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="agree_accurate" class="form-check-input" id="agreeAccurate" required>
                                    <label class="form-check-label" for="agreeAccurate">I confirm that all information provided is accurate and complete.</label>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="agree_terms" class="form-check-input" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">I agree to JailaOi's monetization terms and conditions.</label>
                                </div>
                                <button type="button" class="btn btn-secondary" onclick="prevStep(3)">&larr; Back</button>
                                <button type="button" class="btn btn-success" id="kycSubmitBtn" onclick="submitKyc()">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Submit KYC
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            @elseif (in_array($kyc->status, ['submitted', 'under_review']))
                {{-- JAILAOI: Submitted / Under Review --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div style="font-size:64px;color:#f59e0b;margin-bottom:16px;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h4>KYC Under Review</h4>
                        <p class="text-muted">
                            Your KYC was submitted on <strong>{{ $kyc->created_at?->format('F j, Y') }}</strong>.
                            We will notify you by email within 3-5 business days.
                        </p>
                        <span class="badge badge-warning" style="font-size:14px;padding:6px 16px;">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> {{ ucfirst(str_replace('_', ' ', $kyc->status)) }}
                        </span>
                    </div>
                </div>

            @elseif ($kyc->status === 'approved')
                {{-- JAILAOI: Approved --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div style="font-size:64px;color:#10b981;margin-bottom:16px;">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <h4>KYC Approved</h4>
                        <p class="text-muted">
                            Your identity has been verified. Your payment method on file:
                            <strong>{{ ucfirst(str_replace('_', ' ', $kyc->payment_method)) }}</strong>
                        </p>
                        <a href="{{ route('user.earnings.index') }}" class="btn btn-primary mt-3">
                            <i class="fa-solid fa-wallet me-2"></i> Go to Earnings
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
<style>
.step-indicator {
    flex: 1;
    text-align: center;
    padding: 10px;
    background: #f1f3f5;
    border-radius: 8px;
    margin: 0 4px;
    font-weight: 600;
    font-size: 13px;
    color: #6c757d;
    transition: all 0.2s;
}
.step-indicator.active {
    background: #E01E75;
    color: #fff;
}
.step-indicator.completed {
    background: #10b981;
    color: #fff;
}
</style>
<script>
let currentStep = 1;

function nextStep(step) {
    // Basic validation for current step
    const currentEl = document.getElementById('step' + currentStep);
    const inputs = currentEl.querySelectorAll('input[required], select[required]');
    for (const input of inputs) {
        if (!input.value.trim()) {
            toastr.error('Please fill in all required fields.');
            return;
        }
        if (input.type === 'file' && input.files.length === 0) {
            toastr.error('Please upload the required file.');
            return;
        }
    }

    // Build payment JSON when leaving step 3
    if (currentStep === 3) {
        buildPaymentJson();
    }

    showStep(step);
}

function prevStep(step) {
    showStep(step);
}

function showStep(step) {
    document.querySelectorAll('.kyc-step').forEach(el => el.classList.add('d-none'));
    document.getElementById('step' + step).classList.remove('d-none');

    document.querySelectorAll('.step-indicator').forEach(el => {
        el.classList.remove('active', 'completed');
        const s = parseInt(el.dataset.step);
        if (s === step) el.classList.add('active');
        else if (s < step) el.classList.add('completed');
    });

    currentStep = step;
}

function togglePaymentFields() {
    const method = document.getElementById('paymentMethod').value;
    const map = { paypal: 'paypal', bank: 'bank', mobile_money: 'mobileMoney' };
    document.querySelectorAll('.payment-fields').forEach(el => el.classList.add('d-none'));
    if (map[method]) {
        document.getElementById(map[method] + 'Fields').classList.remove('d-none');
    }
}

function buildPaymentJson() {
    const method = document.getElementById('paymentMethod').value;
    let details = {};

    if (method === 'paypal') {
        details = {
            paypal_email: document.querySelector('[name="payment_details_paypal"]').value
        };
    } else if (method === 'bank') {
        details = {
            bank_name: document.querySelector('[name="payment_details_bank_name"]').value,
            account_name: document.querySelector('[name="payment_details_acct_name"]').value,
            account_number: document.querySelector('[name="payment_details_acct_number"]').value,
            swift: document.querySelector('[name="payment_details_swift"]').value,
        };
    } else if (method === 'mobile_money') {
        details = {
            provider: document.querySelector('[name="payment_details_provider"]').value,
            mobile_number: document.querySelector('[name="payment_details_mobile"]').value,
        };
    }

    document.getElementById('paymentDetailsJson').value = JSON.stringify(details);
}

$(document).ready(function() {
    // Show fields for initial payment method on load
    togglePaymentFields();
});

function submitKyc() {
    const btn = document.getElementById('kycSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Submitting...';

    // Build payment JSON one more time
    buildPaymentJson();

    const formData = new FormData(document.getElementById('kycForm'));
    $.ajax({
        type: 'POST',
        url: '{{ route("user.kyc.store") }}',
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit KYC';
            if (resp.status == 200) {
                toastr.success(resp.success);
                setTimeout(() => location.reload(), 1500);
            } else {
                const err = Array.isArray(resp.errors) ? resp.errors.join('\n') : resp.errors;
                toastr.error(err);
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Submit KYC';
            toastr.error('Something went wrong');
        }
    });
}
</script>
@endsection
