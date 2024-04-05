<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index() {

        $data = Setting::find(1);

        return view('setting', compact('data'));
    }

    public function update(Request $request) {

        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'code' => 'required|string',
            'working_hour' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'time_in' => 'required',
            'time_out' => 'required',
        ]);

        $data = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone_number' => $request->input('phone_number'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'code' => $request->input('code'),
            'working_hour' => $request->input('working_hour'),
            'time_in' => $request->input('time_in'),
            'time_out' => $request->input('time_out'),
            'type' => 'QR',
            'status' => 'active'
        ];

        if($request->hasFile('logo')){
            $path = $request->file('logo')->store('public/logo');
            $path = str_replace('public/logo', 'storage/logo', $path);

            $data['logo'] = $path;
        }

        $setting = Setting::find(1);
        if(!empty($setting)) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        return redirect()->back()->with(['success' => 'Data Setting berhasil diubah!']);
    }

    public function workingDay() {
        $data = Setting::find(1);

        return view('workingday', compact('data'));
    }

    public function workingUpdate(Request $request) {
        $request->validate([
            'sunday_type' => 'required',
            'monday_type' => 'required',
            'tuesday_type' => 'required',
            'wednesday_type' => 'required',
            'thursday_type' => 'required',
            'friday_type' => 'required',
            'saturday_type' => 'required',
        ]);

        $data = [
            'sunday_type' => $request->input('sunday_type'),
            'sunday_in' => $request->input('sunday_in'),
            'sunday_out' => $request->input('sunday_out'),
            'sunday_total' => $request->input('sunday_total'),
            'monday_type' => $request->input('monday_type'),
            'monday_in' => $request->input('monday_in'),
            'monday_out' => $request->input('monday_out'),
            'monday_total' => $request->input('monday_total'),
            'tuesday_type' => $request->input('tuesday_type'),
            'tuesday_in' => $request->input('tuesday_in'),
            'tuesday_out' => $request->input('tuesday_out'),
            'tuesday_total' => $request->input('tuesday_total'),
            'wednesday_type' => $request->input('wednesday_type'),
            'wednesday_in' => $request->input('wednesday_in'),
            'wednesday_out' => $request->input('wednesday_out'),
            'wednesday_total' => $request->input('wednesday_total'),
            'thursday_type' => $request->input('thursday_type'),
            'thursday_in' => $request->input('thursday_in'),
            'thursday_out' => $request->input('thursday_out'),
            'thursday_total' => $request->input('thursday_total'),
            'friday_type' => $request->input('friday_type'),
            'friday_in' => $request->input('friday_in'),
            'friday_out' => $request->input('friday_out'),
            'friday_total' => $request->input('friday_total'),
            'saturday_type' => $request->input('saturday_type'),
            'saturday_in' => $request->input('saturday_in'),
            'saturday_out' => $request->input('saturday_out'),
            'saturday_total' => $request->input('saturday_total'),
        ];

        $setting = Setting::find(1);
        if(!empty($setting)) {
            $setting->update($data);
        } else {
            return redirect()->back()->with(['error' => 'Setting General Terlebih Dahulu!']);
        }

        return redirect()->back()->with(['success' => 'Data Jam Kerja universal berhasil diubah!']);
    }
}
