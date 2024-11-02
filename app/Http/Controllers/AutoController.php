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
use App\Models\MarcaAuto;
use App\Models\ModeloAuto;
use App\Models\OrigenProspecto;
use App\Models\EtiquetaProspecto;

use App\Models\Auto;
use App\Models\StockAuto;
use App\Models\Prospecto;
use App\Models\ClienteAuto;
use App\Models\AdicionalObsequio;
use App\Models\Oportunidad;
use App\Models\MensajeSistema;
use App\Models\Personal;

use App\Libraries\Funciones;
use DB;

use Validator;
use Auth;

class AutoController extends Controller
{
	public $listPermisosNotificaciones = [1, 15];

	public function getMarcas(Request $request)
    {
        $marcas = MarcaAuto::select('id', 'nombre')
                            ->whereNull('deleted_at')
                            ->orderBy('nombre', 'ASC')
                            ->get();
        
        return ['marcas' => $marcas];
    }


    public function getAll(Request $request)
    {
        $version     = $request->get('version');
        $transmision = $request->get('transmision');
        $descripcion = $request->get('descripcion');
        $marcaId     = $request->get('marcaId');
        $modeloId    = $request->get('modeloId');
        $linea       = $request->get('linea');
        $filas       = $request->get('filas');
        $page        = $request->get('page');
    
        $autos = Auto::leftJoin('marcaauto as m', 'm.id', '=', 'auto.marcaId')
                     ->leftJoin('modeloauto as mod', 'mod.id', '=', 'auto.modeloId')
                     ->where('auto.descripcion', 'LIKE', '%'.$descripcion.'%')
                     ->where('auto.transmision', 'LIKE', '%'.$transmision.'%')
                     ->where('auto.version', 'LIKE', '%'.$version.'%');
    
        if ($marcaId != '' && $marcaId != 'todo') {
            $autos = $autos->where('auto.marcaId', '=', $marcaId);
        }
    
        if ($modeloId != '' && $modeloId != 'todo') {
            $autos = $autos->where('auto.modeloId', '=', $modeloId);
        }
    
        if ($linea != '' && $linea != 'todo') {
            $autos = $autos->where('auto.linea', '=', $linea);
        }
    
        $autos = $autos->select('auto.*', 'mod.nombre as modelo',  
            DB::raw("(CASE auto.linea WHEN 'L' THEN 'Liviano' ELSE 'Pesado' END) as linea"),
            DB::raw("UPPER(m.nombre) as marca"), // Convertimos el nombre de la marca a mayúsculas
            DB::raw("DATE_FORMAT(auto.created_at,'%d/%m/%Y') as fechaRegistro"),
            DB::raw("CONCAT('/storage', auto.urlImagen) as url"))
            ->orderBy('auto.id', 'ASC');
    
        $lista = $autos->get();
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
    
        $lista = $autos->offset(($page-1) * $filas)
                       ->limit($filas)
                       ->get();
    
        return [
            'autos' => $lista,
            'cantidad' => ($cantidad < 10 ? '0'.$cantidad : $cantidad).($cantidad == 1 ? ' Auto' : ' Autos'),
            'page' => $page,
            'paginador' => $arrPag,
            'inicio' => $inicio,
            'fin' => $fin,
            'paramInicio' => $paramInicio,
            'paramFin' => $paramFin
        ];
    }

	 

    public function getAuto ($id, Request $request) {
    	$auto = Auto::find($id);
    
    	if (!is_null($auto)) {
    		$respuesta = ['estado' => true, 'auto' => $auto];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
    }

    public function getAutos(Request $request) {
        $busqueda = $request->get('query');
    
        $autos = DB::table('auto as a')
                    ->leftJoin('marcaauto as mc', 'mc.id', '=', 'a.marcaId')
                    ->leftJoin('modeloauto as ma', 'ma.id', '=', 'a.modeloId')
                    ->select(
                        'a.id',
                        'a.linea', 
                        'mc.id as marcaId', 
                        'ma.id as modeloId',
                        DB::raw("CONCAT(mc.nombre, ' ', IFNULL(ma.nombre,''), 
                            ', Versión: ', IFNULL(a.version,'-'), 
                            ', Transmisión: ', IFNULL(a.transmision,'-'), 
                            ', Año: ', IFNULL(a.anio,'-')) as auto"),
                        'mc.nombre as marca', 
                        'ma.nombre as modelo',
                        'a.anio'
                    )
                    ->whereNull('a.deleted_at')  // Filtrar registros no eliminados
                    ->whereNotNull('mc.nombre')  // Filtrar registros donde marca no es NULL
                    ->whereNotNull('ma.nombre')  // Filtrar registros donde modelo no es NULL
                    ->whereNotNull('a.anio')     // Filtrar registros donde año no es NULL
                    ->where(DB::raw("CONCAT(mc.nombre, ' ', IFNULL(ma.nombre,''), 
                        ', Versión: ', IFNULL(a.version,'-'), ', Año: ', IFNULL(a.anio,'-'))"), 
                        'LIKE', '%'.$busqueda.'%')
                    ->get();
    
        return ['autos' => $autos];
    }


    public function getModelosAuto(Request $request)
    {
        $modelosauto = ModeloAuto::select('id', 'nombre')
                                 ->whereNull('deleted_at')
                                 ->orderBy('nombre', 'ASC')
                                 ->get();
        
        return ['modelosauto' => $modelosauto];
    }


	public function getOrigenesProspecto (Request $request) {
    	$origenes = OrigenProspecto::select('id','nombre')->orderBy('nombre','ASC')->get();
  		return ['origenesprospecto' => $origenes];
    }

	public function getEtiquetasProspecto (Request $request) {
		$etiquetas = EtiquetaProspecto::select('id','nombre')->orderBy('nombre','ASC')->get();
		return ['etiquetasprospecto' => $etiquetas];
	}

    public function guardarAuto(Request $request) {
		// dd($request);
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				#PARA MARCA DE AUTO
				if ($request->get('select_marca') === 'otro') {
					$nombre = $request->get('marca');
					$existe = MarcaAuto::where('nombre','=', $nombre)->first();
					$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
					if(is_null($existe)){
						$cat = new MarcaAuto;
						$cat->nombre = $nombre_g;
						// $cat->tipo = $request->get('tipo');
						$cat->save();

						$id_marca = $cat->id;
					}else{
						$id_marca = $existe->id;
					}
				}else{
					$id_marca = $request->get('select_marca');
				}

				#PARA MODELO DE AUTO
				if ($request->get('select_modelo') === 'otro') {
					$nombre = $request->get('modelo');
					$existe = ModeloAuto::where('nombre','=', $nombre)->first();
					$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
					if(is_null($existe)){
						$cat = new ModeloAuto;
						$cat->nombre = $nombre_g;
						// $cat->tipo = $request->get('tipo');
						$cat->save();

						$id_modelo = $cat->id;
					}else{
						$id_modelo = $existe->id;
					}
				}else{
					$id_modelo = $request->get('select_modelo');
				}

				if ($id == 0) {
					$auto = new Auto;
				} else {
					$auto = Auto::find($id);
				}
				$auto->codproveedor      = $request->get('codproveedor');
				// $auto->tipoAuto    = $request->get('tipo');
				$auto->marcaId     = $id_marca;
				$auto->modeloId    = $id_modelo;
				// $auto->modelo      = $request->get('modelo');
				$auto->anio        = $request->get('anio');
				$auto->version     = $request->get('version');
				$auto->transmision = $request->get('transmision');
				$auto->descripcion = $request->get('descripcion');
				$auto->linea 	   = $request->get('linea');

				// $auto->color = $request->get('color');
				// $auto->precio = $request->get('precio');

				if (isset($_FILES['ficha'])) {
					// $checkFile = $_FILES['ficha']['tmp_name'];
					$ficha=$request->file('ficha');
					if (!is_null($ficha)) {
						$checkName = $_FILES['ficha']['name'];
						$extension = pathinfo($checkName, PATHINFO_EXTENSION);
						// $size = $_FILES['ficha']['size'];
						// dd($checkFile, $extension);
						// return [$checkFile, $extension];
						if ($id == 0) {
							$id_max = Auto::max('id')+1;
						} else {
							$id_max = $id;
						}

						$chkNombre = $id_max.'_ficha.' .$extension;
						// $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes/'.$chkNombre;
						
						// move_uploaded_file($checkFile, $path);     
						// dd(\Storage::disk('local_ficha'));
						\Storage::disk('local_ficha')->put($chkNombre, \File::get($ficha));
						$auto->urlFicha = '/fichas/'.$chkNombre;
					}
				} /*elseif ($id == 0) {
					$errors[] = 'Indique Ficha Técnica Por Favor';
					$band = false;					
				}*/

				if (isset($_FILES['imagen'])) {
					$imagen=$request->file('imagen');
					if (!is_null($imagen)) {
						$checkName = $_FILES['imagen']['name'];
						$extension = pathinfo($checkName, PATHINFO_EXTENSION);
						
						// $size = $_FILES['imagen']['size'];
						// dd($checkFile, $extension);
						// return [$checkFile, $extension];
						if ($id == 0) {
							$id_max = Auto::max('id')+1;
						} else {
							$id_max = $id;
						}

						$chkNombre = $id_max.'_imagen_auto.' .$extension;
						// $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes/'.$chkNombre;
						
						// move_uploaded_file($checkFile, $path);     
						// dd(\Storage::disk('local_ficha'));
						\Storage::disk('local_auto_imagen')->put($chkNombre, \File::get($imagen));
						$auto->urlImagen = '/autos_imagen/'.$chkNombre;
					}
				} 

				if ($band) {
					if ($id == 0) {
						$auto->save();
						$this->guardarStock($auto->id);
						$cad = ' Registrado';
					} else {
						$auto->update();
						$cad = ' Actualizado';
					}
					
					$errors[] = 'Auto'.$cad.' Correctamente';
				}

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}

	public function eliminarAuto (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$prod = Auto::find($id);
			$prod->delete();
			$errors[] = 'Auto Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

	public function getMensajesSistema($id, $tipo) {
		$isRenderDeleted = false;
		$msjs = DB::table('mensajesistema as ms')
				->join('trabajador as tr', 'tr.id','=','ms.idPersonal')
				->whereNull('ms.deleted_at');

		if ($tipo == 'P') {
			$msjs = $msjs->where('idProspecto', $id);
		} else {
			$msjs = $msjs->where('idOportunidad', $id);
		}

		$lista = $msjs->select(DB::Raw("CONCAT(tr.nombres,' ', tr.apellidos) as registradoPor"), 
			DB::Raw("DATE_FORMAT(ms.created_at,'%d/%m/%Y') as fechaRegistro"), 'ms.hora',
			DB::Raw("(CASE WHEN ms.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
			'ms.realizado', 'ms.comentario_realizacion as comentario',
			DB::Raw("(CASE WHEN ms.realizado = 'N' AND ms.fechaFin >= CURDATE() THEN 'S' ELSE 'N' END) as activoMarcar"),
			DB::Raw("DATE_FORMAT(ms.fechaInicio,'%d/%m/%Y') as fechaInicio"),
			DB::Raw("DATE_FORMAT(ms.fechaFin,'%d/%m/%Y') as fechaFin"),
			'ms.titulo', 'ms.descripcion','ms.id')
			->orderBy('ms.created_at')
			->get();

		if (in_array(Auth::user()->categoriaPersonalId, $this->listPermisosNotificaciones)) {
			$isRenderDeleted = true;
		}
		return ['detalles' => $lista, 'tienePermiso' => $isRenderDeleted];
		
	}

	public function marcarNotificacion (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$comentarios = $request->get('comentario');
			$msje = MensajeSistema::find($id);
	
			if (!is_null($msje)) {
				$msje->realizado = 'S';
				$msje->comentario_realizacion = $comentarios;
				$msje->update();
			}

			$errors[] = 'Tarea Actualizada Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

	public function eliminarNotificacion (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$comentarios = $request->get('comentario');
			$msje = MensajeSistema::find($id);
	
			if (!is_null($msje)) {
				$msje->delete();
			}

			$errors[] = 'Tarea Eliminada Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	

	}

    public function guardarStock($id){
    	$almacenes = Local::where('tipo','=','A')->select('id')->get();
    	foreach ($almacenes as $value) {
    	 	$a = new StockAuto;
    		$a->idAuto     = $id;
    		$a->totalCompras    = 0;
    		$a->totalVentas     = 0;
			$a->totalIncidencias= 0;
			$a->idAlmacen 		= $value->id;
    		$a->save();
    	} 
    }

	###################################################
	#			PROSPECTOS							  #
	###################################################

	public function eliminarProspecto (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$prod = Prospecto::find($id);
			$prod->idPersonalEliminar = Auth::user()->usuarioId;
			$prod->update();
			$prod->delete();
			$errors[] = 'Prospecto Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

	public function obtenerProspecto ($id) {
		$result = DB::table('prospecto as ps')
				->leftjoin('origenprospecto as op','op.id', '=','ps.idOrigen')
				->leftjoin('etiquetaprospecto as ep','ep.id', '=','ps.idEtiqueta')
				->leftjoin('marcaauto as m','m.id','=','ps.idMarcaAuto')
				->leftjoin('modeloauto as mod', 'mod.id','=', 'ps.idModeloAuto')
				->leftjoin('clienteauto as cl','cl.id','=', 'ps.idCliente')
				->leftjoin('auto as at','at.id','=','ps.idAuto')
				->where('ps.id', $id)
				->select('ps.*', 'at.linea', 'm.nombre as marca', 'mod.nombre as modelo', 
				'cl.tipodocumento', 'cl.nombres', 'cl.apellidos', 'cl.razonSocial', 'cl.telefono',
				'cl.correoElectronico', 'cl.direccion', 'cl.documento', 'op.nombre as origen', 
				'ep.nombre as etiqueta')
				->first();

		return ['prospecto' => $result, 'estado' => true];
	}

    public function getProspectosAll(Request $request) {
        $documento   = $request->get('dni');
        $asesor      = $request->get('asesor'); // Cambiado de correo a asesor
        $personal    = $request->get('personal');
        $telefono    = $request->get('telefono');
        $marcaId     = $request->get('marca');
        $modeloId    = $request->get('modelo');
        $origenId    = $request->get('origen');
        $etiquetaId  = $request->get('etiqueta');
        $linea       = $request->get('linea');
        $filas       = $request->get('filas');
        $page        = $request->get('page');
    
        $prospectos = DB::table('prospecto as ps')
            ->leftJoin('trabajador as asesor', 'asesor.id', '=', 'ps.idAsesorRegistra')
            ->leftjoin('marcaauto as m', 'm.id', '=', 'ps.idMarcaAuto')
            ->leftjoin('modeloauto as mod', 'mod.id', '=', 'ps.idModeloAuto')
            ->leftjoin('clienteauto as cl', 'cl.id', '=', 'ps.idCliente')
            ->leftjoin('origenprospecto as op', 'op.id', '=', 'ps.idOrigen')
            ->leftjoin('etiquetaprospecto as ep', 'ep.id', '=', 'ps.idEtiqueta')
            ->where('cl.documento', 'LIKE', '%'.$documento.'%')
            ->where('cl.telefono', 'LIKE', '%'.$telefono.'%')
            ->where(function($qq) use($personal) {
                $qq->where('cl.razonSocial', 'LIKE', '%'.$personal.'%')
                   ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"), 'LIKE', '%'.$personal.'%');
            });
    
        // Filtro por asesor (nombre concatenado)
        if ($asesor != '') {
            $prospectos = $prospectos->where(DB::Raw("CONCAT(asesor.apellidos,' ', asesor.nombres)"), 'LIKE', '%'.$asesor.'%');
        }
    
        if ($marcaId != '' && $marcaId != 'todo') {
            $prospectos = $prospectos->where('ps.idMarcaAuto', '=', $marcaId);
        }
    
        if ($modeloId != '' && $modeloId != 'todo') {
            $prospectos = $prospectos->where('ps.idModeloAuto', '=', $modeloId);
        }
    
        if ($origenId != '' && $origenId != 'todo') {
            $prospectos = $prospectos->where('ps.idOrigen', '=', $origenId);
        }
    
        if ($etiquetaId != '' && $etiquetaId != 'todo') {
            $prospectos = $prospectos->where('ps.idEtiqueta', '=', $etiquetaId);
        }
    
        if (Auth::user()->categoriaPersonalId != '1') { // ADMINISTRADOR DEL SISTEMA
            $prospectos = $prospectos->where('ps.idAsesorRegistra', Auth::user()->usuarioId);
        }
    
        $prospectos = $prospectos->select(
            'ps.*',
            'mod.nombre as modelo', 
            'op.nombre as origen',
            'ep.nombre as etiqueta',
            'ps.tiempoEstimadoCompra', 
            'cl.tipodocumento', 
            'cl.documento', 
            'cl.correoElectronico',
            'cl.telefono', 
            'cl.direccion',
            DB::Raw("(CASE cl.tipodocumento WHEN 'RUC' THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
            'm.nombre as marca', 
            DB::Raw("DATE_FORMAT(ps.created_at, '%d/%m/%Y') as fechaRegistro"), 
            DB::Raw("DATE_FORMAT(ps.tiempoEstimadoCompra, '%d/%m/%Y') as tiempoEstimado"),
            DB::Raw("CONCAT(asesor.apellidos, ' ', asesor.nombres) as asesor"), // Mostrar el nombre del asesor
            DB::Raw("(CASE WHEN ps.deleted_at IS NOT NULL THEN 'S' ELSE 'N' END) eliminado")
        )
        ->orderBy('ps.created_at', 'DESC');
    
        $lista = $prospectos->get();
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
    
        $lista = $prospectos->offset(($page-1) * $filas)
            ->limit($filas)
            ->get();
    
        return [
            'prospectos' => $lista, 
            'cantidad' => ($cantidad < 10 ? '0'.$cantidad : $cantidad).($cantidad == 1 ? ' Prospecto' : ' Prospectos'), 
            'page' => $page, 
            'paginador' => $arrPag, 
            'inicio' => $inicio, 
            'fin' => $fin, 
            'paramInicio' => $paramInicio, 
            'paramFin' => $paramFin
        ];
    }


    
	public function findOportunidad ($id) {
		$oportunidad = DB::table('oportunidad as op')
			->leftjoin('prospecto as ps', 'ps.id', '=', 'op.idProspecto')
			->leftjoin('clienteauto as cl','cl.id','=', 'ps.idCliente')
			->leftjoin('clienteauto as cl2', 'cl2.id','=','op.idCliente')
			->select('op.*', 'op.idAuto', DB::Raw("(CASE WHEN cl.id IS NOT NULL THEN cl.documento ELSE cl2.documento END) as documento"))
			->where('op.id', $id)
			->first();
	
		return ['estado' => true, 'oportunidad' => $oportunidad];

	}

	public function eliminarOportunidad (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$prod = Oportunidad::find($id);
			if (!is_null($prod)) {
				#PARA RESTAURAR PROSPECTO
				// DB::table('prospecto')
				// ->where('id', $prod->idProspecto)
				// ->update(['deleted_at' => null, 'idPersonalEliminar' => null, 'situacion' => 'N']);
				$prod->fase = 'B';
				$prod->idPersonalEliminar = Auth::user()->usuarioId;
				$prod->motivoAnulacion = $request->get('motivo');
				$prod->update();
				$prod->delete();
				$errors[] = 'Oportunidad Eliminada Correctamente';
			} else {
				$band = false;
				$errors[] = 'Oportunidad ya antes Eliminada';
			}
		
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		if ($band) {
			DB::commit();
		} else {
			DB::rollback();
		}
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}
	public function obtenerOportunidad ($id) {
		$result = DB::table('oportunidad as ps')
				->leftjoin('marcaauto as m','m.id','=','ps.idMarcaAuto')
				->leftjoin('modeloauto as mod', 'mod.id','=', 'ps.idModeloAuto')
				->where('ps.id', $id)
				->select('ps.*',  'm.nombre as marca', 'mod.nombre as modelo',  
				DB::Raw("DATE_FORMAT(ps.fechaCierre,'%d/%m/%Y') as fechaCierreF"))
				->first();

		$adicionales = [];
		$obsequios = [];

		$resultRef = null;
		
		if (!is_null($result)) {			
			$resultRef = DB::table('prospecto as ps')
				->leftjoin('origenprospecto as op','op.id', '=','ps.idOrigen')
				->leftjoin('etiquetaprospecto as ep','ep.id', '=','ps.idEtiqueta')
				->leftjoin('marcaauto as m','m.id','=','ps.idMarcaAuto')
				->leftjoin('modeloauto as mod', 'mod.id','=', 'ps.idModeloAuto')
				->leftjoin('clienteauto as cl','cl.id','=', 'ps.idCliente')
				->leftjoin('auto as at','at.id','=','ps.idAuto')
				->where('ps.id', $result->idProspecto)
				// ->orWhere('cl.id', $result->idCliente)
				->select('ps.*', 'at.linea', 'm.nombre as marca', 'mod.nombre as modelo', 
				'cl.tipodocumento', 'cl.nombres', 'cl.apellidos', 'cl.razonSocial', 'cl.telefono',
				'cl.correoElectronico', 'cl.direccion', 'cl.documento', 'op.nombre as origen', 
				'ep.nombre as etiqueta')
				->first();

			$adicionales = DB::table('adicionalobsequio')
					->where('tipo', 'A')
					->whereIn('id', explode(',', $result->adicionales))
					->select('id', 'nombre as adicional')
					->get();
			$obsequios = DB::table('adicionalobsequio')
					->where('tipo', 'O')
					->whereIn('id', explode(',', $result->obsequios))
					->select('id', 'nombre as obsequio')
					->get();
		}
		return ['oportunidad' => $result, 'prospecto' => $resultRef, 'obsequios' => $obsequios, 'adicionales' => $adicionales, 'estado' => true];
	}


public function getOportunidadesAll(Request $request) {
    $documento   = $request->get('dni');
    $asesor      = $request->get('asesor'); // Filtro por asesor
    $personal    = $request->get('personal');
    $telefono    = $request->get('telefono');
    $faseId      = $request->get('fase');
    $lineaId     = $request->get('linea');
    $monedaId    = $request->get('moneda');
    $certezaId   = $request->get('certeza');
    $filas       = $request->get('filas');
    $page        = $request->get('page');
    $marcaId     = $request->get('marca');
    $modeloId    = $request->get('modelo');

    $oportunidades = DB::table('oportunidad as op')
        ->leftJoin('trabajador as asesor', 'asesor.id', '=', 'op.idAsesorRegistra')
        ->leftjoin('prospecto as ps', 'ps.id', '=', 'op.idProspecto')
        ->leftjoin('clienteauto as cl', 'cl.id', '=', 'ps.idCliente')
        ->leftjoin('clienteauto as cl2', 'cl2.id', '=', 'op.idCliente')
        ->leftjoin('auto as a', 'a.id', '=', 'op.idAuto') // Relación con la tabla de autos usando idAuto
        ->leftjoin('marcaauto as m', 'm.id', '=', 'a.marcaId') // Relación con la tabla de marcas del auto
        ->leftjoin('modeloauto as mod', 'mod.id', '=', 'a.modeloId') // Relación con la tabla de modelos del auto
        ->where(DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.documento ELSE cl.documento END)"), 'LIKE', '%'.$documento.'%')
        ->where(DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.telefono ELSE cl.telefono END)"), 'LIKE', '%'.$telefono.'%')
        ->where(function ($qq) use($personal) {
            $qq->where(DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.razonSocial ELSE cl.razonSocial END)"), 'LIKE', '%'.$personal.'%')
               ->orWhere(DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN CONCAT(cl2.apellidos,' ', cl2.nombres) ELSE CONCAT(cl.apellidos,' ', cl.nombres) END)"), 'LIKE', '%'.$personal.'%');
        });

    // Filtro por asesor (nombre concatenado)
    if ($asesor != '') {
        $oportunidades = $oportunidades->where(DB::Raw("CONCAT(asesor.apellidos, ' ', asesor.nombres)"), 'LIKE', '%'.$asesor.'%');
    }

    if ($faseId != '' && $faseId != 'todo') {
        $oportunidades = $oportunidades->where('op.fase', '=', $faseId);
    }

    if ($monedaId != '' && $monedaId != 'todo') {
        $oportunidades = $oportunidades->where('op.moneda', '=', $monedaId);
    }

    if ($lineaId != '' && $lineaId != 'todo') {
        $oportunidades = $oportunidades->where('op.linea', '=', $lineaId);
    }

    if ($certezaId != '' && $certezaId != 'todo') {
        $oportunidades = $oportunidades->where('op.certeza', '=', $certezaId);
    }
    
    if ($marcaId != '' && $marcaId != 'todo') {
        $oportunidades = $oportunidades->where('a.marcaId', '=', $marcaId);
    }

	if ($modeloId != '' && $modeloId != 'todo') {
        $oportunidades = $oportunidades->where('a.modeloId', '=', $modeloId);
    }

    if (Auth::user()->categoriaPersonalId != '1') { // ADMINISTRADOR DEL SISTEMA
        $oportunidades = $oportunidades->where('op.idAsesorRegistra', Auth::user()->usuarioId);
    }

    // Selección de campos, incluyendo autos, marca y modelo
    $oportunidades = $oportunidades->select(
        'op.*',
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.tipodocumento ELSE cl.tipodocumento END) as tipodocumento"),
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.documento ELSE cl.documento END) as documento"),
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.correoElectronico ELSE cl.correoElectronico END) as correoElectronico"),
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.telefono ELSE cl.telefono END) as telefono"),
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN cl2.direccion ELSE cl.direccion END) as direccion"),
        DB::Raw("(CASE WHEN op.idCliente IS NOT NULL THEN 
            (CASE cl2.tipodocumento WHEN 'RUC' THEN cl2.razonSocial ELSE CONCAT(cl2.apellidos, ' ', cl2.nombres) END) ELSE 
            (CASE cl.tipodocumento WHEN 'RUC' THEN cl.razonSocial ELSE CONCAT(cl.apellidos, ' ', cl.nombres) END) END) as cliente"),
        DB::Raw("DATE_FORMAT(op.created_at,'%d/%m/%Y') as fechaRegistro"),
        DB::Raw("DATE_FORMAT(op.fechaCierre,'%d/%m/%Y') as fecha"),
        DB::Raw("ROUND(op.monto, 2) montoRedondeado"),
        DB::Raw("CONCAT(asesor.apellidos, ' ', asesor.nombres) as asesor"), // Mostrar el nombre del asesor
        DB::Raw("UPPER(m.nombre) as marca"), // Mostrar el nombre de la marca del auto
        'mod.nombre as modelo', // Mostrar el nombre del modelo del auto
		'm.nombre as marca', // Mostrar el nombre de la marca del auto
        'a.descripcion as autoDescripcion', // Mostrar la descripción del auto
        'a.transmision as autoTransmision', // Mostrar la transmisión del auto
        'a.version as autoVersion', // Mostrar la versión del auto
        DB::Raw("(CASE WHEN op.deleted_at IS NOT NULL THEN 'S' ELSE 'N' END) as eliminado")
    )
    ->orderBy('op.created_at', 'DESC');

    $lista = $oportunidades->get();
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

    $lista = $oportunidades->offset(($page-1) * $filas)
        ->limit($filas)
        ->get();

    return [
        'oportunidades' => $lista, 
        'cantidad' => ($cantidad < 10 ? '0'.$cantidad : $cantidad).($cantidad == 1 ? ' Oportunidad' : ' Oportunidades'), 
        'page' => $page, 
        'paginador' => $arrPag, 
        'inicio' => $inicio, 
        'fin' => $fin, 
        'paramInicio' => $paramInicio, 
        'paramFin' => $paramFin
    ];
}





public function guardarProspecto(Request $request)
{
    $errors = $this->validarProspecto($request);

    if (count($errors) > 0) {
        return ['errores' => $errors, 'estado' => false];
    } else {
        $id = $request->get('id');
        DB::beginTransaction();
        $band = true;
        $errors = [];

        try {
            // Verificar si es un nuevo registro
            if ($id == 0) {
                // Verificar si el cliente ya existe (independientemente del asesor)
                $clienteDuplicado = DB::table('clienteauto')
                    ->where('tipoDocumento', $request->get('tipodocumento'))
                    ->where('documento', $request->get('documento'))
                    ->whereNull('deleted_at')
                    ->first();

                    if ($clienteDuplicado) {
                      // Obtener el nombre del asesor desde la tabla trabajador
                      $asesorRegistrante = DB::table('trabajador')
                          ->where('id', $clienteDuplicado->idAsesorRegistra)
                          ->first();
                  
                      // Depura el resultado para verificar si existe o no
                      if ($clienteDuplicado) {
                        // Obtener los nombres y apellidos del asesor desde la tabla trabajador
                        $asesorRegistrante = DB::table('trabajador')
                            ->where('id', $clienteDuplicado->idAsesorRegistra)
                            ->first();
                    
                        // Depura el resultado para verificar si existe o no
                        if ($asesorRegistrante) {
                            if (isset($asesorRegistrante->nombres) && isset($asesorRegistrante->apellidos)) {  // Concatenamos nombres y apellidos
                                $nombreCompletoAsesor = $asesorRegistrante->nombres . ' ' . $asesorRegistrante->apellidos;
                                $errors[] = "Error: El cliente con este número de documento ya está registrado por "
                                    . $nombreCompletoAsesor . ".";
                            } else {
                                $errors[] = "Error: No se encontró el nombre completo del asesor en la base de datos.";
                            }
                        } else {
                            $errors[] = "Error: No se encontró el asesor relacionado al cliente.";
                        }
                    
                        DB::rollback();
                        return ['errores' => (object)$errors, 'estado' => false];
                    }
                    
                  
                      DB::rollback();
                      return ['errores' => (object)$errors, 'estado' => false];
                  }
                  
                  
            } else {
                // Si es una actualización, permitir solo si el cliente fue registrado por el mismo asesor
                $prospectoExistente = DB::table('prospecto')
                    ->join('clienteauto', 'prospecto.idCliente', '=', 'clienteauto.id')
                    ->where('prospecto.id', $id)
                    ->where('clienteauto.tipoDocumento', $request->get('tipodocumento'))
                    ->where('clienteauto.documento', $request->get('documento'))
                    ->where('clienteauto.idAsesorRegistra', Auth::user()->usuarioId)
                    ->first();

                if (!$prospectoExistente) {
                    // Verificar que $prospectoExistente no sea null antes de acceder a sus propiedades
                    $clienteAsesor = DB::table('clienteauto')
                        ->where('tipoDocumento', $request->get('tipodocumento'))
                        ->where('documento', $request->get('documento'))
                        ->first();

                    if ($clienteAsesor) {
                        $asesorRegistrante = DB::table('trabajador')
                            ->where('id', $clienteAsesor->idAsesorRegistra)
                            ->first();

                        $errors[] = "Error: El cliente con este número de documento ya está registrado por "
                            . $asesorRegistrante->nombre . ".";
                    } else {
                        $errors[] = "Error: No se encontró el cliente registrado con este número de documento.";
                    }

                    DB::rollback();
                    return ['errores' => (object)$errors, 'estado' => false];
                }
            }

            // Registrar o actualizar el cliente
            $clientFind = DB::table('clienteauto')
                ->where('documento', $request->get('documento'))
                ->where('tipoDocumento', $request->get('tipodocumento'))
                ->where('idAsesorRegistra', Auth::user()->usuarioId)
                ->first();

            if (is_null($clientFind)) {
                // Nuevo registro
                $cliente = new ClienteAuto;
                $cliente->tipoDocumento = $request->get('tipodocumento');
                $cliente->documento = $request->get('documento');
                $cliente->apellidos = $request->get('apellidos');
                $cliente->nombres = $request->get('nombres');
                $cliente->razonSocial = $request->get('razonSocial');
                $cliente->telefono = $request->get('telefono');
                $cliente->correoElectronico = $request->get('correo');
                $cliente->direccion = $request->get('direccion');
                $cliente->idAsesorRegistra = Auth::user()->usuarioId;
                $cliente->save();
                $idCliente = $cliente->id;    
            } else {
                // Actualizar el registro existente
                if (!is_null($clientFind->deleted_at)) {
                    DB::table('clienteauto')
                        ->where('id', $clientFind->id)
                        ->update(['deleted_at' => null, 'idPersonalEliminar' => null]);
                }

                $clienteUp = ClienteAuto::find($clientFind->id);
                $clienteUp->tipoDocumento = $request->get('tipodocumento');
                $clienteUp->documento = $request->get('documento');
                $clienteUp->apellidos = $request->get('apellidos');
                $clienteUp->nombres = $request->get('nombres');
                $clienteUp->razonSocial = $request->get('razonSocial');
                $clienteUp->telefono = $request->get('telefono');
                $clienteUp->correoElectronico = $request->get('correo');
                $clienteUp->direccion = $request->get('direccion');
                $clienteUp->idAsesorRegistra = Auth::user()->usuarioId;
                $clienteUp->update();
                $idCliente = $clientFind->id;
            }

            // Guardar prospecto
            if ($id == 0) {
                $prospecto = new Prospecto;
            } else {
                $prospecto = Prospecto::find($id);
            }
            $prospecto->idCliente = $idCliente;
            $prospecto->comentarios = $request->get('comentarios');
            $prospecto->idOrigen = $request->get('select_origen');
            $prospecto->idAsesorRegistra = Auth::user()->usuarioId;

            if ($id == 0) {
                $prospecto->save();
                $errors[] = 'Prospecto registrado correctamente.';
            } else {
                $prospecto->update();
                $errors[] = 'Prospecto actualizado correctamente.';
            }

        } catch (\Exception $ex) {
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }

        DB::commit();

        return ['errores' => (object)$errors, 'estado' => $band];
    }
}
    
	public function validar (Request $request) {
		$reglas = [
			'codproveedor' => 'required|max:20',
			'linea' => 'required|max:1',
            // 'tipo'=> 'required|string',
			'select_marca'=> 'required',
			'marca'  => ($request->get('select_marca')=='otro'?'required':'nullable'),
			'select_modelo'=> 'required',
			'modelo'  => ($request->get('select_modelo')=='otro'?'required':'nullable'),
			// 'modelo' => 'required',
			'anio' => 'required|numeric',
    		'version' => 'required|max:255|string',
			'transmision'=> 'required|max:255|string',
			// 'color' => 'required|string',
			// 'precio' => 'numeric|required',
			// 'ficha'	=> ($request->get('id')>0?'nullable':'required'),
			'descripcion' => 'required'
        ];

        $mensajes = [
			'codproveedor'	=> 'Indique Código de Proveedor',
			'codproveedor.max'  => 'Código del Proveedor debe tener como máximo 20 caracteres',
			'linea.required' => 'Indique Línea',
			'linea.max' => 'Línea debe tener como máximo 1 caracter',
			// 'tipo.required'=> 'Indique Tipo de Carrocería',
            'select_marca.required'=> 'Seleccione Marca',
			'marca.required'  => 'Indique Marca',
			'select_modelo.required'=> 'Seleccione Modelo',
			'modelo.required'  => 'Indique Modelo',
			// 'modelo.required' => 'Indique Modelo',
			'anio.required'  => 'Indique Año',
			'anio.numeric'  => 'Año debe ser numérico',
			'version.required'  => 'Indique Versión',
			'version.max'  => 'Versión debe tener como máximo 255 caracteres',
			'transmision.required'  => 'Indique Transmisión',
			'transmision.max'  => 'Transmisión debe tener como máximo 255 caracteres',
			// 'color.required'  => 'Indique Color',
			// 'precio.required'  => 'Indique Precio',
			// 'precio.numeric'  => 'Precio debe ser numérico',
			// 'ficha.required'	=> 'Indique Ficha Técnica del Auto',
			'descripcion.required'  => 'Indique Descripción'
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarProspecto (Request $request) {
		$reglas = [
			'tipodocumento' => 'required|max:3',
			'documento' => 'required|max:12',
			'nombres' => $request->get('tipodocumento') != 'RUC'?'required|max:255':'nullable',
			'apellidos' => $request->get('tipodocumento') != 'RUC'?'required|max:255':'nullable',
			'razonSocial' => $request->get('tipodocumento') == 'RUC'?'required|max:255':'nullable',
			'direccion' => 'required|max:255',
			// 'tiempo_estimado' => 'required',
			// 'idAuto' => 'required',
			// 'marcaId' => 'required',
			// 'modeloId' => 'required',
            'select_origen'=> 'required',
			'origen'  => ($request->get('select_origen')=='otro'?'required':'nullable'),
			// 'select_etiqueta'=> 'required',
			// 'etiqueta'  => ($request->get('select_etiqueta')=='otro'?'required':'nullable'),
        ];

        $mensajes = [
			'tipodocumento.required'	=> 'Indique Tipo Documento',
			'tipodocumento.max'  => 'Tipo Documento debe tener como máximo 3 caracteres',
			'nombres.required' => 'Indique Nombres',
			'nombres.max' => 'Nombres debe tener como máximo 255 caracteres',
			'apellidos.required' => 'Indique Apellidos',
			'apellidos.max' => 'Apellidos debe tener como máximo 255 caracteres',
			'razonSocial.required' => 'Indique Razon Social',
			'razonSocial.max' => 'Razon Social debe tener como máximo 255 caracteres',
			'direccion.required' => 'Indique Dirección',
			'direccion.max' => 'Dirección debe tener como máximo 255 caracteres',
			// 'tiempo_estimado.required' => 'Indique Tiempo estimado',
			// 'idAuto.required' => 'Indique Auto',
			// 'marcaId.required' => 'Indique Marca de Auto',
			// 'modeloId.required' => 'Indique Modelo de Auto',
			#// 'tipo.required'=> 'Indique Tipo de Carrocería',
            'select_origen.required'=> 'Seleccione Origen',
			'origen.required'  => 'Indique Origen',
			// 'select_etiqueta.required'=> 'Seleccione Etiqueta',
			// 'etiqueta.required'  => 'Indique Etiqueta',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarObsequio(Request $request) {
		$reglas = [
			'tipo' => 'required|max:1',
			'nombre' => 'required|max:255',
        ];

        $mensajes = [
			'tipo.required'	=> 'Indique Tipo',
			'tipo.max'  => 'Tipo debe tener como máximo 1 caracter',
			'nombre.required' => 'Indique Nombre',
			'nombre.max' => 'Nombre debe tener como máximo 255 caracteres',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarOportunidad(Request $request) {
		$reglas = [
			'id' => 'required',
			'cliente' => 'required',
			'linea' => 'required|max:1',
			'monto' => 'required|numeric',
			'moneda' => 'required|max:3',
			'certeza' => 'required|max:1',
			'fecha_cierre' => 'required',
			'fase' => 'required|max:1',
			'idAuto' => 'required',
			'marcaId' => 'required',
			'modeloId' => 'required',
			'anio' => 'required'
        ];

        $mensajes = [
			'id.required'	=> 'Indique ID',
			'cliente.required'  => 'Indique Cliente',
			'linea.required' => 'Indique Linea',
			'linea.max' => 'Linea debe tener como maximo 1 caracter',
			'monto.required' => 'Indique Monto',
			'moneda.required' => 'Indique Moneda',
			'moneda.max' => 'Moneda debe tener como máximo 3 caracteres',
			'certeza.required' => 'Indique Certeza',
			'certeza.max' => 'Certeza debe tener como máximo 1 caracter',
			'fecha_cierre.required' => 'Indique Fecha de Cierre',
			'fase.required' => 'Indique Fase',
			'fase.max' => 'Fase debe tener como máximo 1 caracter',
			'idAuto.required' => 'Indique Auto',
			'marcaId.required' => 'Indique Marca de Auto',
			'modeloId.required' => 'Indique Modelo de Auto',
			'anio.required' => 'Indique Año'
		];

        // $mensajes = [
		// 	'tipodocumento.required'	=> 'Indique Tipo Documento',
		// 	'tipodocumento.max'  => 'Tipo Documento debe tener como máximo 3 caracteres',
		// 	'nombres.required' => 'Indique Nombres',
		// 	'nombres.max' => 'Nombres debe tener como máximo 255 caracteres',
		// 	'apellidos.required' => 'Indique Apellidos',
		// 	'apellidos.max' => 'Apellidos debe tener como máximo 255 caracteres',
		// 	'razonSocial.required' => 'Indique Razon Social',
		// 	'razonSocial.max' => 'Razon Social debe tener como máximo 255 caracteres',
		// 	'direccion.required' => 'Indique Dirección',
		// 	'direccion.max' => 'Dirección debe tener como máximo 255 caracteres',
		// 	// 'tiempo_estimado.required' => 'Indique Tiempo estimado',
		// 		#// 'tipo.required'=> 'Indique Tipo de Carrocería',
        //     'select_origen.required'=> 'Seleccione Origen',
		// 	'origen.required'  => 'Indique Origen',
		// 	// 'select_etiqueta.required'=> 'Seleccione Etiqueta',
		// 	// 'etiqueta.required'  => 'Indique Etiqueta',
		// ];


        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarMovAsesor (Request $request) {
		$reglas = [
			'idAsesor' => 'required|numeric|min:1',
			'operacion' => 'required',
			'prospectoId' => 'required|numeric',
			'refIdAsesor' => 'required|numeric|min:1',
        ];

        $mensajes = [
			'idAsesor.required'	=> 'Indique Asesor',
			'idAsesor.min'	=> 'Indique Asesor',
			'operacion.required'  => 'Indique Operación',
			'prospectoId.required' => 'Indique ID de Prospecto',
			'refIdAsesor.required' => 'Indique Asesor Referenciado',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}
	########################################################
	#		OBSEQUIOS									   #
	########################################################

	public function getObsequios(Request $request) {
		$busqueda = $request->get('query');

		$obsequios = DB::table('adicionalobsequio as a')
					->select('a.id', 'a.nombre as obsequio')
					->where('a.tipo','O')
					->whereNull('deleted_at')
                    ->where('a.nombre', 'LIKE','%'.$busqueda.'%')
                    ->get();

        return ['obsequios' => $obsequios];
    }

	public function getAdicionales(Request $request) {
		$busqueda = $request->get('query');

		$adicionales = DB::table('adicionalobsequio as a')
					->select('a.id', 'a.nombre as adicional')
					->where('a.tipo','A')
					->whereNull('deleted_at')
                    ->where('a.nombre', 'LIKE','%'.$busqueda.'%')
                    ->get();

        return ['adicionales' => $adicionales];
    }
	
	public function guardarobsequio(Request $request) {	
		$errors = $this->validarObsequio($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				if ($id == 0) {
					$adicional = new AdicionalObsequio;
				} else {
					$adicional = AdicionalObsequio::find($id);
				}
				$adicional->tipo  = $request->get('tipo');
				$adicional->nombre = $request->get('nombre');
				$adicional->idPersonal = Auth::user()->usuarioId;

				if ($band) {
					if ($id == 0) {
						$adicional->save();
						$cad = ' Registrado';
					} else {
						$adicional->update();
						$cad = ' Actualizado';
					}
					
					$errors[] = ($adicional->tipo  =='O'?'Obsequio':'Adicional').' '.$cad.' Correctamente';
				}

			}catch(\Exception $ex){
				// $errors[] = $ex->getMessage();
				$errors[] = 'Obsequio/Adicional ya antes Registrado'; //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}

	}

	public function obtenerObsequio($id) {
		$obsequio = DB::table('adicionalobsequio')->where('id', $id)->first();

		return ['estado' => true, 'obsequio' => $obsequio];
	}

	public function getObsequiosAll (Request $request) {
		$nombre		 = $request->get('nombre');
		$tipoId	 = $request->get('tipo');
	
		$filas 		 = $request->get('filas');
		$page 		 = $request->get('page');
		

		$obsequios = DB::table('adicionalobsequio as a')
					->where('a.nombre','LIKE', '%'.$nombre.'%');


		if ($tipoId != '' && $tipoId != 'todo') {
			$obsequios = $obsequios->where('a.tipo','=',$tipoId);
		}

		
		
		$obsequios =  $obsequios->select('a.*', 
			DB::Raw("(CASE WHEN a.deleted_at IS NOT NULL THEN 'S' ELSE 'N' END) as eliminado"),
			DB::Raw("DATE_FORMAT(a.created_at,'%d/%m/%Y') as fechaRegistro"))
			->orderBy('a.id','ASC');

		$lista = $obsequios->get();
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
		
		$lista = $obsequios->offset(($page-1)*$filas)
					->limit($filas)
					->get();

		return ['obsequios' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Adicional/Obsequio':' Adicionales/Obsequios'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
		
	}

	public function guardarOportunidad(Request $request) {
		// dd($request);
		$errors = $this->validarOportunidad($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				
				if ($id == 0) {
					$oportunidad = new Oportunidad;
				} else {
					$oportunidad = Oportunidad::find($id);

					#CREANDO TAREA AUTOMATICA
					if ($oportunidad->fechaCierre < $request->get('fecha_cierre')) {
						$msje = new MensajeSistema;
						$msje->titulo = "SEGUIMIENTO DE CLIENTE";
						$msje->descripcion = "TAREA CREADA POR EL SISTEMA";
						$msje->idPersonal = Auth::user()->usuarioId;
						$msje->idPersonalMostrar = Auth::user()->usuarioId;
						$msje->fechaInicio = date('Y-m-d');
						$msje->fechaFin = $request->get('fecha_cierre');
						$msje->hora = date('H:i');
						
						$msje->idPersonalMostrar = Auth::user()->usuarioId;
						$msje->idOportunidad = $oportunidad->id;
						
						#OPORTUNIDAD
						if ($oportunidad->fase == 'C') {
							$oportunidad->fase = 'N';
							$oportunidad->update();
						}
						$msje->save();
					}
				}
				$oportunidad->cliente      =  $request->get('cliente');
				$oportunidad->concepto	   =  $request->get('concepto');
				$oportunidad->idProspecto  =  $request->get('prospectoId');
				$oportunidad->linea  =  $request->get('linea');
				$oportunidad->moneda  =  $request->get('moneda');
				$oportunidad->certeza  =  $request->get('certeza');
				$oportunidad->fase  =  $request->get('fase');
				$oportunidad->comentarios     = $request->get('comentarios');
				$oportunidad->fechaCierre  = $request->get('fecha_cierre');
				$oportunidad->monto  = $request->get('monto');	
				$oportunidad->obsequios     = $request->get('arrObsequios');
				$oportunidad->adicionales    = $request->get('arrAdicionales');
				$oportunidad->idAsesorRegistra = Auth::user()->usuarioId;
				

				$oportunidad->idMarcaAuto     = $request->get('marcaId');
				$oportunidad->idModeloAuto    = $request->get('modeloId');
				$oportunidad->idAuto      	  = $request->get('idAuto');
				$oportunidad->anioAuto 		  = $request->get('anio');
				$oportunidad->autoTexto       = $request->get('autoTexto');
				

				if ($band) {
					if ($id == 0) {
						$oportunidad->save();
						$cad = ' Registrada';
					} else {
						$oportunidad->update();
						$cad = ' Actualizada';
					}
					
					if ($id == 0) {
						#$prospecto = Prospecto::find($oportunidad->idProspecto);
						#if (!is_null($prospecto) && $prospecto->situacion == 'N') {
							#$prospecto->situacion = 'C';
							#$prospecto->update();

							$errors[] = 'Oportunidad '.$cad.' Correctamente';
						#} else {
						#	$band = false;
						#	$errors[] = 'Prospecto ya antes convertido a Oportunidad';
						#}
					} else {
						$errors[] = 'Oportunidad '.$cad.' Correctamente';
					}
					
				}

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			if  ($band) { 
				DB::commit();
			} else {
				DB::rollback();	
			}
			
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}
	
	public function buscarAsesor(Request $request) {
		$query = $request->get('query');
		$asesorId = $request->get('asesorId');
		$catAsesorId = 15;

        $personal = Personal::select('id', DB::Raw("CONCAT(dni, ' - ', apellidos,' ', nombres) as asesor"))
		->where('idCategoriaPersonal', $catAsesorId)
		->where('id','!=', $asesorId)
        ->where(DB::Raw("CONCAT(dni, ' - ', apellidos,' ', nombres)"),'LIKE', '%'.$query.'%')
        ->get();

        return ['asesores' => $personal];
	}

	public function guardarAsesor (Request $request) {
		#dd($request);
		$errors = $this->validarMovAsesor($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('prospectoId');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				$prospecto = Prospecto::find($id);
				if (!is_null($prospecto) &&  $request->get('idAsesor') != $prospecto->idAsesorRegistra) {
					if ($request->get('operacion') == 'A') {
						$prospecto->idAsesorRegistra = $request->get('idAsesor');
						$prospecto->update();
					} else {
						$newProspecto = new Prospecto();
						$newProspecto->idCliente = $prospecto->idCliente;
						$newProspecto->idOrigen = $prospecto->idOrigen;
						$newProspecto->idAsesorRegistra = $request->get('idAsesor');
						$newProspecto->comentarios = $prospecto->comentarios;
						$newProspecto->save();
					}
				} else {
					$band = false;
				}

			
				if ($band) {		
					$errors[] ='Prospecto '.($request->get('operacion') == 'A' ?'Asignado': 'Compartido').' Correctamente';
				}

			}catch(\Exception $ex){
				// $errors[] = $ex->getMessage();
				$errors[] = 'Prospecto no ubicado, intentelo nuevamente'; //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}
}
