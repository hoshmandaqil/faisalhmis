<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PatientPharmacyMedicine extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMedicineDetailFromDoctor($patientId, $medicineId){
        return DB::table('patient_medicines')->where('patient_id',$patientId)
            ->where('medicine_id', $medicineId)->select('remark')->value('remark');
    }
}
