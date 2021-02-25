<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class LevelService extends Service
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLevels()
    {
        return Cache::rememberForever('levels', function () {
            return Level::query()
                ->select(['id', 'name', 'price', 'days'])
                ->orderByDesc('id')
                ->get()
                ->keyBy('id');
        });
    }

    /**
     * @param int $id
     * @return Level|Level[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getLevelById(int $id)
    {
        return Level::find($id);
    }

    /**
     * @param Level $level
     * @param User $user
     * @param string $license
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws BusinessException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function carBuyLevel(Level $level, User $user, string $license)
    {
        $car = app(CarService::class)->getCarByLicense($license, true);
        if ($car->end_at) {
            throw new BusinessException('this car have a level!');
        }
        $orderNumber = 'F' . app('snowflake')->id();
        // create finance
        app(FinanceService::class)->store($orderNumber, $user->id, $car->id, $level->id, $level->price);
        $order = [
            'out_trade_no' => $orderNumber,
            'body' => 'buy level',
            'total_fee' => bcmul($level->price, 100),
            'openid' => $user->open_id,
            'notify_url' => route('api.payment.notify'),
            'trade_type' => 'JSAPI',
        ];
        /** @var \EasyWeChat\Payment\Application $wechatPayment */
        $wechatPayment = app('wechat.payment');

        return $wechatPayment->order->unify($order);
    }
}
