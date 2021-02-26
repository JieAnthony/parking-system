<?php

namespace App\Services;

use App\Enums\FinanceStatusEnum;
use App\Events\FinancePaymentSuccessEvent;
use App\Exceptions\BusinessException;
use App\Models\Finance;

class FinanceService
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserFinances(int $userId)
    {
        return Finance::query()
            ->with(['car:id,license'])
            ->where('user_id', $userId)
            ->where('status', FinanceStatusEnum::OK)
            ->orderByDesc('id')
            ->paginate(config('info.page.limit', 10));
    }

    /**
     * @param string $no
     * @return Finance|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getFinanceByNo(string $no)
    {
        return Finance::query()->where('no', $no)->firstOrFail();
    }

    /**
     * @param string $no
     * @param int $userId
     * @param int $carId
     * @param $levelId
     * @param float $price
     * @return Finance
     */
    public function store(string $no, int $userId, int $carId, $levelId, float $price)
    {
        $finance = new Finance();
        $finance->no = $no;
        $finance->user_id = $userId;
        $finance->car_id = $carId;
        $finance->level_id = $levelId;
        $finance->price = $price;
        $finance->save();

        return $finance;
    }

    /**
     * @param Finance $finance
     * @return Finance
     */
    public function handlePaymentSuccess(Finance $finance)
    {
        if ($finance->status == FinanceStatusEnum::OK) {
            return $finance;
        }
        $finance->status = true;
        $finance->payed_at = now();
        $finance->save();
        event(new FinancePaymentSuccessEvent($finance));

        return $finance;
    }
}
