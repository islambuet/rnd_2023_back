<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time=Carbon::now();
        DB::table(TABLE_TASKS)->insert([
            //1
            [
                'name' => 'System Settings',
                'type' => 'MODULE',
                'parent' => 0,
                'url' => '',
                'ordering' => 1,
                'created_by' => 1,
                'created_at' => $time
            ],
            //2
            [
                'name' => 'Modules & Tasks',
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'module-tasks',
                'ordering' => 1,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //3
            [
                'name' => 'System Configuration',
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'system-configurations',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //4
            [
                'name' => 'User Groups',
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'user-groups',
                'ordering' => 3,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //5
            [
                'name' => 'User Types',
                'type' => 'TASK',
                'parent' => 1,
                'url' => 'user-types',
                'ordering' => 3,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //6
            [
                'name' => 'Setup',
                'type' => 'MODULE',
                'parent' => 0,
                'url' => '',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //7
            [
                'name' => 'Admin Setup',
                'type' => 'MODULE',
                'parent' => 6,
                'url' => '',
                'ordering' => 1,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //8
            [
                'name' => 'Designations',
                'type' => 'TASK',
                'parent' => 7,
                'url' => 'setup-designations',
                'ordering' => 1,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //9
            [
                'name' => 'Principals',
                'type' => 'TASK',
                'parent' => 7,
                'url' => 'setup-principals',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //10
            [
                'name' => 'Competitors',
                'type' => 'TASK',
                'parent' => 7,
                'url' => 'setup-competitors',
                'ordering' => 3,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //11
            [
                'name' => 'Seasons',
                'type' => 'TASK',
                'parent' => 7,
                'url' => 'setup-seasons',
                'ordering' => 4,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //12
            [
                'name' => 'Users',
                'type' => 'TASK',
                'parent' => 0,
                'url' => 'users',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //13
            [
                'name' => 'Crop Classification',
                'type' => 'MODULE',
                'parent' => 6,
                'url' => '',
                'ordering' => 1,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //14
            [
                'name' => 'Crop',
                'type' => 'TASK',
                'parent' => 13,
                'url' => 'setup-crops',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //15
            [
                'name' => 'Crop Types',
                'type' => 'TASK',
                'parent' => 13,
                'url' => 'setup-crop-types',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
            //16
            [
                'name' => 'Variety',
                'type' => 'TASK',
                'parent' => 13,
                'url' => 'setup-varieties',
                'ordering' => 2,
                'created_by' => 1,
                'created_at' => $time,
            ],
        ]);
    }
}
