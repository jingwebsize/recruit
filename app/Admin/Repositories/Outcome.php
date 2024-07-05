<?php

namespace App\Admin\Repositories;

use App\Models\Outcome as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Outcome extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
