<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use SoftDeletes;
	protected $table = 'cita';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
