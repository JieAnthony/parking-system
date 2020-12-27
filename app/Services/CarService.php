<?php

namespace App\Services;

use App\Models\Car;

class CarService
{
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
