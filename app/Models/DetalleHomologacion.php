<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleHomologacion extends Model
{
    use SoftDeletes;

	protected $table = 'detallehomologacion';
    protected $primaryKey = 'id';
  
}
