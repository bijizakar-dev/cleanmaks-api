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
use Illuminate\Support\Facades\Storage;
use Stevebauman\Location\Facades\Location;
use Intervention\Image\ImageManagerStatic;

class EmployeeAbsenceController extends Controller
{
    public function clock(Request $request) {
        try {
            $request->validate([
                'date' => ['string'],
                'latitude' => ['required', 'string'],
                'longitude' => ['required', 'string'],
                'address' => ['required', 'string'],
                'image' => ['required'],
                'type' => ['required', 'string', 'in:IN,OUT']
            ]);

            if ($request->hasFile('image')) {
                $imageCom = ImageManagerStatic::make($request->file('image'))->encode('jpg', 50);
                $path = 'public/photos/absences/' . uniqid() . '.jpg';
                Storage::disk('local')->put($path, $imageCom->stream());

                $path = str_replace('public/photos/absences', 'storage/photos/absences', $path);
            }

            $user = auth()->user()->id;
            $employee_id = auth()->user()->employee_id;
            $type = $request->input('type');

            $today = now()->toDateString();
            $dayToday = date('l', strtotime($today));

            $settingApp = Setting::find(1);

            $dataSchedule = EmployeeSchedule::where('employee_id', $employee_id)
                            ->where('day', $dayToday)
                            ->where('date', $today)
                            ->first();

            $dataAbsence = EmployeeAbsence::where('date', $today)
                            ->where('employee_id', $employee_id)
                            ->first();

            //check apakah pegawai mempunyai jadwal hari ini
            if($dataSchedule == null) {
                // setting jam kerja mengikuti jam kerja universal
                switch($dayToday) {
                    case 'Monday':
                        $typeSche= $settingApp->monday_type;
                        $timeStart = $settingApp->monday_in;
                        $timeEnd = $settingApp->monday_out;
                        $timeDiff = $settingApp->monday_total;
                        break;
                    case 'Tuesday':
                        $typeSche = $settingApp->tuesday_type;
                        $timeStart = $settingApp->tuesday_in;
                        $timeEnd = $settingApp->tuesday_out;
                        $timeDiff = $settingApp->tuesday_total;
                        break;
                    case 'Wednesday':
                        $typeSche = $settingApp->wednesday_type;
                        $timeStart = $settingApp->wednesday_in;
                        $timeEnd = $settingApp->wednesday_out;
                        $timeDiff = $settingApp->wednesday_total;
                        break;
                    case 'Thursday':
                        $typeSche = $settingApp->thursday_type;
                        $timeStart = $settingApp->thursday_in;
                        $timeEnd = $settingApp->thursday_out;
                        $timeDiff = $settingApp->thursday_total;
                        break;
                    case 'Friday':
                        $typeSche = $settingApp->friday_type;
                        $timeStart = $settingApp->friday_in;
                        $timeEnd = $settingApp->friday_out;
                        $timeDiff = $settingApp->friday_total;
                        break;
                    case 'Saturday':
                        $typeSche = $settingApp->saturday_type;
                        $timeStart = $settingApp->saturday_in;
                        $timeEnd = $settingApp->saturday_out;
                        $timeDiff = $settingApp->saturday_total;
                        break;
                    case 'Sunday':
                        $typeSche = $settingApp->sunday_type;
                        $timeStart = $settingApp->sunday_in;
                        $timeEnd = $settingApp->sunday_out;
                        $timeDiff = $settingApp->sunday_total;
                        break;
                }

                $dataSchedule = (object) [
                    "day" => $dayToday,
                    "time_start" => $timeStart,
                    "time_end" => $timeEnd,
                    "time_diff" => $timeDiff,
                    "status" => $typeSche,
                ];
            }

            if($dataAbsence == null) {
                if($type == 'OUT') {
                    throw new Exception('Belum melakukan Clock In');
                }

                // Create row absen baru pasti IN Row Baru
                $dbClock = EmployeeAbsence::create([
                    'date' => $today,
                    'user_id' => $user,
                    'employee_id' => $employee_id,
                    'clock_in' => ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s'),
                    'location_in' => $request->input('address'),
                    'latitude_longitude_in' => $request->input('latitude').' / '.$request->input('longitude'),
                    'image_in' => isset($path) ? $path : '',
                    'schedule' => json_encode($dataSchedule, true),
                    'status' => 'On Working',
                ]);
            } else {
                if($dataAbsence->clock_in != null && $dataAbsence->latitude_longitude_in != '') {
                    if($type == 'IN') {
                        throw new Exception('sudah melakukan Clock In');
                    } else {
                        $working_hour = strtotime($dataSchedule->time_diff);
                        $start_work = strtotime($dataSchedule->time_start);

                        $clockOut = ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s');
                        $clockIn_work = strtotime(date('H:i:s', strtotime($dataAbsence->clock_in)));
                        $clockOut_work = strtotime(date('H:i:s', strtotime($clockOut)));

                        $total_workingTime = ($clockOut_work - $clockIn_work);

                        $status = 'On Working';

                        if (date('H:i:s', $start_work) < date('H:i:s', $clockIn_work) && date('H:i:s', $working_hour) <= date('H:i:s', $total_workingTime)) {
                            $status = 'Telat & Memenuhi';
                        } else if (date('H:i:s', $start_work) < date('H:i:s', $clockIn_work) && date('H:i:s', $working_hour) > date('H:i:s', $total_workingTime)) {
                            $status = 'Telat & Tidak Memenuhi';
                        } else if (date('H:i:s', $start_work) >= date('H:i:s', $clockIn_work) && date('H:i:s', $working_hour) > date('H:i:s', $total_workingTime)) {
                            $status = 'Tepat Waktu & Tidak Memenuhi';
                        } else if (date('H:i:s', $start_work) >= date('H:i:s', $clockIn_work) && date('H:i:s', $working_hour) <= date('H:i:s', $total_workingTime)) {
                            $status = 'Tepat Waktu & Memenuhi';
                        }

                        $dbClock = $dataAbsence->update([
                            'clock_out' => ($request->input('date') != '') ? $request->input('date') : date('Y-m-d H:i:s'),
                            'location_out' => $request->input('address'),
                            'latitude_longitude_out' => $request->input('latitude').' / '.$request->input('longitude'),
                            'image_out' => isset($path) ? $path : '',
                            'total_hour' => gmdate('H:i:s', $total_workingTime),
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
                            'schedule' => json_encode($dataSchedule, true),
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

            $dataAbsence = EmployeeAbsence::where('date', $today)
                            ->where('employee_id', $employee_id)
                            ->first();

            if($dataAbsence == null) {
                $data['message'] = 'Silahkan Clock In 3';
                $data['absence'] = 'IN';
            } else {
                if($dataAbsence->clock_in != null && $dataAbsence->clock_out != null) {
                    $data['message'] = 'Sudah Absen Untuk Hari Ini';
                    $data['absence'] = '-';
                } else if ($dataAbsence->clock_in == null && $dataAbsence->clock_out == null) {
                    $data['message'] = 'Silahkan Clock In 1';
                    $data['absence'] = 'IN';
                } else if ($dataAbsence->clock_in != null && $dataAbsence->clock_out == null) {
                    $data['message'] = 'Silahkan Clock Out ';
                    $data['absence'] = 'OUT';
                } else {
                    $data['message'] = 'Silahkan Clock IN 2';
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
        $user_id = $request->input('user_id') != null ? $request->input('user_id') : auth()->user()->id;
        $date_start = ($request->input('date_start') != null) ? $request->input('date_start') : '';
        $date_end = ($request->input('date_end') != null) ? $request->input('date_end') : '';
        $limit = $request->input('limit', 10);

        $absences = EmployeeAbsence::select('em.name as employee_name', 'ea.*')
                    ->from('employee_absences as ea')
                    ->join('employees as em', 'ea.employee_id', '=', 'em.id');

        if($user_id != null && $user_id != '') {
            $absences->where('user_id', '=', $user_id);
        }

        if(($date_start !== '') & ($date_end !== '')) {
            $absences->whereBetween('a.date', [$date_start, $date_end]);
        }

        $result = $absences->paginate($limit);

        foreach ($result as $val) {
            $decodeSche = json_decode($val->schedule);

            $val->schedule = $decodeSche;
        }

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Absences Found',
            'data' => $result,
        ]);
    }
}
