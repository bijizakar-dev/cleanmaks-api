<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\JenisType;
use Illuminate\Http\Request;

class JenisTypeController extends Controller
{
    public function index(Request $request)
    {
        $type = JenisType::query();

        if ($request->has('search')) {
            $type->where('category', 'LIKE', '%'.$request->input('search').'%')
                ->orWhere('name', 'LIKE', '%'.$request->input('search').'%');
        }

        $result = $type->paginate(10);

        return view('masterdata.jenisType.index', compact('result'));
    }

    public function create()
    {
        return view('masterdata.jenisType.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        JenisType::create([
            'category' => $request->input('category'),
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('jenis-type.index')->with(['success' => 'Data Tipe Jenis berhasil ditambahkan!']);
    }

    public function edit(string $id)
    {
        $type = JenisType::findOrFail($id);

        return view('masterdata.jenisType.edit', compact('type'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        $jenis = JenisType::findOrFail($id);

        $jenis->update([
            'category' => $request->input('category'),
            'name' => $request->input('name'),
            'status' => $request->input('status')
        ]);

        return redirect()->route('jenis-type.index')->with(['success' => 'Data Jenis Tipe berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        //get post by ID
        $type = JenisType::findOrFail($id);

        //delete post
        $type->delete();

        //redirect to index
        return redirect()->route('jenis-type.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

}
