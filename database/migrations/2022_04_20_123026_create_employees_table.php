<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->text('employee_id');
            $table->text('first_name');
            $table->text('last_name')->nullable();
            $table->text('father_name')->nullable();
            $table->date('dob')->nullable();
            $table->text('nationality')->nullable();
            $table->text('email')->nullable();
            $table->text('phone_number')->nullable();
            $table->text('native_language')->nullable();
            $table->integer('marital_status')->nullable();
            $table->text('position')->nullable();
            $table->integer('gender')->nullable();
            $table->text('tazkira_number')->nullable();
            $table->text('image')->nullable();
            $table->text('department')->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->text('contract_files')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->longText('comment')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->decimal('opd_percentage', 5, 2)->nullable();
            $table->decimal('ipd_percentage', 5, 2)->nullable(); 
            $table->decimal('opd_amount', 10, 2)->nullable(); 
            $table->decimal('ipd_amount', 10, 2)->nullable(); 
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('employees');
    }
}
