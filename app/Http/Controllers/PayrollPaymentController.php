<?php

namespace App\Http\Controllers;

use App\Models\PayrollPayment;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    public function index()
    {
        $payrollPayments = PayrollPayment::all();
        return view('payroll_payments.index', compact('payrollPayments'));
    }

    public function create()
    {
        return view('payroll_payments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slip_no' => 'required|integer|unique:payroll_payments',
            'payroll_id' => 'required|exists:payrolls,id',
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|integer',
            'amount' => 'required|numeric',
            'cashier' => 'required|exists:users,id',
            // Add validation for other fields as needed
        ]);

        PayrollPayment::create($request->all());

        return redirect()->route('payroll_payments.index')->with('success', 'Payroll payment created successfully.');
    }

    public function show(PayrollPayment $payrollPayment)
    {
        return view('payroll_payments.show', compact('payrollPayment'));
    }

    public function edit(PayrollPayment $payrollPayment)
    {
        return view('payroll_payments.edit', compact('payrollPayment'));
    }

    public function update(Request $request, PayrollPayment $payrollPayment)
    {
        $request->validate([
            'slip_no' => 'required|integer|unique:payroll_payments,slip_no,' . $payrollPayment->id,
            'payroll_id' => 'required|exists:payrolls,id',
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|integer',
            'amount' => 'required|numeric',
            'cashier' => 'required|exists:users,id',
            // Add validation for other fields as needed
        ]);

        $payrollPayment->update($request->all());

        return redirect()->route('payroll_payments.index')->with('success', 'Payroll payment updated successfully.');
    }

    public function destroy(PayrollPayment $payrollPayment)
    {
        $payrollPayment->delete();

        return redirect()->route('payroll_payments.index')->with('success', 'Payroll payment deleted successfully.');
    }
}
