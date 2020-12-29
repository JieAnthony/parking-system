<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class UserController extends AdminController
{
    /**
     * @return string
     */
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
            $grid->disableDeleteButton();
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('avatar', '头像')->image('', 50, 50);
            $grid->column('nickname', '昵称');
            $grid->column('username', '手机号')->copyable();
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
            $show->field('avatar', '头像');
            $show->field('nickname', '昵称');
            $show->field('username', '手机号');
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
            $id = $form->getKey();

            $form->text('nickname', '昵称')->required();
            $form->text('username', '手机号')
                ->required()
                ->rules("unique:users,username,$id|regex:/^1[35678][0-9]{9}$/");
            if ($id) {
                $form->password('password', '密码')
                    ->help('不填则不修改密码')
                    ->rules('string')
                    ->minLength(6)
                    ->maxLength(32)
                    ->customFormat(function () {
                        return '';
                    });
            } else {
                $form->password('password', '密码')
                    ->required()
                    ->rules('string')
                    ->minLength(6)
                    ->maxLength(32);
            }
            $form->image('avatar', '头像')
                ->removable(false)
                ->uniqueName()
                ->saveFullUrl();
        })->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }
            if (! $form->password) {
                $form->deleteInput('password');
            }
        });
    }
}
