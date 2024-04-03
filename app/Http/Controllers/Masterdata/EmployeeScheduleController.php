<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function index(Request $request)
    {
        $employees = EmployeeSchedule::select('e.name as nama_pegawai', 'es.*')
                ->from('employee_schedule as es')
                ->join('employees as e', 'e.id', '=', 'es.employee_id')
                ->orderBy('employee_id', 'DESC')
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");

        if ($request->has('search')) {
            $employees->where('e.name', 'like', '%' . $request->input('search') . '%');
        }

        $result = $employees->paginate(10);

        return view('masterdata.employee_schedule.index', compact('result'));
    }

    public function create()
    {
        $employee = Employee::with('divisi')->get();

        return view('masterdata.employee_schedule.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'day' => 'required|string',
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'time_diff' => 'nullable',
        ]);

        $jadwalShift = EmployeeSchedule::where('employee_id', $request->input('employee_id'))
                    ->where('day', $request->input('day'))->get();

        //check data sudah ada jadwal apa belum
        if(count($jadwalShift) != 0) {
            return redirect()->back()->with(['error' => 'Pegawai sudah memiliki jadwal dihari yang sama!']);
        }

        $status = 'Terjadwal';

        if($request->input('time_start') == '' && $request->input('time_end') == '') {
            $status = 'Holiday';
        }

        EmployeeSchedule::create([
            'employee_id' => $request->input('employee_id'),
            'day' => $request->input('day'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'time_diff' => $request->input('time_diff'),
            'status' => $status
        ]);

        return redirect()->route('jadwal-shift.index')->with(['success' => 'Jadwal Pegawai berhasil ditambahkan!']);
    }

    public function edit(string $id)
    {
        $employee = Employee::with('divisi')->get();

        $result = EmployeeSchedule::find($id);

        return view('masterdata.employee_schedule.edit', compact('result', 'employee'));
    }

    public function update(Request $request, String $id) {
        $request->validate([
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'time_diff' => 'nullable',
        ]);

        $jadwal = EmployeeSchedule::findOrFail($id);

        $status = 'Terjadwal';

        if($request->input('time_start') == '' && $request->input('time_end') == '') {
            $status = 'Holiday';
        }

        $data = [
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'time_diff' => $request->input('time_diff'),
            'status' => $status
        ];

        $jadwal->update($data);

        return redirect()->route('jadwal-shift.index')->with(['success' => 'Jadwal Pegawai berhasil diubah!']);

    }

    public function destroy(string $id)
    {
        $jadwal = EmployeeSchedule::findOrFail($id);

        //delete post
        $jadwal->delete();

        //redirect to index
        return redirect()->route('jadwal-shift.index')->with(['success' => 'Jadwal Pegawai Berhasil Dihapus!']);
    }
}
