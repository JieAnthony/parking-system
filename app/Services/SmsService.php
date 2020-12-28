<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Jobs\SendSmsCodeJob;
use Illuminate\Support\Facades\Cache;

class SmsService
{
    /**
     * @param int $username
     * @return bool
     * @throws BusinessException
     */
    public function sendSmsCode(int $username)
    {
        if (! $this->verifyMobile($username)) {
            throw new BusinessException('手机格式不正确');
        }
        $cache = Cache::get($username.'_code', null);
        if ($cache) {
            if (now()->diffInMinutes($cache->get('time')) <= 2) {
                //禁止两分钟内重复请求
                throw new BusinessException('请勿重复发送验证码');
            }
        }
        $data = $this->generate();
        SendSmsCodeJob::dispatch($username, $data->code);

        return Cache::put($username.'_code', $data, 60 * 10);
    }

    /**
     * @param int $username
     * @param int $code
     * @return bool
     * @throws BusinessException
     */
    public function check(int $username, int $code)
    {
        if (app()->isLocal()) {
            return true;
        }
        $cacheCode = Cache::get($username.'_code', null);
        if ($cacheCode) {
            if ($code == $cacheCode->get('code')) {
                return true;
            } else {
                throw new BusinessException('验证码错误！请重新输入');
            }
        } else {
            throw new BusinessException('已超时，请重新获取验证码');
        }
    }

    /**
     * @param $to
     * @return false|int
     */
    protected function verifyMobile($to)
    {
        return preg_match('/^(?=\d{11}$)^1(?:3\d|4[57]|5[^4\D]|6[56]|7[^249\D]|8\d|9[189])\d{8}$/', $to);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function generate()
    {
        return collect([
            'code' => $this->generateCode(config('info.sms_length')),
            'time' => now(),
        ]);
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateCode(int $length = 6)
    {
        $characters = '0123456789';
        $charLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charLength - 1)];
        }

        return $randomString;
    }
}
