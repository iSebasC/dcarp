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

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Serie;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\TipoCambio;
use App\Models\StockProducto;
use App\Models\StockProductoDetalle;
use App\Models\PedidoCompra;
use App\Models\DetallePedidoCompra;
use App\Models\CompraAuto;
use App\Models\DetalleCompraAuto;
use App\Models\LoteAuto;
use App\Models\Cuenta;

use App\Libraries\Funciones;
use DB;
use Auth;

use Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

class CompraController extends Controller
{
	public $almacenId = 2;
	public $tiendaId  = 1;

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
	
    public function getAll (Request $request) {
		$documento 	 = $request->get('documento');
		$comprobante = $request->get('comprobante');
		$proveedor   = $request->get('proveedor');
		
		$flete       = $request->get('flete');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$tipodoc	 = $request->get('tipodoc'); 	

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$compras = DB::table('compra')->leftJoin('persona as pr','pr.id','=','compra.idProveedor')
					->where('compra.documento','LIKE','%'.$comprobante.'%')
					->where('pr.documento','LIKE','%'.$documento.'%')
					->where(function ($qq) use($proveedor) {
						$qq->where('pr.razonSocial','LIKE','%'.$proveedor.'%')
							->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$proveedor.'%');
					});
		// $cotizacion = DB::table('cotizacion')
		// 			  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente');
				  	  
		if ($fechaI != '') {
			$compras = $compras->where('compra.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$compras = $compras->where('compra.fecha','<=',$fechaF);
		}

		if ($moneda != '' && $moneda != 'todo') {
			$compras = $compras->where('compra.tipoMoneda','=',$moneda);
		}

		if ($tipodoc != '' && $tipodoc != 'todo') {
			$compras = $compras->where('compra.tipoDocumento','=',$tipodoc);
		}

		if ($flete != '') {
			$compras = $compras->where('compra.flete','=', (float)$flete);
		}

		$compras =  $compras->select('compra.id',/*DB::Raw("CONCAT(compra.tipoDocumento,'',compra.documento) as documento"),*/
		DB::Raw("(CASE pr.tipoProveedor WHEN 'N' THEN 'Nacional' 
		WHEN 'L' THEN 'Local' WHEN 'I' THEN 'Internacional' ELSE '' END) as tipoProveedor"),
		'compra.documento',
		'pr.documento as documentoProveedor', DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"), 
		DB::Raw("DATE_FORMAT(compra.fecha,'%d/%m/%Y') as fecha"), DB::Raw("DATE_FORMAT(compra.fechaVencimiento,'%d/%m/%Y') as fechaVencimiento"),'compra.diasCredito', DB::Raw("FORMAT(compra.flete,2) as flete"), 
		DB::Raw("FORMAT(compra.total,2) as total"), 
		DB::Raw("(CASE WHEN compra.tipoMoneda = 'S' THEN 'Soles' ELSE 'Dólares' END) as tipoMoneda"), 
		DB::Raw("DATE_FORMAT(compra.created_at,'%d/%m/%Y') as fechaRegistro"),
		DB::Raw("(CASE WHEN compra.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"));
		


		//    ->orderBy('compra.fecha','ASC');

		$lista = $compras->get();
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
		
		$lista = $compras->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['compras' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Compra':' Compras'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

	public function obtenerPedidoCompra ($id) {
		$pedido = PedidoCompra::leftJoin('persona as pr','pr.id','=','pedidocompra.idProveedor')
				  ->where('pedidocompra.id',$id)
				  ->select('pedidocompra.*',
				   DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN 
				   	pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"),
				  	'pr.documento')
				  ->first();
		$detalles = DetallePedidoCompra::leftJoin('producto as prod', 'prod.id','=', 'detallepedidocompra.idProducto')
					->leftjoin('tipoproducto as tp','tp.id','=','prod.idMargenGanancia')
				 	->where('idPedidoCompra', $id)
					->select('detallepedidocompra.*', 'tp.porcentaje')
					->get();

		return ['estado' => true, 'pedido' => $pedido, 'detalles' => $detalles];
	}

	public function getPedidoAll (Request $request) {
		$documento 	 = $request->get('documento');
		$comprobante = $request->get('comprobante');
		$proveedor   = $request->get('proveedor');
		
		// $flete       = $request->get('flete');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$situacion	 = $request->get('situacion'); 	

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$compras = DB::table('pedidocompra as pc')
					->leftJoin('persona as pr','pr.id','=','pc.idProveedor')
					->leftJoin('compra as cp', 'cp.id','=', 'pc.idCompra')
					->where(DB::Raw("CONCAT(LPAD(pc.numero,3,'0'), '-', DATE_FORMAT(pc.created_at, '%Y'))"), 'LIKE','%'.$comprobante.'%')
					->where('pr.documento','LIKE','%'.$documento.'%')
					->where(function ($qq) use($proveedor) {
						$qq->where('pr.razonSocial','LIKE','%'.$proveedor.'%')
							->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$proveedor.'%');
					});
		// $cotizacion = DB::table('cotizacion')
		// 			  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente');
				  	  
		if ($fechaI != '') {
			$compras = $compras->where('pc.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$compras = $compras->where('pc.fecha','<=',$fechaF);
		}

		if ($moneda != '' && $moneda != 'todo') {
			$compras = $compras->where('pc.tipoMoneda','=',$moneda);
		}

		if ($situacion != '' && $situacion != 'todo') {
			$compras = $compras->where('pc.situacion','=',$situacion);
		}

		// if ($flete != '') {
		// 	$compras = $compras->where('compra.flete','=', (float)$flete);
		// }

		$compras =  $compras->select('pc.id',/*DB::Raw("CONCAT(compra.tipoDocumento,'',compra.documento) as documento"),*/
		// 'compra.documento',
		DB::Raw("(CASE pr.tipoProveedor WHEN 'N' THEN 'Nacional' 
		WHEN 'L' THEN 'Local' WHEN 'I' THEN 'Internacional' ELSE '' END) as tipoProveedor"),
		'pr.documento as documentoProveedor', DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"), 
		DB::Raw("DATE_FORMAT(pc.fecha,'%d/%m/%Y') as fecha"), 
		DB::Raw("CONCAT(LPAD(pc.numero,3,'0'), '-', DATE_FORMAT(pc.created_at, '%Y')) as comprobante"),
		DB::Raw("DATE_FORMAT(pc.fechaServicio,'%d/%m/%Y') as fechaServicio"),'pc.diasCredito', 
		DB::Raw("FORMAT(pc.total,2) as total"), 
		DB::Raw("(CASE WHEN pc.tipoMoneda = 'S' THEN 'Soles' ELSE 'Dólares' END) as tipoMoneda"), 
		DB::Raw("DATE_FORMAT(pc.created_at,'%d/%m/%Y') as fechaRegistro"), 
		DB::Raw("IFNULL(CONCAT(cp.tipoDocumento,'',cp.documento),'-') as docCompra"),
		DB::Raw("(CASE WHEN pc.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"), 'pc.situacion');
		


		//    ->orderBy('compra.fecha','ASC');

		$lista = $compras->get();
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
		
		$lista = $compras->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['compras' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Pedidos de Compra':' Pedidos de Compra'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];

	}

	public function excel (Request $request) {
		$documento 	 = $request->get('doc');
		$comprobante = $request->get('comp');
		$proveedor   = $request->get('prov');
		
		$flete       = $request->get('flete');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$tipodoc	 = $request->get('tipodoc'); 	

    	
    	$compras = DB::table('compra')
					->join('persona as pr','pr.id','=','compra.idProveedor')
					->join('trabajador as t','t.id','=','compra.idPersonal')
					->where('compra.documento','LIKE','%'.$comprobante.'%')
					->where('pr.documento','LIKE','%'.$documento.'%')
					->where(function ($qq) use($proveedor) {
						$qq->where('pr.razonSocial','LIKE','%'.$proveedor.'%')
							->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$proveedor.'%');
					});
		// $cotizacion = DB::table('cotizacion')
		// 			  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente');
				  	  
		if ($fechaI != '') {
			$compras = $compras->where('compra.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$compras = $compras->where('compra.fecha','<=',$fechaF);
		}

		if ($moneda != '' && $moneda != 'todo') {
			$compras = $compras->where('compra.tipoMoneda','=',$moneda);
		}

		if ($tipodoc != '' && $tipodoc != 'todo') {
			$compras = $compras->where('compra.tipoDocumento','=',$tipodoc);
		}

		if ($flete != '') {
			$compras = $compras->where('compra.flete','=', (float)$flete);
		}

		$compras =  $compras->select('compra.id',DB::Raw("CONCAT(compra.tipoDocumento,'/',compra.documento) as documento"), DB::Raw("(CASE pr.tipoProveedor WHEN 'N' THEN 'Nacional' 
		WHEN 'L' THEN 'Local' WHEN 'I' THEN 'Internacional' ELSE '' END) as tipoProveedor"),
		'pr.documento as documentoProveedor', DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"), 
		DB::Raw("CONCAT(t.apellidos,' ', t.nombres) as trabajador"),
		DB::Raw("DATE_FORMAT(compra.fecha,'%d/%m/%Y') as fecha"), 
		DB::Raw("DATE_FORMAT(compra.fechaVencimiento,'%d/%m/%Y') as fechaVencimiento"),'compra.diasCredito', DB::Raw("FORMAT(compra.flete,2) as flete"), 
		DB::Raw("FORMAT(compra.total,2) as total"), 
		DB::Raw("(CASE WHEN compra.tipoMoneda = 'S' THEN 'Soles' ELSE 'Dólares' END) as tipoMoneda"), 
		DB::Raw("DATE_FORMAT(compra.created_at,'%d/%m/%Y') as fechaRegistro"),
		DB::Raw("(CASE WHEN compra.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
		DB::Raw("DATE_FORMAT(compra.deleted_at,'%d/%m/%Y %H:%i:%s') as fecha_eliminado")
		)->orderBy('compra.created_at','ASC');
		$lista = $compras->get();
		
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','LISTADO DE COMPRAS');
		$hoja1->mergeCells('A1:L1');
		$hoja1->getStyle('A1:L1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','FECHA');
		$hoja1->setCellValue('C2','TIPO PROVEEDOR');
		$hoja1->setCellValue('D2','DOCUMENTO');
		$hoja1->setCellValue('E2','RAZÓN SOCIAL');
		$hoja1->setCellValue('F2','COMPROBANTE');
		$hoja1->setCellValue('G2','MONEDA');
		$hoja1->setCellValue('H2','FECHA DE VENCIMIENTO');
		$hoja1->setCellValue('I2','FLETE');
		$hoja1->setCellValue('J2','TOTAL');
		$hoja1->setCellValue('K2','FECHA DE REGISTRO');
		$hoja1->setCellValue('L2','REGISTRADO POR');
		$hoja1->setCellValue('M2','ELIMINADO EL');
	
		$hoja1->getStyle('A2:M2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->fecha);
			$hoja1->setCellValue('C'.$j,$value->tipoProveedor);
			$hoja1->setCellValue('D'.$j,$value->documentoProveedor);
			$hoja1->setCellValue('E'.$j,$value->proveedor);
			$hoja1->setCellValue('F'.$j,$value->documento);
			$hoja1->setCellValue('G'.$j,$value->tipoMoneda);
			$hoja1->setCellValue('H'.$j,$value->fechaVencimiento);
			$hoja1->setCellValue('I'.$j,$value->flete);
			$hoja1->setCellValue('J'.$j,$value->total);
			$hoja1->setCellValue('K'.$j,$value->fechaRegistro);
			$hoja1->setCellValue('L'.$j,$value->trabajador);
			$hoja1->setCellValue('M'.$j,($value->eliminado == 'S'?$value->fecha_eliminado:''));
					
			$hoja1->getStyle('A'.$j.':M'.$j)->applyFromArray($this->estilo_content);
			$cont++;
			$j++;
		}

		foreach(range('A','M') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="compras.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	

		
	}

	public function excel2 (Request $request) {
		$documento 	 = $request->get('doc');
		$comprobante = $request->get('comp');
		$proveedor   = $request->get('prov');
		
		$flete       = $request->get('flete');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$tipodoc	 = $request->get('tipodoc'); 	

    	
    	$compras = DB::table('compra')
					->join('persona as pr','pr.id','=','compra.idProveedor')
					->join('trabajador as t','t.id','=','compra.idPersonal')
					->join('detallecompra as dc', 'dc.idCompra', '=', 'compra.id')
					->join('producto as prod', 'prod.id', '=','dc.idProducto')
					->where(function($q) {
						$q->where('prod.nombre','LIKE','%CHEVROLET%')
						->orwhere('prod.nombre','LIKE','%ISUZU%');
					})
					->where('compra.documento','LIKE','%'.$comprobante.'%')
					->where('pr.documento','LIKE','%'.$documento.'%')
					->where(function ($qq) use($proveedor) {
						$qq->where('pr.razonSocial','LIKE','%'.$proveedor.'%')
							->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$proveedor.'%');
					});
		// $cotizacion = DB::table('cotizacion')
		// 			  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente');
				  	  
		if ($fechaI != '') {
			$compras = $compras->where('compra.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$compras = $compras->where('compra.fecha','<=',$fechaF);
		}

		if ($moneda != '' && $moneda != 'todo') {
			$compras = $compras->where('compra.tipoMoneda','=',$moneda);
		}

		if ($tipodoc != '' && $tipodoc != 'todo') {
			$compras = $compras->where('compra.tipoDocumento','=',$tipodoc);
		}

		if ($flete != '') {
			$compras = $compras->where('compra.flete','=', (float)$flete);
		}

		$compras =  $compras->select('dc.descripcion', 'dc.preciocompra','dc.precioventa','dc.cantidad','compra.id',DB::Raw("CONCAT(compra.tipoDocumento,'/',compra.documento) as documento"), DB::Raw("(CASE pr.tipoProveedor WHEN 'N' THEN 'Nacional' 
		WHEN 'L' THEN 'Local' WHEN 'I' THEN 'Internacional' ELSE '' END) as tipoProveedor"),
		'pr.documento as documentoProveedor', DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"), 
		DB::Raw("CONCAT(t.apellidos,' ', t.nombres) as trabajador"),
		DB::Raw("DATE_FORMAT(compra.fecha,'%d/%m/%Y') as fecha"), 
		DB::Raw("DATE_FORMAT(compra.fechaVencimiento,'%d/%m/%Y') as fechaVencimiento"),'compra.diasCredito', DB::Raw("FORMAT(compra.flete,2) as flete"), 
		DB::Raw("FORMAT(compra.total,2) as total"), 
		DB::Raw("(CASE WHEN compra.tipoMoneda = 'S' THEN 'Soles' ELSE 'Dólares' END) as tipoMoneda"), 
		DB::Raw("DATE_FORMAT(compra.created_at,'%d/%m/%Y') as fechaRegistro"),
		DB::Raw("(CASE WHEN compra.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
		DB::Raw("DATE_FORMAT(compra.deleted_at,'%d/%m/%Y %H:%i:%s') as fecha_eliminado")
		)->orderBy('compra.created_at','ASC');
		$lista = $compras->get();
		
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Inventario");
		$hoja1->setCellValue('A1','LISTADO DE COMPRAS [CHEVROLET/ISUZU]');
		$hoja1->mergeCells('A1:R1');
		$hoja1->getStyle('A1:R1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','FECHA');
		$hoja1->setCellValue('C2','MARCA DETALLE');
		$hoja1->setCellValue('D2','DETALLE');
		$hoja1->setCellValue('E2','CANTIDAD');
		$hoja1->setCellValue('F2','PRECIO COMPRA');
		$hoja1->setCellValue('G2','PRECIO VENTA');
		
		$hoja1->setCellValue('H2','TIPO PROVEEDOR');
		$hoja1->setCellValue('I2','DOCUMENTO');
		$hoja1->setCellValue('J2','RAZÓN SOCIAL');
		$hoja1->setCellValue('K2','COMPROBANTE');
		$hoja1->setCellValue('L2','MONEDA');
		$hoja1->setCellValue('M2','FECHA DE VENCIMIENTO');
		$hoja1->setCellValue('N2','FLETE');
		$hoja1->setCellValue('O2','TOTAL');
		$hoja1->setCellValue('P2','FECHA DE REGISTRO');
		$hoja1->setCellValue('Q2','REGISTRADO POR');
		$hoja1->setCellValue('R2','ELIMINADO EL');
	
		$hoja1->getStyle('A2:R2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->fecha);
			$hoja1->setCellValue('C'.$j,(strstr($value->descripcion, "CHEVROLET") != false? 'CHEVROLET': 'ISUZU'));
			$hoja1->setCellValue('D'.$j,$value->descripcion);
			$hoja1->setCellValue('E'.$j,$value->cantidad);
			$hoja1->setCellValue('F'.$j,$value->preciocompra);
			$hoja1->setCellValue('G'.$j,$value->precioventa);

			$hoja1->setCellValue('H'.$j,$value->tipoProveedor);
			$hoja1->setCellValue('I'.$j,$value->documentoProveedor);
			$hoja1->setCellValue('J'.$j,$value->proveedor);
			$hoja1->setCellValue('K'.$j,$value->documento);
			$hoja1->setCellValue('L'.$j,$value->tipoMoneda);
			$hoja1->setCellValue('M'.$j,$value->fechaVencimiento);
			$hoja1->setCellValue('N'.$j,$value->flete);
			$hoja1->setCellValue('O'.$j,$value->total);
			$hoja1->setCellValue('P'.$j,$value->fechaRegistro);
			$hoja1->setCellValue('Q'.$j,$value->trabajador);
			$hoja1->setCellValue('R'.$j,($value->eliminado == 'S'?$value->fecha_eliminado:''));
					
			$hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);
			$cont++;
			$j++;
		}

		foreach(range('A','R') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="compras_chevrolet_isuzu.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	

		
	}

	public function getAll02 (Request $request) {
		$documento 	 = $request->get('documento');
		$comprobante = $request->get('comprobante');
		$proveedor   = $request->get('proveedor');
		
		$flete       = $request->get('flete');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$tipodoc	 = $request->get('tipodoc');
    	    	

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$compras = CompraAuto::join('persona as pr','pr.id','=','compraauto.idProveedor')
				   ->where('compraauto.documento','LIKE','%'.$comprobante.'%')
				   ->where('pr.documento','LIKE','%'.$documento.'%')
				   ->where(function ($qq) use($proveedor) {
						$qq->where('pr.razonSocial','LIKE','%'.$proveedor.'%')
							->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$proveedor.'%');
				   });
		// $cotizacion = DB::table('cotizacion')
		// 			  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente');
				  	  
		if ($fechaI != '') {
			$compras = $compras->where('compraauto.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$compras = $compras->where('compraauto.fecha','<=',$fechaF);
		}

		if ($moneda != '' && $moneda != 'todo') {
			$compras = $compras->where('compraauto.tipoMoneda','=',$moneda);
		}

		if ($tipodoc != '' && $tipodoc != 'todo') {
			$compras = $compras->where('compraauto.tipoDocumento','=',$tipodoc);
		}

		if ($flete != '') {
			$compras = $compras->where('compraauto.flete','=', (float)$flete);
		}

		/*if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'proveedor':
    				if ($descripcion <> '')	
						$compras = $compras->where('pr.razonSocial','LIKE', $descripcion.'%')
									 ->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE', $descripcion. '%');
						break;
				case 'ruc':
    				if ($descripcion <> '')	
						$compras = $compras->where('pr.documento','LIKE', $descripcion.'%');
						break;
				case 'flete':
    				if ($descripcion <> '')	
						$compras = $compras->where('compraauto.flete','>=', $descripcion);
						break;
				default:
					$compras = $compras->where('compraauto.documento','LIKE', $descripcion.'%');
					break;			
    		}
    	}*/



		$compras =  $compras->select('compraauto.id',DB::Raw("CONCAT(compraauto.tipoDocumento,'',compraauto.documento) as documento"),
					'pr.documento as documentoProveedor', DB::raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as proveedor"), 
					DB::Raw("DATE_FORMAT(compraauto.fecha,'%d/%m/%Y') as fecha"), 
					DB::Raw("DATE_FORMAT(compraauto.fechaVencimiento,'%d/%m/%Y') as fechaVencimiento"),
					'compraauto.diasCredito', DB::Raw("FORMAT(compraauto.flete,2) as flete"), 
					DB::Raw("FORMAT(compraauto.total,2) as total"), 
					DB::Raw("(CASE WHEN compraauto.tipoMoneda = 'S' THEN 'Soles' ELSE 'Dólares' END) as tipoMoneda"), 
					DB::Raw("DATE_FORMAT(compraauto.created_at,'%d/%m/%Y') as fechaRegistro"));
		


		//    ->orderBy('compra.fecha','ASC');

		$lista = $compras->get();
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
		
		$lista = $compras->offset(($page-1)*$filas)
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


		
    	return ['compras' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Compra':' Compras'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
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
				   ->leftjoin('tipoproducto as tp','tp.id','=','producto.idMargenGanancia')
				   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where(function ($qq) use ($desc) {
						$qq->where('ma.nombre','LIKE','%'.$desc.'%')
						->orwhere('mt.nombre','LIKE','%'.$desc.'%')
						->orwhere('producto.nombre','LIKE','%'.$desc.'%')
						->orwhere('producto.tipoLlanta','LIKE','%'.$desc.'%')
						->orwhere('producto.medida','LIKE','%'.$desc.'%')
						->orwhere('ml.nombre','LIKE','%'.$desc.'%')
						->orWhere(DB::Raw("(CASE producto.tipoProducto 
						WHEN 'A'  THEN 'Accesorio/Repuesto' 
						WHEN 'LL' THEN 'Neumáticos' 
						WHEN 'I'  THEN 'Insumos' 
						WHEN 'B'  THEN 'Baterías' 
						ELSE 'MUELLES' END)"),'LIKE','%'.$desc.'%');
					})
				   ->select('producto.id', DB::Raw("CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre"), DB::Raw("(CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2"),
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"), DB::Raw("FORMAT(producto.precio,2) as precio"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("FORMAT(sp.totalCompras - sp.totalVentas - sp.totalIncidencias,2) as stock"),
					   'tp.porcentaje');


			#SERVICIOS

			// $sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
			// 		  ->where('servicio.nombre','LIKE',$desc.'%')
			// 		  ->select('servicio.id','servicio.nombre', DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), DB::Raw("FORMAT(servicio.precio,2) as precio"), DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("'0' as stock"));

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
					//   ->unionAll($pr)
            $pr  = $pr->get();

			return ['productos' => $pr];
	    }
	}

	public function obtenerPedidoProductos (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$pr   = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
				   ->leftjoin('tipoproducto as tp','tp.id','=','producto.idMargenGanancia')
				   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where(function ($qq) use ($desc) {
						$qq->where('ma.nombre','LIKE','%'.$desc.'%')
						->orwhere('mt.nombre','LIKE','%'.$desc.'%')
						->orwhere('producto.nombre','LIKE','%'.$desc.'%')
						->orwhere('producto.tipoLlanta','LIKE','%'.$desc.'%')
						->orwhere('producto.medida','LIKE','%'.$desc.'%')
						->orwhere('ml.nombre','LIKE','%'.$desc.'%')
						->orWhere(DB::Raw("(CASE producto.tipoProducto 
						WHEN 'A'  THEN 'Accesorio/Repuesto' 
						WHEN 'LL' THEN 'Neumáticos' 
						WHEN 'I'  THEN 'Insumos' 
						WHEN 'B'  THEN 'Baterías' 
						ELSE 'MUELLES' END)"),'LIKE','%'.$desc.'%');
					})
				   ->select('producto.id', DB::Raw("CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre"), 
				   	   DB::Raw("(CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2"),
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), 
					   DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), 
					   DB::Raw("'-' as tiempo"), DB::Raw("FORMAT(producto.precio,2) as precio"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("FORMAT(sp.totalCompras - sp.totalVentas - sp.totalIncidencias,2) as stock"),
					   'tp.porcentaje');


			#SERVICIOS

			$sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
					  ->where('servicio.nombre','LIKE','%'.$desc.'%')
					  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), 
					  DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"),
					  DB::Raw("FORMAT(servicio.precio,2) as precio"),
					  DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'-' as tipollanta"), 
					  DB::Raw("'0' as stock"), DB::Raw("'0' as porcentaje"))
					  ->unionAll($pr);
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
					//   ->unionAll($pr)
            $sr  = $sr->get();

			return ['productos' => $sr];
	    }
	}


    public function obtenerAutosCompra(Request $request) {
        $desc = $request->get('descripcion');
    
        $autos = Auto::leftJoin('marcaauto as mt', 'mt.id', '=', 'auto.marcaId')
                     ->leftJoin('modeloauto as modelo', 'modelo.id', '=', 'auto.modeloId')
                     ->join('stockauto as sa', 'sa.idAuto', '=', 'auto.id')
                     ->where(function ($qq) use ($desc) {
                         $qq->where('auto.transmision', 'LIKE', '%'.$desc.'%')
                            ->orWhere('auto.descripcion', 'LIKE', '%'.$desc.'%')
                            ->orWhere('auto.version', 'LIKE', '%'.$desc.'%')
                            ->orWhere('mt.nombre', 'LIKE', '%'.$desc.'%')
                            ->orWhere('modelo.nombre', 'LIKE', '%'.$desc.'%');
                     })
                     ->where('sa.idAlmacen', '=', $this->almacenId)
                     ->select(
                         'auto.id',
                         DB::raw("CONCAT('Marca: ', mt.nombre, ', Modelo: ', modelo.nombre, ', Año: ', auto.anio, ', Línea: ', auto.linea, ', Transmisión: ', auto.transmision, '- Versión: ', auto.version) as nombre"),
                         DB::raw("'Auto' as tipo"),
                         'auto.*',
                         DB::raw("(sa.totalCompras - sa.totalVentas - sa.totalIncidencias) as stock"),
                         'mt.nombre as marca',
                         'modelo.nombre as modelo',
                         'auto.version',
                         'auto.anio',
                         'auto.linea',
                         'auto.transmision'
                     )
                     ->groupBy('auto.id')
                     ->get();
    
        return ['autos' => $autos];
    }




    public function getCorrelativo (Request $request) {
		$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
			->where('tipoDocumento','=','C')
			->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"))
			->first();
		
		return ['numero' => $serie->numero];
	}
	
	public function guardarCompra(Request $request) {
		// dd($request);
		$errors = $this->validar($request, 1);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{	
				$proveedor = Persona::where('documento','=',$request->get('documento'))
						->where('tipoPersona','=','P')
						->select('id')
						->first();

				$fa = date('Y-m-d');
				$tipoCambio = TipoCambio::where(DB::Raw("DATE_FORMAT(fechaActualizacion, '%Y-%m-%d')"),'=',$fa)
							  ->select('factorCompra','factorVenta')
							  ->first();
				if (!is_null($tipoCambio)) {
					if (!is_null($proveedor)) {
						$c = Compra::where('idProveedor','=', $proveedor->id)
								->where('tipoDocumento','=',$request->get('tipodocumento'))
								->where('documento','=',$request->get('refdocumento'))
								->first();

						if (is_null($c)) {
							$venta = new Compra;
							$venta->fecha = $request->get('fecha');
							$venta->fechaVencimiento = $request->get('fechavencimiento');
							$venta->diasCredito	  = $request->get('credito');
							$venta->flete    = $request->get('flete');
							$venta->tipoDocumento = $request->get('tipodocumento');
							$venta->documento = $request->get('refdocumento');
							$venta->tipoMoneda = $request->get('tipoMoneda');
							if ($venta->tipoMoneda == 'D') {
								$venta->tipoCambio =  $tipoCambio->factorVenta;
							}
			
							$venta->subTotal = $request->get('subtotalDoc');
							$venta->igv = $request->get('igvDoc');
							$venta->total = $request->get('totalDoc');
							$venta->idTienda = $this->tiendaId;
							$venta->idAlmacen = $this->almacenId;
							$venta->idPersonal = Auth::user()->usuarioId;
							$venta->unidad = $request->get('unidad');
							$venta->idProveedor = $proveedor->id;

							$venta->save();
							
							$id = $venta->id;

							$detalles2 = explode(',',$request->get('listProductos'));
							
							$i = 1;
							// dd($detalles, $detalles2, $detalles3);

							if (count($detalles2) > 0 && $request->get('listProductos') != '') {
								foreach ($detalles2 as $det) {
									$detalle = new DetalleCompra;
									$detalle->item = $i;
									$detalle->descripcion = $request->get('txtproducto'.$det);
									$detalle->cantidad = $request->get('txtcantidad'.$det);
									$detalle->preciocompra = $request->get('txtprecio'.$det);
									$detalle->precioventa = $request->get('txtprecioventa'.$det);
									
									$detalle->subTotal = $request->get('txtsubtototal'.$det);
								
									$detalle->idProducto = $request->get('productoid'.$det);
									$detalle->idCompra = $id;
									$detalle->save();
									
									$lote = Lote::where('idProducto','=',$detalle->idProducto)
											->where('idAlmacen','=',$detalle->idAlmacen)
											->select('numero')
											->first();

									// dd($lote);
									$l = new Lote;
									$l->numero 	   = (!is_null($lote)?$lote->numero+1:1);
									$l->idProducto = $detalle->idProducto;
									$l->idCompra   = $detalle->idCompra;
									$l->idAlmacen  = $venta->idAlmacen;
									$l->tipoMoneda = $venta->tipoMoneda;
									
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
									$s->stock = $detalle->cantidad;
									$s->idTienda = $this->tiendaId;
									$s->idAlmacenSalida = $this->almacenId;
									$s->idProducto = $l->idProducto;
									$s->idLote = $l->id;
									$s->save();

									$sp = StockProducto::where('idAlmacen','=',$venta->idAlmacen)
											->where('idProducto','=',$detalle->idProducto)
											->first();
									$sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
									$sp->update();
				
									$i++;
								}
							}
							
							$refPedido = $request->get('ref_pedido');
							if (!is_null($refPedido) && $refPedido != '') {
								$pedido = PedidoCompra::find($refPedido);
								if (!is_null($pedido)) {
									if ($pedido->situacion != 'P') {
										$errors[] = 'Pedido ya antes Transformado a Compra';
										$band = false;			
										DB::rollback();
									} else {
										$pedido->situacion = 'T'; // Transformado
										$pedido->idCompra = $id; // Guarda idCompra
										$pedido->update();
										$errors[] = 'Compra Registrada Correctamente';
									}
								} else {
									$errors[] = 'Pedido ya antes Transformado a Compra';
									$band = false;			
									DB::rollback();
								}
							} else {
								#GENERANDO CUENTA POR PAGAR
								$cuenta = new Cuenta;
								$cuenta->tipocuenta = '2';
								
								if ($cuenta->tipocuenta == '1') {
									// $cuenta->idCliente  = $request->get('codcliente');
								} else {
									$cuenta->idProveedor = $venta->idProveedor;
									$cuenta->unidad		 = $venta->unidad;
									$cuenta->tipo      = 'C';
									$cuenta->partida   = '';
									// $cuenta->tipogasto   = 'C';
								}

								$cuenta->tipodocumento = $venta->tipoDocumento;
								$arrDoc = explode('-',$venta->documento);
								$cuenta->serie 		 = $arrDoc[0];
								$cuenta->numero		 = $arrDoc[1];
								$cuenta->fechavencimiento = $venta->fechaVencimiento;
								$cuenta->fechaemision= $venta->fecha;
								$cuenta->importe	 = $venta->total;
								$cuenta->moneda		 = ($venta->tipoMoneda == 'D'?'USD':'PEN');
								$cuenta->saldo       = $cuenta->importe;

								$pCambio = 	TipoCambio::find(1);
								if ($cuenta->moneda == 'USD') {
									$cuenta->tipoCambio =  $pCambio->factorVenta;
									$cuenta->importeSoles = $cuenta->tipoCambio * $cuenta->importe;
								} else {
									$cuenta->tipoCambio =  1; // $pCambio->factorVenta;	
									$cuenta->importeSoles = $cuenta->importe;
								}
								
								$cuenta->operacion   = ($venta->diasCredito == 0?'C':'D');
								$cuenta->sustento    = 'COMPRA DEL AREA DE LOGISTICA'; //$request->get('sustento');
								$cuenta->idPersonal  = Auth::user()->usuarioId;
								$cuenta->save();

								$errors[] = 'Compra Registrada Correctamente';
							}
					} else {
							$errors[] = 'Documento ya antes Registrado';
							$band = false;			
							DB::rollback();
						}
					} else {
						$errors[] = 'Proveedor no Registrado';
						$band = false;
						DB::rollback();
					}
				} else {
					$errors[] = 'Tipo de Cambio no Registrado';
					$band = false;
					DB::rollback();					
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

	public function guardarPedidoCompra(Request $request) {
		$errors = $this->validarPedidoCompra($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{	
				$proveedor = Persona::where('documento','=',$request->get('documento'))
						->where('tipoPersona','=','P')
						->select('id')
						->first();
				$detalles2 = explode(',',$request->get('listProductos'));
				$detalles3 = explode(',',$request->get('listServicios'));
					
				if (count($detalles2) + count($detalles3) > 0) {
					if (!is_null($proveedor)) {
						// $c = PedidoCompra::where('idProveedor','=', $proveedor->id)
						// 		->where('tipoDocumento','=',$request->get('tipodocumento'))
						// 		->where('documento','=',$request->get('refdocumento'))
						// 		->first();

						// if (is_null($c)) {
							$venta = new PedidoCompra;
							$venta->fecha = $request->get('fecha');
							$venta->fechaServicio = $request->get('fechaservicio');
							$venta->diasCredito	  = $request->get('credito');
							// $venta->tipoDocumento = $request->get('tipodocumento');
							// $venta->documento = $request->get('refdocumento');
							$venta->tipoMoneda = $request->get('tipoMoneda');
							$venta->total = $request->get('totalDoc');
							$venta->idPersonal = Auth::user()->usuarioId;	
							$venta->idProveedor = $proveedor->id;
							#PARA CALCULAR NUMERO POR AÑO
							$anio = date('Y');
							$numero = PedidoCompra::where(DB::Raw("DATE_FORMAT(created_at, '%Y')"), $anio)
							->max('numero');
							$venta->numero = $numero + 1;
							$venta->save();
							$id = $venta->id;

						
							$i = 1;
							// dd($detalles, $detalles2, $detalles3);

							if (count($detalles2) > 0 && $request->get('listProductos') != '') {
								foreach ($detalles2 as $det) {
									$detalle = new DetallePedidoCompra;
									$detalle->item = $i;
									$detalle->descripcion = $request->get('txtproducto'.$det);
									$detalle->cantidad = $request->get('txtcantidad'.$det);
									$detalle->precio = $request->get('txtprecio'.$det);
									// $detalle->precioventa = $request->get('txtprecioventa'.$det);
									
									$detalle->total = $request->get('txtsubtototal'.$det);
								
									$detalle->idProducto = $request->get('productoid'.$det);
									$detalle->idPedidoCompra = $id;
									$detalle->save();
									$i++;
								}
							}

							if (count($detalles3) > 0 && $request->get('listServicios') != '') {
								foreach ($detalles3 as $det) {
									$detalle = new DetallePedidoCompra;
									$detalle->item = $i;
									$detalle->descripcion = $request->get('txtproducto'.$det);
									$detalle->cantidad = $request->get('txtcantidad'.$det);
									$detalle->precio = $request->get('txtprecio'.$det);
									// $detalle->precioventa = $request->get('txtprecioventa'.$det);
									
									$detalle->total = $request->get('txtsubtototal'.$det);
								
									$detalle->idServicio = $request->get('productoid'.$det);
									$detalle->idPedidoCompra = $id;
									$detalle->save();
									$i++;
								}
							}
						
							$errors[] = 'Pedido de Compra Registrada Correctamente';
						// } else {
						// 	$errors[] = 'Documento ya antes Registrado';
						// 	$band = false;			
						// 	DB::rollback();
						// }
					} else {
						$errors[] = 'Proveedor no Registrado';
						$band = false;
						DB::rollback();
					}
				} else {
					$errors[] = 'No ha indicado detalles para el Pedido de Compra';
					$band = false;
					DB::rollback();
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


	public function guardarCompraAuto (Request $request) {
		$errors = $this->validar($request,2);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{	
				$proveedor = Persona::where('documento','=',$request->get('documento'))
						->where('tipoPersona','=','P')
						->select('id')
						->first();

				$fa = date('Y-m-d');
				$tipoCambio = TipoCambio::where(DB::Raw("DATE_FORMAT(fechaActualizacion, '%Y-%m-%d')"),'=',$fa)
							  ->select('factorCompra','factorVenta')
							  ->first();
				if (!is_null($tipoCambio)) {
					if (!is_null($proveedor)) {
						$c = CompraAuto::where('idProveedor','=', $proveedor->id)
								->where('tipoDocumento','=',$request->get('tipodocumento'))
								->where('documento','=',$request->get('refdocumento'))
								->first();

						if (is_null($c)) {
							$venta = new CompraAuto;
							$venta->fecha = $request->get('fecha');
							$venta->fechaVencimiento = $request->get('fechavencimiento');
							$venta->diasCredito	  = $request->get('credito');
							$venta->flete    = $request->get('flete');
							$venta->tipoDocumento = $request->get('tipodocumento');
							$venta->documento = $request->get('refdocumento');
							$venta->tipoMoneda = $request->get('tipoMoneda');
					
							if ($venta->tipoMoneda == 'D') {
								$venta->tipoCambio =  $tipoCambio->factorVenta;
							}
			
							$venta->subTotal = $request->get('subtotalDoc');
							$venta->igv = $request->get('igvDoc');
							$venta->total = $request->get('totalDoc');
							$venta->idTienda = $this->tiendaId;
							$venta->idAlmacen = $this->almacenId;
							$venta->idPersonal = Auth::user()->usuarioId;
							$venta->unidad = $request->get('unidad');
							$venta->idProveedor = $proveedor->id;

							$venta->save();
							
							$id = $venta->id;

							$detalles2 = explode(',',$request->get('listAutos'));
							
							$i = 1;
							// dd($detalles, $detalles2, $detalles3);

							if (count($detalles2) > 0 && $request->get('listAutos') != '') {
								foreach ($detalles2 as $det) {
									$detalle = new DetalleCompraAuto;
									$detalle->item = $i;
									$detalle->descripcion = $request->get('txtproducto'.$det);
									$detalle->cantidad = $request->get('txtcantidad'.$det);
									$detalle->preciocompra = $request->get('txtprecio'.$det);
									$detalle->precioventa = $request->get('txtprecioventa'.$det);
									$detalle->descripcionadicional = $request->get('descripcion_adic'.$det);
									$detalle->vin = $request->get('vin'.$det);
									
									$detalle->subTotal = $request->get('txtsubtototal'.$det);
								
									$detalle->idAuto = $request->get('productoid'.$det);
									$detalle->idCompra = $id;
									$detalle->save();
									
									$lote = LoteAuto::where('idAuto','=',$detalle->idAuto)
											->where('idAlmacen','=',$detalle->idAlmacen)
											->select('numero')
											->first();

									// dd($lote);
									$l = new LoteAuto;
									$l->numero 	   = (!is_null($lote)?$lote->numero+1:1);
									$l->idAuto = $detalle->idAuto;
									$l->idCompra   = $detalle->idCompra;
									$l->idAlmacen  = $venta->idAlmacen;
									$l->tipoMoneda = $venta->tipoMoneda;
									$l->descripcionadicional    = $request->get('descripcion_adic'.$det);
									$l->vin  = $request->get('vin'.$det);

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
									$s->stock = $detalle->cantidad;
									$s->idTienda = $this->tiendaId;
									$s->idAlmacenSalida = $this->almacenId;
									$s->idAuto = $l->idAuto;
									$s->idLoteAuto = $l->id;
									$s->tipo = 'A';
									$s->save();

									$sp = StockAuto::where('idAlmacen','=',$venta->idAlmacen)
											->where('idAuto','=',$detalle->idAuto)
											->first();
									$sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
									$sp->update();
				
									$i++;
								}
							}
						    
						    #GENERANDO CUENTA POR PAGAR
							$cuenta = new Cuenta;
							$cuenta->tipocuenta = '2';
							
							if ($cuenta->tipocuenta == '1') {
								// $cuenta->idCliente  = $request->get('codcliente');
							} else {
								$cuenta->idProveedor = $venta->idProveedor;
								$cuenta->unidad		 = $venta->unidad;
								$cuenta->tipo      = 'C';
								$cuenta->partida   = '';
								// $cuenta->tipogasto   = 'C';
							}

							$cuenta->tipodocumento = $venta->tipoDocumento;
							$arrDoc = explode('-',$venta->documento);
							$cuenta->serie 		 = $arrDoc[0];
							$cuenta->numero		 = $arrDoc[1];
							$cuenta->fechavencimiento = $venta->fechaVencimiento;
							$cuenta->fechaemision= $venta->fecha;
							$cuenta->importe	 = $venta->total;
							$cuenta->moneda		 = ($venta->tipoMoneda == 'D'?'USD':'PEN');
							$cuenta->saldo       = $cuenta->importe;

							$pCambio = 	TipoCambio::find(1);
							if ($cuenta->moneda == 'USD') {
								$cuenta->tipoCambio =  $pCambio->factorVenta;
								$cuenta->importeSoles = $cuenta->tipoCambio * $cuenta->importe;
							} else {
								$cuenta->tipoCambio =  1; // $pCambio->factorVenta;	
								$cuenta->importeSoles = $cuenta->importe;
							}
							
							$cuenta->operacion   = ($venta->diasCredito == 0?'C':'D');
							$cuenta->sustento    = 'COMPRA DEL AREA DE LOGISTICA'; //$request->get('sustento');
							$cuenta->idPersonal  = Auth::user()->usuarioId;
							$cuenta->save();
					
							$errors[] = 'Compra Registrada Correctamente';
						} else {
							$errors[] = 'Documento ya antes Registrado';
							$band = false;			
							DB::rollback();
						}
					} else {
						$errors[] = 'Proveedor no Registrado';
						$band = false;
						DB::rollback();
					}
				} else {
					$errors[] = 'Tipo de Cambio no Registrado';
					$band = false;
					DB::rollback();					
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

	public function getTipoCambio (Request $request) {
		// $fa = date('Y-m-d');
		$precio = TipoCambio::select(DB::Raw("DATE_FORMAT(fechaActualizacion,'%d/%m/%Y %H:%i') as fechaUltima"),
				  'factorCompra','factorVenta')
				  ->orderBy('fechaUltima','DESC')
				  ->first();
		if (!is_null($precio)) {
			return ['estado' => true, 'tipo' => $precio];
		} else {
			return ['estado' => false];
		}
	}
	
	public function getValidTipoCambio (Request $request) {
		$fecha_act = date('Y-m-d');
		$tipo = TipoCambio::where(DB::raw("DATE_FORMAT(fechaActualizacion,'%Y-%m-%d')"),'=', $fecha_act)
				->first();

		$band = false;
		if (!is_null($tipo)) {
			$band = true;
		}

		return ['estado' => $band];
	}

	public function validarTipoCambio (Request $request) {
		$reglas = [
        	'compra' => 'numeric|required|min:1',
        	'venta' => 'numeric|required|min:1',
        ];

        $mensajes = [
        	'compra.required'  => 'Indique Compra',
            'compra.numeric'  => 'Compra debe ser numérico',
            'compra.min'  => 'Compra debe ser mayor a 1',
  			'venta.required'  => 'Indique Venta',
            'venta.numeric'  => 'Venta debe ser numérico',
            'venta.min'  => 'Venta debe ser mayor a 1',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function guardarTipoCambio (Request $request) {
		$errors = $this->validarTipoCambio($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				$p = TipoCambio::find(1);
				$p->factorCompra = $request->get('compra');
				$p->factorVenta  = $request->get('venta');
				$p->fechaActualizacion = date('Y-m-d H:i:s');
				$p->idPersonal = Auth::user()->usuarioId;	
				$p->update();
				$cad = ' Actualizado';

				$this->actualizarLotes($p);

				$errors[] = 'Tipo de Cambio '.$cad.' Correctamente';
	

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}	
	}

	public function guardarTipoCambioAPI (Request $request) {
		DB::beginTransaction();
		$band = true;
		$errors = [];

		try{
			$p = TipoCambio::find(1);
			$p->factorCompra = $request->get('precio_compra');
			$p->factorVenta  = $request->get('precio_venta');
			$p->fechaSunat   = $request->get('fecha_sunat');
			$p->fechaActualizacion = date('Y-m-d H:i:s');
			$p->idPersonal = Auth::user()->usuarioId;	
			$p->update();
			$cad = ' Actualizado';

			$this->actualizarLotes($p);

			$errors[] = 'Tipo de Cambio '.$cad.' Correctamente';


		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
	
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	
	}

	public function actualizarLotes ($tipo) {
		$lotes 	   = Lote::where('situacion','=','V')->select('id')->get();
		$lotesAuto = LoteAuto::where('situacion','=','V')->select('id')->get();
		
		$precioDolar = 0;
		$precioSoles = 0;
		foreach ($lotes as $l) {
			$a = Lote::find($l->id);
			if ($a->tipoMoneda == 'S') {
				$precioDolar = round(($a->precioSoles/$tipo->factorVenta),2);	
				if ($precioDolar > $a->precioDolar) {
					$a->precioDolares = $precioDolar;	
				}
			} else {
				$precioSoles = round(($tipo->factorCompra * $a->precioDolares),2);
				if ($precioSoles > $a->precioSoles) {
					$a->precioSoles = $precioSoles;
				}
				$st = StockProductoDetalle::where('idLote',$a->id)->first();
				if (!is_null($st)) {
					$st->precioSoles = $a->precioSoles;
					$st->update();
				}
			}
			$a->update();
		}

		foreach ($lotesAuto as $l) {
			$a = LoteAuto::find($l->id);
			if ($a->tipoMoneda == 'S') {
				$precioDolar = round(($a->precioSoles/$tipo->factorVenta),2);
				if ($precioDolar > $a->precioDolar) {
					$a->precioDolares = $precioDolar;
				}
			} else {
				$precioSoles = round(($tipo->factorCompra * $a->precioDolares),2);
				if ($precioSoles > $a->precioSoles) {
					$a->precioSoles = $precioSoles;
				}
				$st = StockProductoDetalle::where('idLoteAuto',$a->id)->first();
				if (!is_null($st)) {
					$st->precioSoles = $a->precioSoles;
					$st->update();
				}
			}
			$a->update();
		}
	}

	public function validar (Request $request, $tipo) {
		$reglas = [
            'fecha'=>  'required',
			'documento'=> 'required|numeric|digits_between:8,11',
          	'cliente'=> 'required',
          	'flete'	=> 'required|numeric|min:0',
            'tipodocumento' => 'required|string',
		  	'refdocumento'=> 'required|max:13',
            'tipoMoneda' => 'required',
            'fechavencimiento' => 'required',
            'credito'		=> 'required|numeric|min:0',
			'listProductos' => ($tipo == 1?'required':'nullable'),
			'listAutos' => ($tipo == 2?'required':'nullable'),
			'unidad' => 'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric'
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'documento.required'=> 'Indique Documento',
			'cliente.required'=> 'Registre Cliente',
			'flete.required'  => 'Indique Flete',
			'flete.numeric'	  => 'Flete debe ser un número',
			'flete.min'		  => 'Flete debe ser mayor o igual a Cero',
			'credito.required' => 'Indique Días de Crédito',
			'credito.min'=> 'Días de Crédito debe ser mayor o igual a Cero',
			'credito.numeric'	 => 'Días de Crédito debe ser un número',
		    'tipodocumento.required'=> 'Indique Tipo de Documento',
			'refdocumento.required'=> 'Indique Documento de Referencia',
			'refdocumento.max'=> 'Documento de Referencia debe tener máximo 13 Caracteres',
			'tipoMoneda.required'=> 'Indique Tipo de Moneda',
			'listProductos.required' => 'Indique Detalles de Compra',
			'listAutos.required' => 'Indique Detalles de Compra',
    		'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
			'unidad.required' => 'Indique Unidad'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}
	
	public function validarPedidoCompra (Request $request) {
		$reglas = [
            'fecha'=>  'required',
			'documento'=> 'required|numeric|digits_between:8,11',
          	'cliente'=> 'required',
            'tipoMoneda' => 'required',
            'fechaservicio' => 'required',
            'credito'		=> 'required|numeric|min:0',
			'listProductos' => 'nullable',
			// 'listServicios' => 'nullable',
			'totalDoc'    => 'required|numeric'
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'documento.required'=> 'Indique Documento',
			'cliente.required'=> 'Registre Cliente',
			'credito.required' => 'Indique Días de Crédito',
			'credito.min'=> 'Días de Crédito debe ser mayor o igual a Cero',
			'credito.numeric'	 => 'Días de Crédito debe ser un número',
		  	'tipoMoneda.required'=> 'Indique Tipo de Moneda',
			'listProductos.required' => 'Indique Detalles de Pedido de Compra',
			// 'listServicios.required' => 'Indique Detalles de Pedido de Compra',
			'totalDoc.required'	=> 'Indique Total',
			'totalDoc.numeric'    => 'Total debe ser un número',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}


	public function validarBaja (Request $request) {
		$reglas = [
            'tipo'=>  'required',
			'listEliminacion'=> ($request->get('tipo') == 'A'?'nullable':'required'),
          	'docRef'=> ($request->get('tipo') == 'A'?'nullable':'required'),
        ];

        $mensajes = [
            'tipo.required'=> 'Indique Tipo de Anulación',
            'listEliminacion.required'=> 'Indique Elementos a Aplicar Nota de Crédito',
			'docRef.required'=> 'Indique Doc. de Referencia',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}
	

    public function getDetalles ($id, Request $request) {
		$detalles = DB::table('compra')->leftJoin('detallecompra as det','det.idCompra','=','compra.id')
					->where('compra.id','=',$id)
					->select('det.cantidad','det.descripcion', 
							DB::Raw("FORMAT(compra.total,2) as total"),
							DB::Raw("FORMAT(det.preciocompra,2) as preciocompra"), 
							DB::Raw("FORMAT(det.precioventa,2) as precioventa"),
							DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id',DB::Raw("(CASE WHEN det.deleted_at IS NULL THEN 1 ELSE 0 END) as estado"),
							'compra.tipoMoneda')
					->orderBy('det.id','ASC')
					->get();

		$total = 0;
		$tipo = '';
		if (count($detalles)) {
			$total = $detalles[0]->total;
			$tipo  = $detalles[0]->tipoMoneda;
		}

    	return ['detalles' => $detalles,'total' => $total, 'tipo' => $tipo];
	}

    public function getDetalles02 ($id, Request $request) {
		$detalles = CompraAuto::leftJoin('detallecompraauto as det','det.idCompra','=','compraauto.id')
					->where('compraauto.id','=',$id)
					->select('det.cantidad','det.vin','det.descripcion', 'det.descripcionadicional',
							'compraauto.tipoMoneda',
							DB::Raw("FORMAT(compraauto.total,2) as total"),
							DB::Raw("FORMAT(det.preciocompra,2) as preciocompra"), 
							DB::Raw("FORMAT(det.precioventa,2) as precioventa"),
							DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id',DB::Raw("(CASE WHEN det.deleted_at IS NULL THEN 1 ELSE 0 END) as estado"))
					->orderBy('det.id','ASC')
					->get();

		$total = 0;
		$tipo = 'D';
		// dd($detalles, $tipo);
		if (count($detalles) > 0) {
			$total = $detalles[0]->total;
			$tipo = $detalles[0]->tipoMoneda;
		}

    	return ['detalles' => $detalles,'total' => $total, 'tipo' => $tipo];
	}


	public function darBaja ($id, Request $request) {
		$errors = $this->validarBaja($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
		
			DB::beginTransaction();
			$errors = [];
			$band = true;
			try{
				$compra = Compra::find($id);
				$tipo = $request->get('tipo');
				$detalles = DetalleCompra::where('idCompra','=',$compra->id);

				if ($tipo != 'A') {
					$lista = explode(',', $request->get('listEliminacion'));
					$detalles = $detalles->whereIn('id',$lista);
				}

				$detalles = $detalles->select('id')
							->get();

				foreach ($detalles as $det) {
						$d = DetalleCompra::find($det->id);

						$sp = StockProducto::where('idAlmacen','=',$compra->idAlmacen)
								->where('idProducto','=',$d->idProducto)
								->first();
						$sp->totalCompras = $sp->totalCompras - $d->cantidad;
						$sp->update();

						$lote = Lote::where('idProducto','=',$d->idProducto)
								->where('idAlmacen','=',$compra->idAlmacen)
								->where('idCompra','=',$compra->id)
								->first();
					
						$lote->situacion = 'A';
						$lote->update();
						$lote->delete();
						if ($tipo != 'A') {
							$d->docReferencia  = $request->get('docRef');
							$d->idPersonalNota = Auth::user()->usuarioId;
							$d->update();
						}
						$d->delete();	
					}
					$compra->idPersonalEliminar = Auth::user()->usuarioId;
					$compra->update();
					if ($tipo == 'A') {
						$compra->delete();
					}
					$errors = 'Compra Modificada Correctamente';
			
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
			DB::commit();
		}
		return ['errores' => $errors, 'estado' => $band];	
	
	}

	public function darBaja02 ($id, Request $request) {
		$errors = $this->validarBaja($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
		
			DB::beginTransaction();
			$errors = [];
			$band = true;
			try{
				$compra = CompraAuto::find($id);
				$tipo = $request->get('tipo');
				$detalles = DetalleCompraAuto::where('idCompra','=',$compra->id);

				if ($tipo != 'A') {
					$lista = explode(',', $request->get('listEliminacion'));
					$detalles = $detalles->whereIn('id',$lista);
				}

				$detalles = $detalles->select('id')
							->get();

				foreach ($detalles as $det) {
						$d = DetalleCompraAuto::find($det->id);

						$sp = StockAuto::where('idAlmacen','=',$compra->idAlmacen)
								->where('idAuto','=',$d->idAuto)
								->first();
						$sp->totalCompras = $sp->totalCompras - $d->cantidad;
						$sp->update();

						$lote = LoteAuto::where('idAuto','=',$d->idAuto)
								->where('idAlmacen','=',$compra->idAlmacen)
								->where('idCompra','=',$compra->id)
								->first();
					
						$lote->situacion = 'A';
						$lote->update();
						$lote->delete();
						if ($tipo != 'A') {
							$d->docReferencia  = $request->get('docRef');
							$d->idPersonalNota = Auth::user()->usuarioId;
							$d->update();
						}
						$d->delete();	
					}
					$compra->idPersonalEliminar = Auth::user()->usuarioId;
					$compra->update();
					if ($tipo == 'A') {
						$compra->delete();
					}
					$errors[] = 'Compra Modificada Correctamente';
			
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
			DB::commit();
		}
		return ['errores' => (object)$errors, 'estado' => $band];	
	
	}
	// ELIMINAR COMPRA
	public function eliminar ($id, Request $request) {
        DB::beginTransaction();
        $errors = '';
        $band = true;
        try{
            $compra = Compra::find($id);
            $detalles = DetalleCompra::where('idCompra','=',$compra->id)
                        ->select('id','cantidad','idProducto')
                        ->get();

            foreach ($detalles as $det) {
                $stockPD = StockProductoDetalle::where('idProducto', $det->idProducto)
                ->where('idAlmacenSalida',$compra->idAlmacen)
                ->where('idLote', DB::Raw(
                    "(SELECT id FROM lote WHERE idProducto = $det->idProducto AND idAlmacen = $compra->idAlmacen AND idCompra = $compra->id LIMIT 1)"
                ))
				->first();
				if ($stockPD->stock == $det->cantidad) {
                    // Actualizamos Stock
                    $stockP = StockProducto::where('idAlmacen','=',$compra->idAlmacen)
                            ->where('idProducto','=',$det->idProducto)
                            ->first();
                    $stockP->totalCompras = $stockP->totalCompras - $det->cantidad;
                    $stockP->update();
        
                    // Eliminar Lote
                    Lote::where('id',$stockPD->idLote)
                    ->update(['deleted_at'=> date('Y-m-d H:i:s')]);
                    // Eliminar StockProductoDetalle
                    $stockPD->delete();
                    // Eliminar Detalles de Compra
                    DetalleCompra::where('id',$det->id)
                    ->update(['deleted_at'=> date('Y-m-d H:i:s'), 
                        'idPersonalNota' => Auth::user()->usuarioId]);
                
                } else {
                    $band = false;
                    break;
                    $errors = 'En sus detalles existen movimientos de productos vigentes, verifique por favor';
                }
            }
			$compra->delete();		
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

	// ELIMINAR PEDIDO DE COMPRA
	public function eliminarPedidoCompra ($id, Request $request) {
		DB::beginTransaction();
		$errors = '';
		$band = true;
		try{
			$compra = PedidoCompra::find($id);
			if (!is_null($compra)) {
				if ($compra->situacion != 'P') {
					$errors = 'Pedido de Compra no se puede anular, verifique que no esté transformado a Compra';
					$band = false;
				} else {
					$compra->idPersonalEliminar = Auth::user()->usuarioId;
					$compra->update();
					$compra->delete();		
				}
		
			} else {
				$errors = 'Pedido de Compra no Encontrado, ya antes Anulado';
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

	// ELIMINAR COMPRA AUTO 
	public function eliminarAuto ($id, Request $request) {
        DB::beginTransaction();
        $errors = '';
        $band = true;
        try{
            $compra = CompraAuto::find($id);
            $detalles = DetalleCompraAuto::where('idCompra','=',$compra->id)
                        ->select('id','cantidad','idAuto')
                        ->get();

            foreach ($detalles as $det) {
                $stockPD = StockProductoDetalle::where('idAuto', $det->idAuto)
                ->where('idAlmacenSalida',$compra->idAlmacen)
                ->where('idLoteAuto', DB::Raw(
                    "(SELECT id FROM loteauto WHERE idAuto = $det->idAuto AND idAlmacen = $compra->idAlmacen AND idCompra = $compra->id LIMIT 1)"
                ))
				->first();
				if ($stockPD->stock == $det->cantidad) {
                    // Actualizamos Stock
                    $stockP = StockAuto::where('idAlmacen','=',$compra->idAlmacen)
                            ->where('idAuto','=',$det->idAuto)
                            ->first();
                    $stockP->totalCompras = $stockP->totalCompras - $det->cantidad;
                    $stockP->update();
        
                    // Eliminar Lote
                    LoteAuto::where('id',$stockPD->idLote)
                    ->update(['deleted_at'=> date('Y-m-d H:i:s')]);
                    // Eliminar StockProductoDetalle
                    $stockPD->delete();
                    // Eliminar Detalles de Compra
                    DetalleCompraAuto::where('id',$det->id)
                    ->update(['deleted_at'=> date('Y-m-d H:i:s'), 
                        'idPersonalNota' => Auth::user()->usuarioId]);
                
                } else {
                    $band = false;
                    break;
                    $errors = 'En sus detalles existen movimientos de productos vigentes, verifique por favor';
                }
            }
			$compra->delete();		
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

}
