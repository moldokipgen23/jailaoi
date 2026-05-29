@extends('user.layout.page-app')
@section('page_title', __('label.profile'))
@section('tab_title', __('label.profile'))

@section('content')
    @include('user.layout.sidebar')

    <div class="right-content">
        @include('user.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.profile')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.profile')}}</li>
                    </ol>
                </div>
            </div>

            <form id="profile" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $data['id'] }}">
                <input type="hidden" name="old_image_storage_type" value="{{ $data['image_storage_type'] }}">
                <input type="hidden" name="old_cover_img_storage_type" value="{{ $data['cover_img_storage_type'] }}">
                <input type="hidden" name="old_front_id_proof_storage_type" value="{{ $data['front_id_proof_storage_type'] }}">
                <input type="hidden" name="old_back_id_proof_storage_type" value="{{ $data['back_id_proof_storage_type'] }}">
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.channel_info')}}</h5>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.channel_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="channel_name" value="{{ $data['channel_name'] }}" class="form-control" placeholder="{{__('label.channel_name_here')}}" autofocus>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>{{__('label.description')}}<span class="text-danger">*</span></label>
                                    <textarea name="description" rows="1" class="form-control" placeholder="{{__('label.description_here')}}">{{ $data['description'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.personal_info')}}</h5>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.full_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="full_name" value="{{ $data['full_name'] }}" class="form-control" placeholder="{{__('label.full_name_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                            <input type="email" name="email" value="{{ $data['email'] }}" class="form-control" placeholder="{{__('label.email_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{__('label.country_code')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="country_code" value="{{ $data['country_code'] }}" class="form-control" placeholder="{{__('label.+91')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.mobile_number')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="mobile_number" value="{{ $data['mobile_number'] }}" class="form-control" placeholder="{{__('label.mobile_number_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{__('label.country_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="country_name" value="{{ $data['country_name'] }}" class="form-control" placeholder="{{__('label.in')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.push_notification_status')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="push_notification_status_on" name="push_notification_status" class="custom-control-input" value="1" {{ $data['push_notification_status'] == 1 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="push_notification_status_on">{{__('label.on')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="push_notification_status_off" name="push_notification_status" class="custom-control-input" value="0" {{ $data['push_notification_status'] == 0 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="push_notification_status_off">{{__('label.off')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.send_mail_status')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="send_mail_status_on" name="send_mail_status" class="custom-control-input" value="1" {{ $data['send_mail_status'] == 1 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="send_mail_status_on">{{__('label.on')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="send_mail_status_off" name="send_mail_status" class="custom-control-input" value="0" {{ $data['send_mail_status'] == 0 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="send_mail_status_off">{{__('label.off')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.thumbnail_image')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' name="image" id="imageUpload1" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload1" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ $data['image'] }}" id="imagePreview1">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_image" value="{{ $data['image'] }}">
                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{__('label.cover_image')}}</label>
                                    <div class="avatar-upload-landscape">
                                        <div class="avatar-edit-landscape">
                                            <input type='file' name="cover_img" id="imageUpload2" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload2" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview-landscape">
                                            <img src="{{ $data['cover_img'] }}" id="imagePreview2">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_cover_img" value="{{ $data['cover_img'] }}">
                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.address_info')}}</h5>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{__('label.address')}}<span class="text-danger">*</span></label>
                                    <textarea name="address" rows="1" class="form-control" placeholder="{{__('label.address_here')}}">{{ $data['address'] }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.city')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="city" value="{{ $data['city'] }}" class="form-control" placeholder="{{__('label.city_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.state')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="state" value="{{ $data['state'] }}" class="form-control" placeholder="{{__('label.state_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.country')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="country" value="{{ $data['country'] }}" class="form-control" placeholder="{{__('label.country_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{__('label.pincode')}}<span class="text-danger">*</span></label>
                                            <input type="number" name="pincode" value="{{ $data['pincode'] }}" class="form-control" placeholder="{{__('label.pincode_here')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.social_info')}}</h5>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.website')}}</label>
                                    <input type="text" name="website" value="{{ $data['website'] }}" class="form-control" placeholder="{{__('label.website_here')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.facebook_url')}}</label>
                                    <input type="text" name="facebook_url" value="{{ $data['facebook_url'] }}" class="form-control" placeholder="{{__('label.facebook_url_here')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.instagram_url')}}</label>
                                    <input type="text" name="instagram_url" value="{{ $data['instagram_url'] }}" class="form-control" placeholder="{{__('label.instagram_url_here')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{__('label.twitter_url')}}</label>
                                    <input type="text" name="twitter_url" value="{{ $data['twitter_url'] }}" class="form-control" placeholder="{{__('label.twitter_url_here')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card custom-border-card">
                    <h5 class="card-header">{{__('label.banking_info')}}</h5>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-8">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.is_account_verify')}}<span class="text-danger">*</span></label>
                                            <div class="radio-group">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="is_account_verify_no" name="is_account_verify" class="custom-control-input" value="0" {{ $data['is_account_verify'] == 0 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="is_account_verify_no">{{__('label.no')}}</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="is_account_verify_yes" name="is_account_verify" class="custom-control-input" value="1" {{ $data['is_account_verify'] == 1 ? "checked" : "" }}>
                                                    <label class="custom-control-label" for="is_account_verify_yes">{{__('label.yes')}}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row bank_info">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.bank_name')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="bank_name" value="{{ $data['bank_name'] }}" class="form-control" placeholder="{{__('label.bank_name_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('label.bank_address')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="bank_address" value="{{ $data['bank_address'] }}" class="form-control" placeholder="{{__('label.bank_address_here')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row bank_info">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.account_no')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="account_no" value="{{ $data['account_no'] }}" class="form-control" placeholder="{{__('label.account_no_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.ifsc_no')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="ifsc_no" value="{{ $data['ifsc_no'] }}" class="form-control" placeholder="{{__('label.ifsc_no_here')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('label.bank_code')}}<span class="text-danger">*</span></label>
                                            <input type="text" name="bank_code" value="{{ $data['bank_code'] }}" class="form-control" placeholder="{{__('label.bank_code_here')}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 bank_info">
                                <div class="form-group">
                                    <label>{{__('label.id_proof_front')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' name="front_id_proof" id="imageUpload3" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload3" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ $data['front_id_proof'] }}" id="imagePreview3">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_front_id_proof" value="{{ $data['front_id_proof'] }}">
                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                            <div class="col-md-2 bank_info">
                                <div class="form-group">
                                    <label>{{__('label.id_proof_back')}}<span class="text-danger">*</span></label>
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' name="back_id_proof" id="imageUpload4" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload4" title="{{__('label.upload_file')}}"></label>
                                        </div>
                                        <div class="avatar-preview">
                                            <img src="{{ $data['back_id_proof'] }}" id="imagePreview4">
                                        </div>
                                    </div>
                                    <input type="hidden" name="old_back_id_proof" value="{{ $data['back_id_proof'] }}">
                                    <label class="mt-3 text-gray">{{__('label.max_size_5mb')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-default mw-120" onclick="update_profile()">{{__('label.update')}}</button>
                    <input type="hidden" name="_method" value="PATCH">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script>
        $(document).ready(function () {
            function toggleBankInfo() {
                if ($('#is_account_verify_yes').is(':checked')) {
                    $('.bank_info').show();
                } else {
                    $('.bank_info').hide();
                }
            }

            // Initial toggle on page load
            toggleBankInfo();

            // Toggle on radio button change
            $('input[name="is_account_verify"]').change(function () {
                toggleBankInfo();
            });
        });

        function update_profile() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#profile")[0]);
                $.ajax({
                    type: 'POST',
					url:'{{ route("user.profile.update", [ $data->id ]) }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'profile', '{{ route("user.profile.index") }}');
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