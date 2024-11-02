<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaContable extends Model
{
    use SoftDeletes;
	protected $table = 'cuentacontable';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
