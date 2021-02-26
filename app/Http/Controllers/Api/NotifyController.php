<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentModeEnum;
use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use App\Services\OrderService;
use App\Services\UserService;

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

    /**
     * @var UserService
     */
    public $userService;

    public function __construct(FinanceService $financeService, OrderService $orderService, UserService $userService)
    {
        $this->financeService = $financeService;
        $this->orderService = $orderService;
        $this->userService = $userService;
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
                                $finance = $this->financeService->getFinanceByNo($outTradeNo);
                                $this->financeService->handlePaymentSuccess($finance);
                                \Log::info('buy level success', ['finance' => $finance->id]);
                                break;
                            case "P":
                                $order = $this->orderService->getOrderByNo($outTradeNo);
                                $user = $this->userService->getUserByOpenId($message['openid']);
                                $this->orderService->handlePaymentSuccess($order, PaymentModeEnum::WECHAT, $user);
                                \Log::info('payment parking order success', ['order' => $order->id]);

                                break;
                            default:
                                throw new \Exception('type error');
                        }

                    } catch (\Exception $exception) {
                        return $fail($exception->getMessage());
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return true;
        });
    }
}
