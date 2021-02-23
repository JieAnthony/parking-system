<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;

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

    public function index(Request $request)
    {
    }

    public function show(Order $order)
    {
    }

    public function find(LicenseRequest $request)
    {
    }

    public function pay(Order $order)
    {
    }

    public function destroy(Order $order)
    {
    }
}
