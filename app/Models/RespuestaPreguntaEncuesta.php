<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaPreguntaEncuesta extends Model
{
    use SoftDeletes;

    protected $table = 'rptapreguntasencuesta';
    protected $primaryKey = 'id';
  
}
