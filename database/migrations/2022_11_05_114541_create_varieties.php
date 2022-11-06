<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVarieties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_VARIETIES, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('crop_id');
            $table->string('crop_type_ids')->default(',');
            $table->string('whose', 10)->default('ARM')->comment('ARM,Principal,Competitor');
            $table->integer('principal_id')->nullable();
            $table->integer('competitor_id')->nullable();
            $table->integer('ordering')->default(9999);
            $table->enum('status', [SYSTEM_STATUS_ACTIVE, SYSTEM_STATUS_INACTIVE, SYSTEM_STATUS_DELETE])->default(SYSTEM_STATUS_ACTIVE)->comment(SYSTEM_STATUS_ACTIVE.','. SYSTEM_STATUS_INACTIVE.','.SYSTEM_STATUS_DELETE);
            $table->enum('retrial', [SYSTEM_STATUS_YES, SYSTEM_STATUS_NO])->default(SYSTEM_STATUS_YES)->comment(SYSTEM_STATUS_YES.','. SYSTEM_STATUS_NO);
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
        Schema::dropIfExists(TABLE_VARIETIES);
    }
}
