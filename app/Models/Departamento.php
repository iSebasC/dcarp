<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    // use SoftDeletes;

	protected $table = 'departamento';
    protected $primaryKey = 'codigo';
  
    protected $fillable = [
	    'codigo',
	    'nombre',
    	'codPais',
	];

}
