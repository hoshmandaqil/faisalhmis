<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderFile extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_files';

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
