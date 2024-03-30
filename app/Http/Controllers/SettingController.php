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
            'working_hour' => 'required|integer',
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
}
