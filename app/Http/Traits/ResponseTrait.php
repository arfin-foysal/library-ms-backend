<?php

namespace app\Http\Traits;
trait ResponseTrait
{
    /**
     * @param $data
     * @param $message
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data, $message, $status)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }
}
