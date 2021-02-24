<?php

namespace App\Events;

use App\Models\Finance;
use Illuminate\Queue\SerializesModels;

class FinancePaymentSuccessEvent
{
    use SerializesModels;

    /**
     * @var Finance
     */
    public $finance;

    public function __construct(Finance $finance)
    {
        $this->finance = $finance;
    }
}
