<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryPatientLab extends Model
{
    use HasFactory;

    protected $table = "laboratory_patient_labs";

    public function testName()
    {
        return $this->belongsTo(LabDepartment::class, 'lab_id');
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
