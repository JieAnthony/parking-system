<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    public $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $items = $this->orderService->getUserOrders($user->id);

        return $this->response()->success('ok', new CollectionResource($items));
    }

    public function find(LicenseRequest $request)
    {
    }

    public function pay(Order $order)
    {
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Order $order)
    {
        $this->authorize('own', $order);

        return $this->response()->success('ok',$this->orderService->delete($order));
    }
}
