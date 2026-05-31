# JailaOi Project Todo List

## ⚠️ ORIGIN: DeepSound → JailaOi
This project migrated from **DeepSound** (old music platform at jailaoi.com) to **JailaOi** (Laravel at m.jailaoi.com).
- Old DB: `jailaoic_jailaoi` (DeepSound) → New DB: `jailaoic_jailaoinew` (Laravel)
- Old files: `~/public_html/upload/` → New storage: `storage/app/public/{user,artist,content}/`
- Migration command: `php artisan migrate:deepsound --old-db=jailaoic_jailaoi`
- See `AGENTS.md` for full project context.

## ✅ COMPLETED — Admin UI Redesign
- [x] Rewrite CSS with modern indigo theme (style.css)
- [x] Redesign sidebar, header, layout, login page, dashboard
- [x] js.js toggle handlers, CSS fallbacks for all old class patterns
- [x] Stat cards updated: dashboard, earning_dashboard, ads/edit
- [x] Video-card grid pages modernized (music, video, radio, podcast episodes)

## ✅ COMPLETED — Content Toggles
- [x] video_status toggle in App Settings (reels/feed/livestream features removed)
- [x] Admin sidebar hides video when toggled off
- [x] Artist portal blocks video (music only)

## ✅ COMPLETED — Migration
- [x] migrate:deepsound command: 11,226 users, 75 artists, 1,045 songs, 65 playlists, 1,664 likes, 95 comments, 8,228 followers, 2,684 history, 9 categories
- [x] transformPath strips old prefixes, stores clean relative paths

## ✅ COMPLETED — Media Files (on live server)
- [x] Audio: 2,786 files from `upload/audio/` → `storage/app/public/content/`
- [x] Photos: 1,123 files from `upload/photos/` → `storage/app/public/{user,artist,content}/`
- [x] Fixed config/app.php: `image_url` changed to `APP_URL/storage/` (was `/storage/app/public/`) so URLs resolve correctly through symlink
- [x] No SQL path fixes needed — transformPath already stored clean paths

## ✅ COMPLETED — Labels & Fixes
- [x] Channel → Artist rename in label.php
- [x] Artist DataTable fix (query builder, image render, orderable:false)

## ❌ PENDING — Live Server Deploy (run on cPanel)
- [x] git pull origin main ✅ done
- [ ] composer install --no-dev --optimize-autoloader (with -d flags)
- [ ] artisan config:clear (with -d flags)
- [ ] chmod -R 777 storage bootstrap/cache
- [ ] Delete old `~/public_html/upload/` to free ~14GB (optional)

## ❌ PENDING — High Priority Features
- [ ] Albums: table, artist CRUD, assign songs, API
- [ ] Lyrics: column/table, artist input, API, player display
- [ ] Download toggle: per-track allow_download, artist control, API
- [ ] Bulk upload: multi-file, queue with progress
- [ ] Waveform visualization on upload, music player UI
- [ ] Embeddable player: /embed/{track_id}, shareable code

## ❌ PENDING — Monetization
- [ ] Pro subscriptions: plans, payments, ad-free, artist perks
- [ ] Earnings & withdrawals: stream tracking, commission, payouts
- [ ] Referral program: codes, sign-up tracking, rewards

## ❌ PENDING — Infrastructure
- [ ] SSL for m.jailaoi.com

## ❌ PENDING — Flutter App
- [ ] (no longer needed — features removed)

## ❌ PENDING — Medium Priority
- [ ] Import from SoundCloud/YouTube/Deezer
- [ ] User verification badges
- [ ] Comment timestamps (tied to playback position)
- [ ] Two-factor auth (2FA)
- [ ] FAQs management
- [ ] Announcement system
- [ ] Sitemap generation
