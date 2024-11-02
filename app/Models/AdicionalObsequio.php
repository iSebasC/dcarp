<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdicionalObsequio extends Model
{
    use SoftDeletes;
	protected $table = 'adicionalobsequio';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
