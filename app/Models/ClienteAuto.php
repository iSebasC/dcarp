<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteAuto extends Model
{
    use SoftDeletes;
	protected $table = 'clienteauto';
    protected $primaryKey = 'id';
  
    protected $dates = ['deleted_at'];
}
