<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_labs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('created_by');
            $table->longText('remark');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('lab_id')
                ->references('id')
                ->on('lab_departments')
                ->onDelete('no action')
                ->onUpdate('no action');
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
        Schema::dropIfExists('patient_labs');
    }
}
