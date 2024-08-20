<?php

namespace App\Models\Expense;

use App\Http\Traits\HasManySync;
use App\Models\PurchaseOrder;
use App\Models\User;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSlip extends Model
{
    use HasFactory, HasManySync;

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

    /**
     * expenses: Relation to expense items in the slip
     *
     * @return object
     */
    public function expenses()
    {
        return $this->hasManySync(ExpenseItem::class, 'slip_id', 'id');
    }

    /**
     * purchaserOrder
     *
     * @return void
     */
    public function purchaserOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }

    /**
     * cashierUser
     *
     * @return $this->belongsto
     */
    public function cashierUser()
    {
        return $this->belongsTo(User::class, 'cashier', 'id');
    }

    /**
     * Sum paid amount in expenses.
     *
     * @return int
     */
    public function getSumPaidAttribute()
    {
        return $this->expenses->sum(function ($expense) {
            return $expense->amount * $expense->quantity;
        });
    }
}
