<?php

namespace App\Services;

use App\Events\UserCarsEvent;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CarService
{
    /**
     * @param int $userId
     * @return Car[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getUserCars(int $userId)
    {
        return Cache::rememberForever('user_' . $userId . '_cars', function () use ($userId) {
            return Car::query()
                ->whereHas('users', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->where('status', true)
                ->get();
        });
    }

    /**
     * @param string $license
     * @param bool $needStore
     * @return Car|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCarByLicense(string $license, bool $needStore = false, bool $hasError = false)
    {
        $query = Car::query()->where('license', $license);
        if ($needStore) {
            return $query->firstOrCreate([
                'license' => $license,
            ]);
        } else {
            return $query->first();
        }
    }

    /**
     * @param int $id
     * @return Car|Car[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getCarById(int $id)
    {
        return Car::find($id);
    }

    /**
     * @param Car $car
     * @param User $user
     * @return array
     */
    public function carBindUser(Car $car, User $user) : array
    {
        $result = $car->users()->syncWithoutDetaching($user);
        event(new UserCarsEvent($user, $car));

        return $result;
    }

    /**
     * @param Car $car
     * @param User $user
     * @return bool
     */
    public function userRemoveCar(Car $car, User $user) : bool
    {
        $result = $car->users()->detach($user);
        event(new UserCarsEvent($user, $car));

        return (bool)$result;
    }
}
