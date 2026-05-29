@extends('admin.layout.page-app')
@section('page_title', __('label.profile'))
@section('tab_title', __('label.profile'))

@section('content')
	@include('admin.layout.sidebar')

	<div class="right-content">
		@include('admin.layout.header')

		<div class="body-content">
			<!-- mobile title -->
			<h1 class="page-title-sm">{{__('label.profile')}}</h1>

			<div class="border-bottom row mb-3">
				<div class="col-sm-12">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
						<li class="breadcrumb-item active" aria-current="page">{{__('label.profile')}}</li>
					</ol>
				</div>
			</div>

            <!-- Profile Info -->
            <div class="card custom-border-card">
                <h5 class="card-header">{{__('label.personal_info')}}</h5>
                <div class="card-body">
                    <form id="profile" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.user_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="user_name" value="{{ $data->user_name }}" class="form-control" placeholder="{{__('label.user_name_here')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ $data->email }}" class="form-control" placeholder="{{__('label.email_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="update_profile()">{{__('label.update')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password  -->
            <div class="card custom-border-card">
                <h5 class="card-header">{{__('label.change_password')}}</h5>
                <div class="card-body">
                    <form id="change_password" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.current_password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="current_password" class="form-control" placeholder="{{__('label.current_password_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.new_password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="new_password" class="form-control" placeholder="{{__('label.new_password_here')}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.confirm_password')}}<span class="text-danger">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="{{__('label.confirm_password_here')}}">
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-3 text-right">
                            <button type="button" class="btn btn-default mw-120" onclick="update_password()">{{__('label.update')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function update_profile(){

			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#profile")[0]);

                $.ajax({
                    type: 'POST',
                    url: '{{route("admin.profile.store")}}',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'profile', '{{ route("admin.profile.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
		}
        function update_password() {

			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#change_password")[0]);

                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.profile.changepassword") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'change_password', '{{ route("admin.profile.index") }}');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#dvloader").hide();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
	</script>
@endsection