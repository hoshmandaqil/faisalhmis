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

    public function create()
    {
        // Get all employees with their related laboratory tests
        $allEmployees = Employee::with(['user.patients.laboratoryTests'])->get();
        
        $mainLabDepartments = MainLabDepartment::all();

        // Transform the data to include all employee fields and related laboratory tests
        $employees = $allEmployees->map(function ($employee) use ($mainLabDepartments) {
            $labTests = $employee->user->patients->flatMap(function ($patient) {
                return $patient->laboratoryTests;
            });

            $employeeMainLabDepartments = EmployeeMainLabDepartment::where('employee_id', $employee->id)->get();

            $mainLabDepartmentSummary = $mainLabDepartments->map(function ($mainLabDepartment) use ($labTests, $employeeMainLabDepartments) {
                $testsForMainDepartment = $labTests->filter(function ($test) use ($mainLabDepartment) {
                    return $test->testName->mainDepartment->id === $mainLabDepartment->id;
                });

                $totalPrice = $testsForMainDepartment->sum('price');
                $employeePercentageAndTax = $employeeMainLabDepartments->where('main_lab_department_id', $mainLabDepartment->id)->first();
                
                if ($employeePercentageAndTax) {
                    $payable = $totalPrice * ($employeePercentageAndTax->percentage / 100);
                    $tax = $payable * ($employeePercentageAndTax->tax / 100);
                } else {
                    $payable = 0;
                    $tax = 0;
                }

                return [
                    'main_lab_department' => $mainLabDepartment->dep_name,
                    'number_of_tests' => $testsForMainDepartment->count(),
                    'total_price' => $totalPrice,
                    'payable' => $payable,
                    'tax' => $tax,
                ];
            })->filter(function ($summary) {
                return $summary['number_of_tests'] > 0;
            })->values();

            $patients = Patient::with('ipds')->where('doctor_id', $employee->user->id)->get();

            // $opd_amount = $employee->opd_amount;
            $opd_percentage = $employee->opd_percentage;

            // $ipd_amount = $employee->ipd_amount;
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
