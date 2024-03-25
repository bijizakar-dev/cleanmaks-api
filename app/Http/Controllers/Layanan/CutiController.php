<?php

namespace App\Http\Controllers\Layanan;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Employee;
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

    public function edit_status(Request $request, $id) {
        try {
            $statusValue = $request->input('status');

            $status = Cuti::find($id);

            if(!$status) {
                throw new Exception('Ajuan Cuti tidak ditemukan');
            }

            $user = auth()->user();

            $status->update([
                'status' => $statusValue,
                'user_id_decide' => $user->id,
                'verified_at' => date('Y-m-d H:i:s')
            ]);

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
}
