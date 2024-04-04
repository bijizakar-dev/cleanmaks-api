<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsencesRequest;
use App\Models\Absence;
use App\Models\Employee;
use App\Models\EmployeeAbsence;
use App\Models\EmployeeAbsenceLog;
use App\Models\EmployeeSchedule;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class EmployeeAbsenceController extends Controller
{
    public function clock(Request $request) {
        try {
            $request->validate([
                'date' => ['string'],
                'latitude' => ['required', 'string'],
                'longitude' => ['required', 'string'],
                'address' => ['required', 'string'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
                'type' => ['required', 'string', 'in:IN,OUT']
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/photos/absences');
                $path = str_replace('public/photos/absences', 'storage/photos/absences', $path);
            }

            $user = auth()->user()->id;
            $employee_id = auth()->user()->employee_id;
            $type = $request->input('type');

            $today = now()->toDateString();
            $dayToday = date('l', strtotime($today));

            $dataSchedule = EmployeeSchedule::where('employee_id', $employee_id)
                            ->where('day', $dayToday)
                            ->first();

            $dataAbsence = EmployeeAbsence::where('date', $today)
                            ->where('employee_id', $employee_id)
                            ->first();


            if($dataAbsence == null) {
                //check apakah pegawai mempunyai jadwal hari ini
                if($dataSchedule == null) {
                    throw new Exception('Tidak terdapat jadwal shift hari ini!');
                } else {
                    // Create row absen baru pasti IN Row Baru
                    if($type == 'OUT') {
                        throw new Exception('Belum melakukan Clock In');
                    }

                    $dbClock = EmployeeAbsence::create([
                        'date' => $today,
                        'user_id' => $user,
                        'employee_id' => $employee_id,
                        'clock_in' => ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s'),
                        'location_in' => $request->input('address'),
                        'latitude_longitude_in' => $request->input('latitude').' / '.$request->input('longitude'),
                        'image_in' => isset($path) ? $path : '',
                        'schedule' => json_encode($dataSchedule->toJson(), true),
                        'status' => 'On Working',
                    ]);
                }
            } else {
                if($dataAbsence->clock_in != null && $dataAbsence->latitude_longitude_in != '') {
                    if($type == 'IN') {
                        throw new Exception('sudah melakukan Clock In');
                    } else {
                        $working_hour = strtotime($dataSchedule->time_diff) * 1000;
                        $start_work = strtotime($dataSchedule->time_start) * 1000 ;

                        $clockOut = ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s');
                        $clockIn_work = strtotime(date('H:i:s', strtotime($dataAbsence->clock_in))) * 1000;
                        $clockOut_work = strtotime(date('H:i:s', strtotime($clockOut))) * 1000;

                        $total_workingTime = $clockOut_work - $clockIn_work;

                        $status = 'On Working';

                        if ($start_work < $clockIn_work && $working_hour > $total_workingTime) {
                            $status = 'Telat & Tidak Memenuhi';
                        } else if ($start_work < $clockIn_work && $working_hour <= $total_workingTime) {
                            $status = 'Telat & Memenuhi';
                        } else if ($start_work >= $clockIn_work && $working_hour > $total_workingTime) {
                            $status = 'Tepat Waktu & Tidak Memenuhi';
                        } else if ($start_work >= $clockIn_work && $working_hour <= $total_workingTime) {
                            $status = 'Tepat Waktu & Memenuhi';
                        }

                        $dbClock = $dataAbsence->update([
                            'clock_out' => ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s'),
                            'location_out' => $request->input('address'),
                            'latitude_longitude_out' => $request->input('latitude').' / '.$request->input('longitude'),
                            'image_out' => isset($path) ? $path : '',
                            'total_hour' => gmdate('H:i:s', $total_workingTime / 1000),
                            'status' => $status,
                        ]);
                    }
                } else {
                    if($type == 'OUT') {
                        throw new Exception('Belum melakukan Clock In');
                    } else {
                        $dbClock = $dataAbsence->update([
                            'date' => $today,
                            'user_id' => $user,
                            'employee_id' => $employee_id,
                            'clock_in' => ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s'),
                            'location_in' => $request->input('address'),
                            'latitude_longitude_in' => $request->input('latitude').' / '.$request->input('longitude'),
                            'image_in' => isset($path) ? $path : '',
                            'schedule' => json_encode($dataSchedule->toJson(), true),
                            'status' => 'On Working',
                        ]);
                    }
                }
            }

            if(!$dbClock) {
                throw new Exception('Data not recorded in database');
            }

            $lastAbsence = EmployeeAbsence::where('date', $today)
                            ->where('employee_id', $employee_id)
                            ->first();

            //create Absen Log
            $Createlog = EmployeeAbsenceLog::create([
                'date' => date('Y-m-d H:i:s'),
                'employee_absences_id' => $lastAbsence->id,
                'absence' => $request->input('type'),
                'type' => 'Selfie',
                'device_info' => !empty($request->input('device_info')) ? $request->input('device_info') : '-',
                'user_id' => $user
            ]);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Absence Clock Recorded',
                'data' => $Createlog
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

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
            $data['message'] = '';

            $user = auth()->user();
            $employee_id = auth()->user()->employee_id;

            $today = now()->toDateString();
            $dayToday = date('l', strtotime($today));

            $dataSchedule = EmployeeSchedule::where('employee_id', $employee_id)
                            ->where('day', $dayToday)
                            ->first();

            $dataAbsence = EmployeeAbsence::where('date', $today)
                            ->where('employee_id', $employee_id)
                            ->first();

            if($dataAbsence == null) {
                $data['message'] = 'Silahkan Clock In';
                $data['absence'] = 'IN';
            } else {
                if($dataAbsence->clock_in != null && $dataAbsence->clock_out != null) {
                    $data['message'] = 'Sudah Absen Untuk Hari Ini';
                    $data['absence'] = '-';
                } else if ($dataAbsence->clock_in == null && $dataAbsence->clock_out == null) {
                    $data['message'] = 'Silahkan Clock In';
                    $data['absence'] = 'IN';
                } else if ($dataAbsence->clock_out != null && $dataAbsence->clock_out == null) {
                    $data['message'] = 'Silahkan Clock Out ';
                    $data['absence'] = 'OUT';
                } else {
                    $data['message'] = 'Silahkan Clock IN ';
                    $data['absence'] = 'IN';
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
