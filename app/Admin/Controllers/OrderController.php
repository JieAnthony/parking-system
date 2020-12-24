<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Order;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Order(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('no');
            $grid->column('user_id');
            $grid->column('car_id');
            $grid->column('status');
            $grid->column('enter_barrier_id');
            $grid->column('out_barrier_id');
            $grid->column('payment');
            $grid->column('price');
            $grid->column('outed_at');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
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
            $show->field('id');
            $show->field('no');
            $show->field('user_id');
            $show->field('car_id');
            $show->field('status');
            $show->field('enter_barrier_id');
            $show->field('out_barrier_id');
            $show->field('payment');
            $show->field('price');
            $show->field('outed_at');
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
        return Form::make(new Order(), function (Form $form) {
            $form->display('id');
            $form->text('no');
            $form->text('user_id');
            $form->text('car_id');
            $form->text('status');
            $form->text('enter_barrier_id');
            $form->text('out_barrier_id');
            $form->text('payment');
            $form->text('price');
            $form->text('outed_at');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
