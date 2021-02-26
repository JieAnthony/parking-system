<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Order;
use App\Services\CarService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    public $orderService;

    /**
     * @var CarService
     */
    public $carService;

    public function __construct(OrderService $orderService, CarService $carService)
    {
        $this->orderService = $orderService;
        $this->carService = $carService;
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

    /**
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     */
    public function find(LicenseRequest $request)
    {
        $license = $request->get('license');
        $car = $this->carService->getCarByLicense($license);

        return $this->response()->success('ok', $this->orderService->getParkingOrderInfo($car));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function payment(Request $request, Order $order)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->response()->success('ok', $this->orderService->paymentParkingOrder($order, $user));
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Order $order)
    {
        $this->authorize('own', $order);

        return $this->response()->success('ok', $this->orderService->delete($order));
    }
}
