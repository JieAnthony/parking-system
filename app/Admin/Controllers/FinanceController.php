<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Finance;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class FinanceController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Finance(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('no');
            $grid->column('user_id');
            $grid->column('level_id');
            $grid->column('payment');
            $grid->column('price');
            $grid->column('status');
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
