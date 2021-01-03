<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseRequest;
use App\Http\Resources\CollectionResource;
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

        return $this->response()->success('ok', new CollectionResource($this->carService->getUserCarList($user->id)));
    }

    /**
     * @param LicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LicenseRequest $request)
    {
        $license = $request->get('license');
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->response()->success('ok', $this->carService->store($license, $user));
    }

    /**
     * @param Request $request
     * @param Car $car
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Car $car)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->response()->success('ok', $this->carService->delete($car, $user->id));
    }
}
