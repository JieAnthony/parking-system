<?php

namespace App\Services;


use App\Enums\FinanceStatusEnum;
use App\Events\FinancePaymentSuccessEvent;
use App\Exceptions\BusinessException;
use App\Models\Finance;
use Illuminate\Support\Carbon;

class FinanceService
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserFinances(int $userId)
    {
        return Finance::query()
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
        return Finance::query()->where('no', $no)->first();
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
     * @param string $no
     * @param string $payedAt
     * @return Finance|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws BusinessException
     */
    public function handlePaymentSuccess(string $no, string $payedAt)
    {
        $finance = $this->getFinanceByNo($no);
        if (! $finance) {
            throw new BusinessException('data not found');
        }
        if ($finance->status) {
            return $finance;
        }
        $finance->status = true;
        $finance->payed_at = Carbon::parse($payedAt);
        $finance->save();
        event(new FinancePaymentSuccessEvent($finance));

        return $finance;
    }
}
