<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSlip extends Model
{
    use HasFactory;

    protected $table = 'expenses_slip';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * expenses: Relation to expense items in the slip
     *
     * @return object
     */
    public function expenseItems()
    {
        return $this->hasManySync(ExpenseItem::class, 'slip_id', 'id');
    }

    /**
     * category: Relation to categories
     *
     * @return void
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category', 'id');
    }
}
