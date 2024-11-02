<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auto extends Model
{
    use SoftDeletes;
	protected $table = 'auto';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
