<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Maatwebsite\Excel\Facades\Excel;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = User::where('attendance_id', '!=', 'NULL')->select('attendance_id', 'name', 'check_in', 'check_out')->get();
        return view('Attendance.attendance', compact('staffs'));
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

        if (isset($request->select_file) && $request->select_file != NULL && isset($request->type)) {

            $file = FacadesRequest::file('select_file');
            if ($file->getClientOriginalExtension() == 'xlsx') {

                $destinationPath = public_path('attendanceFiles/');
                $filename = 'att_emp.' . $file->getClientOriginalExtension();
                FacadesRequest::file('select_file')->move($destinationPath, $filename);

                $excelData = Excel::import(new AttendanceImport($request), public_path('attendanceFiles/att_emp.xlsx'));

                return redirect()->back()->with('alert', 'Data has imported Successfully')->with('alert-type', 'alert-success');

            } else {
                return redirect()->back()->with('alert', 'The File Type Should be .xlsx')->with('alert-type', 'alert-danger');
            }
        } else {
            return redirect()->back()->with('alert', 'The File and Type is Required')->with('alert-type', 'alert-danger');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendance $attendance)
    {
    }

    public function attendance_report(Request $request)
    {
        $userId = $request->attendance_emps;
        $year = $request->year;
        $month = $request->month;
        if ($month < 10) {
            $month = '0' . $month;
        }

        $from = $year . '-' . $month . '-01';
        $to = $year . '-' . $month . '-31';

        $returnData = [];
        $holidays = Holiday::where('holiday_date', '<=', $to)->where('holiday_date', '>=', $from)->pluck('holiday_date')->toArray();
        if ($userId == 'all') {
            $attendanceReport = Attendance::where('persian_date', '<=', $to)->where('persian_date', '>=', $from)->orderBy('date', 'ASC')->get();
        } else {
            $attendanceReport = Attendance::where('user_id', $userId)->where('date', '<=', $to)
                ->where('date', '>=', $from)->get();
        }
        foreach ($attendanceReport as $key => $report) {
            $userId = $report->user_id;
            $userName = $report->user_name;
            $date = $report->date;
            $checkIn = $report->check_in;
            $checkOut = $report->check_out;
            $returnData[$userId][$userName][$date]['checkIn'] = $checkIn;
            $returnData[$userId][$userName][$date]['checkOut'] = $checkOut;
            $returnData[$userId][$userName][$date]['databaseId'] = $report->id;
            $returnData[$userId][$userName][$date]['comment'] = $report->comment;
            $returnData[$userId][$userName][$date]['absent'] = $report->absent;
            $returnData[$userId][$userName][$date]['approved'] = $report->approved;
        }

        return view('Attendance.attendance_report', compact('returnData', 'from', 'to', 'holidays'));
    }

    public function saveJustifyReason(Request $request)
    {

        $recordId = $request->id;
        $attDay = Attendance::where('id',$recordId)->first();
        $attDay->comment = $request->message;
        if ($request->isPresent === 'true') {
            $attDay->absent = 0;
        } else {
            $attDay->absent = 1;
        }
        $attDay->commented_by = \Auth::user('id')->id;
        $attDay->save();
        return "true";
    }

    public function approveAttendance()
    {
        $from = $_GET['from'];
        $to = $_GET['to'];
        DB::table('attendances')
            ->where('persian_date', '<=', $to)
            ->where('persian_date', '>=', $from)
            ->update(['approved' => 1, 'approved_by' => \Auth::user('id')->id]);
        return redirect()->route('attendance.index')->with('alert', 'Attendance has Approved Successfully')->with('alert-type', 'alert-success');
    }
}
