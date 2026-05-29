@extends('user.layout.page-app')
@section('page_title', __('label.change_password'))
@section('tab_title', __('label.change_password'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.change_password')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.change_password')}}</li>
                    </ol>
                </div>
            </div>

            <form id="change_password" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="id" value="{{ $user_id }}">
                <div class="card custom-border-card">
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
                        <input type="hidden" name="_method" value="PATCH">
                    </div>
                </div>
            </form>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
		function update_password() {

			var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#change_password")[0]);

                $.ajax({
                    type: 'POST',
                    url: '{{route("user.password.update", [$user_id])}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'change_password', '{{ route("user.password.index") }}');
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