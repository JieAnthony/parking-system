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
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('license', '车牌')->display(function () {
                return $this->first_word.' '.$this->license;
            });
            $grid->column('status', '状态')->using([false => '禁止', true => '正常'])->dot([false => 'danger', true => 'success']);
            $grid->column('created_at', '创建时间');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('first_word', '车牌首字')->select(function () {
                    return app(\App\Services\DictionaryService::class)->getAdminSelect();
                });
                $filter->like('license', '车牌');
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
            $show->field('license', '车牌')->as(function ($license) {
                $firstWord = $this->first_word;

                return "{$firstWord}{$license}";
            });
            $show->field('is_big', '是否为大型车（黄牌）')->using([false => '否', true => '是']);
            $show->field('status', '状态')->using([false => '禁止', true => '正常'])->dot([false => 'danger', true => 'success']);
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
        return Form::make(new Car(), function (Form $form) {
            $form->select('first_word', '车牌首字')
                ->options(function () {
                    return app(\App\Services\DictionaryService::class)->getAdminSelect();
                });
            $form->text('license', '车牌');
            $form->switch('is_big', '是否为大型车（黄牌）');
            $form->switch('status', '状态');
        });
    }
}
