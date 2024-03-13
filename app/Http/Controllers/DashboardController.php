<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function index() {

        $data = array();
        $data['total_pegawai'] = Employee::count();

        return view('dashboard', compact('data'));
    }

}
