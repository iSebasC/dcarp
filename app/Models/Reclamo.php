<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reclamo extends Model
{
    use SoftDeletes;
	protected $table = 'reclamo';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
