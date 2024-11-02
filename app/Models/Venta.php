<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

	protected $table = 'venta';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'serie',
        'numero',
        'fecha',
        'total',
        'idAlmacenSalida',
        'idCliente'
    ];


}
