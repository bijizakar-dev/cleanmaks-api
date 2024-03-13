<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = User::query();

        if ($request->has('search')) {
            $user->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $result = $user->paginate(10);

        return view('masterdata.user.index', compact('result'));
    }

    public function create()
    {
        // $employee = Employee::pluck('id', 'name', 'email', 'unit_id');
        $employee = Employee::with('divisi')->get();

        return view('masterdata.user.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:2048|unique:users',
            'password' => 'required|string',
            'employee_id' => 'required|integer|unique:users',
            'status' => 'integer',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'employee_id' => $request->input('employee_id'),
            'role' => 0,
            'status' => $request->input('status'),
        ]);

        return redirect()->route('user.index')->with(['success' => 'Data User berhasil ditambahkan!']);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $employee = Employee::with('divisi')->get();

        $result = User::with(['employee'])->findOrFail($id);

        return view('masterdata.user.edit', compact('result', 'employee'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:2048',
            'employee_id' => 'required|integer',
            'status' => 'string|in:0,1',
        ]);

        $user = User::findOrFail($id);
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'employee_id' => $request->input('employee_id'),
            'status' => $request->input('status'),
        ];

        $user->update($data);

        return redirect()->route('user.index')->with(['success' => 'Data User berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        //delete post
        $user->delete();

        //redirect to index
        return redirect()->route('user.index')->with(['success' => 'Data User Berhasil Dihapus!']);
    }
}
