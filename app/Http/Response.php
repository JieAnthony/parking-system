<?php

namespace App\Http;

use App\Enums\CodeEnum;

class Response
{
    /**
     * @param string $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(string $message, $data = null)
    {
        return $this->formatData($message, CodeEnum::SUCCESS, $data);
    }

    /**
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail(string $message, int $code = CodeEnum::FAIL)
    {
        return $this->formatData($message, $code);
    }

    /**
     * @param string $message
     * @param int $code
     * @param null $data
     * @param int $status
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatData(string $message, int $code, $data = null, int $status = 200)
    {
        return response()->json([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
        ], $status);
    }
}
