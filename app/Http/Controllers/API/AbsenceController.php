<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsencesRequest;
use App\Models\Absence;
use App\Models\Employee;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class AbsenceController extends Controller
{
    public function clock(Request $request) {
        try {
            //validate Request
            $request->validate([
                'date' => ['required', 'string'],
                'latitude' => ['required', 'string'],
                'longitude' => ['required', 'string'],
                'address' => ['required', 'string'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
                'type' => ['required', 'string', 'in:IN,OUT']
            ]);

            //check last absence user
            $user = auth()->user()->id;
            $lastAbsence = Absence::last_absence_user($user);

            $date_clock = $request->date;

            //Check validation status user absence
            // 1. Check apakah type absen sama dengan yg di request
            if(!empty($lastAbsence)) {
                if($lastAbsence->type != $request->type) {
                    // 2. Check type Out / In
                    if($request->type == 'OUT') {
                        // 3. Check ClockOut dihari yg berbeda dengan ClockIn (Tidak bisa absen dihari yg berbeda)
                        if(date('Y-m-d', strtotime($lastAbsence->date)) != date('Y-m-d', strtotime($request->date))){
                            $date_clock = $lastAbsence->date;
                        }
                    }
                } else {
                    $type = $lastAbsence->type == 'IN' ? 'Pulang' : 'Masuk';
                    throw new Exception('Silahkan Absen '.$type.' terlebih dahulu');
                }
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/photos/absences'); // Simpan file di dalam direktori storage/app/files/cuti
                $path = str_replace('public/photos/absences', 'storage/photos/absences', $path); // Ubah path agar sesuai dengan penyimpanan publik
            }

            //Create Absence User
            $createClock = Absence::create([
                'date' => ($date_clock != '') ? $date_clock : date('Y-m-d H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'image' => isset($path) ? $path : '',
                'type' => $request->type,
                'user_id' => isset($request->user_id) ? $request->user_id : auth()->user()->id
            ]);

            if(!$createClock) {
                throw new Exception('ClockIn not created');
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Absence ClockIn Created',
                'data' => $createClock
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    // public function absenceList(Request $request) {
    //     $id = $request->input('id');
    //     $user_id = $request->input('user_id');
    //     $type = $request->input('type');
    //     $date_start = ($request->input('date_start') != null) ? $request->input('date_start') : '';
    //     $date_end = ($request->input('date_end') != null) ? $request->input('date_end') : '';
    //     $limit = $request->input('limit', 10);

    //     $absenceQuery = Absence::query();

    //     if($id) {
    //         $absence = $absenceQuery->find($id);

    //         if($absence) {
    //             return ResponseFormatter::success([
    //                 'status' => true,
    //                 'msg' => 'Absence Found',
    //                 'data' => $absence
    //             ]);
    //         }

    //         return ResponseFormatter::error([
    //             'status' => false,
    //             'msg' => 'Employee not found'
    //         ], 500);
    //     }

    //     $absences = $absenceQuery;

    //     if($user_id != null && $user_id != '') {
    //         $absences->where('user_id', '=', $user_id);
    //     }

    //     if($type != null && $type != '') {
    //         $absences->where('type', '=', $type);
    //     }

    //     if(($date_start !== '') & ($date_end !== '')) {
    //         $absences->whereBetween('date', [$date_start, $date_end]);
    //     }

    //     return ResponseFormatter::success([
    //         'status' => true,
    //         'msg' => 'Absences Found',
    //         'data' => $absences->paginate($limit)
    //     ]);

    // }

    public function radiusAbsence(Request $request) {
        try {
            //validate parameters
            $request->validate([
                'latitude' => ['required', 'string'], // -7.512453
                'longitude' => ['required', 'string'] // 110.225730
            ]);

            $data['type'] = 'QR';
            $data['distance'] = 0;
            $data['status'] = 'In Radius';
            $data['absence'] = 'IN';

            $user = auth()->user();
            $lastAbsence = Absence::last_absence_user($user->id);
            if(!empty($lastAbsence)) {
                if($lastAbsence->type != 'OUT') {
                    $data['absence'] = 'OUT';
                }
            }

            $coordinates = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];

            $distance = $this->calculateRadius($coordinates);
            $data['distance'] = $distance;

            if($distance > 0.1) { // batas toleransi 100m
                $data['type'] = 'Selfie';
                $data['status'] = 'Out Radius';
            }

            $dataStatus = Employee::employeeStatusCount($user->employee_id);
            $data = array_merge($data, $dataStatus);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Checking Radius Success',
                'data' => $data
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    function calculateRadius($param){
        // get longitude and latitude from setting application
        $settingApp = Setting::first();

        // define longitude and latitude
        $latCom = deg2rad($settingApp->latitude);
        $longCom = deg2rad($settingApp->longitude);
        $latUser = deg2rad($param['latitude']);
        $longUser = deg2rad($param['longitude']);

        // radius earth in KM
        $earthRad = 6371;

        //Calculate Radius
        $latDiff = $latCom - $latUser;
        $longDiff = $longCom - $longUser;

        // Haversine formula
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latCom) * cos($latUser) * sin($longDiff / 2) * sin($longDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRad * $c;

        return $distance;
    }

    public function checkQr(Request $request) {
        try {
            //check qr code
            $code_setting = Setting::check_codeqr_setting();

            if($code_setting->code != $request->qrCode){
                throw new Exception('Checking QR Failed Different Code');
            } else {
                return ResponseFormatter::success([
                    'status' => true,
                    'msg' => 'Checking QR Success',
                    'data' => ''
                ]);
            }


        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function absenceList(Request $request) {
        $user_id = $request->input('user_id');
        $date_start = ($request->input('date_start') != null) ? $request->input('date_start') : '';
        $date_end = ($request->input('date_end') != null) ? $request->input('date_end') : '';
        $limit = $request->input('limit', 10);

        $setting = Setting::find(1);

        $start_work = strtotime($setting->time_in) * 1000;
        $end_work = strtotime($setting->time_out) * 1000;
        $working_hour = strtotime($setting->working_hour) * 1000;

        $absences = Absence::select('a.user_id', 'a.date', 'em.name', 'a.date as date_clock_in', 'a.address as in_address')
                ->from('absences as a')
                ->join('users as u', 'a.user_id', '=', 'u.id')
                ->join('employees as em', 'u.employee_id', '=', 'em.id')
                ->selectSub(function ($query) {
                    $query->from('absences as b')
                        ->selectRaw('MIN(b.date)')
                        ->whereColumn('b.user_id', 'a.user_id')
                        ->where('b.date', '>=', DB::raw('a.date'))
                        ->where('b.type', 'OUT');
                }, 'date_clock_out')
                ->selectSub(function ($query) {
                    $query->from('absences as b')
                        ->select('b.address')
                        ->whereColumn('b.user_id', 'a.user_id')
                        ->where('b.date', '>=', DB::raw('a.date'))
                        ->where('b.type', 'OUT')
                        ->limit(1);
                }, 'out_address')
                ->selectRaw('TIMEDIFF((SELECT date_clock_out), a.date) as time_difference')
                ->where('a.type', 'IN')
                ->orderBy('a.date');

        if($user_id != null && $user_id != '') {
            $absences->where('a.user_id', '=', $user_id);
        }

        if(($date_start !== '') & ($date_end !== '')) {
            $absences->whereBetween('a.date', [$date_start, $date_end]);
        }

        $result = $absences->paginate($limit);

        foreach ($result as $val) {
            $val->status = 'Belum Pulang';

            if($val->date_clock_out != null && $val->time_difference != null) {
                $clockInTime = strtotime(date('H:i:s', strtotime($val->date_clock_in))) * 1000;
                $diffTime = strtotime($val->time_difference) * 1000;
                if ($start_work < $clockInTime && $working_hour > $diffTime) {
                    $val->status = 'Telat & Tidak Memenuhi';
                } else if ($start_work < $clockInTime && $working_hour <= $diffTime) {
                    $val->status = 'Telat & Memenuhi';
                } else if ($start_work >= $clockInTime && $working_hour <= $diffTime) {
                    $val->status = 'Tepat Waktu & Memenuhi';
                }
            }

        }
        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Absences Found',
            'data' => $result,
        ]);
    }
}
