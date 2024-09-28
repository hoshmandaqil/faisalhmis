<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmployeeAttendanceImport;
use App\Models\EmployeeAttendance;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\CarbonPeriod;

class EmployeeAttendanceController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return view()
     */
    public function index(Request $request)
    {
        // Get distinct employee names and years
        $employeeNames = EmployeeAttendance::distinct()->pluck('name');
        $years = EmployeeAttendance::selectRaw('YEAR(date) as year')->distinct()->pluck('year');
        $months = EmployeeAttendance::selectRaw('MONTH(date) as month')->distinct()->pluck('month');

        $today = date('Y-m-d');
        $todayDateExists = EmployeeAttendance::whereDate('date', $today)->exists();

        // Fetch request inputs
        $employeeName = $request->query('employee_name');
        $from = $request->query('from') ? $request->query('from') : null;
        $to = $request->query('to') ? $request->query('to') : null;

        $attendanceRecords = collect(); // Initialize an empty collection
        $allDates = "";

        // Check if all required parameters are provided
        if ($from && $to) {
            // Build the query
            $query = EmployeeAttendance::query();

            if ($employeeName) {
                $query->where('name', $employeeName);
            }

            $query->whereBetween('date', [$from, $to]);

            // Get the attendance records based on the filters
            $attendanceRecords = $query->get()->groupBy('name');

            $allDates = CarbonPeriod::create($from, $to)->toArray();
        }

        $todayShamsiDate = date('Y-m-d');


        return view(
            'Employee.employees-attendance',
            compact('employeeNames', 'years', 'months', 'attendanceRecords', 'todayShamsiDate', 'allDates', 'todayDateExists')
        );
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        $selectedDate = $request->date ? $request->date : date('Y-m-d');

        if (!in_array('Upload Employee Attendance All Days', user_permissions()) && $request->date != date('Y-m-d')) {
            $selectedDate = date('Y-m-d');
        }

        Excel::import(new EmployeeAttendanceImport($selectedDate), $request->file('file'));

        return redirect()->back()->with('success', 'Attendance data imported successfully.');
    }
}
