<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use App\Services\OrderService;

class NotifyController extends Controller
{
    /**
     * @var FinanceService
     */
    public $financeService;

    /**
     * @var OrderService
     */
    public $orderService;

    public function __construct(FinanceService $financeService, OrderService $orderService)
    {
        $this->financeService = $financeService;
        $this->orderService = $orderService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify()
    {
        /** @var \EasyWeChat\Payment\Application $wechatPayment */
        $wechatPayment = app('wechat.payment');

        return $wechatPayment->handlePaidNotify(function ($message, $fail) {
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (data_get($message, 'result_code') === 'SUCCESS') {
                    try {
                        $outTradeNo = $message['out_trade_no'];
                        $firstText = \Str::substr($outTradeNo, 0, 1);
                        switch ($firstText) {
                            case "F":
                                $finance = $this->financeService->handlePaymentSuccess($outTradeNo);
                                \Log::info('buy level success', ['finance' => $finance->id]);
                                break;
                            case "P":
                                $order = $this->orderService->handlePaymentSuccess($outTradeNo);
                                \Log::info('payment parking order success', ['order' => $order->id]);
                                break;
                            default:
                                throw new \Exception('type error');
                        }

                    } catch (\Exception $exception) {

                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return true;
        });
    }
}
