<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCompra extends Model
{
  	use SoftDeletes;
	protected $table = 'detallecompra';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
