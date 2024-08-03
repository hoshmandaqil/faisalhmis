<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->double('quantity');
            $table->double('price');
            $table->double('total_price');
            $table->date('date');
            $table->string('files')->nullable();
            $table->longText('comment')->nullable();
            $table->integer('approved')->default(0)->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('checked_by')->nullable();
            $table->integer('verified_by')->nullable();
            $table->integer('rejected_by')->nullable();
            $table->integer('approved_by')->nullable();
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
        Schema::dropIfExists('purchase_orders');
    }
}
