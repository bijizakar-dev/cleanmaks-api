<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAbsence;
use Illuminate\Http\Request;

class AbsenPegawaiController extends Controller
{
    // public function lama(Request $request) {
    //     $result = Absence::select('a.user_id', 'a.date as date_clock_in', 'a.date', 'em.name', 'a.address as in_address')
    //             ->from('absences as a')
    //             ->join('users as u', 'a.user_id', '=', 'u.id')
    //             ->join('employees as em', 'u.employee_id', '=', 'em.id')
    //             ->selectSub(function ($query) {
    //                 $query->from('absences as b')
    //                     ->selectRaw('MIN(b.date)')
    //                     ->whereColumn('b.user_id', 'a.user_id')
    //                     ->where('b.date', '>=', DB::raw('a.date'))
    //                     ->where('b.type', 'OUT');
    //             }, 'date_clock_out')
    //             ->selectSub(function ($query) {
    //                 $query->from('absences as b')
    //                     ->select('b.address')
    //                     ->whereColumn('b.user_id', 'a.user_id')
    //                     ->where('b.date', '>=', DB::raw('a.date'))
    //                     ->where('b.type', 'OUT')
    //                     ->limit(1);
    //             }, 'out_address')
    //             ->where('a.type', 'IN')
    //             ->orderBy('a.date')
    //             ->paginate(10);

    //     $setting = Setting::find(1);

    //     return view('layanan.absen.index', compact('result', 'setting'));
    // }

    public function index(Request $request) {
        $absen = EmployeeAbsence::select('e.name as employee_name', 'a.*')
                ->from('employee_absences as a')
                ->join('employees as e', 'e.id', '=', 'employee_id');

        $result = $absen->paginate(20);

        foreach ($result as $val) {
            $decodeSche = json_decode($val->schedule);
            $val->schedule = $decodeSche;
        }

        return view('layanan.absen.index', compact('result'));

    }
}
