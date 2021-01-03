<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Events\Order\OrderCarLeaveEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderPaySuccessEvent;
use App\Exceptions\BusinessException;
use App\Models\Order;
use Carbon\Carbon;

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
        event(new OrderCreatedEvent($order));

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
     * @param bool $needException
     * @return int|mixed|string|null
     * @throws BusinessException
     */
    public function getOrderPrice(Order $order, bool $needException = false)
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
                $price += (int) round(bcmul($diff->i, $perMinute, 3));
            }
        }
        if ($needException && empty($price)) {
            throw new BusinessException('当前车辆处于免费时间，无需缴费');
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
            throw new BusinessException('暂无该车辆信息，请重新输入');
        }
        $order = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->first();
        if (! $order) {
            throw new BusinessException('暂时没有查询到该车辆有正在进行中的订单');
        }
        $price = $this->getOrderPrice($order, true);
        $diff = $this->getOrderTimeDiff($order);

        return compact('order', 'price', 'diff');
    }

    /**
     * @param Order $order
     * @param int $paymentMode
     * @return false|string|\Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     * @throws BusinessException
     */
    public function userPayOrder(Order $order, int $paymentMode)
    {
        $this->beforeCheckOrder($order);
        $price = $this->getOrderPrice($order, true);
        $payData = app(PayService::class)->sendPay(
            $order->no,
            '停车缴费',
            $price,
            $paymentMode
        );

        return $payData;
    }

    /**
     * @param Order $order
     * @param int $paymentMode
     * @param float|null $price
     * @param null $payedAt
     * @return Order
     * @throws BusinessException
     */
    public function handleOrder(Order $order, int $paymentMode, float $price = null, $payedAt = null)
    {
        $this->beforeCheckOrder($order);
        if (! $price) {
            $price = $this->getOrderPrice($order, true);
        }
        $order->status = OrderStatusEnum::DONE;
        $order->payment_mode = $paymentMode;
        $order->price = $price;
        $order->payed_at = $payedAt ?: now();
        $order->save();

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
        event(new OrderCarLeaveEvent($order));

        return $order;
    }

    /**
     * @param string $no
     * @param string $payedAt
     * @return Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function handlePaySuccess(string $no, string $payedAt)
    {
        $order = Order::query()->where('no', $no)->firstOrFail();
        if ($order->status) {
            return $order;
        }
        $order->payed_at = Carbon::parse($payedAt);
        $order->status = true;
        $order->save();
        event(new OrderPaySuccessEvent($order));

        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     * @throws BusinessException
     */
    protected function beforeCheckOrder(Order $order)
    {
        if ($order->status !== OrderStatusEnum::PARKING) {
            throw new BusinessException('订单状态有误，该订单已完成。本次操作无效');
        }
        if ($order->car->level_id !== 0) {
            throw new BusinessException('当前车辆为月卡车，无需缴费即可离场。本次操作无效！');
        }

        return $order;
    }
}
