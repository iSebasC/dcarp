<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forma extends Model
{
    protected $table = 'forma';
    protected $primaryKey = 'id';
    
    protected $fillable = [
	    'id',
	    'nombre'
	];

}
