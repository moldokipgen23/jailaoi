@extends('user.layout.page-app')
@section('page_title', __('label.add_music'))
@section('tab_title', __('label.add_music'))

@section('content')
    @include('user.layout.sidebar')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <style>
        /* ── Jailaoi Upload Wizard ─────────────────────────── */
        .jlw { max-width: 840px; margin: 0 auto; }

        /* Step bar */
        .jlw-steps { display:flex; align-items:flex-start; margin-bottom:32px; }
        .jlw-step  { display:flex; flex-direction:column; align-items:center; flex:1; }
        .jlw-c {
            width:34px; height:34px; border-radius:50%; border:2px solid #383838;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:700; color:#555; background:transparent; transition:all .3s;
        }
        .jlw-c.active { border-color:var(--primary-color,#4caf50); background:var(--primary-color,#4caf50); color:#fff; }
        .jlw-c.done   { border-color:var(--primary-color,#4caf50); color:var(--primary-color,#4caf50); }
        .jlw-l { font-size:11px; color:#555; margin-top:5px; text-align:center; white-space:nowrap; }
        .jlw-l.active { color:var(--primary-color,#4caf50); font-weight:600; }
        .jlw-line { flex:1; height:2px; margin:17px -2px 0; background:#2d2d2d; transition:background .3s; }
        .jlw-line.done { background:var(--primary-color,#4caf50); }

        /* Panels */
        .jlw-panel { display:none; animation:jlIn .28s ease; }
        .jlw-panel.active { display:block; }
        @keyframes jlIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

        /* Card override */
        .jlw-card { border-radius:14px !important; padding:24px !important; margin-bottom:14px !important; }
        .jlw-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px;
                     color:var(--primary-color,#4caf50); margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .jlw-lbl  { font-size:12px; font-weight:600; color:#999; margin-bottom:6px; display:block; }
        .jlw-lbl .req { color:#f66; }

        /* Audio drop zone */
        .jlw-drop {
            border:2px dashed #363636; border-radius:14px; padding:44px 20px;
            text-align:center; cursor:pointer; position:relative; overflow:hidden;
            transition:border-color .22s, background .22s;
        }
        .jlw-drop:hover, .jlw-drop.dg {
            border-color:var(--primary-color,#4caf50);
            background:rgba(76,175,80,.05);
        }
        .jlw-drop input[type="file"] {
            position:absolute; inset:0; opacity:0; width:100%; height:100%; cursor:pointer; z-index:2;
        }
        .jlw-drop .di { font-size:38px; display:block; margin-bottom:12px; }
        .jlw-drop .dt { font-size:16px; font-weight:600; }
        .jlw-drop .ds { font-size:12px; color:#5a5a5a; margin-top:5px; }
        .jlw-fname { font-size:13px; color:var(--primary-color,#4caf50); font-weight:600; margin-top:12px; display:none; word-break:break-all; }

        /* Waveform */
        .jlw-wave { display:flex; align-items:flex-end; justify-content:center; gap:3px; height:28px; margin:14px auto 0; width:84px; }
        .jlw-wb   { width:4px; border-radius:2px; background:var(--primary-color,#4caf50); opacity:.2; height:4px; }
        .jlw-wave.on .jlw-wb { opacity:1; animation:wv .9s ease-in-out infinite; }
        .jlw-wb:nth-child(1){animation-delay:.00s}
        .jlw-wb:nth-child(2){animation-delay:.10s}
        .jlw-wb:nth-child(3){animation-delay:.20s}
        .jlw-wb:nth-child(4){animation-delay:.15s}
        .jlw-wb:nth-child(5){animation-delay:.05s}
        .jlw-wb:nth-child(6){animation-delay:.25s}
        .jlw-wb:nth-child(7){animation-delay:.12s}
        @keyframes wv{0%,100%{height:3px}50%{height:22px}}

        /* Duration badge */
        .jlw-dur { display:none; margin-top:14px; }
        .jlw-dur-b {
            display:inline-flex; align-items:center; gap:7px;
            background:rgba(76,175,80,.1); border:1px solid var(--primary-color,#4caf50);
            color:var(--primary-color,#4caf50); border-radius:20px; padding:5px 14px; font-size:13px; font-weight:600;
        }

        /* Local plupload zone */
        .jlw-pzone { border:2px dashed #363636; border-radius:14px; padding:36px 20px; text-align:center; }
        .jlw-pzone #filelist1 { font-size:12px; color:#888; min-height:16px; margin:8px 0; }
        .jlw-pbtn {
            display:inline-flex; align-items:center; gap:7px;
            background:var(--primary-color,#4caf50); color:#fff;
            border-radius:22px; padding:9px 22px; font-size:13px; font-weight:600; cursor:pointer; margin-top:12px;
        }

        /* Image zones */
        .jlw-imgz {
            border:2px dashed #363636; border-radius:12px; cursor:pointer; overflow:hidden;
            position:relative; transition:border-color .2s; display:flex; align-items:center; justify-content:center;
        }
        .jlw-imgz:hover { border-color:var(--primary-color,#4caf50); }
        .jlw-imgz .jlw-iov { text-align:center; padding:28px 12px; pointer-events:none; }
        .jlw-imgz .jlw-iov i { font-size:28px; color:#3a3a3a; display:block; margin-bottom:8px; }
        .jlw-imgz .jlw-iov p { font-size:12px; color:#555; margin:0; line-height:1.5; }
        .jlw-imgz img.jlw-prev { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; z-index:1; }
        .jlw-imgz input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; z-index:3; }
        .jlw-imgz .jlw-ie {
            position:absolute; bottom:8px; right:8px; z-index:4;
            background:rgba(0,0,0,.75); color:#fff; border-radius:6px; padding:3px 9px; font-size:11px; display:none;
        }
        .jlw-imgz.has .jlw-iov { display:none; }
        .jlw-imgz.has img.jlw-prev { display:block; }
        .jlw-imgz.has .jlw-ie   { display:block; }
        .jlw-sq  { aspect-ratio:1; }
        .jlw-169 { aspect-ratio:16/9; }

        /* Toggles */
        .jlw-togs { display:flex; gap:28px; flex-wrap:wrap; }
        .jlw-tog  { display:flex; flex-direction:column; align-items:center; gap:7px; }
        .jlw-tlbl { font-size:12px; color:#999; }
        .jlw-sw   { position:relative; display:inline-block; width:46px; height:26px; }
        .jlw-sw input { opacity:0; width:0; height:0; }
        .jlw-sl { position:absolute; inset:0; background:#2d2d2d; border-radius:26px; cursor:pointer; transition:.28s; }
        .jlw-sl:before { content:''; position:absolute; width:20px; height:20px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.28s; }
        input:checked + .jlw-sl { background:var(--primary-color,#4caf50); }
        input:checked + .jlw-sl:before { transform:translateX(20px); }

        /* Review */
        .jlw-review { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .jlw-ri  { background:rgba(255,255,255,.04); border-radius:8px; padding:10px 14px; }
        .jlw-rl  { font-size:10px; color:#555; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
        .jlw-rv  { font-size:13px; font-weight:500; }
        .jlw-rv.ok { color:var(--primary-color,#4caf50); }

        /* Save progress */
        .jlw-saving { display:none; padding:20px; margin-bottom:14px; }
        .jlw-pb { height:7px; background:rgba(255,255,255,.06); border-radius:7px; overflow:hidden; margin:10px 0 6px; }
        .jlw-pf { height:100%; width:0; background:var(--primary-color,#4caf50); border-radius:7px; transition:width .3s; }

        /* Nav */
        .jlw-nav { display:flex; align-items:center; gap:10px; margin-top:18px; }
        .jlw-sp  { flex:1; }
        .jlw-bnext { background:var(--primary-color,#4caf50); color:#fff; border:none; border-radius:25px; padding:11px 26px; font-size:13px; font-weight:700; cursor:pointer; transition:opacity .2s; }
        .jlw-bnext:hover { opacity:.85; }
        .jlw-bprev { background:transparent; color:#999; border:1px solid #363636; border-radius:25px; padding:10px 20px; font-size:13px; cursor:pointer; transition:all .2s; }
        .jlw-bprev:hover { border-color:#666; color:#ccc; }
        .jlw-bpub {
            background:var(--primary-color,#4caf50); color:#fff; border:none; border-radius:25px;
            padding:13px 34px; font-size:14px; font-weight:700; cursor:pointer;
            display:inline-flex; align-items:center; gap:8px; transition:all .22s;
        }
        .jlw-bpub:hover { transform:translateY(-2px); box-shadow:0 6px 22px rgba(76,175,80,.32); }
        .jlw-bcancel { color:#777; border:1px solid #2d2d2d; border-radius:25px; padding:10px 18px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; }
        .jlw-bcancel:hover { color:#ccc; text-decoration:none; }

        /* Validation */
        .jlw-err { font-size:11px; color:#f55; margin-top:4px; display:none; }
        .has-jlerr .form-control,
        .has-jlerr .select2-container--default .select2-selection--single { border-color:#f55 !important; }
        .has-jlerr .jlw-err { display:block; }
        .has-jlerr .jlw-drop { border-color:#f55; }
    </style>

    <div class="right-content">
        @include('user.layout.header')
        <div class="body-content">
            <h1 class="page-title-sm">{{__('label.add_music')}}</h1>

            <div class="border-bottom row mb-4">
                <div class="col-sm-10">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">{{__('label.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.music.index') }}">{{__('label.music')}}</a></li>
                        <li class="breadcrumb-item active">{{__('label.add_music')}}</li>
                    </ol>
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-end">
                    <a href="{{ route('user.music.index') }}" class="btn btn-default mw-120" style="margin-top:-14px">{{__('label.music_list')}}</a>
                </div>
            </div>

            <form id="music" enctype="multipart/form-data">
                <input type="hidden" name="id" value="">
                <input type="hidden" name="content_upload_type" id="content_upload_type" value="server_video">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="jlw">

                    {{-- Step indicator --}}
                    <div class="jlw-steps">
                        <div class="jlw-step">
                            <div class="jlw-c active" id="jlc1">1</div>
                            <div class="jlw-l active" id="jll1">Track Details</div>
                        </div>
                        <div class="jlw-line" id="jlln1"></div>
                        <div class="jlw-step">
                            <div class="jlw-c" id="jlc2">2</div>
                            <div class="jlw-l" id="jll2">Upload Audio</div>
                        </div>
                        <div class="jlw-line" id="jlln2"></div>
                        <div class="jlw-step">
                            <div class="jlw-c" id="jlc3">3</div>
                            <div class="jlw-l" id="jll3">Artwork & Publish</div>
                        </div>
                    </div>

                    {{-- ── STEP 1: Track Details ───────────────── --}}
                    <div class="jlw-panel active" id="jlp1">
                        <div class="card custom-border-card jlw-card">
                            <div class="jlw-title"><i class="fa-solid fa-music"></i> Track Details</div>

                            <div class="form-group" id="jlf-title">
                                <label class="jlw-lbl">{{__('label.title')}} <span class="req">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Give your track a name..." autofocus autocomplete="off">
                                <div class="jlw-err">Title is required</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="jlw-lbl">{{__('label.description')}}</label>
                                        <textarea name="description" class="form-control" rows="5" placeholder="Tell listeners about this track..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="jlw-lbl">Lyrics</label>
                                        <textarea name="lyrics" class="form-control" rows="5" placeholder="Add lyrics here..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" id="jlf-cat">
                                        <label class="jlw-lbl">{{__('label.category')}} <span class="req">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control" style="width:100%!important">
                                            <option value="">{{__('label.select_category')}}</option>
                                            @foreach($category as $v)
                                                <option value="{{$v['id']}}">{{$v['name']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="jlw-err">Please select a category</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="jlf-lang">
                                        <label class="jlw-lbl">{{__('label.language')}} <span class="req">*</span></label>
                                        <select name="language_id" id="language_id" class="form-control" style="width:100%!important">
                                            <option value="">{{__('label.select_language')}}</option>
                                            @foreach($language as $v)
                                                <option value="{{$v['id']}}">{{$v['name']}}</option>
                                            @endforeach
                                        </select>
                                        <div class="jlw-err">Please select a language</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="jlw-lbl">Album</label>
                                        <select name="album_id" class="form-control">
                                            <option value="">None</option>
                                            @php $albums = \App\Models\Album::where('status',1)->get(); @endphp
                                            @foreach($albums as $album)
                                                <option value="{{ $album->id }}">{{ $album->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="jlw-nav">
                            <a href="{{ route('user.music.index') }}" class="jlw-bcancel">Cancel</a>
                            <div class="jlw-sp"></div>
                            <button type="button" class="jlw-bnext" onclick="jlNext(1)">
                                Next: Upload Audio &nbsp;<i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── STEP 2: Upload Audio ────────────────── --}}
                    <div class="jlw-panel" id="jlp2">
                        <div class="card custom-border-card jlw-card">
                            <div class="jlw-title"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Audio File</div>

                            {{-- Hidden field stores pre-uploaded filename returned by uploadAudio() --}}
                            <input type="hidden" name="music" id="uploadedFilename">

                            <div id="jlf-audio">
                                {{-- Idle: drag-drop / click to pick --}}
                                <div class="jlw-drop" id="jl-drop">
                                    <input type="file" id="audioFileInput" accept=".mp3,.m4a,.aac,.flac,.wav,.ogg">
                                    <span class="di" id="jl-di">🎵</span>
                                    <div class="dt" id="jl-dt">Drag &amp; drop your audio file here</div>
                                    <div class="ds" id="jl-ds">or click to browse &nbsp;·&nbsp; MP3 · M4A · WAV · FLAC · OGG · AAC</div>
                                    <div class="jlw-wave" id="jl-wave">
                                        <div class="jlw-wb"></div><div class="jlw-wb"></div><div class="jlw-wb"></div>
                                        <div class="jlw-wb"></div><div class="jlw-wb"></div><div class="jlw-wb"></div>
                                        <div class="jlw-wb"></div>
                                    </div>
                                    <div class="jlw-fname" id="jl-aname"></div>
                                </div>

                                {{-- Uploading state --}}
                                <div id="jl-uploading" style="display:none;text-align:center;padding:28px 0">
                                    <div style="font-weight:600;margin-bottom:14px;font-size:15px" id="jl-upname"></div>
                                    <div style="background:rgba(255,255,255,.1);border-radius:99px;overflow:hidden;height:10px;max-width:420px;margin:0 auto 10px">
                                        <div id="jl-upbar" style="height:100%;width:0%;background:var(--primary-color,#4caf50);border-radius:99px;transition:width .2s"></div>
                                    </div>
                                    <div id="jl-uppct" style="font-size:13px;opacity:.7">Uploading…</div>
                                </div>

                                {{-- Done state --}}
                                <div id="jl-uploaded" style="display:none;text-align:center;padding:22px 0">
                                    <div style="font-size:36px;color:var(--primary-color,#4caf50)"><i class="fa-solid fa-circle-check"></i></div>
                                    <div style="font-weight:600;margin:8px 0 4px;font-size:15px" id="jl-updone"></div>
                                    <div style="font-size:12px;opacity:.6">Upload complete &nbsp;·&nbsp; <a href="#" id="jl-reupload" style="color:var(--primary-color,#4caf50)">Choose different file</a></div>
                                </div>

                                <div class="jlw-err" id="jl-aerr" style="margin-top:6px">Please upload an audio file to continue</div>
                            </div>

                            {{-- Duration --}}
                            <div class="mt-4" style="display:flex;align-items:center;flex-wrap:wrap;gap:16px">
                                <div>
                                    <label class="jlw-lbl">Audio Duration <span class="req">*</span></label>
                                    <input type="text" id="timePicker" name="content_duration" class="form-control" placeholder="Auto-detected from file" style="width:170px">
                                </div>
                                <div class="jlw-dur" id="jl-dur">
                                    <div class="jlw-dur-b">
                                        <i class="fa-solid fa-clock"></i>
                                        <span id="jl-durval"></span>
                                        <small style="opacity:.6">auto-detected</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="jlw-nav">
                            <button type="button" class="jlw-bprev" onclick="jlGo(1)">
                                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back
                            </button>
                            <div class="jlw-sp"></div>
                            <button type="button" class="jlw-bnext" onclick="jlNext(2)">
                                Next: Artwork &amp; Publish &nbsp;<i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── STEP 3: Artwork & Publish ───────────── --}}
                    <div class="jlw-panel" id="jlp3">

                        {{-- Summary --}}
                        <div class="card custom-border-card jlw-card">
                            <div class="jlw-title"><i class="fa-solid fa-list-check"></i> Track Summary</div>
                            <div class="jlw-review">
                                <div class="jlw-ri"><div class="jlw-rl">Title</div><div class="jlw-rv" id="rv-title">—</div></div>
                                <div class="jlw-ri"><div class="jlw-rl">Duration</div><div class="jlw-rv ok" id="rv-dur">—</div></div>
                                <div class="jlw-ri"><div class="jlw-rl">Category</div><div class="jlw-rv" id="rv-cat">—</div></div>
                                <div class="jlw-ri"><div class="jlw-rl">Language</div><div class="jlw-rv" id="rv-lang">—</div></div>
                            </div>
                        </div>

                        {{-- Artwork --}}
                        <div class="card custom-border-card jlw-card">
                            <div class="jlw-title"><i class="fa-solid fa-image"></i> Cover Artwork</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="jlw-lbl mb-2">Portrait Cover <small class="text-muted">(square · max 5MB)</small></label>
                                    <div class="jlw-imgz jlw-sq" id="jl-pz">
                                        <img class="jlw-prev" id="imagePreview1" src="{{ asset('assets/imgs/upload_img.png') }}" alt="">
                                        <div class="jlw-iov">
                                            <i class="fa-solid fa-camera"></i>
                                            <p>Click to upload<br><small>JPG · PNG · WEBP</small></p>
                                        </div>
                                        <div class="jlw-ie"><i class="fa-solid fa-pencil fa-xs"></i> Change</div>
                                        <input type="file" name="portrait_img" id="imageUpload1" accept=".png,.jpg,.jpeg,.webp">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="jlw-lbl mb-2">Landscape Cover <small class="text-muted">(16:9 · max 5MB)</small></label>
                                    <div class="jlw-imgz jlw-169" id="jl-lz">
                                        <img class="jlw-prev" id="imagePreview2" src="{{ asset('assets/imgs/upload_img.png') }}" alt="">
                                        <div class="jlw-iov">
                                            <i class="fa-solid fa-panorama"></i>
                                            <p>Click to upload<br><small>JPG · PNG · WEBP</small></p>
                                        </div>
                                        <div class="jlw-ie"><i class="fa-solid fa-pencil fa-xs"></i> Change</div>
                                        <input type="file" name="landscape_img" id="imageUpload2" accept=".png,.jpg,.jpeg,.webp">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <div class="card custom-border-card jlw-card">
                            <div class="jlw-title"><i class="fa-solid fa-sliders"></i> Permissions</div>
                            <div class="jlw-togs">
                                <div class="jlw-tog">
                                    <div class="jlw-tlbl"><i class="fa-regular fa-comment"></i> &nbsp;Comments</div>
                                    <label class="jlw-sw">
                                        <input type="checkbox" id="sw-c" checked onchange="jlSetR('is_comment',this.checked)">
                                        <span class="jlw-sl"></span>
                                    </label>
                                </div>
                                <div class="jlw-tog">
                                    <div class="jlw-tlbl"><i class="fa-solid fa-download"></i> &nbsp;Downloads</div>
                                    <label class="jlw-sw">
                                        <input type="checkbox" id="sw-d" checked onchange="jlSetR('is_download',this.checked)">
                                        <span class="jlw-sl"></span>
                                    </label>
                                </div>
                                <div class="jlw-tog">
                                    <div class="jlw-tlbl"><i class="fa-regular fa-heart"></i> &nbsp;Likes</div>
                                    <label class="jlw-sw">
                                        <input type="checkbox" id="sw-l" checked onchange="jlSetR('is_like',this.checked)">
                                        <span class="jlw-sl"></span>
                                    </label>
                                </div>
                            </div>
                            {{-- Hidden radios — backend reads name=is_comment / is_download / is_like --}}
                            <div style="display:none">
                                <input type="radio" name="is_comment"  id="is_comment_yes"  value="1" checked>
                                <input type="radio" name="is_comment"  id="is_comment_no"   value="0">
                                <input type="radio" name="is_download" id="is_download_yes" value="1" checked>
                                <input type="radio" name="is_download" id="is_download_no"  value="0">
                                <input type="radio" name="is_like"     id="is_like_yes"     value="1" checked>
                                <input type="radio" name="is_like"     id="is_like_no"      value="0">
                            </div>
                        </div>

                        {{-- Upload progress bar --}}
                        <div class="card custom-border-card jlw-saving" id="jl-saving">
                            <div style="font-size:13px;font-weight:600;margin-bottom:4px">
                                <i class="fa-solid fa-cloud-arrow-up mr-2" style="color:var(--primary-color,#4caf50)"></i>Uploading track...
                            </div>
                            <div class="jlw-pb"><div class="jlw-pf" id="jl-pbar"></div></div>
                            <div style="font-size:12px;color:#777" id="jl-ppct">Preparing...</div>
                        </div>

                        <div class="jlw-nav" id="jl-pubnav">
                            <button type="button" class="jlw-bprev" onclick="jlGo(2)">
                                <i class="fa-solid fa-arrow-left"></i> &nbsp;Back
                            </button>
                            <div class="jlw-sp"></div>
                            <a href="{{ route('user.music.index') }}" class="jlw-bcancel mr-2">Cancel</a>
                            <button type="button" class="jlw-bpub" onclick="save_music()">
                                <i class="fa-solid fa-rocket"></i> Publish Track
                            </button>
                        </div>
                    </div>

                </div>{{-- /jlw --}}
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
        // ── Init ────────────────────────────────────────────────
        $('#category_id').select2();
        $('#language_id').select2();

        var d0 = new Date(); d0.setHours(0,0,0);
        $('#timePicker').datetimepicker({
            useCurrent:false, format:'HH:mm:ss', defaultDate:d0, showClose:true, showTodayButton:true,
            icons:{up:'fa fa-chevron-up',down:'fa fa-chevron-down',today:'fa fa-clock fa-regular',close:'fa fa-times'}
        });

        $(document).ready(function(){
            // Drag-and-drop on audio zone
            var dz = document.getElementById('jl-drop');
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

            // Audio selected → auto-upload immediately
            $('#audioFileInput').on('change', function(){
                var f = this.files[0]; if(!f) return;
                jlDetectDur(f);
                jlStartUpload(f);
            });

            // "Choose different file" link
            $('#jl-reupload').on('click', function(e){
                e.preventDefault();
                $('#jl-uploaded').hide();
                $('#jl-drop').show();
                $('#uploadedFilename').val('');
                document.getElementById('audioFileInput').value = '';
            });

            // Image previews (page-app.blade already handles this but we add zone class too)
            $('#imageUpload1').on('change', function(){ jlImgPrev(this, 'imagePreview1', 'jl-pz'); });
            $('#imageUpload2').on('change', function(){ jlImgPrev(this, 'imagePreview2', 'jl-lz'); });
        });

        function jlStartUpload(file){
            $('#jl-drop').hide();
            $('#jl-uploaded').hide();
            $('#jl-aerr').hide();
            $('#jl-upname').text(file.name);
            $('#jl-upbar').css('width','0%');
            $('#jl-uppct').text('Uploading…');
            $('#jl-uploading').show();

            var fd = new FormData();
            fd.append('audio', file);
            fd.append('_token', '{{ csrf_token() }}');

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("user.music.upload.audio") }}', true);
            xhr.upload.onprogress = function(e){
                if(e.lengthComputable){
                    var pct = Math.round(e.loaded/e.total*100);
                    $('#jl-upbar').css('width', pct+'%');
                    $('#jl-uppct').text(pct+'% uploaded…');
                }
            };
            xhr.onload = function(){
                $('#jl-uploading').hide();
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if(resp.status === 200){
                        $('#uploadedFilename').val(resp.filename);
                        $('#jl-updone').text(file.name);
                        $('#jl-uploaded').show();
                    } else {
                        var err = Array.isArray(resp.errors) ? resp.errors.join(', ') : resp.errors;
                        toastr.error(err || 'Upload failed');
                        $('#jl-drop').show();
                    }
                } catch(ex){
                    toastr.error('Upload failed');
                    $('#jl-drop').show();
                }
            };
            xhr.onerror = function(){
                $('#jl-uploading').hide();
                toastr.error('Upload failed — check your connection');
                $('#jl-drop').show();
            };
            xhr.send(fd);
        }

        function jlDetectDur(file){
            var a = document.createElement('audio'); a.preload = 'metadata';
            a.onloadedmetadata = function(){
                URL.revokeObjectURL(a.src);
                var s = Math.floor(a.duration), h = Math.floor(s/3600), m = Math.floor((s%3600)/60), ss = s%60;
                var fmt = (h>0 ? String(h).padStart(2,'0')+':' : '') + String(m).padStart(2,'0') + ':' + String(ss).padStart(2,'0');
                $('#timePicker').val(fmt);
                $('#jl-durval').text(fmt); $('#jl-dur').show();
            };
            a.src = URL.createObjectURL(file);
        }

        function jlImgPrev(input, imgId, zoneId){
            if(!input.files || !input.files[0]) return;
            var r = new FileReader();
            r.onload = function(e){
                var img = document.getElementById(imgId);
                img.src = e.target.result; img.style.display = 'block';
                document.getElementById(zoneId).classList.add('has');
            };
            r.readAsDataURL(input.files[0]);
        }

        function jlSetR(name, val){
            document.querySelector('input[name="'+name+'"][value="'+(val?1:0)+'"]').checked = true;
        }

        // ── Wizard navigation ───────────────────────────────────
        var jlStep = 1;

        function jlGo(n){
            document.getElementById('jlp'+jlStep).classList.remove('active');
            jlStep = n;
            document.getElementById('jlp'+n).classList.add('active');
            jlRefresh();
            window.scrollTo({top:0, behavior:'smooth'});
            if(n === 3) jlReview();
        }

        function jlNext(from){
            if(!jlValidate(from)) return;
            jlGo(from + 1);
        }

        function jlValidate(step){
            var ok = true;
            if(step === 1){
                var t = $('[name="title"]').val().trim();
                var c = $('[name="category_id"]').val();
                var l = $('[name="language_id"]').val();
                jlErrField('jlf-title', !t);
                jlErrField('jlf-cat',   !c);
                jlErrField('jlf-lang',  !l);
                if(!t || !c || !l) ok = false;
            }
            if(step === 2){
                var uploaded = $('#uploadedFilename').val();
                var uploading = $('#jl-uploading').is(':visible');
                var aerr = document.getElementById('jl-aerr');
                if(uploading){
                    toastr.warning('Please wait for upload to finish');
                    ok = false;
                } else if(!uploaded){
                    aerr.style.display = 'block';
                    ok = false;
                } else {
                    aerr.style.display = 'none';
                }
            }
            return ok;
        }

        function jlErrField(id, show){
            var el = document.getElementById(id); if(!el) return;
            el.classList.toggle('has-jlerr', show);
        }

        function jlRefresh(){
            for(var i=1; i<=3; i++){
                var c = document.getElementById('jlc'+i);
                var l = document.getElementById('jll'+i);
                c.className = 'jlw-c'; l.className = 'jlw-l';
                if(i < jlStep){
                    c.classList.add('done');
                    c.innerHTML = '<i class="fa-solid fa-check" style="font-size:10px"></i>';
                } else if(i === jlStep){
                    c.classList.add('active'); c.textContent = i; l.classList.add('active');
                } else {
                    c.textContent = i;
                }
                if(i < 3) document.getElementById('jlln'+i).classList.toggle('done', jlStep > i);
            }
        }

        function jlReview(){
            var catEl  = document.getElementById('category_id');
            var langEl = document.getElementById('language_id');
            document.getElementById('rv-title').textContent = $('[name="title"]').val() || '—';
            document.getElementById('rv-dur').textContent   = $('#timePicker').val() || '—';
            document.getElementById('rv-cat').textContent   = catEl.options[catEl.selectedIndex]?.text || '—';
            document.getElementById('rv-lang').textContent  = langEl.options[langEl.selectedIndex]?.text || '—';
        }

        // ── Save (original logic + XHR upload progress) ─────────
        function save_music(){
            var Check_Admin = '<?php echo Demo_Mode(); ?>';
            if(Check_Admin == 1){
                $('#jl-pubnav').hide();
                $('#jl-saving').show();
                var formData = new FormData($('#music')[0]);
                $.ajax({
                    type:'POST', url:'{{ route("user.music.store") }}',
                    data:formData, cache:false, contentType:false, processData:false,
                    xhr: function(){
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e){
                            if(e.lengthComputable){
                                var pct = Math.round(e.loaded / e.total * 100);
                                $('#jl-pbar').css('width', pct + '%');
                                $('#jl-ppct').text(pct + '% uploaded...');
                            }
                        }, false);
                        return xhr;
                    },
                    success:function(resp){
                        $('#jl-saving').hide();
                        get_responce_message(resp, 'music', '{{ route("user.music.index") }}');
                    },
                    error:function(xhr, textStatus, errorThrown){
                        $('#jl-saving').hide();
                        $('#jl-pubnav').show();
                        toastr.error(errorThrown, textStatus);
                    }
                });
            } else {
                showError();
            }
        }
    </script>
@endsection
