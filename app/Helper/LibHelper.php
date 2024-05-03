<?php

namespace App\Helper;

use DateTime;
use Intervention\Image\ImageManagerStatic;

class LibHelper {

    public static function formatTanggalIndo($input_date) {
        $tgl = '';

        $dayName = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $monthName = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $getDate = new DateTime($input_date);
        $day = $dayName[$getDate->format('w')];
        $date = $getDate->format('d');
        $month = $monthName[$getDate->format('n') - 1];
        $year = $getDate->format('Y');

        $tgl = $day. ', '. $date.' '. $month.' '. $year;

        return $tgl;
    }

    public static function formatTanggalHari($input_date) {
        $tgl = '';

        $dayName = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $monthName = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $getDate = new DateTime($input_date);
        $day = $dayName[$getDate->format('w')];
        $date = $getDate->format('d');
        $month = $getDate->format('n');
        $year = $getDate->format('Y');

        $tgl = $day. ', '. $date.'/'. $month.'/'. $year;

        return $tgl;
    }

    public static function diffDatetimeStr($start, $end) {
        $date1 = new DateTime($start);
        $date2 = new DateTime($end);

        $interval = $date1->diff($date2);
        $totalHours = $interval->h;
        $totalMinutes = $interval->i;

        $totalHours += $interval->days * 24;

        return $totalHours." Jam ".$totalMinutes." Menit";
    }

    public static function diffDatetime($start, $end) {
        $date1 = new DateTime($start);
        $date2 = new DateTime($end);

        $interval = $date1->diff($date2);
        $totalHours = $interval->h;
        $totalMinutes = $interval->i;

        $totalHours += $interval->days * 24;

        $totalHoursFormatted = str_pad($totalHours, 2, '0', STR_PAD_LEFT);
        $totalMinutesFormatted = str_pad($totalMinutes, 2, '0', STR_PAD_LEFT);

        return $totalHoursFormatted.":".$totalMinutesFormatted.":00";
    }

    public static function compressImage($image, $type = 'jpg') { // png, jpeg, jpg
        $img = ImageManagerStatic::make($image);
                $img->encode($type, 75);

        return $img->stream()->detach();
    }
}
