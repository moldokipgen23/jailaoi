# Experimental YouTube audio import — 6 October 2026

## Delivered

Artist Add Music step 2 has an optional YouTube audio import for explicitly granted test accounts. The user confirms publishing rights, submits one public video link, and receives queued/checking/downloading/converting/saving status. Success supplies an MP3 filename and server-measured duration to the existing artist publishing form. Failed imports provide a readable message and preserve manual uploads. Session refresh can resume an import; publishing consumes the import once. Importing alone never creates a catalogue entry, stream credit, payout or charge.

Admin /admin/youtube-import provides a global testing switch, selected-artist access and the 25 most recent attempts. Listener subscriptions do not confer access. Only the JailaOi Audio account shown by the user was granted test access. Monetized plans, checkout, billing and channel synchronization are not implemented.

## Controls

- Artist authentication, CSRF, per-account start lock, authenticated UUID status lookup, throttling and duplicate active request protection.
- Public YouTube URL normalized to an 11-character ID. No arbitrary source URLs or shell interpolation. Fixed argument-array subprocesses run as jailaoi-portal.
- One dedicated database queue worker; 15-minute input limit, 100 MiB source limit, 3 attempts/day and 20/month including failures, queue capacity check, bounded subprocess times, no automatic retries.
- ffprobe validates source duration and presence of audio; ffmpeg prepares 192 kbps MP3. Private extraction directories are removed after success/failure. A timer expires stale jobs and removes extraction remnants.
- Existing storage driver retained. Storage errors do not create ready results. Imported duration is authoritative at publication. Signed-owner source binding is retained even if the browser omits its hidden import ID. A locked import row prevents publishing the same imported asset twice.
- No login cookies, proxies, CAPTCHA solving, account credentials or anti-bot bypasses were added.

## Live checks and limits

99 backend tests / 290 assertions passed; import JS syntax passed. Live table migration, protected routes, artist form rendering and admin controls verified. Dedicated worker and cleanup timer are active. A harmless job referencing a nonexistent UUID was consumed without failure. A self-generated three-second tone passed the real WAV-to-MP3 pipeline and ffprobe confirmed MP3 codec (73,395 bytes).

Real download attempts for the screenshot-transcribed video RdkWFaZc3JY failed with YouTube requiring sign-in to confirm the requester is not a bot. No audio was retained or published. A second public sample (Big Buck Bunny, aqz-KE-bpKQ; Blender Foundation / https://peach.blender.org/about/) was blocked by the same bot check. A successful live YouTube audio download has NOT been verified. No live artist import row, catalogue entry or payment was created during verification. End-to-end artist browser interaction is pending sign-in in Codex's browser; the user's Chrome session is separate.

This is an experimental implementation, not a verified production subscription feature. YouTube Data API provides metadata; yt-dlp is an unofficial download dependency and remains subject to upstream blocks and changes. Current developer policies still apply: https://developers.google.com/youtube/terms/developer-policies . Adding code is not evidence of YouTube approval or artist ownership.

Existing Bunny delivery/configuration issues were not changed. Abandoned uploaded assets follow the existing manual pre-upload retention behavior; the new cleanup removes extraction files and expires upload proofs, and does not delete artist catalogue/CDN music. Add verified abandoned-asset cleanup before charging for this feature.

## Operations

Downloader: /opt/jailaoi-youtube/yt-dlp, pinned release 2026.08.19; SHA-256 matched the official release checksum. ffmpeg/ffprobe installed from Ubuntu packages. Official downloader source: https://github.com/yt-dlp/yt-dlp . Worker runs as the application user with private temporary storage, CPU/memory/file-size limits, read-only system paths and constrained write paths. Systemd units are in ops/youtube-audio/.

Dedicated connection youtube_audio uses jobs table / youtube-audio queue, retry_after 720 seconds; one attempt, worker timeout 600 seconds. Existing default queue behavior is unchanged. UMask remains 0077; only new public playback directories and the new published MP3 receive web-server read access. Private extraction directories remain 0700. Worker restarts after each job to refresh stored settings. Cleanup timer runs every ten minutes, independently of the existing app scheduler.

Commands: sudo systemctl status jailaoi-youtube-audio.service jailaoi-youtube-clean.timer; sudo journalctl -u jailaoi-youtube-audio.service --since today; sudo -u jailaoi-portal php artisan youtube:clean-imports (run from the live app root).

Backups: /home/jailaoi-portal/repair-backups/codex-20261006T174400Z, codex-20261006T174751Z, codex-20261006T174926Z, plus follow-up backups for wording and narrow playback permissions. Sixteen installed application files verified by SHA-256. To disable new import starts, untick the testing switch in admin. To pause processing, stop the dedicated service; manual uploads and metadata import remain available. No database rollback or file deletion is necessary to disable the feature.
