<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            UserGroupsSeeder::class,
            UserTypesSeeder::class,
            UsersSeeder::class,
            SystemTasksSeeder::class,
            SystemConfigurationsSeeder::class,
        ]);
    }
}
