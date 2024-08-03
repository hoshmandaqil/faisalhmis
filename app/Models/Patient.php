<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicines()
    {
        return $this->hasMany(PatientMedicine::class, 'patient_id');
    }

    public function medicine()
    {
        return $this->hasOne(PatientMedicine::class, 'patient_id');
    }

    public function pharmacyMedicines()
    {
        return $this->hasMany(PatientPharmacyMedicine::class, 'patient_id');
    }

    public function ipd()
    {
        return $this->hasOne(PatientIPD::class, 'patient_id')->orderBy('id', 'desc');
    }

    public function ipds()
    {
        return $this->hasMany(PatientIPD::class, 'patient_id')->orderBy('id', 'desc');
    }

    public function labs()
    {
        return $this->hasMany(PatientLab::class, 'patient_id');
    }

    public function laboratoryTests()
    {
        return $this->hasMany(LaboratoryPatientLab::class, 'patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
