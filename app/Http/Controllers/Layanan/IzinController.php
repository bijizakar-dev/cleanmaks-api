<?php

namespace App\Http\Controllers\Layanan;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\JenisType;
use App\Models\Permit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

class IzinController extends Controller
{
    public function index(Request $request) {
        $permits = Permit::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');

            $permits->whereHas('applicant', function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $result = $permits->paginate(10);

        return view('layanan.izin.index', compact('result'));
    }

    public function detail($id) {
        // $permit = Permit::with('applicant.divisi', 'applicant.jabatan', 'user_decide')->find($id);
        $permit = new Permit();
        $data = $permit->detail($id);

        if (!$data) {
            return response()->json(['message' => 'Permit not found'], 200);
        }

        return response()->json($data, 200);
    }

    public function create() {
        $employee = Employee::with('divisi')->get();
        $type = JenisType::where('category', '=', 'Izin')
                        ->where('status', '=', '1')
                        ->get();

        return view('layanan.izin.create', compact('employee', 'type'));
    }

    public function store(Request $request) {
        $request->validate([
            'employee_id_applicant' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required|integer',
            'reason' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $total_day = $this->hitungHariIzin($request->input('start_date'), $request->input('end_date'));

        if($request->hasFile('image')){
            $imageCom = ImageManagerStatic::make($request->file('image'))->encode('jpg', 50);
            $path = 'public/photos/izin/' . uniqid() . '.jpg';
            Storage::disk('local')->put($path, $imageCom->stream());

            $path = str_replace('public/photos/izin', 'storage/photos/izin', $path);
        }

        Permit::create([
            'employee_id_applicant' => $request->input('employee_id_applicant'),
            'date' => date('Y-m-d H:i:s'),
            'type' => $request->input('type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'total' => $total_day,
            'reason' => $request->input('reason'),
            'image' => isset($path) ? $path : '',
            'status' => 'Submitted'
        ]);

        return redirect()->route('izin.index')->with(['success' => 'Data Perizinan berhasil ditambahkan!']);

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

    public function edit_status(Request $request, $id) {
        try {
            $statusValue = $request->input('status');

            $status = Permit::find($id);

            if(!$status) {
                throw new Exception('Ajuan Perizinan tidak ditemukan');
            }

            $user = auth()->user();

            $status->update([
                'status' => $statusValue,
                'user_id_decide' => $user->id,
                'verified_at' => date('Y-m-d H:i:s')
            ]);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Status Ajuan Perizinan berhasil diubah',
                'data' => $status
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $employee = Employee::with('divisi')->get();
        $type = JenisType::where('category', '=', 'Izin')
                ->where('status', '=', '1')
                ->get();

        $result = Permit::with(['applicant'])->findOrFail($id);

        return view('layanan.izin.edit', compact('result', 'employee', 'type'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'employee_id_applicant' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required|string',
            'reason' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $total_day = $this->hitungHariIzin($request->input('start_date'), $request->input('end_date'));

        $permit = Permit::findOrFail($id);

        $data = [
            'employee_id_applicant' => $request->input('employee_id_applicant'),
            'date' => date('Y-m-d H:i:s'),
            'type' => $request->input('type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'total' => $total_day,
            'reason' => $request->input('reason'),
            'status' => 'Submitted'
        ];

        if($request->hasFile('image')){
            $imageCom = ImageManagerStatic::make($request->file('image'))->encode('jpg', 50);
            $path = 'public/photos/izin/' . uniqid() . '.jpg';
            Storage::disk('local')->put($path, $imageCom->stream());

            $path = str_replace('public/photos/izin', 'storage/photos/izin', $path);

            $data['image'] = $path;
        }

        $permit->update($data);

        return redirect()->route('izin.index')->with(['success' => 'Data Perizinan berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $permit = Permit::findOrFail($id);

        //delete image
        if($permit->image != null) {
            Storage::delete($permit->image);
        }

        //delete post
        $permit->delete();

        //redirect to index
        return redirect()->route('izin.index')->with(['success' => 'Data Perizinan Berhasil Dihapus!']);
    }
}
