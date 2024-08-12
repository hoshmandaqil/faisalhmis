<?php

namespace App\Http\Controllers;

use App\Models\Expense\ExpenseCategory;
use App\Models\Expense\ExpenseSlip;
use Illuminate\Http\Request;

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

        // $pos = PurchaseOrder::university()->orderBy('id', 'desc')->get();

        return view(
            'expenses.index',
            compact('expenses', 'categories')
        );
    }
}
