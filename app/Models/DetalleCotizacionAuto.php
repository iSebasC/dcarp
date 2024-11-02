<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCotizacionAuto extends Model
{
  	use SoftDeletes;

	protected $table = 'detallecotizacionauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

}
