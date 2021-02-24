<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * @var CarService
     */
    public $carService;

    public function __construct(CarService $carService)
    {
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

        return $this->response()->success('ok', $this->carService->getUserCars($user->id));
    }

    /**
     * @param Request $request
     * @param Car $car
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Car $car)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->response()->success('ok', $this->carService->userRemoveCar($car, $user));
    }
}
