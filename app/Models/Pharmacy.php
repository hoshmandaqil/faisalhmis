<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    public function patientMedicines()
    {
        return $this->hasMany(PatientMedicine::class, 'pharmacy_id');
    }

    public function medicineName()
    {
        return $this->belongsTo(MedicineName::class, 'medicine_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
