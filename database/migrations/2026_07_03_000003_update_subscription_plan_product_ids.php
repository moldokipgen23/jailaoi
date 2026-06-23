<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $updates = [
        [
            'old' => 'jailaoi_individual_monthly',
            'android' => 'com.jailaoi.android.monthly',
            'ios'     => 'com.jailaoi.ios.monthly',
        ],
        [
            'old' => 'jailaoi_individual_quarterly',
            'android' => 'com.jailaoi.android.quarterly',
            'ios'     => 'com.jailaoi.ios.quarterly',
        ],
        [
            'old' => 'jailaoi_individual_annual',
            'android' => 'com.jailaoi.android.annual',
            'ios'     => 'com.jailaoi.ios.annual',
        ],
        [
            'old' => 'jailaoi_family_monthly',
            'android' => 'com.jailaoi.android.familymonthly',
            'ios'     => 'com.jailaoi.ios.familymonthly',
        ],
        [
            'old' => 'jailaoi_family_annual',
            'android' => 'com.jailaoi.android.familyannual',
            'ios'     => 'com.jailaoi.ios.familyannual',
        ],
    ];

    public function up(): void
    {
        foreach ($this->updates as $u) {
            DB::table('tbl_package')
                ->where('android_product_package', $u['old'])
                ->update([
                    'android_product_package' => $u['android'],
                    'ios_product_package'     => $u['ios'],
                ]);
        }
    }

    public function down(): void
    {
        foreach ($this->updates as $u) {
            DB::table('tbl_package')
                ->where('android_product_package', $u['android'])
                ->update([
                    'android_product_package' => $u['old'],
                    'ios_product_package'     => $u['old'],
                ]);
        }
    }
};
