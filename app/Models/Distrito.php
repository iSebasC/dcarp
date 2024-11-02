<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distrito extends Model
{
    // use SoftDeletes;
	protected $table = 'distrito';
    protected $primaryKey = 'codigo';
  
    protected $fillable = [
	    'codigo',
	    'nombre',
    	'codProvincia',
	];

}
