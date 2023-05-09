<?php

namespace App\Traits;

trait apiResponse
{
    public function sendSuccessApi($result = [], $message = "Thành công",$code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'result'    => $result,
        ];

        return response()->json($response, $code);
    }


    public function sendErrorApi($result = [],$message = "Thất bại", $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($result)) {
            $response['result'] = $result;
        }

        return response()->json($response,  $code);
    }
}
