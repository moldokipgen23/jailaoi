@extends('admin.layout.page-app')
@section('page_title', __('label.edit_radio_station'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
	@include('admin.layout.header')

	<div class="body-content">
		<!-- mobile title -->
		<h1 class="page-title-sm">{{__('label.edit_radio_station')}}</h1>

		<div class="border-bottom row mb-3">
			<div class="col-sm-10">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
					<li class="breadcrumb-item"><a href="{{ route('song.index') }}">{{__('label.radio_station')}}</a></li>
					<li class="breadcrumb-item active" aria-current="page">{{__('label.edit_radio_station')}}</li>
				</ol>
			</div>
			<div class="col-sm-2 d-flex align-items-center justify-content-end">
				<a href="{{ route('song.index') }}" class="btn btn-default mw-120 mb-3">{{__('label.radio_station')}}</a>
			</div>
		</div>
		<!-- edit song  -->
		<div class="card custom-border-card mt-3">
			<form id="song_update" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="id" value="@if($data){{$data->id}}@endif">
				<input type="hidden" name="old_song_url" value="@if($data){{$data->song_url}}@endif">
				<input type="hidden" name="old_upload_type" value="@if($data){{$data->upload_type}}@endif">
				<div class="form-row mb-2">
					<div class="col-md-8">
						<div class="form-row">
							<div class="col-md-6">
								<div class="form-group">
									<label>{{__('label.name')}}<span class="text-danger">*</span></label>
									<input type="text" value="@if($data){{$data->name}}@endif" name="name" class="form-control" placeholder="{{__('label.name_here')}}">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>{{__('label.artist')}}<span class="text-danger">*</span></label>
									<select class="form-control" name="artist_id" id="artist_id">
										<option value="">{{__('label.select_artist')}}</option>
										@foreach ($artist as $key => $value)
										<option value="{{ $value->id}}" {{ $data->artist_id == $value->id  ? 'selected' : ''}}>
											{{ $value->name }}
										</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="form-group">
									<label>{{__('label.category')}}<span class="text-danger">*</span></label>
									<select class="form-control" name="category_id" id="category_id">
										<option value="">{{__('label.select_category')}}</option>
										@foreach ($category as $key => $value)
										<option value="{{ $value->id}}" {{ $data->category_id == $value->id  ? 'selected' : ''}}>
											{{ $value->name }}
										</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>{{__('label.language')}}<span class="text-danger">*</span></label>
									<select class="form-control" name="language_id" id="language_id">
										<option value="">{{__('label.select_language')}}</option>
										@foreach ($language as $key => $value)
										<option value="{{ $value->id}}" {{ $data->language_id == $value->id  ? 'selected' : ''}}>
											{{ $value->name }}
										</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-6">
								<div class="form-group">
									<label>{{__('label.city')}}<span class="text-danger">*</span></label>
									<select class="form-control" name="city_id" id="city_id">
										<option value="">{{__('label.select_city')}}</option>
										@foreach ($city as $key => $value)
										<option value="{{ $value->id}}" {{ $data->city_id == $value->id  ? 'selected' : ''}}>
											{{ $value->name }}
										</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{{__('label.is_premium')}}<span class="text-danger">*</span></label>
									<div class="radio-group">
										<div class="custom-control custom-radio">
											<input type="radio" name="is_premium" id="is_premium_yes" class="custom-control-input" value="1" {{ $data->is_premium == 1 ? 'checked' : ''}}>
											<label class="custom-control-label" for="is_premium_yes">{{__('label.yes')}}</label>
										</div>
										<div class="custom-control custom-radio">
											<input type="radio" name="is_premium" id="is_premium_no" class="custom-control-input" value="0" {{ $data->is_premium == 0 ? 'checked' : ''}}>
											<label class="custom-control-label" for="is_premium_no">{{__('label.no')}}</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>{{__('label.duration')}}<span class="text-danger">*</span></label>
									<input type="text"  name="duration" id="timePicker" class="form-control" placeholder="{{__('label.duration_here')}}">
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-3">
								<div class="form-group">
									<label>{{__('label.upload_type')}}<span class="text-danger">*</span></label>
									<select class="form-control" name="upload_type" id="upload_type">
										<option value="1" {{ $data->upload_type == 1  ? 'selected' : ''}}>{{__('label.server_audio')}}</option>
										<option value="2" {{ $data->upload_type == 2  ? 'selected' : ''}}>{{__('label.external_url')}}</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 video_box">
								<div class="form-group">
									<div class="d-block">
										<label>{{__('label.upload_full_song')}}<span class="text-danger">*</span></label>
										<div id="filelist"></div>
										<div id="container">
											<div class="form-group">
												<input type="file" id="uploadFile" name="uploadFile" class="form-control import-file p-2">
											</div>
											<input type="hidden" name="song_url" id="song_url" class="form-control">
										</div>
									</div>
								</div>
								@if($data->upload_type==1)
								<a href="{{$data->song_url}}" target="_blank" class="btn-link">{{basename($data->song_url)}}</a>
								@endif
							</div>
							<div class="col-md-2 mt-4 video_box">
								<div class="form-group mt-3">
									<a id="upload" class="btn text-white bg-primary-color">Upload Files</a>
								</div>
							</div>
							<div class="col-md-6 url_box">
								<div class="form-group">
									<label>{{__('label.audio_url')}}<span class="text-danger">*</span></label>
									<input type="text" name="url" class="form-control" value="@if($data->upload_type !=1) {{$data->song_url}} @endif" placeholder="{{__('label.audio_url_here')}}">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group ml-5">
							<label>{{__('label.image')}}<span class="text-danger">*</span></label>
							<div class="avatar-upload">
								<div class="avatar-edit">
									<input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
									<label for="imageUpload" title="Select File"></label>
								</div>
								<div class="avatar-preview">
									<img src="{{$data->image}}" alt="upload_img.png" id="imagePreview">
								</div>
							</div>
							<label class="mt-3 text-gray">{{__('label.image_note')}}</label>
							<input type="hidden" name="old_image" value="{{$data->image}}">
						</div>
					</div>
				</div>
				<div class="border-top pt-3 text-right">
					<button type="button" class="btn btn-default mw-120" onclick="update_song()">{{__('label.update')}}</button>
					<a href="{{route('song.index')}}" class="btn btn-cancel mw-120 ml-2">{{__('label.cancel')}}</a>
					<input type="hidden" name="_method" value="PATCH">
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('pagescript')
<script>
	var duration = '<?php echo $data->duration; ?>';

	let hours = msToHours(duration);
	let minutes = msToMinutes(duration);
	let seconds = msToSeconds(duration);
	var date = new Date();
	date.setHours(hours, minutes, seconds);

	$('#timePicker').datetimepicker({
		useCurrent: false,
		format: 'HH:mm:ss',
		defaultDate: date,
		showClose: true,
		showTodayButton: true,
		icons: {
			up: "fa fa-chevron-up",
			down: "fa fa-chevron-down",
			today: "fa fa-clock fa-regular",
			close: "fa fa-times",
		}
	})


	$("#category_id").select2();
	$("#artist_id").select2();
	$("#language_id").select2();
	$("#city_id").select2();

	function update_song() {

		var Check_Admin = '<?php echo Check_Admin_Access(); ?>';
		if (Check_Admin == 1) {

			$("#dvloader").show();
			var formData = new FormData($("#song_update")[0]);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				enctype: 'multipart/form-data',
				type: 'POST',
				url: '{{route("song.update", [$data->id])}}',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				success: function(resp) {
					$("#dvloader").hide();
					get_responce_message(resp, 'song_update', '{{ route("song.index") }}');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$("#dvloader").hide();
					toastr.error(errorThrown, textStatus);
				}
			});
		} else {
			toastr.error('{{__("label.you_have_no_right_to_add_edit_and_delete")}}');
		}
	}

	var upload_type = "<?php echo $data->upload_type; ?>";
	if (upload_type == 1) {
		$(".url_box").hide();
	} else {
		$(".video_box").hide();
	}
	$('#upload_type').change(function() {
		var optionValue = $(this).val();

		if (optionValue == 1) {
			$(".video_box").show();
			$(".url_box").hide();
		} else {
			$(".url_box").show();
			$(".video_box").hide();
		}
	});
</script>
@endsection