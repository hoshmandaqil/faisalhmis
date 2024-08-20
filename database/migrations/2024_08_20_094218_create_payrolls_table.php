<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->date('payroll_date');
            $table->decimal('total_amount', 10, 2);
            $table->integer('official_days');
            $table->enum('status', ['pending', 'approved', 'rejected',  'checked', 'verified']);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('rejected_date')->nullable();
            $table->date('checked_date')->nullable();
            $table->date('verified_date')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('checked_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('verified_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
