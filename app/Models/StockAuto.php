<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAuto extends Model
{
    use SoftDeletes;

	protected $table = 'stockauto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
}
