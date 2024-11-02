<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

	protected $table = 'producto';
    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];
   
}
