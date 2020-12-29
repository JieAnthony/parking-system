<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * @var FinanceService
     */
    public $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->response()->success('ok', new CollectionResource($this->financeService->getUserFinanceList($user->id)));
    }
}
