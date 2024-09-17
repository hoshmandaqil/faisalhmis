<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MiscellaneousIncome extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'miscellaneous_income';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    // public function getDateShamsiAttribute()
    // {
    //     return toShamsi($this->date);
    // }

    /**
     * category: Relation to categories
     *
     * @return $this->belongsTo()
     */
    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class, 'category', 'id');
    }

    /**
     * user
     *
     * @return $this->belongsTo()
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'cashier', 'id');
    }
}
