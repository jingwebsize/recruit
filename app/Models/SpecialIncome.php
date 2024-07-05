<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class SpecialIncome extends Model
{
	use HasDateTimeFormatter;
    //protected $table = 'special_incomes';
    protected $guarded = [''];
    
}
