<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    public function index()
    {
        $payrollPayments = PayrollPayment::with('employee')
            ->orderBy('id', 'desc')
            ->get();

        $employees = Employee::all();

        return view('payrolls.payroll-payments', compact('payrollPayments', 'employees'));
    }

    public function create()
    {
        return view('payroll_payments.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_date' => 'required|date',
            'payment_type' => 'required|string',
            'payment_amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        // Find the payroll for the given date
        $payroll = Payroll::whereYear('end_date', Carbon::parse($validatedData['payroll_date'])->year)
            ->whereMonth('end_date', Carbon::parse($validatedData['payroll_date'])->month)
            ->firstOrFail();

        // // Find the corresponding payroll item
        // $payrollItem = PayrollItem::where('payroll_id', $payroll->id)
        //     ->where('employee_id', $validatedData['employee_id'])
        //     ->firstOrFail();

        $id = $request->id;
        if (!$id) {
            $data['slip_no'] = 1;

            $last_slip = PayrollPayment::orderBy('id', 'desc')->first();

            if ($last_slip) {
                $data['slip_no'] = $last_slip->slip_no + 1;
            }
        }

        // Update or Create the payment
        $payment = PayrollPayment::updateOrCreate(
            ['id' => $id],
            [
                'employee_id' => $request->employee_id,
                'slip_no' => $data['slip_no'],
                'payroll_id' => $payroll->id,
                'amount' => $request->payment_amount,
                'cashier' => $id == null ? auth()->user()->id : $request->cashier,
                'payment_method' => $request->payment_type,
                'payment_date' => $request->payment_date,
                'remarks' => $request->remarks,
            ],
        );

        return response()->json([
            'message' => 'Payroll payment successfully recorded!',
            'payroll_payment' => $payment,
        ]);
    }

    public function show(Request $request)
    {
        $paymentId = $request->input('id');

        // Fetch the payroll payment along with related employee and payroll data
        $payment = PayrollPayment::with(['employee', 'payroll'])
            ->find($paymentId);

        $payrollItems = PayrollItem::where('payroll_id', $payment->payroll_id)
            ->where('employee_id', $payment->employee_id)
            ->select('gross_salary','net_salary','net_salary','bonus','tax','present_days','additional_payments')
            ->first();
            
        if ($payment) {
            // Format the payroll date
            $payment->payroll_date = $payment->payroll ? \Carbon\Carbon::parse($payment->payroll->payroll_date)->format('m/Y') : 'N/A';

            return response()->json([
                'success' => true,
                'data' => $payment,
                'payrollItems' => $payrollItems
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Payroll payment not found.'
            ]);
        }
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

    public function destroy($id)
    {
        $payrollPayment = PayrollPayment::findOrFail($id);
        $payrollPayment->delete();

        return redirect()->route('payroll_payments.index')->with('success', 'Payroll payment deleted successfully.');
    }

    public function getPayrollDetails(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $payrollDate = $request->input('payroll_date'); // Expected format: 'YYYY-MM'

        // Split the provided payroll_date into year and month
        [$year, $month] = explode('-', $payrollDate);

        // Find the payroll where the year and month match
        $payroll = Payroll::whereYear('end_date', $year)->whereMonth('end_date', $month)->firstOrFail();

        // Fetch the payroll item details based on employee ID and payroll ID
        $payrollItem = PayrollItem::where('payroll_id', $payroll->id)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        // Calculate total paid amount for this payroll and employee
        $totalPaid = PayrollPayment::where('payroll_id', $payroll->id)
            ->where('employee_id', $employeeId)
            ->sum('amount');

        // Calculate the remaining balance
        $balance = $payrollItem->net_salary - $totalPaid;

        return response()->json([
            'payroll_date' => $payroll->end_date,
            'salary' => $payrollItem->gross_salary,
            'present_days' => $payrollItem->present_days,
            'additional_payments' => json_decode($payrollItem->additional_payments),
            'tax' => $payrollItem->tax,
            'bonus' => $payrollItem->bonus,
            'gross_salary' => $payrollItem->gross_salary,
            'net_salary' => $payrollItem->net_salary,
            'payable' => $payrollItem->net_salary,
            'paid' => $totalPaid,
            'balance' => $balance,
            'remarks' => $payrollItem->remarks,
        ]);
    }
}
