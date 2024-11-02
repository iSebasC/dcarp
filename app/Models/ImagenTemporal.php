<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImagenTemporal extends Model
{
    use SoftDeletes;

	protected $table = 'imagentemp';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}
