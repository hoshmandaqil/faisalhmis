<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyOnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses_slip', function (Blueprint $table) {
            $table->dropForeign(['po_id']);

            // Add the new foreign key constraint
            $table->foreign('po_id')
                  ->references('id')
                  ->on('purchase_order')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses_slip', function (Blueprint $table) {
            $table->dropForeign(['po_id']);  // drop the new foreign key
            
            $table->foreign('po_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->onDelete('cascade');
        });
    }
}
