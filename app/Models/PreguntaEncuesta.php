<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreguntaEncuesta extends Model
{
    use SoftDeletes;

	protected $table = 'preguntasencuesta';
    protected $primaryKey = 'id';

}
