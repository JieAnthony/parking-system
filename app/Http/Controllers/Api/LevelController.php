<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
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
     * @param PaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function buy(Level $level, PaymentRequest $request)
    {
        $user = $request->user();
        $payment = $request->get('payment');

        return $this->response()->success('ok', $this->levelService->buy($level, $user, $payment));
    }
}
