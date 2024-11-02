<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerificacionCheckList extends Model
{
    use SoftDeletes;
    //
    protected $table = 'verificacionchecklist';
    protected $primaryKey = 'id';
    
    protected $dates = ['deleted_at', 'updated_at'];
}
