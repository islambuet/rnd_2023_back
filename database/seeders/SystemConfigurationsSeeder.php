<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(TABLE_CONFIGURATIONS)->insert([
            [
                'purpose' => SYSTEM_CONFIGURATIONS_SITE_OFF_LINE,
                'description' => 'Making the application go offline.',
                'config_value' => 0,
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'purpose' => SYSTEM_CONFIGURATIONS_OTP_EXPIRE_DURATION,
                'description' => 'OTP expires/Resend OTP in seconds.',
                'config_value' => 600,
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
        ]
        );
    }
}
