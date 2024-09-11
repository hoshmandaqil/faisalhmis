<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        return response()->json(IncomeCategory::all(), 200);
    }

    public function store(Request $request, $id = null)
    {
        $request->validate([
            'name' => ['required', Rule::unique('income_categories')->ignore($id)],
            'name_fa' => 'required'
        ]);

        $data = $request->all();
        $data['tax'] = isset($data['tax']) && $data['tax'] == 'on' ? 1 : 0;

        IncomeCategory::updateOrCreate(['id' => $id], $data);

        return back()->with('success', 'The expense category successfully saved.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        IncomeCategory::findOrFail($id)->delete();

        return back()->with('success', 'The expense category successfully deleted.');
    }
}
