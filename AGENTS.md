# AGENTS.md — Instructions for AI agents

## Project
**JailaOi** — Multi-media platform (video, music, reels, radio, podcasts, live streaming) with admin panel + Flutter app.

## Artist System (just added)
- `tbl_artist` + `tbl_artist_requests` tables
- API: `get_artist_list`, `get_artist_profile`, `get_artist_content`, `apply_artist`, `get_artist_request_status`, `follow_artist`, `unfollow_artist`, `get_artist_dashboard`
- Admin: Artist CRUD + Artist Requests approve/reject
- Flutter: artistlist.dart, artistprofile.dart, applyartist.dart

## Key Commands
- **Run dev**: `php artisan serve --port=8000`
- **Tests**: N/A (no tests yet)
- **DB**: MySQL 8.0, database `jailaoi_tube`

## Structure
- `/dttube/backend/` — Laravel backend
- `/dttube/flutter app/` — Flutter cross-platform app
- `/Jailaoi admin/` — Git repo, contains DTTube backend

## Existing Tools/Scripts
- `socket.js` — Node.js socket.io server for live streaming (port 3002)
- `db/dt_tube.sql` — Full database dump
- `public/` — Laravel public dir

## Artist Tables
- `tbl_artist`: id, user_id (FK→users), name, image, bio, status
- `tbl_artist_requests`: id, user_id (FK→users), artist_name, bio, status (pending/approved/rejected), admin_note
- `tbl_user.role`: 'user' or 'artist'
- `tbl_user.bio`: text field for bio
