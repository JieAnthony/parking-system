<?php

namespace App\Admin\Repositories;

use App\Models\Car as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Car extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
