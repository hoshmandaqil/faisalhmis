<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabDepartment extends Model
{
    use HasFactory;

    public function mainDepartment()
    {
        return $this->belongsTo(MainLabDepartment::class, 'main_dep_id');
    }
}
