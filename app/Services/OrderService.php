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
        event(new OrderCreateEvent($order));

        return $order;
    }

    /**
     * @param string $license
     * @return array
     * @throws BusinessException
     */
    public function getParkingOrderInfo(string $license)
    {
        $car = $this->beforeCarCheck(null, $license);
        $order = Order::query()
            ->where('car_id', $car->id)
            ->where('status', OrderStatusEnum::PARKING)
            ->first();
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
        $this->beforeCarCheck($order->car_id);
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
     * @param string $no
     * @return Order
     * @throws BusinessException
     */
    public function handlePaymentSuccess(string $no)
    {
        $order = $this->getOrderByNo($no);
        if (! $order) {
            throw new BusinessException('order data not found');
        }
        if ($order->status == OrderStatusEnum::DONE) {
            return $order;
        }
        $order->status = OrderStatusEnum::DONE;
        $order->payed_at = now();
        $order->save();
        event(new OrderPaymentSuccessEvent($order));

        return $order;
    }

    public function orderCarLeave(string $license)
    {

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
        $diff = $this->getParkingOrderTimeDiff($order);
        $topPrice = $deduction['top_price'];
        $perHour = $deduction['per_hour'];
        $perMinute = bcdiv($perHour, 60, 2);
        if ($diff->days > 0 || $diff->h > 0 || $diff->i > $deduction['free_time']) {
            if ($diff->days !== 0) {
                $price += (int)bcmul($diff->days, $topPrice);
            }
            if ($diff->h !== 0) {
                if ($diff->h >= bcdiv($topPrice, $perHour, 0)) {
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
        return Order::query()->where('no', $no)->first();
    }

    /**
     * @param int|null $id
     * @param string|null $license
     * @return Car|Car[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|object
     * @throws BusinessException
     */
    private function beforeCarCheck(int $id = null, string $license = null)
    {
        if (! $id && ! $license) {
            throw new BusinessException('params error');
        }
        $carService = app(CarService::class);
        if ($id && ! $license) {
            $car = $carService->getCarById($id);
        } elseif (! $id && $license) {
            $car = $carService->getCarByLicense($license);
        } else {
            $car = null;
        }
        if (! $car) {
            throw new BusinessException('car data not found');
        }
        if ($car->end_at) {
            throw new BusinessException('this car is vip,no payment');
        }

        return $car;

    }
}
