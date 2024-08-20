<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_fname')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_generated_id')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('marital_status')->nullable();
            $table->integer('age')->nullable();
            $table->string('blood_group')->nullable();
            $table->integer('OPD_fee');
            $table->integer('advance_pay')->default(0);
            $table->integer('type')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('respiration_rate')->nullable();
            $table->string('pulse_rate')->nullable();
            $table->string('heart_rate')->nullable();
            $table->string('temperature')->nullable();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
            $table->string('mental_state')->nullable();
            $table->longText('medical_history')->nullable();
            $table->longText('va_1')->nullable();
            $table->longText('va_2')->nullable();
            $table->longText('iop_1')->nullable();
            $table->longText('iop_2')->nullable();
            $table->longText('chief_complaint')->nullable();  
            $table->longText('dx')->nullable();  
            $table->date('reg_date')->nullable();
            $table->integer('no_discount')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('doctor_id');

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
        Schema::dropIfExists('patients');
    }
}
