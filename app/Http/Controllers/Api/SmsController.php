<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SmsService;

class SmsController extends Controller
{
    /**
     * @param int $username
     * @param SmsService $smsService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function send(int $username, SmsService $smsService)
    {
        return $smsService->sendSmsCode($username)
            ? $this->response()->success('发送成功')
            : $this->response()->fail('发送失败');
    }
}
