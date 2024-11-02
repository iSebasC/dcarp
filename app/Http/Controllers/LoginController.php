<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Session;
use DB;

use App\Models\User;
use App\Models\MenuUsuario;


class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }


    public function login(Request $request){
        $credenciales = $this->validate(request(),[
            'user' =>'required|string|max:191',
            'pass' =>'required|string|max:255'
        ]);

      
        if(Auth::attempt(['usuario'=> $request->input('user'), 'password' => $request->input('pass')])){
            try{
                // if (Auth::check()) {
                $usuario = Auth::user(); 
                if(!is_null($usuario)){
                    Session::put('usuario_sesion',$usuario);
                    $id = $usuario->categoriaPersonalId;
                    $menuPr = [];
                    
                    // $c = SocioContinuador::find(Auth::user()->usuarioId);
                    // Session::put('socio_sesion',$c); 
                        
                    $menuPath = DB::table('menu as m')
                                ->join('menuusuario as mu','mu.idMenu','=','m.id')
                                ->whereNull('m.deleted_at')
                                ->where('mu.estado','S')
                                ->where('mu.idCategoriaPersonal', $id)
                                ->select('m.nombre','m.accion')
                                ->distinct()
                                ->get();


                    $menuP = MenuUsuario::join('menu as m','m.id','=','menuusuario.idMenu')
                        ->where('m.nivel','=','1')
                        ->whereNull('m.deleted_at')
                        ->where('menuusuario.idCategoriaPersonal','=',$id)
                        // ->where('menuusuario.estado','=','S')
                        ->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
                        ->orderBy('m.idMenu','ASC')->orderBy('m.ordenItem','ASC')->get();

                    $menuS = MenuUsuario::join('menu as m','m.id','=','menuusuario.idMenu')
                            ->where('m.nivel','=','2')
                            ->whereNotNull('m.idMenu')
                            ->whereNull('m.deleted_at')
                            ->where('menuusuario.idCategoriaPersonal','=',$id)
                            ->where('menuusuario.estado','=','S')
                            ->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
                            ->orderBy('m.idMenu','ASC')->orderBy('m.ordenItem','ASC')->get();
                    
                    foreach($menuP as $menu_p) {
                        $msec = [];
                        foreach ($menuS as $menu_s) {
                            if ($menu_p->id == $menu_s->idMenu) {
                                $msec [] = $menu_s;
                            }
                        }

                        if (count($msec)>0) {
                            $menuPr[] = array('menu_p' => $menu_p, 'menu_s' => (object) $msec, 'cantSubs' => count($msec));
                        } else {
                            if ($menu_p->estado == 'S') {
                                $menuPr[] = array('menu_p' => $menu_p, 'menu_s' => (object) $msec, 'cantSubs' => count($msec));
                            }
                        }
                        $msec = [];
                    }
                    // dd($menuPr);
                    
                    /* 
                    $menuP = MenuUsuario::join('menu as m','m.id','=','menuusuario.menuId')
                        ->where('m.agruparMant','=','1')
                        ->whereNull('m.deleted_at')
                        ->where('menuusuario.tipoUsuarioId','=',$id)
                        ->where('menuusuario.estado','=','S')
                        ->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
                        ->orderBy('m.agruparMant','ASC')->orderBy('m.ordenItem','ASC')->get();


                    $menuS = MenuUsuario::join('menu as m','m.id','=','menuusuario.menuId')
                        ->where('m.agruparMant','=','2')
                        ->whereNull('m.deleted_at')
                        ->where('menuusuario.tipoUsuarioId','=',$id)
                        ->where('menuusuario.estado','=','S')
                        ->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
                        ->orderBy('m.agruparMant','ASC')->orderBy('m.ordenItem','ASC')->get();
                    */
                    // dd($menuPr);

                    Session::put('menuP', (object) $menuPr);
                    // dd($menuPr);
                    // Session::put('menuS',$menuS);
                    return array('usuario' => $usuario, 'menuP' => (object) $menuPr, 'menuPath' => $menuPath, 'redireccionar' => ($id!=8?'inicio':'seguimiento'), 'estado' => true);
                    // return view('/layouts.principal'); 
            
                }else{
                    return array('error' => 'Error en Credenciales, Consulte con el Administrador del Sistema', 'estado' => false);
                }
                // }
            }catch(\Exception $e){
                $msje = $e->getMessage(); //'Error de Conexión';
                return array('error' => $msje, 'estado' => false);
            }
        }else{
            return array('error' => 'Error en Credenciales, Consulte con el Administrador del Sistema', 'estado' => false);
        }
    }

    public function logout() {
        if (Auth::guest()) {
            return redirect('/');
        }
        Session::flush();
        Auth::logout();
        //   Session::regenerate();
        return 'Ok';
        // return redirect('/');
    }
    
    public function isValidSession () {
        $band = false;
        if (Auth::check()) {
            $band = true;
        }

        return ['estado' => $band];
    }

}
