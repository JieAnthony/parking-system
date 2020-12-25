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
     * @return string
     */
    public function title()
    {
        return '级别';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Level(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('name', '级别名称');
            $grid->column('price', '价格');
            $grid->column('days', '使用天数');
            $grid->column('note', '备注');
            $grid->column('created_at', '创建时间');
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
            $show->field('name', '级别名称');
            $show->field('price', '价格');
            $show->field('days', '使用天数');
            $show->field('note', '备注');
            $show->field('created_at', '创建时间');
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
            $id = $form->getKey();
            $form->text('name', '级别名称')
                ->required()
                ->creationRules(['required', 'unique:levels'])
                ->updateRules(['required', "unique:levels,name,$id"]);
            $form->currency('price', '价格')->default('')->symbol('￥')->required()->rules('gte:0');
            $form->number('days', '使用天数')->required()->rules('gt:0');
            $form->textarea('note', '备注');
        });
    }
}
