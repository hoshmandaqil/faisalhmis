<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\PayrollItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::all();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        // Get all employees with their base salary
        $employees = Employee::all();
        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payroll_date' => 'required|date',
            'official_days' => 'required|integer',
            'employees' => 'required|array',
            'employees.*.employee_id' => 'required|exists:employees,id',
            'employees.*.present_days' => 'required|numeric',
            'employees.*.bonus' => 'required|numeric',
            'employees.*.additional_payments' => 'nullable|string',
            'employees.*.tax' => 'required|numeric',
            'employees.*.amount' => 'required|numeric',
            // Add more validation rules if needed
        ]);

        info($request->all);
        
        DB::transaction(function () use ($request) {
            // Create the payroll
            $payroll = Payroll::create([
                'payroll_date' => $request->input('payroll_date'),
                'total_amount' => array_sum(array_column($request->input('employees'), 'amount')),
                'official_days' => $request->input('official_days'),
                'status' => 'pending', // or any initial status
                'description' => $request->input('description'),
            ]);

            // Create payroll items
            foreach ($request->input('employees') as $employeeData) {
                PayrollItem::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $employeeData['employee_id'],
                    'present_days' => $employeeData['present_days'],
                    'bonus' => $employeeData['bonus'],
                    'tax' => $employeeData['tax'],
                    'additional_payments' => $employeeData['additional_payments'],
                    'amount' => $employeeData['amount'], // This should be calculated in the frontend and passed to backend
                    'gross_salary' => $employeeData['gross_salary'], // if you include it in the frontend data
                    'net_salary' => $employeeData['net_salary'], // if you include it in the frontend data
                ]);
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payrollItems = $payroll->items; // get related payroll items
        return view('payrolls.show', compact('payroll', 'payrollItems'));
    }

    public function edit(Payroll $payroll)
    {
        // Handle the edit logic here if needed
    }

    public function update(Request $request, Payroll $payroll)
    {
        // Handle the update logic here if needed
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
    }
}
