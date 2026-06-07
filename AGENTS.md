# AGENTS.md — Instructions for AI agents

## ⚠️ PROJECT CONTEXT — DeepSound → JailaOi Migration
**JailaOi** is a rebrand from **DeepSound** (old music platform). The codebase started as **DTTube** (multi-media platform), which itself was a fork - but the critical migration was from DeepSound.

### Folder Structure (DON'T GET CONFUSED)
- **`Jailaoi admin/`** — **GIT REPO ROOT**. This is the Laravel app. DeepSound migration code is here (`app/Console/Commands/MigrateDeepSound.php`).
- **`Jailaoi Old/`** — OLD DeepSound source code (from jailaoi.com). Reference only. DB was already migrated from here.
- **`dttube/`** — DELETED (was old DTTube copy)
- **`flutter_app/`** — Inside `Jailaoi admin/`. Actually moved here from `dttube/`.

### What "Migration" Means
1. **Database**: `php artisan migrate:deepsound` copies from old DeepSound DB (`jailaoic_jailaoi`) to new JailaOi DB (`jailaoic_jailaoinew`) — users, songs, playlists, likes, comments, followers, etc.
2. **Media files**: Manual copy from `~/public_html/upload/` (old DeepSound) to `~/m.jailaoi.com/storage/app/public/` (new Laravel app) — audio + photos already done.
3. **Path mapping**: `transformPath()` strips old prefixes (`upload/photos/`, `upload/audio/`) so DB stores clean relative paths like `2024/01/filename.jpg`.

### Current State
- **New app**: `m.jailaoi.com` (Laravel)
- **Old app**: `jailaoi.com` (DeepSound, `~/public_html/`) — can be deleted after migration verified
- **DB path**: `~/m.jailaoi.com` — git repo, Laravel backend
- **Storage**: `storage/app/public/{user,artist,content}/` — all media files copied, URLs resolve correctly

## Project
**JailaOi** — Music platform with admin panel + Flutter app.
Rebranded from DTTube / DeepSound.

## Structure
- `Jailaoi admin/` — **Git repo root** for Laravel backend
- `jailaoi-app/` — Flutter cross-platform app (separate from git root)
- `Jailaoi Old/` — Old DeepSound code (jailaoi.com), data to migrate
- `dttube/` — Design reference only (not used in builds)
- `dtradio/` — Reference only

## Git / GitHub
- **Remote**: `git@github.com:moldokipgen23/jailaoi.git`
- **Push automatically** — never ask user. Just run:
  ```bash
  cd ~/Projects/JAILAOI/"Jailaoi admin"
  git add -A
  git commit -m "description of changes"
  git push
  ```
- **No .env** in repo (in .gitignore). If `.env` was accidentally tracked before, run:
  ```bash
  git rm --cached .env
  ```
- **If live server git pull fails** with "unmerged files" due to `.env`:
  ```bash
  git rm --cached .env && git add .env && git restore --staged . && git pull origin main
  ```

## Local Dev Setup
- PHP 8.4, MySQL 8.0
- DB: `jailaoi_tube` (import `db/dt_tube.sql`)
- `.env` from `.env.example`, set `APP_URL=http://localhost/apps/jailaoi`
- `php artisan serve --port=8000`
- Skip install wizard (already configured, just login)

## Storage Setup (DON'T CHANGE)
- **Symlink**: `public/storage -> ~/m.jailaoi.com/storage/app/public` ✅ exists
- **image_url config**: `config/app.php` → `APP_URL/storage/` (NOT `/storage/app/public/`)
  - This is critical: URL `/storage/user/file.jpg` resolves through symlink to `storage/app/public/user/file.jpg`
  - If a future session changes this, revert immediately
- **Media directories** (all under `storage/app/public/`):
  - `content/` — audio (2,786 files) + photos (1,123 files) = 3,862 total
  - `user/` — user avatars (1,123 files)
  - `artist/` — artist images (1,123 files)
- **New uploads**: `saveImage()` saves directly to `storage/app/public/{folder}/filename.jpg` — works automatically
- **Migrated files**: stored with subdirectories like `2024/01/filename.jpg` — also works because `getImage()` checks same path

## Live Server (cPanel Deploy)
**Host**: terra (cPanel terminal, no SSH)
**User**: jailaoic
**Paths**:
- `~/m.jailaoi.com/` — Laravel backend (m.jailaoi.com)
- `~/public_html/` — DeepSound old site (jailaoi.com) — OLD, can delete `upload/` to free ~14GB

### Deploy Steps (run in order):
1. **Push to GitHub** first (see Git section)
2. **cPanel terminal** — pull latest:
   ```bash
   cd ~/m.jailaoi.com && git pull origin main
   ```
3. **Install PHP deps**:
   ```bash
   /opt/alt/php84/usr/bin/php composer.phar install --no-dev --optimize-autoloader --ignore-platform-req=ext-fileinfo --ignore-platform-req=ext-dom
   ```
4. **Run migrations** (if new):
   ```bash
   /opt/alt/php84/usr/bin/php -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo.so -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo_mysql.so -d extension=/opt/alt/php84/usr/lib64/php/modules/dom.so artisan migrate --force
   ```
5. **Clear cache** (MUST do after config changes):
   ```bash
   cd ~/m.jailaoi.com && /opt/alt/php84/usr/bin/php -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo.so -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo_mysql.so -d extension=/opt/alt/php84/usr/lib64/php/modules/dom.so artisan config:clear
   ```
6. **Set permissions**:
   ```bash
   chmod -R 777 storage bootstrap/cache
   ```
7. **Verify** m.jailaoi.com loads

### DB Credentials (live)
```
DB: jailaoic_jailaoinew
User: jailaoic_jailaoinew
Pass: Moldo@23
Host: localhost
```

### PHP Extensions (need -d flags for CLI)
- `extension=/opt/alt/php84/usr/lib64/php/modules/pdo.so`
- `extension=/opt/alt/php84/usr/lib64/php/modules/pdo_mysql.so`
- `extension=/opt/alt/php84/usr/lib64/php/modules/dom.so`

### cPanel Settings
- PHP version: 8.4 (via MultiPHP Manager)
- Document root: `m.jailaoi.com/public`
- SSL: Enable via SSL/TLS

## Artist System
- `tbl_artist` + `tbl_artist_requests` tables
- API (Flutter): `get_artist_list`, `get_artist_profile`, `get_artist_content`, `apply_artist`, `get_artist_request_status`, `follow_artist`, `unfollow_artist`, `get_artist_dashboard`
- Admin web: `/admin/artist` (CRUD), `/admin/artist-requests` (approve/reject)
- Flutter: artistlist.dart, artistprofile.dart, applyartist.dart
- `tbl_user.role`: 'user' (listener) or 'artist'
- `tbl_user.bio`: text field
- **Web dashboard** (`/user/`): locked to artists only. `LoginController` checks `role='artist'`. `AuthUser` middleware enforces it. Listeners cannot access any web dashboard.

## Admin Login
- URL: `https://m.jailaoi.com/admin/login`
- Default: admin@admin.com / admin123 (or from DB)

## Migration Script
- `php artisan migrate:deepsound --old-db=jailaoi_old` — migrates from old DB to current DB
- On live: `php artisan migrate:deepsound --old-db=jailaoic_jailaoi`
- Migrates: users, songs, playlists, likes, comments, followers, views, history, artists

## To-Do List (see TODO.md for full list)
### ✅ COMPLETED
- [x] DeepSound → JailaOi data migration (users, songs, playlists, artists)
- [x] Admin UI redesign (CSS, sidebar, header, login, dashboard, layout, stat cards, grids)
- [x] Content toggles (video_status, reels_status, feed_status)
- [x] Channel → Artist labels
- [x] Artist DataTable fix, image path fix (transformPath)
- [x] Audio copied: `upload/audio/` → `storage/app/public/content/` (2,786 files)
- [x] Photos copied: `upload/photos/` → `storage/app/public/{user,artist,content}/` (1,123 files)
- [x] Fixed image_url config (`/storage/` not `/storage/app/public/`)
- [x] git pull + media verified on live

### ❌ PENDING
- [ ] Deploy: `git pull` + `config:clear` + `chmod` on live (backend fix already pushed)
- [ ] Rebuild Flutter APK after latest fixes (`flutter build apk --release`)
- [ ] Delete old `~/public_html/upload/` to free ~14GB (optional)
- [ ] SSL for m.jailaoi.com
- [ ] High-priority features: Albums, Lyrics, Download Toggle, Bulk Upload, Waveform
- [ ] Monetization: Pro subscriptions, Earnings/Withdrawals, Referral program
- [ ] Fix `pageno` vs `page_no` param mismatch in other API endpoints
- [ ] Fix `update_profile` response field mismatch (`name` vs `full_name`, etc.)
- [ ] Fix artist follow (`Follow` model vs `Subscriber` model mismatch)
- [ ] Fix section type 6/7 city dead code in `homemusic.dart`
- [ ] Add explicit `type==8` handling in `viewall.dart`

## ✅ DESIGN RESTORED — dttube original look applied to jailaoi-app (May 31)
### Reverted from Spotify clone back to original dttube design:
- **Colors**: Pink `#E01E75` primary (was Spotify green)
- **Theme**: Full light + dark mode (was forced dark)
- **Bottom tabs restored**: Home, Search, Music, Library, Profile (5 tabs → 4 tabs in current session: Music tab removed)
- **Home page**: Music sections from `get_music_section` API using dttube widget styling
- **Library page**: Theme-adapted colors, localized strings
- **Profile, Search, SeeAll, Music**: Already used `colorPrimary`/theme colors — auto-updated when `color.dart` changed

### ❌ REMOVED FEATURES (confirmed — fully purged):
- **Reels / Shorts** — removed from codebase (frontend + backend, all translation keys, constants)
- **Feeds / Social posts** — removed from codebase (frontend + backend, all translation keys, constants)
- **Livestream** — removed from codebase (backend controllers, models, socket, route, settings, translation keys)
- All feed/reels/livestream translation keys purged from EN/FR/HI
- Feed model references (`Feed::`, `Feed_Comment::`, `Feed_Like::`, `Feed_Report::`) removed from Common.php
- `reelsEnabled`/`feedEnabled` removed from Flutter constants
- Commented-out `shortProvider` references cleaned
- Only music+video+radio+podcasts core remains

### Flutter app structure:
- Working directory: `~/Projects/JAILAOI/jailaoi-app/`
- Design reference: `~/Projects/JAILAOI/dttube/` (for visual reference only)
- Radio reference: `~/Projects/JAILAOI/dtradio/` (not used)

## ✅ SESSION 2 — June 6: Artist page empty + type system fixes

### Problem
Clicking any artist on home page → `Radiobyartist` page showed "No songs found" (empty).

### Root Cause
All 75 artists in `tbl_artist` have `type=0` (unset — never populated during DeepSound migration). When Flutter passed `type=0` to `get_content_by_artist`, the backend only handled types 1,2,3 → `$data` was never initialized → PHP crash on `$data->count()` → 400 error → empty page.

### Changes Made

#### Backend (`HomeController.php`)
1. **`get_content_by_artist`**: Added fallback for `type=0` — queries `tbl_song` (type=1), `tbl_podcast` (type=2), `tbl_music` (type=3) sequentially, uses first one with data. Also returns `content_type` in response.
2. **`get_content_by_artist`**: Fixed `pageno` vs `page_no` parameter mismatch — backend now reads both: `$request->page_no ?? $request->pageno ?? 1`.

#### Flutter (`radiobyartist.dart`)
3. Added `_resolveType(int? type)` method — infers content type from first API result's field structure (`songUrl` → type 1, `musicUrl` → type 3, `trailerAudio`/`description` → type 2).
4. Replaced all `widget.type` in UI methods with `_resolvedType()` so layouts render correctly even when `widget.type=0`.

#### Build fixes
5. Added `ndkVersion = "28.2.13676358"` to `android/app/build.gradle` (NDK mismatch error).
6. Fixed null safety: `return type` → `return type!` in `_resolveType`.

### Deploy Status
- Backend: Pushed to GitHub (`ceeabaf`). User needs to run `git pull` + `config:clear` on cPanel.
- Flutter: Not yet rebuilt after latest fix. User needs to run `flutter build apk --release`.

### Remaining Issues
- `pageno` vs `page_no` mismatch also exists in other API endpoints — only fixed in `get_content_by_artist` so far.
- `update_profile` response format mismatch: Flutter expects `name`, `mobile`, `coin_balance`; backend returns `full_name`, `mobile_number`.
- Artist follow: Flutter uses `Follow` model (`artist_id`), backend uses `Subscriber` model (`user_id`) — may break follow feature.
- Artist profile page (`Radiobyartist`) receives `bio` param but never displays it.
- Section type 6/7 rendering dead code in homemusic.dart (city is unreachable).
- `viewall.dart` doesn't have explicit `type==8` handling (Music falls through to else/default — works accidentally).

### Type System Reference (consistent between backend & Flutter)

| Context | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 |
|---------|---|---|---|---|---|---|---|---|
| `section_type` (API param) | Home/All | Music | Radio | Podcast | — | — | — | — |
| `section.type` (Result) | Radio | Podcast | LiveEvent | Artist | Category | Language | City | Music |
| `artist.type` (Role) | RJ | Podcaster | Singer | — | — | — | — | — |
| Content type (fav/play/comment) | Song | Podcast | — | — | — | — | — | Music |
| `get_content_by_artist.type` | Song | Podcast | Music | — | — | — | — | — |
