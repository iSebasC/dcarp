<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaServicio extends Model
{
    protected $table = 'categoriaservicio';
    protected $primaryKey = 'id';
    
    protected $fillable = [
	    'id',
	    'nombre'
	];
}
