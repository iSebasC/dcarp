<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionIgv extends Model
{
    use SoftDeletes;
	protected $table = 'configuracionigv';
    protected $primaryKey = 'id';

}
