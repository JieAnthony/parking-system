<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Finance;
use App\Models\Level;
use App\Models\User;
use Illuminate\Support\Arr;

class LevelService extends Service
{
    /**
     * @return Level[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function list()
    {
        return Level::query()
            ->select(['id', 'name', 'price', 'days'])
            ->orderByDesc('id')
            ->get()
            ->keyBy('id');
    }

    /**
     * @param Level $level
     * @param User $user
     * @param int $payment
     * @return false|string|\Symfony\Component\HttpFoundation\Response|\Yansongda\Supports\Collection
     * @throws BusinessException
     */
    public function buy(Level $level, User $user, int $payment)
    {
        if ($user->level_id) {
            throw new BusinessException('购买失败：目前级别并未到期！');
        }
        $no = $this->getNo(false);
        $finance = Finance::create([
            'no' => $no,
            'user_id' => $user->id,
            'level_id' => $level->id,
            'payment' => $payment,
            'price' => $level->price,
        ]);
        $body = '购买级别【'.$level->name.'】';

        return app(PayService::class)->sendPay($finance->no, $body, $finance->price, $payment, $user);
    }

    /**
     * @return array
     */
    public function getAdminSelect()
    {
        $levels = Level::query()->pluck('name', 'id')->toArray();

        return Arr::prepend($levels, '注册会员', 0);
    }
}
