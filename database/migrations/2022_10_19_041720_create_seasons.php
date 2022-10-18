<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_SEASONS, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('ordering')->default(9999);
            $table->enum('status', [SYSTEM_STATUS_ACTIVE, SYSTEM_STATUS_INACTIVE, SYSTEM_STATUS_DELETE])->default(SYSTEM_STATUS_ACTIVE)->comment(SYSTEM_STATUS_ACTIVE.','. SYSTEM_STATUS_INACTIVE.','.SYSTEM_STATUS_DELETE);
            $table->timestamp('season_start_date')->nullable();
            $table->timestamp('expected_sowing_start')->nullable();
            $table->timestamp('expected_sowing_end')->nullable();
            $table->timestamp('estimated_delivery_date')->nullable();
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
        Schema::dropIfExists(TABLE_SEASONS);
    }
}
