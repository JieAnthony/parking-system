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
     * @return string
     */
    public function title()
    {
        return '车辆';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Car(), function (Grid $grid) {
            $grid->disableCreateButton();
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('license', '车牌');
            $grid->column('end_at', '截止日期');
            $grid->column('status', '状态')->using([false => '禁止', true => '正常'])->dot([false => 'danger', true => 'success']);
            $grid->column('created_at', '创建时间');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('license', '车牌');
                $filter->between('created_at', '创建时间')->date();
                $filter->between('end_at', '截止日期')->date();
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
            $show->field('license', '车牌');
            $show->field('end_at', '截止日期');
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
            $id = $form->getKey();
            if ($id) {
                $form->text('license', '车牌')->disable();
            } else {
                $form->text('license', '车牌')
                    ->required()
                    ->minLength(7)
                    ->maxLength(9)
                    ->rules("string|license|unique:cars,license,$id");
            }

            $form->date('end_at', '截止日期')->rules('date|after:'.now()->toDateString());
            $form->switch('status', '状态')->required()->default(true);
        });
    }
}
