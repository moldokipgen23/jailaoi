@extends('admin.layout.page-app')
@section('page_title', 'YouTube Import')
@section('tab_title', 'YouTube Import')
@section('content')
@include('admin.layout.sidebar')
<div class="right-content">@include('admin.layout.header')<div class="body-content"><div class="card"><div class="card-body">
<h1 style="font-size:24px;">YouTube import setup</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<p>{{ $configured ? 'API key configured. Full metadata import is available, subject to Google API access and quota.' : 'Basic title and thumbnail preview are active. Add an API key to enable descriptions and duration.' }}</p>
<ol><li>Create or select a project in <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a>.</li><li>Enable YouTube Data API v3.</li><li>Create an API key. Restrict it to YouTube Data API v3 and the public outbound IP of this VPS.</li><li>Enter it below, then test an artist video import.</li></ol>
<form method="POST" action="{{ route('admin.youtube.save') }}">@csrf
<label for="youtubeApiKey">YouTube Data API key</label><input id="youtubeApiKey" name="api_key" type="password" autocomplete="new-password" class="form-control" required maxlength="256">
<p class="mt-2">Saved keys are encrypted and never displayed here. Enter a new key only to configure or replace it.</p><button class="btn btn-primary" type="submit">Save API key</button></form>
<p class="mt-4">Imports title, description and duration. Artists can preview the thumbnail and select it as artwork after confirming their rights. Audio downloads and automatic channel sync are not enabled. Artists must provide original audio and artwork they have rights to publish.</p>
</div></div></div></div>
@endsection
