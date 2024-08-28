<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slip_no',
        'payroll_id',
        'employee_id',
        'payment_date',
        'payment_method',
        'amount',
        'cashier',
        'remarks',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier');
    }
}
