<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Car;
use App\Models\User;

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
                'level' => function ($query) {
                    $query->select(['id', 'name']);
                },
            ])
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', true)
            ->orderByDesc('id')
            ->paginate(config('info.page.limit'));
    }

    /**
     * @param string $license
     * @param User $user
     * @return Car|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws BusinessException
     */
    public function store(string $license, User $user)
    {
        $car = $this->getCarByLicense($license, true);
        if ($car->users()->where('user_id', $user->id)->exists()) {
            throw new BusinessException('已添加过该车辆了！');
        } else {
            $car->users()->attach($user);
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
        return (bool) $car->users()->detach($userId);
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
