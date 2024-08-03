<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineName extends Model
{
    use HasFactory;

    public function thisMedicinePharmacy()
    {
        return $this->hasMany(Pharmacy::class, 'medicine_id')->orderBy('created_at', 'desc');
    }

    public function patientPharmacyMedicines()
    {
        return $this->hasMany(PatientPharmacyMedicine::class, 'medicine_id');
    }
}
