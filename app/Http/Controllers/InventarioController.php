<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;

use App\Models\CategoriaProducto;
use App\Models\Guia;
use App\Models\DetalleGuia;
use App\Models\Lote;
use App\Models\LoteAuto;
use App\Models\TipoCambio;
use App\Models\StockProducto;
use App\Models\StockAuto;
use App\Models\StockProductoDetalle;
use App\Models\StockProductoDetalleSalida;

use App\Models\Producto;
use App\Models\Serie;
use Auth;
use Fpdf;
use App\Libraries\Funciones;
use App\Libraries\EnLetras;


use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;


use DB;
use Validator;

class InventarioController extends Controller
{
	public $almacenId = 2;
	public $tiendaId  = 1;
	public $tipoDocumentoGuia = 'GI';

	public $estilo_header = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => '4C3FB3',
			)           
		),
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri',
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
	);

	public $estilo_header_02 = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'F5F5F5',
			)           
		),
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '000000'),
			'size'  => 12,
			'name'  => 'Calibri',
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
	);

	public $estilo_content = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'E3E1EE',
			)           
		),
		'font'  => array(
			'color' => array('rgb' => '000000'),
			'size'  => 10,
			'name'  => 'Calibri',
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
	);

	public $estilo_content_danger = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => '961414',
			)           
		),
		'font'  => array(
			'color' => array('rgb' => 'f5f5f5'),
			'size'  => 10,
			'name'  => 'Calibri',
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
	);


	public function getAll (Request $request) {
    	$filtro 	 = $request->get('filtro');
    	$descripcion = $request->get('descripcion');
    	$departamentoId	 = $request->get('departamentoId');
  
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
                        ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
                        ->where('local.tipo','=','A');
                     
    	if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'codigo':
    				if ($descripcion <> '')	
						$locales = $locales->where('local.codRegistro','LIKE', $descripcion.'%');
    				break;
    			default:
    				if ($descripcion <> '')
                        $locales = $locales->where('local.direccion','LIKE', $descripcion.'%');
                    break;
    		}
    	}

    	if ($departamentoId != '' && $departamentoId != 'todo') {
    		$locales = $locales->where('local.idDepartamento','=',$departamentoId);
    	}
    	
    	$locales =  $locales->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'dep.nombre as departamento')
			   ->orderBy('local.codRegistro','ASC');

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
			$arrPag = [['opc' => '1', 'bloqueado'=> true]];
			$page = '1';
			$inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $locales->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();
		
        
    	return ['inventarios' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Almacén':' Almacenes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
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
		 				->leftjoin('relacionLocal as rel','rel.idAlmacen','=','local.id')
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

 			foreach ($dep_almacenes as $dep) {
 				foreach ($almacenes as $alm) {
 					if ($dep->idDepartamento == $alm->idDepartamento) {
 						if ($band == 0) {
 							$departamento = $dep;
 							$band = 1;
 						}
 						$arreglo[] = $alm;
 					}
 				}
 				$general[] = ['departamento' => $departamento, 'almacenes' => (object) $arreglo];
 				$band = 0;
 				$arreglo = [];
 			}


 			return ['arreglo' => (object)$general, 'local' => $local, 'estado' => true];
 			// $local_hab = RelacionLocal::where('idTienda','=',$id)
 			// 			 ->where('situacion','=','S')
 			// 			 ->select('idAlmacen')->orderBy('id')->get();
 		}else{
 			return ['url' => '/local', 'estado' => false];
 		}
	}	

	public function reporte (Request $request) {
		$almacenId 	 = $request->get('almacenId');
		$tipoId = $request->get('tipoId');
		$fechaI = $request->get('fechaI');
		$fechaF = $request->get('fechaF');
		
    	// $productoId	 = $request->get('productoId');
  
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
		
		$detalles = Guia::leftJoin('detalleguia as dg','dg.idGuia','=','guia.id')
					->leftJoin('producto as pr','pr.id','=','dg.idProducto')
					// ->leftJoin('categoriaproducto as cp','cp.id','=','pr.idCategoriaProducto')
					->leftJoin('tipodocumento as td','guia.idTipoGuia','=','td.id');

		if ($almacenId != '') {
			$detalles = $detalles->where('guia.idAlmacen','=',$almacenId);
		}

		if ($tipoId != '') {
			$detalles = $detalles->where('pr.tipoProducto','=',$tipoId);
		}

		if ($fechaI != '') {
			$detalles = $detalles->where('guia.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$detalles = $detalles->where('guia.fecha','<=',$fechaF);
		}


		$detalles = $detalles->select('dg.cantidad', DB::Raw("DATE_FORMAT(guia.fecha,'%d/%m/%Y') as fecha"), 
		DB::Raw("CONCAT(td.abreviatura,LPAD(guia.serie,3,'0'),'-',LPAD(guia.numero,8,'0')) as documento"),
		DB::Raw("(CASE WHEN td.abreviatura = 'GE' THEN 'ENTRADA' ELSE 'SALIDA' END) as tipo"),
		'dg.descripcion as detalles',
		DB::Raw("(CASE pr.tipoProducto 
		WHEN 'A'  THEN 'Accesorio/Repuesto' 
		WHEN 'LL' THEN 'Neumáticos' 
		WHEN 'I'  THEN 'Insumos' 
		WHEN 'B'  THEN 'Baterías' 
		ELSE 'MUELLES' END) as tipoProd"),'guia.id',DB::Raw("DATE_FORMAT(guia.created_at, '%d/%m/%Y') as fechaRegistro"))
		->orderBy('guia.fecha','DESC');

		
		$lista = $detalles->get();
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
		
		$lista = $detalles->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();
		
		return ['detalles' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Producto':' Productos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	}

	public function getAlmacenes (Request $request) {
		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
 				   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
 				   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
 				   ->where('local.tipo','=','A')
 				   ->select('local.id', 'local.direccion','dep.nombre as departamento')
				   ->orderBy('local.direccion','ASC')
				   ->get();

		return ['locales' => (object)$locales];
	}	

	public function getTiposDocumentosGuia(Request $request) {
		$locales = DB::table('tipodocumento as t')
					->where('t.tipoLocal', 'A')
					->whereNotIn('t.id', [1])
					->select('t.id', 't.nombre')
					->orderBy('t.nombre','ASC')
					->get();

		return ['locales' => (object)$locales];
	}

	public function guardar (Request $request) {
		$errors = $this->validar($request);
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$errors = [];
			DB::beginTransaction();
			$band = true;
			try{
				if ($band) {
					$fa = date('Y-m-d');
					$tipoCambio = TipoCambio::where(DB::Raw("DATE_FORMAT(fechaActualizacion, '%Y-%m-%d')"),'=',$fa)
						->select('factorCompra','factorVenta')
						->first();
					
					if (!is_null($tipoCambio)) {
						$guia = new Guia;
						$guia->idalmacen  = $this->almacenId;
						$guia->fecha = $request->get('fecha');
						$guia->idTipoGuia = ($request->get('tipoOperacion')=='E'?2:4);
						$guia->total = $request->get('totalDoc');
						$serie = Serie::where('idLocal','=',$guia->idalmacen)
								->where('tipoLocal','=','A')
								->where('tipoDocumento','=',$this->tipoDocumentoGuia)
								->first();
					
						$guia->serie = $serie->serie;
						$guia->numero = $serie->numero + 1;
						$guia->flete  = ($guia->idTipoGuia == 2?$request->get('flete'):null);
						$guia->observacion = $request->get('observacion');
						$guia->tipoMoneda  = $request->get('tipoMoneda');

						if ($guia->idTipoGuia == 2) {
							if ($guia->tipoMoneda == 'D') {
								$guia->tipoCambio =  $tipoCambio->factorVenta;
							}
						}
		
						$guia->idPersonal = Auth::user()->usuarioId;
						$guia->save();

						$id = $guia->id;
						$serie->numero = $guia->numero;
						$serie->update();
						
						if ($guia->idTipoGuia == 2) {
							$detalles = explode(',',$request->get('listProductos'));
						} else {
							$detalles = explode(',',$request->get('listDetalles'));
						}
						
						$i = 1;
						foreach ($detalles as $det) {
							$detalle = new DetalleGuia;
							$detalle->item = $i;
							$detalle->descripcion = $request->get('txtproducto'.$det);
							$detalle->cantidad = $request->get('txtcantidad'.$det);
								
							$detalle->subTotal = $request->get('txtsubtototal'.$det);
							$detalle->idProducto = $request->get('productoid'.$det);
							$detalle->idGuia = $id;
							
							if ($guia->idTipoGuia == 2) {
								$detalle->preciocompra = $request->get('txtprecio'.$det);
								$detalle->precioventa = $request->get('txtprecioventa'.$det);
							} else {
								$detalle->precioventa = $request->get('txtprecio'.$det);
							}
							$detalle->save();
							
							if ($guia->idTipoGuia == 2) {
								$lote = Lote::where('idProducto','=',$detalle->idProducto)
										->where('idAlmacen','=',$guia->idalmacen)
										->select('numero')
										->first();

								// dd($lote);
								$l = new Lote;
								$l->numero 	   = (!is_null($lote)?$lote->numero+1:1);
								$l->idProducto = $detalle->idProducto;
								$l->idGuiaCompra   = $detalle->idGuia;
								$l->idAlmacen  = $guia->idalmacen;
								$l->tipoMoneda = $guia->tipoMoneda;
								if ($l->tipoMoneda == 'S') {
									$l->precioSoles   = $detalle->precioventa;
									$l->precioDolares = round(($l->precioSoles/$tipoCambio->factorCompra),2);
								} else {
									$l->precioDolares = $detalle->precioventa;
									$l->precioSoles = round(($l->precioDolares * $tipoCambio->factorVenta),2);
								}
							
								// $l->precioSoles   = $detalle->precioventa;
								// $l->precioDolares = round(($l->precioSoles/$tipoCambio->factorCompra),2);
								$l->save();

								$s = new StockProductoDetalle;
								$s->precioSoles = $l->precioSoles;
								$s->stock = $detalle->cantidad;
								$s->idTienda = $this->tiendaId;
								$s->idAlmacenSalida = $this->almacenId;
								$s->idLote = $l->id;
								$s->idProducto = $l->idProducto;
								$s->save();
							} else {
								$s = StockProductoDetalle::where('idProducto', $detalle->idProducto)
									->where('idTienda', $this->tiendaId)
									->where('idAlmacenSalida', $this->almacenId)
									->where('idLote', $request->get('lote'.$det))
									->first();
								$acumDism = $detalle->cantidad;
								// $aux = 0;
								
								if (!is_null($s)) {
									if ($acumDism <= $s->stock) {
										$s->stock = $s->stock - $acumDism;
										$s->update();

										$spds = new StockProductoDetalleSalida;
										$spds->idStockProductoDetalle = $s->id;
										$spds->idProducto = $s->idProducto;
										$spds->idAlmacen = $s->idAlmacenSalida;
										$spds->idGuia = $guia->id;
										$spds->stock = $acumDism;
										$spds->save();
									} else {
										$band = false;
										$errors[] = "Producto $detalle->descripcion no cuenta con stock: $acumDism";
										break;
									}
									// if ($acum>)
								}
									// ->orderBy('created_at','ASC')
									// ->select('stock','id')
									// ->get();

								// $cantidad = $dp->cantidad;
								/* foreach ($s as $sd) {
									if ($acumDism > 0) {
										$a = StockProductoDetalle::find($sd->id);
										if ($acumDism > $a->stock) {
											$aux = $a->stock;
											$acumDism-=$a->stock;
											$a->stock = 0;
										} else {
											$aux = $acumDism;
											$a->stock = $a->stock - $acumDism;
											$acumDism=0;
										}

										$a->update();
										$spds = new StockProductoDetalleSalida;
										$spds->idStockProductoDetalle = $a->id;
										$spds->idProducto = $a->idProducto;
										$spds->idAlmacen = $a->idAlmacenSalida;
										$spds->idGuia = $guia->id;
										$spds->stock = $aux;
										$spds->save();
									}
								} */
							}

							if ($band) {
								$sp = StockProducto::where('idAlmacen','=',$guia->idalmacen)
									->where('idProducto','=',$detalle->idProducto)
									->first();
								if ($guia->idTipoGuia == 2) {
									$sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
								} else {
									$sp->totalIncidencias = $sp->totalIncidencias + $detalle->cantidad;
								}

								$sp->update();
							}
							$i++;
						}
						$errors[] = 'Movimiento de Almacén Registrado Correctamente';
					} else {
						$errors[] = 'No se ha indicado Tipo de Cambio, Regístrelo por favor.';
						$band = false;
					}
					
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
		// dd($errors);
	}

	public function guardarAuto (Request $request) {
		$errors = $this->validarAuto($request);
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$errors = [];
			DB::beginTransaction();
			$band = true;
			try{
				if ($band) {
					$fa = date('Y-m-d');
					$tipoCambio = TipoCambio::where(DB::Raw("DATE_FORMAT(fechaActualizacion, '%Y-%m-%d')"),'=',$fa)
						->select('factorCompra','factorVenta')
						->first();
					
					if (!is_null($tipoCambio)) {
						$guia = new Guia;
						$guia->idalmacen  = $this->almacenId;
						$guia->fecha = $request->get('fecha');
						$guia->idTipoGuia = ($request->get('tipoOperacion')=='E'?2:4);
						$guia->total = $request->get('totalDoc');
						$serie = Serie::where('idLocal','=',$guia->idalmacen)
								->where('tipoLocal','=','A')
								->where('tipoDocumento','=',$this->tipoDocumentoGuia)
								->first();
					
						$guia->serie = $serie->serie;
						$guia->numero = $serie->numero + 1;
						$guia->flete  = ($guia->idTipoGuia == 2?$request->get('flete'):null);
						$guia->observacion = $request->get('observacion');
						$guia->tipoMoneda  = $request->get('tipoMoneda');
						$guia->idPersonal = Auth::user()->usuarioId;

						if ($guia->idTipoGuia == 2) {
							if ($guia->tipoMoneda == 'D') {
								$guia->tipoCambio =  $tipoCambio->factorVenta;
							}
						}
						$guia->save();

						$id = $guia->id;
						$serie->numero = $guia->numero;
						$serie->update();
						
						if ($guia->idTipoGuia == 2) {
							$detalles = explode(',',$request->get('listAutos'));
						} else {
							$detalles = explode(',',$request->get('listDetalles'));
						}
						
						$i = 1;
						foreach ($detalles as $det) {
							$detalle = new DetalleGuia;
							$detalle->item = $i;
							$detalle->descripcion = $request->get('txtproducto'.$det);
							$detalle->cantidad = $request->get('txtcantidad'.$det);
								
							$detalle->subTotal = $request->get('txtsubtototal'.$det);
							$detalle->idAuto = $request->get('productoid'.$det);
							$detalle->idGuia = $id;
							
							if ($guia->idTipoGuia == 2) {
								$detalle->preciocompra = $request->get('txtprecio'.$det);
								$detalle->precioventa = $request->get('txtprecioventa'.$det);
								$detalle->descripcionadicional = $request->get('descripcion_adic'.$det);
							} else {
								$detalle->precioventa = $request->get('txtprecio'.$det);
							}
							$detalle->save();
							
							if ($guia->idTipoGuia == 2) {
								$lote = LoteAuto::where('idAuto','=',$detalle->idAuto)
										->where('idAlmacen','=',$guia->idalmacen)
										->select('numero')
										->first();

								// dd($lote);
								$l = new LoteAuto;
								$l->numero 	   = (!is_null($lote)?$lote->numero+1:1);
								$l->idAuto = $detalle->idAuto;
								$l->idGuiaCompra   = $detalle->idGuia;
								$l->idAlmacen  = $guia->idalmacen;
								$l->tipoMoneda = $guia->tipoMoneda;
								$l->descripcionadicional    = $request->get('descripcion_adic'.$det);
								// $l->precioSoles   = $detalle->precioventa;
								// $l->precioDolares = round(($l->precioSoles/$tipoCambio->factorCompra),2);
								if ($l->tipoMoneda == 'S') {
									$l->precioSoles   = $detalle->precioventa;
									$l->precioDolares = round(($l->precioSoles/$tipoCambio->factorCompra),2);
								} else {
									$l->precioDolares = $detalle->precioventa;
									$l->precioSoles = round(($l->precioDolares * $tipoCambio->factorVenta),2);
								}
							
								$l->save();

								$s = new StockProductoDetalle;
								$s->precioSoles = $l->precioSoles;
								$s->tipo = 'A';
								$s->stock = $detalle->cantidad;
								$s->idTienda = $this->tiendaId;
								$s->idAlmacenSalida = $this->almacenId;
								$s->idLoteAuto = $l->id;
								$s->idAuto = $l->idAuto;
								$s->save();
							} else {
								$s = StockProductoDetalle::where('idAuto', $detalle->idAuto)
									->where('idTienda', $this->tiendaId)
									->where('idAlmacenSalida', $this->almacenId)
									->where('idLoteAuto', $request->get('lote'.$det))
									->first();
								$acumDism = $detalle->cantidad;
								// $aux = 0;
								
								if (!is_null($s)) {
									if ($acumDism <= $s->stock) {
										$s->stock = $s->stock - $acumDism;
										$s->update();

										$spds = new StockProductoDetalleSalida;
										$spds->idStockProductoDetalle = $s->id;
										$spds->idAuto = $s->idAuto;
										$spds->idAlmacen = $s->idAlmacenSalida;
										$spds->idGuia = $guia->id;
										$spds->stock = $acumDism;
										$spds->save();
									} else {
										$band = false;
										$errors[] = "Auto $detalle->descripcion no cuenta con stock: $acumDism";
										break;
									}
									// if ($acum>)
								}
									// ->orderBy('created_at','ASC')
									// ->select('stock','id')
									// ->get();

								// $cantidad = $dp->cantidad;
								/* foreach ($s as $sd) {
									if ($acumDism > 0) {
										$a = StockProductoDetalle::find($sd->id);
										if ($acumDism > $a->stock) {
											$aux = $a->stock;
											$acumDism-=$a->stock;
											$a->stock = 0;
										} else {
											$aux = $acumDism;
											$a->stock = $a->stock - $acumDism;
											$acumDism=0;
										}

										$a->update();
										$spds = new StockProductoDetalleSalida;
										$spds->idStockProductoDetalle = $a->id;
										$spds->idProducto = $a->idProducto;
										$spds->idAlmacen = $a->idAlmacenSalida;
										$spds->idGuia = $guia->id;
										$spds->stock = $aux;
										$spds->save();
									}
								} */
							}

							if ($band) {
								$sp = StockAuto::where('idAlmacen','=',$guia->idalmacen)
									->where('idAuto','=',$detalle->idAuto)
									->first();
								if ($guia->idTipoGuia == 2) {
									$sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
								} else {
									$sp->totalIncidencias = $sp->totalIncidencias + $detalle->cantidad;
								}

								$sp->update();
							}
							$i++;
						}
						$errors[] = 'Movimiento de Almacén Registrado Correctamente';
					} else {
						$errors[] = 'No se ha indicado Tipo de Cambio, Regístrelo por favor.';
						$band = false;
					}
					
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
		// dd($errors);
	}

	public function validar(Request $request) {
		$reglas = [
            'listProductos'	=> 'required',
            'subtotalDoc' 	=> 'required|numeric',
            'igvDoc'      	=> 'required|numeric',
			'totalDoc'    	=> 'required|numeric',
			'tipoOperacion' => 'required',
			'fecha'		    => 'required',
			'tipoMoneda'	=> 'required',
			'observacion'	=> 'required',
        ];

        $mensajes = [
            'listProductos.required'=> 'Indique Detalles para la Operación',
			'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
		    'fecha.required'=> 'Indique Fecha',
			'tipoMoneda.required'	=> 'Indique Tipo de Moneda',
			'observacion.required' => 'Indique Observación'
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarAuto (Request $request) {
		$reglas = [
            'listAutos'	=> 'required',
            'subtotalDoc' 	=> 'required|numeric',
            'igvDoc'      	=> 'required|numeric',
			'totalDoc'    	=> 'required|numeric',
			'tipoOperacion' => 'required',
			'fecha'		    => 'required',
			'tipoMoneda'	=> 'required'
        ];

        $mensajes = [
            'listAutos.required'=> 'Indique Detalles para la Operación',
			'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
		    'fecha.required'=> 'Indique Fecha',
			'tipoMoneda.required'	=> 'Indique Tipo de Moneda',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function getDetalles($id, Request $request) {
		$detalles = Guia::leftJoin('detalleguia as det','det.idGuia','=','guia.id')
					->where('guia.id','=',$id)
					->select('det.cantidad','det.descripcion',DB::Raw("FORMAT(det.preciocompra,2) as preciocompra"),
					DB::Raw("FORMAT(det.precioventa,2) as precioventa"), DB::Raw("FORMAT(det.subTotal,2) as subTotal"), 
					'det.item','det.id','guia.total','guia.tipoMoneda')
					->orderBy('det.id','ASC')
					->get();

		$total = 0;
		$moneda = '';	
		if (count($detalles)) {
			$total = $detalles[0]->total;
			$moneda = $detalles[0]->tipoMoneda;
		}

    	return ['detalles' => $detalles,'total' => $total, 'tipo' => $moneda];
	}

	public function eliminarGuia (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$guia = Guia::find($id);
			$detalles = DetalleGuia::where('idGuia','=',$guia->id)->select('id')->get();
			foreach ($detalles as $det) {
				$d = DetalleGuia::find($det->id);
				$sp = StockProducto::where('idAlmacen','=',$guia->idAlmacen)
						->where('idProducto','=',$d->idProducto)
						->first();
				if ($guia->idTipoGuia == 2) {
					$sp->totalCompras = $sp->totalCompras - $d->cantidad;
					$lote = Lote::where('idProducto','=',$d->idProducto)
						->where('idAlmacen','=',$guia->idAlmacen)
						->where('idGuiaCompra','=',$guia->id)
						->first();
			
					$lote->situacion = 'A';
					$lote->update();
					$lote->delete();
				} else {
					$sp->totalIncidencias = $sp->totalIncidencias - $d->cantidad;
					
					$s = StockProductoDetalleSalida::where('idProducto','=',$d->idProducto)
						->where('idAlmacen','=',$this->almacenId)
						->where('idGuia','=',$guia->id)
						->orderBy('id','ASC')
						->select('id','stock','idStockProductoDetalle')
						->get();

					foreach ($s as $sd) {
						$u  = StockProductoDetalleSalida::find($sd->id);
						$a = StockProductoDetalle::find($sd->idStockProductoDetalle);
						$a->stock = $a->stock + $sd->stock;
						$a->update();
						$u->delete();
					}
				}
				$sp->update();
				$d->delete();
			}
			$guia->idPersonalEliminar = Auth::user()->usuarioId;
			$guia->update();
			$guia->delete();
			$errors[] = 'Guía Eliminada Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	

	}

	public function getReporteES(Request $request) {
		$almacenId 	 = $request->get('almacenId');
		$tipoId 	 = $request->get('tipoId');
		$producto 	 = $request->get('producto');

    	// $productoId	 = $request->get('productoId');
  
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
		
		$detalles = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
				   ->leftjoin('tipoproducto as tp','tp.id','=','producto.idMargenGanancia')
				   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->where('sp.idAlmacen','=',$almacenId)
				   ->where(function ($qq) use ($producto) {
						$qq->where('ma.nombre','LIKE','%'.$producto.'%')
						->orwhere('mt.nombre','LIKE','%'.$producto.'%')
						->orwhere('producto.nombre','LIKE','%'.$producto.'%')
						->orwhere('producto.tipoLlanta','LIKE','%'.$producto.'%')
						->orwhere('producto.medida','LIKE','%'.$producto.'%')
						->orwhere('ml.nombre','LIKE','%'.$producto.'%');
					});

				if ($tipoId != '') {
					$detalles = $detalles->where('producto.tipoProducto',$tipoId);
				}			
				$detalles = $detalles->select('producto.id', 
					DB::Raw("(CASE producto.tipoProducto 
							  WHEN 'A'  THEN 'Accesorio/Repuesto' 
							  WHEN 'LL' THEN 'Neumáticos' 
							  WHEN 'I'  THEN 'Insumos' 
							  WHEN 'B'  THEN 'Baterías' 
							  ELSE 'MUELLES' END) as tipoProducto"),
					DB::Raw("CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre"), DB::Raw("(CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2"),
					DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"), DB::Raw("FORMAT(producto.precio,2) as precio"),
					DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
					DB::Raw("'Producto' as tipo"),
					// DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					DB::Raw("FORMAT(sp.totalCompras - sp.totalVentas - sp.totalIncidencias,2) as stock"),
					'tp.porcentaje');
		
		$lista = $detalles->get();
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
		
		$lista = $detalles->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();
		
		return ['detalles' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Producto':' Productos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];

          
	}

	public function getMovimientosES(Request $request) {
		$idalmacen 	 = $request->get('idalmacen');
		$idprod 	 = $request->get('idprod');
	
		/* $detCompras = DB::table('compra as c')
					->join('detallecompra as dc','dc.idCompra','=','c.id')
					->join('persona as prov','prov.id','=','c.idProveedor')
					->where('c.idalmacen',$idalmacen)
					->where('dc.idProducto',$idprod)
					->select('dc.cantidad', DB::Raw("'ENTRADA' as movimiento"),
							DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha"),
							DB::Raw("DATE_FORMAT(dc.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
							DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
							DB::Raw("(CASE WHEN dc.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
							DB::Raw("(CASE c.tipoDocumento WHEN 'F' THEN 'FACTURA' ELSE 'BOLETA' END) as tipoDoc"),
							DB::Raw("CONCAT(c.tipoMoneda,'-',dc.preciocompra) as det_compra"),
							DB::Raw("NULL as det_guia"),
							DB::Raw("CONCAT(c.tipoDocumento,c.documento) as documento"), 
							'dc.created_at'
					);
		$detGuiasI = DB::table('guia as g')
					->join('detalleguia as dg','dg.idGuia','=','g.id')
					->where('g.idAlmacen',$idalmacen)
					->where('dg.idProducto',$idprod)
					->where('g.idTipoGuia',2)
					->select('dg.cantidad', 
						DB::Raw("(CASE g.idTipoGuia WHEN 2 THEN 'ENTRADA' ELSE 'SALIDA' END) as movimiento"),
						DB::Raw("DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dg.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("'-' as persona"),
						DB::Raw("(CASE WHEN dg.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'GUIA DE INGRESO' as tipoDoc"),
						DB::Raw("NULL as det_compra"),
						DB::Raw("CONCAT(g.tipoMoneda,'-',dg.preciocompra) as det_guia"),
						DB::Raw("CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = g.idTipoGuia),LPAD(g.serie,2,'0'),'-',LPAD(g.numero,8,'0')) as documento"),
						'dg.created_at'
					);

		$detGuiasAux = DB::table('guia as g')
					->join('detalleguia as dg','dg.idGuia','=','g.id')
					->join('stockproductodetallesalida as spds','spds.idGuia','=','g.id')
					->whereRaw('spds.idProducto = dg.idProducto')
					->where('g.idAlmacen',$idalmacen)
					->where('dg.idProducto',$idprod)
					->where('g.idTipoGuia',4)
					->whereNotNull('det_compra')
					->select('dg.cantidad', 
						DB::Raw("(CASE g.idTipoGuia WHEN 2 THEN 'ENTRADA' ELSE 'SALIDA' END) as movimiento"),
						DB::Raw("DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dg.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("'-' as persona"),
						DB::Raw("(CASE WHEN dg.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'GUIA DE EGRESO' as tipoDoc"),
						DB::Raw("(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) as det FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
						JOIN lote as l ON l.idCompra = c.id JOIN stockproductodetalle spd ON spd.idProducto = l.idProducto WHERE 
						dc.idProducto = l.idProducto AND spd.idLote = l.id AND l.idProducto = dg.idProducto 
						AND spds.idStockProductoDetalle = spd.id AND dc.id IS NOT NULL LIMIT 1 UNION ALL
                        SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) as det FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
						JOIN lote as l ON l.idGuiaCompra = g.id JOIN stockproductodetalle spd ON spd.idProducto = l.idProducto WHERE 
						dg.idProducto = l.idProducto AND spd.idLote = l.id AND l.idProducto = dg.idProducto 
						AND spds.idStockProductoDetalle = spd.id AND dg.id IS NOT NULL LIMIT 1) as det_compra"),
						DB::Raw("NULL as det_guia"),
						DB::Raw("CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = g.idTipoGuia),LPAD(g.serie,2,'0'),'-',LPAD(g.numero,8,'0')) as documento"),
						'dg.created_at'
					);
		
		*/
		/* $detOrdenes = DB::table('cotizacion as cot')
					->join('detallecotizacion as dc','dc.idCotizacion','=','cot.id')
					->join('persona as prov','prov.id','=','cot.idCliente')
					->where('cot.idAlmacenSalida',$idalmacen)
					->where('dc.idProducto',$idprod)
					->where('cot.situacion','U')
					->select('dc.cantidad', DB::Raw("'SALIDA' as movimiento"),
						DB::Raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dc.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
						DB::Raw("(CASE WHEN dc.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'COTIZACION' as tipoDoc"),
						DB::Raw("CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = 8),LPAD(cot.serie,3,'0'),'-',LPAD(cot.numero,8,'0')) as documento"),
						'dc.created_at'
					);
		*/ 
		/*$detOrdenes = DB::table('ordentrabajo as ord')
					->join('detalleordentrabajo as do','do.idOrdenTrabajo','=','ord.id')
					->join('detallecotizacion as dc','dc.idCotizacion','=','do.idCotizacion')
					->join('persona as prov','prov.id','=','ord.idCliente')
					->where('ord.idAlmacenSalida',$idalmacen)
					->where('dc.idProducto',$idprod)
					->select('dc.cantidad', DB::Raw("'SALIDA' as movimiento"),
						DB::Raw("DATE_FORMAT(ord.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(do.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
						DB::Raw("(CASE WHEN dc.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'ORDEN DE TRABAJO' as tipoDoc"),
						DB::Raw("(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
						JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
						l.id = dc.idLote AND l.idProducto = dc.idProducto) as det_compra"),
						DB::Raw("(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
						JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
						l.id = dc.idLote AND l.idProducto = dc.idProducto) as det_guia"),
						DB::Raw("CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = 10),LPAD(ord.serie,2,'0'),'-',LPAD(ord.numero,8,'0')) as documento"),
						'do.created_at'
				);
			
		$detVentas = DB::table('venta as vt')
					->join('detalleventa as dv','dv.idVenta','=','vt.id')
					->join('persona as prov','prov.id','=','vt.idCliente')
					->join('pagodetalle as pd','pd.idDetalleVenta','=','dv.id')
					->where('vt.idAlmacenSalida',$idalmacen)
					->where('dv.idProducto',$idprod)
					->whereNull('pd.idCotizacion')
					->whereNull('pd.idOrden')
					->select('dv.cantidad', DB::Raw("'SALIDA' as movimiento"),
						DB::Raw("DATE_FORMAT(vt.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dv.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
						DB::Raw("(CASE WHEN dv.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'VENTA' as tipoDoc"),
						DB::Raw("(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
						JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
						l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra"),
						DB::Raw("(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
						JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
						l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia"),
						DB::Raw("CONCAT(vt.tipoComprobante,LPAD(vt.serie,3,'0'),'-',LPAD(vt.numero,8,'0')) as documento"),
						'dv.created_at'
					);
		
		$detCotizacion = DB::table('venta as vt')
					->join('detalleventa as dv','dv.idVenta','=','vt.id')
					->join('persona as prov','prov.id','=','vt.idCliente')
					->join('pagodetalle as pd','pd.idDetalleVenta','=','dv.id')
					->join('detallecotizacion as dcot','dcot.idCotizacion','=','pd.idCotizacion')
					->whereRaw('dcot.idProducto = dv.idProducto')
					->where('vt.idAlmacenSalida',$idalmacen)
					->where('dv.idProducto',$idprod)
					->whereNotNull('pd.idCotizacion')
					->whereNull('pd.idOrden')
					->select('dv.cantidad', DB::Raw("'SALIDA' as movimiento"),
						DB::Raw("DATE_FORMAT(vt.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dv.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
						DB::Raw("(CASE WHEN dv.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("'VENTA' as tipoDoc"),
						DB::Raw("(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
						JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
						l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_compra"),
						DB::Raw("(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
						JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
						l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_guia"),
						DB::Raw("CONCAT(vt.tipoComprobante,LPAD(vt.serie,3,'0'),'-',LPAD(vt.numero,8,'0')) as documento"),
						'dv.created_at'
					);
		
		$detNotasCompras = DB::table('anulacionnotas as an')
					->join('detalleanulacionnotas as dan','dan.idAnulacion','=','an.id')
					->join('compra as c','c.id','an.idCompra')
					->join('detallecompra as dc','dc.idCompra','=','c.id')
					->join('persona as prov','prov.id','=','c.idProveedor')
					->whereRaw('dan.idProducto = dc.idProducto')
					->where('c.idalmacen',$idalmacen)
					->where('dc.idProducto',$idprod)
					->select('dc.cantidad', DB::Raw("'SALIDA' as movimiento"),
							DB::Raw("DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha"),
							DB::Raw("DATE_FORMAT(dan.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
							DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
							DB::Raw("(CASE WHEN dan.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
							DB::Raw("(CASE an.tipoNota WHEN 'C' THEN 'NOTA DE CRÉDITO' ELSE 'NOTA DE DÉBITO' END) as tipoDoc"),
							DB::Raw("CONCAT(c.tipoMoneda,'-',dc.preciocompra) as det_compra"),
							DB::Raw("NULL as det_guia"),
							DB::Raw("an.documentoCompra as documento"), 
							'dan.created_at'
					);

		$detNotasVentas = DB::table('anulacionnotas as an')
					->join('detalleanulacionnotas as dan','dan.idAnulacion','=','an.id')
					->join('venta as vt','vt.id','=','an.idVenta')
					->join('detalleventa as dv','dv.idVenta','=','vt.id')
					->join('persona as prov','prov.id','=','vt.idCliente')
					->whereRaw('dan.idProducto = dv.idProducto')
					->where('vt.idAlmacenSalida',$idalmacen)
					->where('dv.idProducto',$idprod)
					->select('dv.cantidad', DB::Raw("'ENTRADA' as movimiento"),
						DB::Raw("DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha"),
						DB::Raw("DATE_FORMAT(dan.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg"),
						DB::Raw("(CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona"),
						DB::Raw("(CASE WHEN dan.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado"),
						DB::Raw("(CASE an.tipoNota WHEN 'C' THEN 'NOTA DE CRÉDITO' ELSE 'NOTA DE DÉBITO' END) as tipoDoc"),
						DB::Raw("(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
						JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
						l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra"),
						DB::Raw("(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
						JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
						l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia"),
						DB::Raw("CONCAT(vt.tipoComprobante,an.tipoNota, LPAD(an.serie,2,'0'),'-',LPAD(an.numero,8,'0')) as documento"),
						'dan.created_at'
					);
	

		$query    = $detVentas->unionAll($detCompras)
					->unionAll($detOrdenes)
					->unionAll($detGuiasI)
					->unionAll($detGuiasE)
					->unionAll($detCotizacion)
					->unionAll($detNotasCompras)
					->unionAll($detNotasVentas);

		$querySql	= $query->toSql();
		// $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

		$lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))->mergeBindings($query);
		// $l = DB::query($lista);
		$lista = $lista->get()->toArray();
		*/

		$lista = DB::select("CALL reporteHistorialProducto(?,?)",[$idalmacen, $idprod]);

		$stockP = StockProducto::where('idProducto',$idprod)->where('idAlmacen',$idalmacen)->first();
		$local = Local::where('id',$idalmacen)->first();

		$stock = 0;
		if (!is_null($stockP)) {
			$stock = number_format($stockP->totalCompras - $stockP->totalVentas - $stockP->totalIncidencias,2);
		}
		$direccion = '';
		if (!is_null($local)) {
			$direccion = $local->direccion;
		}

		return ['detalles' => $lista, 'stock' => $stock, 'direccion' => $direccion];

	}

	public function excelReporte(Request $request) {
		$response = $this->getMovimientosES($request);
		$producto = $request->get('prod');
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','REPORTE DE ENTRADAS/SALIDAS');
		$hoja1->mergeCells('A1:H1');
		$hoja1->getStyle('A1:H1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','PRODUCTO:');
		$hoja1->setCellValue('B2',strtoupper($producto));
	
		$hoja1->setCellValue('A3','ALMACÉN:');
		$hoja1->setCellValue('B3', ($response['direccion']));
	
		$hoja1->setCellValue('D3','STOCK ACTUAL:');
		$hoja1->setCellValue('E3', strtoupper($response['stock']));

		$hoja1->getStyle('A2:B2')->applyFromArray($this->estilo_header_02);
		$hoja1->getStyle('A3:B3')->applyFromArray($this->estilo_header_02);
		$hoja1->getStyle('D3:E3')->applyFromArray($this->estilo_header_02);
	
		$hoja1->setCellValue('A5','#');
		$hoja1->setCellValue('B5','FECHA');
		$hoja1->setCellValue('C5','T. DOCUMENTO');
		$hoja1->setCellValue('D5','REFERENCIA');
		$hoja1->setCellValue('E5','ENTRADA');
		$hoja1->setCellValue('F5','SALIDA');
		$hoja1->setCellValue('G5','CLIENTE/PROVEEDOR');
		$hoja1->setCellValue('H5','COSTO');
		$hoja1->setCellValue('I5','MONEDA COSTO');
		$hoja1->setCellValue('J5','REGISTRADO');

		$hoja1->getStyle('A5:J5')->applyFromArray($this->estilo_header);
		$j = 6;
		$acum = 1;
		foreach ($response['detalles'] as $value) {
			$hoja1->setCellValue('A'.$j,$acum);
			$hoja1->setCellValue('B'.$j,$value->fecha);
			$hoja1->setCellValue('C'.$j,$value->tipoDoc);
			$hoja1->setCellValue('D'.$j,$value->documento);
			$hoja1->setCellValue('E'.$j,($value->movimiento=='ENTRADA'?$value->cantidad:''));
			$hoja1->setCellValue('F'.$j,($value->movimiento=='SALIDA'?$value->cantidad:''));
			$hoja1->setCellValue('G'.$j,$value->persona);
			$hoja1->setCellValue('H'.$j,$this->mostrarParam($value,1));
			$hoja1->setCellValue('I'.$j,$this->mostrarParam($value,0)=='D'?'DÓLARES':'SOLES');
			$hoja1->setCellValue('J'.$j,$value->fechaReg);
			
			if ($value->estado == 'E') {
				$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content_danger);
			} else {
				$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
			}
			$j++;
			$acum++;
		}

		foreach(range('A','J') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="reporteES.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function mostrarParam ($obj, $inc) {
		$param = '';
		if (!is_null($obj->det_compra)) {
			$param = $obj->det_compra;
		}
		if (!is_null($obj->det_guia)) {
			$param = $obj->det_guia;
		}
		
		$valor = explode('-', $param);
		
		return $valor[$inc];
	}

	public function excel($id)
	{	
		$local = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
		   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
		   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
		   ->where('local.tipo','=','A')
		   ->where('local.id','=',$id)
		   ->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'dep.nombre as departamento')
		   ->orderBy('local.codRegistro','ASC')
		   ->first();

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','INVENTARIO DE ALMACEN UBICADO EN: '.strtoupper($local->direccion));
		$hoja1->mergeCells('A1:M1');
		$hoja1->getStyle('A1:M1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','PRODUCTO');
		$hoja1->setCellValue('J2','MOVIMIENTOS DE STOCK');

		
		$hoja1->setCellValue('A3','TIPO DE PRODUCTO');
		$hoja1->setCellValue('B3','CÓDIGO DE PROVEEDOR');
		$hoja1->setCellValue('C3','NOMBRE');
		$hoja1->setCellValue('D3','MARCA');
		$hoja1->setCellValue('E3','MODELO');
		$hoja1->setCellValue('F3','SISTEMA DE AUTO');
		$hoja1->setCellValue('G3','MARCA DE AUTO');
		$hoja1->setCellValue('H3','TIPO DE NEUMÁTICO');
		$hoja1->setCellValue('I3','MEDIDA');
	
		$hoja1->setCellValue('J3','COMPRAS');
		// $hoja1->setCellValue('J3','TRANSLADOS');
		$hoja1->setCellValue('K3','VENTAS');
		$hoja1->setCellValue('L3','INCIDENCIAS');
		$hoja1->setCellValue('M3','ACTUAL');
	
		$hoja1->mergeCells('A2:I2');	
		$hoja1->mergeCells('J2:M2');

		$hoja1->getStyle('A2:I2')->applyFromArray($this->estilo_header);
		$hoja1->getStyle('J2:M2')->applyFromArray($this->estilo_header);
		
		$hoja1->getStyle('A3:M3')->applyFromArray($this->estilo_header);

		$j = 4;
		$productos = DB::table('producto')
				->leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
				->leftjoin('stockproducto as st','st.idProducto','=','producto.id')
				->where('st.idAlmacen','=',$id)
				->whereNull('producto.deleted_at')
				->select('producto.id',DB::Raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),
				DB::Raw("(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ma.nombre ELSE (CASE WHEN producto.idMarcaBateria IS NOT NULL THEN mb.nombre ELSE '-' END) END) END) as marca"),
				DB::Raw("(CASE WHEN producto.idMarcaAuto IS NOT NULL THEN mt.nombre ELSE '-' END) as marcaauto"),
				DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL AND producto.idModeloBateria IS NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL AND producto.idModeloBateria IS NULL THEN producto.modelo ELSE (CASE WHEN producto.idModeloBateria IS NOT NULL THEN modb.nombre ELSE '-' END) END) END) as modelo"),
				DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"),
				DB::Raw("FORMAT(producto.precio,2) as precio"), DB::Raw("FORMAT(producto.stockMinimo,2) as stockMinimo"), 
				DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"), 
				DB::Raw("(CASE producto.tipoProducto 
				WHEN 'A'  THEN 'Accesorio/Repuesto' 
				WHEN 'LL' THEN 'Neumáticos' 
				WHEN 'I'  THEN 'Insumos' 
				WHEN 'B'  THEN 'Baterías' 
				ELSE 'MUELLES' END) as tipoProducto"), 
				DB::Raw("(CASE WHEN producto.codProveedor IS NULL THEN '-' ELSE producto.codProveedor END) as codProveedor"),DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"), DB::Raw("(CASE WHEN producto.deleted_at IS NOT NULL THEN DATE_FORMAT(producto.deleted_at,'%d/%m/%Y %H:%i') ELSE '-' END) as deleted_at"),'st.*')
				->orderBy('nombre','ASC')
				->get();

		foreach ($productos as $value) {
			$hoja1->setCellValue('A'.$j,$value->tipoProducto);
			$hoja1->setCellValue('B'.$j,$value->codProveedor);
			$hoja1->setCellValue('C'.$j,$value->nombre);
			$hoja1->setCellValue('D'.$j,$value->marca);
			$hoja1->setCellValue('E'.$j,$value->modelo);
			$hoja1->setCellValue('F'.$j,$value->sistema);
			$hoja1->setCellValue('G'.$j,$value->marcaauto);
			$hoja1->setCellValue('H'.$j,$value->tipollanta);
			$hoja1->setCellValue('I'.$j,$value->medida);
			
			$hoja1->setCellValue('J'.$j,number_format($value->totalCompras,2,'.',' '));
			// $hoja1->setCellValue('J'.$j,number_format($value->totalTranslados,2,'.',' '));
			$hoja1->setCellValue('K'.$j,number_format($value->totalVentas,2,'.',' '));
			$hoja1->setCellValue('L'.$j,number_format($value->totalIncidencias,2,'.',' '));
			
			$acum = $value->totalCompras-$value->totalVentas-$value->totalIncidencias;
			
			// if($value->totalIncidencias<0){
			// 	$acum = $acum+$value->totalIncidencias;
			// }else{
			// $acum = $acum+$value->totalIncidencias;		
			// }

			$hoja1->setCellValue('M'.$j,number_format($acum,2,'.',' '));
			
			$hoja1->getStyle('A'.$j.':M'.$j)->applyFromArray($this->estilo_content);
			$j++;
		}

		foreach(range('A','M') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="inventario.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

	public function excelAuto ($id) 
	{	
		$local = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
		   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
		   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
		   ->where('local.tipo','=','A')
		   ->where('local.id','=',$id)
		   ->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'dep.nombre as departamento')
		   ->orderBy('local.codRegistro','ASC')
		   ->first();

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario Autos");
		$hoja1->setCellValue('A1','INVENTARIO DE AUTOS DE ALMACEN UBICADO EN: '.strtoupper($local->direccion));
		$hoja1->mergeCells('A1:J1');
		$hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','AUTO');
		$hoja1->setCellValue('F2','MOVIMIENTOS DE STOCK');
		
		$hoja1->setCellValue('A3','MARCA');
		$hoja1->setCellValue('B3','COD. PROVEEDOR');
		$hoja1->setCellValue('C3','DESCRIPCIÓN');
		$hoja1->setCellValue('D3','VERSIÓN');
		$hoja1->setCellValue('E3','TRANSMISIÓN');

		$hoja1->setCellValue('F3','DESC. ADICIONAL');
		$hoja1->setCellValue('G3','MONEDA');
		$hoja1->setCellValue('H3','CANTIDAD');
		$hoja1->setCellValue('I3','PRECIO COSTO');
		$hoja1->setCellValue('J3','TOTAL');
	
		$hoja1->mergeCells('A2:E2');	
		$hoja1->mergeCells('F2:J2');

		$hoja1->getStyle('A2:E2')->applyFromArray($this->estilo_header);
		$hoja1->getStyle('F2:J2')->applyFromArray($this->estilo_header);
		
		$hoja1->getStyle('A3:J3')->applyFromArray($this->estilo_header);

		$j = 4;
		$productos = DB::table('auto')
					->join('marcaauto as mt','mt.id','=','auto.marcaId')
					->join('stockproductodetalle as spd','spd.idAuto','=','auto.id')
					->join('loteauto as la','la.id','=','spd.idLoteAuto')
					->whereRaw('la.idAuto = auto.id')
					->where('spd.idAlmacenSalida',$id)
					->where('la.idAlmacen',$id)
					->where('spd.stock','>','0')
					->whereNotNull('spd.idLoteAuto')
					->where('spd.tipo','A')
					->select('mt.nombre as marca','spd.stock','la.descripcionadicional',
					'la.tipoMoneda',
					'la.precioSoles','auto.*')
					->orderBy('auto.descripcion','ASC')
					->get();

		foreach ($productos as $value) {
			$hoja1->setCellValue('A'.$j,$value->marca);
			$hoja1->setCellValue('B'.$j,$value->codproveedor);
			$hoja1->setCellValue('C'.$j,$value->descripcion);
			$hoja1->setCellValue('D'.$j,$value->version);
			$hoja1->setCellValue('E'.$j,$value->transmision);
			$hoja1->setCellValue('F'.$j,$value->descripcion);
			$hoja1->setCellValue('G'.$j,($value->tipoMoneda == 'S'?'PEN':'USD'));
			$hoja1->setCellValue('H'.$j,$value->stock);
			$hoja1->setCellValue('I'.$j,number_format($value->precioSoles,2,'.',''));
			$hoja1->setCellValue('J'.$j,number_format($value->stock * $value->precioSoles,2,'.',''));
			
			
			$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
			$j++;
		}

		foreach(range('A','J') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="inventario_auto.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

	public function excel02($almacenId, $tipoId, Request $request)
	{		
		$fecha_i = $request->get('fechai');
		$fecha_f = $request->get('fechaf');


		$detalles = Guia::leftJoin('detalleguia as dg','dg.idGuia','=','guia.id')
					->leftJoin('producto as pr','pr.id','=','dg.idProducto')
					->join('tipoproducto','tipoproducto.id','=','pr.idMargenGanancia')
					// ->leftJoin('categoriaproducto as cp','cp.id','=','pr.idCategoriaProducto')
					->leftJoin('tipodocumento as td','guia.idTipoGuia','=','td.id')
					->where('guia.idAlmacen','=',$almacenId);
	
			
		// if ($tipoId != '') {
		// 	$detalles = $detalles->where('pr.idCategoriaProducto','=',$tipoId);
		// }

		if ($fecha_i != '') {
			$detalles = $detalles->where('guia.fecha','>=',$fecha_i);
		}

		if ($fecha_f != '') {
			$detalles = $detalles->where('guia.fecha','<=',$fecha_f);
		}


		$detalles = $detalles->select('dg.cantidad','dg.preciocompra','dg.precioventa','dg.descripcion', 
		DB::Raw("DATE_FORMAT(guia.fecha,'%d/%m/%Y') as fecha"), 
		DB::Raw("CONCAT(td.abreviatura,LPAD(guia.serie,3,'0'),'-',LPAD(guia.numero,8,'0')) as documento"),
		DB::Raw("(CASE WHEN td.abreviatura = 'GC' THEN 'ENTRADA' ELSE 'SALIDA' END) as tipo"),'guia.id',
		DB::Raw("DATE_FORMAT(guia.created_at, '%d/%m/%Y %H:%i:%s') as fechaRegistro"),'tipoproducto.porcentaje as ganancia',
		DB::Raw("(CASE pr.tipoProducto WHEN 'A' THEN 'Accesorios/Repuestos'
				 WHEN 'B' THEN 'Baterías'
				 WHEN 'LL' THEN 'Neumáticos'
				 WHEN 'M' THEN 'Muelles'
				 ELSE 'Insumos' END) as tipoProd")
		)->orderBy('guia.fecha','DESC');
		
		
		$lista = $detalles->get();
		
		// dd($lista);
		$local = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
		   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
		   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
		   ->where('local.tipo','=','A')
		   ->where('local.id','=',$almacenId)
		   ->select('local.id','local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'local.telefono',DB::raw("DATE_FORMAT(local.created_at,'%d/%m/%Y') as fechaRegistro"),'dep.nombre as departamento')
		   ->orderBy('local.codRegistro','ASC')
		   ->first();

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','DETALLE DE ENTRADAS Y SALIDAS DE ALMACEN UBICADO EN: '.strtoupper($local->direccion));
		$hoja1->mergeCells('A1:J1');
		$hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','PRODUCTO');
		$hoja1->setCellValue('D2','MOVIMIENTOS DE STOCK');

		$hoja1->setCellValue('A3','TIPO DE PRODUCTO');
		$hoja1->setCellValue('B3','DESCRIPCIÓN');
		$hoja1->setCellValue('C3','GANACIA (%)');
		$hoja1->setCellValue('D3','CANTIDAD');
		$hoja1->setCellValue('E3','PRECIO COMPRA');
		$hoja1->setCellValue('F3','PRECIO VENTA');
		$hoja1->setCellValue('G3','DOCUMENTO');
		$hoja1->setCellValue('H3','TIPO DE OPERACION');
		$hoja1->setCellValue('I3','FECHA DE GUIA');
		$hoja1->setCellValue('J3','FECHA DE CREACIÓN');
	
		$hoja1->mergeCells('A2:C2');	
		$hoja1->mergeCells('D2:J2');

		$hoja1->getStyle('A2:C2')->applyFromArray($this->estilo_header);
		$hoja1->getStyle('D2:J2')->applyFromArray($this->estilo_header);
		
		$hoja1->getStyle('A3:J3')->applyFromArray($this->estilo_header);

		$j = 4;
		
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$value->tipoProd);
			$hoja1->setCellValue('B'.$j,$value->descripcion);
			$hoja1->setCellValue('C'.$j,number_format($value->ganancia,2,'.',''));
			$hoja1->setCellValue('D'.$j,$value->cantidad);
			$hoja1->setCellValue('E'.$j,$value->preciocompra);
			$hoja1->setCellValue('F'.$j,$value->precioventa);
			$hoja1->setCellValue('G'.$j,$value->documento);
			$hoja1->setCellValue('H'.$j,$value->tipo);
			$hoja1->setCellValue('I'.$j,$value->fecha);
			$hoja1->setCellValue('J'.$j,$value->fechaRegistro);
		
			$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
			$j++;
		}

		foreach(range('A','J') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="detalleguias.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}

	public function getGuiasInventario (Request $request) {
		$comprobante 	 = $request->get('comprobante');
		$tipoDocumento = $request->get('tipodocumento');
		$personal = $request->get('registradopor');

		$fechaI = $request->get('fechaI');
		$fechaF = $request->get('fechaF');
		
		$filas 		 = $request->get('filas');
		$page 		 = $request->get('page');
		
		$guias = DB::table('guia as g')
					->leftJoin('tipodocumento as td','td.id','=', 'g.idTipoGuia')
					->leftJoin('trabajador as tr','tr.id','=','g.idPersonal')
					->where(DB::raw("CONCAT(tr.apellidos,' ', tr.nombres)"),'LIKE', '%'. $personal.'%')
					->where('g.numero','LIKE','%'.$comprobante.'%');

				
		if ($tipoDocumento != '' && $tipoDocumento != 'todo') {
			$guias = $guias->where('g.idTipoGuia', $tipoDocumento);
		}

		if ($fechaI != '') {
			$guias = $guias->where(DB::Raw("DATE_FORMAT(g.created_at,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$guias = $guias->where(DB::Raw("DATE_FORMAT(g.created_at,'%Y-%m-%d')"),'<=',$fechaF);
		}

		$guias = $guias->select('g.id', DB::raw("CONCAT(tr.apellidos,' ', tr.nombres) as personal"),
				DB::Raw("ROUND(g.total,2) as total"), 'g.observacion', 'g.tipoMoneda', DB::Raw("DATE_FORMAT(g.fecha, '%d/%m/%Y') as fecha"),
				DB::Raw("ROUND(g.flete,2) as flete"), DB::Raw("DATE_FORMAT(g.created_at, '%d/%m/%Y %h:%i:%s %p') as fechaRegistro"), 'td.nombre as tipo',
				DB::Raw("CONCAT(td.abreviatura, LPAD(g.serie,2,'0') ,'-', LPAD(g.numero,8,'0')) as guia"),
				DB::Raw("(CASE WHEN g.deleted_at IS NULL THEN 'V' ELSE 'A' END) as situacion"))
				->orderBy('g.created_at','ASC');

		$lista = $guias->get();
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
			$arrPag = [['opc' => '1']];
			$page = '1';
			$inicio = '1';
			$fin = '1';
			$paramInicio = '1';
			$paramFin = '1';
		}
		
		$lista = $guias->offset(($page-1)*$filas)
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
		
		return ['guias' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Movimiento de Almacén':' Movimientos de Almacén'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
		
	}

	public function generarPdfGuia($id){
		// {dd('esto aca'); 
		// $request->get('id');
		// $cotizacion =Cotizacion::find($id);
		// $cliente=Persona::find($cotizacion->idCliente);
		// $trabajdor=Personal::find($cotizacion->idPersonal);
		// dd($cotizacion->idCliente);
		$guia=DB::table('guia as g')
				->leftjoin('tipodocumento as td', 'td.id','=','g.idTipoGuia')
				->leftJoin('trabajador as tra','g.idPersonal','=','tra.id')
				->where('g.id',$id)
				->select('g.id','g.flete', DB::Raw("DATE_FORMAT(g.created_at, '%d/%m/%Y %h:%i:%s %p') as fechaRegistro"), 
				DB::raw("CONCAT(td.abreviatura, LPAD(g.serie,2,'0') ,'-', LPAD(g.numero,8,'0')) as documento"), 
				DB::raw("DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha"), DB::Raw("ROUND(g.total,2) as total"), 
				DB::Raw("ROUND(g.flete,2) as flete"), 'g.tipoMoneda',
				DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"), 'g.observacion', 'td.nombre as tipo')
				->first();

		$detalles = DB::table('detalleguia as det')
					// ->leftJoin('producto as prod','prod.id','=','det.idProducto')
					// ->leftJoin('servicio as serv','serv.id','=','det.idServicio')
					->where('det.idGuia','=',$id)
					// ->whereNull('det.deleted_at')
					->select(DB::Raw("ROUND(det.cantidad,2) as cantidad"), 'det.descripcion', 
					DB::Raw("ROUND(det.preciocompra,2) as preciocompra"), DB::Raw("ROUND(det.precioventa,2) as precioventa"),
					DB::Raw("ROUND(det.subTotal,2) as subTotal"), 'det.item')
					->orderBy('det.item','ASC')
					->get();

		$fpdf = new Fpdf();
		$fpdf::SetTitle(utf8_decode('Guía de Almacén'));
        $fpdf::AddPage('P','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,40,25);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(138, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(60,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(138);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(60,$alto,utf8_decode("$guia->tipo"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, $guia->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(6);
        $alto = 3;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',9);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',8);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELÉFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
        $fpdf::Ln(6);
	    
	    $alto = 6;
		$tam_font = 9;
		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($guia->trabajador)), "TR", "L");
    
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$fpdf::SetFont('Arial','B',$tam_font);
		//$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Elaborado por'), 'LT',0, "L");
		//$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
        
        //$_x = $fpdf::GetX();
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T',0, "L");
        //$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

            
		//$_y = $fpdf::GetY();
		
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($guia->trabajador)), 'TR', "L");
		$fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

	    // if ($alto2 > $alto) {
        //      $fpdf::SetXY(153,$fpdf::GetY()-($alto2-$alto));
        // } else {
        //      $fpdf::SetXY(153,$fpdf::GetY()-$alto2);
        // }

		$fpdf::Ln();
       	
   		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($guia->flete)), 1, "L");
    
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Flete'), 'L',0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'),0,  0, "L");
    
		
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($guia->flete)), 'R', "L");
    	$fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
		$fpdf::Ln();
			
   		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($guia->fecha)), 1, "L");
   		
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		// //$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Fecha'), 'L', 0,  "L");
		// //$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
    
   		// //$_x = $fpdf::GetX();
    	$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");
        // // $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);
    
		$_y = $fpdf::GetY();
   		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($guia->fecha)), 'R', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
       	$fpdf::SetXY(15, ($alto2>$alto?$fpdf::GetY()+$alto:$fpdf::GetY()));
		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($guia->fechaRegistro)), 1, "L");
	   	$fpdf::SetFont('Arial','B',$tam_font);
   		//$_x = $fpdf::GetX();
        $fpdf::Cell(25, $alto2, utf8_decode('Fecha Sistema'), 'L',0, "L");
        //$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

        //$_x = $fpdf::GetX();
		$fpdf::Cell(5, $alto2, utf8_decode(':'),0, 0, "L");
        //$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		//$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
    	$fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($guia->fechaRegistro)), 'R', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		
		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper(($guia->tipoMoneda=='S'?'soles':'dólares'))), 1, "L");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		// $_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Moneda'), 'LB', 0, "L");
        // $fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

		// $_x = $fpdf::GetX();
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");
        // $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper(($guia->tipoMoneda=='S'?'soles':'dólares'))), 'RB', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$fpdf::SetFillColor(255);
		$fpdf::Ln();
		$alto = 2;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',8);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C");
        $fpdf::Cell(13, $alto, utf8_decode("UND."), 1, 0, "C");
        $fpdf::Cell(69, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(27, $alto, utf8_decode("P. COMPRA"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("P. VENTA"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("SUBTOTAL"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',7);
        $alto = 4;
        // $total = 0;
        $acum=0;
		foreach ($detalles as $deta) {
			$alto2 = $fpdf::GetMultiCellHeight(69,$alto,utf8_decode($deta->descripcion), 1, "C");
		
			$fpdf::SetX(15);
			$fpdf::Cell(15, $alto2, utf8_decode($deta->item<10?'0'.$deta->item:$deta->item), 'L', 0, "C");
			$fpdf::Cell(15, $alto2, $deta->cantidad, 'L', 0, "R");
			$fpdf::Cell(13, $alto2, utf8_decode("UND"), 0, 0, "C");
		
			// $_y = $fpdf::GetY();
			$_x = $fpdf::GetX();
			$fpdf::MultiCell(69, $alto, utf8_decode($deta->descripcion), 0, "L");
			$fpdf::setXY($_x+69,$fpdf::GetY()-$alto2);
			// $fpdf::Cell(15, $alto2, number_format($deta->porcentajeDescuento,2,'.',','), 0, 0, "R");
			$fpdf::Cell(27, $alto2, number_format($deta->preciocompra,2,'.',','), 0, 0, "R");
			$fpdf::Cell(22, $alto2, number_format($deta->precioventa,2,'.',','), 0, 0, "R");
			$fpdf::Cell(22, $alto2, number_format($deta->subTotal,2,'.',','), 'R', 0, "R");
			$fpdf::Ln();
			// $total+=$deta->subTotal;
			$acum++;
		}

		$fpdf::SetX(15);
		if ($acum == 0) {
			$fpdf::Cell(183, $alto, utf8_decode("NO EXISTEN DETALLES SELECCIONADOS"), 1, 1, "L");
		} else {
			$fpdf::Cell(158, $alto, '', 'T', 0, "L");
			// $fpdf::SetX(15);
			$fpdf::SetFont('Arial','B',9);
			$fpdf::Cell(25, $alto+2, number_format($guia->total,2,'.',','), 'T', 0, "R");
		}


		$alto = 6;
    	$fpdf::SetFillColor(240);
		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);
      
		$letras = new EnLetras();
        // $fpdf::SetFont('helvetica', 'B', 8);
        $valor = $letras->ValorEnLetras(str_replace(',','',$guia->total), ($guia->tipoMoneda=='S'?'NUEVOS SOLES':'DÓLARES AMERICANOS')); //letras

		$son = strtoupper("SON: ".$valor);
        $fpdf::MultiCell(183, $alto, utf8_decode($son), $borde, "L", true);
		   
		$fpdf::Ln(10);
        $fpdf::SetX(15);
       	$fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", true);
		$fpdf::Ln();
        

		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode($guia->observacion), 1, "L");
		$fpdf::SetTextColor(206,3,3);
        $fpdf::SetX(15);
		$_x = $fpdf::GetX();
		$fpdf::Multicell(183, $alto, utf8_decode($guia->observacion), 'RBL', "L");
		$fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);
   
        $fpdf::SetXY(138, $fpdf::GetY());
        $fpdf::SetFont('Arial','',12);
        $alto = 5;
        
		$fpdf::Output("Guia-".$guia->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
  
	
	}

	public function guiasExcel (Request $request) {
		$comprobante 	 = $request->get('comp');
		$tipoDocumento = $request->get('tipodoc');
		$personal = $request->get('registrado');

		$fechaI = $request->get('fechaI');
		$fechaF = $request->get('fechaf');
		
		$guias = DB::table('guia as g')
					->leftJoin('tipodocumento as td','td.id','=', 'g.idTipoGuia')
					->leftJoin('trabajador as tr','tr.id','=','g.idPersonal')
					->where(DB::raw("CONCAT(tr.apellidos,' ', tr.nombres)"),'LIKE', '%'. $personal.'%')
					->where('g.numero','LIKE','%'.$comprobante.'%');

				
		if ($tipoDocumento != '' && $tipoDocumento != 'todo') {
			$guias = $guias->where('g.idTipoGuia', $tipoDocumento);
		}

		if ($fechaI != '') {
			$guias = $guias->where(DB::Raw("DATE_FORMAT(g.created_at,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$guias = $guias->where(DB::Raw("DATE_FORMAT(g.created_at,'%Y-%m-%d')"),'<=',$fechaF);
		}

		$guias = $guias->select('g.id', DB::raw("CONCAT(tr.apellidos,' ', tr.nombres) as personal"),
				DB::Raw("ROUND(g.total,2) as total"), 'g.observacion', 'g.tipoMoneda', DB::Raw("DATE_FORMAT(g.fecha, '%d/%m/%Y') as fecha"),
				DB::Raw("ROUND(g.flete,2) as flete"), DB::Raw("DATE_FORMAT(g.created_at, '%d/%m/%Y %h:%i:%s %p') as fechaRegistro"), 'td.nombre as tipo',
				DB::Raw("CONCAT(td.abreviatura, LPAD(g.serie,2,'0') ,'-', LPAD(g.numero,8,'0')) as guia"),
				DB::Raw("(CASE WHEN g.deleted_at IS NULL THEN 'V' ELSE 'A' END) as situacion"))
				->orderBy('g.created_at','ASC');

		$lista = $guias->get();
		
		#EXPORTAR A EXCEL

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Guías de Almacén");
		$hoja1->setCellValue('A1','GUÍAS DE ALMACÉN');
		$hoja1->mergeCells('A1:J1');
		$hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_header);
			
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','FECHA');
		$hoja1->setCellValue('C2','T. GUIA');
		$hoja1->setCellValue('D2','COMPROBANTE');
		$hoja1->setCellValue('E2','MONEDA');
		$hoja1->setCellValue('F2','REGISTRADO POR');
		$hoja1->setCellValue('G2','FLETE');
		$hoja1->setCellValue('H2','TOTAL');
		$hoja1->setCellValue('I2','OBSERVACIÓN');
		$hoja1->setCellValue('J2','REGISTRADO EL');

		$hoja1->getStyle('A2:J2')->applyFromArray($this->estilo_header);
		$j = 3;
		$acum = 1;
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$acum);
			$hoja1->setCellValue('B'.$j,$value->fecha);
			$hoja1->setCellValue('C'.$j,$value->tipo);
			$hoja1->setCellValue('D'.$j,$value->guia);
			$hoja1->setCellValue('E'.$j,($value->tipoMoneda=='S'?'SOLES':'DÓLARES'));
			$hoja1->setCellValue('F'.$j,$value->personal);
			$hoja1->setCellValue('G'.$j,$value->flete);
			$hoja1->setCellValue('H'.$j,$value->total);
			$hoja1->setCellValue('I'.$j,$value->observacion);
			$hoja1->setCellValue('J'.$j,$value->fechaRegistro);
			
			if ($value->situacion == 'A') {
				$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content_danger);
			} else {
				$hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
			}
			$j++;
			$acum++;
		}

		foreach(range('A','J') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ReporteGuias.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');

	}

}
