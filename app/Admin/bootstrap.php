<?php

use Dcat\Admin\Form;
use Dcat\Admin\Grid;

Grid::resolving(function (Grid $grid) {
    // 开启弹窗创建
    $grid->enableDialogCreate();
    // 禁用编辑按钮
    $grid->disableEditButton();
    // 显示快捷编辑按钮
    $grid->showQuickEditButton();
    // 禁用 行选择器
    $grid->disableRowSelector();
    // 禁用批量删除按钮
    $grid->disableBatchDelete();
    // 设置工具栏按钮样式
    $grid->toolsWithOutline(false);
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
