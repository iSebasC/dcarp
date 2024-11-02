<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetallePaquete extends Model
{
  	use SoftDeletes;

	protected $table = 'detallepaquete';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

}
