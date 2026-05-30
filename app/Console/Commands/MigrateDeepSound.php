<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateDeepSound extends Command
{
    protected $signature = 'migrate:deepsound
        {--old= : Old DeepSound database connection name}
        {--new= : New JailaOi database connection name}';

    protected $description = 'Migrate data from DeepSound DB to JailaOi DB';

    protected array $userMap = [];
    protected array $contentMap = [];
    protected array $categoryMap = [];
    protected string $oldConn;
    protected string $newConn;

    public function handle(): int
    {
        $this->oldConn = $this->option('old') ?? 'mysql_deepsound';
        $this->newConn = $this->option('new') ?? 'mysql';

        $this->info('Starting DeepSound → JailaOi migration...');

        $this->migrateCategories();
        $this->migrateUsers();
        $this->migrateSongsToContent();
        $this->migratePlaylists();
        $this->migrateLikes();
        $this->migrateComments();
        $this->migrateFollowers();
        $this->migrateViews();
        $this->migrateHistory();

        $this->newLine();
        $this->info('Migration complete!');
        $this->table(
            ['Table', 'Records Migrated'],
            [
                ['Users', count($this->userMap)],
                ['Songs', $this->getCount('tbl_content', 'content_type', 2)],
                ['Playlists', $this->getCount('tbl_content', 'content_type', 5)],
                ['Categories', $this->getCount('tbl_category', '', null)],
            ]
        );

        return Command::SUCCESS;
    }

    protected function old(string $query): array
    {
        return DB::connection($this->oldConn)->select($query);
    }

    protected function newInsert(string $table, array $data): void
    {
        DB::connection($this->newConn)->table($table)->insert($data);
    }

    protected function getCount(string $table, string $column = '', mixed $value = null): int
    {
        $q = DB::connection($this->newConn)->table($table);
        if ($column && $value !== null) {
            $q->where($column, $value);
        }
        return $q->count();
    }

    protected function migrateCategories(): void
    {
        $this->info('Migrating categories...');
        $oldCategories = $this->old('SELECT * FROM categories');

        $insert = [];
        foreach ($oldCategories as $cat) {
            $newId = DB::connection($this->newConn)->table('tbl_category')->insertGetId([
                'name' => $cat->cateogry_name,
                'image' => '',
                'sort_order' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->categoryMap[$cat->id] = $newId;
        }

        $this->info('Migrated ' . count($oldCategories) . ' categories');
    }

    protected function migrateUsers(): void
    {
        $this->info('Migrating users...');
        $bar = $this->output->createProgressBar(11226);
        $bar->start();

        $chunkSize = 500;
        $offset = 0;
        $total = 0;

        while (true) {
            $users = $this->old("SELECT * FROM users LIMIT $chunkSize OFFSET $offset");
            if (empty($users)) break;

            foreach ($users as $user) {
                $channelId = Str::random(8);
                $password = $user->password ?? '';
                if (!empty($password) && !str_starts_with($password, '$2y$') && !str_starts_with($password, '$2a$')) {
                    $password = bcrypt($password);
                }

                $newId = DB::connection($this->newConn)->table('tbl_user')->insertGetId([
                    'channel_id' => $channelId,
                    'channel_name' => $user->username ? '@' . $user->username : '@user_' . $user->id,
                    'full_name' => $user->name ?: $user->username,
                    'email' => $user->email ?: '',
                    'password' => $password,
                    'country_code' => '',
                    'mobile_number' => '',
                    'country_name' => '',
                    'type' => 4,
                    'image_storage_type' => 1,
                    'image' => $this->transformPath($user->avatar, 'user'),
                    'cover_img_storage_type' => 1,
                    'cover_img' => $this->transformPath($user->cover, 'user'),
                    'description' => $user->about ?? '',
                    'device_type' => 0,
                    'device_token' => '',
                    'website' => $user->website ?? '',
                    'facebook_url' => $user->facebook ?? '',
                    'instagram_url' => $user->instagram ?? '',
                    'twitter_url' => $user->twitter ?? '',
                    'wallet_balance' => (int)($user->wallet ?? 0),
                    'wallet_earning' => 0,
                    'is_account_verify' => $user->verified ?? 0,
                    'bank_name' => '',
                    'bank_code' => '',
                    'bank_address' => '',
                    'ifsc_no' => '',
                    'account_no' => '',
                    'front_id_proof_storage_type' => 0,
                    'front_id_proof' => '',
                    'back_id_proof_storage_type' => 0,
                    'back_id_proof' => '',
                    'address' => '',
                    'city' => '',
                    'state' => '',
                    'country' => '',
                    'pincode' => 0,
                    'user_penal_status' => 0,
                    'reference_code' => '',
                    'push_notification_status' => 1,
                    'send_mail_status' => 1,
                    'status' => $user->active ?? 1,
                    'role' => ($user->artist ?? 0) ? 'artist' : 'user',
                    'bio' => $user->about ?? '',
                    'created_at' => $user->time ? date('Y-m-d H:i:s', $user->time) : now(),
                    'updated_at' => now(),
                ]);

                $this->userMap[$user->id] = [
                    'new_id' => $newId,
                    'channel_id' => $channelId,
                ];
                $total++;
            }

            $offset += $chunkSize;
            $bar->advance(min($chunkSize, count($users)));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Migrated $total users");
    }

    protected function migrateSongsToContent(): void
    {
        $this->info('Migrating songs to content...');
        $songs = $this->old('SELECT * FROM songs');
        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();

        $insert = [];
        foreach ($songs as $song) {
            $channelId = $this->userMap[$song->user_id]['channel_id'] ?? 'deleted';
            $categoryId = $this->categoryMap[$song->category_id] ?? 1;

            $insert[] = [
                'content_type' => 2,
                'channel_id' => $channelId,
                'category_id' => $categoryId,
                'language_id' => 1,
                'hashtag_id' => $song->tags ?? '',
                'title' => $song->title ?: 'Untitled',
                'description' => $song->description ?? '',
                'portrait_img' => $this->transformPath($song->thumbnail, 'content'),
                'landscape_img' => $this->transformPath($song->thumbnail, 'content'),
                'content_upload_type' => 'server_video',
                'content' => $this->transformPath($song->audio_location, 'content'),
                'content_duration' => $this->parseDuration($song->duration),
                'is_comment' => 1,
                'is_download' => $song->allow_downloads ?? 1,
                'is_like' => 1,
                'total_view' => $song->views ?? 0,
                'total_like' => $this->getSongLikeCount($song->id),
                'status' => 1,
                'created_at' => $song->time ? date('Y-m-d H:i:s', $song->time) : now(),
                'updated_at' => now(),
            ];

            $bar->advance();
        }

        foreach (array_chunk($insert, 100) as $chunk) {
            DB::connection($this->newConn)->table('tbl_content')->insert($chunk);
        }

        $bar->finish();
        $this->newLine();

        $newContents = DB::connection($this->newConn)
            ->table('tbl_content')
            ->where('content_type', 2)
            ->orderBy('id')
            ->get();

        foreach ($newContents as $i => $content) {
            $this->contentMap[$songs[$i]->id] = $content->id;
        }

        $this->info('Migrated ' . count($songs) . ' songs');
    }

    protected function getSongLikeCount(int $songId): int
    {
        $result = DB::connection($this->oldConn)->select(
            "SELECT COUNT(*) as count FROM likes WHERE track_id = ? AND comment_id = 0",
            [$songId]
        );
        return $result[0]->count ?? 0;
    }

    protected function migratePlaylists(): void
    {
        $this->info('Migrating playlists...');
        $playlists = $this->old('SELECT * FROM playlists');
        $bar = $this->output->createProgressBar(count($playlists));
        $bar->start();

        foreach ($playlists as $playlist) {
            $channelId = $this->userMap[$playlist->user_id]['channel_id'] ?? 'deleted';

            $playlistContentId = DB::connection($this->newConn)->table('tbl_content')->insertGetId([
                'content_type' => 5,
                'channel_id' => $channelId,
                'category_id' => 1,
                'language_id' => 1,
                'hashtag_id' => '',
                'title' => $playlist->name ?: 'Untitled Playlist',
                'description' => '',
                'portrait_img' => $this->transformPath($playlist->thumbnail, 'content'),
                'landscape_img' => $this->transformPath($playlist->thumbnail, 'content'),
                'content_upload_type' => '',
                'content' => '',
                'content_duration' => 0,
                'playlist_type' => $playlist->privacy == 0 ? 1 : 2,
                'is_comment' => 1,
                'is_download' => 0,
                'is_like' => 1,
                'total_view' => 0,
                'total_like' => 0,
                'status' => 1,
                'created_at' => $playlist->time ? date('Y-m-d H:i:s', $playlist->time) : now(),
                'updated_at' => now(),
            ]);

            $playlistSongs = DB::connection($this->oldConn)->select(
                "SELECT * FROM playlist_songs WHERE playlist_id = ?",
                [$playlist->id]
            );

            foreach ($playlistSongs as $ps) {
                $newContentId = $this->contentMap[$ps->track_id] ?? null;
                if (!$newContentId) continue;

                DB::connection($this->newConn)->table('tbl_playlist_content')->insert([
                    'channel_id' => $channelId,
                    'playlist_id' => $playlistContentId,
                    'content_type' => 2,
                    'content_id' => $newContentId,
                    'sort_order' => 0,
                    'status' => 1,
                    'created_at' => $ps->time ? date('Y-m-d H:i:s', $ps->time) : now(),
                    'updated_at' => now(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migrated ' . count($playlists) . ' playlists');
    }

    protected function migrateLikes(): void
    {
        $this->info('Migrating likes...');
        DB::connection($this->newConn)->statement('SET FOREIGN_KEY_CHECKS=0');

        $chunkSize = 500;
        $offset = 0;
        $total = 0;

        while (true) {
            $likes = $this->old("SELECT * FROM likes LIMIT $chunkSize OFFSET $offset");
            if (empty($likes)) break;

            $insert = [];
            foreach ($likes as $like) {
                $newUserId = $this->userMap[$like->user_id]['new_id'] ?? null;
                $newContentId = $this->contentMap[$like->track_id] ?? null;
                if (!$newUserId || !$newContentId) continue;

                $insert[] = [
                    'user_id' => $newUserId,
                    'content_type' => 2,
                    'content_id' => $newContentId,
                    'episode_id' => 0,
                    'status' => 1,
                    'created_at' => $like->time ? date('Y-m-d H:i:s', $like->time) : now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($insert)) {
                foreach (array_chunk($insert, 100) as $chunk) {
                    DB::connection($this->newConn)->table('tbl_content_like')->insert($chunk);
                }
            }

            $total += count($likes);
            $offset += $chunkSize;
        }

        DB::connection($this->newConn)->statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info("Migrated likes");
    }

    protected function migrateComments(): void
    {
        $this->info('Migrating comments...');
        $comments = $this->old('SELECT * FROM comments');

        $insert = [];
        foreach ($comments as $comment) {
            $newUserId = $this->userMap[$comment->user_id]['new_id'] ?? null;
            $newContentId = $this->contentMap[$comment->track_id] ?? null;
            if (!$newUserId || !$newContentId) continue;

            $insert[] = [
                'comment_id' => 0,
                'user_id' => $newUserId,
                'content_type' => 2,
                'content_id' => $newContentId,
                'episode_id' => 0,
                'comment' => $comment->value ?? '',
                'status' => 1,
                'created_at' => $comment->time ? date('Y-m-d H:i:s', $comment->time) : now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($insert, 100) as $chunk) {
            DB::connection($this->newConn)->table('tbl_comment')->insert($chunk);
        }

        $this->info('Migrated ' . count($insert) . ' comments');
    }

    protected function migrateFollowers(): void
    {
        $this->info('Migrating followers...');
        $followers = $this->old('SELECT * FROM followers');

        $insert = [];
        foreach ($followers as $f) {
            $newUserId = $this->userMap[$f->follower_id]['new_id'] ?? null;
            $newToUserId = $this->userMap[$f->following_id]['new_id'] ?? null;
            if (!$newUserId || !$newToUserId) continue;

            $insert[] = [
                'user_id' => $newUserId,
                'to_user_id' => $newToUserId,
                'status' => 1,
                'created_at' => $f->time ? date('Y-m-d H:i:s', $f->time) : now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($insert, 100) as $chunk) {
            DB::connection($this->newConn)->table('tbl_subscriber')->insert($chunk);
        }

        $this->info('Migrated ' . count($insert) . ' followers');
    }

    protected function migrateViews(): void
    {
        $this->info('Migrating view counts...');
        $views = $this->old('SELECT track_id, COUNT(*) as cnt FROM views GROUP BY track_id');

        foreach ($views as $view) {
            $newContentId = $this->contentMap[$view->track_id] ?? null;
            if (!$newContentId) continue;

            DB::connection($this->newConn)->table('tbl_content')
                ->where('id', $newContentId)
                ->increment('total_view', (int)$view->cnt);
        }

        $this->info('Migrated view counts for ' . count($views) . ' tracks');
    }

    protected function migrateHistory(): void
    {
        $this->info('Migrating listen history...');
        $activities = $this->old("SELECT * FROM activities WHERE type = 'uploaded_track' OR type = 'liked_track'");

        $insert = [];
        foreach ($activities as $act) {
            $newUserId = $this->userMap[$act->user_id]['new_id'] ?? null;
            $newContentId = $this->contentMap[$act->track_id] ?? null;
            if (!$newUserId || !$newContentId) continue;

            $insert[] = [
                'user_id' => $newUserId,
                'content_type' => 2,
                'content_id' => $newContentId,
                'episode_id' => 0,
                'stop_time' => 0,
                'status' => 1,
                'created_at' => $act->time ? date('Y-m-d H:i:s', $act->time) : now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($insert, 100) as $chunk) {
            DB::connection($this->newConn)->table('tbl_history')->insert($chunk);
        }

        $this->info('Migrated ' . count($insert) . ' history records');
    }

    protected function transformPath(?string $oldPath, string $newFolder): string
    {
        if (!$oldPath || $oldPath === '' || str_contains($oldPath, 'd-avatar') || str_contains($oldPath, 'd-cover') || $oldPath === 'default') {
            return '';
        }

        $parts = explode('/', $oldPath);
        $filename = end($parts);

        if (!$filename || $filename === '') {
            return '';
        }

        return $newFolder . '/' . $filename;
    }

    protected function parseDuration(?string $duration): int
    {
        if (!$duration || $duration === '' || $duration === '0:0') {
            return 0;
        }

        $parts = explode(':', $duration);
        if (count($parts) === 2) {
            return (int)$parts[0] * 60 + (int)$parts[1];
        }
        if (count($parts) === 3) {
            return (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
        }

        return (int)$duration;
    }
}
