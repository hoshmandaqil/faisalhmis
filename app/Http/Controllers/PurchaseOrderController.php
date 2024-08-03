<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $POs = PurchaseOrder::latest()->when(request('search'), function ($q) {
            $q->where('id', request('search'))
                ->orWhere('description', 'LIKE', '%' . request('search') . '%')
                ->orWhere('total_price', request('search'));
        })->with('createdBy')->paginate(50);
        $totalPos = PurchaseOrder::count();
        $rejectedPos = PurchaseOrder::where('comment', '!=', NULL)->where('rejected_by', '!=', NULL)->where('approved', '!=', 1)->count();
        $unapprovedPos = PurchaseOrder::where('approved', 0)->where('comment', null)->count();
        $approvedPos = PurchaseOrder::where('approved', 1)->count();
        return view('PurchaseOrder.PO', compact('POs', 'totalPos', 'rejectedPos', 'unapprovedPos', 'approvedPos'));
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

        $descriptions = $request->description;

        $quantities = $request->quantity;
        $prices = $request->price;
        $totalPrices = $request->total_price;
        $dates = $request->date;
        $numberOfFilesEach = explode(',', $request->numberOfFilesPerEach);
        $startKeyFile = 0;
        $endKeyFile = 0;
        $thisPOFiles = [];
        foreach ($descriptions as $key => $des) {

            $poItem = new PurchaseOrder();
            $poItem->description = $descriptions[$key];
            $poItem->quantity = $quantities[$key];
            $poItem->price = $prices[$key];
            $poItem->total_price = $totalPrices[$key];
            $poItem->date = $dates[$key];
            $thisPOFilesNumber = $numberOfFilesEach[$key];
            $poItem->created_by = \Auth::user()->id;

            if ($request->hasfile('files')) {
                foreach ($request->file('files') as $image) {
                    $filename =  rand() . '_' . time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path() . '/POs/', $filename);
                    $images[] = $filename;
                }
                $poItem->files = json_encode($images);
            }
            $poItem->save();
        }

        return redirect()->back()->with('alert', 'The PO Added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $po = PurchaseOrder::where('id', $id)->first();
        $po->description = $request->description;
        $po->quantity = $request->quantity;
        $po->price = $request->price;
        $po->total_price = $request->total_price;
        $po->date = $request->date;
        $po->updated_by = \Auth::user()->id;
        if ($request->hasfile('files')) {
            $image = $request->file('files');
            $filename =  rand() . '_' . time() . '_' . $image->getClientOriginalName();
            $image->move(public_path() . '/POs/', $filename);

            $images = (array) json_decode($po->files);

            $images[] = $filename;

            $po->files = json_encode($images);
        }
        $po->save();
        return redirect()->back()->with('alert', 'The PO Updated Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder, $id)
    {
        $po = PurchaseOrder::where('id', $id)->first();
        $po->delete();
        return redirect()->back()->with('alert', 'The PO Deleted Successfully')->with('alert-type', 'alert-danger');
    }

    public function getPOImages($id)
    {
        $poImages = PurchaseOrder::where('id', $id)->select('files')->first();
        return view('ajax.ajax_view_PO_fiels', compact('poImages'));
    }

    public function po_actions(Request $request)
    {
        $po = PurchaseOrder::where('id', $request->po_id)->first();
        if ($request->has('po_checked')) {
            $po->checked_by = \Auth::user()->id;
        }

        if ($request->has('po_verified')) {
            $po->verified_by = \Auth::user()->id;
        }

        if ($request->has('po_approve')) {
            $po->approved = 1;
            $po->approved_by = \Auth::user()->id;
        }
        $po->save();

        return redirect()->back()->with('alert', 'The PO Changed Successfully')->with('alert-type', 'alert-info');
    }

    public function po_reject(Request $request)
    {
        $po = PurchaseOrder::where('id', $request->po_reject_id)->first();
        $po->comment = $request->reject_comment;
        $po->rejected_by = \Auth::user()->id;
        $po->save();
        return redirect()->back()->with('alert', 'The PO Changed Rejected')->with('alert-type', 'alert-info');
    }

    public function approveMultiplePos(Request $request)
    {
        foreach ($request->pos as $po) {
            $singlePo = PurchaseOrder::where('id', $po)->first();
            $singlePo->approved = 1;
            $singlePo->approved_by = \Auth::user()->id;
            $singlePo->save();
        }
        return 'true';
    }

    public function approvedPOs()
    {
        $POs = PurchaseOrder::where('approved', 1)->latest()->paginate(50);
        return view('PurchaseOrder.PO', compact('POs'));
    }

    public function unapprovedPOs()
    {
        $POs = PurchaseOrder::where('approved', 0)->where('comment', null)->latest()->paginate(50);
        return view('PurchaseOrder.PO', compact('POs'));
    }

    public function rejectedPOs()
    {
        $POs = PurchaseOrder::where('comment', '!=', NULL)->where('rejected_by', '!=', NULL)->where('approved', '!=', 1)->latest()->paginate(50);
        return view('PurchaseOrder.PO', compact('POs'));
    }

    public function searchPO(Request $request)
    {
        return redirect('/PO?search=' . $request->search);
    }
}
