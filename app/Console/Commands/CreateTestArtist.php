<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestArtist extends Command
{
    protected $signature = 'test:artist {--password=test1234}';
    protected $description = 'Create a test artist account for Flutter testing';

    public function handle(): int
    {
        $password = $this->option('password');

        $user = User::where('email', 'testartist@jailaoi.com')->first();
        if ($user) {
            $this->info('Test artist already exists:');
            $this->table(['ID', 'Email', 'Password', 'Role', 'Artist ID'], [[
                $user->id,
                'testartist@jailaoi.com',
                $password,
                $user->role,
                $user->artist?->id ?? 'N/A',
            ]]);
            return Command::SUCCESS;
        }

        $user = User::create([
            'user_name' => 'testartist',
            'full_name' => 'Test Artist',
            'email' => 'testartist@jailaoi.com',
            'password' => Hash::make($password),
            'type' => 4,
            'status' => 1,
            'role' => 'artist',
        ]);

        $artist = Artist::create([
            'user_id' => $user->id,
            'name' => 'Test Artist',
            'bio' => 'This is a test artist account for development',
            'status' => 1,
        ]);

        $this->info('Test artist created!');
        $this->table(['Field', 'Value'], [
            ['Email', 'testartist@jailaoi.com'],
            ['Password', $password],
            ['User ID', $user->id],
            ['Artist ID', $artist->id],
            ['Role', 'artist'],
        ]);

        return Command::SUCCESS;
    }
}
