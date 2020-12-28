<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Http\Requests\PaymentRequest;
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
        $items = $this->orderService->getUserOrderList($user->id);

        return $this->response()->success('ok', new CollectionResource($items));
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        return $this->response()->success('ok', $this->orderService->getOrderDetail($order));
    }

    /**
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function find(LicenseRequest $request)
    {
        $license = $request->get('license');

        return $this->response()->success('ok', $this->orderService->findCarOrder($license));
    }

    public function pay(PaymentRequest $request, Order $order)
    {
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Order $order)
    {
        return $this->response()->success('ok', $this->orderService->delete($order));
    }
}
