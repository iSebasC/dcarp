<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallePedidoCompra extends Model
{
  	use SoftDeletes;
	protected $table = 'detallepedidocompra';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
