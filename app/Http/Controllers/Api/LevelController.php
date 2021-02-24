<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
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
        return $this->response()->success('ok', $this->levelService->getLevels());
    }

    /**
     * @param LicenseRequest $request
     * @param Level $level
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function buy(LicenseRequest $request, Level $level)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $license = $request->get('license');

        return $this->response()->success('pay data', $this->levelService->carBuyLevel($level, $user, $license));
    }
}
