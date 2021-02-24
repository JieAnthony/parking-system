<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\User;

class OrderService extends Service
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserOrders(int $userId)
    {
        return Order::query()
            ->where('user_id', $userId)
            ->where('status', OrderStatusEnum::DONE)
            ->orderByDesc('id')
            ->paginate(config('info.page.limit', 10));
    }

    /**
     * @param string $license
     * @param int $enterBarrierId
     * @param null $enteredAt
     * @return Order
     * @throws BusinessException
     */
    public function generate(string $license, int $enterBarrierId, $enteredAt = null)
    {
        $car = app(CarService::class)->getCarByLicense($license, true);
        $hasParkingOrder = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->exists();
        if ($hasParkingOrder) {
            throw new BusinessException('该车辆目前已有进行中的订单，操作有误！');
        }
        $order = new Order();
        $order->no = 'P' . app('snowflake')->id();
        $order->car_id = $car->id;
        $order->enter_barrier_id = $enterBarrierId;
        $order->entered_at = $enteredAt ?: now();
        $order->save();

        return $order;
    }

    /**
     * @param User $user
     * @param string $license
     * @return array
     * @throws BusinessException
     */
    public function getParkingOrderInfo(User $user, string $license)
    {
        $car = app(CarService::class)->getCarByLicense($license, true);
        if ($car->end_at) {
            throw new BusinessException('this car is vip,no payment');
        }
        $order = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->first();
        if (! $order) {
            throw new BusinessException('暂时没有查询到该车辆有正在进行中的订单');
        }
        $price = $this->getParkingOrderPrice($order);
        if (! $price) {
            throw new BusinessException('目前停车时间处于免费时间');
        }
        $diff = $this->getParkingOrderTimeDiff($order);

        return compact('order', 'price', 'diff');
    }

    /**
     * @param Order $order
     * @param bool $needException
     * @return int|mixed|string|null
     * @throws BusinessException
     */
    public function getParkingOrderPrice(Order $order)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            return $order->price;
        }
        $price = 0;
        $deduction = getOption('deduction');
        $diff = $this->getParkingOrderTimeDiff($order);
        $topPrice = $deduction['top_price'];
        $perHour = $deduction['per_hour'];
        $perMinute = bcdiv($perHour, 60, 2);
        if ($diff->days > 0 || $diff->h > 0 || $diff->i > $deduction['free_time']) {
            if ($diff->days !== 0) {
                $price += bcmul($diff->days, $topPrice);
            }
            if ($diff->h !== 0) {
                if ($diff->h >= bcdiv($topPrice, $perHour, 0)) {
                    $price += $topPrice;
                } else {
                    $price += bcmul($diff->h, $perHour);
                }
            }
            if ($diff->i !== 0) {
                $price += (int)round(bcmul($diff->i, $perMinute, 3));
            }
        }

        return $price;
    }

    /**
     * @param Order $order
     * @return \DateInterval
     */
    public function getParkingOrderTimeDiff(Order $order)
    {
        return $order->entered_at->diff($order->status == OrderStatusEnum::PARKING ? now() : $order->outed_at);
    }

    /**
     * @param Order $order
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Order $order)
    {
        return $order->delete();
    }
}
