<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaTallerOrden extends Model
{
    use SoftDeletes;

    protected $table = 'rptatallerorden';
    protected $primaryKey = 'id';
  
}
