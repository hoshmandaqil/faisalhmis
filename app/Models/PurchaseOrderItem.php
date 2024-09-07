<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';

    protected $guarded = ['id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * po
     *
     * @return $this->belongsTo()
     */
    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'id');
    }
}
