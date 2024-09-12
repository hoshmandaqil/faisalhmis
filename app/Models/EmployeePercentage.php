<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePercentage extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'main_dep_id', 'percentage', 'tax'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
