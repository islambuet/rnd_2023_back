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
                'description' => 'Making the application go offline.1 for yes',
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
            [
                'purpose' => SYSTEM_CONFIGURATIONS_LOGIN_MOBILE_VERIFICATION,
                'description' => 'Mobile verification for all.1=yes 0 no',
                'config_value' => 0,
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'purpose' => SYSTEM_CONFIGURATIONS_LOGIN_SESSION_EXPIRE_HOURS,
                'description' => 'User Session Expires in hours',
                'config_value' => 25,
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'purpose' => SYSTEM_CONFIGURATIONS_MOBILE_SMS_API_TOKEN,
                'description' => 'SMS system api key',
                'config_value' => '',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'purpose' => SYSTEM_CONFIGURATIONS_UPLOADED_IMAGE_BASEURL,
                'description' => 'base url for uploaded Image',
                'config_value' => 'http://localhost/rnd_2023_upload/public/',
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ],
        ]
        );
    }
}
