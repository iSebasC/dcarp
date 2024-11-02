<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Serie extends Model
{
	use SoftDeletes;

	protected $table = 'serie';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'idLocal',
        'tipoLocal',
        'tipoDocumento',
        'serie',
        'numero'
    ];

   
}
