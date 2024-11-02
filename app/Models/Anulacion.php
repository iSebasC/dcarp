<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anulacion extends Model
{
    // use SoftDeletes;

	protected $table = 'anulacion';
    protected $primaryKey = 'id';
  
}
