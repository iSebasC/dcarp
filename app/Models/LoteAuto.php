<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoteAuto extends Model
{
    use SoftDeletes;

	protected $table = 'loteauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
}
