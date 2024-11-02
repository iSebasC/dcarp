<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use SoftDeletes;

	protected $table = 'lote';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
