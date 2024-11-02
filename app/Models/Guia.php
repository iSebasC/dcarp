<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guia extends Model
{
    use SoftDeletes;

	protected $table = 'guia';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'serie',
        'numero',
        'fecha',
        'idTipoGuia',
        'total',
        'idAlmacenSalida',
        'idAlmacenLlegada',
        'idProveedor',
        'idCliente',
        'lugarCompra',
        'motivoIncidencia',
        'tipoIncidencia'
    ];


}
