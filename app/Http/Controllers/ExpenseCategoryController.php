<?php

namespace App\Http\Controllers;

use App\Models\Expense\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    /**
     * Return a listing of the ExpenseCategory as JSON
     * 
     * @return Renderable
     */
    public function index()
    {
        return response()->json(ExpenseCategory::where('parent', null)->with('subCategories')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  mixed $request
     * @param  mixed $slug
     * @param  mixed $id
     * @return Renderable
     */
    public function store(Request $request, $id = null)
    {
        $request->validate([
            'name' => ['required', Rule::unique('expenses_categories')->where(function ($query) {
                return $query->where('deleted_at', NULL);
            })->ignore($id)],
            'name_fa' => 'required'
        ]);

        $data = $request->all();
        $data['tax'] = isset($data['tax']) && $data['tax'] == 'on' ? 1 : 0;

        ExpenseCategory::updateOrCreate(['id' => $id], $data);

        return back()->with('success', 'The expense category successfully saved.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        ExpenseCategory::findOrFail($id)->delete();

        return back()->with('success', 'The expense category successfully deleted.');
    }
}
