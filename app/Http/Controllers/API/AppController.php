<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsencesRequest;
use App\Models\Absence;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function getSetting() {
        try {
            $settingCompany = Setting::first();

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Setting Get Successfully',
                'data' => $settingCompany
            ]);
        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateSetting(Request $request) {
        try {
            $request->validate([
                'name' => ['required', 'string'],
                'address' => ['required', 'string'],
                'latitude' => ['required', 'string'],
                'longitude' => ['required', 'string'],
                'type' => ['required', 'string'],
                'working_hour' => ['required', 'integer'],
                'status' => ['required', 'string'],
                // 'status' => ['required', 'string', 'in:Submitted,Pending,Approved,Rejected,Cancelled']
            ]);

            if($request->hasFile('logo')){
                $path = $request->file('logo')->store('public/setting/');
            }

            $param = array(
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'logo' => isset($path) ? $path : '',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'type' => $request->type,
                'working_hour' => (int)$request->working_hour,
                'code' => $request->code,
                'status' => $request->status,
            );

            //check setting company is exists ?
            if(Setting::count() <= 0) {
                $settingCompany = Setting::create($param);
            } else {
                $settingCompany = Setting::first();
                $settingCompany->update($param);
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Setting Updated Successfully',
                'data' => $settingCompany
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

}
