<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\JenisType;
use Exception;
use Illuminate\Http\Request;

class JenisTypeController extends Controller
{
    public function getTypeJenis(Request $request) {
        try {
            $category = $request->input('category');

            $type = JenisType::where('status', '1');

            if($category != null) {
                $type->where('category', $category);
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Jenis Type Found',
                'data' => $type->get()
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }
}
