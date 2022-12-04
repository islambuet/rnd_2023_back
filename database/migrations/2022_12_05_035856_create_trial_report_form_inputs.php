<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialReportFormInputs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_TRIAL_REPORT_FORM_INPUTS, function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('trial_form_id');
            $table->string('name');
            $table->string('type');
            $table->enum('source', ['form_input', 'report_form_input'])->default('form_input')->comment('From Input fields,From Report Fields');
            $table->enum('hidden', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->default(SYSTEM_STATUS_NO)->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO)->nullable();
            $table->string('field_ids')->default(',');
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
        Schema::dropIfExists(TABLE_TRIAL_REPORT_FORM_INPUTS);
    }
}
