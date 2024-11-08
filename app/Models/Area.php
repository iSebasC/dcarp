<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;
	protected $table = 'area';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
