<?php

namespace App\Admin\Forms;

use App\Enums\OptionEnum;
use Dcat\Admin\Widgets\Form;

class InfoForm extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        \Option::set(OptionEnum::INFO, $input);

        return $this
                ->response()
                ->success('Processed successfully.')
                ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->confirm('您确定要修改基础信息吗');
        $this->disableResetButton();
        $this->text('name', '系统名称')
            ->required()
            ->maxLength(255)
            ->rules('string|max:255');
        $this->image('logo', 'LOGO')
            ->url('upload')
            ->removable(false)
            ->uniqueName()
            ->saveFullUrl();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return \Option::get(OptionEnum::INFO);
    }
}
