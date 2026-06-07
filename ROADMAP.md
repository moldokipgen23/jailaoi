# JailaOi — Implementation Roadmap

> **Audience:** AI agents (DeepSeek / Claude) and developers implementing tasks.
> **Rules:** Each task lists files to touch, acceptance criteria, dependencies, and verification steps. Do NOT mark a task complete unless every acceptance criterion is met.

---

## 🛡️ Architectural Ground Rules (MUST READ before any task)

These are non-negotiable. Breaking them blocks the task.

1. **dtradio compatibility.** This codebase started as the CodeCanyon dtradio admin. Future updates from CodeCanyon must be mergeable. Therefore:
   - **NEW JailaOi files** go in their own filenames (e.g., `WithdrawalController.php`, `ArtistEarning.php`).
   - **Modifying dtradio originals** is allowed only when no separate file would work. Mark the change with a `// JAILAOI:` comment so future merges can spot it.
   - Never delete original dtradio files unless 100% certain they're unused (grep the entire codebase first, including views and routes).

2. **Database changes.** Use Laravel migrations only. Never edit existing migrations — add new ones. Migrations must be idempotent (use `Schema::hasColumn`, `Schema::hasTable` checks). Live database `jailaoic_jailaoinew` must keep working after the migration runs.

3. **Backward compatibility for live app.** Users are on Play Store APK. Any API change must keep the old APK working:
   - New endpoints: fine.
   - New fields in responses: fine.
   - Removing/renaming fields/endpoints: NOT ALLOWED.
   - Changing response shape: NOT ALLOWED.

4. **Two Flutter folders confusingly named.** The real Flutter project is at `Jailaoi app/` (with space). NOT `app/`. Always verify with `ls` before edits.

5. **Live server.** `m.jailaoi.com` on cPanel, branch `main`, no SSH (terminal only).

6. **Live DB credentials are in .env on server** — never commit them.

7. **No silent error swallowing.** All `try/catch` blocks must log errors via Laravel `Log::error()` (backend) or `printLog()` (Flutter).

8. **Test before pushing.** PHP: `php -l <file>`. Dart: `flutter analyze`. Both must pass.

---

## 📦 BATCH STRUCTURE

Each batch is a coherent unit. Complete batch 1 fully before batch 2. After each batch:
1. Commit + push to `main`.
2. Deploy to `m.jailaoi.com` (`git pull`, `php artisan migrate --force`, clears).
3. Smoke test on live server.
4. Send the diff/PR back to Claude for review.
5. Move to next batch.

---

# 🔴 BATCH 1 — Critical Infrastructure & Trust (1-2 weeks)

Goal: stop bleeding. Make the platform reliable + trustworthy before adding features.

---

### TASK 1.1 — Audio CDN Migration

**Why:** Audio files are served from PHP server. Won't scale past ~50 concurrent listeners. Must move to CDN.

**Scope:**
- Set up Cloudflare R2 bucket (or AWS S3 + CloudFront). Use Cloudflare R2 (cheaper, free egress).
- Add disk config in `config/filesystems.php` for R2.
- Update Music upload (`User/MusicController.php`), Radio upload (`User/RadioController.php`), Podcast upload to write audio files to R2 instead of local `storage/app/public/music`, etc.
- Update `Common::Get_Song()` helper to return R2 public URL for files stored on R2.
- Add a feature flag `audio_storage_driver` in `tbl_general_setting` (values: `local` or `r2`). Default `local`. Only flip to `r2` after testing.
- One-off Artisan command: `php artisan jailaoi:migrate-audio-to-r2` to copy existing local audio files to R2 + update DB paths.

**Files:**
- `config/filesystems.php` (add r2 disk)
- `.env.example` (document R2_BUCKET, R2_ACCESS_KEY_ID, R2_SECRET, R2_ENDPOINT, R2_PUBLIC_URL)
- `app/Models/Common.php` (modify Get_Song)
- `app/Http/Controllers/User/MusicController.php` `RadioController.php`
- `app/Http/Controllers/Admin/SongController.php` `MusicController.php` `PodcastController.php`
- `app/Console/Commands/MigrateAudioToR2.php` (new)

**Acceptance:**
- New uploads with flag = `r2` end up on R2 and play in the app.
- Existing songs (flag = `local`) still play unchanged.
- Migration command copies files + updates DB.
- Flutter app needs zero changes (URLs come from API).
- Old APK on Play Store continues working.

**Verification by Claude:** Show me one new upload from `/user/music` working on R2, and an old local-file song still playing.

**Estimated effort:** 1-2 days.

---

### TASK 1.2 — Background Job Queue

**Why:** Email sending, image processing, payment webhooks block requests. Need queue.

**Scope:**
- Configure Laravel queue driver = `database`.
- Add migration for `jobs` table (Laravel default).
- Add migration for `failed_jobs` table.
- Create cron entry that runs `php artisan queue:work --stop-when-empty --max-time=50` every minute via cPanel cron.
- Convert all `Mail::send` calls and any heavy ops to `dispatch(Job::class)`.

**Files:**
- `config/queue.php` (driver default = `database`)
- migrations for `jobs` and `failed_jobs` if not present
- `app/Jobs/` (new folder)
- cPanel cron entry (document in README)

**Acceptance:**
- Sending a welcome email returns instantly; email arrives within ~1 minute (cron-driven).
- `failed_jobs` table populated when job fails (for debugging).

**Verification by Claude:** Show me an email being queued + processed.

**Estimated effort:** Half a day.

---

### TASK 1.3 — Email Verification on Signup

**Why:** Anyone can register with fake email; no abuse control. Block artist portal until verified.

**Scope:**
- Add `email_verified_at` column on `tbl_user` if not present.
- Send verification email on registration (via the queue from Task 1.2).
- Add `/user/verify-email/{token}` route + handler.
- Block login at `/user/login` if `role='artist'` and `email_verified_at IS NULL`.
- Show "Verify email" page with "Resend verification" button.
- Token table or signed URL (use Laravel signed URLs).

**Files:**
- New migration if column missing
- `app/Http/Controllers/User/RegisterController.php` (dispatch verify mail)
- `app/Http/Controllers/User/LoginController.php` (block unverified)
- `app/Mail/VerifyEmail.php` (new)
- `resources/views/emails/verify-email.blade.php` (new)
- `resources/views/user/verify/index.blade.php` (new)
- `routes/user.php` (add verify routes)

**Acceptance:**
- New artist application → verification email sent.
- Clicking link verifies and lets them log in.
- Unverified user trying to log in sees "please verify" message.
- "Resend" link works.

**Verification by Claude:** Show full end-to-end: registration → email → click → login.

**Estimated effort:** 1 day.

---

### TASK 1.4 — Artist Approval / Rejection / Withdrawal Email Notifications

**Why:** Right now everything is silent. Artists don't know when they're approved or paid.

**Scope:**
- 3 notification emails sent via queue:
  1. Artist application **approved** → "Welcome! Log in at m.jailaoi.com/user"
  2. Artist application **rejected** → include admin's rejection note
  3. Withdrawal **approved/paid** → amount + reference
- Trigger from `Admin/ArtistRequestController` approve/reject methods and `Admin/WithdrawalController`.
- All emails branded with JailaOi pink (#E01E75).

**Files:**
- `app/Mail/ArtistApprovedMail.php`, `ArtistRejectedMail.php`, `WithdrawalPaidMail.php` (new)
- `resources/views/emails/artist-approved.blade.php`, etc. (new)
- `app/Http/Controllers/Admin/ArtistRequestController.php` (dispatch on action)
- `app/Http/Controllers/Admin/WithdrawalController.php` (dispatch on paid)

**Acceptance:**
- Approve an artist in admin → email arrives with login instructions.
- Reject with note → email contains the note.
- Mark withdrawal as paid → email arrives with amount.

**Verification by Claude:** Show me the 3 emails received in inbox.

**Estimated effort:** Half a day.

---

### TASK 1.5 — Migrated Artist Password Reset Blast

**Why:** 75 migrated artists are on old DeepSound passwords they likely don't remember.

**Scope:**
- One-off Artisan command: `php artisan jailaoi:blast-migrated-artist-emails`.
- For every user where `role='artist'` AND `last_login_at IS NULL` (or created_at < migration date), send a "Welcome to JailaOi — set your password" email containing a one-time signed reset link.
- Use the existing password reset flow (Task done).
- Throttle 100 emails/hour to avoid SMTP limits.

**Files:**
- `app/Console/Commands/BlastMigratedArtistEmails.php` (new)
- `app/Mail/MigratedArtistWelcomeMail.php` (new)
- `resources/views/emails/migrated-artist-welcome.blade.php` (new)

**Acceptance:**
- Running command sends emails to all qualifying users.
- Each email contains a working reset link.
- Command is rerunnable (skips users already emailed in last 7 days).

**Verification by Claude:** Show me the email + clicking link works.

**Estimated effort:** 2 hours.

---

### TASK 1.6 — Better Playback Error Handling

**Why:** Dead song URLs cause silent failures. Users abandon.

**Scope:**
- Backend: new table `tbl_play_error` (id, user_id, content_id, content_type, url, error_message, http_status, created_at).
- New endpoint `POST /api/log_play_error` that logs the error.
- Flutter: when `audioPlayer` throws a `PlayerException` or load error, do these in order:
  1. Call `log_play_error` API with details.
  2. Show user-friendly toast: "This track is unavailable, skipping…"
  3. Auto-skip to next track in playlist.
- Admin page `/admin/play-errors` showing errors with content + count (so admin can fix dead URLs).

**Files:**
- New migration `tbl_play_error`
- `app/Models/PlayError.php` (new)
- `app/Http/Controllers/Api/HomeController.php` add `log_play_error` method
- `routes/api.php` add route
- `app/Http/Controllers/Admin/PlayErrorController.php` (new)
- `resources/views/admin/play_errors/index.blade.php` (new)
- Flutter: `app/lib/music/musicmanager.dart` and `musicdetails.dart` — handle PlayerException + call API

**Acceptance:**
- Playing a broken URL logs an error, shows toast, skips to next track.
- Admin can see all errors in `/admin/play-errors`.

**Verification by Claude:** Show me a broken-URL scenario + the admin error page entry.

**Estimated effort:** 1 day.

---

# 🟠 BATCH 2 — Foundation Music Features (1-2 weeks)

Goal: turn this from "demo" to "real music platform."

---

### TASK 2.1 — Album / EP Grouping

**Why:** Music platforms need album-level grouping. Singles-only feels amateur.

**Scope:**
- New table `tbl_album`: id, artist_id, name, cover_image, type (single/ep/album), release_date, description, status.
- Add column `album_id` (nullable) to `tbl_music`.
- Artist portal upload flow: option to "Create new album" or "Add to existing album."
- Album page in Flutter app: shows cover, tracklist, total duration, "play all."
- Admin can edit/delete albums in `/admin/album`.
- Section type 9 = Album (so sections can show "Featured Albums").

**Files:**
- New migration adding `tbl_album` + `tbl_music.album_id`
- `app/Models/Album.php` (new)
- `app/Http/Controllers/Admin/AlbumController.php` (new)
- `app/Http/Controllers/User/MusicController.php` (album select on upload)
- `app/Http/Controllers/Api/HomeController.php` — new `get_album_detail`, support album type in sections
- `app/Models/Common.php` — extend `section_query` to handle type=9
- Flutter: new `app/lib/pages/albumdetail.dart`, model, provider, API method

**Acceptance:**
- Artist can create album with cover.
- Artist can attach tracks to album on upload OR after.
- Album page shows all tracks, plays in order.
- Section "Featured Albums" works.
- Old standalone tracks (no album) still play normally.

**Verification by Claude:** Show me a created album, its detail page in app, and a track attached to it playing.

**Estimated effort:** 2-3 days.

---

### TASK 2.2 — Lyrics

**Why:** Top-3 requested feature on every music app.

**Scope:**
- Add `lyrics` column (TEXT, nullable) to `tbl_music`, `tbl_song`, `tbl_podcast`.
- Artist portal upload + edit: textarea for lyrics.
- Flutter: in music player full-screen view, add a "Lyrics" toggle button. Tapping shows lyrics scroll view below player controls.
- Return `lyrics` field in all content API responses.

**Files:**
- New migration adding lyrics column to 3 tables
- `app/Http/Controllers/User/MusicController.php` (and similar) — store + return lyrics
- API responses must include `lyrics` field
- Flutter: `app/lib/music/musicdetails.dart` — add lyrics UI

**Acceptance:**
- Artist can paste lyrics on upload.
- Lyrics show in app when toggled.
- If no lyrics: show "No lyrics available" gracefully.

**Verification by Claude:** Show me lyrics displaying in the player.

**Estimated effort:** 1 day.

---

### TASK 2.3 — Liked Songs / Personal Library

**Why:** Users need a "home" for favorites. Existing `tbl_favorite` works but no dedicated screen.

**Scope:**
- Already have `add_remove_favorite` API + `get_favorite_list`.
- Add a "Library" tab or icon in Flutter (or use existing favorites page).
- Library page sections:
  - Liked Songs (grid)
  - Liked Albums
  - Liked Artists
  - Recently played
- Show count + thumbnail collage for "Liked Songs" entry.
- Add favorite heart icon on every track row in app.

**Files:**
- Flutter: `app/lib/pages/library.dart` (new or rework `favoritesonglist.dart`)
- API: `get_favorite_list` already supports types — confirm Albums + Artists work, extend if not.

**Acceptance:**
- Heart icon on tracks → adds to library.
- Library tab shows all 4 sections.
- Recently played auto-updates.

**Verification by Claude:** Show me liking a song + it appearing in library.

**Estimated effort:** 1-2 days.

---

### TASK 2.4 — User-Created Playlists

**Why:** "My Workout Mix" "Sunday Worship" — users want to curate.

**Scope:**
- Tables exist: `tbl_playlist_content`. Verify or create `tbl_playlist` (id, user_id, name, cover, description, is_public, status).
- Flutter: "Create Playlist" button. Add to playlist from any track ("+" icon).
- Playlist detail page: cover, name, tracks, play all, edit, delete.
- Playlists in user library.
- (Optional) Public playlists discoverable by others.

**Files:**
- Backend: `app/Models/Playlist.php` (verify exists), `Playlist_Content.php` (exists)
- `app/Http/Controllers/Api/PlaylistController.php` (new) — CRUD endpoints
- `routes/api.php` — playlist routes
- Flutter: new playlist pages + provider

**Acceptance:**
- User can create playlist, add tracks, reorder, delete.
- Playlist appears in library.

**Verification by Claude:** Show me creating + adding tracks + playing back.

**Estimated effort:** 2 days.

---

### TASK 2.5 — Recently Played

**Why:** Users want to find that song they played yesterday without searching.

**Scope:**
- Backend: query `tbl_user_action` where action=1, user_id=current, order by created_at desc, group by content_id, limit 50.
- New API endpoint `get_recently_played`.
- Flutter: section on Home + dedicated page in Library.

**Files:**
- `app/Http/Controllers/Api/HomeController.php` — add `get_recently_played`
- `routes/api.php` — add route
- Flutter: provider + UI section

**Acceptance:**
- Plays appear in Recently Played within seconds.
- Duplicates collapsed (each song appears once, most recent first).

**Verification by Claude:** Play 3 songs, see them in Recently Played.

**Estimated effort:** Half a day.

---

### TASK 2.6 — Offline Downloads for Premium

**Why:** #1 reason people pay for Premium. Hooks already exist in code.

**Scope:**
- Verify `Utils.playAudio` already reads from SharedPreferences for downloaded files (it does).
- Add download button on every track in app.
- Tapping starts background download to app storage.
- Premium check: only Premium users can download (use `Constant.isSubscription`).
- Download manager page: list of downloads, progress bar, delete option.
- Auto-purge downloads if subscription expires.

**Files:**
- Flutter: `app/lib/pages/downloadhistory.dart` (exists, polish), new download manager UI
- Add `Dio` download support or use existing
- Backend: optional — track downloads in `tbl_download` for analytics

**Acceptance:**
- Premium user downloads track → plays offline (airplane mode test).
- Free user sees Premium upsell when tapping download.
- Subscription cancel → downloads removed.

**Verification by Claude:** Download a track, turn airplane mode on, play.

**Estimated effort:** 2 days.

---

# 🟡 BATCH 3 — Discovery & Personalization (1-2 weeks)

Goal: make the app feel like it knows you.

---

### TASK 3.1 — Onboarding Taste Profile

**Why:** Cold-start personalization. New users get personalized home from minute 1.

**Scope:**
- New first-launch screen: "Pick 3+ artists you love" — grid of top 20 artists.
- Save selection to `tbl_user_taste_profile` (user_id, artist_id, weight).
- Use selection to seed first home recommendations.

**Files:**
- New migration `tbl_user_taste_profile`
- Flutter: new onboarding screen after signup
- API: `save_taste_profile`, `get_recommended_for_taste`

**Acceptance:** New user picks artists → home shows related music.

**Estimated effort:** 1-2 days.

---

### TASK 3.2 — Basic Recommendations

**Why:** "Made for You" / "Because you played X" drives session length.

**Scope:**
- Simple collaborative filter: "users who played X also played Y."
- Endpoint `get_recommended_tracks(user_id)`:
  - Take user's last 30 plays
  - For each play, find other users who played it
  - Aggregate what those users played
  - Return top 20 not already in user's history
- Show as Home section: "Made for You"
- Background job to precompute daily.

**Files:**
- `app/Console/Commands/PrecomputeRecommendations.php` (new)
- `tbl_user_recommendation` table (user_id, content_id, content_type, score, generated_at)
- API endpoint
- Flutter: home section

**Acceptance:** Section shows different tracks for different users.

**Estimated effort:** 2-3 days.

---

### TASK 3.3 — Discover Weekly Playlist

**Why:** Spotify's killer feature.

**Scope:**
- Weekly cron generates one personalized 30-track playlist per active user.
- Pulled from recommendations (Task 3.2) + diversity (different artists, languages).
- Auto-refreshes every Monday.
- Show as featured tile in app.

**Files:**
- `app/Console/Commands/GenerateDiscoverWeekly.php`
- Use `tbl_playlist` + `tbl_playlist_content` (existing).

**Acceptance:** Every Monday, each user has a fresh "Discover Weekly" playlist with 30 tracks.

**Estimated effort:** 2 days.

---

### TASK 3.4 — Search V2

**Why:** Current search is basic. Need typo tolerance + recent searches + suggestions.

**Scope:**
- Recent searches saved per user (SharedPreferences in Flutter).
- Backend: use MySQL FULLTEXT search or simple LIKE + SOUNDEX for typo tolerance.
- API: `search_suggestions` — returns top results as you type.
- Search categories: tracks, albums, artists, playlists, podcasts.

**Files:**
- Flutter: `app/lib/pages/search.dart` improvements
- Backend: enhance `search_content` + add `search_suggestions`

**Acceptance:** Typing "wrship" returns "Worship" results.

**Estimated effort:** 1-2 days.

---

### TASK 3.5 — Year in Review

**Why:** Viral marketing driver. Spotify Wrapped is huge.

**Scope:**
- End of year (Dec 1 onwards), generate per-user summary:
  - Top 5 songs
  - Top 5 artists
  - Total minutes listened
  - Top genre
  - First song of the year
- Shareable image (auto-generated PNG).
- In-app screen with stats.

**Files:**
- `app/Console/Commands/GenerateYearInReview.php`
- API endpoint + Flutter page
- PNG generation: use Intervention Image lib or backend HTML→Image

**Acceptance:** User taps banner → sees their year stats.

**Estimated effort:** 2-3 days.

---

# 🟠 BATCH 4 — Artist Tools V2 (1-2 weeks)

---

### TASK 4.1 — Verified Artist Badge

**Why:** Trust + status.

**Scope:**
- Add `is_verified` column to `tbl_artist`.
- Admin can verify in `/admin/artist`.
- Blue checkmark icon next to verified artists everywhere in app.
- Verified-only filter in section type.

**Files:**
- New migration
- Admin UI toggle
- Flutter: badge component used wherever artist name appears

**Acceptance:** Verified artist shows checkmark; unverified doesn't.

**Estimated effort:** Half a day.

---

### TASK 4.2 — Artist Analytics Dashboard

**Why:** Artists need to see their growth + earnings.

**Scope:**
- Web page `/user/analytics`:
  - Plays over time (line chart, last 30 days)
  - Top tracks (table with play count)
  - Top countries / cities (where listeners are)
  - Demographics (gender, age if you collect)
  - Listener growth chart
  - Top fans (users who played most)
- Use Chart.js or similar.

**Files:**
- `app/Http/Controllers/User/AnalyticsController.php` (new)
- `resources/views/user/analytics/index.blade.php` (new)
- Sidebar link in artist portal

**Acceptance:** Artist sees their stats + charts that match the database.

**Estimated effort:** 2-3 days.

---

### TASK 4.3 — Pre-Save / Release Scheduling

**Why:** Build buzz before track drops.

**Scope:**
- Artist upload form: option "Schedule release for date."
- Track stays hidden until release date (status check).
- Pre-save: users can "Pre-save this track" → auto-add to library on release.
- Notification on release.

**Files:**
- Add `release_date` + `is_scheduled` columns to `tbl_music`
- Cron job auto-publishes scheduled tracks
- Pre-save table `tbl_presave`

**Acceptance:** Scheduled track goes live at scheduled time. Pre-save users get notification.

**Estimated effort:** 2 days.

---

### TASK 4.4 — Multi-Artist Tracks (Featuring)

**Why:** Collabs are common. Need credit splits.

**Scope:**
- Already `tbl_music.artist_id` supports comma-separated.
- Upload UI: search-and-add multiple artists with "featuring" tag + earnings split %.
- Earnings split: `tbl_artist_earnings` already supports multi-artist split, but make it configurable per track (not equal).

**Files:**
- `tbl_music_artists` table (track_id, artist_id, role, earnings_split_percent)
- Update upload form
- Update `creditArtistEarning` to use split

**Acceptance:** Track with 2 artists @ 70/30 → earnings divide accordingly.

**Estimated effort:** 1-2 days.

---

### TASK 4.5 — Bulk Upload

**Why:** Saves artists hours if they have a catalog.

**Scope:**
- Artist portal "Bulk Upload" page.
- Upload CSV (one row per track: name, audio_file_name, category, language, lyrics).
- Upload ZIP with all audio files.
- Backend matches CSV rows to audio files by name.
- Progress page showing each row's status.

**Files:**
- `app/Http/Controllers/User/BulkUploadController.php` (new)
- Background job to process each row

**Acceptance:** Upload 10 tracks via bulk → all appear in artist's catalog.

**Estimated effort:** 2-3 days.

---

# 🟢 BATCH 5 — Monetization (1-2 weeks)

---

### TASK 5.1 — Working Payment Gateway

**Why:** Currently no real way to subscribe. Premium = vaporware.

**Scope:**
- Pick gateway: **Razorpay** (India) or **Stripe** (global) or **Paystack** (Africa).
- Integrate via existing `Payment_Option` model + `Transaction` model.
- Implement Google Play Billing for in-app Android purchases.
- Webhook handler to mark `tbl_user.is_subscription` correctly.
- Free trial: 7 days.

**Files:**
- `app/Http/Controllers/Api/PaymentController.php` extensions
- Webhook routes
- Flutter: real payment flow in `subscription/allpayment.dart`

**Acceptance:** Real Premium purchase works end-to-end.

**Estimated effort:** 3-5 days (gateway-dependent).

---

### TASK 5.2 — Automated Artist Withdrawal Payouts

**Why:** Currently manual. Doesn't scale.

**Scope:**
- Integrate Paystack Transfer API or Razorpay Payout API.
- Admin clicks "Approve" → backend triggers automated transfer to artist's bank/UPI/mobile money.
- Status webhook updates withdrawal record.

**Files:**
- `app/Services/PayoutService.php` (new)
- Webhook handler

**Acceptance:** Approve withdrawal → money lands in artist account within 24h.

**Estimated effort:** 2-3 days.

---

### TASK 5.3 — Tipping / Fan Support

**Why:** Direct fan-to-artist support. Engagement driver.

**Scope:**
- "Tip [Artist]" button on artist profile + while playing.
- User picks amount → pays via Razorpay/Stripe → 80% goes to artist earnings, 20% platform fee.
- Tip notification to artist.

**Files:**
- `tbl_tips` table
- API endpoints
- Flutter UI

**Acceptance:** Tip flow works end-to-end + artist sees tip in earnings.

**Estimated effort:** 2 days.

---

### TASK 5.4 — Premium Tier Plans

**Why:** Currently one Premium = blunt. Need Free / Premium / Family.

**Scope:**
- Tiers: Free (ads, low quality), Premium (no ads, hi quality, offline), Family (up to 5 accounts).
- Admin panel manages tiers.
- Audio quality filter based on tier.

**Files:**
- Tier table + UI
- Quality switch in audio player

**Acceptance:** Family plan works (1 sub = 5 accounts).

**Estimated effort:** 2-3 days.

---

# 🟢 BATCH 6 — Social & Community (2 weeks)

---

### TASK 6.1 — Follow Other Listeners

**Why:** Social graph drives retention.

**Scope:**
- Already have follows for artists. Extend to follow regular users.
- "Friends listening to" feed on home.

**Estimated effort:** 1-2 days.

---

### TASK 6.2 — Activity Feed

**Why:** "Your friend liked X" = discovery channel.

**Estimated effort:** 2 days.

---

### TASK 6.3 — Share Track to Instagram/WhatsApp Story

**Why:** Viral sharing loop.

**Scope:**
- Generate share image with track cover + "Now playing on JailaOi."
- Use native share sheet.

**Estimated effort:** 1 day.

---

### TASK 6.4 — Collaborative Playlists

**Why:** Group playlists for events.

**Estimated effort:** 2 days.

---

### TASK 6.5 — Public Profile Pages

**Why:** Vanity + sharing.

**Estimated effort:** 1-2 days.

---

# 🟢 BATCH 7 — Operations & Infrastructure (ongoing)

---

### TASK 7.1 — Crash Reporting

- Flutter: Firebase Crashlytics.
- Laravel: Sentry.
- Effort: 1 day.

### TASK 7.2 — Redis Cache

- Use for sessions + hot queries.
- Effort: Half a day.

### TASK 7.3 — Daily DB Backup to S3

- Cron + `mysqldump` + upload.
- Effort: Half a day.

### TASK 7.4 — Image Optimization Pipeline

- Auto WebP, multiple sizes (thumbnail/medium/large).
- Effort: 1-2 days.

### TASK 7.5 — Consolidate tbl_song into tbl_music

- One unified content table.
- Migration script keeps old IDs aliased.
- Effort: 3-4 days.

### TASK 7.6 — Automated Tests

- Backend: Pest/PHPUnit for critical flows (login, upload, earnings credit).
- Effort: ongoing.

---

# 🚀 BATCH 8 — Growth (parallel)

- Referral program (give Premium to inviter + invitee)
- Push notification campaigns (track favorite artist drops new release)
- Email marketing (weekly digest)
- Web player (m.jailaoi.com listen interface)
- Deep linking (open shared URLs in app)
- App Store Optimization

---

# 📋 WORKFLOW FOR THE AI EXECUTING THIS

For each task:

1. **Read** the ground rules at the top.
2. **Check dependencies** — earlier batches/tasks must be done first.
3. **Make a feature branch** if multiple tasks in flight.
4. **Implement** per scope + acceptance criteria.
5. **Test locally** — `php -l` for PHP, `flutter analyze` for Dart.
6. **Commit** with clear message: `[BATCH-X-TASK-Y] short description`.
7. **Update this ROADMAP.md**: mark task ✅ done, add date completed + commit SHA.
8. **Push to main.**
9. **Stop and report back** to Claude with:
   - What was built
   - Files touched
   - How to verify
   - Any deviations from spec
   - Any blockers

Claude will verify by:
- Reading the diff
- Checking acceptance criteria
- Smoke-testing the change
- Approving or requesting fixes

---

# 📈 PROGRESS TRACKER

| Batch | Task | Status | Done by | Date | Commit |
|-------|------|--------|---------|------|--------|
| 1.1 | Audio CDN | ⬜ Todo | — | — | — |
| 1.2 | Job Queue | ⬜ Todo | — | — | — |
| 1.3 | Email Verification | ⬜ Todo | — | — | — |
| 1.4 | Approval/Withdrawal Emails | ⬜ Todo | — | — | — |
| 1.5 | Migrated Artist Blast | ⬜ Todo | — | — | — |
| 1.6 | Playback Error Handling | ⬜ Todo | — | — | — |
| 2.1 | Album/EP Grouping | ⬜ Todo | — | — | — |
| 2.2 | Lyrics | ⬜ Todo | — | — | — |
| 2.3 | Liked Songs Library | ⬜ Todo | — | — | — |
| 2.4 | User Playlists | ⬜ Todo | — | — | — |
| 2.5 | Recently Played | ⬜ Todo | — | — | — |
| 2.6 | Offline Downloads | ⬜ Todo | — | — | — |
| 3.1 | Onboarding Taste | ⬜ Todo | — | — | — |
| 3.2 | Basic Recommendations | ⬜ Todo | — | — | — |
| 3.3 | Discover Weekly | ⬜ Todo | — | — | — |
| 3.4 | Search V2 | ⬜ Todo | — | — | — |
| 3.5 | Year in Review | ⬜ Todo | — | — | — |
| 4.1 | Verified Badge | ⬜ Todo | — | — | — |
| 4.2 | Artist Analytics | ⬜ Todo | — | — | — |
| 4.3 | Pre-Save / Scheduling | ⬜ Todo | — | — | — |
| 4.4 | Multi-Artist Tracks | ⬜ Todo | — | — | — |
| 4.5 | Bulk Upload | ⬜ Todo | — | — | — |
| 5.1 | Payment Gateway | ⬜ Todo | — | — | — |
| 5.2 | Automated Withdrawals | ⬜ Todo | — | — | — |
| 5.3 | Tipping | ⬜ Todo | — | — | — |
| 5.4 | Tier Plans | ⬜ Todo | — | — | — |
| 6.x | Social features | ⬜ Todo | — | — | — |
| 7.x | Operations | ⬜ Todo | — | — | — |
| 8.x | Growth | ⬜ Todo | — | — | — |
