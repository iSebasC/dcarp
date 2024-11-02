<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaAuto extends Model
{
    protected $table = 'sistemaauto';
    protected $primaryKey = 'id';
    
    protected $fillable = [
	    'id',
	    'nombre'
	];
}
