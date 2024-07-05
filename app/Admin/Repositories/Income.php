<?php

namespace App\Admin\Repositories;

use App\Models\Income as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Income extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
