<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleCompraAuto extends Model
{
  	use SoftDeletes;
	protected $table = 'detallecompraauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
