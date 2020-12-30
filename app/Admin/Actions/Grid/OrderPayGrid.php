<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\OrderPayForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class OrderPayGrid extends RowAction
{
    /**
     * @return string
     */
    protected $title = '人工缴费';

    /**
     * @return Modal|string
     */
    public function render()
    {
        $form = OrderPayForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title($this->title)
            ->body($form)
            ->button($this->title);
    }
}
