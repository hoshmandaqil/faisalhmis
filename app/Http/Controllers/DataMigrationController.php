<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder as ModelsPurchaseOrder;
use App\Models\PurchaseOrderFile;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataMigrationController extends Controller
{
    public function index()
    {
        $this->movePOToNewDatabase();
    }


    public function movePOToNewDatabase()
    {
        // Fetch all purchase orders from the current database
        $purchaseOrders = DB::connection('mysql2')->table('purchase_order')->where('application_id', 8)->get();

        // Loop through each purchase order
        foreach ($purchaseOrders as $po) {
            PurchaseOrder::insert([
                'id' => $po->id,
                'po_by' => $po->po_by,
                'description' => $po->description,
                'date' => !empty($po->date) ? $po->date : null,
                'inserted_by' => 8,
                'checked_by' => 8,
                'checked_date' => !empty($po->checked_date) ? $po->checked_date : null,
                'verified_by' => 27,
                'verified_date' => !empty($po->verified_date) ? $po->verified_date : null,
                'approved_by' => $po->approved_by,
                'approved_date' => !empty($po->approved_date) ? $po->approved_date : null,
                'rejected_by' => $po->rejected_by,
                'rejected_date' => !empty($po->rejected_date) ? $po->rejected_date : null,
                'reject_comment' => $po->reject_comment,
            ]);
           
        }

        echo "Purchase Order are shifted successfully.";
    }

    public function movePOItemToNewDatabase()
    {
        // Fetch all purchase orders from the current database
        $items = DB::connection('mysql2')->table('purchase_order_items')->where('application_id', 8)->get();

        foreach ($items as $item) {
            PurchaseOrderItem::insert([
                'po_id' => $item->po_id,
                'description' => $item->description,
                'amount' => $item->amount,
                'quantity' => $item->quantity,
            ]);
        }

        echo "Purchase order items shifted successfully.";
    }


    public function movePoFile()
    {
        $files = DB::connection('mysql2')->table('purchase_order_files')->where('application_id', 8)->get();

        foreach ($files as $file) {
            PurchaseOrderFile::insert([
                'po_id' => $file->po_id,
                'file' => $file->file,
                'remarks' => $file->remarks,
                'created_at' => $file->created_at,
                'updated_at' => $file->updated_at,
            ]);
        }

        echo "Purchase order files are shifted successfully.";
    }
}
