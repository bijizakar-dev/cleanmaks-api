<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Employee;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with(['divisi', 'jabatan']);

        $result = $employees->paginate(10);
// dd($result);
        return view('masterdata.employee.index', compact('result'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisi = Divisi::pluck('name', 'id');
        $jabatan = Jabatan::pluck('name', 'id');

        return view('masterdata.employee.create', compact('divisi', 'jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     */
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
            $path = $request->file('photo')->store('public/photos/employees'); // Simpan file di dalam direktori storage/app/files/cuti
            $path = str_replace('public/photos/employees', 'storage/photos/employees', $path); // Ubah path agar sesuai dengan penyimpanan publik
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $divisi = Divisi::pluck('name', 'id');
        $jabatan = Jabatan::pluck('name', 'id');

        $result = Employee::with(['jabatan', 'divisi'])->findOrFail($id);

        return view('masterdata.employee.edit', compact('result', 'divisi', 'jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:2048',
            'gender' => 'required|string|in:M,F',
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'unit_id' => 'required|integer',
            'jabatan_id' => 'required|integer',
            'is_verified' => 'required|integer',
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
            $path = $request->file('photo')->store('public/photos/employees'); // Simpan file di dalam direktori storage/app/files/cuti
            $path = str_replace('public/photos/employees', 'storage/photos/employees', $path); // Ubah path agar sesuai dengan penyimpanan publik

            $data['photo'] = $path;
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with(['success' => 'Data Pegawai berhasil diubah!']);
    }

    public function destroy(string $id)
    {
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
