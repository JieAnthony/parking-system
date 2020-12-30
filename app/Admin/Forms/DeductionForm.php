<?php

namespace App\Admin\Forms;

use App\Enums\OptionEnum;
use Dcat\Admin\Widgets\Form;

class DeductionForm extends Form
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
        \Option::set(OptionEnum::DEDUCTION, $input);

        return $this->response()
                ->success('')
                ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->confirm('您确定要修改扣费方式吗？修改成功会立即生效');
        $this->disableResetButton();
        $this->number('free_time', '免费时间')
            ->help('单位：分钟。最低10分钟')
            ->default(10)
            ->required()
            ->rules('integer|min:10');
        $this->number('top_price', '封顶金额')
            ->help('单位：元。必须设置')
            ->required()
            ->rules('integer|gt:0');
        $this->number('per_hour', '每小时费用')
            ->help('单位：元。必须设置')
            ->required()
            ->rules('integer|gt:0');
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return \Option::get(OptionEnum::DEDUCTION) ?? [];
    }
}
