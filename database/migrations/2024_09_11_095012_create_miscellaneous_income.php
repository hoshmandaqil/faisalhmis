<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiscellaneousIncome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_fa');
            $table->longText('description')->nullable();
            $table->integer('tax')->default(1);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('miscellaneous_income', function (Blueprint $table) {
            $table->id();
            $table->integer('slip_no');
            $table->string('paid_by');
            $table->string('paid_to');
            $table->date('date');
            $table->longText('income_description');
            $table->double('amount');
            $table->longText('remarks')->nullable();
            $table->unsignedBigInteger('category')->nullable();
            $table->unsignedBigInteger('cashier');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('category')->references('id')->on('income_categories')->nullOnDelete();
            $table->foreign('cashier')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('miscellaneous_income');
        Schema::dropIfExists('income_categories');
    }
}
