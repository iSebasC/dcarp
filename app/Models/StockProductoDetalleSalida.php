<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProductoDetalleSalida extends Model
{
    use SoftDeletes;

	protected $table = 'stockproductodetallesalida';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    
}
