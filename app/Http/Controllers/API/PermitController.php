<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Permit;
use Exception;
use Illuminate\Http\Request;

class PermitController extends Controller
{

    public function create(Request $request) {
        try {
            //validate Request
            $request->validate([
                'type' => ['required', 'string'],
                'start_date' => ['required', 'string'],
                'end_date' => ['required', 'string'],
                'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg'],
                // 'status' => ['required', 'string', 'in:Submitted,Pending,Approved,Rejected,Cancelled']
            ]);
            $user = auth()->user();

            $total_day = $this->hitungHariIzin($request->start_date, $request->end_date);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/files/izin', 'local'); // Simpan file di dalam direktori storage/app/files/cuti
                $path = str_replace('public/files/izin', 'storage/files/izin', $path); // Ubah path agar sesuai dengan penyimpanan publik
            }

            $createPermit = Permit::create([
                'date' => date('Y-m-d H:i:s'),
                'employee_id_applicant' => (!empty($request->employee_id_applicant))?$request->employee_id_applicant : $user->employee_id,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total' => $total_day,
                'reason' => (!empty($request->reason))?$request->reason : null,
                'image' => isset($path) ? $path : '',
                'status' => 'Submitted' //'Submitted,Pending,Approved,Rejected,Cancelled'
            ]);

            if(!$createPermit) {
                throw new Exception('Izin not created');
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Izin Created',
                'data' => $createPermit
            ]);
        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function hitungHariIzin($start_date, $end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);

        $total_day = 0;
        while ($start <= $end) {
            if (date('N', $start) != 6 && date('N', $start) != 7) { // 6 = Sabtu, 7 = Minggu
                $total_day++;
            }
            // Tambah 1 hari
            $start = strtotime('+1 day', $start);
        }

        return $total_day;
    }
}
