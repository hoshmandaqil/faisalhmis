<?php

namespace App\Http\Controllers;

use App\Models\MedicineName;
use App\Models\Pharmacy;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pharmacies = Pharmacy::latest()->with('medicineName', 'supplier', 'user')->paginate(35);
        $suppliers = Supplier::latest()->get();
        $medicines = MedicineName::select('id','medicine_name')->latest()->get();
        return view('pharmacy.pharmacy', compact('pharmacies', 'suppliers', 'medicines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::select('id', 'supplier_name', 'supplier_shortCode')->latest()->get();
        $lastIdForInvoiceNumber = Pharmacy::select('id')->latest()->first();
        if ($lastIdForInvoiceNumber == null){
            $lastIdForInvoiceNumber = 1;
        }
        else {
            $lastIdForInvoiceNumber = $lastIdForInvoiceNumber->id+1;
        }

        return view('pharmacy.add-medicine', compact('suppliers', 'lastIdForInvoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $pharmacyData = [];
        $items = $request->item;
        $quantity = $request->quantity;
        $barcode = $request->barcode;
        $purchase_qty = $request->quantity;
        $purchase_price = $request->purchase_price;
        $sale_percentage = $request->sale_percentage;
        $sale_price = $request->sale_price;
        $vendor = $request->vendor;
        $remark = $request->remark;
        $invoiceNo = $request->invice_no;
        $expDate = $request->exp_date;
        $mfgDate = $request->mfg_date;
//        dd($request);
        foreach ($items as $key => $item){
            $pharmacyData[$key]['item'] = $item;
            $pharmacyData[$key]['quantity'] = $quantity[$key];
            $pharmacyData[$key]['barcode'] = $barcode[$key];
            $pharmacyData[$key]['purchase_qty'] = $purchase_qty[$key];
            $pharmacyData[$key]['purchase_price'] = $purchase_price[$key];
            $pharmacyData[$key]['sale_percentage'] = $sale_percentage[$key];
            $pharmacyData[$key]['sale_price'] = $sale_price[$key];
            $pharmacyData[$key]['vendor'] = $vendor[$key];
            $pharmacyData[$key]['remark'] = $remark[$key];
            $pharmacyData[$key]['exp_date'] = $expDate[$key];
            $pharmacyData[$key]['mfg_date'] = $mfgDate[$key];
        }
//        dd($pharmacyData);
        foreach ($pharmacyData as $data){
            $pharmacy = new Pharmacy();
            $pharmacy->medicine_id =  $data['item'];
            $pharmacy->quantity =  $data['quantity'];
            $pharmacy->barcode =  $data['barcode'];
            $pharmacy->purchase_qty =  $data['purchase_qty'];
            $pharmacy->purchase_price =  $data['purchase_price'];
            $pharmacy->sale_percentage =  $data['sale_percentage'];
            $pharmacy->sale_price =  $data['sale_price'];
            $pharmacy->vendor =  $data['vendor'];
            $pharmacy->invoice_no =  $invoiceNo;
            $pharmacy->supplier_id =  $request->supplier_id;
            $pharmacy->remark =  $data['remark'];
            $pharmacy->mfg_date =  $data['mfg_date'];
            $pharmacy->exp_date =  $data['exp_date'];
            $pharmacy->created_by =  \Auth::user()->id;
            $pharmacy->save();
        }
        return  redirect()->route('pharmacy.index')->with('alert', 'The Medicine added Successfully')->with('alert-type', 'alert-success');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pharmacy  $pharmacy
     * @return \Illuminate\Http\Response
     */
    public function show(Pharmacy $pharmacy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pharmacy  $pharmacy
     * @return \Illuminate\Http\Response
     */
    public function edit(Pharmacy $pharmacy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pharmacy  $pharmacy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pharmacy $pharmacy)
    {
       $pharmacy->medicine_id = $request->item;
       $pharmacy->supplier_id = $request->supplier_id;
       $pharmacy->quantity = $request->quantity;
       $pharmacy->purchase_qty = $request->quantity;
       $pharmacy->purchase_price = $request->purchase_price;
       $pharmacy->sale_percentage = $request->sale_percentage;
       $pharmacy->sale_price = $request->sale_price;
       $pharmacy->vendor = $request->vendor;
       $pharmacy->invoice_no = $request->invice_no;
       $pharmacy->remark = $request->remark;
       $pharmacy->save();
        return  redirect()->back()->with('alert', 'The Medicine Updated Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pharmacy  $pharmacy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();
        return  redirect()->back()->with('alert', 'The Medicine Deleted Successfully')->with('alert-type', 'alert-info');

    }

    public function save_requested_medicine(Request $request)
    {
        foreach ($request->values as $value){
            if ($value != NULL){
                DB::table('request_medicines')->insert(['medicine_name'=> $value, 'created_by' => \Auth::user()->id, 'created_at' => Carbon::now()
                ]);
            }
        }
        return true;
    }

    public function setSupplierMultipleMedicine(Request $request)
    {
        foreach ( $request->Pos as $po){
            $result= DB::table('pharmacies')->where('id','=',$po)->update([
                'supplier_id' =>$request->supplierId
            ]);
        }
        return true;
    }


    public function search_medicine()
    {
        $medicineSearchDetail = $_GET['search_medicine'] ?? '';
        if ($medicineSearchDetail !=  NULL){
            $pharmacies = Pharmacy::wherehas('medicineName', function ($q) use ($medicineSearchDetail){
                $q->where('medicine_name', 'Like', '%' . $medicineSearchDetail. '%');
            })->orwhere(function ($query) use ($medicineSearchDetail) {
                $query->where('quantity', 'Like', '%' . $medicineSearchDetail. '%')
                    ->orwhere('purchase_price', 'Like', '%' . $medicineSearchDetail. '%')
                    ->orwhere('sale_price', 'Like', '%' . $medicineSearchDetail. '%')
                    ->orwhere('remark', 'Like', '%' . $medicineSearchDetail. '%')
                    ->orwhere('invoice_no', 'Like', '%' . $medicineSearchDetail. '%');
            })->latest()->with('medicineName', 'supplier', 'user')->paginate(100);
            $suppliers = Supplier::latest()->get();
            $medicines = MedicineName::select('id', 'medicine_name')->latest()->get();
            return view('pharmacy.pharmacy', compact('pharmacies', 'suppliers', 'medicines', 'medicineSearchDetail'));
        }
        else {
            return redirect()->route('pharmacy.index');
        }
    }
    
    
       public function return_medicine($id)
    {
       $pharmacy = Pharmacy::where('id', $id)->first();
       $pharmacy->returned = 1;
       $pharmacy->returned_by = \Auth::user()->id;
       $pharmacy->save();
       return  redirect()->back()->with('alert', 'The Medicine Returned Successfully')->with('alert-type', 'alert-info');
    }

    public function undo_return_medicine($id)
    {
       $pharmacy = Pharmacy::where('id', $id)->first();
       $pharmacy->returned = 0;
       $pharmacy->returned_by = NULL;
       $pharmacy->save();
       return  redirect()->back()->with('alert', 'The Medicine Back Successfully')->with('alert-type', 'alert-info');
    }

    public function expire_this_medicine($id)
    {
       $pharmacy = Pharmacy::where('id', $id)->first();
       $pharmacy->expired = 1;
       $pharmacy->expired_by = \Auth::user()->id;
       $pharmacy->save();
       return  redirect()->back()->with('alert', 'The Medicine Expired Successfully')->with('alert-type', 'alert-info');
    }

    public function undo_expire_this_medicine($id)
    {
       $pharmacy = Pharmacy::where('id', $id)->first();
       $pharmacy->expired = 0;
       $pharmacy->expired_by = NULL;
       $pharmacy->save();
       return  redirect()->back()->with('alert', 'The Medicine Back Successfully')->with('alert-type', 'alert-info');
    }
}
