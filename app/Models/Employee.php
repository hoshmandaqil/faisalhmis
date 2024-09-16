<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['opd_percentage', 'ipd_percentage', 'ipd_amount', 'opd_amount', 'opd_tax', 'ipd_tax'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employeeCurrentSalary()
    {
        return $this->hasOne(Salary::class, 'emp_id')->latest();
    }

    public function labPercentage()
    {
        return $this->belongsToMany(MainLabDepartment::class)->withPivot('percentage', 'tax');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id', 'user_id');
    }

    public function ipd()
    {
        return $this->hasManyThrough(PatientIPD::class, Patient::class, 'doctor_id', 'patient_id', 'user_id', 'id');
    }

    public function laboratoryTests()
    {
        return $this->hasManyThrough(LaboratoryPatientLab::class, Patient::class, 'doctor_id', 'patient_id', 'user_id', 'id');
    }
}
