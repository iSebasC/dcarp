<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaCheckTaller extends Model
{
    use SoftDeletes;

    protected $table = 'rptachecktaller';
    protected $primaryKey = 'id';
  
}
