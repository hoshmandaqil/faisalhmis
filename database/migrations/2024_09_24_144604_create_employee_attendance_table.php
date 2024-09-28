<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('attendances');
        Schema::enableForeignKeyConstraints();

        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->integer('emp_no');
            $table->integer('ac_no');
            $table->string('name');
            $table->date('date');
            $table->string('timetable');
            $table->time('on_duty');
            $table->time('off_duty');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('normal')->nullable();
            $table->integer('real_time')->nullable();
            $table->time('late')->nullable();
            $table->time('early')->nullable();
            $table->boolean('absent')->nullable();
            $table->time('ot_time')->nullable();
            $table->time('work_time')->nullable();
            $table->boolean('must_c_in')->nullable();
            $table->boolean('must_c_out')->nullable();
            $table->string('department')->nullable();
            $table->integer('ndays')->nullable();
            $table->boolean('weekend')->nullable();
            $table->boolean('holiday')->nullable();
            $table->time('att_time')->nullable();
            $table->decimal('ndays_ot')->nullable();
            $table->tinyText('comment')->nullable();
            $table->unsignedBigInteger('commented_by')->nullable();
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('commented_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_attendance');
    }
};
