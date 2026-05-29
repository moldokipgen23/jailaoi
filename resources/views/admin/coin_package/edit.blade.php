@extends('admin.layout.page-app')
@section('page_title', __('label.edit_coin_package'))
@section('tab_title', __('label.edit_coin_package'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.edit_coin_package')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.coinpackage.index') }}">{{__('label.coin_package')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.edit_coin_package')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.coinpackage.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.coin_package_list')}}</a>
                </div>
            </div>

			<div class="card custom-border-card">
				<form id="package_update" enctype="multipart/form-data">
					<input type="hidden" name="id" value="{{ $data['id'] }}">
                    <input type="hidden" name="old_storage_type" value="{{ $data['storage_type'] }}">
					<div class="form-row">
                        <div class="col-md-9">
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('label.name')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $data['name'] }}" class="form-control" placeholder="{{__('label.name_here')}}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.price')}}<span class="text-danger">*</span></label>
                                        <input type="number" value="{{ $data['price'] }}" name="price" min="0" class="form-control" placeholder="{{__('label.price_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{__('label.coin')}}<span class="text-danger">*</span></label>
                                        <input type="number" value="{{ $data['coin'] }}" name="coin" min="0" class="form-control" placeholder="{{__('label.coin_here')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.android_product_package')}}</label>
                                        <input name="android_product_package" value="{{$data['android_product_package'] }}" type="text" class="form-control" placeholder="{{__('label.android_product_package_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.ios_product_package')}}</label>
                                        <input name="ios_product_package" value="{{$data['ios_product_package'] }}" type="text" class="form-control" placeholder="{{__('label.ios_product_package_here')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{__('label.web_product_package')}}</label>
                                        <input name="web_product_package" value="{{$data['web_product_package'] }}" type="text" class="form-control" placeholder="{{__('label.web_product_package_here')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
							<div class="form-group ml-5">
								<label class="ml-5">{{__('label.image')}}<span class="text-danger">*</span></label>
								<div class="avatar-upload ml-5">
									<div class="avatar-edit">
										<input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
										<label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
									</div>
									<div class="avatar-preview">
										<img src="{{ $data['image'] }}" id="imagePreview1">
									</div>
								</div>
								<input type="hidden" name="old_image" value="{{ $data['image'] }}">
								<label class="mt-3 ml-5 text-gray">{{__('label.max_size_5mb')}}</label>
							</div>
						</div>
                    </div>
					<div class="border-top pt-3 text-right">
						<button type="button" class="btn btn-default mw-120" onclick="update_package()">{{__('label.update')}}</button>
						<a href="{{route('admin.package.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
						<input type="hidden" name="_method" value="PATCH">
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('pagescript')
	<script>
        // Sidebar Scroll Down
		sidebar_down(1330);

		function update_package() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#package_update")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    enctype: 'multipart/form-data',
                    type: 'POST',
                    url: '{{route("admin.coinpackage.update", [$data->id])}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'package_update', '{{ route("admin.coinpackage.index") }}');
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