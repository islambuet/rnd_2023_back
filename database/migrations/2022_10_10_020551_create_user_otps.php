<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOtps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_USER_OTPS, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('otp',6);
            $table->smallInteger('reason');
            $table->timestamp('created_at');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(TABLE_USER_OTPS);
    }
}
