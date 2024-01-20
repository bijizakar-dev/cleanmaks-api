<?php

namespace App\Helpers;

class ResponseFormatter {
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success'
        ],
        'response' => null,
    ];

    public static function success($data = null) {
        self::$response['response'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($data = null, $code = 400) {
        self::$response['meta']['code'] = $code;
        self::$response['meta']['status'] = 'error';

        self::$response['response'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}
