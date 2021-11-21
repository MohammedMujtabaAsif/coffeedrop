<?php

namespace App\Traits;

trait ResponseTrait
{

    public function responseWithData($data, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $code);
    }

    public function responseWithMessageSuccess($message, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $code);
    }

    public function responseWithMessageFailed($message, $code = 200)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    public function responseWithNotFoundError($type)
    {
        $message = 'Failed To Find ' . $type;
        return $this->responseWithMessageFailed($message, 404);
    }
}
