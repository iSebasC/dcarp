<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoCompra extends Model
{
    use SoftDeletes;

	protected $table = 'pedidocompra';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
