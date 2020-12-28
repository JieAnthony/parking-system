<?php

namespace App\Services;

use App\Enums\PaymentEnum;
use App\Enums\UserFromEnum;
use App\Models\User;

class PayService
{
    /**
     * @param string $no
     * @param string $body
     * @param float $price
     * @param int $payment
     * @param User|null $user
     * @return false|string|\Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     */
    public function sendPay(string $no, string $body, float $price, int $payment, User $user = null)
    {
        $order = [
            'out_trade_no' => $no,
        ];
        /** @var \Jenssegers\Agent\Agent $agent */
        $agent = app('agent');
        if ($payment == PaymentEnum::WECHAT) {
            $order['body'] = $body;
            $order['total_fee'] = bcmul($price, 100, 2);
            /** @var \Yansongda\Pay\Gateways\Wechat $wechatPay */
            $wechatPay = app('wechatPay');
            if ($agent->is('miniprogram')) {
                $userOauth = $user->profiles()->where('from', UserFromEnum::PROGRAM)->firstOrFail();
                $order['openid'] = $userOauth->open_id;

                return $wechatPay->miniapp($order);
            }

            return $wechatPay->app($order)->getContent();
        } else {
            $order['subject'] = $body;
            $order['total_amount'] = $price;
            /** @var \Yansongda\Pay\Gateways\Alipay $aliPay */
            $aliPay = app('aliPay');

            return $aliPay->app($order)->getContent();
        }
    }
}
