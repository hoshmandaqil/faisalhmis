<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('created_by');
            $table->integer('quantity');
            $table->tinyInteger('status')->default(0);
            $table->longText('remark')->nullable();

            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicine_names')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
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
        Schema::dropIfExists('patient_medicines');
    }
}
