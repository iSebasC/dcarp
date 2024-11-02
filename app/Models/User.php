<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\MenuUsuario;
use Illuminate\Support\Facades\DB;;
use Auth;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'login';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario', 'correoElectronico', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    protected $dates = ['deleted_at'];

    public function hasPermiso($ruta){
        $id = Auth::user()->idCategoriaPersonal;
        
        $menu = MenuUsuario::join('menu as m','m.id','=','menuUsuario.menuId')
                // ->where('m.agruparMant','=','1')
                ->whereNull('m.deleted_at')
                ->where('menuUsuario.tipoUsuarioId','=',$id)
                ->where('menuUsuario.estado','=','S')
                ->select('m.*','menuUsuario.estado','menuUsuario.id as idMenuUsuario')
                ->orderBy('m.agruparMant','ASC')->orderBy('m.ordenItem','ASC')->get();

        if($ruta->route()->getName() == 'logout'){
            return true;
        } 

        foreach ($menu as $value) {
            if ($value->accion == $ruta->route()->getName()) {
                return true;
            }
        } 

        return false;
    }

}
