<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialVarieties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_TRIAL_VARIETIES, function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('trial_station_id');
            $table->smallInteger('year');
            $table->smallInteger('season_id');
            $table->integer('variety_id');
            $table->smallInteger('rnd_ordering')->default(0);
            $table->string('rnd_code')->nullable();

            $table->enum('replica', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO);

            $table->enum('delivery_status', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO);
            $table->timestamp('delivered_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->integer('delivered_by');

            $table->enum('sowing_status', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->default(SYSTEM_STATUS_NO)->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO);
            $table->timestamp('sowing_date')->nullable();
            $table->timestamp('sowing_at')->nullable();
            $table->integer('sowing_by')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(TABLE_TRIAL_VARIETIES);
    }
}
