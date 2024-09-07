<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PurchaseOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('purchase_orders');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        Schema::create('purchase_order_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check');
            $table->unsignedBigInteger('verify');
            $table->unsignedBigInteger('approve');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('check')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('verify')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('approve')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('po_by');
            $table->longText('description')->nullable();
            $table->date('date');
            $table->longText('remarks')->nullable();
            $table->unsignedBigInteger('inserted_by');
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->date('checked_date')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->date('verified_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->date('rejected_date')->nullable();
            $table->longText('reject_comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('inserted_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('checked_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('verified_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->longText('description');
            $table->double('amount');
            $table->double('quantity');
            $table->longText('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('po_id')->references('id')->on('purchase_order')->cascadeOnDelete();
        });

        Schema::create('purchase_order_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->longText('file');
            $table->longText('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('po_id')->references('id')->on('purchase_order')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_files');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('purchase_order_settings');
    }
}
