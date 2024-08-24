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
            'employees.*.net_payable' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // Create the payroll
            $payroll = Payroll::create([
                'payroll_date' => $request->input('payroll_date'),
                'total_amount' => array_sum(array_column($request->input('employees'), 'net_payable')),
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
                    'amount' => $employeeData['net_payable'],
                    'gross_salary' => $employeeData['gross_salary'],
                    'net_salary' => $employeeData['net_payable'],
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

    public function edit($id)
    {
        $payroll = Payroll::with('items.employee')->findOrFail($id);
        return view('payrolls.edit', compact('payroll'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payroll_date' => 'required|date',
            'official_days' => 'required|integer',
            'employees' => 'required|array',
            'employees.*.present_days' => 'required|numeric',
            'employees.*.bonus' => 'required|numeric',
            'employees.*.additional_payments' => 'nullable|numeric',
        ]);

        // Update Payroll
        $payroll = Payroll::findOrFail($id);
        $payroll->payroll_date = $request->input('payroll_date');
        $payroll->official_days = $request->input('official_days');
        $payroll->save();

        // Update Payroll Items
        foreach ($request->input('employees') as $employeeId => $employeeData) {
            $payrollItem = PayrollItem::where('payroll_id', $payroll->id)
                ->where('employee_id', $employeeId)
                ->firstOrFail();

            $grossSalary = ($payrollItem->employee->base_salary / 30) * $employeeData['present_days'];
            $taxableIncome = $grossSalary + $employeeData['bonus'] + ($employeeData['additional_payments'] ?? 0);
            $tax = $taxableIncome * 0.1;  // Example tax calculation
            $netPayable = $taxableIncome - $tax;

            $payrollItem->present_days = $employeeData['present_days'];
            $payrollItem->bonus = $employeeData['bonus'];
            $payrollItem->additional_payments = $employeeData['additional_payments'] ?? 0;
            $payrollItem->gross_salary = $grossSalary;
            $payrollItem->net_salary = $netPayable;
            $payrollItem->tax = $tax;
            $payrollItem->amount = $netPayable;
            $payrollItem->save();
        }

        return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
    }
}
