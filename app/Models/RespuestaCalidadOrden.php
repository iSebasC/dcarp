<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaCalidadOrden extends Model
{
    use SoftDeletes;

    protected $table = 'rptacalidadorden';
    protected $primaryKey = 'id';
  
}
