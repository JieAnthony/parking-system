<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Finance;
use App\Enums\FinanceEnum;
use App\Enums\PaymentEnum;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

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
                    $query->select(['id', 'username', 'nickname']);
                },
                'level' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ]);
            $grid->column('id')->sortable();
            $grid->column('no', '订单号');
            $grid->column('user.username', '会员');
            $grid->column('level.name', '购买级别');
            $grid->column('payment', '支付方式')->display(function ($payment) {
                return PaymentEnum::getDescription($payment);
            });
            $grid->column('price', '金额');
            $grid->column('status', '状态')
                ->using(FinanceEnum::asSelectArray())
                ->dot([
                    FinanceEnum::FAIL => 'danger',
                    FinanceEnum::OK => 'success',
                ]);
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('no', '订单号');
                $filter->equal('status', '订单状态')->select(FinanceEnum::asSelectArray());
                $filter->where('username', function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('username', 'like', "%{$this->input}");
                    });
                }, '会员手机号');
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
        return Show::make($id, new Finance(), function (Show $show) {
            $show->field('id');
            $show->field('no');
            $show->field('user_id');
            $show->field('level_id');
            $show->field('payment');
            $show->field('price');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Finance(), function (Form $form) {
            $form->display('id');
            $form->text('no');
            $form->text('user_id');
            $form->text('level_id');
            $form->text('payment');
            $form->text('price');
            $form->text('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
