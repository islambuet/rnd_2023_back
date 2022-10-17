<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => 'superadmin',
                'password' => Hash::make('12345678'),
                'user_group_id' => 1,
                'user_type_id' => 1,
                'name' => 'Shaiful Islam',
                'email' => 'shaiful@shaiful.me',
                'mobile_no' => '01912097849',
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
            [
                'username' => '0026',
                'password' => Hash::make('12345678'),
                'user_group_id' => 2,
                'user_type_id' => 1,
                'name' => 'Abidur Rahman',
                'email' => 'hoit@malikseeds.com',
                'mobile_no' => '01713090961',
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ],
        ]);
    }
}
