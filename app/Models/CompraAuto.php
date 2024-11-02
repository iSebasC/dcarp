<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompraAuto extends Model
{
    use SoftDeletes;

	protected $table = 'compraauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
