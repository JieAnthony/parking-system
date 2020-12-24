<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Barrier;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class BarrierController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Barrier(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('status');
            $grid->column('direction');
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
        return Show::make($id, new Barrier(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('status');
            $show->field('direction');
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
        return Form::make(new Barrier(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('status');
            $form->text('direction');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
