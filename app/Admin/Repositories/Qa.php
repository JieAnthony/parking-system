<?php

namespace App\Admin\Repositories;

use App\Models\Qa as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Qa extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
