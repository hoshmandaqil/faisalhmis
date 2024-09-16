<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'total_amount',
        'official_days',
        'status',
        'description',
        'approved_by',
        'rejected_by',
        'checked_by',
        'verified_by',
        'approved_date',
        'rejected_date',
        'checked_date',
        'verified_date',
    ];

    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PayrollPayment::class);
    }
}
