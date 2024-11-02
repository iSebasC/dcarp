<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReciboCaja extends Model
{
    use SoftDeletes;
	protected $table = 'reciboscuenta';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
