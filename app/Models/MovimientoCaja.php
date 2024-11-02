<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCaja extends Model
{
    use SoftDeletes;

	protected $table = 'movimientocaja';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'idUsuario',
        'fecha',
        'saldoApertura',
        'saldoCierre',
        'idTienda',
    ];
}
