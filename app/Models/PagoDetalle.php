<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoDetalle extends Model
{
    use SoftDeletes;

	protected $table = 'pagodetalle';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

}