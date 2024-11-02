<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProductoDetalle extends Model
{
    use SoftDeletes;

	protected $table = 'stockproductodetalle';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    
}
