<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelacionLocal extends Model
{
    use SoftDeletes;

	protected $table = 'relacionLocal';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'idTienda',
        'idAlmacen',
        'situacion'
    ];
}
