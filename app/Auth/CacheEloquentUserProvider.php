<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cache;

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
        return Cache::rememberForever('user_'.$identifier, function () use ($identifier) {
            $model = $this->createModel();

            return $this->newModelQuery($model)
                ->where($model->getAuthIdentifierName(), $identifier)
                ->first();
        });
    }
}
