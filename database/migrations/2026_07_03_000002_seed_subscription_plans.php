<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $plans = [
        [
            'name'                    => 'Monthly Plan',
            'price'                   => '149',
            'type'                    => 'Month',
            'time'                    => null,
            'android_product_package' => 'jailaoi_individual_monthly',
            'ios_product_package'     => 'jailaoi_individual_monthly',
            'web_product_package'     => '',
            'color'                   => '#1DB954',
            'device_limit'            => 1,
            'is_download'             => 1,
            'ads_free'                => 1,
        ],
        [
            'name'                    => 'Quarterly Plan',
            'price'                   => '349',
            'type'                    => 'Quarter',
            'time'                    => '22',
            'android_product_package' => 'jailaoi_individual_quarterly',
            'ios_product_package'     => 'jailaoi_individual_quarterly',
            'web_product_package'     => '',
            'color'                   => '#1DB954',
            'device_limit'            => 1,
            'is_download'             => 1,
            'ads_free'                => 1,
        ],
        [
            'name'                    => 'Annual Plan',
            'price'                   => '999',
            'type'                    => 'Year',
            'time'                    => '44',
            'android_product_package' => 'jailaoi_individual_annual',
            'ios_product_package'     => 'jailaoi_individual_annual',
            'web_product_package'     => '',
            'color'                   => '#1DB954',
            'device_limit'            => 1,
            'is_download'             => 1,
            'ads_free'                => 1,
        ],
        [
            'name'                    => 'Family Monthly',
            'price'                   => '349',
            'type'                    => 'Month',
            'time'                    => null,
            'android_product_package' => 'jailaoi_family_monthly',
            'ios_product_package'     => 'jailaoi_family_monthly',
            'web_product_package'     => '',
            'color'                   => '#3A7BD5',
            'device_limit'            => 5,
            'is_download'             => 1,
            'ads_free'                => 1,
        ],
        [
            'name'                    => 'Family Annual',
            'price'                   => '2499',
            'type'                    => 'Year',
            'time'                    => '40',
            'android_product_package' => 'jailaoi_family_annual',
            'ios_product_package'     => 'jailaoi_family_annual',
            'web_product_package'     => '',
            'color'                   => '#3A7BD5',
            'device_limit'            => 5,
            'is_download'             => 1,
            'ads_free'                => 1,
        ],
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->plans as $plan) {
            $exists = DB::table('tbl_package')
                ->where('android_product_package', $plan['android_product_package'])
                ->exists();

            if (! $exists) {
                DB::table('tbl_package')->insert(array_merge($plan, [
                    'image'      => '',
                    'status'     => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        $ids = [
            'jailaoi_individual_monthly',
            'jailaoi_individual_quarterly',
            'jailaoi_individual_annual',
            'jailaoi_family_monthly',
            'jailaoi_family_annual',
        ];

        DB::table('tbl_package')->whereIn('android_product_package', $ids)->delete();
    }
};
