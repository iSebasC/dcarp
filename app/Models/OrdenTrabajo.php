<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajo extends Model
{
    use SoftDeletes;

	protected $table = 'ordentrabajo';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}