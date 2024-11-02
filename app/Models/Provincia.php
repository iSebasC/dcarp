<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provincia extends Model
{
    // use SoftDeletes;

	protected $table = 'provincia';
    protected $primaryKey = 'codigo';
    
    protected $fillable = [
	    'codigo',
	    'nombre',
    	'codDepartamento',
	];
}
