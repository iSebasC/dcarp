<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoTiempoLibre extends Model
{
    use SoftDeletes;

	protected $table = 'motivotiempolibre';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
