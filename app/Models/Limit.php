<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Limit extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'limit';
    protected $guarded = [''];
    
}
