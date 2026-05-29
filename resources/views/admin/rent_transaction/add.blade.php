@extends('admin.layout.page-app')
@section('page_title', __('label.add_rent_transaction'))
@section('tab_title', __('label.add_rent_transaction'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <!-- Select2 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.add_rent_transaction')}}</h1>

            <div class="border-bottom row mb-3">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.renttransaction.index') }}">{{__('label.rent_transactions')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('label.add_rent_transaction')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('admin.renttransaction.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.rent_transactions_list')}}</a>
                </div>
            </div>

            <div class="card custom-border-card mt-3">
                <form id="search_user" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="col-8">
                            <div class="form-group">
                                <input name="name" type="text" class="form-control" id="name" placeholder="{{__('label.search')}}" autocomplete="off" autofocus>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-default mw-120 mr-3" onclick="search_user()">{{__('label.search')}}</button>
                            <a href="{{route('admin.renttransaction.create')}}" class="btn btn-cancel mw-120">{{__('label.clear')}}</a>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </div>
                </form>
            </div>

            <?php if (isset($user->id)) { ?>
                <div class="card custom-border-card mt-3">
                    <form id="add_transaction" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="{{ $user['id'] }}" class="form-control" readonly>
                        <div class="form-row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>{{__('label.channel_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="channel_name" value="{{ $user['channel_name'] }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__('label.full_name')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" value="{{ $user['full_name'] }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__('label.email')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="email" value="{{ $user['email']}}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label>{{__('label.mobile_number')}}<span class="text-danger">*</span></label>
                                    <input type="text" name="mobile_number" value="{{ $user['mobile_number'] }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__('label.content')}}<span class="text-danger">*</span></label>
                                    <select name="content_id" class="form-control" id="content_id">
                                        <option value="">{{__('label.select_content')}}</option>
                                        @foreach($content as $row)
                                            <option value="{{ $row['id'] }}">
                                                {{ $row['title'] }} — {{ $row['rent_price'] }} {{ Currency_Code(); }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-default mw-120" onclick="add_transaction()">{{__('label.save')}}</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            <?php } else { ?>
                <div class="card custom-border-card mt-3">
                    <div class="col-12">
                        <h3>{{__('label.users_list')}}</h3>
                        <div id="user_list"></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        // Sidebar Scroll Down
		sidebar_down(850);

        $("#content_id").select2();

        function add_transaction() {

            var Demo_Mode = '<?php echo Demo_Mode(); ?>';
            if (Demo_Mode == 1) {

                $("#dvloader").show();
                var formData = new FormData($("#add_transaction")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.renttransaction.store") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#dvloader").hide();
                        get_responce_message(resp, 'add_transaction', '{{ route("admin.renttransaction.index") }}');
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

        function search_user() {
            var formData = new FormData($("#search_user")[0]);
            $("#dvloader").show();
            $.ajax({
                type: 'POST',
                url: '{{ route("admin.rent.search_user")}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(resp) {
                    $("#dvloader").hide();
                    $('#user_list').html(resp.result);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#dvloader").hide();
                    toastr.error(errorThrown, textStatus);
                }
            });
        }
    </script>
@endsection