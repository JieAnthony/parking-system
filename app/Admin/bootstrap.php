<?php

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

Grid::resolving(function (Grid $grid) {
    // 开启弹窗创建
    $grid->enableDialogCreate();
    // 禁用 编辑按钮
    $grid->disableEditButton();
    // 显示 快捷编辑按钮
    $grid->showQuickEditButton();
    // 禁用 行选择器
    $grid->disableRowSelector();
    // 禁用批量删除按钮
    $grid->disableBatchDelete();
    // 设置工具栏按钮样式
    $grid->toolsWithOutline(false);
});

Show::resolving(function (Show $show) {
    $show->panel()
        ->tools(function ($tools) {
            // 禁用 编辑
            $tools->disableEdit();
            // 禁用 删除
            $tools->disableDelete();
        });
});

Form::resolving(function (Form $form) {
    $form->tools(function ($tools) {
        // 去掉删除按钮
        $tools->disableDelete();
    });

    $form->footer(function ($footer) {
        // 去掉`查看`checkbox
        $footer->disableViewCheck();

        // 去掉`继续编辑`checkbox
        $footer->disableEditingCheck();
    });
});
