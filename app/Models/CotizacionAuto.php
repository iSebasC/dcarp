<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CotizacionAuto extends Model
{
    use SoftDeletes;

	protected $table = 'cotizacionauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}
