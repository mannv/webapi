<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successResponse($data)
    {
        return response()->json($data);
    }

    protected function errorResponse($message, $httpStatusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'error' => true,
            'message' => $message
        ], $httpStatusCode);
    }
}
