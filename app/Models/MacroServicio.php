<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MacroServicio extends Model
{
    protected $table = 'macroservicio';
    protected $primaryKey = 'id';

    protected $dates = ['updated_at', 'deleted_at'];
}
