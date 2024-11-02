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
use App\Models\TipoProducto;
use App\Models\MarcaAuto;
use App\Models\Auto;
use App\Models\StockAuto;
use App\Models\ConfiguracionIgv;


use App\Libraries\Funciones;
use DB;

use Validator;

class GananciaController extends Controller
{
	
    public function getAll (Request $request) {
    	$nombre 	 = $request->get('nombre');
    	$porcentaje = $request->get('porcentaje');
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$ganancias = DB::table('tipoproducto')->whereNull('deleted_at')
					->where('nombre','LIKE', '%'.$nombre.'%');

		if ($porcentaje != '') {
			$ganancias = $ganancias->where('porcentaje','=', (float)$porcentaje);
		}
    	
    	$ganancias =  $ganancias->orderBy('id','ASC');

        $lista = $ganancias->get();
        $cantidad = count($lista);
		
		if ($cantidad > 0) {
			$paginador = new Funciones();
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
		
		$lista = $ganancias->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['ganancias' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Margen':' Márgenes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	} 
     
    
    public function guardar(Request $request) {
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				if ($id == 0) {
					$tipo = new TipoProducto;
				} else {
					$tipo = TipoProducto::find($id);
				}
				$tipo->codigo    = $request->get('codigo');
				$tipo->porcentaje      = $request->get('porcentaje');
				
				if ($id == 0) {
					$tipo->save();
					$cad = ' Registrado';
				} else {
					$tipo->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Margen'.$cad.' Correctamente';
	

			}catch(\Exception $ex){
				$msje = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}
	
	public function validar (Request $request) {
		$reglas = [
            'codigo'=> 'required|string',
			'porcentaje'=> 'required|numeric|min:0',
			'id' => 'required',
        ];

        $mensajes = [
            'codigo.required'=> 'Indique Código',
            'porcentaje.required'=> 'Indique Margen (%)',
            'porcentaje.numeric'=> 'Margen (%) debe ser un número',
            'porcentaje.min'=> 'Margen (%) debe ser un mayor a 0',
            'id.required'   => 'ID no especificado'
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
		$opciones = MenuUsuario::where('idCategoriaPersonal','=',$id)->get();
		$errors = [];
		try{
			foreach ($opciones as $op) {
				$m = MenuUsuario::find($op->id);
				if (in_array($op->idMenu,$arrOpc) || $op->idMenu == 1) {
					$m->estado = 'S';
				} else {
					$m->estado = 'N';
				}
				$m->update();
			}
			$errors[] = 'Permisos Actualizados Correctamente';
		}catch(\Exception $ex){
			$msje = $ex->getMessage();
			$band = false;
			$errors[] = 'Ocurrió un Error, Vuelva a Intentarlo';
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
	
	/******************************************************** */
	/** CONFIGURACION IGV	 */
	/******************************************************** */
	public function getConfiguracionIgv (Request $request) {
		$configuracion = ConfiguracionIgv::where('id','1')->select('id','igv','mostrar',DB::Raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i') as fechaUlt"))->first();

		return ['estado'=> true, 'configuracion' => $configuracion];
	}

	
	public function validarConfiguracion (Request $request) {
		$reglas = [
            'igv'	  => 'required',
			'mostrar' => 'required',
	    ];

        $mensajes = [
            'igv.required'=> 'Indique Igv',
            'mostrar.required'=> 'Indique Campo Mostrar',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function guardarConfiguracion (Request $request) {
		$errors = $this->validarConfiguracion($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				$p = ConfiguracionIgv::find(1);
				$p->mostrar = $request->get('mostrar');
				$p->update();
				$cad = ' Actualizado';

				$errors[] = 'Configuraci��n de Igv '.$cad.' Correctamente';
	

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}

	public function getMostrarIgv(Request $request) {
		$con = ConfiguracionIgv::find(1);
		$mostrar = true;
		$band = false;
		if (!is_null($con)) {
			$mostrar = ($con->mostrar == 'S'?true:false);
			$band 	 = true;
		}

		return ['estado'=> $band, 'mostrar' => $mostrar];
	}

}
