<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_fa');
            $table->longText('description')->nullable();
            $table->tinyInteger('tax')->default(1);
            $table->unsignedBigInteger('parent')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('parent')->references('id')->on('expenses_categories')->nullOnDelete();
        });

        Schema::create('expenses_slip', function (Blueprint $table) {
            $table->id();
            $table->integer('slip_no');
            $table->string('paid_by');
            $table->string('paid_to');
            $table->unsignedBigInteger('po_id')->nullable();
            $table->date('date');
            $table->longText('file');
            $table->longText('remarks')->nullable();
            $table->unsignedBigInteger('category')->nullable();
            $table->unsignedBigInteger('cashier');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('category')->references('id')->on('expenses_categories')->nullOnDelete();
            $table->foreign('cashier')
                ->references('id')
                ->on('users');
            // Foreign key for po_id will be added in purchase_order migration after purchase_order table is created
        });

        Schema::create('expenses_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slip_id');
            $table->longText('expense_description');
            $table->double('amount');
            $table->integer('quantity');
            $table->longText('remarks')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('slip_id')->references('id')->on('expenses_slip')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses_items');
        Schema::dropIfExists('expenses_slip');
        Schema::dropIfExists('expenses_categories');
    }
}
