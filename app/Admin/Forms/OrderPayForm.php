<?php

namespace App\Admin\Forms;

use App\Enums\PaymentEnum;
use App\Services\OrderService;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class OrderPayForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        $orderId = $this->payload['id'];
        try {
            app(OrderService::class)->setOrderDone(
                $orderId,
                PaymentEnum::CASH,
                $input['out_barrier_id']
            );

            return $this->response()
                ->success('缴费成功，通知客户15分钟内离场。页面即将刷新..')
                ->refresh();
        } catch (\Exception $exception) {
            return $this
                ->response()
                ->error('缴费失败：'.$exception->getMessage())
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
        $this->select('out_barrier_id', '离场道闸')
        ->options(function () {
            return app(\App\Services\BarrierService::class)->getAdminSelect(false);
        })
        ->required();
        $this->text('price', '金额')
            ->disable()
            ->help('停车费用');
        $this->radio('payment', '支付方式')
            ->help('人工缴费仅支持现金支付（包含收款码）')
            ->default(PaymentEnum::CASH)
            ->options([PaymentEnum::CASH => PaymentEnum::getDescription(PaymentEnum::CASH)])
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
            'price'  => time(),
            'payment' => PaymentEnum::CASH,
        ];
    }
}
