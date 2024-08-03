<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientMedicine extends Model
{
    use HasFactory;

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineName::class, 'medicine_id');
    }
    
    
    
    public function checkMedicineAlreadySet($patient_id, $pharmacy_id)
    {
        $medicineExisted = PatientPharmacyMedicine::where('patient_id', $patient_id)->where('medicine_id', $pharmacy_id)->first();

        if($medicineExisted != NULL){
            return false;
        }

        return true;
    }

}
