<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerieDocumento extends Model
{
	use SoftDeletes;

	protected $table = 'seriedocumento';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
   
}
