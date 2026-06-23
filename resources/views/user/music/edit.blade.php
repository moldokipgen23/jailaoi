@extends('user.layout.page-app')
@section('page_title', __('label.edit_music'))
@section('tab_title', __('label.edit_music'))

@section('content')
    @include('user.layout.sidebar')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <style>
        .je { max-width: 860px; margin: 0 auto; }
        .je-card { border-radius: 14px !important; padding: 24px !important; margin-bottom: 16px !important; }
        .je-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px;
                    color: var(--primary-color, #4caf50); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .je-lbl { font-size: 12px; font-weight: 600; color: #999; margin-bottom: 6px; display: block; }
        .je-lbl .req { color: #f66; }

        /* Audio replace zone */
        .je-audio-current {
            background: rgba(255,255,255,.04); border-radius: 10px; padding: 12px 16px;
            margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 10px;
        }
        .je-drop {
            border: 2px dashed #363636; border-radius: 12px; padding: 28px 20px;
            text-align: center; cursor: pointer; position: relative; overflow: hidden;
            transition: border-color .22s, background .22s;
        }
        .je-drop:hover, .je-drop.dg { border-color: var(--primary-color,#4caf50); background: rgba(76,175,80,.05); }
        .je-drop input[type="file"] { position: absolute; inset: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2; }

        /* Uploading / done states */
        .je-uploading { display: none; text-align: center; padding: 22px 0; }
        .je-uploaded  { display: none; text-align: center; padding: 16px 0; }

        /* Image zones */
        .je-imgz {
            border: 2px dashed #363636; border-radius: 12px; cursor: pointer; overflow: hidden;
            position: relative; transition: border-color .2s; display: flex; align-items: center; justify-content: center;
        }
        .je-imgz:hover { border-color: var(--primary-color, #4caf50); }
        .je-imgz img.je-prev { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
        .je-imgz input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 3; }
        .je-imgz .je-ie {
            position: absolute; bottom: 8px; right: 8px; z-index: 4;
            background: rgba(0,0,0,.75); color: #fff; border-radius: 6px; padding: 3px 9px; font-size: 11px;
        }
        .je-sq  { aspect-ratio: 1; }
        .je-169 { aspect-ratio: 16/9; }

        /* Toggles */
        .je-togs { display: flex; gap: 28px; flex-wrap: wrap; }
        .je-tog  { display: flex; flex-direction: column; align-items: center; gap: 7px; }
        .je-tlbl { font-size: 12px; color: #999; }
        .je-sw   { position: relative; display: inline-block; width: 46px; height: 26px; }
        .je-sw input { opacity: 0; width: 0; height: 0; }
        .je-sl { position: absolute; inset: 0; background: #2d2d2d; border-radius: 26px; cursor: pointer; transition: .28s; }
        .je-sl:before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .28s; }
        input:checked + .je-sl { background: var(--primary-color, #4caf50); }
        input:checked + .je-sl:before { transform: translateX(20px); }

        /* Save bar */
        .je-savebar { display: flex; align-items: center; gap: 10px; margin-top: 8px; padding-bottom: 32px; }
        .je-btn-save {
            background: var(--primary-color, #4caf50); color: #fff; border: none; border-radius: 25px;
            padding: 12px 32px; font-size: 14px; font-weight: 700; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; transition: all .22s;
        }
        .je-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(76,175,80,.3); }
        .je-btn-save:disabled { opacity: .6; cursor: not-allowed; transform: none; box-shadow: none; }
        .je-bcancel { color: #777; border: 1px solid #2d2d2d; border-radius: 25px; padding: 10px 20px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; }
        .je-bcancel:hover { color: #ccc; text-decoration: none; }
        .je-saving-bar { display: none; flex: 1; }
        .je-pb { height: 6px; background: rgba(255,255,255,.07); border-radius: 6px; overflow: hidden; }
        .je-pf { height: 100%; width: 0; background: var(--primary-color, #4caf50); border-radius: 6px; transition: width .3s; }
        .je-pct { font-size: 11px; color: #777; margin-top: 4px; }
    </style>

    <div class="right-content">
        @include('user.layout.header')
        <div class="body-content">
            <h1 class="page-title-sm">{{__('label.edit_music')}}</h1>

            <div class="border-bottom row mb-4">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.music.index') }}">{{__('label.music')}}</a></li>
                        <li class="breadcrumb-item active">{{__('label.edit_music')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.music.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.music_list')}}</a>
                </div>
            </div>

            <form id="music" enctype="multipart/form-data">
                <input type="hidden" name="id"                             value="{{ $data['id'] }}">
                <input type="hidden" name="old_hashtag_id"                 value="{{ $data['hashtag_id'] }}">
                <input type="hidden" name="old_portrait_img"               value="{{ $data['portrait_img'] }}">
                <input type="hidden" name="old_landscape_img"              value="{{ $data['landscape_img'] }}">
                <input type="hidden" name="old_content"                    value="{{ $data['content'] }}">
                <input type="hidden" name="old_content_upload_type"        value="{{ $data['content_upload_type'] }}">
                <input type="hidden" name="old_portrait_img_storage_type"  value="{{ $data['portrait_img_storage_type'] }}">
                <input type="hidden" name="old_landscape_img_storage_type" value="{{ $data['landscape_img_storage_type'] }}">
                <input type="hidden" name="old_content_storage_type"       value="{{ $data['content_storage_type'] }}">
                <input type="hidden" name="content_upload_type" id="content_upload_type" value="server_video">
                <input type="hidden" name="music" id="uploadedFilename">
                <input type="hidden" name="_method" value="PATCH">

                <div class="je">

                    {{-- ── Track Details ──────────────────────────── --}}
                    <div class="card custom-border-card je-card">
                        <div class="je-title"><i class="fa-solid fa-music"></i> Track Details</div>

                        <div class="form-group">
                            <label class="je-lbl">{{__('label.title')}} <span class="req">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ $data['title'] }}"
                                   placeholder="{{__('label.title_here')}}" autofocus>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="je-lbl">{{__('label.description')}}</label>
                                    <textarea name="description" class="form-control" rows="4"
                                              placeholder="{{__('label.description_here')}}">{{ $data['description'] }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="je-lbl">Lyrics</label>
                                    <textarea name="lyrics" class="form-control" rows="4"
                                              placeholder="Add lyrics here...">{{ $data['lyrics'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="je-lbl">{{__('label.category')}} <span class="req">*</span></label>
                                    <select name="category_id" id="category_id" class="form-control" style="width:100%!important">
                                        <option value="">{{__('label.select_category')}}</option>
                                        @foreach($category as $v)
                                            <option value="{{$v['id']}}" {{ $data['category_id'] == $v['id'] ? 'selected' : '' }}>{{$v['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="je-lbl">{{__('label.language')}} <span class="req">*</span></label>
                                    <select name="language_id" id="language_id" class="form-control" style="width:100%!important">
                                        <option value="">{{__('label.select_language')}}</option>
                                        @foreach($language as $v)
                                            <option value="{{$v['id']}}" {{ $data['language_id'] == $v['id'] ? 'selected' : '' }}>{{$v['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="je-lbl">Audio Duration <span class="req">*</span></label>
                                    <input type="text" id="timePicker" name="content_duration" class="form-control"
                                           placeholder="HH:MM:SS" style="width:170px">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Audio ──────────────────────────────────── --}}
                    <div class="card custom-border-card je-card">
                        <div class="je-title"><i class="fa-solid fa-cloud-arrow-up"></i> Audio File</div>

                        <div class="je-audio-current">
                            <i class="fa-solid fa-file-audio" style="color:var(--primary-color,#4caf50);font-size:18px"></i>
                            <div>
                                <div style="font-weight:600">{{ basename($data['content']) }}</div>
                                <div style="color:#777;font-size:12px">Leave blank to keep this file</div>
                            </div>
                        </div>

                        {{-- Idle drop zone --}}
                        <div class="je-drop" id="je-drop">
                            <input type="file" id="audioFileInput" accept=".mp3,.m4a,.aac,.flac,.wav,.ogg">
                            <i class="fa-solid fa-arrow-up-from-bracket" style="font-size:26px;color:#444;display:block;margin-bottom:10px"></i>
                            <div style="font-size:14px;font-weight:600">Drop new file to replace</div>
                            <div style="font-size:12px;color:#555;margin-top:4px">or click to browse &nbsp;·&nbsp; MP3 · M4A · WAV · FLAC · OGG · AAC</div>
                        </div>

                        {{-- Uploading --}}
                        <div class="je-uploading" id="je-uploading">
                            <div style="font-weight:600;margin-bottom:12px;font-size:14px" id="je-upname"></div>
                            <div style="background:rgba(255,255,255,.1);border-radius:99px;overflow:hidden;height:8px;max-width:420px;margin:0 auto 8px">
                                <div id="je-upbar" style="height:100%;width:0%;background:var(--primary-color,#4caf50);border-radius:99px;transition:width .2s"></div>
                            </div>
                            <div id="je-uppct" style="font-size:12px;opacity:.6">Uploading…</div>
                        </div>

                        {{-- Done --}}
                        <div class="je-uploaded" id="je-uploaded">
                            <i class="fa-solid fa-circle-check" style="font-size:30px;color:var(--primary-color,#4caf50)"></i>
                            <div style="font-weight:600;margin:8px 0 3px;font-size:14px" id="je-updone"></div>
                            <div style="font-size:12px;opacity:.6">
                                New file ready &nbsp;·&nbsp;
                                <a href="#" id="je-reupload" style="color:var(--primary-color,#4caf50)">Choose different file</a>
                            </div>
                        </div>
                    </div>

                    {{-- ── Cover Artwork ───────────────────────────── --}}
                    <div class="card custom-border-card je-card">
                        <div class="je-title"><i class="fa-solid fa-image"></i> Cover Artwork</div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="je-lbl mb-2">Portrait Cover <small class="text-muted">(max 5MB)</small></label>
                                <div class="je-imgz je-sq">
                                    <img class="je-prev" id="imagePreview1" src="{{ $data['portrait_img'] }}" alt="">
                                    <div class="je-ie"><i class="fa-solid fa-pencil fa-xs"></i> Change</div>
                                    <input type="file" name="portrait_img" id="imageUpload1" accept=".png,.jpg,.jpeg,.webp">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="je-lbl mb-2">Landscape Cover <small class="text-muted">(16:9 · max 5MB)</small></label>
                                <div class="je-imgz je-169">
                                    <img class="je-prev" id="imagePreview2" src="{{ $data['landscape_img'] }}" alt="">
                                    <div class="je-ie"><i class="fa-solid fa-pencil fa-xs"></i> Change</div>
                                    <input type="file" name="landscape_img" id="imageUpload2" accept=".png,.jpg,.jpeg,.webp">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Permissions ─────────────────────────────── --}}
                    <div class="card custom-border-card je-card">
                        <div class="je-title"><i class="fa-solid fa-sliders"></i> Permissions</div>
                        <div class="je-togs">
                            <div class="je-tog">
                                <div class="je-tlbl"><i class="fa-regular fa-comment"></i> &nbsp;Comments</div>
                                <label class="je-sw">
                                    <input type="checkbox" id="sw-c" {{ $data['is_comment'] == 1 ? 'checked' : '' }} onchange="jeSetR('is_comment',this.checked)">
                                    <span class="je-sl"></span>
                                </label>
                            </div>
                            <div class="je-tog">
                                <div class="je-tlbl"><i class="fa-solid fa-download"></i> &nbsp;Downloads</div>
                                <label class="je-sw">
                                    <input type="checkbox" id="sw-d" {{ $data['is_download'] == 1 ? 'checked' : '' }} onchange="jeSetR('is_download',this.checked)">
                                    <span class="je-sl"></span>
                                </label>
                            </div>
                            <div class="je-tog">
                                <div class="je-tlbl"><i class="fa-regular fa-heart"></i> &nbsp;Likes</div>
                                <label class="je-sw">
                                    <input type="checkbox" id="sw-l" {{ $data['is_like'] == 1 ? 'checked' : '' }} onchange="jeSetR('is_like',this.checked)">
                                    <span class="je-sl"></span>
                                </label>
                            </div>
                        </div>
                        <div style="display:none">
                            <input type="radio" name="is_comment"  id="is_comment_yes"  value="1" {{ $data['is_comment']  == 1 ? 'checked' : '' }}>
                            <input type="radio" name="is_comment"  id="is_comment_no"   value="0" {{ $data['is_comment']  == 0 ? 'checked' : '' }}>
                            <input type="radio" name="is_download" id="is_download_yes" value="1" {{ $data['is_download'] == 1 ? 'checked' : '' }}>
                            <input type="radio" name="is_download" id="is_download_no"  value="0" {{ $data['is_download'] == 0 ? 'checked' : '' }}>
                            <input type="radio" name="is_like"     id="is_like_yes"     value="1" {{ $data['is_like']     == 1 ? 'checked' : '' }}>
                            <input type="radio" name="is_like"     id="is_like_no"      value="0" {{ $data['is_like']     == 0 ? 'checked' : '' }}>
                        </div>
                    </div>

                    {{-- ── Save bar ────────────────────────────────── --}}
                    <div class="je-savebar" id="je-savebar">
                        <a href="{{ route('user.music.index') }}" class="je-bcancel">Cancel</a>
                        <div style="flex:1"></div>
                        <div class="je-saving-bar" id="je-saving-bar">
                            <div class="je-pb"><div class="je-pf" id="je-pbar"></div></div>
                            <div class="je-pct" id="je-pct">Saving…</div>
                        </div>
                        <button type="button" class="je-btn-save" id="je-savebtn" onclick="save_music()">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                        </button>
                    </div>

                </div>{{-- /je --}}
            </form>
        </div>
    </div>
@endsection

@section('pagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('/assets/js/plupload.full.min.js')}}"></script>
    <script src="{{ asset('/assets/js/common.js')}}"></script>

    <script>
        $('#category_id').select2();
        $('#language_id').select2();

        // Pre-fill duration from existing data
        var duration = '<?php echo $data['content_duration']; ?>';
        var hours   = msToHours(duration);
        var minutes = msToMinutes(duration);
        var seconds = msToSeconds(duration);
        var dDate   = new Date(); dDate.setHours(hours, minutes, seconds);
        $('#timePicker').datetimepicker({
            useCurrent: false, format: 'HH:mm:ss', defaultDate: dDate, showClose: true, showTodayButton: true,
            icons: { up:'fa fa-chevron-up', down:'fa fa-chevron-down', today:'fa fa-clock fa-regular', close:'fa fa-times' }
        });

        $(document).ready(function(){
            // Drag-drop
            var dz = document.getElementById('je-drop');
            if(dz){
                dz.addEventListener('dragover',  function(e){ e.preventDefault(); this.classList.add('dg'); });
                dz.addEventListener('dragleave', function()  { this.classList.remove('dg'); });
                dz.addEventListener('drop', function(e){
                    e.preventDefault(); this.classList.remove('dg');
                    if(e.dataTransfer.files.length){
                        var dt = new DataTransfer(); dt.items.add(e.dataTransfer.files[0]);
                        document.getElementById('audioFileInput').files = dt.files;
                        $('#audioFileInput').trigger('change');
                    }
                });
            }

            $('#audioFileInput').on('change', function(){
                var f = this.files[0]; if(!f) return;
                jeDetectDur(f);
                jeStartUpload(f);
            });

            $('#je-reupload').on('click', function(e){
                e.preventDefault();
                $('#je-uploaded').hide();
                $('#je-drop').show();
                $('#uploadedFilename').val('');
                document.getElementById('audioFileInput').value = '';
            });

            $('#imageUpload1').on('change', function(){ jeImgPrev(this, 'imagePreview1'); });
            $('#imageUpload2').on('change', function(){ jeImgPrev(this, 'imagePreview2'); });
        });

        function jeStartUpload(file){
            $('#je-drop').hide();
            $('#je-uploaded').hide();
            $('#je-upname').text(file.name);
            $('#je-upbar').css('width', '0%');
            $('#je-uppct').text('Uploading…');
            $('#je-uploading').show();
            $('#je-savebtn').prop('disabled', true);

            var fd = new FormData();
            fd.append('audio', file);
            fd.append('_token', '{{ csrf_token() }}');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("user.music.upload.audio") }}', true);
            xhr.upload.onprogress = function(e){
                if(e.lengthComputable){
                    var pct = Math.round(e.loaded / e.total * 100);
                    $('#je-upbar').css('width', pct + '%');
                    $('#je-uppct').text(pct + '% uploaded…');
                }
            };
            xhr.onload = function(){
                $('#je-uploading').hide();
                $('#je-savebtn').prop('disabled', false);
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if(resp.status === 200){
                        $('#uploadedFilename').val(resp.filename);
                        $('#je-updone').text(file.name);
                        $('#je-uploaded').show();
                    } else {
                        var err = Array.isArray(resp.errors) ? resp.errors.join(', ') : resp.errors;
                        toastr.error(err || 'Upload failed');
                        $('#je-drop').show();
                    }
                } catch(ex){
                    toastr.error('Upload failed');
                    $('#je-drop').show();
                }
            };
            xhr.onerror = function(){
                $('#je-uploading').hide();
                $('#je-savebtn').prop('disabled', false);
                toastr.error('Upload failed — check your connection');
                $('#je-drop').show();
            };
            xhr.send(fd);
        }

        function jeDetectDur(file){
            var a = document.createElement('audio'); a.preload = 'metadata';
            a.onloadedmetadata = function(){
                URL.revokeObjectURL(a.src);
                var s  = Math.floor(a.duration), h = Math.floor(s/3600), m = Math.floor((s%3600)/60), ss = s%60;
                var fmt = (h > 0 ? String(h).padStart(2,'0') + ':' : '') + String(m).padStart(2,'0') + ':' + String(ss).padStart(2,'0');
                $('#timePicker').val(fmt);
            };
            a.src = URL.createObjectURL(file);
        }

        function jeImgPrev(input, imgId){
            if(!input.files || !input.files[0]) return;
            var r = new FileReader();
            r.onload = function(e){ document.getElementById(imgId).src = e.target.result; };
            r.readAsDataURL(input.files[0]);
        }

        function jeSetR(name, val){
            document.querySelector('input[name="'+name+'"][value="'+(val?1:0)+'"]').checked = true;
        }

        function save_music(){
            var title = $('[name="title"]').val().trim();
            if(!title){ toastr.warning('Title is required'); $('[name="title"]').focus(); return; }
            if(!$('[name="category_id"]').val()){ toastr.warning('Please select a category'); return; }
            if(!$('[name="language_id"]').val()){ toastr.warning('Please select a language'); return; }

            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){
                $('#je-savebtn').prop('disabled', true);
                $('#je-saving-bar').show();
                var formData = new FormData($('#music')[0]);
                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'POST', url: '{{route("user.music.update", [$data['id']])}}',
                    data: formData, cache: false, contentType: false, processData: false,
                    xhr: function(){
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e){
                            if(e.lengthComputable){
                                var pct = Math.round(e.loaded / e.total * 100);
                                $('#je-pbar').css('width', pct + '%');
                                $('#je-pct').text(pct + '% saved…');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(resp){
                        $('#je-saving-bar').hide();
                        $('#je-savebtn').prop('disabled', false);
                        get_responce_message(resp, 'music', '{{ route("user.music.index") }}');
                    },
                    error: function(xhr, textStatus, errorThrown){
                        $('#je-saving-bar').hide();
                        $('#je-savebtn').prop('disabled', false);
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
    </script>
@endsection
