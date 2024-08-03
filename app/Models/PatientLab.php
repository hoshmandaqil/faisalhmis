<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientLab extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function lab()
    {
        return $this->belongsTo(LabDepartment::class, 'lab_id');
    }
    
    
    public function checkLabAlreadySet($patient_id, $lab_id)
    {
        $labExisted = LaboratoryPatientLab::where('patient_id', $patient_id)->where('lab_id', $lab_id)->first();
        if($labExisted != NULL){
            return false;
        }

        return true;
    }
}
