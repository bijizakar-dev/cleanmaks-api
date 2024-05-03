<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Employee;
use App\Models\EmployeeCuti;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['divisi', 'jabatan']);

        if ($request->has('search')) {
            $employees->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $result = $employees->paginate(10);

        return view('masterdata.employee.index', compact('result'));
    }

    public function create()
    {
        $divisi = Divisi::pluck('name', 'id');
        $jabatan = Jabatan::pluck('name', 'id');

        return view('masterdata.employee.create', compact('divisi', 'jabatan'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:2048|unique:employees',
            'gender' => 'required|string|in:M,F',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit_id' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'is_verified' => 'required|integer',
        ]);

        if($request->hasFile('photo')){
            $imageCom = ImageManagerStatic::make($request->file('photo'))->encode('jpg', 50);
            $path = 'public/photos/employees/' . uniqid() . '.jpg';
            Storage::disk('local')->put($path, $imageCom->stream());

            $path = str_replace('public/photos/employees', 'storage/photos/employees', $path);

        }

        Employee::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'age' => 0,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'photo' => isset($path) ? $path : '',
            'unit_id' => $request->input('unit_id'),
            'role_id' => 0,
            'jabatan_id' => $request->input('jabatan_id'),
            'is_verified' => $request->input('is_verified'),
        ]);

        return redirect()->route('employees.index')->with(['success' => 'Data Pegawai berhasil ditambahkan!']);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $divisi = Divisi::pluck('name', 'id');
        $jabatan = Jabatan::pluck('name', 'id');

        $result = Employee::with(['jabatan', 'divisi', 'employeeCuti'])->findOrFail($id);

        return view('masterdata.employee.detail', compact('result', 'divisi', 'jabatan'));
    }

    public function update(Request $request, string $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:2048',
            'gender' => 'required|string|in:M,F',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit_id' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'is_verified' => 'required|integer',
            'quota_cuti' => 'integer',
        ]);

        $employee = Employee::findOrFail($id);
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'age' => 0,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'unit_id' => $request->input('unit_id'),
            'role_id' => 0,
            'jabatan_id' => $request->input('jabatan_id'),
            'is_verified' => $request->input('is_verified'),
        ];

        if($request->hasFile('photo')){
            $imageCom = ImageManagerStatic::make($request->file('photo'))->encode('jpg', 50);
            $path = 'public/photos/employees/' . uniqid() . '.jpg';
            Storage::disk('local')->put($path, $imageCom->stream());

            $path = str_replace('public/photos/employees', 'storage/photos/employees', $path);

            $data['photo'] = $path;
        }

        $employee->update($data);

        //check quota cuti employee
        $cuti = EmployeeCuti::where('employee_id', $id)->first();
        if(!empty($cuti)) {
            $cuti->update(['quota' => $request->input('quota_cuti')]);
        } else {
            EmployeeCuti::create([
                'employee_id' => $id,
                'quota' => $request->input('quota_cuti')
            ]);
        }

        return redirect()->back()->with(['success' => 'Data Pegawai berhasil diubah!']);
    }

    public function destroy(string $id) {
        $employee = Employee::findOrFail($id);

        //delete image
        if($employee->photo != null) {
            Storage::delete($employee->photo);
        }

        //delete post
        $employee->delete();

        //redirect to index
        return redirect()->route('employees.index')->with(['success' => 'Data Pegawai Berhasil Dihapus!']);
    }
}
