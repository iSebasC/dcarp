<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProducto extends Model
{
    use SoftDeletes;

	protected $table = 'stockproducto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'id',
        'idProducto',
        'totalCompras',        
        'totalTranslados',
        'totalVentas',
        'totalStock',
        'idAlmacen'
    ];

    
}
