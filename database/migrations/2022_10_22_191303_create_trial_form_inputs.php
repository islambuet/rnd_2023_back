<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialFormInputs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_TRIAL_FORM_INPUTS, function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->smallInteger('trial_form_id');
            $table->text('options')->nullable();
            $table->string('default')->default('')->nullable();
            $table->enum('mandatory', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->default(SYSTEM_STATUS_NO)->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO)->nullable();
            $table->string('class')->default('')->nullable();
            $table->integer('ordering')->default(9999);
            $table->enum('status', [SYSTEM_STATUS_ACTIVE, SYSTEM_STATUS_INACTIVE, SYSTEM_STATUS_DELETE])->default(SYSTEM_STATUS_ACTIVE)->comment(SYSTEM_STATUS_ACTIVE.','. SYSTEM_STATUS_INACTIVE.','.SYSTEM_STATUS_DELETE);
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
        Schema::dropIfExists(TABLE_TRIAL_FORM_INPUTS);
    }
}
