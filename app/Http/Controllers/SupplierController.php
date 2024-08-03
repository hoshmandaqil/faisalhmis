<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!in_array('supplier_list', user_permissions())) {
            return view('access_denied');
        }
        $suppliers = Supplier::latest()->get();
        return view('supplier.suppliers', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!in_array('add_supplier', user_permissions())) {
            return view('access_denied');
        }
        $supplier = new Supplier();
        $supplier->supplier_name = $request->supplier_name;
        $supplier->supplier_address = $request->supplier_address;
        $supplier->supplier_phone = $request->supplier_phone;
        $supplier->supplier_shortCode = $request->supplier_shortCode;
        $supplier->save();
        return  redirect()->back()->with('alert', 'The Supplier added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (!in_array('edit_supplier', user_permissions())) {
            return view('access_denied');
        }
        $supplier = Supplier::where('id', $id)->first();
        $supplier->supplier_name = $request->supplier_name;
        $supplier->supplier_address = $request->supplier_address;
        $supplier->supplier_phone = $request->supplier_phone;
        $supplier->supplier_shortCode = $request->supplier_shortCode;
        $supplier->save();
        return  redirect()->back()->with('alert', 'The Supplier edited Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::where('id', $id)->first();
        $supplier->delete();
        return  redirect()->back()->with('alert', 'The Supplier deleted Successfully')->with('alert-type', 'alert-danger');
    }

    public function suppliers_list()
    {
        $data = Supplier::join('pharmacies', 'pharmacies.supplier_id', '=', 'suppliers.id')
            ->select(DB::raw('suppliers.*'), DB::raw('SUM(pharmacies.purchase_price * pharmacies.purchase_qty) as total_price'))
            ->groupBy('suppliers.id')
            ->latest()
            ->get();

        return $data;
    }

    public function supplier_medicines($id)
    {
        return Supplier::where('id', $id)->with('medicines', 'medicines.medicineName')->first();
    }

    public function suppliers_name()
    {
        return Supplier::select('id', 'supplier_name')->latest()->get();
    }
}