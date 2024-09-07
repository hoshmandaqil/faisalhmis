<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderSetting extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_settings';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
