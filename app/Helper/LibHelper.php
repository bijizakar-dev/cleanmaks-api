<?php

namespace App\Helper;

use DateTime;

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

    public static function diffDatetime($start, $end) {
        $date1 = new DateTime($start);
        $date2 = new DateTime($end);

        $interval = $date1->diff($date2);
        $totalHours = $interval->h;
        $totalMinutes = $interval->i;

        $totalHours += $interval->days * 24;

        return $totalHours." Jam ".$totalMinutes." Menit";
    }
}
