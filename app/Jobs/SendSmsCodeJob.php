<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $username;

    /**
     * @var int
     */
    public $code;

    /**
     * SendSmsCodeJob constructor.
     * @param int $username
     * @param int $code
     */
    public function __construct(int $username, int $code)
    {
        $this->username = $username;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var \Overtrue\EasySms\EasySms $easySms */
        $easySms = app('sms');
        $easySms->send($this->username, [
            'content' => '您的验证码为:'.$this->code,
            'template' => config('info.sms_code_template'),
            'data' => [
                'code' => $this->code,
            ],
        ]);
    }
}
