@extends('admin.layout.page-app')
@section('page_title', __('label.edit_user'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
	@include('admin.layout.header')

	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">{{__('label.edit_user')}}</h1>

		<div class="border-bottom row mb-3">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{__('label.users')}}</a></li>
					<li class="breadcrumb-item active" aria-current="page">{{__('label.edit_user')}}</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('user.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.user_list')}}</a>
			</div>
		</div>
		<!-- edit user  -->
		<div class="card custom-border-card mt-3">
			<form id="user_update" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="id" value="@if($data){{$data->id}}@endif">
				<div class="form-row">
					<div class="col-md-8">
						<div class="form-row">
							<div class="col-md-4">
								<div class="form-group">
									<label>{{__('label.full_name')}}<span class="text-danger">*</span></label>
									<input type="text" value="@if($data){{$data->full_name}}@endif" name="full_name" class="form-control" placeholder="{{__('label.name_here')}}">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>{{__('label.email')}}<span class="text-danger">*</span></label>
									<input type="email" value="@if($data){{$data->email}}@endif" name="email" class="form-control" placeholder="{{__('label.email_here')}}">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="gender">{{__('label.gender')}}<span class="text-danger">*</span></label>
									<div class="radio-group">
										<div class="custom-control custom-radio">
											<input type="radio" id="gender" name="gender" class="custom-control-input" value="1" {{$data->gender==1 ?"checked":""}}>
											<label class="custom-control-label" for="gender">{{__('label.male')}}</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" id="gender1" name="gender" class="custom-control-input" value="2" {{$data->gender==2 ?"checked":""}}>
											<label class="custom-control-label" for="gender1">{{__('label.female')}}</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-2">
								<div class="form-group">
									<label>{{__('label.country_code')}}<span class="text-danger">*</span></label>
									<input type="text" value="@if($data){{$data->country_code}}@endif" name="country_code" class="form-control" placeholder="{{__('label.+91')}}">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label> {{__('label.mobile_number')}}<span class="text-danger">*</span></label>
									<input type="text" value="@if($data){{$data->mobile_number}}@endif" name="mobile_number" class="form-control " placeholder="{{__('label.mobile_number_here')}}">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label>{{__('label.country_name')}}<span class="text-danger">*</span></label>
									<input type="text" value="@if($data){{$data->country_name}}@endif" name="country_name" class="form-control" placeholder="{{__('label.country_name_here')}}">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label> {{__('label.new_password')}}</label>
									<input type="password" name="password" class="form-control" placeholder="{{__('label.password_here')}}">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group ml-5">
							<label class="ml-5">{{__('label.image')}}</label>
							<div class="avatar-upload ml-5">
								<div class="avatar-edit">
									<input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
									<label for="imageUpload" title="Select File"></label>
								</div>
								<div class="avatar-preview">
									<img src="{{$data['image']}}" alt="no_img.png" id="imagePreview">
								</div>
							</div>
							<input type="hidden" name="old_image" value="@if($data){{$data->image}}@endif">
							<label class="mt-3 ml-5 text-gray">{{__('label.image_note')}}</label>
						</div>
					</div>
				</div>
				<div class="border-top pt-3 text-right">
					<button type="button" class="btn btn-default mw-120" onclick="update_user()">{{__('label.update')}}</button>
					<a href="{{route('user.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
					<input type="hidden" name="_method" value="PATCH">
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('pagescript')
<script>
	function update_user() {

		var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
		if (Check_Admin == 1) {

			$("#dvloader").show();
			var formData = new FormData($("#user_update")[0]);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				enctype: 'multipart/form-data',
				type: 'POST',
				url: '{{route("user.update", [$data->id])}}',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function(resp) {
					$("#dvloader").hide();
					get_responce_message(resp, 'user_update', '{{ route("user.index") }}');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$("#dvloader").hide();
					toastr.error(errorThrown, textStatus);
				}
			});
		} else {
			toastr.error('You have no right to add, edit, and delete.');

		}
	}
</script>
@endsection