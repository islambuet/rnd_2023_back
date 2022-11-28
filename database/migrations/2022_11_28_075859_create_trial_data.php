<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_TRIAL_DATA, function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('trial_station_id');
            $table->smallInteger('year');
            $table->smallInteger('season_id');
            $table->integer('trial_form_id');
            $table->integer('variety_id');
            $table->smallInteger('entry_no');
            $table->text('data_1')->nullable();
            $table->text('data_2')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(TABLE_TRIAL_DATA);
    }
}
