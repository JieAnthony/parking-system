<?php

namespace App\Admin\Forms;

use App\Enums\PaymentModeEnum;
use App\Models\Order;
use App\Services\OrderService;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class OrderPayForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle()
    {
        try {
            $order = $this->getOrder();
            $this->orderService()->handlePaymentSuccess($order, PaymentModeEnum::CASH);

            return $this->response()
                ->success('缴费成功，通知客户15分钟内离场。页面即将刷新..')
                ->refresh();
        } catch (\Exception $exception) {
            return $this
                ->response()
                ->timeout(10)
                ->error('缴费失败【' . $exception->getMessage() . '】')
                ->refresh();
        }
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->confirm('您确认对方已付款并且设置订单为已完成吗？');
        $this->disableResetButton();
        $this->text('price', '金额')
            ->disable()
            ->help('停车费用。如果金额为0，则代表当前车辆是月卡或者当前是免费时间，可以直接离场，无需提交表单！人工缴费均为现金支付');
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        $order = $this->getOrder();
        $price = app(OrderService::class)->getParkingOrderPrice($order);

        return compact('price');
    }

    /**
     * @return Order|mixed
     */
    private function getOrder()
    {
        $orderId = $this->payload['id'];

        return Order::findOrFail($orderId);
    }

    /**
     * @return OrderService|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function orderService()
    {
        return app(OrderService::class);
    }

}
