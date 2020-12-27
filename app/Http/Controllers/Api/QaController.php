<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Qa;
use Illuminate\Support\Facades\Cache;

class QaController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $items = Cache::rememberForever('qas', function () {
            return Qa::query()
                ->select(['id', 'title', 'content'])
                ->orderByDesc('id')
                ->get()
                ->toArray();
        });

        return $this->response()->success('获取成功', $items);
    }
}
