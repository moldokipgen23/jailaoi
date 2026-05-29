@extends('admin.layout.page-app')
@section('page_title', __('label.storage_settings'))
@section('tab_title', __('label.storage_settings'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">

            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.storage_settings')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.storage_settings')}}</li>
                    </ol>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="storage_setting" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $storage['id'] }}">
                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.storage_type')}}<span class="text-danger">*</span></label>
                                <select class="form-control" name="storage_type" id="storage_type">
                                    <option value="1" {{ $storage->storage_type == 1 ? 'selected' : ''}}>{{__('label.local_storage')}}</option>
                                    <option value="2" {{ $storage->storage_type == 2 ? 'selected' : ''}}>{{__('label.aws_s3_buckets')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row aws_s3_storage">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.access_key')}}<span class="text-danger">*</span></label>
                                <input type="text" name="s3_access_key" value="{{ $storage['s3_access_key'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.secret_key')}}<span class="text-danger">*</span></label>
                                <input type="text" name="s3_secret_key" value="{{ $storage['s3_secret_key'] }}" class="form-control" placeholder="{{__('label.key_here')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.bucket_name')}}<span class="text-danger">*</span></label>
                                <input type="text" name="s3_bucket_name" value="{{ $storage['s3_bucket_name'] }}" class="form-control" placeholder="{{__('label.bucket_name_here')}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row aws_s3_storage">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.region')}}<span class="text-danger">*</span></label>
                                <input type="text" name="s3_region" value="{{ $storage['s3_region'] }}" class="form-control" placeholder="{{__('label.region_here')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{__('label.endpoint')}}<span class="text-danger">*</span></label>
                                <input type="text" name="s3_endpoint" value="{{ $storage['s3_endpoint'] }}" class="form-control" placeholder="{{__('label.endpoint_here')}}">
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 text-right">
                        <button type="button" class="btn btn-default mw-120" onclick="storage_setting()">{{__('label.save')}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>                   
                </form>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        // Sidebar Scroll Down
        let sidebarHeight = $('.sidebar')[0].scrollHeight;
        sidebar_down(sidebarHeight);

        $(document).ready(function () {
            var storage_type = "<?php echo $storage['storage_type'] ?? 0; ?>";
            if (storage_type == 2) {
                $(".aws_s3_storage").show();
            } else {
                $(".aws_s3_storage").hide();
            }

            $('#storage_type').change(function() {
                var optionValue = $(this).val();
                if (optionValue == 2) {
                    $(".aws_s3_storage").show();
                } else {
                    $(".aws_s3_storage").hide();
                }
            });
		});

        function storage_setting(){

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if(Demo_Mode == 1){

                $("#dvloader").show();
                var formData = new FormData($("#storage_setting")[0]);
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.storagesetting.save") }}',
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(resp){
                        $("#dvloader").hide();
                        get_responce_message(resp, 'storage_setting', '{{ route("admin.storagesetting.index") }}');
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