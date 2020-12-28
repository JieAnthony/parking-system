<?php

namespace App\Auth;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class CacheEloquentUserProvider extends EloquentUserProvider
{
    /**
     * CacheEloquentUserProvider constructor.
     * @param HasherContract $hasher
     */
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $userService = app(UserService::class);

        return $userService->getUserById($identifier, true);
    }
}
