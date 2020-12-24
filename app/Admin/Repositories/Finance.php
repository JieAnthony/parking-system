<?php

namespace App\Admin\Repositories;

use App\Models\Finance as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Finance extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
