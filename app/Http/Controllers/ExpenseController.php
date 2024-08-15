<?php

namespace App\Http\Controllers;

use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseSlip;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $searchTerm = request()->input('search', null);

        $expensesQuery = ExpenseSlip::with('expenseItems', 'expenseCategory', 'cashierUser')
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where('remarks', 'like', "%$searchTerm%");
            })
            ->orderByDesc('id');

        $expenses = $expensesQuery->paginate(10);
        $expenses->appends(request()->query());

        $categories = ExpenseCategory::where('parent', null)->with('subCategories')->get();

        $pos = PurchaseOrder::orderBy('id', 'desc')->get();

        return view(
            'expenses.index',
            compact('expenses', 'categories', 'pos')
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'paid_by' => 'required',
            'paid_to' => 'required',
            'date' => 'required',
            'category' => 'required',
            'expenses' => 'required',
            'expenses.*.expense_description' => 'required',
            'expenses.*.amount' => 'required',
            'po_id' => 'required'
        ]);

        // Update request expenses
        $request->expenses = array_map(function ($payment) use ($id) {
            $payment['remarks'] = $payment['remarks'];

            if ($id) {
                unset($payment['created_at']);
                unset($payment['updated_at']);
                unset($payment['deleted_at']);
            }

            return $payment;
        }, $request->expenses);

        DB::transaction(function () use ($id, $request) {
            $data = $request->all();
            // $data['date'] = toMeladi($data['date']);
            $data['date'] = $data['date'];

            if ($data['po_id'] == 0) {
                $data['po_id'] = NULL;
            }

            // Managing File Upload
            $file_name = "";

            // If Editing
            if ($id) {
                $exp = ExpenseSlip::findOrFail($id);

                // If no file just name it as the current file, otherwise delete it.
                if (!isset($data['file'])) {
                    $file_name = $exp->file;
                } else if(!empty($exp->file)) {
                    $file_path = public_path('storage/expenses' . '/' . $exp->file);

                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }

            if (isset($data['file'])) {
                $originalFile = $request->file('file');
                $file_name = 'expenses-' . $request->po_id . '-' . time() . '.' . $originalFile->getClientOriginalExtension();

                //Upload file to the storage
                Storage::disk('local')->put('public/expenses' . '/' . $file_name, file_get_contents($originalFile));
            }
            // End: Managing File Upload

            if (!$id) {
                $data['cashier'] = auth()->user()->id;

                $data['slip_no'] = 1;

                $last_slip = ExpenseSlip::orderBy('id', 'desc')->first();

                if ($last_slip) $data['slip_no'] = $last_slip->slip_no + 1;
            }

            $expense = ExpenseSlip::updateOrCreate(
                ['id' => $id],
                [
                    'slip_no' => $data['slip_no'],
                    'paid_by' => $data['paid_by'],
                    'paid_to' => $data['paid_to'],
                    'date' => $data['date'],
                    'remarks' => $data['remarks'],
                    'file' => $file_name,
                    'category' => $data['category'],
                    'cashier' => $data['cashier'],
                    'po_id' => $data['po_id'],
                ]
            );

            $expense->expenses()->sync($request->expenses);
        });

        return back()->with('success', 'The expense successfully saved!');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function files($id)
    {
        return response()->json(ExpenseSlip::where('slip_no', $id)->whereNotNull('file')
            ->where('file', '<>', '')->orderByDesc('id')->get(), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param mixed $slug
     * @param int $id
     * @return Renderable
     */
    public function deleteFile($id)
    {
        $expensesFile = ExpenseSlip::where('slip_no', $id)->first();
        $file_path = public_path('storage/expenses' . '/' . $expensesFile->file);

        info($id);
        if (file_exists($file_path)) {
            info($file_path);
            unlink($file_path);
            $expensesFile->update(['file' => '']);
        }
        return back()->with('success',  'Successfully deleted!');
    }
}
