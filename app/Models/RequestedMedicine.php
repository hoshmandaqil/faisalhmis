<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestedMedicine extends Model
{
    use HasFactory;
    protected $table = 'request_medicines';

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
