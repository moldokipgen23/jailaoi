<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateCategories extends Command
{
    protected $signature = 'category:fix-duplicates';
    protected $description = 'Fix duplicate categories: copy images from dupes (10-18) to originals (1-9), delete unused dupes';

    public function handle(): int
    {
        $this->info('Checking for duplicate categories...');

        $all = DB::table('tbl_category')->orderBy('id')->get();
        $this->table(['ID', 'Name', 'Image', 'Song Count'], $all->map(fn($c) => [
            $c->id,
            $c->name,
            $c->image ? substr($c->image, 0, 40) . '...' : '(empty)',
            DB::table('tbl_content')->where('category_id', $c->id)->count(),
        ]));

        if ($all->count() <= 9) {
            $this->info('No duplicates found (<= 9 categories). Nothing to do.');
            return Command::SUCCESS;
        }

        // Map duplicates: cat 10 is duplicate of cat 1, 11 of 2, etc.
        $duplicateStartId = 10;
        $totalOriginals = 9;

        for ($i = 0; $i < $totalOriginals; $i++) {
            $originalId = $i + 1;
            $duplicateId = $duplicateStartId + $i;

            $dup = DB::table('tbl_category')->where('id', $duplicateId)->first();
            $orig = DB::table('tbl_category')->where('id', $originalId)->first();

            if (!$dup || !$orig) continue;

            if ($dup->image && !$orig->image) {
                $this->info("Copying image from category $duplicateId ({$dup->name}) → $originalId ({$orig->name})");
                DB::table('tbl_category')->where('id', $originalId)->update([
                    'image' => $dup->image,
                    'storage_type' => $dup->storage_type,
                ]);
            } elseif ($dup->image && $orig->image) {
                $this->warn("Original $originalId already has image, skipping copy from $duplicateId");
            }
        }

        // Reassign songs from duplicate categories to originals
        $reassigned = 0;
        for ($i = 0; $i < $totalOriginals; $i++) {
            $originalId = $i + 1;
            $duplicateId = $duplicateStartId + $i;

            $count = DB::table('tbl_content')->where('category_id', $duplicateId)->count();
            if ($count > 0) {
                $this->warn("Reassigning $count songs from category $duplicateId → $originalId");
                DB::table('tbl_content')->where('category_id', $duplicateId)->update(['category_id' => $originalId]);
                $reassigned += $count;
            }
        }

        // Delete duplicate categories (10-18)
        $idsToDelete = range(10, 10 + $totalOriginals - 1);
        $deleted = DB::table('tbl_category')->whereIn('id', $idsToDelete)->delete();
        $this->info("Deleted $deleted duplicate categories");

        $this->newLine();
        $this->info("Done! Reassigned $reassigned songs, deleted $deleted duplicate categories.");

        return Command::SUCCESS;
    }
}
