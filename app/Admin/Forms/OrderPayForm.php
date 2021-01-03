<?php

namespace App\Admin\Forms;

use App\Enums\PaymentModeEnum;
use App\Services\OrderService;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class OrderPayForm extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle()
    {
        $orderId = $this->payload['id'];
        $order = $this->getOrder($orderId);
        try {

            app(OrderService::class)->handleOrder($order, PaymentModeEnum::CASH);

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
            ->help('停车费用。如果金额为0，则代表当前车辆是月卡或者当前是免费时间，可以直接离场，无需提交表单！');
        $this->radio('payment_mode', '支付方式')
            ->help('人工缴费仅支持现金支付（包含收款码）')
            ->default(PaymentModeEnum::CASH)
            ->options([PaymentModeEnum::CASH => PaymentModeEnum::getDescription(PaymentModeEnum::CASH)])
            ->disable();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'price' => $this->getOrderPrice($this->payload['id']),
            'payment_mode' => PaymentModeEnum::CASH,
        ];
    }

    /**
     * @param int $id
     * @return int|mixed|string|null
     */
    protected function getOrderPrice(int $id)
    {
        $order = $this->getOrder($id);
        if ($order->car->level_id > 0) {
            return 0;
        }

        return app(OrderService::class)->getOrderPrice($order);
    }

    /**
     * @param int $id
     * @return \App\Models\Order|\App\Models\Order[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    protected function getOrder(int $id)
    {
        return app(OrderService::class)->getOrderById($id);
    }
}
