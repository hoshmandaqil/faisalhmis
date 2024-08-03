<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientIPDSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_ipds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('bed_id');
            $table->unsignedBigInteger('created_by');
            $table->longText('remark')->nullable();

            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('bed_id')
                ->references('id')
                ->on('floors')
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
        Schema::dropIfExists('patient_ipds');
    }
}
