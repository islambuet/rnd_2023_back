<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemHistoryMobileSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_MOBILE_SMS_HISTORIES, function (Blueprint $table) {
            $table->increments('id');
            $table->string('sender_id',20)->nullable();
            $table->text('contacts')->nullable();
            $table->text('msg')->nullable();
            $table->string('status_http',5)->nullable();
            $table->text('status_sms')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(TABLE_MOBILE_SMS_HISTORIES);
    }
}
