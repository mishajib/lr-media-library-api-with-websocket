<?php

use Illuminate\Http\JsonResponse;


// Success response
if (!function_exists('success_response')) {
    function success_response($message, $status_code, $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status_code);
    }
}

// Error response
if (!function_exists('error_response')) {
    function error_response($message, $status_code, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $status_code);
    }
}
