<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\EmployeeMainLabDepartment;
use App\Models\LabDepartment;
use App\Models\MainLabDepartment;
use App\Models\Patient;
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

    public function create(Request $request)
    {
        $start_date = $request->start_date ? toMeladi($request->start_date) : date('Y-m-01');
        $end_date = $request->end_date ? toMeladi($request->end_date) : date('Y-m-t');

        // Get all employees with their related laboratory tests within the date range
        $allEmployees = Employee::with(['user.patients.laboratoryTests' => function ($query) use ($start_date, $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }])->get();
        
        $mainLabDepartments = MainLabDepartment::all();

        // Transform the data to include all employee fields and related laboratory tests
        $employees = $allEmployees->map(function ($employee) use ($mainLabDepartments, $start_date, $end_date) {
            $labTests = $employee->user->patients->flatMap(function ($patient) {
                return $patient->laboratoryTests;
            });

            $employeeMainLabDepartments = EmployeeMainLabDepartment::where('employee_id', $employee->id)->get();

            $mainLabDepartmentSummary = $mainLabDepartments->map(function ($mainLabDepartment) use ($labTests, $employeeMainLabDepartments) {
                $employeePercentageAndTax = $employeeMainLabDepartments->where('main_lab_department_id', $mainLabDepartment->id)->first();
                
                // Skip if employee has zero percentage for this department
                if (!$employeePercentageAndTax || $employeePercentageAndTax->percentage == 0) {
                    return null;
                }

                $testsForMainDepartment = $labTests->filter(function ($test) use ($mainLabDepartment) {
                    return $test->testName->mainDepartment->id === $mainLabDepartment->id;
                });

                $totalPrice = $testsForMainDepartment->sum('price');
                $payable = $totalPrice * ($employeePercentageAndTax->percentage / 100);
                $tax = $payable * ($employeePercentageAndTax->tax / 100);

                return [
                    'main_lab_department' => $mainLabDepartment->dep_name,
                    'number_of_tests' => $testsForMainDepartment->count(),
                    'total_price' => $totalPrice,
                    'payable' => $payable,
                    'tax' => $tax,
                ];
            })->filter()->values();

            $patients = Patient::with(['ipds' => function ($query) use ($start_date, $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }])
            ->where('doctor_id', $employee->user->id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->get();

            $opd_percentage = $employee->opd_percentage;
            $ipd_percentage = $employee->ipd_percentage;

            $total_opd_price = $patients->sum('OPD_fee');
            $total_ipd_price = $patients->flatMap->ipds->sum('price');

            $opd_payable = $total_opd_price * ($opd_percentage / 100);
            $ipd_payable = $total_ipd_price * ($ipd_percentage / 100);

            $mainLabDepartmentSummary = collect([
                [
                    'main_lab_department' => 'OPD',
                    'number_of_tests' => $patients->count(),
                    'total_price' => $total_opd_price,
                    'payable' => $opd_payable,
                    'tax' => $opd_payable * 0.1,
                ],
                [
                    'main_lab_department' => 'IPD',
                    'number_of_tests' => $patients->flatMap->ipds->count(),
                    'total_price' => $total_ipd_price,
                    'payable' => $ipd_payable,
                    'tax' => $ipd_payable * 0.1,
                ]
            ])->concat($mainLabDepartmentSummary);

            $employee->lab_tests_summary = $mainLabDepartmentSummary;
            $employee->lab_tests_count = $labTests->count();
            $employee->lab_tests = $labTests->map(function ($test) {
                return [
                    'id' => $test->id,
                    'price' => $test->price,
                    'result' => $test->result,
                    'lab_department_id' => $test->lab_department_id,
                    'main_lab_department_id' => $test->testName->mainDepartment->id,
                ];
            });
            return $employee;
        });

        return view('payrolls.create', compact('employees', 'start_date', 'end_date'));
    }

    public function store(Request $request)
    {
        info($request->all());
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'official_days' => 'required|integer',
            'employees' => 'required|array',
            'employees.*.employee_id' => 'required|exists:employees,id',
            'employees.*.present_days' => 'required|numeric',
            'employees.*.bonus' => 'nullable|numeric',
            'employees.*.additional_payments' => 'nullable',
            'employees.*.tax' => 'required|numeric',
            'employees.*.net_payable' => 'required|numeric',
            'employees.*.gross_salary' => 'required|numeric',
            'employees.*.grand_total' => 'required|numeric',
        ]);

        $start_date = toMeladi($request->input('start_date'));
        $end_date = toMeladi($request->input('end_date'));

        DB::transaction(function () use ($request, $start_date, $end_date) {
            // Create the payroll
            $payroll = Payroll::create([
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_amount' => array_sum(array_column($request->input('employees'), 'grand_total')),
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
                    'bonus' => $employeeData['bonus'] ?? 0,
                    'tax' => $employeeData['tax'],
                    'additional_payments' => $employeeData['additional_payments'],
                    'gross_salary' => $employeeData['gross_salary'],
                    'net_salary' => $employeeData['net_payable'],
                    'amount' => $employeeData['grand_total'],
                    'grand_total' => $employeeData['grand_total'],
                ]);
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payrollItems = $payroll->items;
        info($payrollItems);
        return view('payrolls.show', compact('payroll', 'payrollItems'));
    }

    public function edit($id)
    {
        $payroll = Payroll::with('items.employee')->findOrFail($id);
        
        // Decode additional_payments for each payroll item
        foreach ($payroll->items as $item) {
            $item->additional_payments = json_decode($item->additional_payments, true);
        }

        return view('payrolls.edit', compact('payroll'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'official_days' => 'required|integer',
            'employees' => 'required|array',
            'employees.*.employee_id' => 'required|exists:employees,id',
            'employees.*.present_days' => 'required|numeric',
            'employees.*.bonus' => 'nullable|numeric',
            'employees.*.additional_payments' => 'nullable',
            'employees.*.tax' => 'required|numeric',
            'employees.*.net_payable' => 'required|numeric',
            'employees.*.gross_salary' => 'required|numeric',
            'employees.*.grand_total' => 'required|numeric',
        ]);

        $start_date = toMeladi($request->input('start_date'));
        $end_date = toMeladi($request->input('end_date'));

        DB::transaction(function () use ($request, $id, $start_date, $end_date) {
            // Update the payroll
            $payroll = Payroll::findOrFail($id);
            $payroll->update([
                'start_date' => $start_date,
                'end_date' => $end_date,
                'total_amount' => array_sum(array_column($request->input('employees'), 'net_payable')),
                'official_days' => $request->input('official_days'),
                'description' => $request->input('description'),
            ]);

            // Update payroll items
            foreach ($request->input('employees') as $employeeData) {
                $payrollItem = PayrollItem::where('payroll_id', $payroll->id)
                    ->where('employee_id', $employeeData['employee_id'])
                    ->firstOrFail();

                $payrollItem->update([
                    'present_days' => $employeeData['present_days'],
                    'bonus' => $employeeData['bonus'] ?? 0,
                    'tax' => $employeeData['tax'],
                    'additional_payments' => json_encode($employeeData['additional_payments']),
                    'gross_salary' => $employeeData['gross_salary'],
                    'net_salary' => $employeeData['net_payable'],
                    'amount' => $employeeData['grand_total'],
                    'grand_total' => $employeeData['grand_total'],
                ]);
            }
        });

        return redirect()->route('payrolls.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
    }
}
