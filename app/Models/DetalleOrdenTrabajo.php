<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleOrdenTrabajo extends Model
{
  	use SoftDeletes;

	protected $table = 'detalleordentrabajo';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

}
