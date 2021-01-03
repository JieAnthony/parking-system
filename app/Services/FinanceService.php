<?php

namespace App\Services;

use App\Events\Finance\FinancePaySuccessEvent;
use App\Exceptions\BusinessException;
use App\Models\Finance;
use Carbon\Carbon;

class FinanceService
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserFinanceList(int $userId)
    {
        return Finance::query()
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate(config('info.page.limit'));
    }

    /**
     * @param string $no
     * @param string $payedAt
     * @return Finance|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function handlePaySuccess(string $no,string $payedAt)
    {
        $finance = Finance::query()->where('no',$no)->firstOrFail();
        if ($finance->status){
            return $finance;
        }
        $level = $finance->level;
        $car = $finance->car;
        $car->level_id = $level->id;
        $car->end_at = now()->addDays($level->days + 1)->toDateString();
        $car->save();
        $finance->status = true;
        $finance->payed_at = Carbon::parse($payedAt);
        $finance->save();
        event(new FinancePaySuccessEvent($finance));

        return $finance;
    }
}
