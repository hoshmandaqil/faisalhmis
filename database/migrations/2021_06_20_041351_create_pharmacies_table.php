<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePharmaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('supplier_id');
            $table->double('quantity')->nullable();
            $table->string('barcode')->nullable();
            $table->double('purchase_qty')->nullable();
            $table->double('purchase_price')->nullable();
            $table->double('sale_percentage')->nullable();
            $table->double('sale_price')->nullable();
            $table->string('vendor')->nullable();
            $table->longText('remark')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->boolean('returned')->default(false); 
            $table->unsignedBigInteger('returned_by')->nullable(); 
            $table->boolean('expired')->default(false);
            $table->unsignedBigInteger('expired_by')->nullable(); 
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('medicine_id')->references('id')->on('medicine_names')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('expired_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pharmacies');
    }
}
