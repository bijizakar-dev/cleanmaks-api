<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\HariLibur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $type = HariLibur::query();

        if ($request->has('search')) {
            $type->where('name', 'LIKE', '%'.$request->input('search').'%')
                ->orWhere('date', 'LIKE', '%'.$request->input('search').'%');
        }

        $result = $type->paginate(10);

        return view('masterdata.hariLibur.index', compact('result'));
    }

    public function edit(Request $request, $id) {
        try {
            $name = $request->input('name');
            $date = $request->input('date');
            $is_cuti = $request->input('is_cuti');

            $data = HariLibur::find($id);

            if(!$data) {
                throw new Exception('Hari Libur tidak ditemukan');
            }

            $data->update([
                'name' => $name,
                'date' => $date,
                'is_cuti' => $is_cuti
            ]);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Hari Libur berhasil diubah',
                'data' => $data
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $data = HariLibur::findOrFail($id);

        //delete post
        $data->delete();

        //redirect to index
        return redirect()->route('hari-libur.index')->with(['success' => 'Data Hari Libur Berhasil Dihapus!']);
    }

    public function getHariLiburFromAPI() {
        Artisan::call('app:api-hari-libur');

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Artisan tasks executed successfully.',
            'data' => ''
        ]);
    }
}
