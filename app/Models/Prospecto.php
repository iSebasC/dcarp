<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospecto extends Model
{
    use SoftDeletes;
	protected $table = 'prospecto';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
