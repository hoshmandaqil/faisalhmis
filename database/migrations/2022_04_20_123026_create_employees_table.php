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
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->longText('comment')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->integer('created_by');
            $table->integer('updated_by');
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
