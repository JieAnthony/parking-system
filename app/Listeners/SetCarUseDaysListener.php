<?php

namespace App\Listeners;

use App\Events\FinancePaymentSuccessEvent;
use App\Services\CarService;
use App\Services\LevelService;
use Illuminate\Support\Facades\Log;

class SetCarUseDaysListener
{
    /**
     * @var CarService
     */
    public $carService;

    /**
     * @var LevelService
     */
    public $levelService;

    public function __construct(CarService $carService, LevelService $levelService)
    {
        $this->carService = $carService;
        $this->levelService = $levelService;
    }

    public function handle(FinancePaymentSuccessEvent $event)
    {
        $car = $this->carService->getCarById($event->finance->car_id);
        $level = $this->levelService->getLevelById($event->finance->level_id);
        $user = $event->finance->user;
        if ($car && $level) {
            $car->end_at = now()->addDays($level->days + 1)->toDateString();
            $car->save();
            $this->carService->carBindUser($car, $user);
        } else {
            Log::error('car or level error', ['finance_id' => $event->finance->id]);
        }
    }
}
