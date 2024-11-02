<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuenta extends Model
{
    use SoftDeletes;
	protected $table = 'cuenta';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
