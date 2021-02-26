<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Events\OrderCreateEvent;
use App\Events\OrderPaymentSuccessEvent;
use App\Exceptions\BusinessException;
use App\Models\Car;
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
     * @param Car $car
     * @param int $enterBarrierId
     * @param null $enteredAt
     * @return Order
     * @throws BusinessException
     */
    public function generate(Car $car, int $enterBarrierId, $enteredAt = null)
    {
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
        event(new OrderCreateEvent($order));

        return $order;
    }

    /**
     * @param string $license
     * @return array
     * @throws BusinessException
     */
    public function getParkingOrderInfo(Car $car)
    {
        $car = $this->beforeCarCheck($car);
        $order = $this->getCarParkingOrder($car);
        if (! $order) {
            throw new BusinessException('no working order');
        }
        $price = $this->getParkingOrderPrice($order);
        if (! $price) {
            throw new BusinessException('in free time');
        }
        $diff = $this->getParkingOrderTimeDiff($order);

        return compact('order', 'price', 'diff');
    }

    /**
     * @param Order $order
     * @param User $user
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws BusinessException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentParkingOrder(Order $order, User $user)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            throw new BusinessException('order done!');
        }
        $this->beforeCarCheck($order->car);
        $price = $this->getParkingOrderPrice($order);
        if (! $price) {
            throw new BusinessException('in free time');
        }
        $order = [
            'out_trade_no' => $order->no,
            'body' => 'payment parking order',
            'total_fee' => bcmul($price, 100),
            'openid' => $user->open_id,
            'notify_url' => route('api.payment.notify'),
            'trade_type' => 'JSAPI',
        ];
        /** @var \EasyWeChat\Payment\Application $wechatPayment */
        $wechatPayment = app('wechat.payment');

        return $wechatPayment->order->unify($order);
    }

    /**
     * @param Order $order
     * @param int $paymentMode
     * @param User|null $user
     * @return Order
     * @throws BusinessException
     */
    public function handlePaymentSuccess(Order $order, int $paymentMode, User $user = null)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            return $order;
        }
        if ($user) {
            $order->user_id = $user->id;
        }
        $order->price = $this->getParkingOrderPrice($order);
        $order->payment_mode = $paymentMode;
        $order->status = OrderStatusEnum::DONE;
        $order->payed_at = now();
        $order->save();
        event(new OrderPaymentSuccessEvent($order));

        return $order;
    }

    public function orderCarLeave(Car $car)
    {

    }

    /**
     * @param Car $car
     * @return Order|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCarParkingOrder(Car $car)
    {
        return Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->select([
                'id', 'no', 'status', 'created_at', 'entered_at'
            ])
            ->first();
    }

    /**
     * @param Order $order
     * @return int|mixed|null
     */
    public function getParkingOrderPrice(Order $order)
    {
        if ($order->status == OrderStatusEnum::DONE) {
            return $order->price;
        }
        $price = 0;
        $deduction = getOption('deduction');
        if (! $deduction) {
            throw new BusinessException('deduction no set!!!');
        }
        $diff = $this->getParkingOrderTimeDiff($order);
        $topPrice = $deduction['top_price'];
        $perHour = $deduction['per_hour'];
        $perMinute = bcdiv($perHour, 60, 2);
        if ($diff->days > 0 || $diff->h > 0 || $diff->i > $deduction['free_time']) {
            if ($diff->days !== 0) {
                $price += (int)bcmul($diff->days, $topPrice);
            }
            if ($diff->h !== 0) {
                if ($diff->h >= bcdiv($topPrice, $perHour)) {
                    $price += $topPrice;
                } else {
                    $price += (int)bcmul($diff->h, $perHour);
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

    /**
     * @param string $no
     * @return Order|null
     */
    public function getOrderByNo(string $no)
    {
        return Order::query()->where('no', $no)->firstOrFail();
    }

    /**
     * @param Car $car
     * @return Car
     * @throws BusinessException
     */
    private function beforeCarCheck(Car $car)
    {
        if ($car->end_at) {
            throw new BusinessException('this car is vip,no payment');
        }

        return $car;

    }
}
