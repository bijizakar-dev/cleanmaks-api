<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Employee;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index() {
        $data = array();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $cuti = Cuti::query()
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->orderBy('id', 'DESC')
                    ->get();

        $data['cuti'] = $cuti;
        $data['total_pegawai'] = Employee::count();

        // dd($data);

        return view('dashboard', compact('data'));
    }

}
