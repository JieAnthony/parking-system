<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\OrderPayGrid;
use App\Admin\Repositories\Order;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentEnum;
use App\Services\OrderService;
use Carbon\CarbonInterface;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Dcat\Admin\Traits\HasFormResponse;
use Dcat\Admin\Widgets\Table;

class OrderController extends AdminController
{
    use HasFormResponse;

    /**
     * @return string
     */
    public function title()
    {
        return '订单';
    }

    /**
     * @return \Closure[]
     */
    private function getWithArray()
    {
        return [
            'user' => function ($query) {
                $query->select(['id', 'username', 'nickname']);
            },
            'car' => function ($query) {
                $query->select(['id', 'license']);
            },
            'enterBarrier' => function ($query) {
                $query->select(['id', 'name', 'direction']);
            },
            'outBarrier' => function ($query) {
                $query->select(['id', 'name', 'direction']);
            },
        ];
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Order(), function (Grid $grid) {
            $grid->disableDeleteButton();
            $grid->disableQuickEditButton();
            $grid->actions([new OrderPayGrid()]);
            $grid->model()
                ->with($this->getWithArray())
                ->orderByDesc('id');
            $grid->column('id')->sortable();
            $grid->column('no', '订单号')->copyable();
            $grid->column('car.license', '车牌');
            $grid->column('user.username', '会员');
            $grid->column('status', '状态')->using(OrderStatusEnum::asSelectArray())->dot([false => 'warning', true => 'success']);
            $grid->column('show', '更多')->expand(function (Grid\Displayers\Expand $expand) {
                // 设置按钮名称
                $expand->button('详情');
                $headers = [
                    '停车用时',
                    '金额',
                    '支付方式',
                    '缴费时间',
                    '进场时间',
                    '进场道闸',
                    '离场道闸',
                    '离场时间',
                ];
                /** @var \Illuminate\Support\Carbon $diffAt */
                $diffAt = ($this->status == OrderStatusEnum::DONE)
                    ? $this->outed_at
                    : now();
                $timeString = $this->entered_at->diffForHumans($diffAt, [
                    'join' => ',',
                    'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                    'options' => CarbonInterface::NO_ZERO_DIFF,
                    'parts' => 3,
                ]);
                if ($this->status == OrderStatusEnum::DONE) {
                    $payment = ($this->payed_at) ? PaymentEnum::getDescription($this->payment) : '免费时间';
                } else {
                    $payment = '暂无';
                }
                $data = [
                    [
                        $timeString,
                        ($this->status == OrderStatusEnum::DONE)
                            ? $this->price
                            : '订单进行中',
                        $payment,
                        $this->payed_at ?: '暂无',
                        $this->entered_at,
                        $this->enterBarrier ? $this->enterBarrier->name : '暂无',
                        $this->outBarrier ? $this->outBarrier->name : '暂无',
                        $this->outed_at ?: '暂无',
                    ],
                ];
                $table = new Table($headers, $data);

                return "<div style='padding:10px 10px 0'>$table</div>";
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('status', '订单状态')->select(OrderStatusEnum::asSelectArray());
                $filter->like('no', '订单号');
                $filter->where('username', function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('username', 'like', "%{$this->input}");
                    });
                }, '会员手机号');
                $filter->where('license', function ($query) {
                    $query->whereHas('car', function ($query) {
                        $query->where('license', 'like', "%{$this->input}");
                    });
                }, '车牌号');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Order(), function (Show $show) {
            $show->model()->load($this->getWithArray());
            $show->field('id');
            $show->field('no', '订单号');
            $show->field('user.username', '会员');
            $show->field('car.license', '车牌');
            $show->field('status', '订单状态')->using(OrderStatusEnum::asSelectArray())->dot([false => 'warning', true => 'success']);
            $show->field('停车用时')->as(function () {
                /** @var \Illuminate\Support\Carbon $diffAt */
                $diffAt = ($this->status == OrderStatusEnum::DONE)
                    ? $this->outed_at
                    : now();

                return $this->entered_at->diffForHumans($diffAt, [
                    'join' => ',',
                    'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                    'options' => CarbonInterface::NO_ZERO_DIFF,
                    'parts' => 3,
                ]);
            });
            $show->field('金额')->as(function () {
                return ($this->status == OrderStatusEnum::DONE)
                    ? $this->price
                    : '订单进行中';
            });
            $show->field('支付方式')->as(function () {
                if ($this->status == OrderStatusEnum::DONE) {
                    return ($this->payed_at) ? PaymentEnum::getDescription($this->payment) : '免费时间';
                } else {
                    return '暂无';
                }
            });
            $show->field('缴费时间')->as(function () {
                return $this->payed_at ?: '暂无';
            });
            $show->field('进场时间')->as(function () {
                return $this->entered_at;
            });
            $show->field('进场道闸')->as(function () {
                return $this->enterBarrier ? $this->enterBarrier->name : '暂无';
            });
            $show->field('离场道闸')->as(function () {
                return $this->outBarrier ? $this->outBarrier->name : '暂无';
            });
            $show->field('离场时间')->as(function () {
                return $this->outed_at ?: '暂无';
            });
        });
    }

    /**
     * 重写新增.
     * @return mixed|void
     */
    public function store()
    {
        $license = request()->get('license');
        $enterBarrierId = request()->get('enter_barrier_id');
        $enteredAt = request()->get('entered_at') ?: null;
        $orderService = app(OrderService::class);
        try {
            $order = $orderService->generate($license, $enterBarrierId, $enteredAt);

            return $this->sendResponse(
                $this->response()
                    ->success('创建订单成功，进场时间为：'.$order->entered_at)
            );
        } catch (\Exception $exception) {
            return $this->sendResponse(
                $this->response()
                    ->error($exception->getMessage())
            );
        }
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Order(), function (Form $form) {
            if ($form->isCreating()) {
                $form->confirm('您确定要新增一笔停车订单吗？创建成功后开始计费！');
                $form->text('license', '车牌')
                    ->minLength(7)
                    ->maxLength(9)
                    ->rules('string');
                $form->datetime('entered_at', '进场时间')
                    ->help('不填则为当前时间');
                $form->select('enter_barrier_id', '进场道闸')
                    ->options(function () {
                        return app(\App\Services\BarrierService::class)->getAdminSelect();
                    });
            }
        });
    }
}
