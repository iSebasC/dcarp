<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleMovimientoCaja extends Model
{
  	use SoftDeletes;

	protected $table = 'detallemovimiento';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'tipo',
        'descripcion',
        'monto',
        'idMovimiento'
    ];

}
