<?php

use Dcat\Admin\Form;
use Dcat\Admin\Grid;

Grid::resolving(function (Grid $grid) {
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
