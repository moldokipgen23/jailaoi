@extends('user.layout.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center" style="background:#E01E75;color:#fff;">
                    <h4 class="m-0">Verify Your Email</h4>
                </div>
                <div class="card-body text-center p-5">
                    <i class="fa-solid fa-envelope-circle-check" style="font-size:64px;color:#E01E75;margin-bottom:20px;"></i>
                    <h5>We sent a verification link to your email</h5>
                    <p class="text-muted mt-3">Click the link in the email to verify your account. If you did not receive the email, check your spam folder or request a new one.</p>

                    <form id="resendForm" method="POST" action="{{ route('user.verify.resend') }}" class="mt-4">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                        </div>
                        <button type="submit" class="btn text-white px-4" style="background:#E01E75;">
                            <i class="fa-solid fa-paper-plane me-2"></i>Resend Verification Email
                        </button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('user.login') }}" class="text-muted">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#resendForm').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        dataType: 'JSON',
        success: function(res) {
            if (res.status == 200) {
                toastr.success(res.success);
            } else {
                toastr.error(res.errors);
            }
        },
        error: function() {
            toastr.error('Something went wrong.');
        }
    });
});
</script>
@endpush
@endsection
