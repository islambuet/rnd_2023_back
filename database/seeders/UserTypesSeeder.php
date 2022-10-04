<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(TABLE_USER_TYPES)->insert([
            [
                'name' => 'Employee',
                'prefix' => '001',
                'created_by' => 1,
                'created_at'=>Carbon::now()
            ]
        ]);
    }
}
