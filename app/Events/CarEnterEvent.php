<?php

namespace App\Events;

class CarEnterEvent
{
    /**
     * @var string
     */
    public $license;

    /**
     * @var int
     */
    public $barrierId;

    public function __construct(string $license, int $barrierId)
    {
        $this->license = $license;
        $this->barrierId = $barrierId;
    }

}
