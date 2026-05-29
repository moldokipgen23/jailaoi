<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;

class MigrateOldData extends Command
{
    protected $signature = 'migrate:old-data
                            {--old-host= : Old database host}
                            {--old-db= : Old database name}
                            {--old-user= : Old database username}
                            {--old-pass= : Old database password}
                            {--old-root= : Absolute path to old project root (for file copying)}
                            {--skip-files : Skip copying audio/image files}
                                {--steps= : Comma-separated steps to run (settings,users,artists,categories,songs,followers,requests,comments,favorites,transactions,playlists,albums,blog,files)}';

    protected $description = 'Migrate data from old DeepSound DB to new DTRadio DB';

    private ?PDO $oldPdo = null;
    private string $oldRoot = '';
    private array $avatarFiles = [];

    public function handle(): int
    {
        $oldHost = $this->option('old-host');
        $oldDb   = $this->option('old-db');
        $oldUser = $this->option('old-user');
        $oldPass = $this->option('old-pass');
        $this->oldRoot = $this->option('old-root') ? rtrim($this->option('old-root'), '/') : '';

        if (!$oldHost || !$oldDb || !$oldUser) {
            $this->error('Missing required options: --old-host, --old-db, --old-user');
            return Command::FAILURE;
        }

        try {
            $this->oldPdo = new PDO(
                "mysql:host={$oldHost};dbname={$oldDb};charset=utf8mb4",
                $oldUser,
                $oldPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (\PDOException $e) {
            $this->error('Failed to connect to old database: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Connected to old database successfully.');
        $steps = $this->parseSteps();

        try {
            if (in_array('categories', $steps)) $this->migrateCategories();
            if (in_array('users', $steps)) $this->migrateUsers();
            if (in_array('artists', $steps)) $this->migrateArtists();
            if (in_array('songs', $steps)) $this->migrateSongs();
            if (in_array('followers', $steps)) $this->migrateFollowers();
            if (in_array('requests', $steps)) $this->migrateArtistRequests();
            if (in_array('transactions', $steps)) $this->migrateTransactions();
            if (in_array('comments', $steps)) $this->migrateComments();
            if (in_array('favorites', $steps)) $this->migrateFavorites();
            if (in_array('settings', $steps)) $this->migrateSettings();
            if (in_array('playlists', $steps)) $this->migratePlaylists();
            if (in_array('albums', $steps)) $this->migrateAlbums();
            if (in_array('blog', $steps)) $this->migrateBlog();
            if (in_array('files', $steps) && !$this->option('skip-files')) $this->copyFiles();
        } catch (\Throwable $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            $this->error('Line: ' . $e->getLine() . ' in ' . $e->getFile());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('Migration completed successfully.');

        return Command::SUCCESS;
    }

    private function parseSteps(): array
    {
        $steps = $this->option('steps');
        if ($steps) {
            return array_map('trim', explode(',', $steps));
        }
        return ['settings', 'users', 'artists', 'categories', 'songs', 'followers', 'requests', 'transactions', 'comments', 'favorites', 'playlists', 'albums', 'blog', 'files'];
    }

    private function extractFilename(string $path): string
    {
        if (empty($path)) return '';
        $base = basename($path);
        return $base;
    }

    private function migrateUsers(): void
    {
        $this->newLine();
        $this->info('Migrating users...');

        $oldUsers = $this->oldPdo->query(
            "SELECT id, username, email, name, password, avatar, about, artist, admin, active, time, instagram, facebook, twitter FROM users"
        )->fetchAll();
        $bar = $this->output->createProgressBar(count($oldUsers));
        $bar->start();

        $existingIds = DB::table('tbl_user')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldUsers as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            $role = 'user';
            if ((int) $old['admin'] > 0) {
                $role = 'admin';
            } elseif ((int) $old['artist'] === 1) {
                $role = 'artist';
            }

            $avatar = $this->extractFilename($old['avatar'] ?? '');
            if ($avatar) {
                $this->avatarFiles[] = $avatar;
            }

            try {
                DB::table('tbl_user')->insert([
                    'id'         => $id,
                    'user_name'  => $old['username'],
                    'full_name'  => $old['name'] ?: $old['username'],
                    'email'      => $old['email'],
                    'password'   => $old['password'],
                    'image'      => $avatar,
                    'role'       => $role,
                    'bio'        => $old['about'] ?? '',
                    'type'       => 4,
                    'status'     => (int) ($old['active'] ?? 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped user ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateArtists(): void
    {
        $this->info('Migrating artists (linking existing + creating for artist users)...');

        // Step 1: Link existing tbl_artist records to tbl_user where user_id is null but name matches
        $artistUsers = $this->oldPdo->query(
            "SELECT id, username, name, avatar, about FROM users WHERE artist = 1"
        )->fetchAll();

        $bar = $this->output->createProgressBar(count($artistUsers));
        $bar->start();

        foreach ($artistUsers as $old) {
            $userId = (int) $old['id'];

            try {
                // Check if an artist record already exists for this user
                $existing = DB::table('tbl_artist')->where('user_id', $userId)->first();
                if ($existing) {
                    $bar->advance();
                    continue;
                }

                // Try to find an existing artist by matching user_id or name
                $artist = DB::table('tbl_artist')
                    ->where('user_id', $userId)
                    ->orWhere(function ($q) use ($userId) {
                        $q->whereNull('user_id')->where('id', $userId);
                    })
                    ->first();

                $image = $this->extractFilename($old['avatar'] ?? '');
                if ($image) {
                    $this->avatarFiles[] = $image;
                }

                if ($artist) {
                    DB::table('tbl_artist')->where('id', $artist->id)->update([
                        'user_id' => $userId,
                        'name'    => $old['username'],
                        'image'   => $image ?: $artist->image,
                        'bio'     => $old['about'] ?? $artist->bio,
                    ]);
                } else {
                    DB::table('tbl_artist')->insert([
                        'id'         => $userId,
                        'user_id'    => $userId,
                        'name'       => $old['name'] ?: $old['username'],
                        'image'      => $image,
                        'bio'        => $old['about'] ?? '',
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                $this->warn("  Skipped artist user ID {$userId}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        // Step 2: For any songs with artist_id pointing to a user, ensure that artist exists
        $songArtistIds = $this->oldPdo->query(
            "SELECT DISTINCT artist_id FROM songs WHERE artist_id IS NOT NULL AND artist_id > 0"
        )->fetchAll(PDO::FETCH_COLUMN);

        foreach ($songArtistIds as $artistId) {
            $artistId = (int) $artistId;
            $existing = DB::table('tbl_artist')->where('id', $artistId)->first();
            if ($existing) continue;

            $oldUser = $this->oldPdo->prepare("SELECT id, username, name, avatar, about FROM users WHERE id = ?");
            $oldUser->execute([$artistId]);
            $old = $oldUser->fetch();

            if ($old) {
                $image = $this->extractFilename($old['avatar'] ?? '');
                if ($image) {
                    $this->avatarFiles[] = $image;
                }
                try {
                    DB::table('tbl_artist')->insert([
                        'id'         => $artistId,
                        'user_id'    => $artistId,
                        'name'       => $old['name'] ?: $old['username'],
                        'image'      => $image,
                        'bio'        => $old['about'] ?? '',
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    $this->warn("  Skipped artist ID {$artistId}: {$e->getMessage()}");
                }
            } else {
                // Artist referenced but no user record - create minimal entry
                try {
                    DB::table('tbl_artist')->insert([
                        'id'     => $artistId,
                        'name'   => "Artist #{$artistId}",
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    $this->warn("  Skipped orphan artist ID {$artistId}: {$e->getMessage()}");
                }
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateCategories(): void
    {
        $this->info('Migrating categories...');

        $oldCategories = $this->oldPdo->query(
            "SELECT id, cateogry_name, background_thumb FROM categories"
        )->fetchAll();
        if (empty($oldCategories)) {
            $this->warn('  No categories to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldCategories));
        $bar->start();

        $existingIds = DB::table('tbl_category')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldCategories as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_category')->insert([
                    'id'         => $id,
                    'name'       => $old['cateogry_name'],
                    'image'      => $this->extractFilename($old['background_thumb'] ?? ''),
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped category ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateSongs(): void
    {
        $this->info('Migrating songs...');

        $oldSongs = $this->oldPdo->query(
            "SELECT id, user_id, audio_id, title, description, tags, thumbnail, availability, time, views, artist_id, album_id, price, duration, audio_location, category_id, registered, size FROM songs"
        )->fetchAll();

        if (empty($oldSongs)) {
            $this->warn('  No songs to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldSongs));
        $bar->start();

        $existingIds = DB::table('tbl_song')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldSongs as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            // Determine artist_id: prefer artist_id from old song, fallback to user_id
            $artistId = (int) ($old['artist_id'] ?: $old['user_id'] ?: 0);

            try {
                DB::table('tbl_song')->insert([
                    'id'               => $id,
                    'artist_id'        => $artistId,
                    'category_id'      => (int) ($old['category_id'] ?: 0),
                    'name'             => $old['title'] ?? 'Untitled',
                    'image'            => $this->extractFilename($old['thumbnail'] ?? ''),
                    'song_upload_type' => 'server_video',
                    'song_url'         => $this->extractFilename($old['audio_location'] ?? ''),
                    'is_premium'       => ((int) ($old['price'] ?? 0) > 0) ? 1 : 0,
                    'total_play'       => (int) ($old['views'] ?? 0),
                    'status'           => ($old['availability'] ?? 1) ? 1 : 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped song ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateFollowers(): void
    {
        $this->info('Migrating followers...');

        $oldFollowers = $this->oldPdo->query(
            "SELECT id, follower_id, following_id, artist_id, time FROM followers"
        )->fetchAll();

        if (empty($oldFollowers)) {
            $this->warn('  No followers to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldFollowers));
        $bar->start();

        $existingIds = DB::table('tbl_followers')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldFollowers as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_followers')->insert([
                    'id'         => $id,
                    'user_id'    => (int) ($old['follower_id'] ?: 0),
                    'artist_id'  => (int) ($old['following_id'] ?: $old['artist_id'] ?: 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped follower ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateArtistRequests(): void
    {
        $this->info('Migrating artist requests...');

        $oldRequests = $this->oldPdo->query(
            "SELECT id, name, website, details, category_id, photo, passport, time, user_id FROM artist_requests"
        )->fetchAll();

        if (empty($oldRequests)) {
            $this->warn('  No artist requests to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldRequests));
        $bar->start();

        $existingIds = DB::table('tbl_artist_requests')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldRequests as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_artist_requests')->insert([
                    'id'          => $id,
                    'user_id'     => (int) ($old['user_id'] ?: 0),
                    'artist_name' => $old['name'] ?? '',
                    'bio'         => $old['details'] ?? '',
                    'status'      => 'pending',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped request ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateTransactions(): void
    {
        $this->info('Migrating transactions...');

        $oldPayments = $this->oldPdo->query(
            "SELECT id, user_id, amount, type, date, via AS payment_method, '' AS transaction_id FROM payments"
        )->fetchAll();

        if (empty($oldPayments)) {
            $this->warn('  No transactions to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldPayments));
        $bar->start();

        $existingIds = DB::table('tbl_transaction')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldPayments as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_transaction')->insert([
                    'id'             => $id,
                    'user_id'        => (int) ($old['user_id'] ?: 0),
                    'package_id'     => 0,
                    'price'          => (float) ($old['amount'] ?: 0),
                    'transaction_id' => $old['transaction_id'] ?? '',
                    'description'    => ($old['type'] ?? '') . ' via ' . ($old['payment_method'] ?? 'unknown'),
                    'expiry_date'    => '',
                    'status'         => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped transaction ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateComments(): void
    {
        $this->info('Migrating comments...');

        $oldComments = $this->oldPdo->query(
            "SELECT id, track_id AS song_id, user_id, value AS comment, time FROM comments"
        )->fetchAll();

        if (empty($oldComments)) {
            $this->warn('  No comments to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldComments));
        $bar->start();

        $existingIds = DB::table('tbl_comment')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldComments as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_comment')->insert([
                    'id'         => $id,
                    'type'       => 1,
                    'user_id'    => (int) ($old['user_id'] ?: 0),
                    'content_id' => (int) ($old['song_id'] ?: 0),
                    'episode_id' => 0,
                    'comment'    => $old['comment'] ?? '',
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped comment ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateFavorites(): void
    {
        $this->info('Migrating favorites...');

        $oldFavorites = $this->oldPdo->query(
            "SELECT id, user_id, track_id AS song_id, time FROM likes"
        )->fetchAll();

        if (empty($oldFavorites)) {
            $this->warn('  No favorites to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldFavorites));
        $bar->start();

        $existingIds = DB::table('tbl_favorite')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldFavorites as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_favorite')->insert([
                    'id'         => $id,
                    'type'       => 1,
                    'user_id'    => (int) ($old['user_id'] ?: 0),
                    'content_id' => (int) ($old['song_id'] ?: 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped favorite ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateSettings(): void
    {
        $this->info('Migrating app settings from old DeepSound config...');

        $oldConfigs = $this->oldPdo->query("SELECT name, value FROM config")->fetchAll();

        if (empty($oldConfigs)) {
            $this->warn('  No settings to migrate.');
            return;
        }

        $map = [
            'name'               => 'app_name',
            'title'              => 'app_desripation',
            'email'              => 'app_email',
            'keyword'            => null, // skip
            'description'        => null, // skip
            'smtp_host'          => null, // handled separately
            'smtp_username'      => null,
            'smtp_password'      => null,
            'smtp_encryption'    => null,
            'smtp_port'          => null,
        ];

        $bar = $this->output->createProgressBar(count($oldConfigs));
        $bar->start();

        $configLookup = [];
        foreach ($oldConfigs as $row) {
            $configLookup[$row['name']] = $row['value'];
        }

        foreach ($oldConfigs as $old) {
            $oldKey = $old['name'];
            $newKey = $map[$oldKey] ?? null;

            if ($newKey) {
                try {
                    DB::table('tbl_general_setting')
                        ->where('key', $newKey)
                        ->update(['value' => $old['value'], 'updated_at' => now()]);
                } catch (\Exception $e) {
                    $this->warn("  Skipped setting {$oldKey}: {$e->getMessage()}");
                }
            }
            $bar->advance();
        }

        // Handle logo separately — copy from old system
        $logoKey = $configLookup['logo_cache'] ?? null;
        if ($logoKey && $this->oldRoot) {
            $this->copyLogoFiles($configLookup);
        }

        // Handle SMTP settings separately
        if (isset($configLookup['smtp_host']) && !empty($configLookup['smtp_host'])) {
            try {
                $smtpExists = DB::table('tbl_smtp_setting')->count();
                if ($smtpExists == 0) {
                    DB::table('tbl_smtp_setting')->insert([
                        'protocol'   => $configLookup['smtp_encryption'] ?? 'ssl',
                        'host'       => $configLookup['smtp_host'] ?? '',
                        'port'       => $configLookup['smtp_port'] ?? '465',
                        'user'       => $configLookup['smtp_username'] ?? '',
                        'pass'       => $configLookup['smtp_password'] ?? '',
                        'from_email' => $configLookup['email'] ?? 'support@jailaoi.com',
                        'from_name'  => $configLookup['name'] ?? 'JailaOi',
                        'status'     => 1,
                    ]);
                    $this->info('  SMTP settings migrated.');
                }
            } catch (\Exception $e) {
                $this->warn('  SMTP migration skipped: ' . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function copyLogoFiles(array $configLookup): void
    {
        // DeepSound stores logo as uploaded files, look for logo.* in upload/photos
        $srcDir = $this->oldRoot . '/upload/photos';
        if (!is_dir($srcDir)) return;

        $dstDir = storage_path('app/public/setting');
        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0755, true);
        }

        $logoFile = null;
        $faviconFile = null;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($srcDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $name = strtolower($file->getFilename());
            if (strpos($name, 'logo') !== false) {
                $logoFile = $file;
            }
            if (strpos($name, 'favicon') !== false) {
                $faviconFile = $file;
            }
        }

        if ($logoFile) {
            $ext = $logoFile->getExtension();
            $newName = 'logo.' . $ext;
            $target = $dstDir . '/' . $newName;
            if (!file_exists($target)) {
                copy($logoFile->getPathname(), $target);
                DB::table('tbl_general_setting')
                    ->where('key', 'app_logo')
                    ->update(['value' => $newName]);
                $this->info('  Logo file copied.');
            }
        }

        if ($faviconFile) {
            $ext = $faviconFile->getExtension();
            $newName = 'favicon.' . $ext;
            $target = $dstDir . '/' . $newName;
            if (!file_exists($target)) {
                copy($faviconFile->getPathname(), $target);
                DB::table('tbl_general_setting')
                    ->where('key', 'app_favicon')
                    ->update(['value' => $newName]);
                $this->info('  Favicon file copied.');
            }
        }
    }

    private function migratePlaylists(): void
    {
        $this->info('Migrating playlists...');

        $oldPlaylists = $this->oldPdo->query(
            "SELECT id, name, user_id, privacy, thumbnail, time FROM playlists"
        )->fetchAll();

        if (empty($oldPlaylists)) {
            $this->warn('  No playlists to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldPlaylists));
        $bar->start();

        $existingIds = DB::table('tbl_playlist')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldPlaylists as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_playlist')->insert([
                    'id'         => $id,
                    'user_id'    => (int) ($old['user_id'] ?: 0),
                    'name'       => $old['name'] ?? '',
                    'privacy'    => (int) ($old['privacy'] ?? 0),
                    'image'      => $this->extractFilename($old['thumbnail'] ?? ''),
                    'plays'      => 0,
                    'status'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped playlist ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Migrate playlist songs
        $this->info('Migrating playlist songs...');
        $oldPlaylistSongs = $this->oldPdo->query(
            "SELECT id, playlist_id, track_id, user_id, time FROM playlist_songs"
        )->fetchAll();

        if (empty($oldPlaylistSongs)) {
            $this->warn('  No playlist songs to migrate.');
            return;
        }

        $bar2 = $this->output->createProgressBar(count($oldPlaylistSongs));
        $bar2->start();

        $existingPsIds = DB::table('tbl_playlist_song')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldPlaylistSongs as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingPsIds)) {
                $bar2->advance();
                continue;
            }

            try {
                DB::table('tbl_playlist_song')->insert([
                    'id'          => $id,
                    'playlist_id' => (int) ($old['playlist_id'] ?: 0),
                    'song_id'     => (int) ($old['track_id'] ?: 0),
                    'user_id'     => (int) ($old['user_id'] ?: 0),
                    'sort_order'  => 0,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped playlist song ID {$id}: {$e->getMessage()}");
            }

            $bar2->advance();
        }

        $bar2->finish();
        $this->newLine(2);
    }

    private function migrateAlbums(): void
    {
        $this->info('Migrating albums...');

        $oldAlbums = $this->oldPdo->query(
            "SELECT id, user_id, title, description, category_id, thumbnail, price, time FROM albums"
        )->fetchAll();

        if (empty($oldAlbums)) {
            $this->warn('  No albums to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldAlbums));
        $bar->start();

        $existingIds = DB::table('tbl_album')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldAlbums as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_album')->insert([
                    'id'          => $id,
                    'user_id'     => (int) ($old['user_id'] ?: 0),
                    'title'       => $old['title'] ?? '',
                    'description' => $old['description'] ?? '',
                    'category_id' => (int) ($old['category_id'] ?: 0),
                    'image'       => $this->extractFilename($old['thumbnail'] ?? ''),
                    'price'       => (float) ($old['price'] ?? 0),
                    'status'      => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped album ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function migrateBlog(): void
    {
        $this->info('Migrating blog posts...');

        $oldBlog = $this->oldPdo->query(
            "SELECT id, title, content, description, category, thumbnail, view, tags, created_at, created_by FROM blog"
        )->fetchAll();

        if (empty($oldBlog)) {
            $this->warn('  No blog posts to migrate.');
            return;
        }

        $bar = $this->output->createProgressBar(count($oldBlog));
        $bar->start();

        $existingIds = DB::table('tbl_blog')->pluck('id')->map(fn($v) => (int) $v)->toArray();

        foreach ($oldBlog as $old) {
            $id = (int) $old['id'];
            if (in_array($id, $existingIds)) {
                $bar->advance();
                continue;
            }

            try {
                DB::table('tbl_blog')->insert([
                    'id'          => $id,
                    'title'       => $old['title'] ?? '',
                    'content'     => $old['content'] ?? '',
                    'description' => $old['description'] ?? '',
                    'image'       => $this->extractFilename($old['thumbnail'] ?? ''),
                    'category'    => (int) ($old['category'] ?? 0),
                    'view'        => (int) ($old['view'] ?? 0),
                    'tags'        => $old['tags'] ?? '',
                    'created_by'  => (int) ($old['created_by'] ?? 0),
                    'status'      => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } catch (\Exception $e) {
                $this->warn("  Skipped blog post ID {$id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function copyFiles(): void
    {
        if (!$this->oldRoot) {
            $this->warn('  --old-root not specified. Skipping file copy.');
            return;
        }

        if (!is_dir($this->oldRoot)) {
            $this->error("  Old root path does not exist: {$this->oldRoot}");
            return;
        }

        $this->info('Copying audio files to song/ folder...');
        $this->copyFilesFlat('upload/audio', 'song');
        $this->info('Copying image files to song/ folder...');
        $this->copyFilesFlat('upload/photos', 'song');

        if ($this->avatarFiles) {
            $avatarFiles = array_unique($this->avatarFiles);
            $photosDir = $this->oldRoot . '/upload/photos';
            foreach (['user', 'artist'] as $folder) {
                $this->info("Copying avatar files to {$folder}/ folder...");
                $dstDir = storage_path('app/public/' . $folder);
                if (!is_dir($dstDir)) {
                    mkdir($dstDir, 0755, true);
                }
                $count = 0;
                foreach ($avatarFiles as $avatar) {
                    $this->copyFileByName($photosDir, $avatar, $dstDir, $count);
                }
                $this->info("    Copied {$count} files to {$folder}/.");
            }
        }

        $this->info('File copy complete.');
    }

    private function copyFilesFlat(string $relativePath, string $targetFolder): void
    {
        $src = $this->oldRoot . '/' . $relativePath;
        if (!is_dir($src)) {
            $this->warn("  Source directory not found: {$src}");
            return;
        }

        $dst = storage_path('app/public/' . $targetFolder);
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $count = 0;
        foreach ($files as $file) {
            if ($file->isFile()) {
                $target = $dst . '/' . $file->getFilename();
                if (!file_exists($target)) {
                    copy($file->getPathname(), $target);
                    $count++;
                }
            }
        }

        $this->info("    Copied {$count} files to {$targetFolder}/.");
    }

    private function copyFileByName(string $srcDir, string $filename, string $dstDir, int &$count): void
    {
        if (empty($filename)) return;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($srcDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                $target = $dstDir . '/' . $filename;
                if (!file_exists($target)) {
                    copy($file->getPathname(), $target);
                    $count++;
                }
                return;
            }
        }
    }
}
