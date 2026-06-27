@extends('admin.layout.page-app')
@section('page_title', __('label.edit_payment'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
	@include('admin.layout.header')

	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">{{__('label.edit_payment')}}</h1>

		<div class="border-bottom row mb-3">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{ route('payment.index') }}">{{__('label.payment')}}</a></li>
					<li class="breadcrumb-item active" aria-current="page">{{__('label.edit_payment')}}</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center">
				<a href="{{ route('payment.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.payment_list')}}</a>
			</div>
		</div>
		<!-- edit payment  -->
		<div class="card custom-border-card mt-3">
			<div class="card-body">
				<form id="payment_update" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" name="id" value="@if($data){{$data->id}}@endif">
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.name')}}<span class="text-danger">*</span></label>
								<input name="name" type="text" class="form-control" readonly placeholder="{{__('label.name_here')}}" value="@if($data){{$data->name}}@endif">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.status')}}<span class="text-danger">*</span></label>
								<select class="form-control" name="visibility">
									<option value="">{{__('label.select_visibility')}}</option>
									<option value="1" {{$data->visibility == 1 ? 'selected' : ''}}>{{__('label.active')}}</option>
									<option value="0" {{$data->visibility == 0 ? 'selected' : ''}}>{{__('label.in_active')}}</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.payment_environment')}}<span class="text-danger">*</span></label>
								<select class="form-control" name="is_live">
									<option value="">{{__('label.select_payment_environment')}}</option>
									<option value="1" {{$data->is_live == 1 ? 'selected' : ''}}>{{__('label.live')}}</option>
									<option value="0" {{$data->is_live == 0 ? 'selected' : ''}}>{{__('label.sandbox')}}</option>
								</select>
							</div>
						</div>
					</div>
					<!-- inapppurchage -->
					@if($data->id == 1)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.andorid_id')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.android_id_here')}}" value="{{$data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.ios_id')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.ios_id_here')}}" value="{{$data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- Paypal -->
					@if($data->id == 2)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.client_id')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.client_id_here')}}" value="{{$data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.secret_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.secret_key_here')}}" value="{{$data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- Razorpay -->
					@if($data->id == 3)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.key')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.key_here')}}" value="{{$data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.secret_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.secret_key_here')}}" value="{{$data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- FlutterWave -->
					@if($data->id == 4)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.public_id')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.public_id_here')}}" value="{{ $data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.encryption_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.encryption_key_here')}}" value="{{ $data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- PayUMoney -->
					@if($data->id == 5)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.merchant_id')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.merchant_id_here')}}" value="{{ $data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.merchant_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.merchant_key_here')}}" value="{{ $data->key_2}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.merchant_salt_key')}}<span class="text-danger">*</span></label>
								<input name="key_3" type="text" class="form-control" placeholder="{{__('label.merchant_salt_key_here')}}" value="{{ $data->key_3}}">
							</div>
						</div>
					</div>
					@endif
					<!-- PayTm -->
					@if($data->id == 6)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.merchant_id')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.merchant_id_here')}}" value="{{ $data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.merchant_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.merchant_key_here')}}" value="{{ $data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- Stripe -->
					@if($data->id == 7)
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.publishable_key')}}<span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="{{__('label.publishable_key_here')}}" value="{{$data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{{__('label.secret_key')}}<span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="{{__('label.secret_key_here')}}" value="{{$data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<!-- Cashfree -->
					@if($data->name == 'cashfree')
					<div class="form-row">
						<div class="col-md-4">
							<div class="form-group">
								<label>App ID <span class="text-danger">*</span></label>
								<input name="key_1" type="text" class="form-control" placeholder="Cashfree App ID" value="{{$data->key_1}}">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Secret Key <span class="text-danger">*</span></label>
								<input name="key_2" type="text" class="form-control" placeholder="Cashfree Secret Key" value="{{$data->key_2}}">
							</div>
						</div>
					</div>
					@endif
					<div class="border-top pt-3 text-right">
						<button type="button" class="btn btn-default mw-120" onclick="update_payment()">{{__('label.update')}}</button>
						<a href="{{route('payment.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
						<input type="hidden" name="_method" value="PATCH">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('pagescript')
<script>
	// Sidebar Scroll Down
	sidebar_down($(document).height());

	function update_payment() {

		var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
		if (Check_Admin == 1) {
			$("#dvloader").show();
			var formData = new FormData($("#payment_update")[0]);

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				enctype: 'multipart/form-data',
				type: 'POST',
				url: '{{route("payment.update", [$data->id])}}',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function(resp) {
					$("#dvloader").hide();
					get_responce_message(resp, 'payment_update', '{{ route("payment.index") }}');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$("#dvloader").hide();
					toastr.error(errorThrown, textStatus);
				}
			});
		} else {
			toastr.error("{{__('label.you_have_no_right_to_add_edit_and_delete')}}");
		}
	}
</script>
@endsection