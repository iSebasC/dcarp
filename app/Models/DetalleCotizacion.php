<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCotizacion extends Model
{
  	use SoftDeletes;

	protected $table = 'detallecotizacion';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

}
