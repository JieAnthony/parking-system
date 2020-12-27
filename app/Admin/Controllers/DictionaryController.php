<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Dictionary;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Callout;

class DictionaryController extends AdminController
{
    /**
     * @return string
     */
    public function title()
    {
        return '车牌字典';
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header($this->title())
            ->description($this->description()['index'] ?? trans('admin.list'))
            ->body(function (Row $row) {
                $row->column(12, Callout::make('该功能用于会员搜索车牌首字的所有数据', '提示')->primary()->removable());
            })
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Dictionary(), function (Grid $grid) {
            $grid->disableViewButton();
            $grid->model()->orderBy('order')->orderByDesc('id');
            $grid->column('id');
            $grid->column('name', '首字');
            $grid->column('order', '排序');
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
            $id = $form->getKey();
            $form->text('name', '首字')
                ->required()
                ->rules("unique:dictionaries,name,$id");
            $form->number('order', '排序');
        });
    }
}
