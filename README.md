# Jailaoi - Radio Streaming App (Backend)

Laravel backend for the Jailaoi radio streaming application. Provides API for the Flutter app, admin panel, and artist web dashboard.

## Features
- Admin panel at `/admin` (users, songs, categories, artists, settings)
- Artist web dashboard at `/artist` (song upload, stats, management)
- REST API for Flutter mobile app
- Role-based auth: user / artist / admin

## Deployment

### 1. Upload to cPanel
Upload all files (excluding `vendor/`, `.env`, `node_modules/`, `storage/app/public/`) to your web root.

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configure Environment
Copy `.env.example` to `.env` and update:
```
APP_URL=https://yourdomain.com
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```
Generate app key:
```bash
php artisan key:generate
```

### 4. Storage Link
```bash
php artisan storage:link
```

### 5. Migrate Database
```bash
php artisan migrate --force
```

### 6. Import Old Data (if migrating from DeepSound)
```bash
php artisan migrate:old-data --old-root=/path/to/old/project
```

### 7. Create Test Artist (optional)
```bash
php artisan test:artist
```

### 8. Configure App Settings
Login to `/admin` → App Settings to set:
- Logo & favicon
- App name & description
- Social links
- SMTP mail settings
- Google/Facebook login credentials

## Login URLs

| Panel    | URL            | Credentials                   |
|----------|----------------|-------------------------------|
| Admin    | `/admin/login` | Set via admin seeder          |
| Artist   | `/artist/login`| Created via `test:artist` cmd |
| API      | `/api/*`       | Via Flutter app login         |

## Tech Stack
- Laravel 8
- MySQL
- Bootstrap 4 (admin panel)
- Toaster, Select2, Summernote, Chart.js
