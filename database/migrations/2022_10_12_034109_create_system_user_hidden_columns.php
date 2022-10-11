<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemUserHiddenColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_USER_HIDDEN_COLUMNS, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->mediumText('hidden_columns');
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
        Schema::dropIfExists(TABLE_USER_HIDDEN_COLUMNS);
    }
}
