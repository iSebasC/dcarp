<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaCheckCalidad extends Model
{
    use SoftDeletes;

    protected $table = 'rptacheckcalidad';
    protected $primaryKey = 'id';
  
}
