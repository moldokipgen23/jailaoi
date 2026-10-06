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
<p class="mt-4">Imports title, description and duration. Artists can preview the thumbnail and select it as artwork after confirming their rights. Automatic channel sync is not enabled. Artists must have rights to publish their releases.</p>
<hr><h2 style="font-size:20px;">Experimental audio import</h2>
<p>Test access only. Downloads can be blocked by YouTube. This feature is not ready for paid subscriptions. Maximum 15 minutes and 100 MB per video; 3 attempts per day and 20 per month per artist, including failed attempts.</p>
<form method="POST" action="{{ route('admin.youtube.audio.save') }}">@csrf
<label><input type="checkbox" name="enabled" value="1" {{ $audioSettings['enabled'] ? 'checked' : '' }}> Enable testing for selected artists</label>
<label for="youtubeAudioArtists" class="d-block mt-2">Artists with test access</label>
<select id="youtubeAudioArtists" name="user_ids[]" multiple class="form-control" size="6">@foreach($artists as $artist)<option value="{{ $artist->user_id }}" {{ in_array((int)$artist->user_id,$audioSettings['user_ids'],true) ? 'selected' : '' }}>{{ $artist->name }}</option>@endforeach</select>
<button type="submit" class="btn btn-primary mt-2">Save test access</button></form>
<h2 style="font-size:20px;" class="mt-4">Recent audio import attempts</h2>
<div class="table-responsive"><table class="table"><thead><tr><th>Time</th><th>Artist user ID</th><th>Video</th><th>Status</th><th>Details</th></tr></thead><tbody>@forelse($audioImports as $attempt)<tr><td>{{ $attempt->created_at }}</td><td>{{ $attempt->user_id }}</td><td>{{ $attempt->video_id }}</td><td>{{ $attempt->status }} / {{ $attempt->phase }}</td><td>{{ $attempt->message ?: $attempt->title }}</td></tr>@empty<tr><td colspan="5">No audio imports yet.</td></tr>@endforelse</tbody></table></div>
</div></div></div></div>
@endsection
