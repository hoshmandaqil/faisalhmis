<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainLabDepartment extends Model
{
  use HasFactory;

  public function thisDepTests()
  {
    return $this->hasMany(LabDepartment::class, 'main_dep_id');
  }

  public function employee()
  {
    return $this->belongsToMany(Employee::class)->withPivot('percentage');
  }

  public function patientLabs()
  {
    return $this->hasManyThrough(LaboratoryPatientLab::class, LabDepartment::class);
  }
}
