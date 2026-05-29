@extends('admin.layout.page-app')
@section('page_title', __('Label.Edit_Package'))

@section('content')
	@include('admin.layout.sidebar')

	<div class="right-content">
		@include('admin.layout.header')

		<div class="body-content">
			<!-- mobile title -->
			<h1 class="page-title-sm">{{__('Label.Edit_Package')}}</h1>

			<div class="border-bottom row mb-3">
				<div class="col-sm-10">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Label.Dashboard')}}</a></li>
						<li class="breadcrumb-item"><a href="{{ route('package.index') }}">{{__('Label.Package')}}</a></li>
						<li class="breadcrumb-item active" aria-current="page">{{__('Label.Edit_Package')}}</li>
					</ol>
				</div>
				<div class="col-sm-2 d-flex align-items-center justify-content-end">
					<a href="{{ route('package.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('Label.Package List')}}</a>
				</div>
			</div>

			<div class="card custom-border-card mt-3">
				<div class="card-body">
					<form id="package_update" enctype="multipart/form-data" autocomplete="off">
						<input type="hidden" name="id" value="@if($data){{$data->id}}@endif">
						<div class="form-row">
							<div class="col-md-9">
								<div class="form-row">
									<div class="col-md-6">
										<div class="form-group">
											<label>{{__('Label.Name')}}<span class="text-danger">*</span></label>
											<input type="text" name="name" value="@if($data){{$data->name}}@endif" class="form-control" placeholder="Enter Name">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>{{__('Label.Price')}}<span class="text-danger">*</span></label>
											<input type="number" name="price" min="0" value="@if($data){{$data->price}}@endif" class="form-control" placeholder="Enter Price">
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="type">{{__('Label.Package Time')}}<span class="text-danger">*</span></label>
											<select class="form-control" id="validity_type" name="type">
												<option value="">Select Type</option>
												<option value="Day" {{$data->type == 'Day' ? 'selected' : ''}}>Day</option>
												<option value="Week" {{$data->type == 'Week' ? 'selected' : ''}}>Week</option>
												<option value="Month" {{$data->type == 'Month' ? 'selected' : ''}}>Month</option>
												<option value="Year" {{$data->type == 'Year' ? 'selected' : ''}}>Year</option>
											</select>
										</div>
									</div>
									<div class="col-md-6 mb-6 mt-4">
										<div class="form-group mt-2">
											<select class="form-control time" id="time" name="time">
												<option value="">Select Number</option>
												@for($i=1; $i<=31; $i++) 
													<option value="{{$i}}" {{$data->time == $i ? 'selected' : ''}}>{{$i}}</option>
												@endfor
											</select>
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Android Package<span class="text-danger">*</span></label>
											<input name="android_product_package" type="text" class="form-control" placeholder="Enter Android Package" value="{{$data->android_product_package}}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>ISO Package<span class="text-danger">*</span></label>
											<input name="ios_product_package" type="text" class="form-control" placeholder="Enter ISO Package" value="{{$data->ios_product_package}}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Web Package<span class="text-danger">*</span></label>
											<input name="web_product_package" type="text" class="form-control" placeholder="Enter Web Package" value="{{$data->web_product_package}}">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-2 ml-5">
								<div class="form-group ml-3">
									<label>{{__('Label.Image')}}<span class="text-danger">*</span></label>
									<div class="avatar-upload">
										<div class="avatar-edit">
											<input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
											<label for="imageUpload" title="Select File"></label>
										</div>
										<div class="avatar-preview">
											<img src="{{$data['image']}}" alt="upload_img.png" id="imagePreview">
										</div>
									</div>
									<label class="mt-3 text-gray">{{__('Label.image_note')}}</label>
									<input type="hidden" name="old_image" value="@if($data){{$data->image}}@endif">
								</div>
							</div>
						</div>
						<div class="border-top pt-3 text-right">
							<button type="button" class="btn btn-default mw-120" onclick="update_package()">{{__('Label.UPDATE')}}</button>
							<a href="{{route('package.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('Label.CANCEL')}}</a>
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

		function update_package() {

			var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
			if (Check_Admin == 1) {
				$("#dvloader").show();
				var formData = new FormData($("#package_update")[0]);

				$.ajax({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					enctype: 'multipart/form-data',
					type: 'POST',
					url: '{{route("package.update", [$data->id])}}',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success: function(resp) {
						$("#dvloader").hide();
						get_responce_message(resp, 'package_update', '{{ route("package.index") }}');
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

		$(document).ready(function() {
			var validity_type = "<?php echo $data->type; ?>";
			if (validity_type == "Day") {
				for (let i = 8; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Week") {
				for (let i = 5; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Month") {
				for (let i = 13; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (validity_type == "Year") {
				for (let i = 2; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else {
				$('.time').hide();
			}
		});

		$('#validity_type').on('click', function() {
			$('.time').show();
			var type = $("#validity_type").val()

			for (let i = 1; i <= 31; i++) {
				$(".time option[value=" + i + "]").show();
				$(".time option[value=" + i + "]").attr("selected", false);
			}

			if (type == "Day") {
				for (let i = 8; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Week") {
				for (let i = 5; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Month") {
				for (let i = 13; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else if (type == "Year") {
				for (let i = 2; i <= 31; i++) {
					$(".time option[value=" + i + "]").hide();
				}
			} else {
				$('.time').hide();
			}
		})
	</script>
@endsection