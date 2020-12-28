<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
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
     */
    public function getOrderDetail(Order $order)
    {
        return $order->load([
            'car',
        ]);
    }

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
     * @return array
     * @throws BusinessException
     */
    public function findCarOrder(string $license)
    {
        $car = app(CarService::class)->getCarByLicense($license);
        if (! $car) {
            throw new BusinessException('查询错误！车辆或者订单不存在');
        }
        $order = $this->hasOrderParking($car->id, true);
        if (! $order) {
            throw new BusinessException('暂无进行中的订单！');
        }
        $price = $this->getOrderPrice($order);

        return compact('order', 'price');
    }

    /**
     * @param int $carId
     * @param bool $isModel
     * @return Order|bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function hasOrderParking(int $carId, bool $isModel = false)
    {
        $query = Order::query()
            ->where('car_id', $carId)
            ->where('status', OrderStatusEnum::PARKING);
        if ($isModel) {
            return $query->first();
        } else {
            return $query->exists();
        }
    }

    public function getOrderPrice($order)
    {
        if (is_int($order)) {
            $order = $this->getOrderById($order);
        }
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
