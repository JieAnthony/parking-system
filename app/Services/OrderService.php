<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BusinessException;
use App\Models\Order;

class OrderService extends Service
{
    /**
     * @param string $license
     * @param int $enterBarrierId
     * @param string|null $enteredAt
     * @return Order|\Illuminate\Database\Eloquent\Model
     * @throws BusinessException
     */
    public function generate(string $license, int $enterBarrierId, string $enteredAt = null)
    {
        $car = app(CarService::class)->getCarByLicense($license, true);
        if ($this->hasOrderParking($car->id)) {
            throw new BusinessException('该车辆已有订单正在进行中！创建失败');
        }
        $no = $this->getNo();

        return Order::create([
            'no' => $no,
            'car_id' => $car->id,
            'enter_barrier_id' => $enterBarrierId,
            'entered_at' => $enteredAt ?: now(),
        ]);
    }

    /**
     * @param int $carId
     * @return bool
     */
    public function hasOrderParking(int $carId)
    {
        return Order::query()
            ->where('car_id', $carId)
            ->where('status', OrderStatusEnum::PARKING)
            ->exists();
    }

    public function getOrderPrice($order)
    {
        return '15.00';
    }

    /**
     * @param int $orderId
     * @param int $payment
     * @param int $outBarrierId
     * @param int $userId
     * @return Order|Order[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function setOrderDone(int $orderId, int $payment, int $outBarrierId, int $userId = 0)
    {
        $order = Order::query()->findOrFail($orderId);
        $order->status = OrderStatusEnum::DONE;
        $order->payment = $payment;
        $order->payed_at = now();
        $order->price = $this->getOrderPrice($order);
        $order->out_barrier_id = $outBarrierId;
        $order->user_id = $userId;
        $order->save();

        return $order;
    }
}
