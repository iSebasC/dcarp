<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaManejoOrden extends Model
{
    use SoftDeletes;

    protected $table = 'rptamanejoorden';
    protected $primaryKey = 'id';
  
}
