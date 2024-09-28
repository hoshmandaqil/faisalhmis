<?php

namespace App\Imports;

use App\Models\EmployeeAttendance;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class EmployeeAttendanceImport implements ToCollection, WithHeadingRow
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $date = is_numeric($row['date'])
                ? ExcelDate::excelToDateTimeObject($row['date'])->format('Y-m-d')
                : Carbon::parse($row['date'])->format('Y-m-d');

            if ($date !== $this->date) {
                info([$date, $this->date]);
                continue;
            }

            EmployeeAttendance::create([
                'emp_no' => $row['emp_no'],
                'ac_no' => $row['ac_no'],
                'name' => $row['name'],
                'date' => $date,
                'timetable' => $row['timetable'],
                'on_duty' => $row['on_duty'],
                'off_duty' => $row['off_duty'],
                'clock_in' => $row['clock_in'],
                'clock_out' => $row['clock_out'],
                'normal' => $row['normal'],
                'real_time' => $row['real_time'] ? $row['real_time'] : null,
                'late' => $row['late'],
                'early' => $row['early'],
                'absent' => $this->convertToBoolean($row['absent']),
                'ot_time' => $row['ot_time'],
                'work_time' => $row['work_time'],
                'must_c_in' => $this->convertToBoolean($row['must_cin']),
                'must_c_out' => $this->convertToBoolean($row['must_cout']),
                'department' => $row['department'],
                'ndays' => $row['ndays'] ? $row['ndays'] : null,
                'weekend' => $this->convertToBoolean($row['weekend']),
                'holiday' => $this->convertToBoolean($row['holiday']),
                'att_time' => $row['att_time'],
                'ndays_ot' => $row['ndays_ot'] ? $row['ndays_ot'] : null,
            ]);
        }
    }

    private function convertToBoolean($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
