<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientIPD extends Model
{
    use HasFactory;
    protected $table = 'patient_ipds';

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'bed_id');
    }

    public function dischargedBy()
    {
        return $this->belongsTo(User::class, 'discharged_by');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
