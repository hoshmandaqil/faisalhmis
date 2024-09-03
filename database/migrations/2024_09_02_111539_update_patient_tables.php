<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePatientTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_ipds', function (Blueprint $table) {
            $table->boolean('status')->default(0)->change();
            $table->unsignedBigInteger('discharged_by')->nullable()->after('discharge_date');
            $table->foreign('discharged_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_ipds', function (Blueprint $table) {
            $table->dropForeign(['discharged_by']);
            $table->dropColumn('discharged_by');
            $table->boolean('status')->default(1)->change();

        });
    }
}
