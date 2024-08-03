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
            $table->unsignedInteger('medicine_id');
            $table->unsignedInteger('supplier_id');
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
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('pharmacies');
    }
}
