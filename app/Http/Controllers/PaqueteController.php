<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Auto;
use App\Models\Servicio;
use App\Models\Producto;

use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Menu;
use App\Models\MenuUsuario;
use App\Models\TipoUsuario;
use App\Models\MarcaAuto;
use App\Models\StockAuto;
use App\Models\Persona;
use App\Models\Paquete;
use App\Models\Personal;
use App\Models\DetallePaquete;
use App\Models\Serie;
// use PDF;
use App\Libraries\Funciones;
use DB;
use Auth;

use Validator;

class PaqueteController extends Controller
{
	public function __construct(){
        $this->middleware('auth');
    }

	public $almacenId = 2;
	public $tiendaId  = 1;

	public function getMarcas($tipo, Request $request) {
		$marcas = MarcaAuto::where('tipo','=',$tipo)->get();
		
		return ['marcas' => $marcas];
	}

    public function getAll (Request $request) {
    	$modelo 	 = $request->get('modelo');
		$marca 	 	 = $request->get('marca');
		
		$paquete	 = $request->get('nombre');
		$kilometraje = $request->get('kilometraje');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	     = $request->get('fechaF');
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
        $cotizacion = DB::table('paquete')
                      ->leftJoin('marcaauto as marca','marca.id','=','paquete.idMarca')
                      ->leftJoin('trabajador as tra','paquete.idPersonal','=','tra.id')
					  ->where('paquete.nombre','LIKE', '%'.$paquete.'%')
					  ->where('paquete.modelo','LIKE', '%'.$modelo.'%') 
					  ->where('paquete.kilometraje','LIKE', '%'.$kilometraje.'%');
		if ($fechaI != '') {
			$cotizacion = $cotizacion->where(DB::Raw("DATE_FORMAT(paquete.created_at,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$cotizacion = $cotizacion->where(DB::Raw("DATE_FORMAT(paquete.created_at,'%Y-%m-%d')"),'<=',$fechaF);
		}

        if ($marca != 'todo') {
			$cotizacion = $cotizacion->where('paquete.idMarca','=',(int) $marca);
		}


		$cotizacion =  $cotizacion->select('paquete.id','paquete.nombre','paquete.modelo','paquete.kilometraje','paquete.situacion',DB::Raw("FORMAT(paquete.total,2) as total"), 'marca.nombre as marca', DB::raw("(CASE paquete.situacion 
		WHEN 'V' THEN 'VIGENTE'
		WHEN 'A' THEN 'ANULADO' 
		WHEN 'N' THEN 'NO VIGENTE'
		WHEN 'U' THEN 'REVISADO' END) as situaciontexto"), DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"), DB::Raw("DATE_FORMAT(paquete.created_at,'%d/%m/%Y') as fecha"),'paquete.situacion');
		


		//    ->orderBy('cotizacion.fecha','ASC');

		$lista = $cotizacion->get();
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
			$arrPag = [['opc' => '1', 'bloqueaado' => true]];
			$page = '1';
			$inicio = '1';
			$fin = '1';
	        $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $cotizacion->offset(($page-1)*$filas)
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
		// 	$arrPag[] = array('opc' => '1');
		// 	$arrPag[] = array('opc' => '2');
		// 	$arrPag[] = array('opc' => '3');
		// 	$arrPag[] = array('opc' => '...');
		// 	$arrPag[] = array('opc' => $paginador-1);
		// 	$arrPag[] = array('opc' => $paginador);
		// }


		
    	return ['paquetes' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Plan':' Planes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 
    
    public function generarPdf($id){
	// {dd('esto aca'); 
		// $request->get('id');
		// $cotizacion =Cotizacion::find($id);
		// $cliente=Persona::find($cotizacion->idCliente);
		// $trabajdor=Personal::find($cotizacion->idPersonal);
		// dd($cotizacion->idCliente);
		$cotizacion=DB::table('cotizacion as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id',DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"),
							 
					          DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
					         DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','cot.placa','cot.vin','cot.total')
					->first();
		$productos=DetalleCotizacion::withTrashed()->where('idCotizacion',$cotizacion->id)->where('tipoDetalle','P')->get();
	   $sumaProductos=0;
	   if($productos->count()>0){
		   foreach ($productos as $pro) {
			$sumaProductos+=$pro->subTotal;
		}
	   }
		
		$servicios=DetalleCotizacion::withTrashed()->where('idCotizacion',$cotizacion->id)->where('tipoDetalle','S')->get();

		$sumaServicios=0;
		if($servicios->count()>0){
			foreach ($servicios as $ser) {
			 $sumaServicios+=$ser->subTotal;
		 }
		}
		$total=number_format($cotizacion->total,3,'.','');

		$sumaProductos=number_format($sumaProductos,3,'.','');
		
		// $sumaServiciosr=round($sumaServicios,3);
		$sumaServicios=number_format($sumaServicios,3,'.','');
		
		// dd($productos);
		
		$pdf=PDF::loadView('pdf.cotizacion',compact('cotizacion','productos','sumaProductos','servicios','sumaServicios','total'));
		$pdf->setPaper("A4", "portrait");
		// dd($pdf);
		//   return PDF::loadView('pdf.cotizacion')
		//     ->download('archivo.pdf');
		return $pdf->stream('archivo.pdf');
	
	}
	
	public function obtenerProductos (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$pr   = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
			   	   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->leftjoin('lote as l','l.idProducto','=','producto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idProducto','=','l.idProducto')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
				   ->where(function ($qq) use ($desc) {
						$qq->where('ma.nombre','LIKE',$desc.'%')
						->orwhere('mt.nombre','LIKE',$desc.'%')
						->orwhere('producto.nombre','LIKE',$desc.'%')
						->orwhere('producto.tipoLlanta','LIKE',$desc.'%')
						->orwhere('producto.medida','LIKE',$desc.'%')
						->orwhere('ml.nombre','LIKE',$desc.'%')
						->orWhere(DB::Raw("(CASE producto.tipoProducto 
						WHEN 'A'  THEN 'Accesorio/Repuesto' 
						WHEN 'LL' THEN 'Neumáticos' 
						WHEN 'I'  THEN 'Insumos' 
						WHEN 'B'  THEN 'Baterías' 
						ELSE 'MUELLES' END)"),'LIKE','%'.$desc.'%');
					})
				   ->select('producto.id', DB::Raw("CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre"), DB::Raw("(CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2"),
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD")  
					)
				   ->orderBy('l.numero','ASC');


			#SERVICIOS

			$sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
					  ->where('servicio.nombre','LIKE',$desc.'%')
					  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("'0' as stock"), DB::Raw("FORMAT(servicio.precio,2) as precioS"), DB::Raw("'-' as precioD"))
					  ->unionAll($pr)
					  ->get();
    // dd($sr);
			// $sr  = $sr->unionAll($pr);

			#AUTOS
			// $at    =  Auto::leftJoin('marcaauto as mt','mt.id','=','auto.marcaId')
			// 		  ->leftjoin('stockauto as sa','sa.idAuto','=','auto.id')
			// 		  ->where(function ($qq) use ($desc) {
			// 		  	$qq->where('auto.modelo','LIKE',$desc.'%')
			// 		  		->orwhere('auto.version','LIKE',$desc.'%')
			// 		  		->orwhere('mt.nombre','LIKE',$desc.'%');
			// 		  })
			//   	      ->where('sa.idAlmacen','=',$this->almacenId)
			// 		  ->select('auto.id',DB::Raw("CONCAT('Tipo: ', (CASE WHEN auto.tipoAuto = 'L' THEN 'Livianos' ELSE 'Camiones y Comerciales' END),', Marca: ',mt.nombre, ', Modelo: ', auto.modelo, '- Año: ',auto.anio) as nombre"), DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("'-' as tiempo"), DB::Raw("FORMAT(auto.precio,2) as precio"), DB::Raw("'-' as medida"), DB::Raw("'Auto' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("(sa.totalCompras-sa.totalVentas) as stock"))
			// 		  ->unionAll($pr)
			// 		  ->unionAll($sr)
			// 		  ->get();

			return ['productos' => $sr];
	    }
	}

	public function obtenerProductosMov (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$pr   = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
			   	   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->leftjoin('lote as l','l.idProducto','=','producto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idProducto','=','l.idProducto')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
				   ->where(function ($qq) use ($desc) {
						$qq->where('ma.nombre','LIKE',$desc.'%')
						->orwhere('mt.nombre','LIKE',$desc.'%')
						->orwhere('producto.nombre','LIKE',$desc.'%')
						->orwhere('producto.tipoLlanta','LIKE',$desc.'%')
						->orwhere('producto.medida','LIKE',$desc.'%')
						->orwhere('ml.nombre','LIKE',$desc.'%')
						->orWhere(DB::Raw("(CASE producto.tipoProducto 
						WHEN 'A'  THEN 'Accesorio/Repuesto' 
						WHEN 'LL' THEN 'Neumáticos' 
						WHEN 'I'  THEN 'Insumos' 
						WHEN 'B'  THEN 'Baterías' 
						ELSE 'MUELLES' END)"),'LIKE','%'.$desc.'%');
					})
				   ->select('producto.id', DB::Raw("CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre"), DB::Raw("(CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2"),
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD")  
					)
				   ->orderBy('l.numero','ASC')
				   ->get();


			#SERVICIOS

			// $sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
			// 		  ->where('servicio.nombre','LIKE',$desc.'%')
			// 		  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("'0' as stock"), DB::Raw("FORMAT(servicio.precio,2) as precioS"), DB::Raw("'-' as precioD"))
			// 		  ->unionAll($pr)
			// 		  ->get();

			// $sr  = $sr->unionAll($pr);

			#AUTOS
			// $at    =  Auto::leftJoin('marcaauto as mt','mt.id','=','auto.marcaId')
			// 		  ->leftjoin('stockauto as sa','sa.idAuto','=','auto.id')
			// 		  ->where(function ($qq) use ($desc) {
			// 		  	$qq->where('auto.modelo','LIKE',$desc.'%')
			// 		  		->orwhere('auto.version','LIKE',$desc.'%')
			// 		  		->orwhere('mt.nombre','LIKE',$desc.'%');
			// 		  })
			//   	      ->where('sa.idAlmacen','=',$this->almacenId)
			// 		  ->select('auto.id',DB::Raw("CONCAT('Tipo: ', (CASE WHEN auto.tipoAuto = 'L' THEN 'Livianos' ELSE 'Camiones y Comerciales' END),', Marca: ',mt.nombre, ', Modelo: ', auto.modelo, '- Año: ',auto.anio) as nombre"), DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("'-' as tiempo"), DB::Raw("FORMAT(auto.precio,2) as precio"), DB::Raw("'-' as medida"), DB::Raw("'Auto' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("(sa.totalCompras-sa.totalVentas) as stock"))
			// 		  ->unionAll($pr)
			// 		  ->unionAll($sr)
			// 		  ->get();

			return ['productos' => $pr];
	    }
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

	public function getDetalles ($id, Request $request) {
		$detalles = DB::table('paquete')
					->leftJoin('detallepaquete as det','det.idPaquete','=','paquete.id')
					->where('paquete.id','=',$id)
					->select('det.cantidad','det.descripcion', DB::Raw("(CASE WHEN det.tipoDetalle = 'S' THEN 'Servicio' ELSE (CASE WHEN det.tipoDetalle = 'P' THEN 'Producto' ELSE 'Auto' END) END) as tipo"),
						DB::Raw("FORMAT(det.precio,2) as precio"),
						DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id', DB::Raw("FORMAT(paquete.total,2) as total"))
					->orderBy('det.tipoDetalle','ASC')
					->get();

		$total = 0;
		if (count($detalles)) {
			$total = $detalles[0]->total;
		}
 
    	return ['detalles' => $detalles,'total' => $total];
	}

    public function getCorrelativo (Request $request) {
		$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
			->where('tipoDocumento','=','C')
			->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"))
			->first();
		
		return ['numero' => $serie->numero];
	}

	public function obtenerCotizaciones (Request $request) {  
		$placa = $request->get('placa');
		$nro   = $request->get('descripcion');
		$clienteId = $request->get('clienteId');

		$cotizacion = Cotizacion::where('placa','=',$placa)
					  ->where('situacion','=','V')
					  ->where('idCliente','=',$clienteId);
		if ($nro != '') { 
			$cotizacion  = $cotizacion->where(DB::Raw("CONCAT(serie,'-',numero)"),'LIKE', $nro.'%');
		}

		$cotizacion = $cotizacion->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"), DB::Raw("DATE_FORMAT(fecha,'%d/%m/%Y') as fecha"),DB::Raw("FORMAT(total,2) as total2"),'id', 'total')
		->get();
		return ['cotizaciones' => $cotizacion];
	}
    
    public function obtenerPaquetes (Request $request) {
        $filtro = $request->get('filtro');
        $descripcion = $request->get('descripcion');
        $band = true;
        
        if ($filtro != '' && !is_null($descripcion)) {
            $paquete = DB::table('paquete')
                        ->leftjoin('marcaauto as marca','marca.id','=','paquete.idMarca')
                        ->where('paquete.situacion','=','V');
        
            switch ($filtro) {
    			case 'nombre':
    				if ($descripcion <> '')
						$paquete = $paquete->where('paquete.nombre','LIKE', '%'.$descripcion.'%');
						break;
				case 'kilometraje':
    				if ($descripcion <> '')	
						$paquete = $paquete->where('paquete.kilometraje','LIKE', '%'.$descripcion.'%');
						break;
				case 'modelo':
    				if ($descripcion <> '')	
    					$paquete = $paquete->where('paquete.modelo','LIKE', '%'.$descripcion.'%');
	    				break;
                default:
    				if ($descripcion <> '')	
    					$paquete = $paquete->where('marca.nombre','LIKE', '%'.$descripcion.'%');
	    				break;
        	}

            $paquete = $paquete->select('paquete.id','paquete.nombre', DB::Raw("FORMAT(paquete.total,2) as total"), 
                            'marca.nombre as marcaauto','paquete.modelo','paquete.kilometraje', 
                            DB::Raw("(SELECT COUNT(detp.id) FROM detallepaquete as detp WHERE detp.idPaquete = paquete.id) as totalItems"))
                        ->get();
                            
            foreach ($paquete as $pq) {
                if (is_null($pq->id)) {
                    $band = false;
                }
            }
        } else {
            $paquete = [];
        }
        

        return ['paquetes' => ($band==true?$paquete:[])];
    }	
	
	public function guardarPaquete(Request $request) {
		// dd($request);
		$errors = $this->validar($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				$id = Auth::user()->usuarioId;
                if ($request->get('select_marca') === 'otro') {
					$nombre = $request->get('marca');
					$existe = MarcaAuto::where('nombre','=', $nombre)->first();
					$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
					if(is_null($existe)){
						$cat = new MarcaAuto;
						$cat->nombre = $nombre_g;
						$cat->save();

						$id_marca = $cat->id;
					}else{
						$id_marca = $existe->id;
					}
				}else{
					$id_marca = $request->get('select_marca');
				}

				$venta = new Paquete;
				$venta->nombre = $request->get('nombre');
				$venta->kilometraje = $request->get('kilometraje');
                $venta->idMarca = $id_marca;
                $venta->modelo  = $request->get('modelo');
                $venta->idPersonal = Auth::user()->usuarioId;
                $venta->total = $request->get('totalDoc');
                $venta->save();
                
                $id = $venta->id;
			
				
				$detalles3  = explode(',',$request->get('listDetalles'));
				$i = 1;
				if (count($detalles3) > 0 && $request->get('listDetalles') != '') {
					foreach ($detalles3 as $det) {
						$detalle = new DetallePaquete;
						$detalle->item = $i;
						$detalle->descripcion = $request->get('txtproducto'.$det);
						$detalle->cantidad = $request->get('txtcantidad'.$det);
						$detalle->precio = $request->get('txtprecio'.$det);
						$detalle->subTotal = $request->get('txtsubtototal'.$det);
						
						$tipo = $request->get('tipo'.$det);
						if ($tipo == 'Servicio') {
							$t = 'S';
						} elseif ($tipo == 'Producto') {
							$t = 'P';
						} else {
							$t = 'A';
						}
						$detalle->tipoDetalle = $t;

						if ($t == 'P') {
							$detalle->idProducto = $request->get('productoid'.$det);
							$detalle->idLote = $request->get('lote'.$det);
						} elseif ($t == 'A') {
							$detalle->idAuto = $request->get('productoid'.$det);
						} else {
							$detalle->idServicio = $request->get('productoid'.$det);
						}
						$detalle->idPaquete = $id;
						$detalle->save();
						$i++;
					}
				}

				$errors[] = 'Paquete Registrado Correctamente';
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
	}
// 
	public function validar (Request $request) {
		$reglas = [
            'nombre'        =>  'required',
            'select_marca'  =>  'required',
            'marca'         =>  ($request->get('select_marca') == 'otro'?'required':'nullable'),
            'modelo'        =>  'required',
            'kilometraje'   =>  'required',
            'listProductos' => 'nullable',
			'listServicios' => 'nullable',
			'listAutos'	  => 'nullable',
			'listDetalles' => 'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric'
        ];

        $mensajes = [
            'nombre.required'=> 'Indique Fecha',
            'select_marca.required'=> 'Seleccione Marca',
            'marca.required'=> 'Indique Marca',
			'modelo.required'=> 'Indique Modelo',
			'kilometraje.required'=> 'Indique Kilometraje',
    		'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
			'listDetalles.required'=> 'Indique Detalles a Paquete',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function cargarDetalles (Request $request) {
        $id = $request->get('id');
        $detalles = DB::table('detallepaquete as det')
                    ->leftJoin('stockproducto as stprod','stprod.idProducto','=','det.idProducto')
                    ->leftJoin('servicio as serv','det.idServicio','=','serv.id')
                    ->where('det.idPaquete','=',$id)
                    ->where(function ($qq) {
                        $qq->where('stprod.idAlmacen','=',$this->almacenId)
                           ->orWhereNotNull('det.idServicio');
                    })
                    ->select(DB::Raw("(CASE WHEN det.idServicio IS NOT NULL THEN 'Servicio' ELSE 'Producto' END) as tipo"), DB::Raw("FORMAT(det.subTotal,2) as subTotal"), 'serv.tiempoEjecucion as tiempo', 'serv.unidad', DB::Raw("(CASE WHEN det.idServicio IS NOT NULL THEN det.idServicio ELSE det.idProducto END) as id"),
                    DB::Raw("FORMAT(det.precio,2) as precio"),'det.cantidad','det.descripcion',DB::Raw('stprod.totalCompras-stprod.totalIncidencias-stprod.totalVentas as stock'))
                    ->orderBy('det.item','ASC')
                    ->get();
        // dd($detalles);
        return ['detalles' => $detalles];
    }

	public function eliminarPaquete (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$cotizacion = Paquete::find($id);
			$detalles = DetallePaquete::where('idPaquete','=',$cotizacion->id)
						->select('id')->get();
			foreach ($detalles as $det) {
				$d = DetallePaquete::find($det->id);
				$d->delete();	
			}
			$cotizacion->idPersonalEliminar = Auth::user()->usuarioId;
			$cotizacion->situacion = 'A';
			$cotizacion->update();
			$cotizacion->delete();
			$errors[] = 'Paquete Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	
	}
	public function situacionCotizacion()
	{
		$situuacion=Cotizacion::select(DB::raw('TIMESTAMPDIFF(minute,created_at,now()) as tiempoconcurrido'),'id')->whereNotIn('situacion',['A','U'])->get();
		// dd('estoy aca');
		// dd($situuacion->count());

		// </dd>
		if($situuacion->count()>0){
			// dd('estoy aca');
			foreach ($situuacion as  $value) {
			if( $value->tiempoconcurrido>=10080){
				DB::update("update cotizacion set situacion ='N'  where id = ?", [$value->id]);

			}
		}
		}
	
	}

}
