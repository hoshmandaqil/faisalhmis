<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderSetting;
use App\Models\User;
use Illuminate\Http\Request;


class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pos = PurchaseOrder::filter(request(['search', 'status']))
            ->with('items', 'insertedByUser', 'checkedByUser', 'verifiedByUser', 'approvedByUser', 'rejectedByUser', 'expenses')
            ->orderByDesc('id')
            ->paginate(25);

        $pos->appends(request()->query());

        $setting = PurchaseOrderSetting::first();

        $users = User::select('id', 'name')->get();

        $status = [
            'issued' => 'Issued',
            'checked' => 'Checked',
            'verified' => 'Verified',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'approved_no_expenses' => 'Approved/No Expenses',
        ];

        return view('PurchaseOrder.PO', compact('pos', 'setting', 'users', 'status'));
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

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'po_by' => 'required|string|max:255',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'description.*' => 'required|string',
            'amount.*' => 'required|numeric',
            'quantity.*' => 'required|integer',
            'item_remarks.*' => 'nullable|string',
        ]);

        // Create the PurchaseOrder
        $purchaseOrder = PurchaseOrder::create([
            'po_by' => $validated['po_by'],
            'date' => $validated['date'], // Assuming it's already in the correct format
            'remarks' => $validated['remarks'] ?? null,
            'inserted_by' => auth()->id(),
        ]);

        // Loop through the items and create PurchaseOrderItem for each
        foreach ($validated['description'] as $index => $description) {
            PurchaseOrderItem::create([
                'po_id' => $purchaseOrder->id,
                'description' => $description,
                'amount' => $validated['amount'][$index],
                'quantity' => $validated['quantity'][$index],
                'remarks' => $validated['item_remarks'][$index] ?? null,
            ]);
        }

        return back()->with(['message' => 'Purchase Order and items successfully stored']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);
        return view('PurchaseOrder.show', compact('po'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        // Load the related PO items
        $purchaseOrder->load('items');

        // Return the purchase order and items as JSON
        return response()->json([
            'po' => $purchaseOrder,
            'items' => $purchaseOrder->items
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'po_id' => 'required|exists:purchase_order,id',
            'po_by' => 'required|string',
            'date' => 'required|date',
            'remarks' => 'nullable|string',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer',
            'item_remarks' => 'nullable|array',
            'item_remarks.*' => 'nullable|string',
        ]);

        // Find the purchase order
        $purchaseOrder = PurchaseOrder::findOrFail($validated['po_id']);

        // Update the purchase order
        $purchaseOrder->update([
            'po_by' => $validated['po_by'],
            'date' => $validated['date'],
            'remarks' => $validated['remarks'],
        ]);

        // Delete existing items
        $purchaseOrder->items()->delete();

        // Create new items
        foreach ($validated['description'] as $index => $description) {
            $purchaseOrder->items()->create([
                'description' => $description,
                'amount' => $validated['amount'][$index],
                'quantity' => $validated['quantity'][$index],
                'remarks' => $validated['item_remarks'][$index] ?? null,
            ]);
        }
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
