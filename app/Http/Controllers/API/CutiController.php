<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
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

            if($request->hasFile('file')){
                $path = $request->file('file')->store('public/files/cuti');
            }

            $createCuti = Cuti::create([
                'date' => date('Y-m-d h:i:s'),
                'employee_id_applicant' => (!empty($request->employee_id_applicant))?$request->employee_id_applicant : $user->employee_id,
                'employee_id_replacement' => (!empty($request->employee_id_replacement))?$request->employee_id_replacement : null,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
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

}
