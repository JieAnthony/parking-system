<?php

namespace App\Listeners;

use App\Events\CarEnterEvent;
use App\Services\CarService;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Simps\MQTT\Client;

class GenerateOrderListener implements ShouldQueue
{
    /**
     * @var OrderService
     */
    public $orderService;

    /**
     * @var CarService
     */
    public $carService;

    public function __construct(OrderService $orderService, CarService $carService)
    {
        $this->orderService = $orderService;
        $this->carService = $carService;
    }


    public function handle(CarEnterEvent $event)
    {
        $config = config('mqtt');
        $configObj = new \Simps\MQTT\Config\ClientConfig($config['config']);
        $client = new Client($config['host'], $config['port'], $configObj, Client::SYNC_CLIENT_TYPE);
        $client->connect();
        try {
            $car = $this->carService->getCarByLicense($event->license,true);
            $this->orderService->generate($car, $event->barrierId);
            $data = [
                'r' => true,
                'l' => $event->license
            ];
            $client->publish('barrier/' . $event->barrierId, json_encode($data), 1);
        } catch (\Exception $exception) {
            $data = [
                'r' => false,
                'l' => $event->license
            ];
            $client->publish('barrier/' . $event->barrierId, json_encode($data), 1);
        }
    }
}
