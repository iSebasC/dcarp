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
use App\Models\User;

use App\Models\OrdenTrabajo;
use App\Models\DetalleOrdenTrabajo;
use App\Models\DetalleCotizacionOrden;

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Serie;
use App\Models\Cita;

use App\Models\StockProductoDetalle;
use App\Models\StockProductoDetalleSalida;

use App\Models\StockProducto;
use App\Models\Personal;
use App\Models\DetalleOrdenPersonal;
use App\Models\ImagenDetalle;
use App\Models\ImagenTemporal;

use App\Models\OpcionInventario;
use App\Models\OpcionCalidad;
use App\Models\OpcionManejo;
use App\Models\OpcionTaller;

use App\Models\RespuestaCheckInventario;
use App\Models\RespuestaCheckCalidad;
use App\Models\RespuestaCheckManejo;

use App\Models\RespuestaCalidadOrden;
use App\Models\RespuestaManejoOrden;
use App\Models\RespuestaTallerOrden;
use App\Models\RespuestaCheckTaller;
use App\Models\MotivoTiempoLibre;
use App\Models\TiempoDetalle;

use App\Models\PreguntaEncuesta;
use App\Models\RespuestaPreguntaEncuesta;
use App\Models\VerificacionCheckList;
use App\Models\Area;
use App\Models\Reclamo;
use App\Models\MensajeSistema;
use App\Models\Oportunidad;

use Fpdf;
use App\Libraries\Funciones;

use DB;
use Auth;
use Image;
use Validator;
use Session;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

class OrdenTrabajoController extends Controller
{
	public $almacenId = 2;
	public $tiendaId  = 1;
    public $urlPattern = '/storage/imagenes/';
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

    
    public $estilo_header2 = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => '033497',
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

    public $estilo_content2 = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'E1FF0B',
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

    public $estilo_contentR = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'FFCCD3',
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
    public $estilo_contentA = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'FFE0CC',
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
    public $estilo_contentV = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'C3F2E1',
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
    public $estilo_contentB = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'F2F2F2',
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

    public function excel (Request $request) {
    	$tipoT = Auth::user()->categoriaPersonalId;
        $documento 	 = $request->get('documento');
		$cliente     = $request->get('cliente');
		$servicio        = $request->get('servicio');
    	$placa        = $request->get('placa');


        $fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');
    	
        
        $id = Auth::user()->usuarioId;
        
    
        // $cliente = $request->get('cliente');
   
        $detalles = DetalleCotizacionOrden::leftjoin('detalleordentrabajo as deto','deto.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
        ->leftJoin('ordentrabajo as or','or.id','=','deto.idOrdenTrabajo')
		->leftJoin('trabajador as per_as','per_as.id','=','detallecotizacionorden.idPersonalAsigna')
        ->leftJoin('cotizacion as c','c.id','=','detallecotizacionorden.idCotizacion')
        ->leftJoin('persona as cliente','cliente.id','=','c.idCliente')
        ->leftJoin('detallecotizacionordenpersonal as detcotper','detcotper.idDetalleCotizacionOrden','=','detallecotizacionorden.id')
        ->leftJoin('trabajador as trab','trab.id','=','detcotper.idPersonal')
        ->leftJoin('categoriapersonal as catp','catp.id','=','trab.idCategoriaPersonal')
        ->leftJoin('tiempodetalle as tempdet','tempdet.idDetalleOrdenTrabajo','=','detallecotizacionorden.id')
        ->leftJoin('trabajador as pe2','pe2.id','=','tempdet.idPersonal')
        ->leftJoin('motivotiempolibre as motlibre','motlibre.id','=','tempdet.idMotivo')
        //->whereNotIn('tempdet.idMotivo',[7,8])
        ->whereNull('detcotper.deleted_at')
        ->where('cliente.documento','LIKE', '%'.$documento.'%')
        ->where(DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END)"),'LIKE', '%'.$cliente.'%')
        ->where('c.placa','LIKE','%'.$placa.'%')
        ->where('detallecotizacionorden.descripcion','LIKE', '%'.$servicio.'%');

        if ($fechaI != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'>=',$fechaI);
        }

		if ($fechaF != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'<=',$fechaF);
        }


        if ($tipoT != 3) {
            if (!in_array($tipoT,[1,2])) {
                $detalles = $detalles->where(function ($qq) use ($id) {
                    $qq->where('detcotper.idPersonal','=',$id)
                        ->orWhere('per_as.id','=',$id);
                });    
            } 
        } else {
            $detalles = $detalles->where('c.idCliente','=',$id);
        }


        // $detalles = $detalles->where(function ($qq) use ($id) {
        //     $qq->where('detcotper.idPersonal','=',$id)
        //         ->orWhere('per_as.id','=',$id);
        // });
    
        $detalles = $detalles->select('detallecotizacionorden.id as idcts', DB::Raw("CONCAT(trab.apellidos,', ', trab.nombres) as asignado"), 
        DB::Raw("CONCAT(per_as.apellidos,', ', per_as.nombres) as asigna"), 
        DB::Raw("CONCAT(pe2.apellidos,', ', pe2.nombres) as asigna2"),
        DB::Raw("CONCAT(per_as.apellidos,', ', per_as.nombres) as asigna"),'cliente.documento',
        DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END) as cliente"), 
        'detallecotizacionorden.descripcion as servicio','catp.nombre as cargo',
        DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%d/%m/%Y %H:%i:%s') as fecha"), 
        DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaFin,'%d/%m/%Y %H:%i:%s') as fecha2"),
        'c.id as idCotizacion','detallecotizacionorden.enProceso','c.placa as placaAuto', 
        DB::Raw("DATE_FORMAT(detallecotizacionorden.created_at,'%d/%m/%Y') as fechaRegistro"),
        'detallecotizacionorden.situacion', 
        DB::Raw("DATE_FORMAT(tempdet.inicio,'%d/%m/%Y %H:%i:%s') as fechaI"), 
        DB::Raw("DATE_FORMAT(tempdet.fin,'%d/%m/%Y %H:%i:%s') as fechaF"), 
        DB::Raw("TIMESTAMPDIFF(SECOND,tempdet.inicio,tempdet.fin)/60 as minutos"),
        'motlibre.nombre as motivo', DB::Raw("CONCAT('OD', LPAD(or.serie,2,'0') ,'-', LPAD(or.numero,8,'0')) as numero"),
        'deto.id', DB::Raw("detallecotizacionorden.tiempoEstimado as original"),
        'detallecotizacionorden.tiempoLibre as tiempoL', 'tempdet.idMotivo')
        ->orderBy('detallecotizacionorden.created_at','ASC')
        ->orderBy('or.id','ASC')
        ->orderBy('deto.id','ASC');
	   
		$lista = $detalles->get();
        
        $idPase = 0;
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Evaluación");
		$hoja1->setCellValue('A1','EVALUACIÓN DE PRODUCTIVIDAD');
		$hoja1->mergeCells('A1:K1');
		$hoja1->getStyle('A1:K1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','TIPO');
		$hoja1->setCellValue('B2','ACTIVIDAD');
		$hoja1->setCellValue('C2','ASIGNADO POR');
		$hoja1->setCellValue('D2','ASIGNADO A');
		$hoja1->setCellValue('E2','ORDEN RELACIONADA');
		$hoja1->setCellValue('F2','SITUACIÓN');
		$hoja1->setCellValue('G2','F. INICIO');
        $hoja1->setCellValue('H2','F. FIN');
        $hoja1->setCellValue('I2','DIFERENCIA (MIN.)');
		$hoja1->setCellValue('J2','T. ESTIMADO');
        $hoja1->setCellValue('K2','T. DE PEMISOS');
        
		$hoja1->getStyle('A2:K2')->applyFromArray($this->estilo_header);
		
        $j = 3;
		foreach ($lista as $value) {
		    //echo 'ID PASE: '. $idPase .'<br/>';
            // echo $value->idcts. '<==>'. $value->servicio.'<br/>';
    		if ($idPase != $value->idcts) {
                $hoja1->setCellValue('A'.$j,'ACTIVIDAD');
	    		$hoja1->setCellValue('B'.$j,$value->servicio);
	        	$hoja1->setCellValue('C'.$j,$value->asigna);
	         	$hoja1->setCellValue('D'.$j,$value->asignado);
	         	$hoja1->setCellValue('E'.$j,$value->numero);
	         	$hoja1->setCellValue('F'.$j,($value->situacion == 'R'?'Revisado':'Pendiente'));
	        	$hoja1->setCellValue('G'.$j,$value->fecha);
            	$hoja1->setCellValue('H'.$j,$value->fecha2);
            	$hoja1->setCellValue('I'.$j,'=ROUND(((H'.$j.'-G'.$j.')*1440),2)');
            	$hoja1->setCellValue('J'.$j,number_format($value->original,2,'.',' '));
            	$hoja1->setCellValue('K'.$j,number_format($value->tiempoL,2,'.',' '));
                $hoja1->getStyle('A'.$j.':K'.$j)->applyFromArray($this->estilo_header2);
                
                $j++;
                $hoja1->setCellValue('A'.$j,'INT. DE TIEMPO');
	    		$hoja1->setCellValue('B'.$j,$value->motivo);
	        	$hoja1->setCellValue('C'.$j,$value->asigna2);
	         	$hoja1->setCellValue('D'.$j,'-');
	         	$hoja1->setCellValue('E'.$j,$value->numero);
	         	$hoja1->setCellValue('F'.$j,'-');
	        	$hoja1->setCellValue('G'.$j,$value->fechaI);
            	$hoja1->setCellValue('H'.$j,$value->fechaF);
            	$hoja1->setCellValue('I'.$j,number_format($value->minutos,2,'.',' '));
             	$hoja1->setCellValue('J'.$j,'-');
	         	$hoja1->setCellValue('K'.$j,'-');
	    
                $hoja1->getStyle('A'.$j.':K'.$j)->applyFromArray($this->estilo_content);
	        
            } else {
                //if (!in_array($value->idMotivo,[7,8])) {
                    $hoja1->setCellValue('A'.$j,'INT. DE TIEMPO');
    	    		$hoja1->setCellValue('B'.$j,$value->motivo);
    	        	$hoja1->setCellValue('C'.$j,$value->asigna2);
    	         	$hoja1->setCellValue('D'.$j,'-');
    	         	$hoja1->setCellValue('E'.$j,$value->numero);
    	         	$hoja1->setCellValue('F'.$j,'-');
    	        	$hoja1->setCellValue('G'.$j,$value->fechaI);
                	$hoja1->setCellValue('H'.$j,$value->fechaF);
                	$hoja1->setCellValue('I'.$j,number_format($value->minutos,2,'.',' '));
                 	$hoja1->setCellValue('J'.$j,'-');
    	         	$hoja1->setCellValue('K'.$j,'-');
    	    
                    $hoja1->getStyle('A'.$j.':K'.$j)->applyFromArray($this->estilo_content);
                    
                   if (!in_array($value->idMotivo,[7,8])) {
                    $hoja1->getStyle('I'.$j)->applyFromArray($this->estilo_content2);
                   }
            }

            $idPase = $value->idcts;
            $j++;
		}

        foreach(range('A','K') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		// $objWriter->setPreCalculateFormulas(true);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Evaluacion-'.date('Y-m-d H:i').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		
	}

    public function excelProductividad (Request $request) {
        $documento = !is_null($request->get('doc'))?$request->get('doc'):'';
        $cliente = !is_null($request->get('cli'))?$request->get('cli'):'';
        $orden = !is_null($request->get('comp'))?$request->get('comp'):'';
        $placa = !is_null($request->get('pl'))?$request->get('pl'):'';
        $fechaI = !is_null($request->get('fi'))?$request->get('fi'):'';
        $fechaF = !is_null($request->get('ff'))?$request->get('ff'):'';
        
        $ordenes = DB::table('ordentrabajo as ot')
            ->leftjoin('cita as ct','ct.id','=','ot.idCita')
            ->leftjoin('persona as cl','cl.id','=','ot.idCliente')
            ->leftjoin('trabajador as asig','asig.id','=','ot.idPersonal')
            ->leftjoin('trabajador as asignado','asignado.id','=','ot.idAsignado')
            ->where('cl.documento','LIKE', '%'.$documento.'%')
            ->where('ot.placa','LIKE', '%'.$placa.'%')
            ->where(DB::Raw("CONCAT(ot.serie,'-',ot.numero)"),'LIKE', '%'.$orden.'%')
            ->where(function ($qq) use ($cliente) {
                $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                    ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
            });

        if ($fechaI != '') {
            $ordenes = $ordenes->where('ot.fecha','>=', $fechaI);
        }

        if ($fechaF != '') {
            $ordenes = $ordenes->where('ot.fecha','<=', $fechaF);
        }

        $ordenes = $ordenes->select(
                'ot.id', DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"), 
                DB::Raw("CONCAT(asig.apellidos,' ', asig.nombres) asigno"),
                DB::Raw("CONCAT(asignado.apellidos,' ', asignado.nombres) asignado"),
                DB::Raw("DATE_FORMAT(ot.inicia,'%d/%m/%Y %h:%i:%s %p') as inicia"),
                DB::Raw("DATE_FORMAT(ot.finaliza,'%d/%m/%Y %h:%i:%s %p') as finaliza"),
                'ot.observaciones',
                DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaRegistro"),
                DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden"),
                DB::Raw("CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) as cita"),
                DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa', 'ot.situacionFacturado', 'ot.situacion',
                'cl.documento as documentoCliente',
                DB::Raw("(CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos, ', ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                DB::Raw("(SELECT SUM(detcotor.tiempoEstimado) FROM detallecotizacionorden as detcotor 
                INNER JOIN detalleordentrabajo as detot ON detot.id = detcotor.idDetalleOrdenTrabajo
                WHERE detcotor.deleted_at IS NULL AND detot.idOrdenTrabajo = ot.id) as tiempoEstimado"),
                DB::Raw("(SELECT GROUP_CONCAT(CONCAT(A.tiempo,' ',A.unidadTiempo)) FROM 
                (SELECT SUM(td.tiempo) as tiempo, td.unidadTiempo, td.idOrdenTrabajo 
                FROM tiempodetalle as td 
                GROUP BY td.unidadTiempo, td.idOrdenTrabajo ORDER BY td.unidadTiempo) A 
                WHERE A.idOrdenTrabajo = ot.id) as tiempoDetalle"),
                DB::Raw("TIMESTAMPDIFF(MINUTE, ot.inicia, ot.finaliza) as duracion")
                )
                ->orderBy('ot.created_at','ASC')
                ->get();


        $excel = new PHPExcel(); 
        $excel->setActiveSheetIndex(0);
        $hoja1 = $excel->getActiveSheet();
        $hoja1->setTitle("Evaluación");
        $hoja1->setCellValue('A1','EVALUACIÓN DE PRODUCTIVIDAD');
        $hoja1->mergeCells('A1:R1');
        $hoja1->getStyle('A1:R1')->applyFromArray($this->estilo_header);
        
        $hoja1->setCellValue('A2','FECHA');
        $hoja1->setCellValue('B2','N° ORDEN');
        $hoja1->setCellValue('C2','N° CITA');
        $hoja1->setCellValue('D2','DOC. CLIENTE');
        $hoja1->setCellValue('E2','CLIENTE');
        $hoja1->setCellValue('F2','PLACA');
        $hoja1->setCellValue('G2','ASIGNÓ');
        $hoja1->setCellValue('H2','ASIGNADO A');
        $hoja1->setCellValue('I2','TOTAL');
        $hoja1->setCellValue('J2','SITUACIÓN');
        $hoja1->setCellValue('K2','SIT. FACTURADO');
        $hoja1->setCellValue('L2','T. ESTIMADO (MIN.)');
        $hoja1->setCellValue('M2','F. INICIO');
        $hoja1->setCellValue('N2','F. FIN');
        $hoja1->setCellValue('O2','DURACIÓN (MIN.)');
        $hoja1->setCellValue('P2','T. DE INCIDENCIAS');
        $hoja1->setCellValue('Q2','DURACION REAL (MIN.)');
        $hoja1->setCellValue('R2','OBSERVACIONES');
        $hoja1->setCellValue('S2','REGISTRADO EL');
        
         
		$hoja1->getStyle('A2:S2')->applyFromArray($this->estilo_header);
		
        $j = 3;
        foreach ($ordenes as $value) {
            $hoja1->setCellValue('A'.$j,$value->fecha);
            $hoja1->setCellValue('B'.$j,$value->orden);
            $hoja1->setCellValue('C'.$j,$value->cita);
            $hoja1->setCellValue('D'.$j,$value->documentoCliente);
            $hoja1->setCellValue('E'.$j,$value->cliente);
            $hoja1->setCellValue('F'.$j,$value->placa);
            $hoja1->setCellValue('G'.$j,$value->asigno);
            $hoja1->setCellValue('H'.$j,$value->asignado);
            $hoja1->setCellValue('I'.$j,$value->total);
            $hoja1->setCellValue('J'.$j,$value->situacion=='I'?'Iniciada':($value->situacion=='F'?'Finalizada':'Pendiente'));
            $hoja1->setCellValue('K'.$j,$value->situacionFacturado=='P'?'Si':'No');
            $hoja1->setCellValue('L'.$j,$value->tiempoEstimado);
            $hoja1->setCellValue('M'.$j,$value->inicia);
            $hoja1->setCellValue('N'.$j,$value->finaliza);
            $hoja1->setCellValue('O'.$j,$value->duracion);
            $hoja1->setCellValue('P'.$j,$value->tiempoDetalle);
            $t = 0;
            if (!is_null($value->tiempoDetalle)) {
                $t = $this->calcularTiempoIncidencia($value->tiempoDetalle);
            }
            $hoja1->setCellValue('Q'.$j,$value->duracion - $t);         
            $hoja1->setCellValue('R'.$j,$value->observaciones);
            $hoja1->setCellValue('S'.$j,$value->fechaRegistro);
            
            $hoja1->getStyle('A'.$j.':S'.$j)->applyFromArray($this->estilo_content);
            
            $j++;
        }

        foreach(range('A','R') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		// $objWriter->setPreCalculateFormulas(true);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="EvaluacionProductividad-'.date('Y-m-d').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
    }

    public function calcularTiempoIncidencia($tiempo) {
       $tiempos =  explode(',',$tiempo);
       $tiempoR = 0;

       foreach ($tiempos as $t) {
        if (!is_null($t)) {
            $tt = explode(' ', $t);
            if ($tt[1] == 'minuto(s)') {
                $tiempoR +=floatval($tt[0]);
            } elseif($tt[1] == 'dia(s)') {
                $tiempoR +=floatval($tt[0]) * 24 * 60;
            } elseif($tt[1] == 'hora(s)') {
                $tiempoR +=floatval($tt[0]) * 60;
            }
        } 
       }

       return $tiempoR;
    }
    public function getTrabajos(Request $request) {
		$tipoT = Auth::user()->categoriaPersonalId;
        // $tipo = Auth::user()->categoriaPersonalId;
        $id = Auth::user()->usuarioId;
		$cliente 	 = $request->get('cliente');
    	$documento 	 = $request->get('documento');
    	$placa 	     = $request->get('placa');
    	$servicio    = $request->get('servicio');
    	$fechaI      = $request->get('fechaI');
        $fechaF      = $request->get('fechaF');
	

        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
        
        // $cliente = $request->get('cliente');
   
		$detalles = DetalleCotizacionOrden::leftjoin('detalleordentrabajo as deto','deto.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
		->leftJoin('trabajador as per_as','per_as.id','=','detallecotizacionorden.idPersonalAsigna')
        ->leftJoin('cotizacion as c','c.id','=','detallecotizacionorden.idCotizacion')
        ->leftJoin('persona as cliente','cliente.id','=','c.idCliente')
        ->leftJoin('detallecotizacionordenpersonal as detcotper','detcotper.idDetalleCotizacionOrden','=','detallecotizacionorden.id')
        ->leftJoin('trabajador as trab','trab.id','=','detcotper.idPersonal')
        ->leftJoin('categoriapersonal as catp','catp.id','=','trab.idCategoriaPersonal')
        ->whereNull('detcotper.deleted_at')
        ->where('cliente.documento','LIKE', '%'.$documento.'%')
        ->where(DB::Raw("(CASE WHEN cliente.razonSocial IS NULL THEN CONCAT(cliente.apellidos, ', ', cliente.nombres) ELSE cliente.razonSocial END)"),'LIKE', 
        '%'.$cliente.'%')
        ->where('c.placa','LIKE','%'.$placa.'%')
        ->where('detallecotizacionorden.descripcion','LIKE', '%'.$servicio.'%');

  
        if ($fechaI != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'>=',$fechaI);
        }

		if ($fechaF != '') {
            $detalles = $detalles->where(DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%Y-%m-%d')"),'<=',$fechaF);
        }

        if ($tipoT != 8) { // VALIDAR SI ES CLIENTE
            if (!in_array($tipoT,[1,2])) { // VALIDAR SI ES ADMIN
                $detalles = $detalles->where(function ($qq) use ($id) {
                    $qq->where('detcotper.idPersonal','=',$id)
                        ->orWhere('per_as.id','=',$id);
                });    
            } 
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
			$arrPag = [['opc' => '1', 'bloqueado' => true]];
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

    public function getPreguntasEncuestas (Request $request) {
        $preguntas = PreguntaEncuesta::where('nombre', 'NOT LIKE', '%Estado de Contactabilidad%')->get();

        return ['estado' => true, 'preguntas' => $preguntas];
    }

    public function guardarEncuesta (Request $request) {
        $errors = $this->validarEncuesta($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{

                $preguntas = PreguntaEncuesta::all();
                $puntuacion = 0;
                foreach ($preguntas as $item) {
                    $puntaje = $request->get('estrellasP_'.$item->id);
                    $rpta = new RespuestaPreguntaEncuesta;
                    $rpta->idPregunta = $item->id;
                    $rpta->idOrden    = $request->get('idOrden');
                        
                    if ($item->conRespuesta == '1') {
                        if(!is_null($puntaje)) {
                            $rpta->puntuacion = $puntaje;
                            $puntuacion +=$rpta->puntuacion;
                        } else {
                            $band = false;
                            $errors[] = 'Indique Puntaje del Item '.$item->nombre.' por favor';
                        }        
                    } else {
                        if ($item->nombre == 'Estado de Contactabilidad') {
                            $rpta->estadoContactabilidad = $request->get('estado');
                        } else {
                            $rpta->observacion = $request->get('observaciones_encuesta');
                        }
                    }

                    $rpta->save();
                }

                if (!$band) {
                    DB::rollback();
                } else {
                    $orden = OrdenTrabajo::find($request->get('idOrden'));
                    if (!is_null($orden)) {
                        $orden->puntuacionEncuesta = $puntuacion;
                        $orden->update();
                        $errors[] = 'Encuesta Guardada Correctamente';
                    } else {
                        DB::rollback();
                        $band = false;
                        $errors[] = 'Orden de Trabajo no Encontrada';
                    }
                }

		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage(); //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
    }
    
    public function getAll (Request $request) {
        $documento 	 = $request->get('documento');
		$cliente     = $request->get('cliente');
		$comprobante = $request->get('comprobante');
		$cita        = $request->get('cita');
		$trabajador = $request->get('trabajador');
		$placa        = $request->get('placa');


        $fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');
        $isFacturado = $request->get('isFacturado');
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
        $ordenes = DB::table('ordentrabajo as ot')
                      ->leftjoin('cita as c','c.id','=','ot.idCita')
                      ->leftjoin('persona as cl','cl.id','=','ot.idCliente')
                      ->leftjoin('trabajador as t','t.id','=','ot.idPersonal')
                      ->leftjoin('trabajador as t2','t2.id','=','ot.idAsignado')
                      ->where(function ($qq) use ($cliente) {
                            $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                               ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
                      })
                      ->where('cl.documento','LIKE', '%'.$documento.'%')
                      ->where(DB::Raw("CONCAT(ot.serie,'-',ot.numero)"),'LIKE', '%'.$comprobante.'%')
                      ->where(DB::Raw("CONCAT(c.serie,'-',c.numero)"),'LIKE', '%'.$cita.'%')
                      ->where(DB::Raw("CONCAT(t.apellidos,' ',t.nombres)"),'LIKE', '%'.$trabajador.'%')
                      ->where('ot.placa','LIKE', '%'.$placa.'%');

		if ($fechaI != '') {
			$ordenes = $ordenes->where('ot.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$ordenes = $ordenes->where('ot.fecha','<=',$fechaF);
		}

        if ($isFacturado != '') {
			$ordenes = $ordenes->where('ot.situacionFacturado',$isFacturado);
		}

        $ordenes =  $ordenes->select('ot.id','ot.situacionFacturado', DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa',
        DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as documentocita"), 
        DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa',
        DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as documento") ,
        DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
        'cl.documento as doc', 
        DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),'ot.situacion', 
        DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
        DB::Raw("CONCAT(t2.nombres,' ',t2.apellidos) as asignadoA"), 
        DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente')
        ->orderBy('ot.created_at','DESC');
		


		//    ->orderBy('cotizacion.fecha','ASC');

		$lista = $ordenes->get();
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
		
		$lista = $ordenes->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

    	return ['ordenes' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Orden':' Órdenes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

    public function getPdfCheckListInventario($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id','orden.placa', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.telefono', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin, '@@', (CASE WHEN ct.marcamodelo IS NULL THEN '-' ELSE ct.marcamodelo END))  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje != '-' AND ct.marcamodelo IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params")
                )
                ->first();
        
        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre','observacion')
                ->first();

        $menuPr = [];
        $menuP = OpcionInventario::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionInventario::join('checkinventario as m','m.id','=','checkinventario.itemId')
                ->where('checkinventario.nivel','=','2')
                ->whereNotNull('checkinventario.itemId')
                ->whereNull('checkinventario.deleted_at')
                ->select('checkinventario.id','checkinventario.nombre','checkinventario.itemId', DB::Raw("(SELECT rpta.situacion FROM rptacheckinventario as rpta WHERE rpta.idOrdenTrabajo = $id AND rpta.idCheckInventario = checkinventario.id) as rpta"))
                ->orderBy('checkinventario.itemId','ASC')
                ->orderBy('checkinventario.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
            } 
            $msec = [];
        }
    
        $opciones = (Object) $menuPr;

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
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
		$fpdf::Cell(60,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, $orden->documento, 'RBL',0, 'C');
		
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
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
    
        
        $fpdf::SetFont('Arial','B',12);
        $fpdf::Ln(8);
        $fpdf::SetX(15);
        $fpdf::Cell(60, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE INVENTARIO'), 'B', 0, "C");
        $fpdf::Ln(8);
	  
        $fpdf::SetFont('Arial','B',9);
     	$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 9;

        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->fecha)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Fecha Emisión'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->placa)), 0, "L");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");
    
        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Placa'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->placa)), 'R', "L");
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 0, "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        // $fpdf::SetXY(88,$_y);
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
    
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->telefono)), 'R', "L");
        // $fpdf::SetXY(35,$_y)
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
        $fpdf::Ln();

    
        if (is_null($orden->params)) {
            $orden->params = '-@@-@@-';
        } 
        
        $params = explode('@@',$orden->params);
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($params[0])), 'B', "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($params[1])), 'RB', "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($params[0])), 0, "L");
        // $fpdf::SetXY(88,$_y);
        //$fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
        $fpdf::SetXY($_x+88, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
	
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('VIN'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($params[1])), 'R', "L");
        // $fpdf::SetXY(35,$_y)
        $fpdf::SetXY($_x+40, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        $fpdf::Ln();
    
        
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($params[2])), 'R', "L");
    
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Marca/Modelo'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($params[2])), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        
        if (!is_null($firma)) {

            // INI MOD 27.10.2022
            $fpdf::Ln();
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',$tam_font);
            $fpdf::Cell(183, $alto, utf8_decode("Observaciones   : "), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetFont('Arial','',$tam_font);
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($firma->observacion)), 'RBL', "L");
            //FIN MOD. 27.10.2022
        }
        
        $fpdf::SetTextColor(0,0,0);
          
        $fpdf::SetFillColor(255);
		$fpdf::Ln(5);
		$alto = 2;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',8);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(100, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK, BUEN ESTADO"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("FALTA, MAL ESTADO"), 1, 0, "C");
        $fpdf::Ln();
        // $fpdf::SetFont('Arial','',7);
        $alto = 4;
        $alto_2 = 6;
        
        foreach ($opciones as $opc) {
            $encabezado = $opc['header'];
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',8);
            $alto2 = $fpdf::GetMultiCellHeight(183,$alto_2,utf8_decode($encabezado->nombre), 1,0, "L");
            $_x = $fpdf::GetX();
            $fpdf::MultiCell(183, $alto_2, utf8_decode($encabezado->nombre), 1, 0, "L");
            $fpdf::SetXY($_x+183, $fpdf::GetY()-$alto2);

            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',7);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $fpdf::SetX(15);
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1,0, "L");
                $fpdf::SetFont('Arial','',7);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                $_x = $fpdf::GetX();
                $fpdf::MultiCell(100, $alto, utf8_decode($opc2->nombre), 1, 0, "L");
                $fpdf::SetXY($_x+100, $fpdf::GetY()-$alto2);

                $fpdf::SetFont('Arial','B',9);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } else{
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                }
                $fpdf::Ln();
                $item++;
                $fpdf::SetTextColor(0);
            }
        }

		// foreach ($detalles as $deta) {
		// 	// if (!is_null($deta->idProducto)) {
		// 		$fpdf::SetX(15);
        // 		$fpdf::Cell(15, $alto, utf8_decode("-"), 'L', 0, "C");
		// 		$fpdf::Cell(15, $alto, $deta->cantidad, 0, 0, "R");
		// 		$fpdf::Cell(15, $alto, utf8_decode("UND"), 0, 0, "C");
			
		// 		$_y = $fpdf::GetY();
    	// 		$fpdf::MultiCell(88, $alto, utf8_decode($deta->descripcion), 0, "L");
		// 		$fpdf::setXY(148,$_y);
			
		// 		$fpdf::Cell(25, $alto, number_format($deta->precio,2,'.',','), 0, 0, "R");
		// 		$fpdf::Cell(25, $alto, number_format($deta->subTotal,2,'.',','), 'R', 0, "R");
		// 		$fpdf::Ln();
		// 		$total+=$deta->subTotal;
        // 	// }
		// }

		// $fpdf::SetX(15);
		// if ($total == 0) {
		// 	$fpdf::Cell(183, $alto, utf8_decode("NO EXISTEN ITEMS SELECCIONADOS"), 1, 1, "L");
		// } else {
		// 	$fpdf::Cell(158, $alto, '', 'T', 0, "L");
		// 	// $fpdf::SetX(15);
		// 	$fpdf::SetFont('Arial','B',9);
		// 	$fpdf::Cell(25, $alto, number_format($total,2,'.',','), 'T', 0, "R");
		// }

		#$fpdf::Ln();
        $fpdf::SetX(30);
        $fpdf::SetFont('Arial','B',9);

        // $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
        //         ->where('isFirma','=',1)
        //         ->select('nombre','observacion')
        //         ->first();

        if (!is_null($firma)) {
    		$fpdf::SetAutoPageBreak(false, 0);
    		if ($fpdf::GetY() > 256.5) {
    		    $fpdf::SetAutoPageBreak(true, 10);
    		    $fpdf::AddPage('P','A4');
    		}
            #dd($fpdf::GetY());
            #$fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, $fpdf::GetX(), $fpdf::GetY(), 40);
                $fpdf::SetY($fpdf::GetY()+28);
            } else {
                $fpdf::Cell(40, $alto);
            } 

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            $fpdf::SetX(28);
            $fpdf::Cell(50,$alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln();
            // INI MOD 27.10.2022
            // $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            // $fpdf::Ln();
            
            // $fpdf::SetTextColor(206,3,3);
            // $fpdf::SetX(15);
            
            // $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($firma->observacion)), 'RBL', "L");
            //FIN MOD. 27.10.2022
            // $fpdf::SetXY(153,$_y);

            //$fpdf::Cell(183, $alto, utf8_decode($firma->observacion), 'RBL',0, "L");
            
        }

        $multimedia = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma',0)
                ->where('tipo','I')
                ->select('nombre')
                ->get();
        
        // $fpdf::AddPage('P','A4');
        $fpdf::SetTextColor(0,0,0);
        $fpdf::SetFont('Arial','B',14);
        // $fpdf::Ln(8);
        // $fpdf::SetX(15);
        // $fpdf::Cell(75, 7,'',0,0,'C');
  	    // $fpdf::Cell(25, 7, utf8_decode('ANEXOS'), 'B', 0, "C");
        // $fpdf::Ln(12);
        // $alto = 35;
        // $factor = 40;
        // $alto1 = 0;
        // $alto2 = 0;
        
        // $_x = $fpdf::getX();
        $i = 0;
        while ($i < count($multimedia)) {
            $mt = $multimedia[$i];
            $exist = \Storage::disk('local_temp')->exists($mt->nombre);
            if ($exist) {
                // $fpdf::Image("storage/imagenes_temp/".$mt->nombre, $fpdf::getX(), $fpdf::getY());
                // $fpdf::SetXY(70, $fpdf::getY() + 12);
                // $fpdf::SetXY(15,$fpdf::getY() + 10);
                // if ($i != 0) {
                    $fpdf::AddPage('P','A4');
                    $fpdf::Ln(8);
                    $fpdf::SetX(15);
                    $fpdf::Cell(75, 7,'',0,0,'C');
                    $fpdf::Cell(25, 7, utf8_decode('ANEXO '.($i+1<10?'0'.$i+1:$i+1)), 'B', 0, "C");
                    $fpdf::Ln(12);
                // }
                $fpdf::setX(10);
                $fpdf::Cell(190,100, $fpdf::Image("storage/imagenes_temp/".$mt->nombre, $fpdf::getX(), $fpdf::getY(), 190),0);
                $fpdf::setY($fpdf::getY()+190);
                // $_x = $fpdf::getX();
                // $_y = $fpdf::getY();
                // $fpdf::Ln();
                // // $fpdf::setY($_y);

                // $fpdf::setY($_y);
                $i++;
            }
            // if ($i <= count($multimedia)-1) {
            //     $mt = $multimedia[$i];
            //     $exist = \Storage::disk('local_temp')->exists($mt->nombre);
            //     if ($exist) {
            //         $fpdf::Cell(10);
            //         $fpdf::Cell(80,80, $fpdf::Image("storage/imagenes_temp/".$mt->nombre, $fpdf::getX(), $fpdf::getY(), 80),0,0,'C');
            //     }

            //     $fpdf::Ln();

            //     $fpdf::setXY($fpdf::getX(),$fpdf::getY());
            //     $i++;
            // }
        }
		// $letras = new EnLetras();
        // $fpdf::SetFont('helvetica', 'B', 8);
        // $valor = $letras->ValorEnLetras(str_replace(',','',$orden->total), ' '.$tipoMoneda); //letras

		// $son = strtoupper("SON: ".$valor);
        // $fpdf::MultiCell(183, $alto, utf8_decode($son), $borde, "L", true);
		   
		   
		

        $fpdf::SetXY(138, $fpdf::GetY());
        $fpdf::SetFont('Arial','',12);
        $alto = 5;
        
		$fpdf::Output("OrdenC_Inventario-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
  

    }
    public function getPdfOrdenCotizacion ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id','orden.placa', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin,'@@', ct.marcamodelo)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL AND ct.marcamodelo IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params")
                )
                ->first();

        $detalles =  DetalleOrdenTrabajo::leftJoin('cotizacion as c','c.id','=','detalleordentrabajo.idCotizacion')
                        ->where('detalleordentrabajo.idOrdenTrabajo','=',$id)
                        ->where('detalleordentrabajo.situacion','=','V')
                        ->select(DB::Raw("CONCAT('C', LPAD(c.serie,2,'0') ,'-', LPAD(c.numero,8,'0')) as numero"), 
                        'c.id', 'c.total', DB::Raw("FORMAT(c.total,2) as total2"), DB::Raw('false as mostrarOpc'))
                        ->get();

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo y Cotizaciones'));
        // $fpdf::AliasNbPages();
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
        $fpdf::Cell(60,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(138);
        $fpdf::Cell(60, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(138);
        $fpdf::Cell(60, $alto, $orden->documento, 'RBL',0, 'C');
    
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
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
        
        $fpdf::SetFont('Arial','B',12);
        $fpdf::Ln(8);
        $fpdf::SetX(15);
        $fpdf::Cell(60, 7,'',0,0,'C');
        $fpdf::Cell(62, 7, utf8_decode('LISTADO DE COTIZACIONES'), 'B', 0, "C");
        $fpdf::Ln(8);
          
        $fpdf::SetFont('Arial','B',9);
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $alto = 7;
        $tam_font = 9;

        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln();
        $fpdf::SetXY(15, ($alto2>$alto?$fpdf::GetY()+$alto:$fpdf::GetY()));
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->fecha)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Fecha Emisión'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->placa)), 0, "L");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");
    
        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Placa'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->placa)), 'R', "L");
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 0, "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        // $fpdf::SetXY(88,$_y);
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
    
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'R', "L");
        // $fpdf::SetXY(35,$_y)
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
        $fpdf::Ln();
    
        if (is_null($orden->params)) {
            $orden->params = '-@@-@@-';
        } 
        
        $params = explode('@@',$orden->params);
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($params[0])), 'B', "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($params[1])), 'RB', "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($params[0])), 0, "L");
        // $fpdf::SetXY(88,$_y);
        //$fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
    	$fpdf::SetXY($_x+88,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('VIN'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");
        
        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($params[1])), 'R', "L");
        // $fpdf::SetXY(35,$_y)
        //$fpdf::SetXY($_x+35, $fpdf::GetY()-$alto2);
    	$fpdf::SetXY($_x+40,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
   
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($params[2])), 'BR', "L");
     
        $fpdf::Cell(25, $alto2, utf8_decode('Marca/Modelo'), 'LB', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($params[2])), 'BR', "L");
        // $fpdf::SetXY(88,$_y);
        // $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
     	$fpdf::SetXY($_x+153,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
   
    
        $fpdf::SetFillColor(255);
        $fpdf::Ln(12);
        $alto = 2;   
        $fpdf::SetXY(32, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',8);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(100, $alto, utf8_decode("COTIZACIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("TOTAL"), 1, 0, "C");
        // $fpdf::Cell(34, $alto, utf8_decode("FALTA, MAL ESTADO"), 1, 0, "C");
        $fpdf::Ln();
            // $fpdf::SetFont('Arial','',7);
        $alto = 5;

        $fpdf::SetFont('Arial','',8);
        $j=1;
        $acum = 0;
        foreach ($detalles as $det) {
            $fpdf::SetX(32);
            $fpdf::Cell(15, $alto, utf8_decode($j<10?'0'.$j:$j), 1, 0, "C");
            $fpdf::Cell(100, $alto, utf8_decode($det->numero), 1, 0, "C");
            $fpdf::Cell(34, $alto, utf8_decode($det->total2), 1, 0, "C");
            $fpdf::Ln();
            $j++;
            $acum += $det->total;
        }
        $alto = 12;
        $fpdf::SetFont('Arial','B',14);
        $fpdf::SetXY(25, $fpdf::GetY()+$alto);
        $fpdf::Cell(30, $alto, utf8_decode("TOTAL"), 0, 0, "C");
        $fpdf::Cell(7, $alto, utf8_decode(":"), 0, 0, "C");
        $fpdf::Cell(34, $alto, 'S/ '.number_format($acum,2,'.',' '), 0, 0, "C");
        $fpdf::Ln();
       
        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre','observacion')
                ->first();

        $alto = 5;
        $fpdf::SetFont('Arial','',9);
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*4));
            } else {
                $fpdf::Cell(50, $alto);
            }
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());

            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($firma->observacion)), 'RBL', "L");
            
        }

          
        $fpdf::SetXY(138, $fpdf::GetY());
        $fpdf::SetFont('Arial','',12);
        $alto = 5;
        
        $fpdf::Output("Orden_Cotizacion-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
        $fpdf::Output();

        exit;
      
        
    }
    public function getPdfCheckListCalidad ($id) {
        $orden = DB::table('ordentrabajo as orden')
        ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
        ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
        ->leftJoin('cita','cita.id','=','orden.idCita')
        ->where('orden.id',$id)
        ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
        DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente')
        ->first();
    

        $rptacalord = DB::table('rptacalidadorden')
                    ->where('idOrdenTrabajo','=',$id)
                    ->select('id','observacion')
                    ->first();

        $idrpta = 0;
        $observacion = '';
        if (!is_null($rptacalord)) {
            $idrpta  = $rptacalord->id;
            $observacion = $rptacalord->observacion;
        }

        // dd($idrpta,$id, $rptacalord);

        $menuPr = [];
        $menuP = OpcionCalidad::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();


        $menuS = OpcionCalidad::join('checkcalidad as m','m.id','=','checkcalidad.itemId')
                ->where('checkcalidad.nivel','=','2')
                ->whereNotNull('checkcalidad.itemId')
                ->whereNull('checkcalidad.deleted_at')
                ->select('checkcalidad.itemId','checkcalidad.nombre', DB::Raw("(SELECT rpta.situacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as rpta"), DB::Raw("(SELECT rpta.observacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as observacion"))
                ->orderBy('checkcalidad.itemId','ASC')
                ->orderBy('checkcalidad.orden','ASC')
                ->get();

        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec);
            } 
            $msec = [];
        }

        $opciones1 = (Object) $menuPr;

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('L','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
      
        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE CALIDAD'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 11;
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($orden->trabajador)), 'B', "L");
		$fpdf::SetXY(183,$_y);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Teléfono'), 'B', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(67, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'RB', "L");
		$fpdf::SetXY(67,$_y);
	
		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(100, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(82, $alto, utf8_decode("OBSERVACIONES"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',10);
        $alto = 4;
        
        foreach ($opciones1 as $opc) {
            $encabezado = $opc['header'];
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',10);
            $fpdf::Cell(265, $alto+2, utf8_decode($encabezado->nombre), 1, 0, "L");
          
            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',9);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $fpdf::SetX(15,$fpdf::GetY()+$alto);
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1, "C");
         
                // dd($alto2, $alto);
                $fpdf::SetFont('Arial','',9);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                // $fpdf::MultiCell(15, $alto, utf8_decode($item), 0, "C");
                // $fpdf::SetXY(30,$fpdf::getY()-$alto2);
                
                $_y = $fpdf::getY();
                if (strlen($opc2->nombre) >= 90) {
                    $fpdf::MultiCell(100, $alto, utf8_decode($opc2->nombre), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(130,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(100, $alto2, utf8_decode($opc2->nombre),1, 0, "L");
                }
                // dd($_y,$fpdf::getY()-$alto2, $alto, $alto2);
                $fpdf::SetFont('Arial','B',9);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'N') {
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                } else {
                    // $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                }
       
                $fpdf::SetTextColor(0);
                $_y = $fpdf::getY();
                if (strlen($opc2->observacion) >= 90) {
                    $fpdf::MultiCell(82, $alto, utf8_decode($opc2->observacion), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(130,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(82, $alto2, utf8_decode($opc2->observacion),1, 0, "C");
                }
       
                $fpdf::Ln();
                $item++;
                // $fpdf::SetTextColor(0);
            // break;
            }
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }
            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion), 'RBL',0, "L");
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($observacion)), 'RBL', "L");
            
        }

        $fpdf::Output("OrdenC_CheckCalidad-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;

    }

    public function getPdfCheckTallerOld ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"))
                ->first();
    

        $rptacalord = DB::table('rptacalidadorden')
                    ->where('idOrdenTrabajo','=',$id)
                    ->select('id','observacion')
                    ->first();

        $idrpta = 0;
        $observacion = '';
        if (!is_null($rptacalord)) {
            $idrpta  = $rptacalord->id;
            $observacion = $rptacalord->observacion;
        }


        $rptatallerord = DB::table('rptatallerorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion2 = '';
        if (!is_null($rptatallerord)) {
            $idrpta  = $rptatallerord->id;
            $observacion2 = $rptatallerord->observacion;
        }

        // dd($idrpta, $observacion2);

        if ($idrpta != 0) {
            $rptas = DB::table('rptachecktaller as rchk')
                    ->leftJoin('checktaller as check','rchk.idCheckTaller','=','check.id')
                    ->where('rchk.idRptaTaller','=',$idrpta)
                    ->select('check.nombre','rchk.situacion as valor','rchk.indicacion','rchk.indicacion1','rchk.indicacion2','rchk.indicacion3')
                    ->orderBy('check.orden')
                    ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();
        } else {
            $rptas = DB::table('checktaller as check')
                    ->select('check.nombre', DB::Raw("'' as valor"), DB::Raw("'' as indicacion"),
                            DB::Raw("'' as indicacion1"), DB::Raw("'' as indicacion2"), DB::Raw("'' as indicacion3"))
                    ->orderBy('check.orden')
                    // ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();

        }
        $opciones3 = $rptas;

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('L','A4');

		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE TALLER'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 10;
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->cliente), 'TR', "C");
         
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->doc_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY() - $alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->direccion_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->fecha), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('F. Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode(strtoupper($orden->documento_cita)), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);
		$fpdf::Ln();

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'R', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);
        $fpdf::Ln();

        if (is_null($orden->params)) {
            $orden->params = '-@@-'; 
        }
        $params = explode('@@', $orden->params);

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($params[0])), 'R', "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($params[1])), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($params[0])), 'B', "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('VIN'), 'B', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($params[1])), 'RB', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);


        $fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
         
        $fpdf::MultiCell(34, $alto+3, utf8_decode("SEGÚN INDICACIÓN"), 1, "C");
        $fpdf::SetXY(49,$_Y_list);
        
        $fpdf::MultiCell(23, $alto, utf8_decode("25, 35, 45, 55, 65, 75, 85, 95"), 1, "C");
        $fpdf::SetXY(72,$_Y_list);
     
        $fpdf::MultiCell(16, $alto, utf8_decode("10, 30, 50, 70, 90"), 1, "C");
        $fpdf::SetXY(88,$_Y_list);
        
        $fpdf::MultiCell(14, $alto, utf8_decode("20, 40, 60, 80, 100"), 1, "C");
        $fpdf::SetXY(102,$_Y_list);
       
        $fpdf::MultiCell(76, $alto*3, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, "C");
        $fpdf::SetXY(178,$_Y_list);
        // $fpdf::Cell(57, $alto, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("CORREGIDO"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',9);
        $alto = 4;
        // dd($opciones3);

        foreach ($opciones3 as $opc) {
            $fpdf::SetXY(15,$fpdf::GetY());
            $alto2 = $fpdf::GetMultiCellHeight(76,$alto,utf8_decode($opc->nombre), 1, "C");
                     
            $fpdf::Cell(34, $alto2, utf8_decode($opc->indicacion),1, 0, "C");
            $fpdf::Cell(23, $alto2, utf8_decode($opc->indicacion1),1, 0, "C");
            // $fpdf::SetXY(49,$_Y_list);

            $fpdf::Cell(16, $alto2, utf8_decode($opc->indicacion2), 1,0, "C");
            // $fpdf::SetXY(72,$_Y_list);

            $fpdf::Cell(14, $alto2, utf8_decode($opc->indicacion3), 1,0, "C");
            // $fpdf::SetXY(102,$_Y_list);
            $_x = $fpdf::GetX();
             
            $fpdf::MultiCell(76, $alto, utf8_decode($opc->nombre), 1, "L");
            $fpdf::SetXY($_x+76,$fpdf::GetY()-$alto2);
       
            //dd($opc->valor);
            if($opc->valor == 'S') {
                $fpdf::SetTextColor(5,122,31);
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'N'){
                $fpdf::SetTextColor(206,3,3);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'C') {
                $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            } else {
                // $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            }
    
              
            $fpdf::Ln();
            // $fpdf::SetY($fpdf::GetY()+$alto2-$alto);
        
            // $item++;
            $fpdf::SetTextColor(0);
        // break;
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        // $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
        //         ->where('isFirma','=',1)
        //         ->select('nombre')
        //         ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $alto = 6;
           
            $alto2 = $fpdf::GetMultiCellHeight(265,$alto,utf8_decode(strtoupper($observacion2)), 'RBL', "C");
       
            $fpdf::Cell(265, $alto2, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion2), 'RBL',0, "L");
            $_x = $fpdf::GetX();
            $fpdf::MultiCell(265, $alto, utf8_decode(strtoupper($observacion2)), 'RBL', "L");
            $fpdf::SetXY($_x+265,$fpdf::GetY()-$alto2);
        }

        
		$fpdf::Output("OrdenC_CheckListTaller-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
    }

    public function getPdfCheckTaller ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.numero', 'orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),
                DB::raw("DATE_FORMAT(orden.inicia,'%d/%m/%Y %h:%i:%s %p') as inicia"),
                DB::raw("DATE_FORMAT(orden.finaliza,'%d/%m/%Y %h:%i:%s %p') as finaliza"),
                DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"))
                ->first();
    

        $rptacalord = DB::table('rptacalidadorden')
                    ->where('idOrdenTrabajo','=',$id)
                    ->select('id','observacion')
                    ->first();

        $idrpta = 0;
        $observacion = '';
        if (!is_null($rptacalord)) {
            $idrpta  = $rptacalord->id;
            $observacion = $rptacalord->observacion;
        }


        $rptatallerord = DB::table('rptatallerorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion2 = '';
        if (!is_null($rptatallerord)) {
            $idrpta  = $rptatallerord->id;
            $observacion2 = $rptatallerord->observacion;
        }

        // dd($idrpta, $observacion2);

        if ($idrpta != 0) {
            $rptas = DB::table('rptachecktaller as rchk')
                    ->leftJoin('checktaller as check','rchk.idCheckTaller','=','check.id')
                    ->where('rchk.idRptaTaller','=',$idrpta)
                    ->select('check.nombre','rchk.situacion as valor',
                            'check.indicacion1','check.indicacion2', 'check.indicacion3', 'check.indicacion4')
                    ->orderBy('check.orden')
                    ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();
        } else {
            $rptas = DB::table('checktaller as check')
                    ->select('check.nombre', DB::Raw("'' as valor"),
                            'check.indicacion1','check.indicacion2', 'check.indicacion3', 'check.indicacion4')
                    ->orderBy('check.orden')
                    // ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();

        }
        $opciones3 = $rptas;
        
        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Check List de Taller | Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('P','A4');

		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
    	// $fpdf::SetX(150);
        $fpdf::Image("images/logo-carpio.png", 95,10,22,15);
		
        
        // $fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		// $fpdf::SetXY(210, 14);
        // $fpdf::SetFont('Arial','B',14);
        // $alto = 7;
        // $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        // $fpdf::Ln($alto);
        // $fpdf::SetX(210);
        // $fpdf::SetFillColor(240);
        // $fpdf::SetFont('Arial','B',12);
		// $fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    // $fpdf::Ln($alto);
        // $fpdf::SetX(210);
       	// $fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		// $fpdf::Ln($alto);
		// $fpdf::SetX(210);
       	// $fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		// $fpdf::Ln(16);
        // $alto = 5;
        // $margin_left = 15;
        // $fpdf::SetFont('Arial','B',11);
        // $fpdf::SetX($margin_left);
        // $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        // $fpdf::SetFont('Arial','',10);
        // $fpdf::Ln();
        // $fpdf::SetX($margin_left);
        // $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        // $fpdf::Ln();
        // $fpdf::SetX($margin_left);
        // $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        // $fpdf::Ln();
        // $fpdf::SetX($margin_left);
  		// $fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::Ln(18);
        $fpdf::SetFont('Arial','B',7.5);
        $fpdf::SetX(7);
        $alto = 6;
        // $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(196, 7, utf8_decode('CHECKLIST DE MANTENIMIENTOS PREVENTIVOS, APLICADOS A VEHÍCULOS DIESEL'), 1,0, "C");
        $fpdf::Ln();
        $fpdf::SetX(7);
        if (is_null($orden->params)) {
            $orden->params = '-@@-'; 
        }
        $params = explode('@@', $orden->params);
        $a = [];
        $a[] = $fpdf::GetMultiCellHeight(20,($alto+2)/2,utf8_decode($orden->documento), 1, "C");
        $a[] = $fpdf::GetMultiCellHeight(20,($alto+2)/2,utf8_decode($orden->placa), 1, "C");
        $a[] = $fpdf::GetMultiCellHeight(20,($alto+2)/2,utf8_decode($params[0]), 1, "C");
        $a[] = $fpdf::GetMultiCellHeight(22,($alto+2)/2,utf8_decode($orden->inicia), 1, "C");
        $a[] = $fpdf::GetMultiCellHeight(22,($alto+2)/2,utf8_decode($orden->finaliza), 1, "C");
        sort($a);
        // dd($a, $alto);
        $alto2 = $a[count($a)-1];
        $alto = $alto2 ;
        $alto3 = 6;
        // dd(($alto+2)/2, $alto, $alto2, $alto3);
        $fpdf::Cell(60, $alto+2, utf8_decode('Intervalo en km. (x1000)'), 1,0, "C");
        $fpdf::Cell(20, $alto3, utf8_decode('Orden'), 1,0, "C");
        $fpdf::Cell(20, $alto3, utf8_decode('Placa'), 1,0, "C");
        $fpdf::Cell(20, $alto3, utf8_decode('Km'), 1,0, "C");
        $fpdf::Cell(22, $alto3, utf8_decode('H. inicio'), 1,0, "C");
        $fpdf::Cell(22, $alto3, utf8_decode('H. termino'), 1,0, "C");
        $fpdf::Cell(10, $alto3, utf8_decode(''), 1,0, "C");
        $fpdf::Cell(10, $alto3, utf8_decode(''), 1,0, "C");
        $fpdf::Cell(12, $alto3, utf8_decode(''), 1,0, "C");
        $fpdf::Ln();
        $fpdf::SetX(67);


        // dd($a, sort($a), $a, $a[count($a)-1]);
        // $fpdf::Cell(20, ($alto+2)/2, utf8_decode($orden->documento), 1,0, "C","");
        $_x = $fpdf::GetX();
        $_y = $fpdf::GetY();
        // $fpdf::MultiCell(20, ($alto - $alto3), utf8_decode($orden->documento), 1, "C");
        // $fpdf::SetXY($_x+20,$_y>($fpdf::GetY()-($alto - $alto3))?$_y:$fpdf::GetY()-($alto - $alto3));
        
        // $fpdf::MultiCell(20, ($alto - $alto3), utf8_decode($orden->placa), 1, "C");
        // $fpdf::SetXY($_x+20,$_y>($fpdf::GetY()-($alto - $alto3))?$_y:$fpdf::GetY()-($alto - $alto3));
        
        $fpdf::CellFitScale(20,($alto-$alto3+2),utf8_decode($orden->documento),1,0,'C',"");
        $fpdf::CellFitScale(20,($alto-$alto3+2),utf8_decode($orden->placa),1,0,'C',"");
        $fpdf::CellFitScale(20,($alto-$alto3+2),utf8_decode($params[0]), 1,0, "C","");
        if (!is_null($orden->inicia)) {
            $fpdf::CellFitScale(22,($alto-$alto3+2),utf8_decode($orden->inicia), 1,0, "C","");
        } else {
            $fpdf::Cell(22,($alto-$alto3+2),utf8_decode($orden->inicia), 1,0, "C");
        }
        if (!is_null($orden->finaliza)) {    
            $fpdf::CellFitScale(22,($alto-$alto3+2),utf8_decode($orden->finaliza), 1,0, "C","");
        } else {
            $fpdf::Cell(22,($alto-$alto3+2),utf8_decode($orden->finaliza), 1,0, "C");
        }
        $fpdf::Cell(10,($alto-$alto3+2),utf8_decode(""), 1,0, "C");
        $fpdf::Cell(10,($alto-$alto3+2),utf8_decode(""), 1,0, "C");
        $fpdf::Cell(12,($alto-$alto3+2),utf8_decode(""), 1,0, "C");
        $fpdf::Ln();
        
        // $fpdf::Cell(15, ($alto+2)/2, utf8_decode('H. inicio'), 1,0, "C");
        // $fpdf::Cell(15, ($alto+2)/2, utf8_decode('H. termino'), 1,0, "C");
        // $fpdf::Cell(10, ($alto+2)/2, utf8_decode(''), 1,0, "C");
        // $fpdf::Cell(10, ($alto+2)/2, utf8_decode(''), 1,0, "C");
        // $fpdf::Cell(12, ($alto+2)/2, utf8_decode(''), 1,0, "C");
        
        // $fpdf::Ln(25);
        // $fpdf::SetX(15);
        // $fpdf::Cell(20, $alto, utf8_decode('Orden'), 1,0, "C");
        // $fpdf::Cell(20, $alto, utf8_decode('Placa'), 1,0, "C");
        // $fpdf::Cell(20, $alto, utf8_decode('Km'), 1,0, "C");
        // $fpdf::Cell(15, $alto, utf8_decode('H. inicio'), 1,0, "C");
        // $fpdf::Cell(15, $alto, utf8_decode('H. termino'), 1,0, "C");
        // $fpdf::Cell(10, $alto, utf8_decode(''), 1,0, "C");
        // $fpdf::Cell(10, $alto, utf8_decode(''), 1,0, "C");
        // $fpdf::Cell(12, $alto, utf8_decode(''), 1,0, "C");
        // $fpdf::Ln();
        $fpdf::SetX(7);
        $alto = 28;
        $fpdf::Cell(10, $alto, $fpdf::TextWithDirection($fpdf::GetX()+6,$fpdf::GetY()+25,utf8_decode('Según Indicación'),'U'), 1,0, "C");

        $alto = 5.6;
        $alto_1 = $fpdf::GetMultiCellHeight(34,$alto,utf8_decode('5, 15, 25, 35, 45, 55, 65, 75, 85, 95'), "RL", "C");
        $alto_2 = $fpdf::GetMultiCellHeight(8,$alto,utf8_decode('10 30 50 70 90'), "RL", "C");
        $alto_3 = $fpdf::GetMultiCellHeight(8,$alto,utf8_decode('20 40 60 80 100'), "RL", "C");

        if ($alto_1 > $alto_2 && $alto_1 > $alto_3) {
            $alto2 = $alto_1;
        } elseif ($alto_2 > $alto_1 && $alto_2 > $alto_3) {
            $alto2 = $alto_2;
        } else {
            $alto2 = $alto_3;
        }
        $_x = $fpdf::GetX();
        $_y = $fpdf::GetY();
        $fpdf::MultiCell(34,14, utf8_decode('5, 15, 25, 35, 45, 55, 65, 75, 85, 95'), 1, "C");
        $fpdf::SetXY($_x+34,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);

        $_x = $fpdf::GetX();
        $_y = $fpdf::GetY();
        $fpdf::MultiCell(8, $alto, utf8_decode('10 30 50 70 90'), 1, "C");
        $fpdf::SetXY($_x+8,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);

        $_x = $fpdf::GetX();
        $_y = $fpdf::GetY();
        $fpdf::MultiCell(8, $alto, utf8_decode('20 40 60 80 100'), 1, "C");
        $fpdf::SetXY($_x+8,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        
        // $fpdf::SetFont('Arial','B',9);
        // $_x = $fpdf::GetX();
        // $_y = $fpdf::GetY();

        // $fpdf::MultiCell(20, 14, utf8_decode($orden->documento), 1, "C"); // $alto = 14
        $fpdf::CellFitScale(52,28,utf8_decode("C = Cambiar"),1,0,'C',"");
        $fpdf::CellFitScale(52,28,utf8_decode("I = Inspeccionar"),1,0,'C',"");
        $fpdf::CellFitScale(10,28,utf8_decode("OK"),1,0,'C',"");
        $fpdf::CellFitScale(10,28,utf8_decode("No. OK"),1,0,'C',"");
        $fpdf::CellFitScale(12,28,utf8_decode("CORREGIDO"),1,0,'C',"");
        $fpdf::Ln();
        // $fpdf::SetXY($_x+20,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        
        // $_x = $fpdf::GetX();
        // $_y = $fpdf::GetY();
        // $fpdf::MultiCell(20, 28, utf8_decode($orden->placa), 1, "C");
        // $fpdf::SetXY($_x+20,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        // $fpdf::CellFitScale(20,28,utf8_decode($orden->placa),1,0,'C',"");
        // $fpdf::CellFitScale(20,28,utf8_decode($orden->placa),1,0,'C',"");
        // if (is_null($orden->params)) {
        //     $orden->params = '-@@-'; 
        // }
        // $params = explode('@@', $orden->params);
        
        // $_x = $fpdf::GetX();
        // $_y = $fpdf::GetY();
        // $fpdf::Cell(20, 28, utf8_decode('584888888118ddede'), 1,0, "C");
        // $fpdf::CellFitScale(20,28,utf8_decode($params[0]),1,0,'C',"");
        // if (strlen('584888888118') > 11) {
        //     $fpdf::MultiCell(20, $alto*3, utf8_decode('584888888118'), 1, "C");
        //     $fpdf::SetXY($_x+20,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
        // }
    
        // $fpdf::TextWithDirection(10,$alto,utf8_decode('Según Indicación'),'L');

        // $fpdf::Ln(100);
       
        // $fpdf::SetXY(15, $fpdf::GetY()+$alto);
		// $alto = 7;
		// $tam_font = 10;
        // $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->cliente), 'TR', "C");
         
		// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

		// $_y = $fpdf::GetY();
		// $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        // $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		// $fpdf::Ln();
        // $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->doc_cliente), 'R', "C");
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
		// $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        // $fpdf::SetXY($_x+235,$fpdf::GetY() - $alto2);

		// $fpdf::Ln();
        // $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->direccion_cliente), 'R', "C");
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
    	// $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        // $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		// $fpdf::Ln();
		// $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->fecha), 'R', "C");
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('F. Emisión'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
		// $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        // $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		// $fpdf::Ln();
		// $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode(strtoupper($orden->documento_cita)), 'R', "C");
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
    	// $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        // $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);
		// $fpdf::Ln();

        // $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "C");
        // $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 'R', "C");

        // if ($alto_1 > $alto_2) {
        //     $alto2 = $alto_1;
        // } else {
        //     $alto2 = $alto_2;
        // }

		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
        // $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
		// $fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

		// $_y = $fpdf::GetY();
        // $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'R', "L");
		// $fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);
        // $fpdf::Ln();

        // if (is_null($orden->params)) {
        //     $orden->params = '-@@-'; 
        // }
        // $params = explode('@@', $orden->params);

        // $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($params[0])), 'R', "C");
        // $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($params[1])), 'R', "C");

        // if ($alto_1 > $alto_2) {
        //     $alto2 = $alto_1;
        // } else {
        //     $alto2 = $alto_2;
        // }

		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'LB', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

		// $_y = $fpdf::GetY();
        // $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($params[0])), 'B', "L");
		// $fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto2, utf8_decode('VIN'), 'B', 0, "L");
		// $fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "B");

		// $_y = $fpdf::GetY();
        // $_x = $fpdf::GetX();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($params[1])), 'RB', "L");
		// $fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);


        // $fpdf::SetFillColor(255);
		// $fpdf::Ln();
		$alto = 3;   
        // $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        // $_Y_list = $fpdf::GetY();
        // $alto = 6;
        // $fpdf::SetFont('Arial','B',10);
         
        // $fpdf::MultiCell(34, $alto+3, utf8_decode("SEGÚN INDICACIÓN"), 1, "C");
        // $fpdf::SetXY(49,$_Y_list);
        
        // $fpdf::MultiCell(23, $alto, utf8_decode("25, 35, 45, 55, 65, 75, 85, 95"), 1, "C");
        // $fpdf::SetXY(72,$_Y_list);
     
        // $fpdf::MultiCell(16, $alto, utf8_decode("10, 30, 50, 70, 90"), 1, "C");
        // $fpdf::SetXY(88,$_Y_list);
        
        // $fpdf::MultiCell(14, $alto, utf8_decode("20, 40, 60, 80, 100"), 1, "C");
        // $fpdf::SetXY(102,$_Y_list);
       
        // $fpdf::MultiCell(76, $alto*3, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, "C");
        // $fpdf::SetXY(178,$_Y_list);
        // // $fpdf::Cell(57, $alto, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("OK"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("NO OK"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("CORREGIDO"), 1, 0, "C");
        // $fpdf::Ln();
        $fpdf::SetFont('Arial','',7.5);
        $alto = 4;
        // dd($opciones3);

        foreach ($opciones3 as $opc) {
            $fpdf::SetXY(7,$fpdf::GetY());
            $alto2 = $fpdf::GetMultiCellHeight(104,$alto,utf8_decode($opc->nombre), 1, "C");
                     
            $fpdf::Cell(10, $alto2, utf8_decode($opc->indicacion1),1, 0, "C");
            $fpdf::Cell(34, $alto2, utf8_decode($opc->indicacion2),1, 0, "C");
            // $fpdf::SetXY(49,$_Y_list);

            $fpdf::Cell(8, $alto2, utf8_decode($opc->indicacion3), 1,0, "C");
            // $fpdf::SetXY(72,$_Y_list);

            $fpdf::Cell(8, $alto2, utf8_decode($opc->indicacion4), 1,0, "C");
            // $fpdf::SetXY(102,$_Y_list);
            $_x = $fpdf::GetX();
             
            $fpdf::MultiCell(104, $alto, utf8_decode($opc->nombre), 1, "L");
            $fpdf::SetXY($_x+104,$fpdf::GetY()-$alto2);
       
            if($opc->valor == 'S') {
                $fpdf::SetTextColor(5,122,31);
                $fpdf::Cell(10, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(12, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'N'){
                $fpdf::SetTextColor(206,3,3);
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(10, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(12, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'C') {
                $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(12, $alto2, utf8_decode("X"), 1, 0, "C");
            } else {
                // $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(10, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(12, $alto2, utf8_decode(""), 1, 0, "C");
            }
    
              
            $fpdf::Ln();
            // $fpdf::SetY($fpdf::GetY()+$alto2-$alto);
        
            // $item++;
            $fpdf::SetTextColor(0);
        // break;
        }

		$fpdf::Ln();
        $fpdf::SetX(7);
        $fpdf::SetFont('Arial','B',8);

        // $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
        //         ->where('isFirma','=',1)
        //         ->select('nombre')
        //         ->first();
        $alto = 8;
        if (!is_null($firma)) {
            $fpdf::SetXY(7, $fpdf::getY());
            // $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            // if ($exist) {
            //     $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
            //     $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            // } else {
            //     $fpdf::Cell(50, $alto);
            // }

            // // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // // $fpdf::Ln(2);
            // // $fpdf::SetXY(15, $fpdf::getY());
            // $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            // $fpdf::Ln(10);
            // $fpdf::SetX(15);
            // $alto = 6;
           
            $alto2 = $fpdf::GetMultiCellHeight(196,$alto,utf8_decode(strtoupper($observacion2)), 'RBL', "C");
       
            $fpdf::Cell(196, $alto2, utf8_decode("COMENTARIOS: "), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(7);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion2), 'RBL',0, "L");
            $_x = $fpdf::GetX();
            $fpdf::MultiCell(196, $alto, utf8_decode(strtoupper($observacion2)), 'RBL', "L");
            $fpdf::SetXY($_x+196,$fpdf::GetY()-$alto2);

            $fpdf::Ln();

            $fpdf::SetXY(45, $fpdf::getY()+9);
            $fpdf::Cell(50, $alto, utf8_decode("FIRMA DEL TÉCNICO"), 'T',0, "C", false);
            
            $fpdf::SetX(120);
            $fpdf::Cell(50, $alto, utf8_decode("FIRMA DE JEFE DE TALLER"), 'T',0, "C", false);
        }

        
		$fpdf::Output("OrdenC_CheckListTaller-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
    }

    public function getPdfCheckCalidad ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"))
                ->first();
    
        $rptacalord = DB::table('rptacalidadorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion = '';
        if (!is_null($rptacalord)) {
            $idrpta  = $rptacalord->id;
            $observacion = $rptacalord->observacion;
        }

        // dd($idrpta,$id, $rptacalord);

        $menuPr = [];
        $menuP = OpcionCalidad::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();
        
        
        $menuS = OpcionCalidad::join('checkcalidad as m','m.id','=','checkcalidad.itemId')
                ->where('checkcalidad.nivel','=','2')
                ->whereNotNull('checkcalidad.itemId')
                ->whereNull('checkcalidad.deleted_at')
                ->select('checkcalidad.itemId','checkcalidad.nombre', DB::Raw("(SELECT rpta.situacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as rpta"), DB::Raw("(SELECT rpta.observacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as observacion"))
                ->orderBy('checkcalidad.itemId','ASC')
                ->orderBy('checkcalidad.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec);
            } 
            $msec = [];
        }
        
        $opciones1 = (Object) $menuPr;

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('L','A4');

		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE CALIDAD'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 10;
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->cliente), 'TR', "C");
         
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->doc_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY() - $alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->direccion_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->fecha), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('F. Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode(strtoupper($orden->documento_cita)), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);
		$fpdf::Ln();

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'R', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);
        $fpdf::Ln();

        if (is_null($orden->params)) {
            $orden->params = '-@@-'; 
        }
        $params = explode('@@', $orden->params);

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($params[0])), 'R', "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($params[1])), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($params[0])), 'B', "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('VIN'), 'B', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($params[1])), 'RB', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);
		$fpdf::Ln(12);

        // ---------------------------------------------------- 
        $fpdf::SetFillColor(255);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(100, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(82, $alto, utf8_decode("OBSERVACIONES"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',10);
        $alto = 5;
        $altoN = 7;
        foreach ($opciones1 as $opc) {
            $fpdf::SetX(15);
            $encabezado = $opc['header'];
            $fpdf::SetFont('Arial','B',10);
            $fpdf::Cell(265, $alto, utf8_decode($encabezado->nombre), 1, 0, "L");
           
            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',8);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1, "L");
                $fpdf::SetX(15);
                // $fpdf::SetX(15);
                // $alto_2 = $fpdf::GetMultiCellHeight(82,$alto,utf8_decode($opc2->observacion), 1, "L");
                // $fpdf::MultiCell(82, $alto, utf8_decode($opc2->observacion), 1, "L");
            
                // if ($alto_1 > $alto_2) {
                //     $alto2 = $alto_1;
                // } else {
                //     $alto2 = $alto_2;
                // }
                
                // dd($alto2, $alto);
                $fpdf::SetFont('Arial','',8);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                // $fpdf::MultiCell(15, $alto, utf8_decode($item), 0, "C");
                // $fpdf::SetXY(30,$fpdf::getY()-$alto2);
                
                $_x = $fpdf::getX();
                $fpdf::MultiCell(100, $alto, utf8_decode($opc2->nombre), 1, "L");
                $fpdf::SetXY($_x+100, $fpdf::GetY()-$alto2);
                // if ($item == 4) {dd($alto_1, $alto_2, $fpdf::getY());}
                // if (strlen($opc2->nombre) >= 90) {
                //     $fpdf::MultiCell(100, $alto, utf8_decode($opc2->nombre), 1, "L");
                //     // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                //     $fpdf::SetXY(130,$fpdf::GetY()-$alto2);
                // } else {
                //     $fpdf::Cell(100, $alto2, utf8_decode($opc2->nombre),1, 0, "L");
                // }
                // dd($_y,$fpdf::getY()-$alto2, $alto, $alto2);
                $fpdf::SetFont('Arial','B',8);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'N'){
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                } else {
                    // $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                }
       
                // $fpdf::SetTextColor(0);
                // $_x = $fpdf::getX();
                $fpdf::Cell(82, $alto2, utf8_decode($opc2->observacion), 1,0, "L");
                // $fpdf::SetXY($_x+82, $fpdf::GetY()-$alto2);
                // dd($_x, $alto, $alto2);
                // $fpdf::SetX($_x+82);
              
                // if (strlen($opc2->observacion) >= 90) {
                //     $_x = $fpdf::getX();
                //     $fpdf::MultiCell(82, $alto, utf8_decode($opc2->observacion), 1, "L");
                //     // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                //     $fpdf::SetXY($_x+82,$fpdf::GetY()-$alto2);
                // } else {
                //     $fpdf::Cell(82, $alto2, utf8_decode($opc2->observacion),1, 0, "C");
                // }
       
                $fpdf::Ln();
                $item++;
                // $fpdf::SetX(15);

                $fpdf::SetTextColor(0);
            // break;
            }
            $fpdf::Ln();
            
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $alto = 6;
            
            $alto2 = $fpdf::GetMultiCellHeight(265,$alto,utf8_decode(strtoupper($observacion)), 'RBL', "C");
        
            $fpdf::Cell(265, $alto2, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion2), 'RBL',0, "L");
            $_x = $fpdf::GetX();
            $fpdf::MultiCell(265, $alto, utf8_decode(strtoupper($observacion)), 'RBL', "L");
            $fpdf::SetXY($_x+265,$fpdf::GetY()-$alto2);
        }
        

        $fpdf::Output("OrdenC_CheckListCalidad-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;


    }

    public function getPdfCheckManejo ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente',
                DB::raw("(SELECT CONCAT(ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"))
                ->first();

                $rptamanord = DB::table('rptamanejoorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion1 = '';
        if (!is_null($rptamanord)) {
            $idrpta  = $rptamanord->id;
            $observacion1 = $rptamanord->observacion;
        }

        
        $menuPr = [];
        $menuP = OpcionManejo::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionManejo::join('checkmanejo as m','m.id','=','checkmanejo.itemId')
                ->where('checkmanejo.nivel','=','2')
                ->whereNotNull('checkmanejo.itemId')
                ->whereNull('checkmanejo.deleted_at')
                ->select('checkmanejo.itemId','checkmanejo.nombre', DB::Raw("(SELECT rpta.situacion FROM rptacheckmanejo as rpta WHERE rpta.idRptaManejo = $idrpta AND rpta.idCheckManejo = checkmanejo.id) as rpta"))
                ->orderBy('checkmanejo.itemId','ASC')
                ->orderBy('checkmanejo.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (Object) $msec);
            } 
            $msec = [];
        }
    
        $opciones2 = (Object) $menuPr;


        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('L','A4');

		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE MANEJO'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 10;
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->cliente), 'TR', "C");
         
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->doc_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY() - $alto2);

		$fpdf::Ln();
        $alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->direccion_cliente), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode($orden->fecha), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('F. Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$alto2 = $fpdf::GetMultiCellHeight(235,$alto,utf8_decode(strtoupper($orden->documento_cita)), 'R', "C");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY($_x+235,$fpdf::GetY()-$alto2);
		$fpdf::Ln();

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'R', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);
        $fpdf::Ln();

        if (is_null($orden->params)) {
            $orden->params = '-@@-'; 
        }
        $params = explode('@@', $orden->params);

        $alto_1 = $fpdf::GetMultiCellHeight(140,$alto,utf8_decode(strtoupper($params[0])), 'R', "C");
        $alto_2 = $fpdf::GetMultiCellHeight(65,$alto,utf8_decode(strtoupper($params[1])), 'R', "C");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(140, $alto, utf8_decode(strtoupper($params[0])), 'B', "L");
		$fpdf::SetXY($_x+140,$fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto2, utf8_decode('VIN'), 'B', 0, "L");
		$fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(65, $alto, utf8_decode(strtoupper($params[1])), 'RB', "L");
		$fpdf::SetXY($_x+65,$fpdf::GetY()-$alto2);

        $fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(148, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("CORREGIDO"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',10);
        $alto = 4;
        
        foreach ($opciones2 as $opc) {
            $encabezado = $opc['header'];
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',10);
            $fpdf::Cell(265, $alto+2, utf8_decode($encabezado->nombre), 1, 0, "L");
          
            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',9);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $fpdf::SetX(15,$fpdf::GetY()+$alto);
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1, "C");
         
                // dd($alto2, $alto);
                $fpdf::SetFont('Arial','',9);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                // $fpdf::MultiCell(15, $alto, utf8_decode($item), 0, "C");
                // $fpdf::SetXY(30,$fpdf::getY()-$alto2);
                
                // $_y = $fpdf::getY();
                if (strlen($opc2->nombre) >= 90) {
                    $fpdf::MultiCell(148, $alto, utf8_decode($opc2->nombre), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(178,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(148, $alto2, utf8_decode($opc2->nombre),1, 0, "L");
                }
                // dd($_y,$fpdf::getY()-$alto2, $alto, $alto2);
                $fpdf::SetFont('Arial','B',9);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'N'){
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'C') {
                    $fpdf::SetTextColor(37,43,238);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                } else {
                    // $fpdf::SetTextColor(37,43,238);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                }
       
              
                $fpdf::Ln();
                $item++;
                $fpdf::SetTextColor(0);
            }
            // $fpdf::Ln();
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();


        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $alto = 6;
           
            $alto2 = $fpdf::GetMultiCellHeight(265,$alto,utf8_decode(strtoupper($observacion1)), 'RBL', "C");
       
            $fpdf::Cell(265, $alto2, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion2), 'RBL',0, "L");
            $_x = $fpdf::GetX();
            $fpdf::MultiCell(265, $alto, utf8_decode(strtoupper($observacion1)), 'RBL', "L");
            $fpdf::SetXY($_x+265,$fpdf::GetY()-$alto2);
        }

        
		$fpdf::Output("OrdenC_CheckListManejo-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
    }

    public function getPdfRevision ($id) {
        $orden = DB::table('ordentrabajo as orden')
                ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
                ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
                ->leftJoin('cita','cita.id','=','orden.idCita')
                ->where('orden.id',$id)
                ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
                DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente')
                ->first();
            
    
        $rptacalord = DB::table('rptacalidadorden')
                     ->where('idOrdenTrabajo','=',$id)
                     ->select('id','observacion')
                     ->first();

        $idrpta = 0;
        $observacion = '';
        if (!is_null($rptacalord)) {
            $idrpta  = $rptacalord->id;
            $observacion = $rptacalord->observacion;
        }

        // dd($idrpta,$id, $rptacalord);

        $menuPr = [];
        $menuP = OpcionCalidad::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();
        
        
        $menuS = OpcionCalidad::join('checkcalidad as m','m.id','=','checkcalidad.itemId')
                ->where('checkcalidad.nivel','=','2')
                ->whereNotNull('checkcalidad.itemId')
                ->whereNull('checkcalidad.deleted_at')
                ->select('checkcalidad.itemId','checkcalidad.nombre', DB::Raw("(SELECT rpta.situacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as rpta"), DB::Raw("(SELECT rpta.observacion FROM rptacheckcalidad as rpta WHERE rpta.idRptaCalidad = $idrpta AND rpta.idCheckCalidad = checkcalidad.id) as observacion"))
                ->orderBy('checkcalidad.itemId','ASC')
                ->orderBy('checkcalidad.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec);
            } 
            $msec = [];
        }
        
        $opciones1 = (Object) $menuPr;
    
        ########################################################################################
        $rptamanord = DB::table('rptamanejoorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion1 = '';
        if (!is_null($rptamanord)) {
            $idrpta  = $rptamanord->id;
            $observacion1 = $rptamanord->observacion;
        }

        
        $menuPr = [];
        $menuP = OpcionManejo::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->select('id','nombre')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionManejo::join('checkmanejo as m','m.id','=','checkmanejo.itemId')
                ->where('checkmanejo.nivel','=','2')
                ->whereNotNull('checkmanejo.itemId')
                ->whereNull('checkmanejo.deleted_at')
                ->select('checkmanejo.itemId','checkmanejo.nombre', DB::Raw("(SELECT rpta.situacion FROM rptacheckmanejo as rpta WHERE rpta.idRptaManejo = $idrpta AND rpta.idCheckManejo = checkmanejo.id) as rpta"))
                ->orderBy('checkmanejo.itemId','ASC')
                ->orderBy('checkmanejo.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (Object) $msec);
            } 
            $msec = [];
        }
    
        $opciones2 = (Object) $menuPr;
        
        #########################################################################################################
        $rptatallerord = DB::table('rptatallerorden')
                ->where('idOrdenTrabajo','=',$id)
                ->select('id','observacion')
                ->first();

        $idrpta = 0;
        $observacion2 = '';
        if (!is_null($rptatallerord)) {
            $idrpta  = $rptatallerord->id;
            $observacion2 = $rptatallerord->observacion;
        }

        // dd($idrpta, $observacion2);

        if ($idrpta != 0) {
            $rptas = DB::table('rptachecktaller as rchk')
                    ->leftJoin('checktaller as check','rchk.idCheckTaller','=','check.id')
                    ->where('rchk.idRptaTaller','=',$idrpta)
                    ->select('check.nombre','rchk.situacion as valor','rchk.indicacion','rchk.indicacion1','rchk.indicacion2','rchk.indicacion3')
                    ->orderBy('check.orden')
                    ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();
        } else {
            $rptas = DB::table('checktaller as check')
                    ->select('check.nombre', DB::Raw("'' as valor"), DB::Raw("'' as indicacion"),
                            DB::Raw("'' as indicacion1"), DB::Raw("'' as indicacion2"), DB::Raw("'' as indicacion3"))
                    ->orderBy('check.orden')
                    // ->orderBy('rchk.idCheckTaller','ASC')
                    ->get();

        }
        $opciones3 = $rptas;

        // dd($opciones3);
        // return array('detalles' => $opciones, 'rptas' => $rptas, 'observaciones' => $obs);

        ###################################################################################################################
        #   RESULTADOS DE ENCUESTA

        // if ($id != 0) {
            $rptasEncuestas = DB::table('preguntasencuesta as pe')
                            ->leftjoin('rptapreguntasencuesta as rpta','rpta.idPregunta','=','pe.id')
                            ->where('rpta.idOrden','=',$id)
                            ->select('pe.nombre as pregunta',
                            DB::Raw("(CASE WHEN pe.conRespuesta = '1' THEN rpta.puntuacion ELSE rpta.observacion END) as rpta")
                            )
                            ->get();
       

            if (count($rptasEncuestas) == 0 ) {
                $rptasEncuestas = DB::table('preguntasencuesta as pe')
                                ->select('pe.nombre as pregunta', DB::Raw("'' as rpta"))
                                ->get();
            }

        // dd($rptasEncuestas);
        ###################################################################################################################
        
        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('L','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
      
        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE CALIDAD'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 11;
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($orden->trabajador)), 'B', "L");
		$fpdf::SetXY(183,$_y);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Teléfono'), 'B', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(67, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'RB', "L");
		$fpdf::SetXY(67,$_y);
	
		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(100, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(82, $alto, utf8_decode("OBSERVACIONES"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',10);
        $alto = 4;
        
        foreach ($opciones1 as $opc) {
            $encabezado = $opc['header'];
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',10);
            $fpdf::Cell(265, $alto+2, utf8_decode($encabezado->nombre), 1, 0, "L");
          
            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',9);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $fpdf::SetX(15,$fpdf::GetY()+$alto);
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1, "C");
         
                // dd($alto2, $alto);
                $fpdf::SetFont('Arial','',9);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                // $fpdf::MultiCell(15, $alto, utf8_decode($item), 0, "C");
                // $fpdf::SetXY(30,$fpdf::getY()-$alto2);
                
                $_y = $fpdf::getY();
                if (strlen($opc2->nombre) >= 90) {
                    $fpdf::MultiCell(100, $alto, utf8_decode($opc2->nombre), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(130,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(100, $alto2, utf8_decode($opc2->nombre),1, 0, "L");
                }
                // dd($_y,$fpdf::getY()-$alto2, $alto, $alto2);
                $fpdf::SetFont('Arial','B',9);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'N'){
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                } else {
                    // $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                }
       
                $fpdf::SetTextColor(0);
                $_y = $fpdf::getY();
                if (strlen($opc2->observacion) >= 90) {
                    $fpdf::MultiCell(82, $alto, utf8_decode($opc2->observacion), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(130,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(82, $alto2, utf8_decode($opc2->observacion),1, 0, "C");
                }
       
                $fpdf::Ln();
                $item++;
                // $fpdf::SetTextColor(0);
            // break;
            }
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion), 'RBL',0, "L");
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($observacion)), 'RBL', "L");
            
        }

        #########################################################################################
        $fpdf::AddPage('L','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE MANEJO'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 11;
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($orden->trabajador)), 'B', "L");
		$fpdf::SetXY(183,$_y);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Teléfono'), 'B', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(67, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'RB', "L");
		$fpdf::SetXY(67,$_y);
	
		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::Cell(15, $alto, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::Cell(148, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto, utf8_decode("CORREGIDO"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',10);
        $alto = 4;
        
        foreach ($opciones2 as $opc) {
            $encabezado = $opc['header'];
            $fpdf::SetX(15);
            $fpdf::SetFont('Arial','B',10);
            $fpdf::Cell(265, $alto+2, utf8_decode($encabezado->nombre), 1, 0, "L");
          
            $cuerpo = $opc['body'];
            $fpdf::SetFont('Arial','',9);
            $fpdf::Ln();
            $item = 1;
            foreach ($cuerpo as $opc2) {
                $fpdf::SetX(15,$fpdf::GetY()+$alto);
                $alto2 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc2->nombre), 1, "C");
         
                // dd($alto2, $alto);
                $fpdf::SetFont('Arial','',9);
                $fpdf::Cell(15, $alto2, utf8_decode($item), 1, 0, "C");
                // $fpdf::MultiCell(15, $alto, utf8_decode($item), 0, "C");
                // $fpdf::SetXY(30,$fpdf::getY()-$alto2);
                
                // $_y = $fpdf::getY();
                if (strlen($opc2->nombre) >= 90) {
                    $fpdf::MultiCell(148, $alto, utf8_decode($opc2->nombre), 1, "L");
                    // $fpdf::SetXY(130,$fpdf::getY()-$alto2);
                    $fpdf::SetXY(178,$fpdf::GetY()-$alto2);
                } else {
                    $fpdf::Cell(148, $alto2, utf8_decode($opc2->nombre),1, 0, "L");
                }
                // dd($_y,$fpdf::getY()-$alto2, $alto, $alto2);
                $fpdf::SetFont('Arial','B',9);
                if($opc2->rpta == 'S') {
                    $fpdf::SetTextColor(5,122,31);
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'N'){
                    $fpdf::SetTextColor(206,3,3);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                } elseif($opc2->rpta == 'C') {
                    $fpdf::SetTextColor(37,43,238);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                } else {
                    // $fpdf::SetTextColor(37,43,238);
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                    $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                }
       
              
                $fpdf::Ln();
                $item++;
                $fpdf::SetTextColor(0);
            }
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        // $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
        //         ->where('isFirma','=',1)
        //         ->select('nombre')
        //         ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion1), 'RBL',0, "L");
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($observacion1)), 'RBL', "L");
            
        }

        #########################################################################################
        $fpdf::AddPage('L','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(60, 7, utf8_decode('CHECKLIST DE TALLER'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 11;
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($orden->trabajador)), 'B', "L");
		$fpdf::SetXY(183,$_y);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Teléfono'), 'B', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(67, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'RB', "L");
		$fpdf::SetXY(67,$_y);
	
		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
         
        $fpdf::MultiCell(34, $alto+3, utf8_decode("SEGÚN INDICACIÓN"), 1, "C");
        $fpdf::SetXY(49,$_Y_list);
        
        $fpdf::MultiCell(23, $alto, utf8_decode("25, 35, 45, 55, 65, 75, 85, 95"), 1, "C");
        $fpdf::SetXY(72,$_Y_list);
     
        $fpdf::MultiCell(16, $alto, utf8_decode("10, 30, 50, 70, 90"), 1, "C");
        $fpdf::SetXY(88,$_Y_list);
        
        $fpdf::MultiCell(14, $alto, utf8_decode("20, 40, 60, 80, 100"), 1, "C");
        $fpdf::SetXY(102,$_Y_list);
       
        $fpdf::MultiCell(76, $alto*3, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, "C");
        $fpdf::SetXY(178,$_Y_list);
        // $fpdf::Cell(57, $alto, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("NO OK"), 1, 0, "C");
        $fpdf::Cell(34, $alto*3, utf8_decode("CORREGIDO"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',9);
        $alto = 4;
        // dd($opciones3);

        foreach ($opciones3 as $opc) {
            $fpdf::SetXY(15,$fpdf::GetY());
            $alto2 = $fpdf::GetMultiCellHeight(76,$alto,utf8_decode($opc->nombre), 1, "C");
                     
            $fpdf::Cell(34, $alto2, utf8_decode($opc->indicacion),1, 0, "C");
            $fpdf::Cell(23, $alto2, utf8_decode($opc->indicacion1),1, 0, "C");
            // $fpdf::SetXY(49,$_Y_list);

            $fpdf::Cell(16, $alto2, utf8_decode($opc->indicacion2), 1,0, "C");
            // $fpdf::SetXY(72,$_Y_list);

            $fpdf::Cell(14, $alto2, utf8_decode($opc->indicacion3), 1,0, "C");
            // $fpdf::SetXY(102,$_Y_list);
            // $_y = $fpdf::GetY();
             
            $fpdf::MultiCell(76, $alto, utf8_decode($opc->nombre), 1, "L");
            $fpdf::SetXY(178,$fpdf::GetY()-$alto2);
       
            //dd($opc->valor);
            if($opc->valor == 'S') {
                $fpdf::SetTextColor(5,122,31);
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'N'){
                $fpdf::SetTextColor(206,3,3);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            } elseif($opc->valor == 'C') {
                $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            } else {
                // $fpdf::SetTextColor(37,43,238);
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
                $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            }
    
              
            $fpdf::Ln();
            // $fpdf::SetY($fpdf::GetY()+$alto2-$alto);
        
            // $item++;
            $fpdf::SetTextColor(0);
        // break;
        }

		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        // $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
        //         ->where('isFirma','=',1)
        //         ->select('nombre')
        //         ->first();
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion2), 'RBL',0, "L");
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($observacion2)), 'RBL', "L");
            
        }

        
        $fpdf::SetXY(138, $fpdf::GetY());
        $fpdf::SetFont('Arial','',12);
        $alto = 5;
        ##########################################################################################################

        $fpdf::AddPage('L','A4');
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(210);
       	$fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',11);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',10);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',13);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(100, 7,'',0,0,'C');
  	    $fpdf::Cell(70, 7, utf8_decode('RESULTADOS DE ENCUESTA'), 'B', 0, "C");
        $fpdf::Ln(8);
	
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 7;
		$tam_font = 11;
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Señor(es)'), 'LT', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'T', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha Emisión'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Ref. Cita'), 'L', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(235, $alto, utf8_decode(strtoupper($orden->documento_cita)), 'R', "L");
        $fpdf::SetXY(235,$_y);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($orden->trabajador)), 'B', "L");
		$fpdf::SetXY(183,$_y);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Teléfono'), 'B', 0, "L");
		$fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(67, $alto, utf8_decode(strtoupper($orden->telefono_tra)), 'RB', "L");
		$fpdf::SetXY(67,$_y);
	
		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
		$alto = 3;   
        $fpdf::SetXY(45, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',10);
        
        $fpdf::Cell(15, $alto+3, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::MultiCell(120, $alto+3, utf8_decode("DESCRIPCIÓN"), 1, "C");
        $fpdf::SetXY(180,$_Y_list);
        
        $fpdf::MultiCell(65, $alto+3, utf8_decode("RESULTADO"), 1, "C");
        $fpdf::SetXY(72,$_Y_list);
     
        // $fpdf::MultiCell(16, $alto, utf8_decode("10, 30, 50, 70, 90"), 1, "C");
        // $fpdf::SetXY(88,$_Y_list);
        
        // $fpdf::MultiCell(14, $alto, utf8_decode("20, 40, 60, 80, 100"), 1, "C");
        // $fpdf::SetXY(102,$_Y_list);
       
        // $fpdf::MultiCell(76, $alto*3, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, "C");
        // $fpdf::SetXY(178,$_Y_list);
        // // $fpdf::Cell(57, $alto, utf8_decode("C = CAMBIAR      I = INSPECCIONAR"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("OK"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("NO OK"), 1, 0, "C");
        // $fpdf::Cell(34, $alto*3, utf8_decode("CORREGIDO"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',9);
        $alto = 4;
        $cont = 1; 
        // dd($rptasEncuestas);
        foreach ($rptasEncuestas as $opc) {
            $fpdf::SetXY(45,$fpdf::GetY());
            $alto2 = $fpdf::GetMultiCellHeight(76,$alto,utf8_decode($opc->rpta), 1, "C");
            
            $fpdf::Cell(15, $alto2, utf8_decode($cont<=9?'0'.$cont:$cont), 1, 0, "C");
            
            // 
            // $fpdf::Cell(34, $alto2, utf8_decode($opc->indicacion),1, 0, "C");
            // $fpdf::Cell(23, $alto2, utf8_}decode($opc->indicacion1),1, 0, "C");
            // $fpdf::SetXY(49,$_Y_list);

            // $fpdf::Cell(16, $alto2, utf8_decode($opc->indicacion2), 1,0, "C");
            // $fpdf::SetXY(72,$_Y_list);

            // $fpdf::Cell(14, $alto2, utf8_decode($opc->indicacion3), 1,0, "C");
            // $fpdf::SetXY(102,$_Y_list);
            // $_y = $fpdf::GetY();
             
            $fpdf::MultiCell(120, $alto, utf8_decode($opc->pregunta), 1, "L");
            $fpdf::SetXY(180,$fpdf::GetY()-$alto2);
            
            $fpdf::MultiCell(65, $alto, utf8_decode($opc->rpta), 1, "C");
            $fpdf::SetXY(180,$fpdf::GetY()-$alto2);
            
            // $fpdf::MultiCell(120, $alto+3, utf8_decode("DESCRIPCIÓN"), 1, "C");
            
            ###################
            
            ###################
            
         
            // $fpdf::SetXY(178,$fpdf::GetY()-$alto2);
       
            // if($opc->valor == 'S') {
            //     $fpdf::SetTextColor(5,122,31);
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            // } elseif($opc->valor == 'N'){
            //     $fpdf::SetTextColor(206,3,3);
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            // } else {
            //     $fpdf::SetTextColor(37,43,238);
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            // }
    
              
            $fpdf::Ln();
            // $fpdf::SetY($fpdf::GetY()+$alto2-$alto);
        
            // $item++;
            $fpdf::SetTextColor(0);

            $cont++;
        // break;
        }

        $fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);
     
        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50,$alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            // $fpdf::Cell(183, $alto, utf8_decode($observacion1), 'RBL',0, "L");
            $fpdf::MultiCell(183, $alto, utf8_decode(strtoupper($observacion1)), 'RBL', "L");
            
        }


        // $


		$fpdf::Output("OrdenC_CheckTodos-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;


        // dd($opciones1, $opciones2, $opciones3);
    }

    public function getPdfEncuesta ($id) {
        $orden = DB::table('ordentrabajo as orden')
            ->leftJoin('persona as cli','orden.idCliente','=','cli.id')
            ->leftJoin('trabajador as tra','orden.idPersonal','=','tra.id')
            ->leftJoin('cita','cita.id','=','orden.idCita')
            ->where('orden.id',$id)
            ->select('orden.id', DB::raw("CONCAT('OD', LPAD(orden.serie,2,'0') ,'-', LPAD(orden.numero,8,'0')) as documento"),DB::raw("CONCAT('CT', LPAD(cita.serie,3,'0') ,'-', LPAD(cita.numero,8,'0')) as documento_cita"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
            DB::raw("DATE_FORMAT(orden.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','orden.placa','orden.total','cli.documento as documento_cli', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente', 'cli.telefono as telefono',
            DB::raw("(SELECT CONCAT((CASE WHEN ct.marcamodelo IS NOT NULL THEN ct.marcamodelo ELSE '-' END), '@@', ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = $id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"))
            ->first();


        ###################################################################################################################
        #   RESULTADOS DE ENCUESTA

        $itemsencuesta =  DB::table('preguntasencuesta as pe')
                          ->whereNull('pe.deleted_at')
                          ->select('pe.nombre',
                          DB::Raw("(SELECT (CASE pe.conRespuesta WHEN 0 THEN rp.observacion ELSE rp.puntuacion END) FROM rptapreguntasencuesta as rp WHERE rp.idOrden = $id AND rp.idPregunta = pe.id) as rpta"),
                          DB::Raw("(SELECT ps.estadoContactabilidad FROM rptapreguntasencuesta as ps WHERE ps.idOrden = $id AND ps.idPregunta = pe.id AND ps.estadoContactabilidad IS NOT NULL) as estadoContactabilidad"))
                          ->get();

        ###################################################################################################################

        $fpdf = new Fpdf();
        $fpdf::SetTitle(utf8_decode('Encuesta de Orden de Trabajo'));
        // $fpdf::AliasNbPages();
        $fpdf::AddPage('P','A4');
        $fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,50,35);
        $fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

        $fpdf::SetXY(210, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(70,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
        $fpdf::Cell(70,$alto,utf8_decode("ORDEN DE TRABAJO"),'RL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::Cell(70, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(210);
        $fpdf::Cell(70, $alto, $orden->documento, 'RBL',0, 'C');

        $fpdf::Ln(16);
        $alto = 5;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',10);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',8);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");

        $fpdf::SetFont('Arial','B',10);
        $fpdf::Ln(10);
        $fpdf::SetX(15);
        $fpdf::Cell(58, 7,'',0,0,'C');
        $fpdf::Cell(65, 7, utf8_decode('RESULTADOS DE ENCUESTA'), 'B', 0, "C");
        $fpdf::Ln(8);

        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $alto = 5;
        $tam_font = 9;
        
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T', 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->cliente)), 'TR', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
        $fpdf::Ln($alto2);
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode(($orden->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();

        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->doc_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln($alto2);
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln($alto2);
        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($orden->fecha)), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Fecha Emisión'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($orden->fecha)), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        $fpdf::SetXY(15, $fpdf::GetY());
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->placa)), 0, "L");

        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Ref. Cita'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");
    
        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->documento_cita)), 0, "L");
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Placa'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->placa)), 'R', "L");
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
    
        $fpdf::Ln();
        
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($orden->telefono_tra)), 0, "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Asesor'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($orden->trabajador)), 0, "L");
        // $fpdf::SetXY(88,$_y);
        $fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
    
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('Teléfono'), 0, 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "B");

        $_y = $fpdf::GetY();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($orden->telefono)), 'R', "L");
        // $fpdf::SetXY(35,$_y)
        $fpdf::SetXY($_x+40, $fpdf::GetY()-$alto2);
        $fpdf::Ln();
    
        if (is_null($orden->params)) {
            $orden->params = '-@@-';
        } 
        
        $params = explode('@@',$orden->params);
        $alto_1 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode(strtoupper($params[0])), 'B', "L");
        $alto_2 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($params[1])), 'RB', "L");

        
        if ($alto_1 > $alto_2) {
            $alto2 = $alto_1;
        } else {
            $alto2 = $alto_2;
        }

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($params[0])), 'R', "L");
    
        $fpdf::Cell(25, $alto2, utf8_decode('Marca/Modelo'), 'L', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($params[0])), 'R', "L");
        $fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

        $fpdf::Ln();

        $fpdf::SetXY(15, $fpdf::GetY());
        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(25, $alto2, utf8_decode('Kilometraje'), 'LB', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "L");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(88, $alto, utf8_decode(strtoupper($params[1])), 'B', "L");
        // $fpdf::SetXY(88,$_y);
        //$fpdf::SetXY($_x+88, $fpdf::GetY()-$alto2);
        $fpdf::SetXY($_x+88, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);

        $fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::Cell(20, $alto2, utf8_decode('VIN'), 'B', 0, "L");
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'B', 0, "B");

        $_y = $fpdf::GetY();
        $_x = $fpdf::GetX();
        
        $fpdf::SetFont('Arial','',$tam_font);
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($params[2])), 'RB', "L");
        // $fpdf::SetXY(35,$_y)
        $fpdf::SetXY($_x+40, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);


        $fpdf::SetFillColor(255);
        $fpdf::Ln(12);
        $alto = 3;   
        $fpdf::SetXY(25, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',9);

        $fpdf::Cell(15, $alto+3, utf8_decode("ITEM"), 1, 0, "C");
        $fpdf::MultiCell(100, $alto+3, utf8_decode("DESCRIPCIÓN"), 1, "C");
        $fpdf::SetXY(140,$_Y_list);

        $fpdf::MultiCell(45, $alto+3, utf8_decode("RESULTADO"), 1, "C");
        $fpdf::SetXY(45,$_Y_list);
        
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',9);
        $alto = 5;
        $cont = 1; 
        // dd($itemsencuesta);
        foreach ($itemsencuesta as $opc) {

            $fpdf::SetXY(25,$fpdf::GetY());
            $alto_1 = $fpdf::GetMultiCellHeight(100,$alto,utf8_decode($opc->nombre), 1, "C");
            $alto_2 = $fpdf::GetMultiCellHeight(45,$alto,utf8_decode($opc->rpta), 1, "C");
            
            if ($alto_1 > $alto_2) {
                $alto2 = $alto_1;
            } else {
                $alto2 = $alto_2;
            }
            // $fpdf::MultiCell(15, $alto2, utf8_decode($cont<=9?'0'.$cont:$cont), 1,0, "C");
            // $fpdf::SetXY(15,$fpdf::GetY()-$alto2);
         
            $fpdf::Cell(15, $alto2, utf8_decode($cont<=9?'0'.$cont:$cont), 1, 0, "C");
            $_x = $fpdf::GetX();
            $_y = $fpdf::GetY();
            $fpdf::Cell(100, $alto2, utf8_decode($opc->nombre), 1, 0, "L");
            /*$fpdf::SetXY($_x+100,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);*/
            
            $_x = $fpdf::GetX();
            $_y = $fpdf::GetY();
            
            $rpta = ($opc->nombre == 'Estado de Contactabilidad'?$opc->estadoContactabilidad:$opc->rpta);
            $fpdf::MultiCell(45, $alto, utf8_decode($rpta == 'NC'? 'No Contestó': ($rpta=='C'?'Contestó':($rpta=='A'?'Apagado':$rpta))), 1, "C");
            $fpdf::SetXY($_x+45,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
            
            // $fpdf::MultiCell(120, $alto+3, utf8_decode("DESCRIPCIÓN"), 1, "C");
            
            ###################
            
            ###################
            
        
            // $fpdf::SetXY(178,$fpdf::GetY()-$alto2);

            // if($opc->valor == 'S') {
            //     $fpdf::SetTextColor(5,122,31);
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            // } elseif($opc->valor == 'N'){
            //     $fpdf::SetTextColor(206,3,3);
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            // } else {
            //     $fpdf::SetTextColor(37,43,238);
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode(""), 1, 0, "C");
            //     $fpdf::Cell(34, $alto2, utf8_decode("X"), 1, 0, "C");
            // }

            
            $fpdf::Ln();
            // $fpdf::SetY($fpdf::GetY()+$alto2-$alto);

            // $item++;
            $fpdf::SetTextColor(0);

            $cont++;
        // break;
        }

        $fpdf::Ln(5);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);

        $firma = ImagenTemporal::where('idOrdenTrabajo','=',$id)
                ->where('isFirma','=',1)
                ->select('nombre')
                ->first();

        if (!is_null($firma)) {
            $fpdf::SetXY(30, $fpdf::getY()+$alto);
            $exist = \Storage::disk('local_temp')->exists($firma->nombre);
            if ($exist) {
                $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 50);
                $fpdf::SetXY(60, $fpdf::getY()-($alto*5));
            } else {
                $fpdf::Cell(50, $alto);
            }

            // $fpdf::Image("storage/imagenes_temp/".$firma->nombre, 45);
            // $fpdf::Ln(2);
            // $fpdf::SetXY(15, $fpdf::getY());
            $fpdf::Cell(50, $alto+5, utf8_decode("FIRMA DEL CLIENTE"), 'T',0, "C", false);
            
            $fpdf::Ln(10);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", false);
            $fpdf::Ln();
            
            $fpdf::SetTextColor(206,3,3);
            $fpdf::SetX(15);
            $fpdf::Cell(183, $alto, utf8_decode(""), 'RBL',0, "L");        
        }


        // $


        $fpdf::Output("Encuesta_OrdenC-".$orden->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
        $fpdf::Output();

        exit;

    }
    public function getReporteEncuesta (Request $request) {
        $documento 	 = (!is_null($request->get('doc'))?$request->get('doc'):'');
        $color       = (!is_null($request->get('color'))?'#'.$request->get('color'):'');
		$cliente     = (!is_null($request->get('cli'))?$request->get('cli'):'');
		$comprobante = (!is_null($request->get('comp'))?$request->get('comp'):'');
		$cita        = (!is_null($request->get('cita'))?$request->get('cita'):'');
		$trabajador = (!is_null($request->get('reg'))?$request->get('reg'):'');
		$placa        = (!is_null($request->get('placa'))?$request->get('placa'):'');
    	$fechaI 	 = (!is_null($request->get('fi'))?$request->get('fi'):'');
    	$fechaF	 = (!is_null($request->get('ff'))?$request->get('ff'):'');
        
        // dd($fechaF, $fechaI, $color);
        $ordenes = DB::table('ordentrabajo as ot')
                    ->leftjoin('cita as c','c.id','=','ot.idCita')
                    ->leftjoin('persona as cl','cl.id','=','ot.idCliente')
                    ->leftjoin('trabajador as t','t.id','=','ot.idPersonal')
                    ->where('ot.situacion','=','F')
                    ->where(function ($qq) use ($cliente) {
                    $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                    ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
                    })
                    ->where('cl.documento','LIKE', '%'.$documento.'%')
                    ->where(DB::Raw("CONCAT(ot.serie,'-',ot.numero)"),'LIKE', '%'.$comprobante.'%')
                    ->where(DB::Raw("CONCAT(c.serie,'-',c.numero)"),'LIKE', '%'.$cita.'%')
                    ->where(DB::Raw("CONCAT(t.apellidos,' ',t.nombres)"),'LIKE', '%'.$trabajador.'%')
                    ->where('ot.placa','LIKE', '%'.$placa.'%');

        if ($fechaI != '') {
            $ordenes = $ordenes->where('ot.fecha','>=',$fechaI);
        }

        if ($fechaF != '') {
            $ordenes = $ordenes->where('ot.fecha','<=',$fechaF);
        }

        if ($color != '#fff') {
            switch ($color) {
            case '#c3f2e1':
                $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [17,20]);
                break;
            case '#ffe0cc':
                $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [14,16]);
                break;
            case '#ffccd3':
                $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [0,13]);
                break;
            default:
                $ordenes = $ordenes->where('ot.puntuacionEncuesta', -1);
                break;
            }
        }

        $ordenes =  $ordenes->select('ot.id', DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa',
            DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as documentocita"), 
            DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa',
            DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as documento"), 
            DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
            'cl.documento as doc', 'cl.correoElectronico as correo_electronico', 'cl.telefono',
            DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),'ot.situacion', 
            DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
            DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente',
            'ot.puntuacionEncuesta',
            DB::raw("(SELECT CONCAT((CASE WHEN ct.marcamodelo IS NOT NULL THEN ct.marcamodelo ELSE '-' END), '@@', ct.kilometraje,'@@', ct.vin)  FROM cotizacion as ct LEFT JOIN detalleordentrabajo as dto ON dto.idCotizacion = ct.id WHERE dto.idOrdenTrabajo = ot.id AND ct.kilometraje IS NOT NULL AND ct.vin IS NOT NULL ORDER BY ct.id ASC LIMIT 1) as params"),
            DB::Raw("(CASE WHEN ot.puntuacionEncuesta = -1 THEN 'Sin Aplicar' ELSE 'Aplicada' END) as situacionEncuesta"))
            ->orderBy('ot.created_at','DESC');

        $lista = $ordenes->get();
        $excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Rep. Encuestas");
		$hoja1->setCellValue('A1','LISTADO DE ENCUESTAS');
		$hoja1->mergeCells('A1:O1');
		$hoja1->getStyle('A1:O1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','FECHA');
		$hoja1->setCellValue('B2','CLIENTE');
        $hoja1->setCellValue('C2','TELEFONO');
        
		$hoja1->setCellValue('D2','N° ORDEN');
		$hoja1->setCellValue('E2','N° CITA');
		$hoja1->setCellValue('F2','PLACA DE AUTO');
        $hoja1->setCellValue('G2','MARCA/MODELO');
        $hoja1->setCellValue('H2','KILOMETRAJE');
        $hoja1->setCellValue('I2','VIN');
        
		$hoja1->setCellValue('J2','SITUACIÓN');
		$hoja1->setCellValue('K2','TOTAL');
        $hoja1->setCellValue('L2','¿SE APLICÓ ENCUESTA?');
        $hoja1->setCellValue('M2','PUNTAJE');
        $hoja1->setCellValue('N2','NPS');
        $hoja1->setCellValue('O2','REGISTRADO POR');
		$hoja1->setCellValue('P2','REGISTRADO EL');
        
		$hoja1->getStyle('A2:P2')->applyFromArray($this->estilo_header);
		
        $j = 3;
		foreach ($lista as $value) {
            $hoja1->setCellValue('A'.$j,$value->fecha);
            $hoja1->setCellValue('B'.$j,$value->doc.' - '.$value->cliente);
            $hoja1->setCellValue('C'.$j,$value->telefono);
            $hoja1->setCellValue('D'.$j,$value->documento);
            $hoja1->setCellValue('E'.$j,$value->documentocita);
            $hoja1->setCellValue('F'.$j,$value->placa);
            $params = explode('@@', $value->params);
            $hoja1->setCellValue('G'.$j,$params[0]);
            $hoja1->setCellValue('H'.$j,$params[1]);
            $hoja1->setCellValue('I'.$j,$params[2]);
            $hoja1->setCellValue('J'.$j,($value->situacion == 'F'?'Finalizado':''));
            $hoja1->setCellValue('K'.$j,$value->total);
            $hoja1->setCellValue('L'.$j,$value->situacionEncuesta);
            $hoja1->setCellValue('M'.$j,$value->puntuacionEncuesta>0?$value->puntuacionEncuesta:'');
            $hoja1->setCellValue('N'.$j,
            ($value->puntuacionEncuesta > 0 && $value->puntuacionEncuesta <=13?'Retractor':
            ($value->puntuacionEncuesta > 13 && $value->puntuacionEncuesta <=16?'Neutro':
            ($value->puntuacionEncuesta > 16 && $value->puntuacionEncuesta <=20?'Promotor':'')))
            );
            $hoja1->setCellValue('O'.$j,$value->trabajador);
            $hoja1->setCellValue('P'.$j,$value->fechaR);

            if ($value->puntuacionEncuesta > 0 && $value->puntuacionEncuesta <=13) {    
                $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_contentR);
            } elseif ($value->puntuacionEncuesta > 13 && $value->puntuacionEncuesta <=16) {
                $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_contentA);
            } elseif ($value->puntuacionEncuesta > 16 && $value->puntuacionEncuesta <=20) {
                $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_contentV);
            } else {
                $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_contentB);
            }
            $j++;
		}

        foreach(range('A','P') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		// $objWriter->setPreCalculateFormulas(true);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Encuestas-'.date('Y-m-d H:i').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
    }
    public function getOrdenF (Request $request) {
        $documento 	 = $request->get('documento');
        $color       = $request->get('colorPtje');
		$cliente     = $request->get('cliente');
		$comprobante = $request->get('comprobante');
		$cita        = $request->get('cita');
		$trabajador = $request->get('trabajador');
		$placa        = $request->get('placa');

    	$fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
        $ordenes = DB::table('ordentrabajo as ot')
                      ->leftjoin('cita as c','c.id','=','ot.idCita')
                      ->leftjoin('persona as cl','cl.id','=','ot.idCliente')
                      ->leftjoin('trabajador as t','t.id','=','ot.idPersonal')
                      ->leftjoin('verificacionchecklist as vcl','vcl.idOrdenTrabajo','=','ot.id')
                      ->where('ot.situacion','=','F')
                      ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                        ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
                      })
                      ->where('cl.documento','LIKE', '%'.$documento.'%')
                      ->where(DB::Raw("CONCAT(ot.serie,'-',ot.numero)"),'LIKE', '%'.$comprobante.'%')
                      ->where(DB::Raw("CONCAT(c.serie,'-',c.numero)"),'LIKE', '%'.$cita.'%')
                      ->where(DB::Raw("CONCAT(t.apellidos,' ',t.nombres)"),'LIKE', '%'.$trabajador.'%')
                      ->where('ot.placa','LIKE', '%'.$placa.'%');
		if ($fechaI != '') {
			$ordenes = $ordenes->where('ot.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$ordenes = $ordenes->where('ot.fecha','<=',$fechaF);
		}

        if ($color != '#fff') {
            switch ($color) {
                case '#c3f2e1':
                    $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [17,20]);
                    break;
                case '#ffe0cc':
                    $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [14,16]);
                    break;
                case '#ffccd3':
                    $ordenes = $ordenes->whereBetween('ot.puntuacionEncuesta', [0,13]);
                    break;
                default:
                    $ordenes = $ordenes->where('ot.puntuacionEncuesta', -1);
                    break;
            }
        }

        $ordenes =  $ordenes->select('ot.id', DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa',
                    DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as documentocita"), 
                    DB::Raw("FORMAT(ot.total,2) as total"),'ot.placa','vcl.rptaVerifCheckCalidad', 'vcl.rptaVerifCheckManejo',
                    'vcl.observaciones as obs_checklist', 'vcl.id as id_verificacion',
                    DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as documento"), 
                    DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
                    'cl.documento as doc', 'cl.correoElectronico as correo_electronico', 'cl.telefono',
                    DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),'ot.situacion', 
                    DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
                    DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente',
                    'ot.puntuacionEncuesta',
                    DB::Raw("(SELECT CONCAT((CASE WHEN cot.marcamodelo IS NOT NULL THEN cot.marcamodelo ELSE '-' END), '@@' , cot.kilometraje, '@@' , cot.vin) FROM detallecotizacionorden as dct JOIN cotizacion as cot ON cot.id = dct.idCotizacion WHERE cot.marcamodelo IS NOT NULL LIMIT 1) as dets"),
                    DB::Raw("(CASE WHEN ot.puntuacionEncuesta = -1 THEN 'Sin Aplicar' ELSE CONCAT('Aplicada/Ptje: ',ot.puntuacionEncuesta) END) as situacionEncuesta"))
                    ->orderBy('ot.created_at','DESC');


		$lista = $ordenes->get();
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
		
		$lista = $ordenes->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

    	return ['ordenes' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Orden Finalizada':' Órdenes Finalizadas'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];

    }
    
    public function getDetalles ($id, Request $request) {
		$detalles = DB::table('cotizacion')
					->leftJoin('detallecotizacion as det','det.idCotizacion','=','cotizacion.id')
					->where('cotizacion.id','=',$id)
					->select('det.cantidad','det.descripcion', DB::Raw("(CASE WHEN det.tipoDetalle = 'S' THEN 'Servicio' ELSE (CASE WHEN det.tipoDetalle = 'P' THEN 'Producto' ELSE 'Auto' END) END) as tipo"),
						DB::Raw("FORMAT(det.precio,2) as precio"),
						DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id', DB::Raw("FORMAT(cotizacion.total,2) as total"))
					->orderBy('det.tipoDetalle','ASC')
					->get();

		$total = 0;
		if (count($detalles)) {
			$total = $detalles[0]->total;
		}

    	return ['detalles' => $detalles,'total' => $total];
	}

    public function getcheckinventario (Request $request) {
        $id = Auth::user()->usuarioId;
      
        $menuPr = [];
        $menuP = OpcionInventario::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionInventario::join('checkinventario as m','m.id','=','checkinventario.itemId')
                ->where('checkinventario.nivel','=','2')
                ->whereNotNull('checkinventario.itemId')
                ->whereNull('checkinventario.deleted_at')
                ->select('checkinventario.*')
                ->orderBy('checkinventario.itemId','ASC')
                ->orderBy('checkinventario.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
            } else {
                if ($menu_p->estado == 'S') {
                    $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
                }
            }
            $msec = [];
        }
    
        
        $img  = ImagenTemporal::whereNull('idOrdenTrabajo')
                        ->where('idPersonal','=',$id)
                        ->where('isFirma','=','1')
                        ->select(DB::Raw("CONCAT('/storage/imagenes_temp/',nombre) as url"), 'observacion','id')
                        ->orderBy('created_at','DESC')
                        ->first();
                        
        return array('detalles' => $menuPr, 'rptas' => Session::get('rptas'),'firma' => (!is_null($img)?$img->url:null), 
                    'observacion' => (!is_null($img)?$img->observacion:null));
                      
    }

    public function getcheckcalidad (Request $request) {
        $menuPr = [];
        $menuP = OpcionCalidad::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionCalidad::join('checkcalidad as m','m.id','=','checkcalidad.itemId')
                ->where('checkcalidad.nivel','=','2')
                ->whereNotNull('checkcalidad.itemId')
                ->whereNull('checkcalidad.deleted_at')
                ->select('checkcalidad.*')
                ->orderBy('checkcalidad.itemId','ASC')
                ->orderBy('checkcalidad.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
            } else {
                if ($menu_p->estado == 'S') {
                    $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
                }
            }
            $msec = [];
        }
        
        $rptas = RespuestaCalidadOrden::leftJoin('rptacheckcalidad as rchk','rchk.idRptaCalidad','=','rptacalidadorden.id')->where('rptacalidadorden.idOrdenTrabajo','=',$request->get('ordenId'))
                ->select('rchk.idCheckCalidad as id','rchk.situacion as valor','rchk.observacion','rptacalidadorden.observacion as observacion_gen')
                ->orderBy('rchk.idCheckCalidad','ASC')
                ->get();

        $obs = '';
        if (count($rptas) > 0) {
            $obs = $rptas[0]->observacion_gen;
        }
        
        return array('detalles' => $menuPr, 'rptas' => $rptas, 'observaciones' => $obs);
        
    }

    public function guardarcheckcalidad (Request $request) {
        $band = true;
        $errors = [];
        DB::beginTransaction();
        try{
            $id = $request->get('ordenid');
            $valid = RespuestaCalidadOrden::where('idOrdenTrabajo','=',$id)
                     ->first();
            if (is_null($valid)) {
                $a = new RespuestaCalidadOrden;
                $a->idOrdenTrabajo = $id;
            } else {
                $a = RespuestaCalidadOrden::find($valid->id);
            }

            $a->observacion = $request->get('observacion');
            if (is_null($valid)) {
                $a->save();
            } else {
                $a->update();
            }
           
            $idR = $a->id;
            $rptas = $request->get('rptas');
            $rptas = implode(',',$rptas);
            // dd($rptas);
            $arr = json_decode($rptas);
            // $arr = explode(',', $rptas);
            foreach ($arr as $av) {
                $r = RespuestaCheckCalidad::where('idRptaCalidad','=',$idR)
                    ->where('idCheckCalidad','=', $av->id)
                    ->first();
                if (is_null($r)) {
                    $u = new RespuestaCheckCalidad;
                    $u->idRptaCalidad = $idR;
                    $u->idCheckCalidad = $av->id;
                } else {
                    $u = RespuestaCheckCalidad::find($r->id);
                }
                $u->observacion = (!is_null($av->observacion)?$av->observacion:null);
                $u->situacion = $av->valor;
                
                if (is_null($r)) {
                    $u->save();
                } else {
                    $u->update();
                }
            }
            
            $errors[] = 'CheckList de Calidad Registrado Correctamente';
    
        }catch(\Exception $ex){
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }
    
        DB::commit();
    
        return ['errores' => (object)$errors, 'estado' => $band];
    
    }

    public function getcheckmanejo (Request $request) {
        $menuPr = [];
        $menuP = OpcionManejo::where('nivel','=','1')
            ->whereNull('deleted_at')
            ->orderBy('itemId','ASC')
            ->orderBy('orden','ASC')
            ->get();

        $menuS = OpcionManejo::join('checkmanejo as m','m.id','=','checkmanejo.itemId')
                ->where('checkmanejo.nivel','=','2')
                ->whereNotNull('checkmanejo.itemId')
                ->whereNull('checkmanejo.deleted_at')
                ->select('checkmanejo.*')
                ->orderBy('checkmanejo.itemId','ASC')
                ->orderBy('checkmanejo.orden','ASC')
                ->get();
        
        foreach($menuP as $menu_p) {
            $msec = [];
            foreach ($menuS as $menu_s) {
                if ($menu_p->id == $menu_s->itemId) {
                    $msec [] = $menu_s;
                }
            }

            if (count($msec)>0) {
                $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
            } else {
                if ($menu_p->estado == 'S') {
                    $menuPr[] = array('header' => $menu_p, 'body' => (object) $msec, 'cantSubs' => count($msec));
                }
            }
            $msec = [];
        }
    
        $rptas = RespuestaManejoOrden::leftJoin('rptacheckmanejo as rchk','rchk.idRptaManejo','=','rptamanejoorden.id')->where('rptamanejoorden.idOrdenTrabajo','=',$request->get('ordenId'))
                ->select('rchk.idCheckManejo as id','rchk.situacion as valor','rptamanejoorden.observacion as observacion_gen')
                ->orderBy('rchk.idCheckManejo','ASC')
                ->get();

        $obs = '';
        if (count($rptas) > 0) {
            $obs = $rptas[0]->observacion_gen;
        }
        
        
        return array('detalles' => $menuPr, 'rptas' => $rptas, 'observaciones' => $obs);
        
    }

    public function getchecktaller (Request $request) {
        $opciones = OpcionTaller::select('id','nombre')
                    ->orderBy('orden','ASC')
                    ->get();

        // $menuPr[] = array('header' => $opciones);

        $rptas = RespuestaTallerOrden::leftJoin('rptachecktaller as rchk','rchk.idRptaTaller','=','rptatallerorden.id')
                ->where('rptatallerorden.idOrdenTrabajo','=',$request->get('ordenId'))
                ->select('rchk.idCheckTaller as id','rchk.situacion as valor','rchk.indicacion','rchk.indicacion1','rchk.indicacion2','rchk.indicacion3','rptatallerorden.observacion as observacion_gen')
                ->orderBy('rchk.idCheckTaller','ASC')
                ->get();

        $obs = '';
        if (count($rptas) > 0) {
            $obs = $rptas[0]->observacion_gen;
        }
        
        return array('detalles' => $opciones, 'rptas' => $rptas, 'observaciones' => $obs);
    }

    public function guardarchecktaller (Request $request) {
        $band = true;
        $errors = [];
        DB::beginTransaction();
        try{
            $id = $request->get('ordenid');
            $valid = RespuestaTallerOrden::where('idOrdenTrabajo','=',$id)
                     ->first();
            if (is_null($valid)) {
                $a = new RespuestaTallerOrden;
                $a->idOrdenTrabajo = $id;
            } else {
                $a = RespuestaTallerOrden::find($valid->id);
            }

            $a->observacion = $request->get('observacion');
            if (is_null($valid)) {
                $a->save();
            } else {
                $a->update();
            }
          
            $idR = $a->id;
            $rptas = $request->get('rptas');
            // $rptas = implode(',',$rptas);
            $arr = json_decode($rptas);
            // $arr = explode(',', $rptas);
            foreach ($arr as $av) {
                $r = RespuestaCheckTaller::where('idRptaTaller','=',$idR)
                    ->where('idCheckTaller','=', $av->id)
                    ->first();
                if (is_null($r)) {
                    $u = new RespuestaCheckTaller;
                    $u->idRptaTaller = $idR;
                    $u->idCheckTaller = $av->id;
                } else {
                    $u = RespuestaCheckTaller::find($r->id);
                }
                $u->situacion = $av->valor;
                // $u->indicacion = strtoupper($av->indicacion);
                // $u->indicacion1 = strtoupper($av->indicacion1);
                // $u->indicacion2 = strtoupper($av->indicacion2);
                // $u->indicacion3 = strtoupper($av->indicacion3);
                
                if (is_null($r)) {
                    $u->save();
                } else {
                    $u->update();
                }
            }
            
            $errors[] = 'CheckList de Taller Registrado Correctamente';
    
        }catch(\Exception $ex){
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }
    
        DB::commit();
    
        return ['errores' => (object)$errors, 'estado' => $band];
    
    }

    
    public function guardarcheckmanejo (Request $request) {
        $band = true;
        $errors = [];
        DB::beginTransaction();
        try{
            $id = $request->get('ordenid');
            $valid = RespuestaManejoOrden::where('idOrdenTrabajo','=',$id)
                     ->first();
            if (is_null($valid)) {
                $a = new RespuestaManejoOrden;
                $a->idOrdenTrabajo = $id;
            } else {
                $a = RespuestaManejoOrden::find($valid->id);
            }
            $a->observacion = $request->get('observacion');
            if (is_null($valid)) {
                $a->save();
            } else {
                $a->update();
            }
            
            $idR = $a->id;
            $rptas = $request->get('rptas');
            // $rptas = implode(',',$rptas);
            $arr = json_decode($rptas);
            // $arr = explode(',', $rptas);
            foreach ($arr as $av) {
                $r = RespuestaCheckManejo::where('idRptaManejo','=',$idR)
                    ->where('idCheckManejo','=', $av->id)
                    ->first();
                if (is_null($r)) {
                    $u = new RespuestaCheckManejo;
                    $u->idRptaManejo = $idR;
                    $u->idCheckManejo = $av->id;
                } else {
                    $u = RespuestaCheckManejo::find($r->id);
                }
                $u->situacion = $av->valor;
                
                if (is_null($r)) {
                    $u->save();
                } else {
                    $u->update();
                }
            }
            
            $errors[] = 'CheckList de Manejo Registrado Correctamente';
    
        }catch(\Exception $ex){
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }
    
        DB::commit();
    
        return ['errores' => (object)$errors, 'estado' => $band];
    
    }

    
    public function getcotizaciones ($id, Request $request) {
        $detalles =  DetalleOrdenTrabajo::leftJoin('cotizacion as c','c.id','=','detalleordentrabajo.idCotizacion')
                    ->where('detalleordentrabajo.idOrdenTrabajo','=',$id)
                    ->where('detalleordentrabajo.situacion','=','V')
                    ->select(DB::Raw("CONCAT('C', LPAD(c.serie,2,'0') ,'-', LPAD(c.numero,8,'0')) as numero"), 
                    'c.id', 'c.total', DB::Raw("FORMAT(c.total,2) as total2"), DB::Raw('false as mostrarOpc'))
                    ->get();

        return ['detalles' => $detalles];
    }

    public function getCorrelativo (Request $request) {
		$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
			->where('tipoDocumento','=','OD')
			->select(DB::Raw("CONCAT('OD', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"))
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

		$cotizacion = $cotizacion->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"), DB::Raw("DATE_FORMAT(fecha,'%d/%m/%Y') as fecha"),DB::Raw("FORMAT(total,2) as total"),'id', 'total')
		->get();
		return ['cotizaciones' => $cotizacion];
	}
	
	public function guardarOrden(Request $request) {
		// dd($request);
		$errors = $this->validar($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{

            	$orden = new OrdenTrabajo;
				$orden->fecha = $request->get('fecha'); 
				$orden->placa = $request->get('placa');
				$orden->idCliente = $request->get('idCliente');
                $orden->idCita   = $request->get('idCita');
                $c = Cita::find($orden->idCita);
                $c->delete();

                $orden->total = $request->get('totalDoc');
                $orden->idTienda = $this->tiendaId;
				$orden->idAlmacenSalida = $this->almacenId;
				
				$serie = Serie::where('idLocal','=',$orden->idTienda)->where('tipoLocal','=','T')
						->where('tipoDocumento','=','OD')
						->first();
				$orden->serie = $serie->serie;
				$orden->numero = $serie->numero + 1;
				$orden->idPersonal = Auth::user()->usuarioId;
				$orden->semanaActual = date('W',strtotime($orden->fecha));
            
                # ADJUNTAMOS CHECKLIST DE MANTENIMIENTO
                // $checkImg = $request->file('imagen');
                // $extension = $checkImg->getClientOriginalExtension();
                // $chkNombre = $orden->placa.'_'.$orden->numero. '.' . $extension;
                // $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes/'.$chkNombre;

                // ->fit(500, 500)
                // Image::make($checkImg)->save($path);
                // $orden->urlCheckList = $chkNombre;
           
                $orden->save();
                $id = $orden->id;
                
                #LOGIN DE USUARIO
                $cliente = DB::table('persona')
                           ->where('id','=',$orden->idCliente)
                           ->first();
                if (!is_null($cliente)) {
                    $valid_user = DB::table('login')
                                  ->where('usuario','=',$cliente->documento)
                                  ->first();
                    if (!is_null($valid_user)) {
                        DB::table('login')
                        ->where('id','=',$valid_user->id)
                        ->update(['deleted_at' => NULL]);
                        
                        $u = User::find($valid_user->id);
                    } else {
                        $u = new User;
                    }   
                    // dd($valid_user, $u);
                    $u->usuario =  $cliente->documento;
                    $u->correoElectronico = $cliente->correoElectronico; 
                    $u->password = bcrypt($cliente->documento);
                    $u->clienteId = $cliente->id;
                    $u->categoriaPersonalId = 8;
                    if (!is_null($valid_user)) {
                        $u->update();
                    } else {
                        $u->save();
                    }        
                }

                
				$serie->numero = $orden->numero;
				$serie->update(); 
                // dd($rptas);
                $rptas = $request->get('listItemCheckList'); // Session::get('rptas');
                $arr = json_decode($rptas);
                if (!is_null($arr)) {
                    foreach ($arr as $av) {
                        $r = new RespuestaCheckInventario;
                        $r->idOrdenTrabajo = $id;
                        $r->idCheckInventario = $av->id;
                        $r->situacion = $av->valor;
                        $r->save();
                    }
                } else {
                   $errors[]='Llene Check List de Inventario por favor.';
                   $band = false; 
                }
                $usuId = Auth::user()->usuarioId;
                #ASIGNAR IMAGENES Y FIRMA DE CHECK LIST
                ImagenTemporal::where('idPersonal','=',$usuId)
                ->whereNull('idOrdenTrabajo')
                ->update(['idOrdenTrabajo' => $id]);
              
                Session::forget('rptas');

				$detalles  = explode(',',$request->get('listDetalles'));
                $i = 1;
                $j=1;
				if (count($detalles) > 0 && $request->get('listDetalles') != '') {
					foreach ($detalles as $det) {
						$detalle = new DetalleOrdenTrabajo;
						$detalle->item = $i;
                        $detalle->idCotizacion = $request->get('idcotizacion'.$det);
                        $detalle->idOrdenTrabajo = $id;
                        $detalle->save();
                      
                        $cot = Cotizacion::find($detalle->idCotizacion);
                        $cot->situacion ='U';
                        $cot->update(); 
                        
                        #DETALLES PARA SERVICIOS
                        $dets = DetalleCotizacion::leftjoin('servicio as serv','serv.id','=','detallecotizacion.idServicio')
                            ->where('detallecotizacion.idCotizacion','=',$detalle->idCotizacion)
                            ->where('detallecotizacion.tipoDetalle','=','S')
                            ->whereNotNull('detallecotizacion.idServicio')
                            ->select('detallecotizacion.cantidad','detallecotizacion.descripcion','detallecotizacion.idServicio','detallecotizacion.idCotizacion','serv.tiempoEjecucion','serv.unidad')
                            ->get();

                        foreach ($dets as $d) {
                            $detco = new DetalleCotizacionOrden;
                            $detco->item = $j;
                            $detco->idCotizacion = $detalle->idCotizacion;
                            $detco->idDetalleOrdenTrabajo = $detalle->id;
                            $detco->idServicio = $d->idServicio;
                            $detco->descripcion = $d->descripcion;
                            $detco->idPersonalRegistra = Auth::user()->usuarioId;
                            
                            if ($d->unidad == 'hr') {
                                $tiempo = $d->tiempoEjecucion * 60;
                            } else {
                                $tiempo = $d->tiempoEjecucion;
                            }
                            $detco->tiempoEstimado = $tiempo;
                            $detco->save();
                            
                            $j++;
                        }
                        
                        #DETALLES PARA PRODUCTOS
                        $detprods = DetalleCotizacion::whereNotNull('idProducto')
                                    ->where('idCotizacion','=',$detalle->idCotizacion)
                                    ->select('idProducto','cantidad','descripcion', 'idLote')
                                    ->get();
                        foreach ($detprods as $dp) {
                            # QUITAR PARA LOS DETALLES DE PRECIOS
                            $s = StockProductoDetalle::where('idProducto',$dp->idProducto)
                                ->where('idLote',$dp->idLote)
                                ->where('idTienda',$this->tiendaId)
                                ->where('idAlmacenSalida',$this->almacenId)
                                ->first();

                            // $cantidad = $dp->cantidad;
                            $acumDism = $dp->cantidad;
                            if (!is_null($s)) {
                                if ($acumDism <= $s->stock) {
                                    $s->stock = $s->stock - $acumDism;
                                    $s->update();

                                    $spds = new StockProductoDetalleSalida;
                                    $spds->idStockProductoDetalle = $s->id;
                                    $spds->idProducto = $s->idProducto;
                                    $spds->idAlmacen = $s->idAlmacenSalida;
                                    $spds->idOrdenTrabajo = $id;
                                    $spds->stock = $acumDism;
                                    $spds->save();
                                    
                                    #QUITAR AL STOCK GENERAL
                                    $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                        ->where('idAlmacen','=',$this->almacenId)
                                        ->first();
                                    if (!is_null($stg)) {
                                        if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                            // bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3)
                                            $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                                $stg->update();		
                                            } else {
                                                $band = false;
                                                $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                                break;
                                            }
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                            break;
                                        }
                                    } else {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                        break;
                                    }
        
                                    // $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                    // $stg->update();
                                }  else {
                                    $s = StockProductoDetalle::where('idProducto','=',$dp->idProducto)
                                        ->where('idTienda','=',$this->tiendaId)
                                        ->where('idAlmacenSalida','=',$this->almacenId)
                                        ->where('stock','>',0)
                                        ->orderBy('created_at','ASC')
                                        ->select('stock','id')
                                        ->get();

                                    // $cantidad = $dp->cantidad;
                                    $acumDism = $dp->cantidad;
                                    $aux = 0;
                                    foreach ($s as $sd) {
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
                                            $spds->idOrdenTrabajo = $id;
                                            $spds->stock = $aux;
                                            $spds->save();
                                        }
                                    }

                                    if ($acumDism > 0) {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                        break;
                                    }
                                    #QUITAR AL STOCK GENERAL
                                    $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                            ->where('idAlmacen','=',$this->almacenId)
                                            ->first();

                                    if (!is_null($stg)) {
                                        if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                            $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                                $stg->update();		
                                            } else {
                                                $band = false;
                                                $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";    
                                                break;
                                            }
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                            break;
                                        }
                                    } else {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                        break;
                                    }
                                    
                                    // $band = false;
                                    // $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                    // break;
                                }
                            } else {
                                $s = StockProductoDetalle::where('idProducto','=',$dp->idProducto)
                                    ->where('idTienda','=',$this->tiendaId)
                                    ->where('idAlmacenSalida','=',$this->almacenId)
                                    ->where('stock','>',0)
                                    ->orderBy('created_at','ASC')
                                    ->select('stock','id')
                                    ->get();

                                // $cantidad = $dp->cantidad;
                                $acumDism = $dp->cantidad;
                                $aux = 0;
                                foreach ($s as $sd) {
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
                                        $spds->idOrdenTrabajo = $id;
                                        $spds->stock = $aux;
                                        $spds->save();
                                    }
                                }

                                if ($acumDism > 0) {
                                    $band = false;
                                    $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                    break;
                                }
                                #QUITAR AL STOCK GENERAL
                                $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                        ->where('idAlmacen','=',$this->almacenId)
                                        ->first();

                                if (!is_null($stg)) {
                                    if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                        $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                        if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                            $stg->update();		
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";    
                                            break;
                                        }
                                    } else {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                        break;
                                    }
                                } else {
                                    $band = false;
                                    $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                    break;
                                }

                                // $band = false;
                                // $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                // break;
                            }
                            
                            /*$s = StockProductoDetalle::where('idProducto','=',$dp->idProducto)
                                ->where('idTienda','=',$this->tiendaId)
                                ->where('idAlmacenSalida','=',$this->almacenId)
                                ->orderBy('created_at','ASC')
                                ->select('stock','id')
                                ->get();

                            // $cantidad = $dp->cantidad;
                            $acumDism = $dp->cantidad;
                            $aux = 0;
                            foreach ($s as $sd) {
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
                                    $spds->idCotizacion = $cot->id;
                                    $spds->stock = $aux;
                                    $spds->save();
                                }
                            }
                            
                            #QUITAR AL STOCK GENERAL
                            $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                ->where('idAlmacen','=',$this->almacenId)
                                ->first();

                            $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                            $stg->update();*/
			
                        }
						$i++;
                        if (!$band) {
                            break;
                        }
					}
				}
                
                if($band) {
                    $errors[] = 'Orden de Trabajo Registrada Correctamente';
                }
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage(); //'Llene el CheckList de Inventario Por Favor'; //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
            if($band) {
                DB::commit();
            } else {
            	DB::rollback();
			}
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
	}
	
	
    public function getMotivosLibre (Request $request) {
       $motivos = MotivoTiempoLibre::whereNotIn('id',[7,8])->select('id','nombre')->get();
    
        return ['motivos' => $motivos];
    }
	


    public function agregarCotizacion (Request $request) {
        // dd($request);
		$errors = $this->validarAgregarCot($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
                $id = $request->get('id');
                $ordenTrabajo = OrdenTrabajo::find($id);
                $detallesAnt = DetalleOrdenTrabajo::where('idOrdenTrabajo','=',$id)
                               ->select('idCotizacion')->get();
                $arrDetAnt = [];
                foreach ($detallesAnt as $dA) {
                    $arrDetAnt[] =  $dA->idCotizacion;
                }

                $detalles  = explode(',',$request->get('listDetalles'));
                $i = 1;
                $j=1;
				if (count($detalles) > 0 && $request->get('listDetalles') != '') {
					foreach ($detalles as $det) {
                        if (!in_array($det,$arrDetAnt)) {
                            $detalle = new DetalleOrdenTrabajo;
                            $detalle->item = $i;
                            $detalle->idCotizacion = $request->get('idcotizacion'.$det);
                            $detalle->idOrdenTrabajo = $id;
                            $detalle->save();
                        
                            $cot = Cotizacion::find($detalle->idCotizacion);
                            $cot->situacion ='U';
                            $cot->update();
                            
                            #DETALLES PARA SERVICIOS
                            $dets = DetalleCotizacion::leftjoin('servicio as serv','serv.id','=','detallecotizacion.idServicio')
                                ->where('detallecotizacion.idCotizacion','=',$detalle->idCotizacion)
                                ->where('detallecotizacion.tipoDetalle','=','S')
                                ->whereNotNull('detallecotizacion.idServicio')
                                ->select('detallecotizacion.cantidad','detallecotizacion.descripcion','detallecotizacion.idServicio','detallecotizacion.idCotizacion','serv.tiempoEjecucion','serv.unidad')
                                ->get();

                            foreach ($dets as $d) {
                                $detco = new DetalleCotizacionOrden;
                                $detco->item = $j;
                                $detco->idCotizacion = $detalle->idCotizacion;
                                $detco->idDetalleOrdenTrabajo = $detalle->id;
                                $detco->idServicio = $d->idServicio;
                                $detco->descripcion = $d->descripcion;
                                $detco->idPersonalRegistra = Auth::user()->usuarioId;
                                
                                if ($d->unidad == 'hr') {
                                    $tiempo = $d->tiempoEjecucion * 60;
                                } else {
                                    $tiempo = $d->tiempoEjecucion;
                                }
                                $detco->tiempoEstimado = $tiempo;
                                $detco->save();
                                
                                $j++;
                            }
                            
                            #DETALLES PARA PRODUCTOS
                            $detprods = DetalleCotizacion::whereNotNull('idProducto')
                                        ->where('idCotizacion','=',$detalle->idCotizacion)
                                        ->select('idProducto','cantidad','idLote','descripcion')
                                        ->get();

                            foreach ($detprods as $dp) {
                                # QUITAR PARA LOS DETALLES DE PRECIOS
                                $s = StockProductoDetalle::where('idProducto',$dp->idProducto)
                                    ->where('idLote',$dp->idLote)
                                    ->where('idTienda',$this->tiendaId)
                                    ->where('idAlmacenSalida',$this->almacenId)
                                    ->first();

                                // $cantidad = $dp->cantidad;
                                $acumDism = $dp->cantidad;
                                if (!is_null($s)) {
                                    if ($acumDism <= $s->stock) {
                                        $s->stock = $s->stock - $acumDism;
                                        $s->update();

                                        $spds = new StockProductoDetalleSalida;
                                        $spds->idStockProductoDetalle = $s->id;
                                        $spds->idProducto = $s->idProducto;
                                        $spds->idAlmacen = $s->idAlmacenSalida;
                                        $spds->idOrdenTrabajo = $id;
                                        $spds->stock = $acumDism;
                                        $spds->save();

                                        #QUITAR AL STOCK GENERAL
                                        $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                                ->where('idAlmacen','=',$this->almacenId)
                                                ->first();
                                         
                                        if (!is_null($stg)) {
                                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                                $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                                if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                                    $stg->update();		
                                                } else {
                                                    $band = false;
                                                    $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                                    break;
                                                }
                                            } else {
                                                $band = false;
                                                $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                                break;
                                            }
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                            break;
                                        }
                                    
                                        // $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                        // $stg->update();
                                    }  else {
                                        $s = StockProductoDetalle::where('idProducto','=',$dp->idProducto)
                                        ->where('idTienda','=',$this->tiendaId)
                                        ->where('idAlmacenSalida','=',$this->almacenId)
                                        ->where('stock','>',0)
                                        ->orderBy('created_at','ASC')
                                        ->select('stock','id')
                                        ->get();

                                        // $cantidad = $dp->cantidad;
                                        $acumDism = $dp->cantidad;
                                        $aux = 0;
                                        foreach ($s as $sd) {
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
                                                $spds->idOrdenTrabajo = $id;
                                                $spds->stock = $aux;
                                                $spds->save();
                                            }
                                        }

                                        if ($acumDism > 0) {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                            break;
                                        }
                                        #QUITAR AL STOCK GENERAL
                                        $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                                ->where('idAlmacen','=',$this->almacenId)
                                                ->first();

                                        if (!is_null($stg)) {
                                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                                $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                                if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                                    $stg->update();		
                                                } else {
                                                    $band = false;
                                                    $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";    
                                                    break;
                                                }
                                            } else {
                                                $band = false;
                                                $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                                break;
                                            }
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                            break;
                                        }
                                        // $band = false;
                                        // $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                        // break;
                                    }
                                } else {
                                    $s = StockProductoDetalle::where('idProducto','=',$dp->idProducto)
                                        ->where('idTienda','=',$this->tiendaId)
                                        ->where('idAlmacenSalida','=',$this->almacenId)
                                        ->where('stock','>',0)
                                        ->orderBy('created_at','ASC')
                                        ->select('stock','id')
                                        ->get();

                                    // $cantidad = $dp->cantidad;
                                    $acumDism = $dp->cantidad;
                                    $aux = 0;
                                    foreach ($s as $sd) {
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
                                            $spds->idOrdenTrabajo = $id;
                                            $spds->stock = $aux;
                                            $spds->save();
                                        }
                                    }

                                    if ($acumDism > 0) {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                        break;
                                    }
                                    #QUITAR AL STOCK GENERAL
                                    $stg = StockProducto::where('idProducto','=',$dp->idProducto)
                                            ->where('idAlmacen','=',$this->almacenId)
                                            ->first();

                                    if (!is_null($stg)) {
                                        if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                                            $stg->totalVentas = $stg->totalVentas + $dp->cantidad;
                                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                                $stg->update();		
                                            } else {
                                                $band = false;
                                                $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";    
                                                break;
                                            }
                                        } else {
                                            $band = false;
                                            $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                            break;
                                        }
                                    } else {
                                        $band = false;
                                        $errors[] = "Producto $dp->descripcion no cuenta con stock, faltan: $acumDism productos";
                                        break;
                                    }
                                    // $band = false;
                                    // $errors[] = "Producto $dp->descripcion no cuenta con stock: $acumDism";
                                    // break;
                                }
                            }
                            $i++;
                            $ordenTrabajo->total = $ordenTrabajo->total + $cot->total;
                            $ordenTrabajo->update();
                            if (!$band) {
                                break;
                            }
                        }
                    }
                }

				if ($band) {
                    $errors[] = 'Orden de Trabajo Actualizada Correctamente';
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
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
    }

    public function agregarAsignacion (Request $request) {
        // dd($request);
		$errors = $this->validarDetallePersonal($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
            if ($request->get('trabajadorId') != '0') {
                $band = true;
                $errors = [];
                DB::beginTransaction();
                try{
                    $id = $request->get('id');
                    $asignado = $request->get('trabajadorId');
                    $usuarioId =  Auth::user()->usuarioId;

                    DB::table('ordentrabajo')
                    // ->where('situacion','!=','F')
                    ->where('id', $id)
                    ->update(['idAsignado' => $asignado, 'idPersonal' => $usuarioId, 'updated_at' => date('Y-m-d H:i:s')]);

                    $lista = explode(',',$request->get('listDetalles'));

                    foreach ($lista as $item) {
                        // Actualizamos el personal que registra la operacion
                        DB::table('detallecotizacionorden')
                        ->where('id', $item)
                        ->update(['idPersonalAsigna' => $usuarioId, 'updated_at' => date('Y-m-d H:i:s')]);

                        // Eliminamos personal asignado anteriormente a tarea
                        DB::table('detallecotizacionordenpersonal')
                        ->where('idDetalleCotizacionOrden', $item)
                        ->update(['deleted_at' => date('Y-m-d H:i:s')]);

                        // Agregamos el nuevo personal
                        $deop = new DetalleOrdenPersonal;
                        $deop->idPersonal = $asignado;
                        $deop->idDetalleCotizacionOrden = $item;
                        $deop->save();
                    }
                    // foreach ($lista as $item) {
                    //     $dd = DetalleCotizacionOrden::find($item);
                    //     // $dd->fechaInicio = $request->get('fecha'.$item).' '.$request->get('hora'.$item).':00';
                    //     $dd->idPersonalAsigna = Auth::user()->usuarioId;
                    //     $dd->update();
                        
                    //     $usu = $request->get('listPersonal'.$item);
                    //     $listaUsu = explode(',',$usu);
                        
                    //     $agregados = [];
                    //     foreach ($listaUsu as $item2) {
                    //         $idp = $request->get('usuarioId'.$item.'_'.$item2);
                    //         $arr = DetalleOrdenPersonal::leftjoin('detallecotizacionorden as detcot','detcot.id','=','detallecotizacionordenpersonal.idDetalleCotizacionOrden')
                    //         ->where('detallecotizacionordenpersonal.idDetalleCotizacionOrden','=',$item) 
                    //         // ->where('detcot.situacion','=','P')
                    //         ->select('detallecotizacionordenpersonal.idPersonal')
                    //         ->get();
    
                    //         $dets = [];
                    //         foreach ($arr as $a) {
                    //             $dets[] = $a->idPersonal;
                    //         }

                    //         if (!in_array($idp,$dets) && !is_null($idp)) {
                    //             $deop = new DetalleOrdenPersonal;
                    //             $deop->idPersonal = $idp;
                    //             $deop->idDetalleCotizacionOrden = $item;
                    //             $deop->save();
                    //             $agregados[] = $idp;
                    //         } elseif(in_array($idp,$dets)) {
                    //             $agregados[] = $idp;
                    //         }
                    //     }
                    //     #ELIMINAR LOS QUE NO APARECEN
                    //     DetalleOrdenPersonal::leftjoin('detallecotizacionorden as detcot','detcot.id','=','detallecotizacionordenpersonal.idDetalleCotizacionOrden')
                    //     ->where('detallecotizacionordenpersonal.idDetalleCotizacionOrden','=',$item) 
                    //     ->whereNotIn('detallecotizacionordenpersonal.idPersonal',$agregados)
                    //     ->update(['detallecotizacionordenpersonal.deleted_at' => date('Y-m-d H:i:s') ]);
                    // }
                
                    $errors[] = 'Personal Asignado a Orden de Trabajo Correctamente';
            
                }catch(\Exception $ex){
                    $errors[] = $ex->getMessage();
                    $band = false;
                    DB::rollback();
                }
            
                DB::commit();   
            } else {
                $errors[] = "Indique Trabajador Válido";
                $band = false;
            }

            return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
         
		}
    }

    public function actualizarEstadoOrden (Request $request) {
        $errors = [];
        $band = true;
        DB::beginTransaction();
        try{  
            $id = $request->get('id');
            $situacion = $request->get('estado');

            $orden = OrdenTrabajo::find($id);
            if (!is_null($orden)) {
                if ($orden->situacion == 'V' && $situacion == 'I' && !is_null($orden->idAsignado)) {
                    $orden->situacion = $situacion;
                    $orden->inicia = date('Y-m-d H:i:s');
                    $orden->update();
                    $errors[] = "Orden de Trabajo Actualizada con Éxito"; 
                } elseif ($orden->situacion == 'I' && $situacion == 'F' && !is_null($orden->idAsignado)) {
                    $orden->situacion = $situacion;
                    $orden->finaliza = date('Y-m-d H:i:s');
                    $orden->update();
                    $errors[] = "Orden de Trabajo Actualizada con Éxito"; 
                } else {
                    $errors[] = "No se puede actualizar Orden de Trabajo, No se ha asignado Personal para la Orden."; 
                    $band = false;           
                }
            } else {
                $band = false;
                $errors[] = "Orden de Trabajo No Encontrada"; 
            }
        }catch(\Exception $ex){
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }
    
        DB::commit();   

        return ['errores' => (object)$errors, 'estado' => $band];

    
    }

    public function getBusquedaPersonal (Request $request) {
        $busqueda = $request->get('busqueda');
        $personas = [];
        if ($busqueda != '') {
            $personas = DB::table('trabajador as trab')
                        ->join('categoriapersonal as cp','cp.id','=','trab.idCategoriaPersonal')
                        // ->join('detallecotizacionordenpersonal as detcot','detcot.idPersonal','=','trab.id')
                        // ->join('detallecotizacionorden as deor','deor.id','=','detcot.idDetalleCotizacionOrden')
                        // ->join('detalleordentrabajo as dot','dot.id','=','deor.idDetalleOrdenTrabajo')
                        ->join('ordentrabajo as ot','ot.idAsignado','=','trab.id')
                        ->whereNull('ot.finaliza')
                        // ->whereNotNull('ot.inicia')
                        ->whereIn('ot.situacion',['I','V'])
                        ->where('cp.nombre','Mecanico')
                        ->where(DB::Raw("CONCAT(trab.apellidos,' ', trab.nombres)"),'LIKE','%'.$busqueda.'%')
                        ->select(
                            DB::Raw("CONCAT(trab.apellidos,' ', trab.nombres) as personal"),
                            DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as servicio"),
                            DB::Raw("(SELECT SUM(ROUND(detc.tiempoEstimado,2)) FROM detalleordentrabajo as dto JOIN detallecotizacionorden as detc ON detc.idDetalleOrdenTrabajo = dto.id 
                            WHERE dto.idOrdenTrabajo = ot.id AND detc.deleted_at IS NULL) as tiempo"),
                            DB::Raw("DATE_FORMAT(ot.inicia,'%d/%m/%Y %h:%i:%s %p') as fecha"),
                            'trab.id as id_personal'
                        )
                        ->get();

            // $personas = Personal::leftJoin('categoriapersonal as cp','cp.id','=','trabajador.idCategoriaPersonal')
            // ->leftJoin('detallecotizacionordenpersonal as detcot','detcot.idPersonal','=','trabajador.id')
            // ->leftJoin('detallecotizacionorden as deor','deor.id','=','detcot.idDetalleCotizacionOrden')
            // ->where('deor.situacion','=','P')
            // ->where(function ($qq) use ($busqueda) {
            //     $qq->where('cp.nombre','LIKE','%'.$busqueda.'%')
            //        ->orWhere(DB::Raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres)"),'LIKE','%'.$busqueda.'%');
            // })
            // ->select(DB::Raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres) as personal"), 
            // 'deor.descripcion as servicio','deor.tiempoEstimado as tiempo', 
            // DB::Raw("DATE_FORMAT(deor.fechaInicio,'%d/%m/%Y') as fecha"),'cp.nombre as tipo','trabajador.id as id_personal')
            // ->get();
        }

        return ['personas' => $personas];
    }
   
    public function cargarMultimedia ($id, Request $request) {
        //dd('Hola...');
        // dd($request->file('imagen'));
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        //dd(ini_get('upload_max_filesize'), ini_get('post_max_size'));
        ini_set('upload_max_filesize', '128M');
        ini_set('post_max_size', '128M');
        # ADJUNTAMOS CHECKLIST DE MANTENIMIENTO
        try {
           
                // dd($checkImg);
            $i = DB::table('imagendetalle')
                    ->where('idOrdenTrabajo','=',$id)
                    ->count();
            //dd($_FILES['imagen']['type']);
            if (strpos($_FILES['imagen']['type'],'video/') === false) {
              
                $checkImg = $request->file('imagen');
                // return $checkImg;
                $extension = $checkImg->getClientOriginalExtension();
                
                $size = $checkImg->getSize();
                $chkNombre = $id.'_multimedia'.($i+1).'.' .$extension;
                // $path = '/home/jolf78cf1j26/backendAuto/storage/app/imagenes/'.$chkNombre; //'http://carpio.dboxperu.com/storage/app\public/imagenes/'.$chkNombre;
                
                // move_uploaded_file(\File::get($checkImg), $path);        
                \Storage::disk('local')->put($chkNombre, \File::get($checkImg));
                
            } else {
                
                $checkImg = $_FILES['imagen']['tmp_name'];
                $checkName = $_FILES['imagen']['name'];
                // dd($checkImg);
            //   $imagen = $request->file('imagen');
                $extension = pathinfo($checkName, PATHINFO_EXTENSION);
                $size = $_FILES['imagen']['size'];
                // dd($checkImg, $extension);
                // return [$checkImg, $extension];
                $chkNombre = $id.'_multimedia'.($i+1).'.' .$extension;
                $path = \Storage::disk('local')->path('').$chkNombre; //'http://carpio.dboxperu.com/storage/app\public/imagenes/'.$chkNombre;
                //\Storage::disk('local')->put($chkNombre, \File::get($checkImg));
                
                move_uploaded_file($checkImg, $path);        
            }

            //   dd($url);
              
            // if ($extension != 'video/') {
            // } else {
            //     return $checkImg.' '.$extension.' '.$size;
            // }
            // // } else {
                // $checkImg = $_FILES['imagen'];
                // $extension = pathinfo($checkImg->getFilename(), PATHINFO_EXTENSION);
                // $size = filesize($checkImg->getFilename());
            // }

            // return [$checkImg, $extension, $size];
            $extensionI = ['png','jpg','jpeg','gif'];
            $a = new ImagenDetalle;
            $a->idOrdenTrabajo = $id;
            $a->tamanio = $size;
            $a->nombre = $chkNombre;
            $a->tipo = in_array($extension,$extensionI)?'I':'V';
            $a->idPersonal = Auth::user()->usuarioId;
            $a->save();
            
            return ['nombre' => $a->nombre, 'url' => $this->urlPattern.$a->nombre,'tamanio' => $a->tamanio, 'tipo' => $a->tipo];

        } catch (\Exception $ex) {
            dd($ex);
            return $ex->getMessage();
        }
    }

    public function guardarTemporal(Request $request) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # ADJUNTAMOS CHECKLIST DE MANTENIMIENTO
        try {
            $id = Auth::user()->usuarioId;
                // dd($checkImg);
            $i = DB::table('imagentemp')
                ->where('idPersonal','=',$id)
                ->count();
        
            if (strpos($_FILES['imagen']['type'],'video/') === false) {
                $checkImg = $request->file('imagen');
                // return $checkImg;
                $extension = $checkImg->getClientOriginalExtension();
                $size = $checkImg->getSize();
                $chkNombre = $id.'_multimedia'.($i+1).'.' .$extension;
               // $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes/'.$chkNombre;

                \Storage::disk('local_temp')->put($chkNombre, \File::get($checkImg));
            } else {
                // dd($_FILES['imagen']);
                $checkImg = $_FILES['imagen']['tmp_name'];
                $checkName = $_FILES['imagen']['name'];
                
                $extension = pathinfo($checkName, PATHINFO_EXTENSION);
                $size = $_FILES['imagen']['size'];
                // dd($checkImg, $extension);
                // return [$checkImg, $extension];
                $chkNombre = $id.'_multimedia'.($i+1).'.' .$extension;
                $path = \Storage::disk('local_temp')->path('').$chkNombre;
                // $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes_temp/'.$chkNombre;
          
                move_uploaded_file($checkImg, $path);        
            }

            // return [$checkImg, $extension, $size];
            $extensionI = ['png','jpg','jpeg','gif'];
            $a = new ImagenTemporal;
            $a->tamanio = $size;
            $a->nombre = $chkNombre;
            $a->tipo = in_array($extension,$extensionI)?'I':'V';
            $a->idPersonal = $id;
            $a->save();
            
            return ['nombre' => $a->nombre, 'url' => '/storage/imagenes_temp/'.$a->nombre,'tamanio' => $a->tamanio, 'tipo' => $a->tipo];

        } catch (\Exception $ex) {

            return $ex->getMessage();
        }
    }

    public function guarAvance (Request $request) {
        $errors = $this->validarAvance($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
                $id = $request->get('id');
                // $cantI = ImagenDetalle::where('idDetalleOrdenTrabajo','=',$id)->count('id');
                $orden = OrdenTrabajo::find($id);
                if (!is_null($orden)) {
                    $orden->observaciones = $request->get('observaciones');
                    $orden->update();
                    $errors[] = 'Proceso Registrado Correctamente';
                } else {
                    $band = false;
                    $errors[] = 'Indique Orden de Referencia';
                }
                // if ($cantI > 0) {
                //     $det = DetalleCotizacionOrden::find($id);
                //     $det->situacion = 'E';
                //     $det->observaciones = $request->get('observaciones');
                //     $det->update();
                //     $errors[] = 'Avance Enviado Correctamente';
                // } else {
                //     $band = false;
                //     $errors[] = 'Indique Lista de Medios';
                // }
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
    }

    public function validarAvance (Request $request) {
        $reglas = [
            'id'=>  'required',
            // 'listMultimedia'=> 'required',
			'observaciones'=> 'nullable',
	    ];

        $mensajes = [
            'id.required'=> 'ID de Trabajo no Existe',
            // 'listMultimedia.required'=> 'Indique Lista de Medios',
            'observaciones.required'=> 'Indique Observaciones',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	
    }

    public function validarEncuesta (Request $request) {
        $reglas = [
            'idOrden'=>  'required',
        	'observaciones_encuesta'=> 'nullable',
            'estado' => 'required',
	    ];

        $mensajes = [
            'idOrden.required'=> 'ID de Trabajo no Existe',
            // 'listMultimedia.required'=> 'Indique Lista de Medios',
            'observaciones_encuesta.required'=> 'Indique Observaciones',
            'estado.required' => 'Indique Estado'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function getDetallesEvaluacion ($id, Request $request) {
        $detalles = DB::table('detalleordentrabajo as dot')
                ->join('detallecotizacionorden as dor', 'dor.idDetalleOrdenTrabajo','=','dot.id')
                ->where('dot.idOrdenTrabajo', $id)
                ->where('dot.situacion','V')
                ->select('dor.descripcion', DB::Raw("ROUND(dor.tiempoEstimado,2) as tiempoEstimado"))
                ->get();

        return ['detalles' => $detalles];
    }

    public function getIncidenciasEvaluacion ($id, Request $request) {
        $orden = DB::table('ordentrabajo')
                ->where('id', $id)
                ->select('situacion')
                ->first();

        $detalles = DB::table('tiempodetalle as td')
                    ->where('td.idOrdenTrabajo', $id)
                    ->whereNull('td.deleted_at')
                    ->select('td.observaciones as descripcion', 'td.idMotivo', 
                    DB::Raw("ROUND(td.tiempo,2) as tiempo"),
                    'td.unidadTiempo', 'td.id', 'td.estado',
                    // DB::Raw("CONCAT(ROUND(td.tiempo,2),' ', td.unidadTiempo) as tiempo"),
                    DB::Raw("'1' as retornaBD"))
                    ->get();
        $motivos = DB::table('motivotiempolibre as mtl')
                    ->whereNull('mtl.deleted_at')
                    ->whereNotIn('mtl.id', [6,7,8])
                    ->select('mtl.nombre', 'mtl.id')
                    ->get();
                    
        $totales =  DB::table('tiempodetalle as td')
                    ->where('td.idOrdenTrabajo', $id)
                    ->whereNull('td.deleted_at')
                    ->where('td.estado', 'T')
                    ->select(
                        DB::Raw("SUM(TIMESTAMPDIFF(DAY, td.inicio, td.fin)) AS dias"),
                        DB::Raw("SUM(HOUR(TIMEDIFF(td.inicio, td.fin))) AS horas"),
                        DB::Raw("SUM(MINUTE(TIMEDIFF(td.inicio, td.fin))) AS minutos"),
                        DB::Raw("SUM(SECOND(TIMEDIFF(td.inicio, td.fin))) AS segundos")
                    )
                    // ->select(DB::Raw("ROUND(SUM(TIMESTAMPDIFF(SECOND, td.inicio, td.fin)/60),2) as tiempo"), 
                    //     DB::Raw("'minuto(s)' as unidadTiempo"))
                    // ->groupBy('td.unidadTiempo')
                    ->get();
        
        $situacion = '';
        if (!is_null($orden)) {
            $situacion = $orden->situacion;
        }
        
        return ['detalles' => $detalles,'motivos' => $motivos, 'totales' => $totales, 'situacion' => $situacion];

    }

    public function guardarIncidencia (Request $request) {
        // dd($request);
        $band = true;
        $errors = [];
        DB::beginTransaction();
        try{
            $id = $request->get('id');
            $orden = OrdenTrabajo::find($id);

           
                $listDetalles = $request->get('listDetalles');
                if ($id != '') {
                    if (!is_null($orden)) {
                        if ($orden->situacion == 'I') {
                            if (!is_null($listDetalles)) {
                                $arrDetalles = explode(',', $listDetalles);
                                $cont = 1; 
                                foreach($arrDetalles as $item) {
                                    $motivoId = $request->get('motivoId_'.$item);
                                    $descripcion = $request->get('descripcion_'.$item);
                                    $estado = $request->get('estado_'.$item);
                                    $retornaBD = $request->get('retornaBD_'.$item);
                                    $refId = $request->get('refId_'.$item);

                                    if (is_null($motivoId) || (int) $motivoId <= 0) {
                                        $errors[] = "Motivo no admitido, en el Item $cont";
                                        $band = false; 
                                    }

                                    if (is_null($descripcion)) {
                                        $errors[] = "Descripción no admitido, en el Item $cont";
                                        $band = false;    
                                    }

                                    if (is_null($retornaBD)) {
                                        $errors[] = "No se logró identificar procedencia, en el Item $cont";
                                        $band = false;    
                                    }

                                    if (is_null($estado)) {
                                        $errors[] = "Estado no admitido, en el Item $cont";
                                        $band = false;    
                                    }

                                    if ($band && $retornaBD == '0') {
                                        $t = new TiempoDetalle;
                                        $t->idOrdenTrabajo = $id;
                                        $t->idMotivo = $motivoId;
                                        $t->observaciones = $descripcion;
                                        $t->inicio = date('Y-m-d H:i:s');
                                        $t->estado = 'I';
                                        // $t->tiempo = $tiempo;
                                        // $t->unidadTiempo = $tipoTiempo;
                                        $t->idPersonal = Auth::user()->usuarioId;
                                        $t->save();
                                    } else  if ($band && $retornaBD == '1') {
                                        if (!is_null($refId)) {
                                            $t = TiempoDetalle::find($refId);
                                            if (!is_null($t) && $t->estado == 'I' && $estado != 'I') {
                                                $t->fin = date('Y-m-d H:i:s');
                                                $t->estado = $estado;
                                                $t->save();
                                            }
                                            
                                        } else {
                                            $errors[] = "No se encontró ID de referencia, en el Item $cont";
                                            $band = false;
                                        }
                                    }
                                    $cont++;
                                }
                            } else {
                                $errors[] = "No se encontraron detalles, registre por favor.";
                                $band = false;
                            }
                        } else {
                            $errors[] = "Orden de Referencia no se encuentra Iniciada.";
                            $band = false;                               
                        }
                    } else {
                        $errors[] = "ID de Orden no referenciado, registre por favor.";
                        $band = false;
                    }
                } else {
                    $errors[] = "ID de Orden no referenciado, registre por favor.";
                    $band = false;
                }
           
        }catch(\Exception $ex){
            $errors[] = $ex->getMessage();
            $band = false;
            DB::rollback();
        }
        
        if ($band) {
            $errors[] = "Incidencia(s) Registradas correctamente.";
            DB::commit();
        } else {
            DB::rollback();
        }
    
        return ['errores' => (object)$errors, 'estado' => $band];
    }

    public function revisarAvance (Request $request) {
        $errors = $this->validarRevision($request);
        $idPersonal = Auth::user()->usuarioId;
     
        $id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
                $id = $request->get('id');
                $det = DetalleCotizacionOrden::find($id);
                $se = $request->get('select_estado');
                $idTiempo = $request->get('idTiempo');

                if ($idTiempo != 0) {
                    $t = TiempoDetalle::find($idTiempo);
                    if (!is_null($t)) {
                        $t->fin = $request->get('tiempoFinR');
                        // $t->observaciones = $request->get('observaciones');
                        $t->update();
                        
                        if ($se != 2 && $t->idMotivo != $request->get('select_motivo')) {
                            $a = new TiempoDetalle;
                            $a->idDetalleOrdenTrabajo = $det->id;
                            $a->idMotivo = $request->get('select_motivo');
                            $a->inicio = $request->get('tiempoInicioR');
                            $a->observaciones = $request->get('observaciones');
                            $a->idPersonal = $idPersonal;
                            $a->save();
                        }
                    } 
                } else {
                    if ($se != 2) {
                        $a = new TiempoDetalle;
                        $a->idDetalleOrdenTrabajo = $det->id;
                        $a->idMotivo = $request->get('select_motivo');
                        $a->inicio = $request->get('tiempoInicioR');
                        $a->observaciones = $request->get('observaciones');
                        $a->idPersonal = $idPersonal;
                        $a->save();
                    }
                }
            
                if ($se == 2) {
                    $a = new TiempoDetalle;
                    $a->idDetalleOrdenTrabajo = $det->id;
                    $a->idMotivo = 8;
                    #ENCONTRAR ACT. ANTERIOR
                    // $act = DB::table('tiempodetalle')->where('idDetalleOrdenTrabajo','=',$id)
                    //         ->select('inicio')
                    //         ->orderBy('id','DESC')
                    //         ->first();

                    // if (!is_null($act)) {
                    $a->inicio = $request->get('tiempoFinR');
                    // }
                    $a->fin = $request->get('tiempoFinR');
                    $a->idPersonal = $idPersonal;
                    $a->save();
                
                
                    $det->situacion = 'R';
                    $det->fechaFin = $a->fin;
                    $det->enProceso = $se;

                    $tmps = DB::table('tiempodetalle')
                            ->where('idDetalleOrdenTrabajo','=',$id)
                            ->whereNotIn('idMotivo',[7,8])
                            ->select(DB::Raw("SUM(TIMESTAMPDIFF(SECOND,inicio,fin)/60) as tiempo"))
                            ->first();
    
                    $det->tiempoLibre = round($tmps->tiempo,2);
                    $det->update();
                }
                // $det->observacionesR = $request->get('observaciones');
                // $det->tiempoLibre = $request->get('tiempomuerto');
                // $det->idMotivo = $request->get('select_motivo');
                // $det->update();


                $idDet  = $det->idDetalleOrdenTrabajo;
                /*$arr = DetalleCotizacionOrden::leftJoin('detalleordentrabajo as detor','detor.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
                ->whereNull('detallecotizacionorden.deleted_at')
                ->select(DB::Raw("COUNT(detallecotizacionorden.situacion) as cantidad"), 'detallecotizacionorden.situacion')
                ->groupBy('detallecotizacionorden.situacion')
                ->get();
                */
                
                $arr = DB::table('detallecotizacionorden')
                        ->where('idDetalleOrdenTrabajo','=',$idDet)
                        ->whereNull('deleted_at')
                        ->select(DB::Raw("COUNT(situacion) as cantidad"), 'situacion')
                        ->groupBy('situacion')
                        ->get();

                $cantidad = 0;
                $cantTerminado = 0;
                foreach ($arr as $a) {
                    if ($a->situacion == 'R') {
                        $cantTerminado += $a->cantidad;
                    }
                    $cantidad += $a->cantidad;
                }

                if ($cantidad == $cantTerminado) {
                    $deo= DetalleOrdenTrabajo::find($idDet);
                    $o = OrdenTrabajo::find($deo->idOrdenTrabajo);
                    $o->situacion = 'F';
                    $o->update();
                }
                // if (count($arr) == $arr)
				$errors[] = 'Avance Revisado Correctamente';
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
    }

    public function validarRevision (Request $request) {
        $reglas = [
            'id' => 'required',
            'idTiempo' => 'required',
            // 'enProceso' => 'required',
            'tiempoInicioR' => ($request->get('select_estado')==1?'required':'nullable'),
            'tiempoFinR' => 'required',
            'id'=>  'required',
            'select_motivo' => ($request->get('select_estado')==1?'required':'nullable'),
           // 'tiempomuerto'=> 'required|numeric|min:0',
            'observaciones'=> 'nullable',
            'select_estado' => 'required',
	    ];

        $mensajes = [
            'id.required'=> 'ID de Trabajo no Existe',
            'idTiempo.required'=> 'ID de Tiempo no Existe',
            'select_motivo.required' => 'Indique Motivo',
            'tiempoInicioR.required'=> 'Indique Fecha/Hora de Inicio de Actividad',
            'tiempoFinR.required'=> 'Indique Fecha/Hora de Fin de Actividad',
            // 'tiempomuerto.min'=> 'Tiempo Libre debe ser como mínimo 0',
            'observaciones.required'=> 'Indique Observaciones',
            'select_estado.required' => 'Indique Situación',
            'enTiempo.required' => 'Indique Fecha y Hora'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	
    }


    public function eliminarMultimedia ($id, Request $request) {
        $checkImg = $request->get('imagen');
        $a = ImagenDetalle::where('idOrdenTrabajo','=',$id)
             ->where('nombre','=',$checkImg)
             ->first();
        if (!is_null($a)) {
            $exist = \Storage::disk('local')->exists($a->nombre);
        
            if ($exist) {
                \Storage::disk('local')->delete($a->nombre);
            }
            $a->delete();
        }
        
        return 'Ok';
             // $extension = $checkImg->getClientOriginalExtension();
        // dd($checkImg);
    }

    public function eliminarTemporal (Request $request) {
        $checkImg = $request->get('imagen');
        $id = Auth::user()->usuarioId;
        $a = ImagenTemporal::where('idPersonal','=',$id)
             ->where('nombre','=',$checkImg)
             ->first();
        if (!is_null($a)) {
            $exist = \Storage::disk('local_temp')->exists($a->nombre);
            if ($exist) {
                \Storage::disk('local_temp')->delete($a->nombre);
            }
            $a->delete();
        }
        
        return 'Ok';
             // $extension = $checkImg->getClientOriginalExtension();
        // dd($checkImg);
    }

    public function guardarFirma (Request $request) {
        $estado = false;
        $id = Auth::user()->usuarioId;
        $rptas = explode(',', $request->get('rptas'));
        Session::put('rptas', $rptas);
        
    	DB::beginTransaction();
		try{
            // Eliminar Firmas
            $imagenes = ImagenTemporal::whereNull('idOrdenTrabajo')
                        ->where('idPersonal','=',$id)
                        ->where('isFirma','=','1')
                        // ->select('nombre','id')
                        ->get();
            foreach ($imagenes as $a) {
                // $image_path = 'C:\Users\Erick\Desktop\App\public/storage/imagenes_temp/'.$a->nombre;
                $exist=\Storage::disk('local_temp')->exists($a->nombre);
        
                if ($exist) {
                    \Storage::disk('local_temp')->delete($a->nombre);
                }
                $a->delete();
            }
    
            $i = DB::table('imagentemp')
                ->where('idPersonal','=',$id)
                // ->whereNull('idOrdenTrabajo')
                ->where('isFirma','=','1')
                ->count();
        
            // ImagenTemporal::whereNull('idOrdenTrabajo')
            // ->where('idPersonal','=',$id)
            // ->where('isFirma','=','1')
            // ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            
            $firma  = $request->get('firma');
            $constFirma = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAD6CAYAAAAbbXrzAAAHPUlEQVR4Xu3UAQkAAAwCwdm/9HI83BLIOdw5AgQIRAQWySkmAQIEzmB5AgIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQer6YA+zEVsR8AAAAASUVORK5CYII=';
            
            if ($firma != $constFirma) {
                $firma = str_replace('data:image/png;base64,', '', $firma);
                $firma  = str_replace(' ','+',$firma);
                $firmaF = base64_decode($firma);
                // dd($image, $firmaF);
                // $image = imagecreatefromstring($firmaF);
                $chkNombre = $id.'_firma'.($i+1).'.png';
                
                // return $checkImg;
                // $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes_temp/'.$chkNombre;
                
                // $path='/home/jolf78cf1j26/backendAuto/storage/app/public/imagenes_temp/'.$chkNombre;
                // file_put_contents($path, $firmaF);
           
                // $chkNombre = $id.'_multimedia'.($i+1).'.' .$extension;
                // $path = 'C:\Users\Erick\Desktop\App/storage/app\public/imagenes/'.$chkNombre;
                 \Storage::disk('local_temp')->put($chkNombre, $firmaF);
            
                
                // if (!is_null($chkNombre)) {
                    $a = new ImagenTemporal;
                    $a->tamanio = 10;
                    $a->nombre = $chkNombre;
                    $a->tipo = 'I';
                    $a->isFirma = '1';
                    $a->idPersonal = $id;
                    $a->observacion = $request->get('observacion');
                    $a->save();
            
                    $errors = 'Ok';
                    $estado = true;
                // } 
            } else {
        		$errors = 'Recuerde Confirmar Firma Por Favor'; //$ex->getMessage();
	        	DB::rollback();
		    }
		}catch(\Exception $ex){
			$errors = 'Indique Firma Por Favor'; //$ex->getMessage();
			DB::rollback();
		}
	
		DB::commit();
		
        return ['errors' => $errors, 'estado' => $estado];
    }

    public function getMultimedia ($id, Request $request) {
        $band = true;
        $tiempoInicio = '';
        $tiempoInicioR = '';
        $imagenes = DB::table('imagendetalle as img')
                    // ->join('ordentrabajo as ot','ot.id','=','img.idOrdenTrabajo')
                    ->where('img.idOrdenTrabajo', $id)
                    ->whereNull('img.deleted_at')
                    ->select(DB::Raw("CONCAT('/storage/imagenes/',img.nombre) as url"),'img.tamanio','img.tipo','img.nombre')
                    ->get();

        // $imagenes = ImagenDetalle::leftJoin('detallecotizacionorden as detor','detor.id','=','imagendetalle.idDetalleOrdenTrabajo')->where('imagendetalle.idDetalleOrdenTrabajo','=',$id)
        //             ->select('imagendetalle.nombre','imagendetalle.tamanio', 'imagendetalle.tipo','detor.observaciones')
        //             ->get();
 
        // $imagenes = 
        
        // $arr = [];
        $orden = DB::table('ordentrabajo as ot')
                ->where('ot.id', $id)
                ->select('ot.observaciones')
                ->first();

        $observaciones = null;
        if (!is_null($orden)) {
            $observaciones = $orden->observaciones;
        }

        return ['arreglo' => $imagenes, 'observaciones' => $observaciones];

        // foreach ($imagenes as $img) {
        //     if (is_null($observaciones)) {
        //         $observaciones = $img->observaciones;
        //     }

        //     $arr[] = ['nombre' => $img->nombre, 'url' => $this->urlPattern.$img->nombre,'tamanio' => $img->tamanio, 'tipo' => $img->tipo];
        // }

        // $tiempo = TiempoDetalle::where('idDetalleOrdenTrabajo','=',$id)
        //             ->whereNull('fin')
        //             ->select(DB::Raw("DATE_FORMAT(inicio,'%d/%m/%Y %H:%i:%s') as fechaHora"),'inicio as fechaHoraR','idMotivo',
        //             'observaciones','id')
        //             ->orderBy('id','DESC')
        //             ->first();

        // $motivoAct  = TiempoDetalle::join('motivotiempolibre as mtl','mtl.id','=','tiempodetalle.idMotivo')
        //               ->select('mtl.nombre as motivo')
        //               ->where('tiempodetalle.idDetalleOrdenTrabajo','=', $id)  
        //               ->orderBy('tiempodetalle.id','DESC')
        //               ->first();

        // $t = DB::table('detallecotizacionorden')
        //     ->where('id','=',$id)
        //     ->whereNull('fechaFin')
        //     ->select(DB::Raw("DATE_FORMAT(fechaInicio,'%d/%m/%Y %H:%i:%s') as fechaHora"),'fechaInicio')
        //     ->first();

        // if (!is_null($t)) {
        //     $tiempoInicio = $t->fechaHora;
        //     $tiempoInicioR = $t->fechaInicio;
        // }

        // if (is_null($tiempo)) {
        //     $tiempo = null;
        //     if (is_null($t)) {
        //         $t = DB::table('detallecotizacionorden')
        //                 ->where('id','=',$id)
        //                 ->whereNotNull('fechaFin')
        //                 ->select(DB::Raw("DATE_FORMAT(fechaFin,'%d/%m/%Y %H:%i:%s') as fechaHora"),'fechaFin')
        //                 ->first();
        //         if (!is_null($t)) {
        //             $tiempoInicio = $t->fechaHora;
        //             $tiempoInicioR = $t->fechaFin;
        //             // $band = false;
        //         }
        //     }
        // } else {
        //     $tiempoInicio = $tiempo->fechaHora;
        //     $tiempoInicioR = $tiempo->fechaInicio;
        // }
        
        // return ['arreglo' => $arr, 'tiempo' => $tiempo, 'tiempoInicio' => $tiempoInicio, 
        // 'tiempoInicioR' => $tiempoInicioR,'observaciones' => $observaciones, 'motivo' => (!is_null($motivoAct)?$motivoAct->motivo:''), 'estado' =>$band]; 

    }


    public function getTemporal (Request $request) {
        $id = Auth::user()->usuarioId;

        $imagenes = ImagenTemporal::where('idPersonal','=',$id)
        ->whereNull('idOrdenTrabajo')
        ->select('nombre','tamanio', 'tipo','isFirma')
        ->get();

        $arr = [];
        $firma = null;
        foreach ($imagenes as $img) {
            if ($img->isFirma == '0') {
                $arr[] = ['nombre' => $img->nombre, 'url' => 'http://carpio.ayluby.com/storage/imagenes_temp/'.$img->nombre,'tamanio' => $img->tamanio, 'tipo' => $img->tipo];
            } 

            if ($img->isFirma == '1') {
                 $firma = ['nombre' => $img->nombre, 'url' => 'http://carpio.ayluby.com/storage/imagenes_temp/'.$img->nombre ];
            }
        }
        return ['arreglo' => $arr, 'firma' => (Object)$firma];
        
    }

    public function buscardetalleorden (Request $request) {
        $id = $request->get('id');

        $orden = DB::table('ordentrabajo as ot')
                ->leftjoin('trabajador as p','p.id','=','ot.idAsignado') 
                ->leftjoin('persona as cl','cl.id','=','ot.idCliente') 
                ->where('ot.id', $id)
                ->select(
                    DB::Raw("CONCAT(p.apellidos, ' ', p.nombres) as asignado"), 'p.id', 'ot.placa', 
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos, ' ', cl.nombres) ELSE cl.razonSocial END) as cliente")
                )
                ->first();

        $detalles = DetalleCotizacionOrden::leftJoin('detalleordentrabajo as deo','deo.id','=','detallecotizacionorden.idDetalleOrdenTrabajo')
                    ->leftJoin('cotizacion as c','c.id','detallecotizacionorden.idCotizacion')
                    ->where('deo.idOrdenTrabajo','=',$id)
                    ->select('detallecotizacionorden.id','detallecotizacionorden.descripcion',
                    DB::Raw("FORMAT(detallecotizacionorden.tiempoEstimado,2) as tiempoEstimado"), 
                    DB::Raw("CONCAT(FORMAT(detallecotizacionorden.tiempoEstimado,2),':00') as tiempoEstimado2"),
                    DB::Raw("(CASE WHEN detallecotizacionorden.fechaInicio IS NULL THEN c.fecha ELSE DATE_FORMAT(detallecotizacionorden.fechaInicio, '%Y-%m-%d') END ) as fecha"),
                    DB::Raw("DATE_FORMAT(detallecotizacionorden.fechaInicio,'%H:%i') as hora"),
                    'detallecotizacionorden.enProceso')
                    ->get();

        $arreglo = [];
        $listDetalles = [];
        foreach ($detalles as $det) {
            //  
            $listDetalles[] = $det->id;
            $detpers = DetalleOrdenPersonal::leftJoin('trabajador as pers','pers.id','=','detallecotizacionordenpersonal.idPersonal')
            ->where('detallecotizacionordenpersonal.idDetalleCotizacionOrden','=',$det->id)
            ->select('pers.id', DB::Raw("CONCAT(pers.apellidos,' ',pers.nombres) as trabajador"))
            ->get();

            $arr2 = [];
            foreach ($detpers as $dp) {
                $arr2[] = $dp->id;
            }

            $arreglo[] = ['detalles' => $det, 'personal' => $detpers, 'listPersonal' => $arr2];
        }

        return ['detalles' => $arreglo,'listDetalles' => implode(',',$listDetalles), 'orden' => $orden];
    }

    public function getInicioFin ($id, Request $request) {
        $band = true;
        $reglas = [
            'fecha'=>  'required',
            'hora'=> 'required',
            'personal' => 'required',
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'hora.required'=> 'Indique Hora',
            'personal.required' => 'Indique al menos un Personal',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$errors = []; 

		if ($validator->fails()) {
            $errors = $validator->errors();
		}
        
        if (count($errors) > 0) {
            $band = false;
    		return ['errores' => $errors, 'estado' => $band];
        } else {
            DB::beginTransaction();
            $idPersonal = Auth::user()->usuarioId;
            $enProceso = 0;
            $det = DetalleCotizacionOrden::find($id);                
            if ($request->get('tipo') == 1) {
                DB::table('tiempodetalle')
                ->where('idDetalleOrdenTrabajo','=',$det->id)
                ->where('idMotivo','=',7)
                ->update(['deleted_at'=> null]);
            }
         
            try{
                if ($request->get('tipo') == 1) {
                    $det->fechaInicio = $request->get('fecha').' '.$request->get('hora');
                    $valid = TiempoDetalle::where('idDetalleOrdenTrabajo','=',$det->id)
                             ->where('idMotivo','=',7)
                             ->first();

                    if (is_null($valid)) {
                        $a = new TiempoDetalle;
                    } else {
                        $a = $valid;
                    }

                    $a->idDetalleOrdenTrabajo = $det->id;
                    $a->idMotivo = 7;
                    $a->inicio = $det->fechaInicio;
                    $a->idPersonal = $idPersonal;
                    $a->save();
                    $enProceso=1;
                } else {
                    $det->fechaInicio = null;
                    $a = TiempoDetalle::where('idDetalleOrdenTrabajo','=',$det->id)
                         ->where('idMotivo','=',7)->first();
                    // // dd($det, $a);
                    // $a->idDetalleOrdenTrabajo = $det->id;
                    // $a->idMotivo = 7;
                    // $a->inicio = $det->fechaInicio;
                    // $a->idPersonal = $idPersonal;
                    $a->delete();
                    // $det->fechaInicio = $request->get('fecha').' '.$request->get('hora');
                }
                
                $det->enProceso  = $enProceso;
                $det->update();
             
                
                

                $usu = $request->get('personal');
                $listaUsu = explode(',',$usu);
                
                $agregados = [];
                foreach ($listaUsu as $item2) {
                    $idp = $item2;
                    $arr = DetalleOrdenPersonal::leftjoin('detallecotizacionorden as detcot','detcot.id','=','detallecotizacionordenpersonal.idDetalleCotizacionOrden')
                    ->where('detallecotizacionordenpersonal.idDetalleCotizacionOrden','=',$id) 
                    // ->where('detcot.situacion','=','P')
                    ->select('detallecotizacionordenpersonal.idPersonal')
                    ->get();

                    $dets = [];
                    foreach ($arr as $a) {
                        $dets[] = $a->idPersonal;
                    }

                    if (!in_array($idp,$dets) && !is_null($idp)) {
                        $deop = new DetalleOrdenPersonal;
                        $deop->idPersonal = $idp;
                        $deop->idDetalleCotizacionOrden = $id;
                        $deop->save();
                        $agregados[] = $idp;
                    } elseif (in_array($idp,$dets)) {
                        $agregados[] = $idp;
                    }
                }
                #ELIMINAR LOS QUE NO APARECEN
                DetalleOrdenPersonal::leftjoin('detallecotizacionorden as detcot','detcot.id','=','detallecotizacionordenpersonal.idDetalleCotizacionOrden')
                ->where('detallecotizacionordenpersonal.idDetalleCotizacionOrden','=',$id) 
                ->whereNotIn('detallecotizacionordenpersonal.idPersonal',$agregados)
                ->update(['detallecotizacionordenpersonal.deleted_at' => date('Y-m-d H:i:s') ]);


                if ($request->get('tipo') == 1) {
                    $errors[] = 'Se dio Comienzo a Actividad';
                } else {
                    $errors[] = 'Se Reinicia Actividad';
                }
            } catch (\Exception $ex) {
                $errors[] = $ex->getMessage();
                $band = false;
                DB::rollback();
            }
            DB::commit();
        }

        return ['errores' => $errors, 'estado' => $band];
    }
    public function getTrabajadores ($search, Request $request) {
        $p = Personal::where(DB::Raw("CONCAT(apellidos,' ',nombres)"), 'LIKE', $search.'%')
            ->select(DB::Raw("CONCAT(apellidos,' ',nombres) as personal"),'id')
            ->orderBy('personal','ASC')
            ->take(10)
            ->get();

        return ['personal' => $p];
    }

    public function getTrabajadoresQuery (Request $request) {
        $search = $request->get('query');

        $p = Personal::join('categoriapersonal as cp','cp.id','=','trabajador.idCategoriaPersonal')
            ->where('cp.nombre','Mecanico')
            ->where(DB::Raw("CONCAT(trabajador.apellidos,' ',trabajador.nombres)"), 'LIKE', $search.'%')
            ->select(DB::Raw("CONCAT(trabajador.apellidos,' ',trabajador.nombres) as personal"),'trabajador.id')
            ->orderBy('personal','ASC')
            ->take(10)
            ->get();

        return ['personal' => $p];
    }

    public function getTrabajadoresAsesorQuery (Request $request) {
        // $search = $request->get('query');

        $p = Personal::join('categoriapersonal as cp','cp.id','=','trabajador.idCategoriaPersonal')
            ->where('cp.nombre','Asesor')
            // ->where(DB::Raw("CONCAT(trabajador.apellidos,' ',trabajador.nombres)"), 'LIKE', $search.'%')
            ->select(DB::Raw("CONCAT(trabajador.apellidos,' ',trabajador.nombres) as personal"),'trabajador.id')
            ->orderBy('personal','ASC')
            // ->take(10)
            ->get();

        return ['personal' => $p];
    }

	public function validar (Request $request) {
		$reglas = [
            'fecha'=>  'required',
            'numero'=> 'required',
			'documento'=> 'required|numeric|digits_between:8,11',
			'cliente'=> 'required',
            'placa'	=> 'required',
            'idCliente' => 'required',
			'idCita' => 'required',
            'listDetalles' => 'required',
            'totalDoc'    => 'required|numeric',
            // 'imagen' => 'required|image',
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'numero.required'=> 'Indique N°',
            'documento.required'=> 'Indique Documento',
            'cliente.required'=> 'Registre Cliente',
            'idCliente.required'  => 'Cliente no Encontrado',
            'idCita.required'  => 'Indique Cita',
            'placa.required'=> 'Indique Placa',
            'idCliente.required'    => 'Indique Cliente',
            'idCita.required'    => 'Seleccione una Cita de la Tabla',
			'vin.required'=> 'Indique Vin',
			'tiempo.required' => 'Indique Tiempo',
    		'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'totalDoc.numeric'    => 'Total debe ser un número',
            'listDetalles.required'=> 'Indique Detalles a Orden de Trabajo',
            // 'imagen.required' => 'Indique CheckList',   
            // 'imagen.image'    => 'Archivo Seleccionado no es una Imagen',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function validarReclamo (Request $request) {
        $reglas = [
            'fecha'=>  'required',
            'idComprobante'=> 'required|numeric|min:1',
			'idPersona'=> 'required|numeric|min:1',
			'idPersonal'=> 'required|numeric|min:1',
            'area' => 'required|numeric|min:1',
			'reclamo'=> 'required',
            'grado' => 'required'
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'idComprobante.required'=> 'Indique N° Orden',
            'idComprobante.min' => 'N° Orden seleccionada es incorrecta',
            'idPersona.required'=> 'Indique Cliente',
            'idPersona.min'=> 'Cliente seleccionado incorrecto',
            'idPersonal.required'=> 'Indique Personal Asignado',
            'idPersonal.min'=> 'Personal Asignado seleccionado incorrecto',
            'area.required' => 'Indique Área',
            'area.min'=> 'Área seleccionado incorrecto',
            'reclamo.required' => 'Especifique reclamo',
            'grado.required' => 'Indique Grado'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
    } 

    public function validarReclamoResponder (Request $request) {
        $reglas = [
            'id'=>  'required',
            'solucion'=> 'required',
        ];

        $mensajes = [
            'id.required'=> 'Indique ID',
            'solucion.required'=> 'Indique Solución',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
    } 

    public function validarNotificacion (Request $request) {
        $reglas = [
            'id'=>  'required',
            'titulo'=> 'required|max:255',
            'mensaje' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'tipo' => 'required|max:1',
            'hora' => 'required',
        ];

        $mensajes = [
            'id.required'=> 'Indique ID',
            'titulo.required'=> 'Indique Título',
            'titulo.max' => 'Título debe tener como máximo 255 caracteres',
            'mensaje.required' => 'Indique Mensaje',
            'fecha_inicio.required' => 'Indique Fecha Inicio',
            'fecha_fin.required' => 'Indique Fecha Fin',
            'tipo.required' => 'Indique Tipo',
            'hora.required' => 'Indique Hora'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
    }

    public function validarAgregarCot (Request $request) {
		$reglas = [
            'id'    => 'required',  
            'cliente'=> 'required',
            'placa'	=> 'required',
            'clienteId' => 'required',
		    'listDetalles' => 'required',
            'totalDoc'    => 'required|numeric',
        ];

        $mensajes = [
            'id.required'=> 'Indique Orden de Trabajo',
            'cliente.required'=> 'Registre Cliente',
            'clienteId.required'  => 'Cliente no Encontrado',
            'placa.required'=> 'Indique Placa',
         	'totalDoc.required'	=> 'Indique Total',
			'totalDoc.numeric'    => 'Total debe ser un número',
            'listDetalles.required'=> 'Indique Detalles a Orden de Trabajo',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function validarDetallePersonal (Request $request) {
		$reglas = [
            'id'    => 'required',  
            'cliente'=> 'required',
            'placa'	=> 'required',
            'clienteId' => 'required',
            'trabajadorId' => 'required',
		    'listDetalles' => 'required',
        ];

        $mensajes = [
            'id.required'=> 'Indique Orden de Trabajo',
            'cliente.required'=> 'Registre Cliente',
            'clienteId.required'  => 'Cliente no Encontrado',
            'trabajadorId.required' => 'Indique Personal Asignado',
            'placa.required'=> 'Indique Placa',
            'listDetalles.required'=> 'Indique Detalles de Asignación',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}


    public function eliminar(Request $request) {
        DB::beginTransaction();
        $errors = [];
        $band = true;
        try {
            $id = $request->get('id');
            $orden = OrdenTrabajo::find($id);
            if ($orden->situacion != 'F') {
                $detalles = DetalleOrdenTrabajo::where('idOrdenTrabajo', '=', $orden->id)
                            ->get();
                foreach ($detalles as $det) {
                    $c = Cotizacion::find($det->idCotizacion);
                    if (!is_null($c)) {
                        $ds = DetalleCotizacion::where('idCotizacion', '=', $det->id)
                              ->get();
                        foreach ($ds as $dv) {
                            $detalleStock = StockProductoDetalleSalida::where('idOrdenTrabajo', $orden->id)
                                            ->where('idAlmacen', $c->idAlmacenSalida)
                                            ->where('idProducto', $dv->idProducto)
                                            ->select('id', 'idStockProductoDetalle', 'stock')
                                            ->get();
                            foreach ($detalleStock as $dt) {
                                $spds = StockProductoDetalleSalida::find($dt->id);
                                if (!is_null($spds)) {
                                    $spd = StockProductoDetalle::where('id', $dt->idStockProductoDetalle)
                                        ->where('idTienda', $c->idTienda)
                                        ->where('idAlmacenSalida', $c->idAlmacenSalida)
                                        ->first();
                                    if (!is_null($spd)) {
                                        $spd->stock = $spd->stock + $dt->stock;
                                        $spd->update();
                                    } else {
                                        $band = false;
                                    }
                                    $spds->delete();
                                } else {
                                    $band = false;
                                }
    
                                if (!$band) {
                                    break;
                                }
                            }
    
                            $sp = StockProducto::where('idAlmacen', '=', $c->idAlmacenSalida)
                                ->where('idProducto', '=', $dv->idProducto)
                                ->first();
    
                            if (!is_null($sp)) {
                                $sp->totalVentas = $sp->totalVentas - $dv->cantidad;
                                $sp->update();
                            } else {
                                $band = false; 
                            }
    
                            $dv->delete();	
    
                            if (!$band) {
                                break;
                            }
                        }
                        $c->situacion = 'V';
                        $c->update();
                    }
                    if (!$band) {
                        break;
                    }
                    $det->situacion = 'A';
                    $det->update();
                    $det->delete();
                }
                $orden->idPersonalEliminar = Auth::user()->usuarioId;
                $orden->situacion = 'A';
                $orden->update();
                $orden->delete();
    
                // Verificar y actualizar la cita
                $cita = DB::table('cita')
                    ->where('id', $orden->idCita)
                    ->first(); // Verificamos si existe la cita
    
                if ($cita) { // Si la cita existe, la actualizamos
                    DB::table('cita')
                        ->where('id', $orden->idCita)
                        ->update(['situacion' => 'A', 'deleted_at' => null]);
                } else {
                    $errors[] = "No se encontró la cita asociada.";
                    $band = false;
                }
    
                if ($band) {
                    $errors[] = 'Orden de Trabajo Eliminada Correctamente';
                }
    
            }
        } catch (\Exception $ex) {
            $errors[] = "Error en la línea " . $ex->getLine() . ": " . $ex->getMessage();
            $band = false;
            DB::rollback();
        }
        DB::commit();
    
        return ['errores' => (object)$errors, 'estado' => $band];	
    }

    public function arreglar (Request $request) {
        DB::beginTransaction();
        $detalles = DetalleOrdenTrabajo::where('idOrdenTrabajo','>=',49)->get();
        $arreglo = [];
        foreach ($detalles as $det) {
            $spdsA = DB::table('stockproductodetallesalida')->where('idCotizacion',$det->idCotizacion)
                    ->update(['idOrdenTrabajo' => $det->idOrdenTrabajo, 'idCotizacion' => null]);
        }
        
        $query = DB::select("SELECT * FROM `detalleordentrabajo` WHERE idOrdenTrabajo >=49 AND idOrdenTrabajo NOT IN (SELECT idOrdenTrabajo FROM `stockproductodetallesalida` WHERE idOrdenTrabajo IS NOT NULL )");
        foreach ($query as $el) {
            $detprods = DetalleCotizacion::whereNotNull('idProducto')
                        ->where('idCotizacion','=',$el->idCotizacion)
                        ->select('id','idProducto','cantidad','idLote','descripcion')
                        ->get();

            foreach ($detprods as $dp) {
                # QUITAR PARA LOS DETALLES DE PRECIOS
                $s = StockProductoDetalle::where('idProducto',$dp->idProducto)
                    ->where('idLote',$dp->idLote)
                    ->where('idTienda',$this->tiendaId)
                    ->where('idAlmacenSalida',$this->almacenId)
                    ->first();

                // $cantidad = $dp->cantidad;
                $acumDism = $dp->cantidad;
                if (!is_null($s)) {
                    if ($acumDism <= $s->stock) {
                        $s->stock = $s->stock - $acumDism;
                        $s->update();

                        $spds = new StockProductoDetalleSalida;
                        $spds->idStockProductoDetalle = $s->id;
                        $spds->idProducto = $s->idProducto;
                        $spds->idAlmacen = $s->idAlmacenSalida;
                        $spds->idOrdenTrabajo = $el->idOrdenTrabajo;
                        $spds->stock = $acumDism;
                        $spds->save();

                    } else {
                        $arreglo[] = $dp->id;
                    }
                } else {
                    $arreglo[] = $dp->id;
                }
                    // dd($query);
            }
        }  
        dd('ok', json_encode($arreglo));
        DB::commit();
    }

    public function getVerificacionCheckList ($id, Request $request) {
        $band = false;
        $verificacion = DB::table('verificacionchecklist')
                        ->where('idOrdenTrabajo', $id)
                        ->select('id','idOrdenTrabajo','observaciones','rptaVerifCheckCalidad', 'rptaVerifCheckManejo')
                        ->first();
        if (!is_null($verificacion)) {
            $band = true;
        }

        return ['verificacion' => $verificacion, 'estado' => $band];
    }

    public function guardarVerificacionCheckList (Request $request) {
        DB::beginTransaction();
		$errors = [];
        $band = true;
		try{
			$idOrden = $request->get('idOrden');
            $id = $request->get('id');
            $verify = DB::table('verificacionchecklist')->where('id', $id)->first();
            if (is_null($verify)) {
                $verificacion = new VerificacionCheckList;
            } else {
                $verificacion = VerificacionCheckList::find($id);
            }
            $verificacion->rptaVerifCheckCalidad = $request->get('rptaCheckCalidad');
            $verificacion->rptaVerifCheckManejo = $request->get('rptaCheckManejo');
            $verificacion->observaciones = $request->get('observaciones');
            $verificacion->idOrdenTrabajo = $idOrden;
            $verificacion->idPersonal = Auth::user()->usuarioId;

            if (is_null($verify)) {
                $verificacion->save();
                $errors[] = 'Verificación de CheckList guardado Correctamente';
            } else {
                $verificacion->update();
                $errors[] = 'Verificación de CheckList actualizado Correctamente';
            }

            if (!$band) {
                $errors[] = 'Verificación de CheckList no se puede actualizar, intentelo nuevamente';
                $band = false;
            }
        }catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}

        DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	
    }

    #MOD DE RECLAMOS BASADOS EN LA ORDEN DE TRABAJO
    public function getAreas (Request $request) {
        $areas = Area::select('id', 'nombre')->orderBy('nombre','ASC')->get();
        return ['estado' => true, 'areas' => $areas];
    }

    public function getOrdenes (Request $request) {
        $personaId = $request->get('personaId');
        $query = $request->get('query');

        $ordenes = OrdenTrabajo::select('id',
        DB::Raw("CONCAT('OD', LPAD(serie,2,'0') ,'-', LPAD(numero,8,'0'), '- Ptje: ', puntuacionEncuesta) as orden"))
        ->where('puntuacionEncuesta', '!=', '-1')
        ->where('idCliente', $personaId)
        ->where(DB::Raw("CONCAT('OD', LPAD(serie,2,'0') ,'-', LPAD(numero,8,'0'))"),'LIKE', '%'.$query.'%')
        ->get();

        return ['ordenes' => $ordenes];
    }

    public function getPersonal (Request $request) {
        $query = $request->get('query');

        $personal = Personal::select('id',
        DB::Raw("CONCAT(dni, ' - ', apellidos,' ', nombres) as personal"))
        ->where(DB::Raw("CONCAT(dni, ' - ', apellidos,' ', nombres)"),'LIKE', '%'.$query.'%')
        ->get();

        return ['personal' => $personal];
    }

    public function responderReclamo(Request $request) {
        $errors = $this->validarReclamoResponder($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
                $id = $request->get('id');
            	$reclamo = Reclamo::find($id);
				$reclamo->solucion = $request->solucion;
                $reclamo->update();
                
                if($band) {
                    $msjeD = MensajeSistema::where('idReclamo', $id)->first();  
                    if (!is_null($msjeD)) {
                        $msjeD->delete();

                        $msje = new MensajeSistema;
                        $msje->titulo = "Respuesta de Reclamo ". str_pad($reclamo->numero, 3,'0', STR_PAD_LEFT).'-'.date('Y', strtotime($reclamo->created_at))." de grado ".
                        ($reclamo->grado == 'U'?'Urgente':
                        ($reclamo->grado == 'M'?'Medio':'Bajo'));
                        $msje->descripcion = $reclamo->solucion;
                        $msje->idPersonal = Auth::user()->usuarioId;
                        $msje->idPersonalMostrar = $reclamo->idPersonal;
                        $msje->tipo = 'R';
                        $msje->save();
    
                    }
                  
                    $errors[] = 'Solución de Reclamo Registrado Correctamente';
                }
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage(); //'Llene el CheckList de Inventario Por Favor'; //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
            if($band) {
                DB::commit();
            } else {
            	DB::rollback();
			}
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
    }

    public function cerrarReclamo($id, Request $request) {
        DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$reclamo = Reclamo::find($id);
			if (!is_null($reclamo) && $reclamo->situacion == 'A') {
                $reclamo->situacion = 'C';
                $reclamo->update();
                MensajeSistema::where('idReclamo', $id)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
             
                $errors = ['Reclamo Cerrado Correctamente'];    
			} else {
				$errors = ['Reclamo no Encontradoo o ya antes Cerrado'];
				$band = false;
			}
		}catch(\Exception $ex){
			$errors = $ex->getMessage();
			$band = false;
			DB::rollback();
		}

		if ($band) {
			DB::commit();
		} else {
			DB::rollback();
		}
		return ['errores' => $errors, 'estado' => $band];	
    }

    public function guardarReclamo(Request $request) {
		$errors = $this->validarReclamo($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{

            	$reclamo = new Reclamo;
				$reclamo->fecha = $request->get('fecha'); 
				$reclamo->idCliente = $request->get('idPersona');
				$reclamo->idOrden = $request->get('idComprobante');
                $reclamo->idPersonalAsignadoA  = $request->get('idPersonal');
                $reclamo->idAreaDestino = $request->get('area');
                $reclamo->reclamo = $request->get('reclamo');
                $reclamo->grado = $request->get('grado');
                $reclamo->idPersonal = Auth::user()->usuarioId;
               
                #PARA CALCULAR NUMERO POR AÑO
                $anio = date('Y');
                $numero = Reclamo::where(DB::Raw("DATE_FORMAT(created_at, '%Y')"), $anio)
                ->max('numero');
                $reclamo->numero = $numero + 1;

                $reclamo->save();
                
                if($band) {
                    $msje = new MensajeSistema;
                    $msje->titulo = "Nuevo Reclamo ". str_pad($reclamo->numero,'0', STR_PAD_LEFT).'-'.$anio." de grado ".
                    ($reclamo->grado == 'U'?'Urgente':
                    ($reclamo->grado == 'M'?'Medio':'Bajo')). " por Responder";
                    $msje->descripcion = $reclamo->reclamo;
                    $msje->idPersonal = Auth::user()->usuarioId;
                    $msje->idPersonalMostrar = $request->get('idPersonal');
                    $msje->idReclamo = $reclamo->id;
                    $msje->save();

                    $errors[] = 'Reclamo Registrado Correctamente';
                }
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage(); //'Llene el CheckList de Inventario Por Favor'; //$ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
            if($band) {
                DB::commit();
            } else {
            	DB::rollback();
			}
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}

    public function getReclamosAll (Request $request) {
        $documento 	 = $request->get('documento');
		$cliente     = $request->get('razonSocial');
		$orden       = $request->get('orden');
		$cita        = $request->get('cita ');
		$reclamo        = $request->get('reclamo');
		$trabajador  = $request->get('asignadoA');
		$trabajadorR = $request->get('registradoPor');
        $situacion   = $request->get('situacion');

        $fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
        $reclamos = DB::table('reclamo as r')
                      ->leftjoin('ordentrabajo as ot', 'ot.id','=', 'r.idOrden')
                      ->leftjoin('area as ar', 'ar.id', '=', 'r.idAreaDestino')
                      ->leftjoin('cita as c','c.id','=','ot.idCita')
                      ->leftjoin('persona as cl','cl.id','=','r.idCliente')
                      ->leftjoin('trabajador as t','t.id','=','r.idPersonal')
                      ->leftjoin('trabajador as t2','t2.id','=','r.idPersonalAsignadoA')
                      ->where(function ($qq) use ($cliente) {
                            $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                               ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
                      })
                      ->where('cl.documento','LIKE', '%'.$documento.'%')
                      ->where(DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0'))"),'LIKE', '%'.$orden.'%')
                      ->where(DB::Raw("CONCAT(LPAD(r.numero,3,'0'), '-', DATE_FORMAT(r.created_at, '%Y'))"),'LIKE', '%'.$reclamo.'%')
                      ->where(DB::Raw("CONCAT(c.serie,'-',c.numero)"),'LIKE', '%'.$cita.'%')
                      ->where(DB::Raw("CONCAT(t.apellidos,' ',t.nombres)"),'LIKE', '%'.$trabajadorR.'%')
                      ->where(DB::Raw("CONCAT(t2.apellidos,' ',t2.nombres)"),'LIKE', '%'.$trabajador.'%');
                      ///   ->where('ot.placa','LIKE', '%'.$placa.'%');

		if ($fechaI != '') {
			$reclamos = $reclamos->where('r.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$reclamos = $reclamos->where('r.fecha','<=',$fechaF);
		}

        if ($situacion != 'todo' && $situacion != '') {
			$reclamos = $reclamos->where('r.situacion',$situacion);
		}

        $reclamos =  $reclamos->select('r.*', DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as cita"), 
        'ot.placa', 'ot.puntuacionEncuesta', DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden") ,
        DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
        'cl.documento as doc', 'ar.nombre as areaDestino',
        DB::Raw("CONCAT(LPAD(r.numero,3,'0'), '-', DATE_FORMAT(r.created_at, '%Y')) as nroReclamo"),
        DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),
        DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
        DB::Raw("CONCAT(t2.nombres,' ',t2.apellidos) as asignadoA"), 
        DB::Raw("(CASE WHEN r.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
        DB::Raw("DATE_FORMAT(r.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente')
        ->orderBy('r.created_at','DESC');
		


		//    ->orderBy('cotizacion.fecha','ASC');

		$lista = $reclamos->get();
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
		
		$lista = $reclamos->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

    	return ['reclamos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Reclamo':' Reclamos'), 'page' => $page,  'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin, 'usuarioId' => Auth::user()->usuarioId];
    } 

    public function excelReclamos (Request $request) {
        $documento 	 = $request->get('doc');
		$cliente     = $request->get('cliente');
		$orden       = $request->get('orden');
		$cita        = $request->get('cita ');
		$reclamo        = $request->get('reclamo');
		$trabajador  = $request->get('asignado');
		$trabajadorR = $request->get('registrado');
        $situacion   = $request->get('situacion');

        $fechaI 	 = $request->get('fechai');
    	$fechaF	 = $request->get('fechaf');
       
        $reclamos = DB::table('reclamo as r')
                      ->leftjoin('ordentrabajo as ot', 'ot.id','=', 'r.idOrden')
                      ->leftjoin('area as ar', 'ar.id', '=', 'r.idAreaDestino')
                      ->leftjoin('cita as c','c.id','=','ot.idCita')
                      ->leftjoin('persona as cl','cl.id','=','r.idCliente')
                      ->leftjoin('trabajador as t','t.id','=','r.idPersonal')
                      ->leftjoin('trabajador as t2','t2.id','=','r.idPersonalAsignadoA')
                      ->where(function ($qq) use ($cliente) {
                            $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
                               ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente.'%');
                      })
                      ->where('cl.documento','LIKE', '%'.$documento.'%')
                      ->where(DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0'))"),'LIKE', '%'.$orden.'%')
                      ->where(DB::Raw("CONCAT(LPAD(r.numero,3,'0'), '-', DATE_FORMAT(r.created_at, '%Y'))"),'LIKE', '%'.$reclamo.'%')
                      ->where(DB::Raw("CONCAT(c.serie,'-',c.numero)"),'LIKE', '%'.$cita.'%')
                      ->where(DB::Raw("CONCAT(t.apellidos,' ',t.nombres)"),'LIKE', '%'.$trabajadorR.'%')
                      ->where(DB::Raw("CONCAT(t2.apellidos,' ',t2.nombres)"),'LIKE', '%'.$trabajador.'%');
                      ///   ->where('ot.placa','LIKE', '%'.$placa.'%');

		if ($fechaI != '') {
			$reclamos = $reclamos->where('r.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$reclamos = $reclamos->where('r.fecha','<=',$fechaF);
		}

        if ($situacion != 'todo' && $situacion != '') {
			$reclamos = $reclamos->where('r.situacion',$situacion);
		}

        $reclamos =  $reclamos->select('r.*', DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as cita"), 
        'ot.placa', 'ot.puntuacionEncuesta', DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden") ,
        DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
        'cl.documento as doc', 'ar.nombre as areaDestino',
        DB::Raw("CONCAT(LPAD(r.numero,3,'0'), '-', DATE_FORMAT(r.created_at, '%Y')) as nroReclamo"),
        DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),
        DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
        DB::Raw("CONCAT(t2.nombres,' ',t2.apellidos) as asignadoA"), 
        DB::Raw("(CASE WHEN r.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
        DB::Raw("DATE_FORMAT(r.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente')
        ->orderBy('r.created_at','DESC')
        ->get();
		

     	$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Reclamos");
		$hoja1->setCellValue('A1','RECLAMOS');
		$hoja1->mergeCells('A1:O1');
		$hoja1->getStyle('A1:O1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','FECHA');
		$hoja1->setCellValue('B2','DOCUMENTO');
		$hoja1->setCellValue('C2','CLIENTE');
		$hoja1->setCellValue('D2','N° RECLAMO');
		$hoja1->setCellValue('E2','ÁREA DESTINO');
		$hoja1->setCellValue('F2','N° CITA');
		$hoja1->setCellValue('G2','N° ORDEN');
        $hoja1->setCellValue('H2','PLACA');
        $hoja1->setCellValue('I2','PTJE ENCUESTA');
        $hoja1->setCellValue('J2','ASIGNADO A');
        $hoja1->setCellValue('K2','SOLUCION');
        $hoja1->setCellValue('L2','SITUACION');
        $hoja1->setCellValue('M2','GRADO');
        $hoja1->setCellValue('N2','REGISTRADO POR');
        $hoja1->setCellValue('O2','REGISTRADO EL');
   
        
		$hoja1->getStyle('A2:O2')->applyFromArray($this->estilo_header);
		
        $j = 3;
		foreach ($reclamos as $value) {
            $hoja1->setCellValue('A'.$j, $value->fecha);
            $hoja1->setCellValue('B'.$j,$value->doc);
            $hoja1->setCellValue('C'.$j,$value->cliente);
            $hoja1->setCellValue('D'.$j,$value->nroReclamo);
            $hoja1->setCellValue('E'.$j,$value->areaDestino);
            $hoja1->setCellValue('F'.$j,$value->cita);
            $hoja1->setCellValue('G'.$j,$value->orden);
            $hoja1->setCellValue('H'.$j,$value->placa);
            $hoja1->setCellValue('I'.$j,$value->puntuacionEncuesta);
            $hoja1->setCellValue('J'.$j,$value->asignadoA);
            $hoja1->setCellValue('K'.$j,$value->solucion);
            $hoja1->setCellValue('L'.$j,($value->situacion == 'A'?'Abierto':'Cerrado'));
            $hoja1->setCellValue('M'.$j,($value->grado == 'U'?'Urgente':($value->grado == 'M'?'Medio':'Bajo')));
            $hoja1->setCellValue('N'.$j,$value->trabajador);
            $hoja1->setCellValue('O'.$j,$value->fechaR);
            
            $hoja1->getStyle('A'.$j.':O'.$j)->applyFromArray($this->estilo_content);
	        
            $j++;
		}

        foreach(range('A','O') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		// $objWriter->setPreCalculateFormulas(true);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Reclamos-'.date('Y-m-d H:i').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		


    }
	
    public function verReclamo ($id, Request $request) {
        $reclamo = DB::table('reclamo as r')
        ->leftjoin('ordentrabajo as ot', 'ot.id','=', 'r.idOrden')
        ->leftjoin('area as ar', 'ar.id', '=', 'r.idAreaDestino')
        ->leftjoin('cita as c','c.id','=','ot.idCita')
        ->leftjoin('persona as cl','cl.id','=','r.idCliente')
        ->leftjoin('trabajador as t','t.id','=','r.idPersonal')
        ->leftjoin('trabajador as t2','t2.id','=','r.idPersonalAsignadoA')
        ->where('r.id', $id)
        ->select('r.*', DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as cita"), 
            DB::Raw("(CASE r.grado WHEN 'U' THEN 'Urgente' WHEN 'M' THEN 'Medio' ELSE 'Bajo' END) AS gradoText"),
            'ot.placa', 'ot.puntuacionEncuesta', DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden") ,
            DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
            'cl.documento as doc', 'ar.nombre as areaDestino',
            DB::Raw("CONCAT(LPAD(r.numero,3,'0'), '-', DATE_FORMAT(r.created_at, '%Y')) as nroReclamo"),
            DB::Raw("CONCAT(t.nombres,' ',t.apellidos) as trabajador"), 
            DB::Raw("CONCAT(t2.nombres,' ',t2.apellidos) as asignadoA"), 
            DB::Raw("(CASE WHEN r.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
            DB::Raw("DATE_FORMAT(r.created_at,'%d/%m/%Y %h:%i %p') as fechaR"),'ot.idCliente')
        ->first();
		
        return ['reclamo' => $reclamo];
        ///   ->where('ot.placa','LIKE', '%'.$placa.'%');

    }
    public function eliminarReclamo ($id, Request $request) {
        DB::beginTransaction();
		$errors = '';
		$band = true;
		try{
			$reclamo = Reclamo::find($id);
			if (!is_null($reclamo)) {
                $reclamo->idPersonalEliminar = Auth::user()->usuarioId;
                $reclamo->update();
                $reclamo->delete();	
                
                MensajeSistema::where('idReclamo', $id)
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
              
			} else {
				$errors = ['Reclamo no Encontrado, ya antes Eliminado'];
				$band = false;
			}
		}catch(\Exception $ex){
			$errors = $ex->getMessage();
			$band = false;
			DB::rollback();
		}

		if ($band) {
			DB::commit();
		} else {
			DB::rollback();
		}
		return ['errores' => $errors, 'estado' => $band];	
    }

    #NOTIFICACIONES
    public function guardarNotificacion (Request $request) {
        $errors = $this->validarNotificacion($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
		
            DB::beginTransaction();
            $errors = [];
            $band = true;
            try{
                $msje = new MensajeSistema;
                $msje->titulo = $request->get('titulo');
                $msje->descripcion = $request->get('mensaje');
                $msje->idPersonal = Auth::user()->usuarioId;
                $msje->idPersonalMostrar = Auth::user()->usuarioId;
                $msje->fechaInicio = $request->get('fecha_inicio');
                $msje->fechaFin = $request->get('fecha_fin');
                $msje->hora = $request->get('hora');

                $msje->idPersonalMostrar = Auth::user()->usuarioId;
                if ($request->get('tipo') == 'P') {
                    $msje->idProspecto = $request->get('id');
                } else {
                    $msje->idOportunidad = $request->get('id'); 
                    
                    #OPORTUNIDAD
                    $oportunidad = Oportunidad::find($msje->idOportunidad);
                    if (!is_null($oportunidad) && $oportunidad->fase == 'C') {
                        $oportunidad->fase = 'N';
                        $oportunidad->update();
                    }
                }
                $msje->save();
                $errors[] = 'Notificación Creada con Éxito';    
            
            } catch(\Exception $ex){
                $errors[] = $ex->getMessage();
                $band = false;
                DB::rollback();
            }

            if ($band) {
                DB::commit();
            } else {
                DB::rollback();
            }
        }
		return ['errores' => $errors, 'estado' => $band];	
    }
}
