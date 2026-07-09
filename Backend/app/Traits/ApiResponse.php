<?php
// app/Traits/ApiResponse.php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = [], string $message = '', int $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function error(string $message = '', int $code = 400, $data = null)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}