<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Qa;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class QaController extends AdminController
{
    /**
     * @return string
     */
    public function title()
    {
        return '常见问题';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Qa(), function (Grid $grid) {
            $grid->disableEditButton(false);
            $grid->showQuickEditButton(false);
            $grid->model()->select(['id', 'title'])->orderByDesc('id');
            $grid->column('id')->sortable();
            $grid->column('title', '标题');
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
        return Show::make($id, new Qa(), function (Show $show) {
            $show->field('id');
            $show->field('title', '标题');
            $show->html($show->model()->content);
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Qa(), function (Form $form) {
            $form->display('id');
            $form->text('title', '标题');
            $form->editor('content', '内容');
        });
    }
}
