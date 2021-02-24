<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

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
            // 禁用 操作
            $grid->disableActions();
            // 禁用 添加
            $grid->disableCreateButton();
            $grid->model()->orderByDesc('id');
            $grid->column('id');
            $grid->column('avatar', '头像')->image('', 50, 50);
            $grid->column('nickname', '昵称');
            $grid->column('created_at', '注册时间');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->between('created_at', '注册时间')->date();
            });
        });
    }
}
