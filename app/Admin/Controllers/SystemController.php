<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\DeductionForm;
use App\Admin\Forms\InfoForm;
use App\Http\Controllers\Controller;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;

class SystemController extends Controller
{
    /**
     * @param Content $content
     * @return Content
     */
    public function info(Content $content)
    {
        return $content
            ->title('基础信息')
            ->body(new Card(new InfoForm()));
    }

    public function deduction(Content $content)
    {
        return $content
            ->title('扣费方式')
            ->body(new Card(new DeductionForm()));
    }
}
