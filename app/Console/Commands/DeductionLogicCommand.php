<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeductionLogicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $price = 0;
//        $deduction = getOption('deduction');
//        $diff = $this->getOrderTimeDiff($order);
//        $topPrice = $deduction['top_price'];
//        $perHour = $deduction['per_hour'];
//        $perMinute = bcdiv($perHour, 60, 2);
//        if ($diff->days > 0 || $diff->h > 0 || $diff->i > $deduction['free_time']) {
//            if ($diff->days !== 0) {
//                $price += bcmul($diff->days, $topPrice);
//            }
//            if ($diff->h !== 0) {
//                if ($diff->h >= bcdiv($topPrice, $perHour, 0)) {
//                    $price += $topPrice;
//                } else {
//                    $price += bcmul($diff->h, $perHour);
//                }
//            }
//            if ($diff->i !== 0) {
//                $price += (int) round(bcmul($diff->i, $perMinute, 3));
//            }
//        }
    }
}
