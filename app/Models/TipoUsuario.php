<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoUsuario extends Model
{
    use SoftDeletes;

	protected $table = 'categoriapersonal';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'nombre',
        'descripcion'
    ];
}


