<?php
// Change to Shamsi
function toShamsi($date, $type = '-', $format = 'Y-m-d')
{
    if (env('DATE_LOCALE') == 'SHAMSI') {
        $date = \Morilog\Jalali\CalendarUtils::strftime($format, strtotime($date));
    }

    if ($type != '-') {
        $date = explode('-', $date);
        $date = $date[0] . $type . $date[1] . $type . $date[2];
    }

    return $date;
}

// Change to Meladi
function toMeladi($date = '1398/10/12', $type = '/')
{
    if (env('DATE_LOCALE') == 'SHAMSI') {
        $date = explode($type, $date);

        $date = \Morilog\Jalali\CalendarUtils::toGregorian($date[0], (int) $date[1], (int) $date[2]);

        $date = $date[0] . '-' . $date[1] . '-' . $date[2];

        return date('Y-m-d', strtotime($date));
    }

    return $date;
}

function validateDate($date)
{
    $pattern = '/^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])$/';

    return preg_match($pattern, $date) === 1;
}

if (!function_exists('changeDateFormat')) {
    function changeDateFormat($dateTime)
    {
        return \Carbon\Carbon::parse($dateTime)->diffForHumans();
    }
}

function isShamsiLeapYear($year)
{
    $leapYears = [1, 5, 9, 13, 17, 22, 26, 30];
    $mod = $year % 33;

    return in_array($mod, $leapYears);
}
