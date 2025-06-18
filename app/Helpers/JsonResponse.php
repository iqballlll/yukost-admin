<?php

if (!function_exists('jsonResponse')) {



    function jsonResponse(
        string $message = 'OK',
        mixed $data = null,
        string $error = '',
        int $code = 200
    ) {

        return response()->json([
            'code' => $code,
            'message' => $message,
            'data'    => $data,
            'error'  => $error,
        ], $code);
    }
}
