# AGENTS.md — Instructions for AI agents

## Project
**JailaOi** — Multi-media platform (video, music, reels, radio, podcasts, live streaming) with admin panel + Flutter app.
Rebranded from DTTube.

## Structure
- `Jailaoi admin/` — **Git repo root** for Laravel backend
- `Jailaoi admin/flutter_app/` — Flutter cross-platform app (copied from dttube/)
- `Jailaoi Old/` — Old DeepSound code (jailaoi.com), data to migrate
- `dttube/` — DELETED (was old copy, Flutter moved to Jailaoi admin/flutter_app/)

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

## Live Server (cPanel Deploy)
**Host**: terra (cPanel terminal, no SSH)
**User**: jailaoic
**Paths**:
- `~/m.jailaoi.com/` — Laravel backend (jailaoi.com)
- `~/public_html/` — DeepSound (jailaoi.com) — DO NOT TOUCH

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
5. **Clear cache**:
   ```bash
   /opt/alt/php84/usr/bin/php -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo.so -d extension=/opt/alt/php84/usr/lib64/php/modules/pdo_mysql.so -d extension=/opt/alt/php84/usr/lib64/php/modules/dom.so artisan config:clear
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

## Existing Tools
- `socket.js` — Node.js socket.io server (port 3002) for live streaming
- `db/dt_tube.sql` — Full database dump

## Migration Script
- `php artisan migrate:deepsound` — migrates from `jailaoi_old` → `jailaoi_tube`
- Config: `config/database.php` connection `mysql_deepsound`; `.env` vars `DB_OLD_*`
- Migrates: users, songs, playlists, likes, comments, followers, views, history

## To-Do List
- [x] DeepSound → JailaOi data migration (users, songs, playlists)
- [ ] Copy media files from live DeepSound (`~/public_html/upload/`) to local `storage/app/public/`
- [ ] Set up socket.io on live: `pm2 start socket.js --name jailaoi-socket`
- [ ] Test Flutter app connected to live API
- [ ] Set up SSL for m.jailaoi.com
