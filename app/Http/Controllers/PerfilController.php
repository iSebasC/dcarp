<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Personal;
use App\Models\Persona;
use App\Models\User;

use App\Models\TipoUsuario;
use App\Models\RelacionPersonal;
use App\Models\Servicio;
use App\Models\CategoriaServicio;
use App\Models\ServicioUsuario;
use App\Models\DetalleCotizacionOrden;
use App\Models\OrdenTrabajo;

use App\Libraries\Funciones;

use DB;
use Validator;
use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

class PerfilController extends Controller
{
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
     
    public function getPerfil(Request $request) {
        $tipo = Auth::user()->categoriaPersonalId;
        $id = Auth::user()->clienteId;
		$flagCliente = true;
		//   dd($tipo);
		// $tipo = 3;
		// $id   = 1; 
		if ($tipo == 8) {
			$p = Persona::where('tipoPersona','=','C') 
				->where('id','=',$id)
				->select('documento', 
				DB::Raw("CONCAT((CASE WHEN razonSocial IS NULL THEN CONCAT(apellidos, ' ', nombres) ELSE razonSocial END)) as nombres"), 
				'correoElectronico as correo')
				->first();

			$marcas=OrdenTrabajo::leftJoin('cita','ordentrabajo.idCita','=','cita.id')
					->leftJoin('marcaauto','cita.idMarcaAuto','=','marcaauto.id') 
					->where('cita.idCliente',$id)
					->whereNotNull('cita.deleted_at')
					->select(DB::raw('DISTINCT marcaauto.nombre'),'marcaauto.id')
					->take(3)
					->get();
			// return ['marcas'=>$marcas,'perfil' => $p];
        } else {
			$id = Auth::user()->usuarioId;
			$p = Personal::where('id','=',$id)
			->select('dni as documento', 
			DB::Raw("CONCAT(apellidos, ' ', nombres) as nombres"), 
			'correoE as correo')
			->first();

			$marcas = DB::table('ordentrabajo as o')
					->leftJoin('cita','o.idCita','=','cita.id')
					->leftJoin('marcaauto','cita.idMarcaAuto','=','marcaauto.id') 
					->whereNotNull('cita.deleted_at')
					->select('marcaauto.nombre','marcaauto.id', DB::Raw("COUNT(marcaauto.id) as cantidad"))
					->groupBy('marcaauto.nombre','marcaauto.id')
					->orderBy(DB::Raw("COUNT(marcaauto.id)"),'DESC')
					->take(3)
					->get();
			$flagCliente = false;
		
		}
		return ['marcas'=>$marcas,'perfil' => $p, 'isCliente' => $flagCliente];
    }

    public function getPerfilTrabajos (Request $request) {
        $limit = 50;
		$tipo = Auth::user()->categoriaPersonalId;
		$id = Auth::user()->clienteId;
		$fechaI=$request->get('fechai');
		$fechaF=$request->get('fechaf');
		
		$placa=$request->get('placa');
		$marca=$request->get('marca');
		$cliente=$request->get('cliente');

		$detalles = DB::table('ordentrabajo as or')
					->join('persona as cl','cl.id','=','or.idCliente')
					->join('cita as c','c.id','=','or.idCita')
					->leftjoin('trabajador as tr','tr.id','=','or.idAsignado')
					->whereNull('or.deleted_at')
					->where('or.placa','LIKE','%'.$placa.'%');


		if (!is_null($id)) {
			$detalles=$detalles->where('cl.id',$id);
			$limit = 10;
		} else {
			$detalles=$detalles->where(function ($qq) use ($cliente) {
				$qq->where('cl.documento','LIKE','%'.$cliente.'%')
					->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ',cl.nombres) ELSE cl.razonSocial END)"),'LIKE','%'.$cliente.'%');
			});

		}
		if ($marca != '') {
			$detalles=$detalles->where('c.idMarcaAuto',$marca);
		}
		if ($fechaI != '') {
			$detalles=$detalles->where(DB::Raw("DATE_FORMAT(or.fecha,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$detalles=$detalles->where(DB::Raw("DATE_FORMAT(or.fecha,'%Y-%m-%d')"),'<=',$fechaF);
		}
			
		$detalles = $detalles->select('or.placa', DB::Raw("CONCAT('OD', LPAD(or.serie,2,'0') ,'-', LPAD(or.numero,8,'0')) as orden"),
		DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.nombres,' ',cl.apellidos) ELSE cl.razonSocial END) as cliente"),
		'cl.documento', DB::Raw("DATE_FORMAT(or.inicia,'%d/%m/%Y %h:%i:%s %p') as inicia"), 
		DB::Raw("DATE_FORMAT(or.finaliza,'%d/%m/%Y %h:%i:%s %p') as finaliza"),
		DB::Raw("DATE_FORMAT(or.fecha,'%d/%m/%Y') as fecha"),
		DB::Raw("(SELECT v.idDocumentoSUNAT FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idOrden = or.id LIMIT 1) as ventaId"), 
		'or.id',
		DB::Raw("(SELECT GROUP_CONCAT(detc.descripcion ORDER BY detc.item ASC SEPARATOR '@@') FROM detalleordentrabajo as deto JOIN detallecotizacionorden as detc ON detc.idDetalleOrdenTrabajo = deto.id WHERE deto.idOrdenTrabajo = or.id) as servicios")
		)
		->orderBy('or.created_at','DESC')
		->take($limit)
		->get();

		// $marca='';
		//$tipo = 3;
		//$id = 1;

        
     
        // $detalles = DetalleCotizacionOrden::leftjoin('detalleordentrabajo as deto','deto.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
        // ->leftJoin('cotizacion as c','c.id','=','detallecotizacionorden.idCotizacion')
        // ->leftJoin('persona as cliente','cliente.id','=','c.idCliente')
        // ->leftJoin('detallecotizacionordenpersonal as detcotper','detcotper.idDetalleCotizacionOrden','=','detallecotizacionorden.id')
        // ->leftJoin('trabajador as trab','trab.id','=','detallecotizacionorden.idPersonalAsigna')
		// ->leftJoin('categoriapersonal as catp','catp.id','=','trab.idCategoriaPersonal')
		// ->leftJoin('cita','cliente.id','=','cita.idCliente')
		// ->leftJoin('marcaauto','cita.idMarcaAuto','=','marcaauto.id')
		// ->whereNull('detcotper.deleted_at')

		// ->whereNull('cita.deleted_at');
         
        // // if ($fecha != '') {
        // //     $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'<=',$fecha);
        // // }

        // // if ($placa != '') {
        // //     $detalles = $detalles->where('c.placa','LIKE',$placa.'%');
        // // }

		// if ($tipo != 3) {
        //     $detalles = $detalles->where('detcotper.idPersonal','=',$id);
        // } else {
        //     $detalles = $detalles->where('c.idCliente','=',$id);
        // }
        // $detalles=$detalles->where('marcaauto.id',$marca);
        // $detalles = $detalles->select('marcaauto.nombre as marc',DB::Raw("CONCAT(trab.apellidos,', ', trab.nombres) as asignado"),'cliente.documento',DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END) as cliente"),'detallecotizacionorden.tiempoEstimado as tiempo', 'detallecotizacionorden.descripcion as servicio','detallecotizacionorden.id','catp.nombre as cargo', DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%d/%m/%Y') as fecha"),'c.id as idCotizacion','detallecotizacionorden.enProceso','c.placa as placaAuto')
        // ->get();
		// $detalles = DetalleCotizacionOrden::leftjoin('detalleordentrabajo as deto','deto.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
        // ->leftJoin('cotizacion as c','c.id','=','detallecotizacionorden.idCotizacion')
        // ->leftJoin('persona as cliente','cliente.id','=','c.idCliente')
        // ->leftJoin('detallecotizacionordenpersonal as detcotper','detcotper.idDetalleCotizacionOrden','=','detallecotizacionorden.id')
        // ->leftJoin('trabajador as trab','trab.id','=','detallecotizacionorden.idPersonalAsigna')
        // ->leftJoin('categoriapersonal as catp','catp.id','=','trab.idCategoriaPersonal')
        // ->whereNull('detcotper.deleted_at');

       
        // $detalles = $detalles->select(DB::Raw("CONCAT(trab.apellidos,', ', trab.nombres) as asignado"),'cliente.documento',DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END) as cliente"),'detallecotizacionorden.tiempoEstimado as tiempo', 'detallecotizacionorden.descripcion as servicio','detallecotizacionorden.id','catp.nombre as cargo', DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%d/%m/%Y') as fecha"),'c.id as idCotizacion','detallecotizacionorden.enProceso','c.placa as placaAuto')
        // ->get();
	//   dd($detalles);  
	  	// $detalles2 = DetalleCotizacionOrden::
	    //           leftJoin('detalleordentrabajo as dto','detallecotizacionorden.idDetalleOrdenTrabajo','=','dto.id')		
		// 			->leftJoin('detallecotizacionordenpersonal as dcop','detallecotizacionorden.id','=','dcop.idDetalleCotizacionOrden')
		// 			->leftJoin('cotizacion as coti','detallecotizacionorden.idCotizacion','=','coti.id')
		// 			->leftJoin('persona as cliente','coti.idCliente','=','cliente.id')
		// 			->leftJoin('trabajador as trab','dcop.idPersonal','=','trab.id')
		// 			->leftJoin('cita','cliente.id','=','cita.idCliente')
		// 			->leftJoin('marcaauto','cita.idMarcaAuto','=','marcaauto.id')
		// 			->whereNull('dcop.deleted_at')
		// 			->whereNotNull('cita.deleted_at')
		// 			->where('coti.placa',$placa)
		// 			->where('cliente.id','=',$id);

		// if ($fechaI != '') {
		// 	$detalles2=$detalles2->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'>=',$fechaI);
		// }

		// if ($fechaF != '') {
		// 	$detalles2=$detalles2->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'<=',$fechaF);
		// }

		// $detalles2=$detalles2->select('coti.placa as placacotizacion','marcaauto.id as idmarca',
		// 			'marcaauto.nombre as marcanombre','trab.id as idtrabajador',
		// 			'detallecotizacionorden.id as iddetallecotizacionorden',
		// 			'cita.id as cita', 
		// 			DB::Raw("CONCAT(trab.apellidos,' ', trab.nombres) as asignado"),
		// 			'cliente.documento', 
		// 			DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END) as cliente"),
		// 			DB::Raw("CONCAT(FORMAT(detallecotizacionorden.tiempoEstimado,2),' min.') as tiempo"),
		// 			'detallecotizacionorden.descripcion as servicio','detallecotizacionorden.id',
		// 			DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%d/%m/%Y') as fecha"), 
		// 			'detallecotizacionorden.enProceso','cita.placa as placaAuto')
		// 			->get();
		
		// $arrayValid=[];

		// $arreglo=[];
		// $cliente = null;
		// foreach($detalles2 as $value) {
		// 	if (is_null($cliente)) {
		// 		$cliente = ['cliente'=> $value->cliente, 'documento' => $value->documento, 'marca_auto' => $value->marcanombre];
		// 	}

		// 	// $idMarca=$value->idMarca;
		// 	$marcaautos=$value->marcanombre;

		// 	$trabajadores=[];
		// 	$id=$value->iddetallecotizacionorden;
		// 	// $validarTrabajadores=[];
		// 	foreach($detalles2 as $act) {


		// 		if ($act->iddetallecotizacionorden == $value->iddetallecotizacionorden) {
		// 			$trabajadores[] = $act->asignado;
		// 		}
		
		// 		// if(!in_array($act->idtrabajador,$validarTrabajadores)){
		// 		// 	if($act->iddetallecotizacionorden == $act->idacomparar){
		// 		// 		$trabajadores[]=$act->asignado;
		// 		// 		$validarTrabajadores[]=$act->idtrabajador;
		// 	    //   }
		// 		// }
				 
		// 	}
			  
        // //    if(!in_array($value->iddetallecotizacionorden,$arrayValid)){
		// 	if($value->placacotizacion == $value->placaAuto) {
        //        $arreglo[]=[
		// 		     "id"=>$value->iddetallecotizacionorden,
		// 			 "trabajadores"=>implode(',',$trabajadores),
		// 			//  "documento"=>$value->documento,
		// 			//  "cliente"=>$value->cliente,
		// 			 "servicio"=>$value->servicio,
		// 			 "fecha"=>$value->fecha,
		// 			 "proceso"=>$value->enProceso,
		// 			 "tiempo"=>$value->tiempo,
		// 			 "placa"=>$value->placaAuto,
		// 			//  'marcanombre'=>$value->marcanombre,
		// 			 'idmarca'=>$value->idmarca,
		// 			 'placacotizacion'=>$value->placacotizacion
		// 	   ];
		// 	   $arrayValid[]=$value->iddetallecotizacionorden;
		// 	}
		//    }

		// }	

		// $arreglofiltroMarca=[];
		// $validMarcas= [];
		// foreach ($arreglo as $value) {
		// 	if (!in_array($value['idmarca'], $validMarcas)) {
		// 		$arreglofiltroMarca[] = $value;
		// 	}
		// }	

		// if($marca!=''){
		// 	foreach ($arreglo as $value) {
		// 		if($marca==$value["idmarca"]){
					
		// 			$arreglofiltroMarca[]=$value;
		// 		}
		// 	}
		// 	$arreglo=$arreglofiltroMarca;
		// }
		// // dd($arreglofiltroMarca);
		// dd($arreglo);

		$arreglo = [];
		foreach($detalles as $item) {
			$items = explode('@@',$item->servicios);
			$arreglo[] = [
				'id' => $item->id,
				'cliente' => $item->cliente,
				'documento' => $item->documento,
				'fecha' => $item->fecha,
				'finaliza' => $item->finaliza,
				'inicia' => $item->inicia,
				'orden' => $item->orden,
				'placa' => $item->placa,
				'idVenta' => $item->ventaId,
				'servicios' => $items,
			]; 
		}
        return ['detalles' => $arreglo];
    } 

	public function getTrabajos(Request $request) {
		$tipoT = $request->get('tipo');
		// $tipo = Auth::user()->categoriaPersonalId;
        $id = Auth::user()->usuarioId;
		$filtro 	 = $request->get('filtro');
    	$descripcion = $request->get('descripcion');
    	
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
        $fechaI = $request->get('fechaI');
        $fechaF = $request->get('fechaF');
	
        // $cliente = $request->get('cliente');
   
		$detalles = DetalleCotizacionOrden::leftjoin('detalleordentrabajo as deto','deto.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
		->leftJoin('trabajador as per_as','per_as.id','=','detallecotizacionorden.idPersonalAsigna')
        ->leftJoin('cotizacion as c','c.id','=','detallecotizacionorden.idCotizacion')
        ->leftJoin('persona as cliente','cliente.id','=','c.idCliente')
        ->leftJoin('detallecotizacionordenpersonal as detcotper','detcotper.idDetalleCotizacionOrden','=','detallecotizacionorden.id')
        ->leftJoin('trabajador as trab','trab.id','=','detcotper.idPersonal')
        ->leftJoin('categoriapersonal as catp','catp.id','=','trab.idCategoriaPersonal')
        ->whereNull('detcotper.deleted_at');

    	if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'ruc':
    				if ($descripcion <> '')	
						$detalles = $detalles->where('cliente.documento','LIKE', $descripcion.'%');
					break;
				case 'cliente':
					if ($descripcion <> '')	{
						$detalles = $detalles->where(DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END)"),'LIKE', $descripcion.'%');
					}
					break;
				case 'placa': 
					if ($descripcion <> '') {
						$detalles = $detalles->where('c.placa','=',$descripcion);
					}
				default:
					if ($descripcion <> '') {
						$detalles = $detalles->where('detallecotizacionorden.descripcion','LIKE', $descripcion.'%');
					}
    		}
    	}

        if ($fechaI != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'>=',$fechaI);
        }

		if ($fechaF != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'<=',$fechaF);
        }

        if ($tipoT != 3) {
			$detalles = $detalles->where(function ($qq) use ($id) {
				$qq->where('detcotper.idPersonal','=',$id)
					->orWhere('per_as.id','=',$id);
			});
        } else {
            $detalles = $detalles->where('c.idCliente','=',$id);
        }

        $detalles = $detalles->select(DB::Raw("CONCAT(trab.apellidos,', ', trab.nombres) as asignado"),DB::Raw("CONCAT(per_as.apellidos,', ', per_as.nombres) as asigna"),'cliente.documento',DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END) as cliente"),'detallecotizacionorden.tiempoEstimado as tiempo', 'detallecotizacionorden.descripcion as servicio','detallecotizacionorden.id','catp.nombre as cargo', DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%d/%m/%Y') as fecha"),'c.id as idCotizacion','detallecotizacionorden.enProceso','c.placa as placaAuto', DB::Raw("DATE_FORMAT(detallecotizacionorden.created_at,'%d/%m/%Y') as fechaRegistro"),'detallecotizacionorden.situacion');
	   
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
			$arrPag = [['opc' => '1']];
			$page = '1';
			$inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $detalles->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['trabajos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Trabajo':' Trabajos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
        // return ['trabajos' => $detalles];
	}

	public function excel ($filtro, $descripcion, $genero, $categoriaId) {
		if ($filtro == 'null') {$filtro = '';}
		if ($descripcion == 'null') {$descripcion = '';}
		if ($genero == 'null') {$genero = '';}
		if ($categoriaId == 'null') {$categoriaId = '';}
	
    	$personal = Personal::leftjoin('departamento as d','d.codigo','=','trabajador.idDepartamento')
    					->leftjoin('provincia as p','p.codigo','=','trabajador.idProvincia')
    					->leftjoin('distrito as dist','dist.codigo','=','trabajador.idDistrito')
    					->leftjoin('categoriaPersonal as cat','cat.id','=','trabajador.idCategoriaPersonal');

                     
    	if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'dni':
    				if ($descripcion <> '')	
						$personal = $personal->where('trabajador.dni','LIKE', $descripcion.'%');
    				break;
	    		case 'apellidos_nombres':
	    				if ($descripcion <> '')	
							$personal = $personal->where(DB::raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres)"),'LIKE', $descripcion.'%');
	    				break;
	    		case 'correo':
	    				if ($descripcion <> '')	
							$personal = $personal->where('trabajador.correoE','LIKE', $descripcion.'%');
	    				break;
	    		case 'telefono':
    				if ($descripcion <> '')	
						$personal = $personal->where('trabajador.telefono','LIKE', $descripcion.'%');
    				break;
    			default:
    				if ($descripcion <> '')
                        $personal = $personal->where('trabajador.direccion','LIKE', $descripcion.'%');
                    break;
    		}
    	}

    	if ($categoriaId != '' && $categoriaId != 'todo') {
    		$personal = $personal->where('trabajador.idCategoriaPersonal','=',$categoriaId);
    	}

    	if ($genero != '' && $genero != 'todo') {
    		$personal = $personal->where('trabajador.genero','=',$genero);
    	}
    	
    	$personal = $personal->select('trabajador.id','trabajador.dni',DB::raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres) as personal"),
    					DB::raw("(CASE WHEN trabajador.genero = 'M' THEN 'MASCULINO' ELSE 'FEMENINO' END) as genero"), DB::raw("(CASE WHEN trabajador.correoE IS NULL THEN '-' ELSE trabajador.correoE END) as correoE"),'d.nombre as departamento',DB::raw("CONCAT(trabajador.direccion,' - ', dist.nombre,' (',p.nombre,') ') as direccion"),'cat.nombre as tipoUsuario',DB::raw("DATE_FORMAT(trabajador.fechaNacimiento,'%d/%m/%Y') as fechaNacimiento"), 'trabajador.telefono')
    					->orderBy('personal','ASC')
    					->orderBy('cat.id','ASC');

		$lista = $personal->get();
		
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','LISTADO DE PERSONAL');
		$hoja1->mergeCells('A1:I1');
		$hoja1->getStyle('A1:I1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','DNI');
		$hoja1->setCellValue('C2','PERSONAL');
		$hoja1->setCellValue('D2','GÉNERO');
		$hoja1->setCellValue('E2','FECHA DE NACIMIENTO');
		$hoja1->setCellValue('F2','CORREO ELECTRÓNICO');
		$hoja1->setCellValue('G2','TELÉFONO');
		$hoja1->setCellValue('H2','DIRECCIÓN');
		$hoja1->setCellValue('I2','TIPO DE USUARIO');
	
		$hoja1->getStyle('A2:I2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;

		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->dni);
			$hoja1->setCellValue('C'.$j,$value->personal);
			$hoja1->setCellValue('D'.$j,$value->genero);
			$hoja1->setCellValue('E'.$j,$value->fechaNacimiento);
			$hoja1->setCellValue('F'.$j,$value->correoE);
			$hoja1->setCellValue('G'.$j,$value->telefono);
			$hoja1->setCellValue('H'.$j,$value->direccion);
			$hoja1->setCellValue('I'.$j,$value->tipoUsuario);
		
			$hoja1->getStyle('A'.$j.':I'.$j)->applyFromArray($this->estilo_content);
			$cont++;
			$j++;
		}

		foreach(range('A','I') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="personal.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

}
