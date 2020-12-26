<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Dictionary;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class DictionaryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Dictionary(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('name', '首字');
            $grid->column('order', '排序');
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
        return Show::make($id, new Dictionary(), function (Show $show) {
            $show->field('id');
            $show->field('name', '首字');
            $show->field('order', '排序');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Dictionary(), function (Form $form) {
            $form->display('id');
            $form->text('name', '首字')
                ->required()
                ->rules('unique:users');
            $form->number('order', '排序');
        });
    }
}
