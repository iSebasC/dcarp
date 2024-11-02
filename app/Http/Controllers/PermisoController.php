<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Menu;
use App\Models\MenuUsuario;
use App\Models\TipoUsuario;


use App\Libraries\Funciones;
use DB;

use Validator;

class PermisoController extends Controller
{
    public function getAll (Request $request) {
    	$filtro 	 = $request->get('filtro');
    	$descripcion = $request->get('descripcion');
    	$nombre		 = $request->get('nombre');
    	
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$permisos = DB::table('categoriapersonal')
    				->whereNull('deleted_at')
					->where('nombre','LIKE', '%'.$nombre.'%')
					->where('descripcion','LIKE', '%'.$descripcion.'%');
    	// ->get();
                     
    	// if ($filtro != '' && $filtro != 'todo') {
    	// 	switch ($filtro) {
    	// 		case 'nombre':
    	// 			if ($descripcion <> '')	
						// $permisos = $permisos;
    	// 			break;
    	// 	}
    	// }

    	
    	$permisos =  $permisos->select('id','nombre','descripcion', DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y') as fechaRegistro"))->orderBy('nombre','ASC');

        $lista = $permisos->get();
        $cantidad = count($lista);

		if ($cantidad > 0) {
			$paginador = new Funciones();
			// dd($filas);
			$paramPaginador = $paginador->generarPaginacion($lista, $page, $filas);
			$arrPag = $paramPaginador['cadenapaginacion'];
			$page = $paramPaginador['nuevapagina'];
			$inicio = $paramPaginador['inicio'];
            $fin = $paramPaginador['fin'];
            $paramInicio = $paramPaginador['inicioArr'];
            $paramFin = $paramPaginador['finArr'];
            
		} else {
			$arrPag = [['opc' => '1', 'bloqueado'=> true]];
			$page = '1';
			$inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $permisos->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();
		// $paginador = round($cantidad/$filas);
		// $arrPag = array();
		// if ($paginador < 6) {
		// 	if ($paginador <> 0) {
		// 		for ($i=0; $i < $paginador; $i++) { 
		// 			$arrPag[] = array('opc'=> ($i+1));
		// 		}
		// 	} else {
		// 		$arrPag[] = array('opc'=> '1');
		// 	}
		// } else {
		// 	//10
		// 	$arrPag[] = array('opc' => '1');s
		// 	$arrPag[] = array('opc' => '2');
		// 	$arrPag[] = array('opc' => '3');
		// 	$arrPag[] = array('opc' => '...');
		// 	$arrPag[] = array('opc' => $paginador-1);
		// 	$arrPag[] = array('opc' => $paginador);
		// }
        
    	return ['permisos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Tipo de Usuario':' Tipos de Usuario'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

    public function getMenu ($id, Request $request) {

      $menuP = MenuUsuario::join('menu as m','m.id','=','menuusuario.idMenu')
      			->where('m.nivel','=','1')->whereNull('m.deleted_at')->where('menuusuario.idCategoriaPersonal','=',$id)
      			->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
      			->orderBy('m.agruparMant','DESC')->orderBy('m.ordenItem','ASC')->get();

      $menuS = MenuUsuario::join('menu as m','m.id','=','menuusuario.idMenu')
      		   ->where('m.nivel','=','2')->whereNotNull('m.idMenu')->whereNull('m.deleted_at')->where('menuusuario.idCategoriaPersonal','=',$id)
      		   ->select('m.*','menuusuario.estado','menuusuario.id as idMenuUsuario')
      		   ->orderBy('m.idMenu','ASC')->orderBy('m.ordenItem','ASC')->get();
	  
	  $arrOpc = [];
      foreach($menuP as $menu_p) {
		    $msec = [];
		    foreach ($menuS as $menu_s) {
		        if ($menu_p->id == $menu_s->idMenu) {
					$msec [] = $menu_s;
					if ($menu_s->estado == 'S')
						$arrOpc[] = $menu_s->id;
		        }
		    }

		    if (count($msec)>0) {
		        $menuPr[] = array('menu_p' => $menu_p, 'menu_s' => (object) $msec, 'cantSubs' => count($msec));
		    } else {
				$menuSO[] = array('menu_p' => $menu_p);
				if ($menu_p->estado == 'S')
					$arrOpc[] = $menu_p->id;
		    //     if ($menu_p->estado == 'S') {
		    //         $menuPr[] = array('menu_p' => $menu_p, 'menu_s' => (object) $msec, 'cantSubs' => count($msec));
		    //     }
		    }
		    $msec = [];
		}

		return ['opciones' => (object)$menuPr, 'encabezados' => (object)$menuSO, 'opciones02' => implode(',', $arrOpc)];
    }

    public function getPermiso ($id, Request $request) {
    	$per = TipoUsuario::find($id);
    
    	if (!is_null($per)) {
    		$respuesta = ['estado' => true, 'permiso' => $per];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
    }

    public function guardarPermiso(Request $request) {
		// dd('Ok');
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			try{
				$errors = [];
				$nombre = $request->get('nombres');
				$band = true;
				if ($id == 0) {
					$valid = TipoUsuario::where('nombre','=',$nombre)->first();
					if (is_null($valid))
						$c = new TipoUsuario;
					else 
						$band = false;
				} else {
					$valid = TipoUsuario::where('nombre','=',$nombre)->where('id','<>',$id)->first();
					if (is_null($valid)) 
						$c = TipoUsuario::find($id);
					else 
						$band = false;
				}

				if ($band) {
					$c->nombre = $request->get('nombres');
					$c->descripcion = $request->get('descripcion');
					$cad = '';
					if ($id == 0) {
						$c->save();
						$cad = ' Registrado';

						$opciones = Menu::select('id')->get();
						foreach ($opciones as $value) {
							$mc = new MenuUsuario;
							$mc->idCategoriaPersonal = $c->id;
							$mc->idMenu = $value->id;
							$mc->estado = ($value->id==1?'S':'N');
							$mc->save();
						}
					} else {
						$c->update();
						$cad = ' Actualizado';
					}
					$errors[] = 'Tipo de Usuario'.$cad.' Correctamente';
				} else {
					$errors[] = 'Tipo de Usuario ya Antes Registrado';
				}

			}catch(\Exception $ex){
				$msje = $ex->getMessage();
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}
	
	public function validar (Request $request) {
		$reglas = [
            'nombres'=> 'required|max:191|string',
            'descripcion'=> 'nullable|max:255|string'
        ];

        $mensajes = [
            'nombres.required'=> 'Indique Nombre',
            'nombres.max'=> 'Nombre debe tener como máximo 191 caracteres',
		    'descripcion.max'=> 'Descripción debe tener como máximo 255 caracteres',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function guardarPermisoUsuario (Request $request) {
		DB::beginTransaction();
		$band = true;
		$id = $request->get('id');
		$arrOpc = explode(',',$request->get('opciones'));
		// dd($arrOpc);
		$opciones = MenuUsuario::where('idCategoriaPersonal','=',$id)->get();
		$errors = [];
		try{
			foreach ($opciones as $op) {
				$m = MenuUsuario::find($op->id);
				if ($request->get('chk_'.$op->idMenu) === 'on') {
					if ($op->idMenu != 1) {
						$m->estado = 'S';
					} 
				} else {
					$m->estado = 'N';
				}

				if ($op->idMenu == 1 && $id != 8) {
					$m->estado = 'S';
				} 
		
				$m->update();
			}
			$errors[] = 'Permisos Actualizados Correctamente';
		}catch(\Exception $ex){
			$msje = $ex->getMessage();
			$band = false;
			$errors[] = 'Ocurri�� un Error, Vuelva a Intentarlo';
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];
	}

	public function eliminarPermisoUsuario(Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$tipo = TipoUsuario::find($id);
			$tipo->delete();
			$errors[] = 'Tipo de Usuario Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}
}
