<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleVenta extends Model
{
  	use SoftDeletes;
	protected $table = 'detalleventa';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'item',
        'cantidad',
        'precio',
        'descripcion',
        'subTotal',
        'idGuia',
        'idProducto'
    ];


}
