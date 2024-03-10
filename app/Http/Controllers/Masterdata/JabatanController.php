<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $jabatan = Jabatan::query();

        if ($request->has('search')) {
            $jabatan->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $result = $jabatan->paginate(10);

        return view('masterdata.jabatan.index', compact('result'));
    }


    public function create()
    {
        return view('masterdata.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        Jabatan::create([
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('jabatan.index')->with(['success' => 'Data Jabatan berhasil ditambahkan!']);
    }

    public function edit(string $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        return view('masterdata.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        $jabatan = Jabatan::findOrFail($id);

        $jabatan->update([
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('jabatan.index')->with(['success' => 'Data Jabatan berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        //get post by ID
        $jabatan = Jabatan::findOrFail($id);

        //delete post
        $jabatan->delete();

        //redirect to index
        return redirect()->route('jabatan.index')->with(['success' => 'Data Jabatan Berhasil Dihapus!']);
    }
}
