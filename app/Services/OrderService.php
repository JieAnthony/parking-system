<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

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
    public function getOrderDetail(Order $order)
    {
        if ($order->status !== OrderStatusEnum::DONE) {
            throw new BusinessException('订单有误');
        }

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
            'no' => 'P' . $no,
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
            throw new BusinessException('查询错误【车辆信息不存在】');
        }
        $order = $this->hasOrderParking($car->id, true);
        if (! $order) {
            throw new BusinessException('查询错误【该车辆暂无正在进行中的订单】');
        }
        //$price = $this->getParkingOrderPrice($order);
        //$time = $this->getParkingOrderTimeSpent($order);

        return compact('order', 'price', 'time');
    }

    /**
     * @param Order $order
     * @param int $payment
     * @param User|null $user
     * @return false|string|\Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     * @throws BusinessException
     */
    public function pay(Order $order, int $payment, User $user = null)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            throw new BusinessException('订单支付失败，该订单已完成！');
        }
        $body = '支付停车费';
        //$price = $this->getParkingOrderPrice($order);

        return app(PayService::class)->sendPay($order->no, $body, $price, $payment, $user);
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

    /**
     * @param $order
     * @return string
     */
    public function getOrderPrice($order)
    {
        if (! $order instanceof Order) {
            $order = $this->getOrderById($order);
        }
        if ($order->status == OrderStatusEnum::DONE) {
            return $order->price;
        }else{

            $now = now();
            $diff = $order->entered_at->diff($now);
        }

        $a = now();
        $b = Carbon::parse('2020-12-29 17:40:28');
        $c = $b->diff($a);
        $top = 30;
        $e = 5;
        $price = 0;
        $m = bcdiv($e, 60, 2);
        if ($c->days > 0 || $c->h > 0 || $c->i > 15) {
            if ($c->days !== 0) {
                $price += bcmul($c->days, $top);
            }
            if ($c->h !== 0) {
                if ($c->h >= bcdiv($top, $e, 0)) {
                    $price += $top;
                } else {
                    $price += bcmul($c->h, $top);
                }
            }
            if ($c->i !== 0) {
                $price += (int) round(bcmul($c->i, $m, 3));
            }
        }
    }


    public function getOrderDiffTime($order)
    {
        if (! $order instanceof Order) {
            $order = $this->getOrderById($order);
        }

        return $order->entered_at->diff($order->status == OrderStatusEnum::PARKING ? now() : $order->outed_at);
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
