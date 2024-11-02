<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use SoftDeletes;

	protected $table = 'trabajador';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'dni',
        'apellidos',
        'nombres',
        'genero',
        'correoE',
        'tipoLocal',
        'idDepartamento',
        'idProvincia',
        'idDistrito',
        'direccion',
        'idCategoriaPersonal',
        'fechaNacimiento',
        'telefono'
    ];

}
