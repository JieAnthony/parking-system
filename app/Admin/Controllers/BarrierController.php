<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Barrier;
use App\Enums\BarrierDirectionEnum;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class BarrierController extends AdminController
{
    /**
     * @return string
     */
    public function title()
    {
        return '道闸';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Barrier(), function (Grid $grid) {
            $grid->disableViewButton();
            $grid->model()->orderByDesc('id');
            $grid->column('id')->sortable();
            $grid->column('name', '名称');
            $grid->column('direction', '方向')->display(function ($direction) {
                return BarrierDirectionEnum::getDescription($direction);
            });
            $grid->column('status', '状态')->bool();
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
            $id = $form->getKey();
            $form->text('name', '道闸名称')
                ->required()
                ->maxLength(255)
                ->rules("string|max:255|unique_with:barriers,direction,$id");
            $form->switch('status', '状态');
            $form->select('direction', '道闸方向')->required()->options(BarrierDirectionEnum::asSelectArray());
        });
    }
}
