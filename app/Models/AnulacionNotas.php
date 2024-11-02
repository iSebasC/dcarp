<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnulacionNotas extends Model
{
    use SoftDeletes;

	protected $table = 'anulacionnotas';
    protected $primaryKey = 'id';
  
}
