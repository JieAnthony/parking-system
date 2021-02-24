<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    /**
     * @var FinanceService
     */
    public $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     */
    public function notify(Request $request)
    {
        $content = $request->getContent();
        $wechatPay = app('wechatPay');
        try {
            $data = $wechatPay->verify($content);
            $finance = $this->financeService->handlePaymentSuccess($data->out_trade_no);
            \Log::info('wechat pay success!!!', ['finance' => $finance->id]);
        } catch (\Exception $exception) {
            return response('fail');
        }

        return $wechatPay->success();
    }
}
