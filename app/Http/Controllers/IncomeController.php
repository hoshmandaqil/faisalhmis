<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use App\Models\MiscellaneousIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = MiscellaneousIncome::with('user', 'incomeCategory')->orderByDesc('id')->paginate(25);

        $incomes->appends(request()->query());

        $categories = IncomeCategory::all();

        return view(
            'incomes.index',
            compact('incomes', 'categories')
        );
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'income_description' => 'required',
            'paid_by' => 'required',
            'paid_to' => 'required',
            'date' => 'required',
            'category' => 'required',
            'amount' => 'required'
        ]);

        $data = $request->all();
        // $data['date'] = toMeladi($data['date']);

        if (!$id) {
            $data['cashier'] = auth()->user()->id;

            $data['slip_no'] = 1;

            $last_slip = MiscellaneousIncome::orderBy('id', 'desc')->first();

            if ($last_slip) $data['slip_no'] = $last_slip->slip_no + 1;
        }

        MiscellaneousIncome::updateOrCreate(
            ['id' => $id],
            $data
        );

        return back()->with('success', 'The expense successfully saved!');
    }


    public function destroy($id)
    {
        $income = MiscellaneousIncome::findOrFail($id);
        $income->delete();

        return back()->with('success', 'Income deleted successfully.');
    }
}
