<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Smtp;
use Config;

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

        // $smtp = Smtp::first();
        // if (isset($smtp) && $smtp != null && $smtp != false && $smtp['status'] == 1) {

        //     if ($smtp) {
        //         $data = [
        //             'driver' => 'smtp',
        //             'host' => $smtp->host,
        //             'port' => $smtp->port,
        //             'encryption' => 'tls',
        //             'username' => $smtp->user,
        //             'password' => $smtp->pass,
        //             'from' => [
        //                 'address' => $smtp->from_email,
        //                 'name' => env('APP_NAME'),
        //             ]
        //         ];
        //         Config::set('mail', $data);
        //     }
        // }
    }
}
