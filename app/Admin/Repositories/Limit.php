<?php

namespace App\Admin\Repositories;

use App\Models\Limit as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Limit extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
