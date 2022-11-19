<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelectedVarieties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_SELECTED_VARIETIES, function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('year');
            $table->integer('variety_id');
            $table->smallInteger('rnd_ordering')->default(0);
            $table->string('season_ids')->default(',');
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
        Schema::dropIfExists(TABLE_SELECTED_VARIETIES);
    }
}
