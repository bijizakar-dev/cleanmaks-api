<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsencesRequest;
use App\Models\Absence;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
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

            //store image
            if($request->hasFile('image')){
                $path = $request->file('image')->store('public/photos/absences');
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

    public function absenceList(Request $request) {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $type = $request->input('type');
        $date_start = ($request->input('date_start') != null) ? $request->input('date_start') : '';
        $date_end = ($request->input('date_end') != null) ? $request->input('date_end') : '';
        $limit = $request->input('limit', 10);

        $absenceQuery = Absence::query();

        if($id) {
            $absence = $absenceQuery->find($id);

            if($absence) {
                return ResponseFormatter::success([
                    'status' => true,
                    'msg' => 'Absence Found',
                    'data' => $absence
                ]);
            }

            return ResponseFormatter::error([
                'status' => false,
                'msg' => 'Employee not found'
            ], 500);
        }

        $absences = $absenceQuery;

        if($user_id != null && $user_id != '') {
            $absences->where('user_id', '=', $user_id);
        }

        if($type != null && $type != '') {
            $absences->where('type', '=', $type);
        }

        if(($date_start !== '') & ($date_end !== '')) {
            $absences->whereBetween('date', [$date_start, $date_end]);
        }

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Absences Found',
            'data' => $absences->paginate($limit)
        ]);

    }

    public function radiusAbsence(Request $request) {
        try {
            //validate parameters
            $request->validate([
                'latitude' => ['required', 'string'], // -7.512453
                'longitude' => ['required', 'string'] // 110.225730
            ]);

            $coordinates = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];

            $distance = $this->calculateRadius($coordinates);

            $data['type'] = 'QR';
            $data['distance'] = $distance;
            $data['status'] = 'In Radius';

            if($distance > 0.1) { // batas toleransi 100m
                $data['type'] = 'Selfie';
                $data['distance'] = $distance;
                $data['status'] = 'Out Radius';
            }

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
}
