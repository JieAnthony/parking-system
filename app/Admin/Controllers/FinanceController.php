<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Finance;
use App\Enums\FinanceStatusEnum;
use App\Enums\PaymentModeEnum;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class FinanceController extends AdminController
{
    /**
     * @return string
     */
    public function title()
    {
        return '财务明细';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Finance(), function (Grid $grid) {
            // 禁用 操作
            $grid->disableActions();
            // 禁用 添加
            $grid->disableCreateButton();
            $grid->model()->with([
                'user' => function ($query) {
                    $query->select(['id', 'nickname']);
                },
                'level' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);
            $grid->column('id')->sortable();
            $grid->column('no', '订单号');
            $grid->column('user.nickname', '会员');
            $grid->column('level.name', '购买级别');
            $grid->column('payment_mode', '支付方式')->display(function ($paymentMode) {
                return PaymentModeEnum::getDescription($paymentMode);
            });
            $grid->column('price', '金额');
            $grid->column('status', '状态')
                ->using(FinanceStatusEnum::asSelectArray())
                ->dot([
                    FinanceStatusEnum::FAIL => 'danger',
                    FinanceStatusEnum::OK => 'success',
                ]);
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('no', '订单号');
                $filter->equal('status', '订单状态')->select(FinanceStatusEnum::asSelectArray());
                $filter->where('nickname', function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('nickname', 'like', "%{$this->input}");
                    });
                }, '会员昵称');
            });
        });
    }
}
