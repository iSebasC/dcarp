<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Forma;
use App\Models\CategoriaProducto;
use App\Models\Acabado;
use App\Models\Producto;
use App\Models\UnidadMedida;
use App\Models\Local;
use App\Models\Auto;
use App\Models\StockProducto;
use App\Models\MarcaRepuesto;
use App\Models\MarcaAuto;
use App\Models\MarcaLlanta;
use App\Models\SistemaAuto;
use App\Models\ModeloLlanta;
use App\Models\ModeloBateria;
use App\Models\MarcaBateria;

use App\Libraries\Funciones;

use DB;
use Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;



class ProductoController extends Controller
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

    public function getAll (Request $request) {
    	$codproveedor 	 = $request->get('codproveedor');
    	$producto        = $request->get('producto');
    	$marca           = $request->get('marca');
    	$modelo          = $request->get('modelo');
		$sistemaauto     = $request->get('sistemaauto');
		$tipobateria          = $request->get('tipobateria');
    	$medida          = $request->get('medida');
   		
		$tipoId 	 = $request->get('tipoid');
    	$tipoLlantaId= $request->get('tipollantaid');
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$productos = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
				   ->where(DB::Raw("IFNULL(producto.nombre,'')"),'LIKE', '%'.$producto.'%')
				   ->where(DB::Raw("IFNULL(producto.codproveedor,'')"),'LIKE', '%'.$codproveedor.'%')
				   ->where(DB::Raw("IFNULL(sa.nombre,'')"),'LIKE', '%'.$sistemaauto.'%')
				   ->where(DB::Raw("IFNULL(producto.medida,'')"),'LIKE','%'.$medida.'%')
				   ->where(function($qq) use($marca) {
						$qq->where(DB::Raw("IFNULL(ml.nombre,'')"),'LIKE', '%'.$marca.'%')
						   ->orWhere(DB::Raw("IFNULL(ma.nombre,'')"),'LIKE', '%'.$marca.'%')
						   ->orWhere(DB::Raw("IFNULL(mb.nombre,'')"),'LIKE', '%'.$marca.'%');
				   })
				   ->where(function($qq) use($modelo) {
						$qq->where(DB::Raw("IFNULL(mol.nombre,'')"),'LIKE', '%'.$modelo.'%')
							->orWhere(DB::Raw("IFNULL(producto.modelo,'')"),'LIKE', '%'.$modelo.'%')
							->orWhere(DB::Raw("IFNULL(modb.nombre,'')"),'LIKE', '%'.$modelo.'%');
				   });

    	// if ($precio != '') {
		// 	$productos = $productos->where('producto.precio','>=', (float)$precio);
		// }

    	if ($tipoId != '' && $tipoId != 'todo') {
    		$productos = $productos->where('producto.tipoproducto','=',$tipoId);
    	}
    	
    	if ($tipoLlantaId != '' && $tipoLlantaId != 'todo') {
    		$productos = $productos->where('producto.tipollanta','=',$tipoLlantaId);
    	}
    	
		if ($tipobateria != 'todo' && $tipobateria != '') {
			$productos = $productos->where(DB::Raw("IFNULL(producto.tipoBateria,'')"),'LIKE','%'.$tipobateria.'%');
		}

		$productos =  $productos->select('producto.id', 'producto.codInterno', DB::Raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),
		DB::Raw("(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ma.nombre ELSE (CASE WHEN producto.idMarcaBateria IS NOT NULL THEN mb.nombre ELSE '-' END) END) END) as marca"),
		DB::Raw("(CASE WHEN producto.idMarcaAuto IS NOT NULL THEN mt.nombre ELSE '-' END) as marcaauto"),
		DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL AND producto.idModeloBateria IS NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL AND producto.idModeloBateria IS NULL THEN producto.modelo ELSE (CASE WHEN producto.idModeloBateria IS NOT NULL THEN modb.nombre ELSE '-' END) END) END) as modelo"),
		DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"),
		/*DB::Raw("FORMAT(producto.precio,2) as precio"),*/ DB::Raw("FORMAT(producto.stockMinimo,2) as stockMinimo"),
		DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
		DB::Raw("(CASE producto.tipoProducto 
		WHEN 'A'  THEN 'Accesorio/Repuesto' 
		WHEN 'LL' THEN 'Neumáticos' 
		WHEN 'I'  THEN 'Insumos' 
		WHEN 'B'  THEN 'Baterías' 
		ELSE 'MUELLES' END) as tipoProducto"), 
		DB::Raw("(CASE WHEN producto.codProveedor IS NULL THEN '-' ELSE producto.codProveedor END) as codProveedor"),
		DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
		DB::Raw("(CASE WHEN producto.tipoBateria IS NULL THEN '-' ELSE (CASE WHEN producto.tipoBateria = 'PR' THEN 'Profesional' ELSE (CASE WHEN producto.tipoBateria = 'AD' THEN 'Alto Desempeño' ELSE 'Platinium' END) END) END) as tipobateria"),
		DB::Raw("(CASE WHEN producto.ubicacion_registro IS NULL THEN '-' ELSE producto.ubicacion_registro END) as ubicacion_registro"))
		->orderBy('producto.nombre','ASC');
		
		// dd($productos->toSql());
		$lista = $productos->get();

		// dd($lista);
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
			$arrPag = [['opc' => '1', 'bloqueado'=> true]];
			$page = '1';
			$inicio = '1';
			$fin = '1';
	        $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $productos->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		
    	return ['productos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Producto':' Productos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	} 
	
	public function excel (Request $request) {
		$codproveedor 	 = $request->get('codproveedor');
    	$producto        = $request->get('producto');
    	$marca           = $request->get('marca');
    	$modelo          = $request->get('modelo');
		$sistemaauto     = $request->get('sistemaauto');
		$tipobateria          = $request->get('tipobateria');
    	$medida          = $request->get('medida');
   		
		$tipoId 	 = $request->get('tipoid');
    	$tipoLlantaId= $request->get('tipollantaid');
    	
		$productos = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
						->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
						->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
						->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
						->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
						->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
						->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
						->where(DB::Raw("IFNULL(producto.nombre,'')"),'LIKE', '%'.$producto.'%')
						->where(DB::Raw("IFNULL(producto.codproveedor,'')"),'LIKE', '%'.$codproveedor.'%')
						->where(DB::Raw("IFNULL(sa.nombre,'')"),'LIKE', '%'.$sistemaauto.'%')
						->where(DB::Raw("IFNULL(producto.medida,'')"),'LIKE','%'.$medida.'%')
						->where(function($qq) use($marca) {
							$qq->where(DB::Raw("IFNULL(ml.nombre,'')"),'LIKE', '%'.$marca.'%')
								->orWhere(DB::Raw("IFNULL(ma.nombre,'')"),'LIKE', '%'.$marca.'%')
								->orWhere(DB::Raw("IFNULL(mb.nombre,'')"),'LIKE', '%'.$marca.'%');
						})
						->where(function($qq) use($modelo) {
							$qq->where(DB::Raw("IFNULL(mol.nombre,'')"),'LIKE', '%'.$modelo.'%')
								->orWhere(DB::Raw("IFNULL(producto.modelo,'')"),'LIKE', '%'.$modelo.'%')
								->orWhere(DB::Raw("IFNULL(modb.nombre,'')"),'LIKE', '%'.$modelo.'%');
						});

		// dd($productos->toSql());

		// if ($precio != '') {
		// 	$productos = $productos->where('producto.precio','=', (float)$precio);
		// }

		if ($tipoId != '' && $tipoId != 'todo') {
			$productos = $productos->where('producto.tipoproducto','=',$tipoId);
		}
		
		if ($tipoLlantaId != '' && $tipoLlantaId != 'todo') {
			$productos = $productos->where('producto.tipollanta','=',$tipoLlantaId);
		}

		if ($tipobateria != 'todo' && $tipobateria != '') {
			$productos = $productos->where(DB::Raw("IFNULL(producto.tipoBateria,'')"),'LIKE','%'.$tipobateria.'%');
		}

				
		$productos =  $productos->select('producto.id','producto.codInterno', DB::Raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),
		DB::Raw("(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ma.nombre ELSE (CASE WHEN producto.idMarcaBateria IS NOT NULL THEN mb.nombre ELSE '-' END) END) END) as marca"),
		DB::Raw("(CASE WHEN producto.idMarcaAuto IS NOT NULL THEN mt.nombre ELSE '-' END) as marcaauto"),
		DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL AND producto.idModeloBateria IS NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL AND producto.idModeloBateria IS NULL THEN producto.modelo ELSE (CASE WHEN producto.idModeloBateria IS NOT NULL THEN modb.nombre ELSE '-' END) END) END) as modelo"),
		DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"),
		DB::Raw("FORMAT(producto.stockMinimo,2) as stockMinimo"),
		DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),'producto.codSunat',
		DB::Raw("(CASE WHEN producto.tipoBateria IS NULL THEN '-' ELSE (CASE WHEN producto.tipoBateria = 'PR' THEN 'Profesional' ELSE (CASE WHEN producto.tipoBateria = 'AD' THEN 'Alto Desempeño' ELSE 'Platinium' END) END) END) as tipobateria"),
		DB::Raw("(CASE producto.tipoProducto 
		WHEN 'A'  THEN 'Accesorio/Repuesto' 
		WHEN 'LL' THEN 'Neumáticos' 
		WHEN 'I'  THEN 'Insumos' 
		WHEN 'B'  THEN 'Baterías' 
		ELSE 'MUELLES' END) as tipoProducto"), 
		DB::Raw("(CASE WHEN producto.codProveedor IS NULL THEN '-' ELSE producto.codProveedor END) as codProveedor"),
		DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"), 
		DB::Raw("(CASE WHEN producto.deleted_at IS NOT NULL THEN DATE_FORMAT(producto.deleted_at,'%d/%m/%Y %H:%i') ELSE '-' END) as eliminado_el"))
		->orderBy('nombre','ASC');

		$lista = $productos->get();
		// dd($lista);
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Productos");
		$hoja1->setCellValue('A1','LISTADO DE PRODUCTOS');
		$hoja1->mergeCells('A1:M1');
		$hoja1->getStyle('A1:M1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','CODIGO');
		$hoja1->setCellValue('B2','TIPO DE PRODUCTO');
		$hoja1->setCellValue('C2','CÓDIGO SUNAT');
		$hoja1->setCellValue('D2','CÓDIGO DE PROVEEDOR');
		$hoja1->setCellValue('E2','NOMBRE');
		$hoja1->setCellValue('F2','MARCA');
		$hoja1->setCellValue('G2','MODELO');
		$hoja1->setCellValue('H2','SISTEMA DE AUTO');
		$hoja1->setCellValue('I2','MARCA DE AUTO');
		$hoja1->setCellValue('J2','TIPO DE NEUMATICO');
		$hoja1->setCellValue('K2','MEDIDA');
		$hoja1->setCellValue('L2','TIPO DE BATERÍA');
		$hoja1->setCellValue('M2','STOCK MÍNIMO');
		$hoja1->setCellValue('N2','ELIMINADO EL');
	
		$hoja1->getStyle('A2:N2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		foreach ($lista as $value) {
			// dd($value);
			$hoja1->setCellValue('A'.$j,$value->codInterno);
			$hoja1->setCellValue('B'.$j,$value->tipoProducto);
			$hoja1->setCellValue('C'.$j,$value->codSunat);
			$hoja1->setCellValue('D'.$j,$value->codProveedor);
			$hoja1->setCellValue('E'.$j,$value->nombre);
			$hoja1->setCellValue('F'.$j,$value->marca);
			$hoja1->setCellValue('G'.$j,$value->modelo);
			$hoja1->setCellValue('H'.$j,$value->sistema);
			$hoja1->setCellValue('I'.$j,$value->marcaauto);
			$hoja1->setCellValue('J'.$j,$value->tipollanta);
			$hoja1->setCellValue('K'.$j,$value->medida);
			$hoja1->setCellValue('L'.$j,$value->tipobateria);
			$hoja1->setCellValue('M'.$j,$value->stockMinimo);
			$hoja1->setCellValue('N'.$j,$value->eliminado_el);
		
			$hoja1->getStyle('A'.$j.':N'.$j)->applyFromArray($this->estilo_content);
			$j++;

		}

		foreach(range('A','N') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="productos-'.date('Ymd_His').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		
	}

	public function getMarcasRepuestos (Request $request) {
		$marcas = MarcaRepuesto::all();
  		return ['marcas' => $marcas];
	}

    public function getMarcasAuto (Request $request) {
    	$marcasauto = MarcaAuto::orderBy('nombre','ASC')->get();
  		return ['marcasauto' => $marcasauto];
    }

    public function getModelosAuto ($marcaId, Request $request) {
		$modelos = Auto::where('marcaId','=',$marcaId)
					  ->select('modelo');
		
		$m2   =   Producto::where('idMarcaAuto','=',$marcaId)
				  ->select('modelo')
				  ->unionAll($modelos)
   				  ->get();

    	return ['modelos' => $m2];
    }

    public function getModelosBateria (Request $request) {
    	$modelos = ModeloBateria::all();

    	return ['modelos' => $modelos];
    }

    public function getMarcasBateria (Request $request) {
    	$modelos = MarcaBateria::all();

    	return ['marcas' => $modelos];
    }

    public function getSistemaAuto (Request $request) {
    	$sistemas = SistemaAuto::all();

    	return ['sistemas' => $sistemas];
	}
	
	public function getMarcasLlanta (Request $request) {
    	$marcasllanta = MarcaLlanta::all();

    	return ['marcasllanta' => $marcasllanta];
	}
	
	public function getModelosLlanta (Request $request) {
    	$modelosllanta = ModeloLlanta::all();

    	return ['modelosllanta' => $modelosllanta];
	}
	
	public function getProducto($id, Request $request) {
		$prod = Producto::find($id);
    
    	if (!is_null($prod)) {
    		$respuesta = ['estado' => true, 'producto' => $prod];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
	}

	public function getProductos ($tipo, $almacenId, Request $request) {
		$productos = Producto::leftjoin('stockproducto as st','st.idProducto','=','producto.id')
					->leftjoin('categoriaProducto as c','c.id','=','producto.idCategoriaProducto')
					->leftjoin('forma as f','f.id','=','producto.idForma')
					->leftjoin('acabado as ac','ac.id','=','producto.idAcabado')
					->leftjoin('unidadMedida as u','u.id','=','producto.idUnidadMedida')
					->where('st.idAlmacen','=',$almacenId)
					->where('producto.idCategoriaProducto','=',$tipo)
					->select('producto.id','producto.precio','producto.codRegistro',DB::raw("(CASE WHEN f.nombre='No Presenta' THEN '-' ELSE f.nombre END) as forma02"), DB::raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),DB::raw("(CASE WHEN producto.calidad IS NULL THEN '-' ELSE producto.calidad END) as calidad"),DB::raw("(CASE WHEN producto.idForma IS NULL THEN '-' ELSE f.nombre END) as forma"),DB::raw("(CASE WHEN producto.idAcabado IS NULL THEN '-' ELSE (CASE WHEN ac.nombre='No Presenta' THEN '-' ELSE ac.nombre END) END) as acabado"),DB::raw("(CASE WHEN producto.espesor IS NULL THEN '-' ELSE producto.espesor END) as espesor"),DB::raw("(CASE WHEN producto.tamanio IS NULL THEN '-' ELSE producto.tamanio END) as tamanio"),DB::raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),'c.nombre as categoriaProducto','u.nombre as unidadMedidaProducto', DB::Raw("(st.totalCompras-st.totalVentas-st.totalIncidencias) as stock"))
					->groupBy('id','precio','codRegistro', 'forma02', 'nombre','calidad','forma','acabado','espesor','tamanio','medida','categoriaProducto','unidadMedidaProducto', 'st.totalCompras','st.totalVentas','st.totalIncidencias')
					->havingRaw('stock > ?', [0])
					->get();

		return ['productos' => $productos];
	}

	public function getProductosTipo ($tipo, Request $request) {
		$productos = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				    ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				    ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				    ->leftjoin('auto as a','a.id','=','producto.idModelo')
				    ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
					->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
					->where('producto.tipoProducto','=',$tipo)
					->select('producto.id',DB::Raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),DB::Raw("(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN ma.nombre ELSE '-' END) END) as marca"),DB::Raw("(CASE WHEN producto.idMarcaAuto IS NOT NULL THEN mt.nombre ELSE '-' END) as marcaauto"),DB::Raw("(CASE WHEN producto.idModelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.idModelo IS NOT NULL THEN a.modelo ELSE '-' END) END) as modelo"),DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"),'producto.tipoLlanta',DB::Raw("FORMAT(producto.precio,2) as precio"), DB::Raw("FORMAT(producto.stockMinimo,2) as stockMinimo"),
					DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
					DB::Raw("(CASE producto.tipoProducto 
					WHEN 'A'  THEN 'Accesorio/Repuesto' 
					WHEN 'LL' THEN 'Neumáticos' 
					WHEN 'I'  THEN 'Insumos' 
					WHEN 'B'  THEN 'Baterías' 
					ELSE 'MUELLES' END) as tipoProducto"), DB::Raw("(CASE WHEN producto.codProveedor IS NULL THEN '-' ELSE producto.codProveedor END) as codProveedor"))
					->get();

		return ['productos' => $productos];
	}

	public function getProductosI ($tipo, $almacenId, Request $request) {
		$productos = Producto::leftjoin('stockproducto as st','st.idProducto','=','producto.id')
					->leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				    ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				    ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				    ->leftjoin('auto as a','a.id','=','producto.idModelo')
				    ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				    ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
		   			->where('st.idAlmacen','=',$almacenId)
					->where('producto.tipoProducto','=',$tipo)
					->select('producto.id',DB::Raw("(CASE WHEN producto.nombre IS NULL THEN '-' ELSE producto.nombre END) as nombre"),DB::Raw("(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN ma.nombre ELSE '-' END) END) as marca"),DB::Raw("(CASE WHEN producto.idMarcaAuto IS NOT NULL THEN mt.nombre ELSE '-' END) as marcaauto"),DB::Raw("(CASE WHEN producto.idModelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.idModelo IS NOT NULL THEN a.modelo ELSE '-' END) END) as modelo"),DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"),'producto.tipoLlanta',DB::Raw("FORMAT(producto.precio,2) as precio"), DB::Raw("FORMAT(producto.stockMinimo,2) as stockMinimo"),
					DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
					DB::Raw("(CASE producto.tipoProducto 
					WHEN 'A'  THEN 'Accesorio/Repuesto' 
					WHEN 'LL' THEN 'Neumáticos' 
					WHEN 'I'  THEN 'Insumos' 
					WHEN 'B'  THEN 'Baterías' 
					ELSE 'MUELLES' END) as tipoProducto"), DB::Raw("(CASE WHEN producto.codProveedor IS NULL THEN '-' ELSE producto.codProveedor END) as codProveedor"),DB::Raw("(st.totalCompras-st.totalVentas-st.totalIncidencias) as stock"))
					->groupBy('id','precio','nombre','marca','marcaauto','modelo','sistema','tipoLlanta','precio','stockMinimo','medida','tipoProducto','codProveedor', 'st.totalCompras','st.totalVentas','st.totalIncidencias')
					->havingRaw('stock >= ?', [0])
					->get();

		return ['productos' => $productos];

	}

    public function guardarProducto(Request $request) {
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				$tipo  = $request->get('select_tipoProducto');
				$id_margen = 4;

				if ($tipo == 'LL') {
					if ($request->get('select_marcallanta') === 'otro') {
						$nombre = $request->get('marcallanta');
						$existe = MarcaRepuesto::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat = new MarcaRepuesto;
							$cat->nombre = $nombre_g;
							$cat->save();

							$id_marca = $cat->id;
						}else{
							$id_marca = $existe->id;
						}
					}else{
						$id_marca = $request->get('select_marcallanta');
					}

					if ($request->get('select_modelollanta') === 'otro') {
						$nombre = $request->get('modelollanta');
						$existe = ModeloLlanta::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat2 = new ModeloLlanta;
							$cat2->nombre = $nombre_g;
							$cat2->save();

							$id_modelo = $cat2->id;
						}else{
							$id_modelo = $existe->id;
						}
					}else{
						$id_modelo = $request->get('select_modelollanta');
					}
					$id_margen = 2;
				} elseif ($tipo == 'A') {
					if ($request->get('select_marcarepuesto') === 'otro') {
						$nombre = $request->get('marca');
						$existe = MarcaRepuesto::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat3 = new MarcaRepuesto;	
							$cat3->nombre = $nombre_g;
							$cat3->save();

							$id_marcaRepuesto = $cat3->id;
						}else{
							$id_marcaRepuesto = $existe->id;
						}
					}else{
						$id_marcaRepuesto = $request->get('select_marcarepuesto');
					}

					if ($request->get('select_marcaauto') === 'otro') {
						$nombre = $request->get('marcaauto');
						$existe = MarcaAuto::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat3 = new MarcaAuto;	
							$cat3->nombre = $nombre_g;
							$cat3->save();

							$id_marcaAuto = $cat3->id;
						}else{
							$id_marcaAuto = $existe->id;
						}
					}else{
						$id_marcaAuto = $request->get('select_marcaauto');
					}
					
					// if ($request->get('select_modeloauto') == 'otro') {
					$modeloAuto = $request->get('modeloauto');
					$id_margen = 1;
					// } else {
					// 	$modeloAuto = $request->get('select_modeloauto');
					// }
				} elseif ($tipo == 'B') {
					if ($request->get('select_marcabateria') === 'otro') {
						$nombre = $request->get('marcabateria');
						$existe = MarcaBateria::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat4 = new MarcaBateria;	
							$cat4->nombre = $nombre_g;
							$cat4->save();

							$id_marcaBateria = $cat4->id;
						}else{
							$id_marcaBateria = $existe->id;
						}
					}else{
						$id_marcaBateria = $request->get('select_marcabateria');
					}

					if ($request->get('select_modelobateria') === 'otro') {
						$nombre = $request->get('modelobateria');
						$existe = ModeloBateria::where('nombre','=', $nombre)->first();
						$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
						if(is_null($existe)){
							$cat5 = new ModeloBateria;	
							$cat5->nombre = $nombre_g;
							$cat5->save();

							$id_modeloBateria = $cat5->id;
						}else{
							$id_modeloBateria = $existe->id;
						}
					}else{
						$id_modeloBateria = $request->get('select_modelobateria');
					}
					$id_margen = 3;
				}

				 
				if ($id == 0) {
					$producto = new Producto;
				} else {
					$producto = Producto::find($id);
				}
				$producto->codInterno = $request->get('codigo_interno');
				$producto->codProveedor = ($tipo=='A'?$request->get('codigo'):null);
				$producto->nombre = ($tipo=='A'?$request->get('nombre'):(in_array($tipo,['I','M'])?$request->get('nombreinsumo'):($tipo=='LL'?$request->get('nombrellanta'):$request->get('nombrebateria'))));
				$producto->idMarca = ($tipo=='A'?$id_marcaRepuesto:null);
				$producto->idMarcaAuto = ($tipo=='A'?$id_marcaAuto:null);
				$producto->modelo = ($tipo=='A'?$modeloAuto:null);
				$producto->idSistema = ($tipo=='A'?$request->get('select_sistemaauto'):null);
				

				$producto->precio = $request->get('precio');
				$producto->stockMinimo = $request->get('stockminimo');
				$producto->tipoProducto = $tipo;

				$producto->idMarcaLlanta = ($tipo=='LL'?$id_marca:null);
				$producto->idModeloLlanta = ($tipo=='LL'?$id_modelo:null);
				$producto->tipoLlanta = ($tipo=='LL'?$request->get('select_tipollanta'):null);
				$producto->medida = ($tipo=='LL'?$request->get('medidallanta'):null);

				$producto->idMarcaBateria = ($tipo=='B'?$id_marcaBateria:null);
				$producto->idModeloBateria= ($tipo=='B'?$id_modeloBateria:null);
				$producto->placaBateria   = ($tipo == 'B'?$request->get('select_nroplaca'):null);
				$producto->tipoBateria    = ($tipo == 'B'?$request->get('select_tipobateria'):null);
				$producto->idMargenGanancia= $id_margen;
				$producto->codSunat=$request->get('codigo_sunat');
				$producto->ubicacion_registro = $request->get('ubicacion_registro');
				if ($id == 0) {
					$producto->save();
					$this->guardarStock($producto->id);
					$cad = ' Registrado';
				} else {
					$producto->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Producto'.$cad.' Correctamente';
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}

    public function guardarStock($id){
    	$almacenes = Local::where('tipo','=','A')->select('id')->get();
    	foreach ($almacenes as $value) {
    	 	$a = new StockProducto;
    		$a->idProducto      = $id;
    		$a->totalCompras    = 0;
    		$a->totalVentas     = 0;
    		$a->totalIncidencias      = 0;
    		$a->idAlmacen 		= $value->id;
    		$a->save();
    	} 
    }
 

	public function validar (Request $request) {
		$reglas = [
			'select_tipoProducto' => 'required',
			'codigo_interno' => 'required',
			// 'precio' => 'required|numeric',
			'stockminimo' => 'required|numeric',
			'medidallanta' => 'max:255|'.($request->get('select_tipoProducto') == 'LL'?'required':'nullable'),
			'select_tipollanta' => ($request->get('select_tipoProducto') == 'LL'?'required':'nullable'),
			'select_modelollanta' => ($request->get('select_tipoProducto') == 'LL'?'required':'nullable'),
			'select_marcallanta' => ($request->get('select_tipoProducto') == 'LL'?'required':'nullable'),
			'marcallanta' => 'max:255|'.($request->get('select_marcallanta') == 'otro'?'required':'nullable'),
			'modelollanta' =>'max:255|'. ($request->get('select_modelollanta') == 'otro'?'required':'nullable'),
	
			'codigo' => 'max:15|'.($request->get('select_tipoProducto') == 'A'?'required':'nullable'),
			'nombre' => 'max:255|'.($request->get('select_tipoProducto') == 'A'?'required':'nullable'),
			'select_marcarepuesto' => ($request->get('select_tipoProducto') == 'A'?'required':'nullable'),
			'select_marcaauto' => ($request->get('select_tipoProducto') == 'A'?'required':'nullable'),
			// 'select_modeloauto' => (($request->get('select_tipoProducto') == 'A' && $request->get('select_marcaauto') != 'otro')?'required':'nullable'),
			'select_sistemaauto' => ($request->get('select_tipoProducto') == 'A'?'required':'nullable'),
			'marca' => 'max:255|'.($request->get('select_marcarepuesto') == 'otro'?'required':'nullable'),
			'marcaauto' => 'max:255|'.(($request->get('select_marcaauto') == 'otro' && $request->get('select_tipoProducto') == 'A')?'required':'nullable'),
			'modeloauto' => 'max:255|'.($request->get('select_tipoProducto') == 'A'?'required':''),
			'nombreinsumo'=> 'max:255|'.($request->get('select_tipoProducto') == 'I'?'required':'nullable'),
            'codigo_sunat'=>'max:6|required|min:6',
			'select_marcabateria' => ($request->get('select_tipoProducto') == 'B'?'required':'nullable'),
			'select_modelobateria'=> ($request->get('select_tipoProducto') == 'B'?'required':'nullable'),
			'select_nroplaca'	  => ($request->get('select_tipoProducto') == 'B'?'required':'nullable'),
			'select_tipobateria'  => ($request->get('select_tipoProducto') == 'B'?'required':'nullable'),

			'marcabateria' => 'max:255|'.(($request->get('select_marcabateria') == 'otro' && $request->get('select_tipoProducto') == 'B')?'required':'nullable'),
			'modelobateria' => 'max:255|'.(($request->get('select_modelobateria') == 'otro' && $request->get('select_tipoProducto') == 'B')?'required':'nullable'),
			
			'nombrebateria' => 'max:255|'.($request->get('select_tipoProducto') == 'B'?'required':'nullable'),
			'nombrellanta' => 'max:255|'.($request->get('select_tipoProducto') == 'LL'?'required':'nullable'),
			
		];

        $mensajes = [
			'select_tipoProducto.required'=> 'Indique Tipo de Producto',
			'codigo_interno' => 'Indique Código de Producto',
			'codigo_sunat.required'=>'Indique Código sunat',
			'codigo_sunat.min'=>'El código sunat debe tener 06 carácteres',
			'codigo_sunat.max'=>'El código sunat debe tener 06 carácteres',

            // 'precio.required'=> 'Indique Precio',
            // 'precio.numeric'=> 'Precio debe ser numérico',
            'stockminimo.required'=> 'Indique Stock Mínimo',
            'stockminimo.numeric'=> 'Stock Mínimo debe ser numérico',
			'medidallanta.required'=> 'Indique Medida de Llanta',
			'medidallanta.max'=> 'Medida de Llanta debe tener como máximo 255 caracteres',
			'select_tipollanta.required'=> 'Seleccione Tipo de Llanta',
			'select_modelollanta.required' => 'Seleccione Modelo de Llanta',
			'select_marcallanta.required' => 'Seleccione Marca de Llanta',
			'marcallanta.required'=> 'Indique Marca de Llanta',
			'marcallanta.max'=> 'Marca de Llanta debe tener como máximo 255 caracteres',
			'modelollanta.required'=> 'Indique Modelo de Llanta',
			'modelollanta.max'=> 'Modelo de Llanta debe tener como máximo 255 caracteres',
			
			'codigo.required'=> 'Indique Codigo de Proveedor',
			'codigo.max'=> 'Codigo de Proveedor debe tener como máximo 15 caracteres',
			'nombre.required'=> 'Indique Nombre de Accesorio/Repuesto',
			'nombre.max'=> 'Nombre de Accesorio/Repuesto debe tener como máximo 255 caracteres',
			'select_marcarepuesto.required'=> 'Seleccione Marca de Repuesto',
			'select_marcaauto.required'=> 'Seleccione Marca de Auto',
			// 'select_modeloauto.required'=> 'Seleccione Modelo de Auto',
			'select_sistemaauto.required'=> 'Seleccione Sistema de Auto',
			'marca.required'=> 'Indique Marca de Accesorio',
			'marca.max'=> 'Marca de Accesorio debe tener como máximo 255 caracteres',

			'marcaauto.required'=> 'Indique Marca de Auto',
			'marcaauto.max'=> 'Marca de Auto debe tener como máximo 255 caracteres',

			'modeloauto.required'=> 'Indique Modelo de Auto',
			'modeloauto.max'=> 'Modelo de Auto debe tener como máximo 255 caracteres',

			'nombreinsumo.required'=> 'Indique Nombre de Insumo',
			'nombreinsumo.max'=> 'Nombre de Insumo debe tener como máximo 255 caracteres',
			'select_marcabateria.required'=> 'Seleccione Marca de Batería',
			'select_modelobateria.required'=> 'Seleccione Modelo de Batería',
			'select_nroplaca.required'=> 'Seleccione Nro de Placa',
			'select_tipobateria.required'=> 'Seleccione Tipo de Batería',
			'select_modelobateria.required'=> 'Seleccione Modelo de Batería',
			'marcabateria.required'=> 'Indique Marca de Batería',
			'marcabateria.max'=> 'Marca de Batería debe tener como máximo 255 caracteres',
			'modelobateria.required'=> 'Indique Modelo de Batería',
			'modelobateria.max'=> 'Modelo de Batería debe tener como máximo 255 caracteres',

			'nombrebateria.required'=> 'Indique Nombre de Batería',
			'nombrebateria.max'=> 'Nombre de Batería debe tener como máximo 255 caracteres',
			'nombrellanta.required'=> 'Indique Nombre de Neumático',
			'nombrellanta.max'=> 'Nombre de Neumático debe tener como máximo 255 caracteres',
			
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function eliminarProducto (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$prod = Producto::find($id);
			$prod->delete();
			$errors[] = 'Producto Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

    #REPORTE DE GRAFICOS DE INICIO
	public function getReporte (Request $request) {
		$productos = Producto::select(DB::Raw("COUNT(IFNULL(deleted_at,0)) as cantidad"),
		DB::Raw("(CASE tipoProducto 
		WHEN 'A'  THEN 'Accesorio/Repuesto' 
		WHEN 'LL' THEN 'Neumáticos' 
		WHEN 'I'  THEN 'Insumos' 
		WHEN 'B'  THEN 'Baterías' 
		ELSE 'MUELLES' END) as tipo"))
		->whereNull('deleted_at')
		->groupBy('tipo')
		->get();

		$labels = [];
		$datos  = [];
		foreach ($productos as $prod) {
			$labels[] = $prod->tipo;
			$datos[]  = $prod->cantidad;
		}


		return ['labels' => $labels, 'datos' => $datos];
	} 
}
