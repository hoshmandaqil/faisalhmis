<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpenseToEmployeeMainLabDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_main_lab_department', function (Blueprint $table) {
            $table->decimal('expense', 10, 2)->after('tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_main_lab_department', function (Blueprint $table) {
            $table->dropColumn('expense');
        });
    }
}
