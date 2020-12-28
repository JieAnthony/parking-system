<?php

namespace App\Services;

use App\Models\Finance;

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
}
