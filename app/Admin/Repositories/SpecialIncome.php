<?php

namespace App\Admin\Repositories;

use App\Models\SpecialIncome as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SpecialIncome extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
