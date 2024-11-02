<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionPersonal extends Model
{
	protected $table = 'relacionempleado';
    protected $primaryKey = 'id';
  
    protected $fillable = [
        'id',
        'idTrabajador',
        'idLocal',
        'tipo',
        'situacion'
    ];

}
