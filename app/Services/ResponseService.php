<?php
namespace App\Services;

class ResponseService
{
    public static function success($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    // return ApiResponse::success($data);

    public static function error($message = 'Error', $status = 400, $data = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    // return ApiResponse::error('Blog not found', 404);

    // try-catch, exception
    public static function exception(\Throwable $e)
    {
        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
            'data' => null,
        ], 500);
    }
    /*
    try {
        
    } catch (\Throwable $e) {
        return ApiResponse::exception($e);
    }
    */
}