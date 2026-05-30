<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSettings extends Command
{
    protected $signature = 'migrate:settings';
    protected $description = 'Import settings (name, description, email, currency) from old DeepSound DB';

    public function handle(): int
    {
        $this->info('Connecting to old DeepSound DB...');

        config(['database.connections.mysql_old' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => 'jailaoic_jailaoi',
            'username' => 'jailaoic_jailaoinew',
            'password' => 'Moldo@23',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]]);

        try {
            $rows = DB::connection('mysql_old')->select("SELECT * FROM config");
        } catch (\Exception $e) {
            $this->error('Cannot connect to old DB: ' . $e->getMessage());
            $this->line('');
            $this->line('On live server, the old DB is: jailaoic_jailaoi');
            $this->line('Make sure the DB credentials in config/database.php match.');
            return Command::FAILURE;
        }

        $mapping = [
            'name' => 'app_name',
            'description' => 'app_description',
            'email' => 'email',
            'currency' => 'currency',
            'currency_symbol' => 'currency_code',
        ];

        $oldValues = [];
        foreach ($rows as $row) {
            $oldValues[$row->name] = $row->value;
        }

        $updated = 0;
        foreach ($mapping as $oldKey => $newKey) {
            if (isset($oldValues[$oldKey]) && $oldValues[$oldKey] !== '') {
                $exists = DB::table('tbl_general_setting')->where('key', $newKey)->first();
                if ($exists) {
                    DB::table('tbl_general_setting')->where('key', $newKey)->update([
                        'value' => $oldValues[$oldKey],
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('tbl_general_setting')->insert([
                        'key' => $newKey,
                        'value' => $oldValues[$oldKey],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $this->line("  $oldKey → $newKey = " . $oldValues[$oldKey]);
                $updated++;
            }
        }

        $this->info("Done! $updated settings imported.");

        return Command::SUCCESS;
    }
}
