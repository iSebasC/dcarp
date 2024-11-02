<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Local extends Model
{
    use SoftDeletes;

	protected $table = 'local';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'codRegistro',
        'idDepartamento',
        'idProvincia',
        'idDistrito',
        'direccion',
        'tipo',
        'telefono'
    ];

}
