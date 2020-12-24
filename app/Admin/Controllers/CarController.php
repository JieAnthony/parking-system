<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Car;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class CarController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Car(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('first_word');
            $grid->column('license');
            $grid->column('is_big');
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
        return Show::make($id, new Car(), function (Show $show) {
            $show->field('id');
            $show->field('first_word');
            $show->field('license');
            $show->field('is_big');
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
        return Form::make(new Car(), function (Form $form) {
            $form->display('id');
            $form->text('first_word');
            $form->text('license');
            $form->text('is_big');
            $form->text('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
