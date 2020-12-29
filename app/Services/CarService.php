<?php

namespace App\Services;

use App\Models\Car;

class CarService
{
    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserCarList(int $userId)
    {
        return Car::query()
            ->with([
                'users' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
            ])
            ->orderByDesc('id')
            ->paginate($params['limit'] ?? config('info.page.limit'));
    }

    /**
     * @param string $license
     * @param int|null $userId
     * @return Car
     */
    public function store(string $license, int $userId = null)
    {
        $car = new Car();
        $car->license = $license;
        $car->save();
        if ($userId) {
            $car->users()->attach($userId);
        }

        return $car;
    }

    /**
     * @param Car $car
     * @param int|null $userId
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Car $car, int $userId = null)
    {
        $car->users()->detach($userId);

        return $car->delete();
    }

    /**
     * @param int $id
     * @return Car|Car[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getCarById(int $id)
    {
        return Car::query()->findOrFail($id);
    }

    /**
     * @param string $license
     * @param bool $needStore
     * @return Car|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getCarByLicense(string $license, bool $needStore = false)
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
}
