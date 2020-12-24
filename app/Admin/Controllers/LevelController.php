<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Level;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class LevelController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Level(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('price');
            $grid->column('days');
            $grid->column('note');
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
        return Show::make($id, new Level(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('price');
            $show->field('days');
            $show->field('note');
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
        return Form::make(new Level(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('price');
            $form->text('days');
            $form->text('note');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
