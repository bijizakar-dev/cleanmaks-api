<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $divisi = Divisi::query();

        if ($request->has('search')) {
            $divisi->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $result = $divisi->paginate(10);

        return view('masterdata.divisi.index', compact('result'));
    }


    public function create()
    {
        return view('masterdata.divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        Divisi::create([
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('divisi.index')->with(['success' => 'Data Divisi berhasil ditambahkan!']);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $divisi = Divisi::findOrFail($id);

        return view('masterdata.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        $divisi = Divisi::findOrFail($id);

        $divisi->update([
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('divisi.index')->with(['success' => 'Data Divisi berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        //get post by ID
        $divisi = Divisi::findOrFail($id);

        //delete post
        $divisi->delete();

        //redirect to index
        return redirect()->route('divisi.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
