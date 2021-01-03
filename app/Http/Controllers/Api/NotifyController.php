<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use App\Services\OrderService;
use App\Services\PayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Yansongda\Pay\Gateways\Wechat\Support;

class NotifyController extends Controller
{
    /**
     * @var PayService
     */
    public $payService;

    public function __construct(PayService $payService)
    {
        $this->payService = $payService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function ali(Request $request)
    {
        try {
            /** @var \Yansongda\Pay\Gateways\Alipay $aliPay */
            $aliPay = app('aliPay');
            $data = $aliPay->verify($request->all());
            if (! in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                throw new \Exception('状态没有TRADE_SUCCESS 或者 TRADE_FINISHED');
            }
            $no = $data->out_trade_no;
            $payedAt = $data->gmt_payment;
            $this->handle($no, $payedAt);
        } catch (\Exception $exception) {
            Log::error('支付宝支付失败【'.$exception->getMessage().'】');

            return new Response('fail');
        }

        return $aliPay->success();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     */
    public function wechat(Request $request)
    {
        try {
            /** @var \Yansongda\Pay\Gateways\Wechat $wechatPay */
            $wechatPay = app('wechatPay');
            $data = $wechatPay->verify($request->getContent());
            $no = $data->out_trade_no;
            $payedAt = $data->time_end;
            $this->handle($no, $payedAt);
        } catch (\Exception $exception) {
            Log::error('微信支付失败【'.$exception->getMessage().'】');

            return new Response(
                Support::toXml(['return_code' => 'FAIL', 'return_msg' => 'FAIL']),
                200,
                ['Content-Type' => 'application/xml']
            );
        }

        return $wechatPay->success();
    }

    /**
     * @param string $no
     * @param string $payedAt
     * @throws BusinessException
     */
    private function handle(string $no, string $payedAt)
    {
        $first = Str::substr($no, 0, 1);
        switch ($first) {
            case 'P':
                app(OrderService::class)->handlePaySuccess($no, $payedAt);
                break;
            case 'F':
                app(FinanceService::class)->handlePaySuccess($no, $payedAt);
                break;
            default:
                throw new BusinessException('订单有误！');
        }
    }
}
