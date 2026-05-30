# JailaOi Project Todo List

## ✅ Admin UI Redesign
- [x] Rewrite CSS with modern indigo theme (style.css)
- [x] Redesign sidebar (sidebar.blade.php)
- [x] Redesign header (header.blade.php)
- [x] Redesign layout (page-app.blade.php with sidebar toggle JS)
- [x] Update js.js with toggle handlers
- [x] Redesign login page (login.blade.php)
- [x] Redesign dashboard summary cards (dashboard.blade.php)
- [x] Add CSS fallbacks for all old class patterns (card-color-primary, video-card, page-search, sorting, breadcrumbs, profile-card, custom-tabs, border-card, avatar, etc.)

## ✅ Content Toggles (video_status, reels_status, feed_status)
- [x] Separate toggles for video, reels, feed in App Settings tab
- [x] updateOrCreate for settings saving
- [x] Sidebar hides menu items when toggled off
- [x] API filters: get_video_list, get_reels_list, search_content, get_feed return empty when disabled
- [x] get_music_section skips video/reel sections, get_music_section_detail returns empty

## ✅ Labels & Channel → Artist
- [x] Renamed channel_info, channel_name, channel, channel_:, all_channel, select_channel in label.php

## ✅ Migration
- [x] migrate:deepsound command with --old-db, --old-host, --old-user, --old-pass params
- [x] Migrated 11,226 users, 75 artists, 1,045 songs, 65 playlists, 1,664 likes, 95 comments, 8,228 followers, 2,684 history, 9 categories
- [x] Artists mapped to both tbl_artist and tbl_user.role='artist'
- [x] Channel names use real names, not @username
- [x] Content ownership uses artist_id from songs table

## ✅ Artist Image / DataTable Fixes
- [x] transformPath preserves date subdirectory (YYYY/MM/filename.jpg)
- [x] ArtistController uses query builder (not collection) for DataTables
- [x] Image rendered as <img> tag
- [x] Virtual columns set orderable: false

## ❌ PENDING — Admin Redesign Touch-ups
- [x] Bulk-replace `border-bottom row mb-3` → CSS fallback added (handled globally)
- [x] Update user/dashboard.blade.php stat cards → new stat-card pattern  
- [x] Update earning_dashboard.blade.php stat cards → new stat-card pattern
- [x] Update ads/edit.blade.php stat cards → new stat-card pattern
- [x] Update video-card grid pages (music, video, reels, feed, radio, podcast episodes) → modern card grid

## ❌ PENDING — Media Copy (needs cPanel terminal)
- [ ] Check if audio copy finished: `upload/audio/.` → `storage/app/public/content/` (2739 files, ~14GB)
- [ ] Copy/hardlink: `upload/photos/.` → `user/`, hardlink to `artist/` and `content/`
- [ ] SQL fixes: update DB paths to strip `upload/photos/` and `upload/audio/` prefixes
- [ ] Delete old `~/public_html/upload/` to free ~14GB
- [ ] Re-run migration with fixed transformPath

## ❌ PENDING — Live Server Deploy
- [ ] git pull on server
- [ ] composer install / migrate / cache clear

## ❌ PENDING — Flutter App
- [ ] Update API calls to respect video_status, reels_status, feed_status

## ❌ PENDING — Infrastructure
- [ ] Socket.io: `pm2 start socket.js --name jailaoi-socket`
- [ ] SSL for m.jailaoi.com
