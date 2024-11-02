<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MensajeSistema extends Model
{
    use SoftDeletes;
    protected $table = 'mensajesistema';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}
