<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $table = 'expenses_items';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * slip: Relation to the main slip
     *
     * @return object
     */
    public function slip()
    {
        return $this->belongsTo(ExpenseSlip::class, 'slip_id', 'id');
    }
}
