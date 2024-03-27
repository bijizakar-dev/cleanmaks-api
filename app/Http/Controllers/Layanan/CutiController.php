<?php

namespace App\Http\Controllers\Layanan;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Employee;
use App\Models\EmployeeCuti;
use App\Models\JenisType;
use Exception;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index(Request $request) {
        $cutis = Cuti::query()->orderBy('id', 'DESC');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            $cutis->whereHas('applicant', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $result = $cutis->paginate(10);

        return view('layanan.cuti.index', compact('result'));
    }

    public function detail($id) {
        $cuti = new Cuti();
        $data = $cuti->detail($id);

        if (!$data) {
            return response()->json(['message' => 'Cuti not found'], 200);
        }

        return response()->json($data, 200);
    }

    public function create() {
        $employee = Employee::with('divisi')->get();
        $type = JenisType::where('category', '=', 'Cuti')
                        ->where('status', '=', '1')
                        ->get();

        return view('layanan.cuti.create', compact('employee', 'type'));
    }

    public function checkCutiTahunan($idEmployee) {
        $checkCuti = EmployeeCuti::where('employee_id', $idEmployee)->first();

        if($checkCuti == null) {
            return ResponseFormatter::success([
                'status' => false,
                'msg' => 'Jatah Cuti tidak ditemukan',
                'data' => ''
            ]);
        }

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Jatah Cuti ditemukan',
            'data' => $checkCuti
        ]);
    }

    public function hitungHariCuti($start_date, $end_date)
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


    public function edit_status(Request $request, $id) {
        try {
            $statusValue = $request->input('status');

            $status = Cuti::find($id);
            $kuotaCuti = EmployeeCuti::where('employee_id', $status->employee_id_applicant)->first();

            if(!$status) {
                throw new Exception('Ajuan Cuti tidak ditemukan');
            }

            $user = auth()->user();
            if($status->status == 'Cancelled' || $status->status == 'Rejected') {
                $kuotaCuti->update([
                    'quota' => ($kuotaCuti->quota + $status->total),
                    'quota_used' => ($kuotaCuti->quota_used - $status->total)
                ]);
            } else {
                $status->update([
                    'status' => $statusValue,
                    'user_id_decide' => $user->id,
                    'verified_at' => date('Y-m-d H:i:s')
                ]);
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Status Ajuan Cuti berhasil diubah',
                'data' => $status
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request) {
        $request->validate([
            'employee_id_applicant' => 'required|integer',
            'employee_id_replacement' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'total' => 'required|integer',
            'type' => 'required|integer',
            'reason' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request->hasFile('file')){
            $path = $request->file('file')->store('public/file/cuti');
            $path = str_replace('public/file/cuti', 'storage/file/cuti', $path);
        }

        $total_day = $this->hitungHariCuti($request->input('start_date'), $request->input('end_date'));
        $checkCuti = EmployeeCuti::where('employee_id', $request->input('employee_id_applicant'))->first();

        Cuti::create([
            'employee_id_applicant' => $request->input('employee_id_applicant'),
            'employee_id_replacement' => $request->input('employee_id_replacement'),
            'date' => date('Y-m-d H:i:s'),
            'type' => $request->input('type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'total' => $total_day,
            'reason' => $request->input('reason'),
            'file' => isset($path) ? $path : '',
            'status' => 'Submitted'
        ]);

        $checkCuti->update([
            'quota' => ($checkCuti->quota - $total_day),
            'quota_used' => ($checkCuti->quota_used + $total_day)
        ]);

        return redirect()->route('cuti.index')->with(['success' => 'Data Pengajuan Cuti berhasil ditambahkan!']);

    }
}
