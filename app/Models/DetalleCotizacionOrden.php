<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCotizacionOrden extends Model
{
  	use SoftDeletes;

	protected $table = 'detallecotizacionorden';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

}
