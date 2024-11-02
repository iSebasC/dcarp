<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleAnulacionNotas extends Model
{
  	use SoftDeletes;
	protected $table = 'detalleanulacionnotas';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
