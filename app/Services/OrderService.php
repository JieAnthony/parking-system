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
        if ($order->car->level_id > 0) {
            return $price;
        }
        $deduction = \Option::get('deduction');
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
}
