<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class UserController extends AdminController
{
    public function title()
    {
        return '会员';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('avatar', '#')->image('', 50, 50);
            $grid->column('nickname', '昵称');
            $grid->column('username', '手机号');
            $grid->column('level_id');
            $grid->column('created_at', '注册时间');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('username', '手机号');
                $filter->between('created_at', '注册时间')->date();
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
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('username');
            $show->field('password');
            $show->field('level_id');
            $show->field('surplus_days');
            $show->field('nickname');
            $show->field('avatar');
            $show->field('gender');
            $show->field('end_at');
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
        return Form::make(new User(), function (Form $form) {
            $form->text('nickname', '昵称');
            $form->text('username', '手机号')->required();
            $form->image('avatar', '头像')->saveFullUrl();
            $form->select('gender', '性别')->options([
                1 => '♂',
                2 => '♀',
            ])->required();
        });
    }
}
