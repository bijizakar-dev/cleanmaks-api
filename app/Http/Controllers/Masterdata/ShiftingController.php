<?php

namespace App\Http\Controllers\Masterdata;

use App\Http\Controllers\Controller;
use App\Models\Shifting;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ShiftingController extends Controller
{

    private function betweenTime($start, $end) {
         // Pisahkan jam dan menit dari string waktu
        list($startHour, $startMinute) = explode(':', $start);
        list($endHour, $endMinute) = explode(':', $end);

        // Konversi semuanya ke menit
        $startTotalMinutes = $startHour * 60 + $startMinute;
        $endTotalMinutes = $endHour * 60 + $endMinute;

        // Jika end time lebih awal dari start time, tambahkan 24 jam dalam menit
        if ($endTotalMinutes <= $startTotalMinutes) {
            $endTotalMinutes += 24 * 60;
        }

        // Hitung selisih waktu dalam menit
        $timeDiff = $endTotalMinutes - $startTotalMinutes;

        // Menghitung jam dan menit dari selisih waktu
        $hours = floor($timeDiff / 60);
        $minutes = $timeDiff % 60;

        // Memformat jam dan menit menjadi dua digit
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        return $formattedHours . ':' . $formattedMinutes;
    }

    public function index(Request $request) {
        $search = $request->get('search');
        $limit = $request->get('limit', 10);

        $shiftings = Shifting::query();

        if ($search) {
            $shiftings->where('name', 'like', "%$search%");
        }

        $shiftings = $shiftings->paginate($limit);
        return view('masterdata.shifting.index', compact('shiftings'));
    }

    public function create()
    {
        return view('masterdata.shifting.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'time_start' => 'required',
            'time_end' => 'required',
            'status' => 'required|in:0,1',
        ]);

        $time_diff = $this->betweenTime($request->input('time_start'), $request->input('time_end'));

        Shifting::create([
            'name' => $request->input('name'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'time_diff' => $time_diff,
            'status' => $request->input('status')
        ]);

        return redirect()->route('shifting.index')->with(['success' => 'Data  berhasil ditambahkan!']);
    }

    public function edit(string $id)
    {
        $shifting = Shifting::findOrFail($id);

        return view('masterdata.shifting.edit', compact('shifting'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'time_start' => 'required',
            'time_end' => 'required',
            'status' => 'required|in:0,1',
        ]);

        $shifting = Shifting::findOrFail($id);

        $time_diff = $this->betweenTime($request->input('time_start'), $request->input('time_end'));

        $shifting->update([
            'name' => $request->input('name'),
            'time_start' => $request->input('time_start'),
            'time_end' => $request->input('time_end'),
            'time_diff' => $time_diff,
            'status' => $request->input('status')
        ]);

        return redirect()->route('shifting.index')->with(['success' => 'Data Shifting berhasil diubah!']);
    }

    public function destroy(string $id)
    {
        $shifting = Shifting::findOrFail($id);
        $shifting->delete();

        return redirect()->route('shifting.index')->with(['success' => 'Data Shifting Berhasil Dihapus!']);
    }
}
