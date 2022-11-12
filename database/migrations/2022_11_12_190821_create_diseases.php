<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiseases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TABLE_DISEASES, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('crop_id');
            $table->enum('type', ['Disease', 'Pest'])->default('Disease')->comment('Disease,Pest');
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
        Schema::dropIfExists(TABLE_DISEASES);
    }
}
