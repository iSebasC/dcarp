<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oportunidad extends Model
{
    use SoftDeletes;
	protected $table = 'oportunidad';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
