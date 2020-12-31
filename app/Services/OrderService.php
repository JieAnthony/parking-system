<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentModeEnum;
use App\Exceptions\BusinessException;
use App\Models\Order;

class OrderService extends Service
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserOrderList(int $userId)
    {
        return Order::query()
            ->with([
                'car' => function ($query) {
                    $query->select(['id', 'license']);
                },
            ])
            ->where('user_id', $userId)
            ->select([
                'id', 'no', 'user_id', 'car_id', 'status', 'entered_at', 'price',
            ])
            ->orderByDesc('id')
            ->paginate(config('info.page.limit'));
    }

    /**
     * @param Order $order
     * @return Order
     * @throws BusinessException
     */
    public function getUserOrderDetail(Order $order)
    {
        if ($order->status !== OrderStatusEnum::DONE) {
            throw new BusinessException('订单有误');
        }

        return $order->load([
            'car',
        ]);
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

    /**
     * @param string $license
     * @param int $enterBarrierId
     * @param null $enteredAt
     * @return Order
     * @throws BusinessException
     */
    public function generateOrder(string $license, int $enterBarrierId, $enteredAt = null)
    {
        $car = app(CarService::class)->getCarByLicense($license, true);
        $hasWorkingOrder = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->exists();
        if ($hasWorkingOrder) {
            throw new BusinessException('该车辆目前已有进行中的订单，操作有误！');
        }
        $order = new Order();
        $order->no = 'P'.$this->getNo();
        $order->car_id = $car->id;
        $order->enter_barrier_id = $enterBarrierId;
        $order->entered_at = $enteredAt ?: now();
        $order->save();

        return $order;
    }

    /**
     * @param int $id
     * @return Order|Order[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getOrderById(int $id)
    {
        return Order::query()->findOrFail($id);
    }

    /**
     * @param Order $order
     * @return int|mixed|string|null
     */
    public function getOrderPrice(Order $order)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            return $order->price;
        }
        $price = 0;
        $deduction = getOption('deduction');
        $diff = $this->getOrderTimeDiff($order);
        $topPrice = $deduction['top_price'];
        $perHour = $deduction['per_hour'];
        $perMinute = bcdiv($perHour, 60, 2);
        if ($diff->days > 0 || $diff->h > 0 || $diff->i > 15) {
            if ($diff->days !== 0) {
                $price += bcmul($diff->days, $topPrice);
            }
            if ($diff->h !== 0) {
                if ($diff->h >= bcdiv($diff->h, $perHour, 0)) {
                    $price += $topPrice;
                } else {
                    $price += bcmul($diff->h, $topPrice);
                }
            }
            if ($diff->i !== 0) {
                $price += (int) round(bcmul($diff->i, $perMinute, 3));
            }
        }

        return $price;
    }

    /**
     * @param Order $order
     * @return \DateInterval|false
     */
    public function getOrderTimeDiff(Order $order)
    {
        return $order->entered_at->diff($order->status == OrderStatusEnum::PARKING ? now() : $order->outed_at);
    }

    /**
     * @param string $license
     * @return array
     * @throws BusinessException
     */
    public function findOrder(string $license)
    {
        $car = app(CarService::class)->getCarByLicense($license);
        if (! $car) {
            throw new BusinessException('暂无该车辆信息');
        }
        $order = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->first();
        if (! $order) {
            throw new BusinessException('暂时没有查询到该车辆有正在进行中的订单');
        }
        $price = $this->getOrderPrice($order);
        $diff = $this->getOrderTimeDiff($order);

        return compact('order', 'price', 'diff');
    }

    public function userOrderPay()
    {
    }

    /**
     * @param Order $order
     * @return Order
     * @throws BusinessException
     */
    public function adminOrderPay(Order $order)
    {
        if ($order->status !== OrderStatusEnum::PARKING) {
            throw new BusinessException('d d z t y w');
        }
        if ($this->checkOrderCarHasLevel($order)) {
            throw new BusinessException('y k c l w x j f!');
        }
        $price = $this->getOrderPrice($order);
        if (! $price) {
            throw new BusinessException('free time');
        }

        return $this->setOrderDone($order, PaymentModeEnum::CASH, $price);
    }

    /**
     * @param Order $order
     * @param int $paymentMode
     * @param float $price
     * @param null $payedAt
     * @return Order
     */
    public function setOrderDone(Order $order, int $paymentMode, float $price, $payedAt = null)
    {
        $order->status = OrderStatusEnum::DONE;
        $order->payment_mode = $paymentMode;
        $order->price = $price;
        $order->payed_at = $payedAt ?: now();

        return $order;
    }

    /**
     * @param Order $order
     * @param int $outBarrierId
     * @param null $outedAt
     * @return Order
     */
    public function setOrderLeave(Order $order, int $outBarrierId, $outedAt = null)
    {
        $order->out_barrier_id = $outBarrierId;
        $order->outed_at = $outedAt ?: now();
        $order->save();

        return $order;
    }

    /**
     * @param Order $order
     * @return bool
     */
    protected function checkOrderCarHasLevel(Order $order)
    {
        return $order->car->level_id > 0;
    }
}
