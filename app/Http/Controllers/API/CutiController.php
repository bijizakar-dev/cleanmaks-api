<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use Exception;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function create(Request $request) {
        try {
            //validate Request
            $request->validate([
                'type' => ['required', 'string'],
                'start_date' => ['required', 'string'],
                'end_date' => ['required', 'string'],
                'file' => ['image', 'mimes:jpeg,png,jpg,gif,svg'],
                // 'status' => ['required', 'string', 'in:Submitted,Pending,Approved,Rejected,Cancelled']
            ]);
            $user = auth()->user();

            $total_day = $this->hitungHariCuti($request->start_date, $request->end_date);

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('public/files/cuti', 'local'); // Simpan file di dalam direktori storage/app/files/cuti
                $path = str_replace('public/files/cuti', 'storage/files/cuti', $path); // Ubah path agar sesuai dengan penyimpanan publik
            }

            $createCuti = Cuti::create([
                'date' => date('Y-m-d h:i:s'),
                'employee_id_applicant' => (!empty($request->employee_id_applicant))?$request->employee_id_applicant : $user->employee_id,
                'employee_id_replacement' => (!empty($request->employee_id_replacement))?$request->employee_id_replacement : null,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total' => $total_day,
                'reason' => (!empty($request->reason))?$request->reason : null,
                'file' => isset($path) ? $path : '',
                'status' => 'Submitted' //'Submitted,Pending,Approved,Rejected,Cancelled'
            ]);

            if(!$createCuti) {
                throw new Exception('Cuti not created');
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Cuti Created',
                'data' => $createCuti
            ]);
        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function fetch(Request $request) {
        try {
            $search = array(
                'id' => $request->input('id'),
                'id_employee' => $request->input('id_employee'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'type' => $request->input('type'),
                'status' => $request->input('status'),
                'limit' => $request->input('limit', 10),
            );

            $cutiInstance = new Cuti();
            $result = $cutiInstance->getListCuti($search);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Cuti Found',
                'data' => $result
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => 'Error fetching cuti data',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function hitungHariCuti($start_date, $end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);

        $total_day = 0;
        while ($start <= $end) {
            if (date('N', $start) != 6 && date('N', $start) != 7) {
                $total_day++;
            }
            // Tambah 1 hari
            $start = strtotime('+1 day', $start);
        }

        return $total_day;
    }
}
