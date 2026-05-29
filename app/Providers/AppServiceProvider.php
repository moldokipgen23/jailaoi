<?php

namespace App\Providers;

use App\Models\Storage_Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Set dynamic AWS S3 config from DB/cache
        $storage = Cache::rememberForever('s3_storage_settings', function () {
            return Storage_Setting::first();
        });

        if ($storage) {
            Config::set('filesystems.disks.s3', [
                'driver' => 's3',
                'key'    => $storage['s3_access_key'],
                'secret' => $storage['s3_secret_key'],
                'region' => $storage['s3_region'],
                'bucket' => $storage['s3_bucket_name'],
                'use_path_style_endpoint' => false,
                'throw' => false,
            ]);
        }
    }
}
