<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function medicines()
    {
        return $this->hasMany(Pharmacy::class, 'supplier_id');
    }
}
