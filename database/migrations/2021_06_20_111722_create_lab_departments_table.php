<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_departments', function (Blueprint $table) {
            $table->id();
            $table->string('dep_name');
            $table->integer('price');
            $table->integer('quantity');
            $table->unsignedBigInteger('main_dep_id');
            $table->string('normal_range');
            $table->timestamps();

            // Foreign key
            $table->foreign('main_dep_id')->references('id')->on('main_lab_departments')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_departments');
    }
}
