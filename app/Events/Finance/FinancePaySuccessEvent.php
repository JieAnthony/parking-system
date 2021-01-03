<?php

namespace App\Events\Finance;

use App\Models\Finance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FinancePaySuccessEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var Finance
     */
    public $finance;

    public function __construct(Finance $finance)
    {
        $this->finance = $finance;
    }
}
