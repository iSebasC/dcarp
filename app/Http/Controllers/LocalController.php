<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Serie;
use App\Models\TipoGuia;
use App\Models\RelacionLocal;
use App\Models\StockProducto;
use App\Models\Producto;
use App\Models\StockAuto;
use App\Models\Auto;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;

use App\Models\CategoriaProducto;

use App\Libraries\Funciones;
use DB;

use Validator;

class LocalController extends Controller
{
    public function getAll (Request $request) {
    	$codigo 	 = $request->get('codigo');
    	$direccion = $request->get('direccion');
    	$tipoId 	 = $request->get('tipoId');
    	$departamentoId	 = $request->get('departamentoId');
  
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
                        ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
						->where('local.codRegistro','LIKE', '%'.$codigo.'%')
						->where('local.direccion','LIKE', '%'.$direccion.'%');
        
    	/* if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'codigo':
    				if ($descripcion <> '')	
    				break;
    			default:
    				if ($descripcion <> '')
                        $locales = $locales
                    break;
    		}
    	} */

    	if ($tipoId != '' && $tipoId != 'todo') {
    		$locales = $locales->where('local.tipo','=',$tipoId);
    	}

    	if ($departamentoId != '' && $departamentoId != 'todo') {
    		$locales = $locales->where('local.idDepartamento','=',$departamentoId);
    	}
    	
    	$locales =  $locales->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"), DB::raw("(CASE WHEN local.tipo = 'A' THEN 'ALMACÉN' ELSE 'TIENDA' END) as tipo"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'dep.nombre as departamento')->orderBy('local.tipo','ASC')->orderBy('departamento','ASC');

        $lista = $locales->get();
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
			$arrPag = [['opc' => '1', 'bloqueado' => true]];
			$page = '1';
			$inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $locales->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['locales' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Local':' Locales'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

    public function getDepartamentos (Request $request) {
    	$departamentos = Departamento::all();
    	return ['departamentos' => $departamentos];
	}
	
	public function getProvincias ($id, Request $request) {
    	$provincias = Provincia::where('codDepartamento','=',$id)->get();
    	return ['provincias' => $provincias];
	}

	public function getDistritos ($id, Request $request) {
    	$distritos = Distrito::where('codProvincia','=',$id)->get();
    	return ['distritos' => $distritos];
	}


	public function obtenerAlmacen(Request $request) {
		$id = $request->get('id');

		$local = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
 				   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
 				   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
 				   ->where('local.id','=',$id)
 				   ->where('local.tipo','=','T')
 				   ->select('local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'dep.nombre as departamento')
 				   ->orderBy('local.tipo','ASC')->first();
 		//Local::findOrFail($id);
 		$almacenes 	   = [];
 		$dep_almacenes = [];
 		$id_almacenes  = '';
 		
 		if (!is_null($local)) {
 			$band = true;
 			$dep_almacenes  = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
 							  ->where('local.tipo','=','A')
 							  ->select('dep.nombre as departamento','dep.codigo as idDepartamento')
 							  ->groupBy('dep.codigo','dep.nombre')
 							  ->orderBy('dep.nombre','ASC')->get();

 			$almacenes = Local::leftjoin('provincia as p','p.codigo','=','local.idProvincia')
		 				->leftjoin('relacionlocal as rel','rel.idAlmacen','=','local.id')
 						->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
		 				->where('local.tipo','=','A')
		 				->where('rel.idTienda','=',$id)
		 				->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"), DB::raw("(CASE WHEN local.tipo = 'A' THEN 'ALMACÉN' ELSE 'TIENDA' END) as tipo"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'local.idDepartamento','rel.id as idRelacion','rel.situacion as situacionRelacion')
 				   		->orderBy('local.idDepartamento','ASC')->get();
 			foreach ($almacenes as $value) {
 				$id_almacenes.=$value->idRelacion.',';
 			}

 			$id_almacenes = substr($id_almacenes, 0,-1);
 			$band = 0;
 			$arreglo = [];
 			$general = [];
			$arregloMarcados = [];

 			foreach ($dep_almacenes as $dep) {
 				foreach ($almacenes as $alm) {
 					if ($dep->idDepartamento == $alm->idDepartamento) {
 						if ($band == 0) {
 							$departamento = $dep;
 							$band = 1;
 						}
						 $arreglo[] = $alm;
						 if ($alm->situacionRelacion == 'S') {
							 $arregloMarcados[] = $alm->idRelacion;
						 }
 					}
 				}
 				$general[] = ['departamento' => $departamento, 'almacenes' => (object) $arreglo];
 				$band = 0;
 				$arreglo = [];
 			}


 			return ['arreglo' => (object)$general, 'local' => $local, 'arreglomarcados' => implode(',',$arregloMarcados), 'estado' => true];
 			// $local_hab = RelacionLocal::where('idTienda','=',$id)
 			// 			 ->where('situacion','=','S')
 			// 			 ->select('idAlmacen')->orderBy('id')->get();
 		}else{
 			return ['url' => '/local', 'estado' => false];
 		}
	}

	public function guardarLocal(Request $request) {
		// dd($request);
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			try{
				$errors = [];
				$band = true;
				if ($id == 0) {
					$max = (int)substr(DB::table('local')->where('tipo','=',$request->get('tipo'))->max('codRegistro'), 1) +1;
		 			$c = new Local;
				} else {
					$c = Local::find($id);
				}
				
				if ($id == 0) {
					$serie = DB::table('local')->where('serie','=',$request->get('serie'))
							->first();
				
					if (!is_null($serie)) {
						$band = false;
						$errors[] = 'Serie ya Antes Registrada';
					} else {
						$band = true;	
					}
				}
				
				if ($band) {
					$c->idDepartamento = $request->get('select_departamento');
					$c->idProvincia    = $request->get('select_provincia');
					$c->idDistrito     = $request->get('select_distrito');
					$c->direccion	   = $request->get('direccion');
					$c->tipo		   = $request->get('tipo');
					$c->telefono	   = $request->get('telefono');
					$c->serie 		   = $request->get('serie');
					$cad = '';
					if ($id == 0) {
						$c->codRegistro	   = $request->get('tipo').str_pad($max, 5, "0", STR_PAD_LEFT);
						$c->save();
						$cad = ' Registrado';
						$idRegistro = $c->id;
						$numSerie   = $c->serie;
		 	
						if($request->get('tipo') == 'T'){
							$lista = Local::where('tipo','=','A')->select('id')->get();
							foreach ($lista as $value) {
								$r = new RelacionLocal;
								$r->idTienda  = $idRegistro;
								$r->idAlmacen = $value->id;
								$r->situacion = 'N';
								$r->save();
							}		 		
						}else{
							$lista = Local::where('tipo','=','T')->select('id')->get();
							foreach ($lista as $value) {
								$r = new RelacionLocal;
								$r->idTienda  = $value->id;
								$r->idAlmacen = $idRegistro; 
								$r->situacion = 'N';
								$r->save();
							}

							#STOCK PRODUCTOS -- INIT
							$existeProd = StockProducto::where('idAlmacen','=',$idRegistro)->first();
							if (is_null($existeProd)) {
								$productos = Producto::select('id')->get();
								foreach ($productos as $value) {
									$a = new StockProducto;
									$a->idProducto      = $value->id;
									$a->totalCompras    = 0;
									// $a->totalTranslados = 0;
									$a->totalVentas     = 0;
									$a->totalIncidencias= 0;
									$a->idAlmacen 		= $idRegistro;
									$a->save();
								}
							}	

							#STOCK AUTOS -- INIT
							$existeProdA = StockAuto::where('idAlmacen','=',$idRegistro)->first();
							if (is_null($existeProdA)) {
								$autos = Auto::select('id')->get();
								foreach ($autos as $value) {
									$a = new StockAuto;
									$a->idAuto      = $value->id;
									$a->totalCompras    = 0;
									$a->totalVentas     = 0;
									$a->idAlmacen 		= $idRegistro;
									$a->save();
								}
							}	
						}
						// SERIES PARA TIENDA O ALMACEN 
						$tipos = TipoGuia::where('tipoLocal','=',$request->get('tipo'))->get();
						foreach ($tipos as $value) {
							$serie = new Serie;
							$serie->idLocal = $idRegistro;
							$serie->tipoLocal = $request->get('tipo');
							$serie->tipoDocumento = $value->abreviatura;
							$serie->serie = $numSerie;
							$serie->numero= 0;
							$serie->save();
						}

					} else {
						$c->update();
						$cad = ' Actualizado';
					}
					$errors[] = 'Local'.$cad.' Correctamente';
				} else {
					$errors[] = 'Local ya Antes Registrado';
				}

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}

	public function validar (Request $request) {
		$reglas = [
            'select_departamento'=> 'required|numeric',
            'select_provincia'=> 'required|numeric',
            'select_distrito'=> 'required|numeric',
			'direccion'=> 'required|max:255|string',
			'tipo'=> 'required|string',
			'telefono' => 'required|numeric|digits_between:6,9',
			'serie' => 'required|numeric|min:1|max:999'
		];

        $mensajes = [
            'select_departamento.required'=> 'Indique Departamento',
			'select_provincia.required'=> 'Indique Provincia',
			'select_distrito.required'=> 'Indique Distrito',
			'direccion.required'	=> 'Indique Dirección',
			'direccion.max' => 'Dirección debe tener como máximo 255 caracteres',
			'tipo.required'	=> 'Indique Tipo de Local',
			'telefono.required'	=> 'Indique Teléfono',
			'telefono.digits_between'=> 'Teléfono Debe Tener 06 o 09 Caracteres Numéricos',
			'serie.required'  => 'Indique Serie',
			'serie.numeric'   => 'Serie debe ser un número',
			'serie.min'		  => 'Serie debe tener un valor mínimo de 1',
			'serie.max'		  => 'Serie debe tener un valor máximo de 999',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function getLocal ($id, Request $request) {
    	$local = Local::find($id);
    
    	if (!is_null($local)) {
    		$respuesta = ['estado' => true, 'local' => $local];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
	}
	
	public function getLocalTipo ($tipo, Request $request) {
		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
						->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
						->where('local.tipo','=',$tipo)
						->select('local.id', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'dep.nombre as departamento')
						->orderBy('local.tipo','ASC')
						->orderBy('departamento','ASC')
						->get();
						
		return ['locales' => $locales];
	}

	public function getTiendas (Request $request) {
		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
						->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
						->where('local.tipo','=','T')
						->select('local.id', 'local.direccion','dep.nombre as departamento')
						->orderBy('local.tipo','ASC')
						->orderBy('departamento','ASC')
						->get();
						
		return ['locales' => $locales];
	}

	public function getAlmacenes ($id, Request $request) {
		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
						->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
						->leftjoin('relacionlocal as rel','rel.idAlmacen','=','local.id')
						->where('local.tipo','=','A')
						->where('rel.idTienda','=',$id)
						->where('rel.situacion','=','S')
						->select('local.id', 'local.direccion','dep.nombre as departamento')
						->orderBy('local.tipo','ASC')
						->orderBy('departamento','ASC')
						->get();
						
		return ['locales' => $locales];
	}
	
	public function guardarLocalRelacion (Request $request) {
		DB::beginTransaction();
		try{
			$errors = [];
			$band = true;
			$id = $request->get('id');

			RelacionLocal::where('idTienda','=',$id)->update(['situacion' => 'N']);
			$lista = explode(',', $request->get('arreglomarcados'));
			foreach ($lista as $value) {
				if ($value != '') {
					$r = RelacionLocal::find($value);
					$r->situacion = 'S';
					$r->update();
				}
			}
			
			$cad = ' Actualizado';
			$errors[] = 'Relación de Tiendas y Almacenes'.$cad.' Correctamente';
		}catch(\Exception $ex){
			$msje = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
	
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];
	}

	public function eliminarLocal(Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$local = Local::find($id);
			$local->delete();
			$errors[] = 'Local Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}
	
}
