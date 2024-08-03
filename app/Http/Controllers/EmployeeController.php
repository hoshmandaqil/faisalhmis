<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MainLabDepartment;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('employeeCurrentSalary')->latest()->get();
        
        $users = User::all();

        return view('Employee.employees', compact('employees', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $employee = new Employee();
        $employee->employee_id = $request->employee_id;
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->father_name = $request->father_name;
        $employee->dob = $request->dob;
        $employee->position = $request->position;
        $employee->department = $request->department_name;
        $employee->email = $request->email;
        $employee->phone_number = $request->phone_number;
        $employee->nationality = $request->nationality;
        $employee->gender = $request->gender;
        $employee->marital_status = $request->marital_status;
        $employee->native_language = $request->native_language;
        $employee->tazkira_number = $request->tazkira_number;
        $employee->current_address = $request->current_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->contract_start = $request->contract_start;
        $employee->contract_end = $request->contract_end;
        $employee->attendance_id = $request->attendance_id;
        $employee->check_in = $request->check_in;
        $employee->check_out = $request->check_out;
        $employee->status = $request->status;
        $employee->comment = $request->comment;
        $employee->user_id = $request->user;
        // $employee->salary = $request->salary;
        $employee->created_by = \Auth::user()->id;

        if ($request->hasfile('image')) {
            $image = $request->file('image');
            $filename =  rand() . '_' . time() . '_' . $image->getClientOriginalName();
            $image->move(public_path() . '/EmpImages/', $filename);
            $employee->image = $filename;
        }

        if ($request->hasfile('contract_files')) {
            foreach ($request->file('contract_files') as $contract) {
                $filenamec =  rand() . '_' . time() . '_' . $contract->getClientOriginalName();
                $contract->move(public_path() . '/contracts/', $filenamec);
                $contracts[] = $filenamec;
            }
            $employee->contract_files = json_encode($contracts);
        }
        $employee->save();


        $salary = new Salary();
        $salary->emp_id = $employee->id;
        $salary->salary_amount = $request->salary;
        $salary->created_by = \Auth::user()->id;
        $salary->save();
        return redirect()->back()->with('alert', 'New Employee Added Successfully')->with('alert-type', 'alert-success');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $employee = Employee::where('id', $request->emp_id)->first();
        $employee->employee_id = $request->employee_id;
        $employee->first_name = $request->first_name;
        $employee->last_name = $request->last_name;
        $employee->father_name = $request->father_name;
        $employee->dob = $request->dob;
        $employee->position = $request->position;
        $employee->department = $request->department_name;
        $employee->email = $request->email;
        $employee->phone_number = $request->phone_number;
        $employee->nationality = $request->nationality;
        $employee->gender = $request->gender;
        $employee->marital_status = $request->marital_status;
        $employee->native_language = $request->native_language;
        $employee->tazkira_number = $request->tazkira_number;
        $employee->current_address = $request->current_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->contract_start = $request->contract_start;
        $employee->contract_end = $request->contract_end;
        $employee->attendance_id = $request->attendance_id;
        $employee->check_in = $request->check_in;
        $employee->check_out = $request->check_out;
        $employee->status = $request->status;
        $employee->comment = $request->comment;
        $employee->user_id = $request->user;
        // $employee->salary = $request->salary;
        $employee->updated_by = \Auth::user()->id;

        if ($request->hasfile('image')) {
            $image = $request->file('image');
            $filename =  rand() . '_' . time() . '_' . $image->getClientOriginalName();
            $image->move(public_path() . '/EmpImages/', $filename);
            $employee->image = $filename;
        }
        if ($request->hasfile('contract_files')) {
            foreach ($request->file('contract_files') as $contract) {
                $filenamec =  rand() . '_' . time() . '_' . $contract->getClientOriginalName();
                $contract->move(public_path() . '/contracts/', $filenamec);
                $contracts[] = $filenamec;
            }
            $employee->contract_files = json_encode($contracts);
        }

        $employee->save();
        $salary = new Salary();
        $salary->emp_id = $employee->id;
        $salary->salary_amount = $request->salary;
        $salary->created_by = \Auth::user()->id;
        $salary->save();
        return redirect()->back()->with('alert', 'New Employee Added Successfully')->with('alert-type', 'alert-info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->back()->with('alert', 'The Employee has been Successfully Deleted!')->with('alert-type', 'alert-danger');
    }

    public function activate_employee($id)
    {
        $user = Employee::findorfail($id);
        $user->status = 1;
        $user->save();
        return redirect()->back()->with('alert', 'The Employee has been Successfully Activated!')->with('alert-type', 'alert-info');
    }

    public function deactivate_employee($id)
    {
        $user = Employee::findorfail($id);
        $user->status = 0;
        $user->save();
        return redirect()->back()->with('alert', 'The Employee has been Successfully Activated!')->with('alert-type', 'alert-info');
    }

    public function getEmployeeProfile($id)
    {
        $employee = Employee::where('id', $id)->with('employeeCurrentSalary')->first();
        return view('ajax.ajax_employee_profile_view', compact('employee'));
    }

    public function getPercentage($id)
    {
        $employee = Employee::find($id);
        $mainLabs = DB::Table('main_lab_departments')->latest()->get();

        $labPercentage = [];

        foreach ($employee->labPercentage as $percentage) {
            $labPercentage[$percentage['pivot']['main_lab_department_id']] = $percentage['pivot']['percentage'];
        }

        return view('ajax.ajax_load_percentage', compact('employee', 'mainLabs', 'labPercentage'));
    }

    public function setPercentage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $labs = [];

        foreach ($request->lab_id as $key => $lab) {
            if ($request->percentage[$key] != NULL) {
                $labs[$lab] = ['percentage' => $request->percentage[$key]];
            }
        }

        Employee::find($request->id)->update([
            'opd_percentage' => $request->opd,
            'ipd_percentage' => $request->ipd,
            'ipd_amount' => $request->ipd_amount,
            'opd_amount' => $request->opd_amount,
        ]);

        Employee::find($request->id)->labPercentage()->sync($labs);

        return redirect()->back()->with('alert', 'Employee Percentage Set Successfully')->with('alert-type', 'alert-info');
    }
}
