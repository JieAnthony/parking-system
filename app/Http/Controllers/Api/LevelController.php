<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyLevelRequest;
use App\Models\Level;
use App\Services\LevelService;

class LevelController extends Controller
{
    /**
     * @var LevelService
     */
    public $levelService;

    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response()->success('ok', $this->levelService->list());
    }

    /**
     * @param Level $level
     * @param BuyLevelRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function buy(Level $level, BuyLevelRequest $request)
    {
        $user = $request->user();
        $payment = $request->get('payment');
        $carId = $request->get('car_id');

        return $this->response()->success('ok', $this->levelService->buy($level, $user, $payment));
    }
}
