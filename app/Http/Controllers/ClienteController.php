<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Forma;
// use App\Models\CategoriaProducto;
use App\Models\Acabado;
use App\Models\Venta;
use App\Models\UnidadMedida;
use App\Models\Local;
use App\Models\StockProducto;
use App\Models\Persona;

use App\Libraries\Funciones;

use DB;
use Validator;

use Eddieace\PhpSimple\HtmlDomParser;

use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};
use Peru\Sunat\{HtmlParser, Ruc, RucParser};

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

class ClienteController extends Controller
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


	public function getAllInterface (Request $request) {
    	$dni 	 = $request->get('dni');
    	$persona = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');
		
    	// $genero 	 = $request->get('genero');
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$personal = Persona::where('documento','LIKE', '%'.$dni.'%')
					->where('tipoPersona','C')
					// ->where(function($qq) use ($persona) {
					// 	// $qq ->orWhere(DB::Raw(""),'LIKE', '%'.$persona. '%');
					// })
					->where(DB::Raw("(CASE tipoDocumento WHEN 'PN' THEN CONCAT(apellidos,' ', nombres) ELSE razonSocial END)"),'LIKE','%'.$persona.'%')
					->where(DB::Raw("IFNULL(correoElectronico,'')"),'LIKE', '%'.$correo.'%')
					->where(DB::Raw("IFNULL(telefono,'')"),'LIKE', '%'.$telefono.'%')
					->where(DB::Raw("IFNULL(direccion,'')"),'LIKE', '%'.$direccion.'%');
    
    	$personal = $personal->select('persona.*', 
					DB::raw("(CASE WHEN razonSocial IS NOT NULL THEN razonSocial ELSE CONCAT(apellidos,' ', nombres) END) as cliente"),
					DB::raw("(CASE WHEN tipoDocumento = 'PN' THEN 'DNI' ELSE 'RUC' END) as tipodoc"),
					DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as ultimamod")
					)
					->orderBy('cliente','ASC');

        $lista = $personal->get();
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
		
		$lista = $personal->offset(($page-1)*$filas)
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
        
    	return ['cliente' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Cliente':' Clientes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	}

	public function getAllInterfaceProveedor (Request $request) {
    	$dni 	 = $request->get('dni');
    	$persona = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');
		$tipo_proveedor = $request->get('tipo_proveedor');
	
    	// $genero 	 = $request->get('genero');
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$personal = Persona::where('documento','LIKE', '%'.$dni.'%')
					->where('tipoPersona','P')
					// ->where(function($qq) use ($persona) {
					// 	// $qq ->orWhere(DB::Raw(""),'LIKE', '%'.$persona. '%');
					// })
					->where(DB::Raw("(CASE tipoDocumento WHEN 'PN' THEN CONCAT(apellidos,' ', nombres) ELSE razonSocial END)"),'LIKE','%'.$persona.'%')
					->where(DB::Raw("IFNULL(correoElectronico,'')"),'LIKE', '%'.$correo.'%')
					->where(DB::Raw("IFNULL(telefono,'')"),'LIKE', '%'.$telefono.'%')
					->where(DB::Raw("IFNULL(direccion,'')"),'LIKE', '%'.$direccion.'%');
						
		if ($tipo_proveedor != '') {
			$personal = $personal->where('tipoProveedor', $tipo_proveedor);
		}
    	$personal = $personal->select('persona.*', 
					DB::raw("(CASE WHEN razonSocial IS NOT NULL THEN razonSocial ELSE CONCAT(apellidos,' ', nombres) END) as cliente"),
					DB::raw("(CASE WHEN tipoDocumento = 'PN' THEN 'DNI' ELSE 'RUC' END) as tipodoc"),
					DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as ultimamod")
					)
					->orderBy('cliente','ASC');

        $lista = $personal->get();
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
		
		$lista = $personal->offset(($page-1)*$filas)
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
        
    	return ['proveedor' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Proveedor':' Proveedores'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	}
	
	public function excel (Request $request) {
		$dni 	 = $request->get('dni');
    	$persona = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');
	
		$personal = Persona::where('documento','LIKE', '%'.$dni.'%')
		->where('tipoPersona','C')
		->where(function($qq) use ($persona) {
			$qq->where('razonSocial','LIKE', $persona.'%')
				->orWhere(DB::Raw("CONCAT(apellidos,' ', nombres)"),'LIKE', $persona. '%');
		})
		->where('correoElectronico','LIKE', '%'.$correo.'%')
		->where('telefono','LIKE', '%'.$telefono.'%')
		->where('direccion','LIKE', '%'.$direccion.'%');

		$personal = $personal->select('persona.*', 
				DB::raw("(CASE WHEN razonSocial IS NOT NULL THEN razonSocial ELSE CONCAT(apellidos,' ', nombres) END) as cliente"),
				DB::raw("(CASE WHEN tipoDocumento = 'PN' THEN 'DNI' ELSE 'RUC' END) as tipodoc"),
				DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as ultimamod")
			)
			->orderBy('cliente','ASC');

		$lista = $personal->get();

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Clientes");
		$hoja1->setCellValue('A1','LISTADO DE CLIENTES');
		$hoja1->mergeCells('A1:H1');
		$hoja1->getStyle('A1:H1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','TIPO CLIENTE');
		$hoja1->setCellValue('C2','DOCUMENTO');
		$hoja1->setCellValue('D2','CLIENTE');
		$hoja1->setCellValue('E2','CORREO ELECTRÓNICO');
		$hoja1->setCellValue('F2','TELÉFONO');
		$hoja1->setCellValue('G2','DIRECCIÓN');
		$hoja1->setCellValue('H2','MODIFICADO EL');
	
		$hoja1->getStyle('A2:H2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;

		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->tipodoc);
			$hoja1->setCellValue('C'.$j,$value->documento);
			$hoja1->setCellValue('D'.$j,$value->cliente);
			$hoja1->setCellValue('E'.$j,$value->correoElectronico);
			$hoja1->setCellValue('F'.$j,$value->telefono);
			$hoja1->setCellValue('G'.$j,$value->direccion);
			$hoja1->setCellValue('H'.$j,$value->ultimamod);
		
			$hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
			$cont++;
			$j++;
		}

		foreach(range('A','H') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="clientes.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

	public function excelProveedor (Request $request) {
		$dni 	 = $request->get('dni');
		$tipo 	 = $request->get('tipo');
    	$persona = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');

		// if(is_null($dni)) $dni = '';
		// if(is_null($persona)) $persona = '';
		// if(is_null($correo)) $correo = '';
		// if(is_null($telefono)) $telefono = '';
		// if(is_null($direccion)) $direccion = '';
		
		$personal = Persona::where('documento','LIKE', $dni.'%')
		->where('tipoPersona','=','P')
		->where(function($qq) use ($persona) {
			$qq->where('razonSocial','LIKE', $persona.'%')
				->orWhere(DB::Raw("CONCAT(apellidos,' ', nombres)"),'LIKE', $persona. '%');
		})
		->where(DB::Raw("IFNULL(correoElectronico, '')"),'LIKE', $correo.'%')
		->where(DB::Raw("IFNULL(telefono, '')"),'LIKE', $telefono.'%')
		->where(DB::Raw("IFNULL(direccion, '')"),'LIKE', $direccion.'%');

		if ($tipo != '') {
			$personal = $personal->where('tipoProveedor', $tipo);
		}
		$personal = $personal->select('persona.*', 
				DB::raw("(CASE WHEN razonSocial IS NOT NULL THEN razonSocial ELSE CONCAT(apellidos,' ', nombres) END) as cliente"),
				DB::raw("(CASE WHEN tipoDocumento = 'PN' THEN 'DNI' ELSE 'RUC' END) as tipodoc"),
				DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as ultimamod"),
				DB::raw("(CASE tipoProveedor WHEN 'N' THEN 'Nacional' 
				WHEN 'I' THEN 'Internacional'
				WHEN 'L' THEN 'Local' ELSE '' END) as tipoProveedor")
			)
			->orderBy('cliente','ASC');

		// dd($personal->toSql());
		$lista = $personal->get();
		// dd($lista);
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Proveedores");
		$hoja1->setCellValue('A1','LISTADO DE PROVEEDORES');
		$hoja1->mergeCells('A1:I1');
		$hoja1->getStyle('A1:I1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','TIPO PROVEEDOR');
		$hoja1->setCellValue('C2','TIPO DOCUMENTO');
		$hoja1->setCellValue('D2','DOCUMENTO');
		$hoja1->setCellValue('E2','PROVEEDOR');
		$hoja1->setCellValue('F2','CORREO ELECTRÓNICO');
		$hoja1->setCellValue('G2','TELÉFONO');
		$hoja1->setCellValue('H2','DIRECCIÓN');
		$hoja1->setCellValue('I2','MODIFICADO EL');
	
		$hoja1->getStyle('A2:I2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;

		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->tipoProveedor);
			$hoja1->setCellValue('C'.$j,$value->tipodoc);
			$hoja1->setCellValue('D'.$j,$value->documento);
			$hoja1->setCellValue('E'.$j,$value->cliente);
			$hoja1->setCellValue('F'.$j,$value->correoElectronico);
			$hoja1->setCellValue('G'.$j,$value->telefono);
			$hoja1->setCellValue('H'.$j,$value->direccion);
			$hoja1->setCellValue('I'.$j,$value->ultimamod);
		
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
		header('Content-Disposition: attachment;filename="proveedores.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

    public function getAll (Request $request) {
    	$filtro 	 = $request->get('filtro');
		$descripcion = $request->get('descripcion');
		$tipodocumento = $request->get('tipodocumento');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');
    	$acabadoId	 = $request->get('acabadoId');	
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$ventas = Venta::leftjoin('persona as cl','cl.id','=','venta.idCliente')
				  ->whereNotNull('venta.tipoComprobante');
		
		if ($fechaI != '') {
			$ventas = $ventas->where('venta.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$ventas = $ventas->where('venta.fecha','<=',$fechaF);
		}

		if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'cliente':
    				if ($descripcion <> '')	
						$ventas = $ventas->where('cl.razonSocial','LIKE', $descripcion.'%')
									 ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $descripcion. '%');
    				break;
				case 'documento':
    				$ventas = $ventas->where(DB::Raw("CONCAT(venta.serie,'-',venta.numero)"),'LIKE', $descripcion.'%');
					break;
    		}
    	}

    	if ($tipodocumento != '' && $tipodocumento != 'todo') {
    		$ventas = $ventas->where('venta.tipocomprobante','=',$tipodocumento);
    	}

    	$ventas =  $ventas->select('venta.id','venta.total',DB::Raw("CONCAT(venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as documento"), DB::raw("(CASE WHEN venta.tipoComprobante='F' THEN 'FACTURA' ELSE (CASE WHEN venta.tipoComprobante = 'B' THEN 'BOLETA' ELSE (CASE WHEN venta.tipoComprobante = 'NC' THEN 'NOTA DE CREDITO' ELSE 'NOTA DE DEBITO' END) END) END) as tipoComprobante"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"), DB::Raw("DATE_FORMAT(venta.fecha,'%d/%m/%Y') as fecha"))
		   ->orderBy('venta.fecha','ASC');

		$lista = $ventas->get();
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
			$arrPag = [['opc' => '1']];
			$page = '1';
			$inicio = '1';
			$fin = '1';
	        $paramInicio = '1';
            $paramFin = '1';
		}
		
		$lista = $ventas->offset(($page-1)*$filas)
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


		
    	return ['ventas' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Comprobante':' Comprobantes'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 


	public function guardarCliente(Request $request) {
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				$documento = $request->get('documento');
				$p = Persona::where('documento','=',$documento)->first();
				if (is_null($p)) {
					$persona = new Persona;
				} else {
					$persona = $p;
				}
				$persona->documento = $documento;
				$persona->nombres  = $request->get('nombres');
				$persona->apellidos = $request->get('apellidos');
				$persona->razonSocial = $request->get('razonsocial');
				$persona->tipoPersona = 'C';
				$persona->tipoDocumento = (strlen($documento)==8 || strlen($documento)==9?'PN':'PJ');
				$persona->direccion	= $request->get('direccion');
				$persona->correoElectronico	= $request->get('correo');
				$persona->telefono	= $request->get('telefono');
				
				if (is_null($p)) {
					$persona->save();
					$cad = ' Registrado';
				} else {
					$persona->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Cliente'.$cad.' Correctamente';
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}
   
	public function getCliente($documento,$tipodocumento, Request $request) {
        $cliente = Persona::where('tipoPersona','=','C')
            ->where('documento','=',$documento);
        
        if ($tipodocumento == 'B') {
            $cliente = $cliente->where('tipoDocumento','=','PN');
        }else {
            $cliente = $cliente->where('tipoDocumento','=','PJ');
        }
        $cliente = $cliente->first();
        
        if (!is_null($cliente)) {
            $data=['cliente'=>$cliente, 'estado' => true];
        } else {
			$data=['cliente'=>null, 'estado' => false];
        }

        return $data;
	}
	 
	public function getCliente02($documento, Request $request) {
        $cliente = Persona::where('tipoPersona','=','C') 
            ->where('documento','=',$documento);
        
        $cliente = $cliente->first();
        
        if (!is_null($cliente)) {
            $data=['cliente'=>$cliente, 'estado' => true];
        } else {
			$data=['cliente'=>null, 'estado' => false];
        }

        return $data;
	}
 	public function getClienteCita($id){
		$cliente=Persona::find($id);
		return $cliente;
	 }
	public function getReniec($documento){
        $cs = new Dni(new ContextClient(), new DniParser());
		try{
            $person = $cs->get($documento);
            if (!$person) {
                $person = ['apellidoPaterno' => '','apellidoMaterno'=>'','nombres'=>''];
            }
        }catch(\Exception $e){
            $person = ['apellidoPaterno' => '','apellidoMaterno'=>'','nombres'=>''];
        }

        return $person;
	}

	public function getSunat ($documento){
    //   $ruc = $request->get('ruc');
      
      /*$cs = new Ruc(new ContextClient(), new RucParser(new HtmlParser()));
      try{
        $company = $cs->get($ruc);
        if (!$company) {
            $company = ['razonSocial' => '','telefonos'=>[null],'direccion'=>''];
        }
      } catch(\Exception $e){
            $company = ['razonSocial' => '','telefonos'=>[null],'direccion'=>''];
      }
      
      return $company;*/
      
      // ************************************************************************/
      //                            VERSION ANTERIOR --
      // ************************************************************************/

      // https://api.sunat.cloud/ruc/'.$ruc
	  // $consulta = file_get_contents('http://intranet.fasteinvoice.com/buscaCliente/BuscaEmpresa.php?fe=N&ruc='.$ruc.'&token=3mXL%25^%3D}%23b%24B%2FTHq');
	  
	   // PROBLEMAS CON SSL
	    $arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  
      $consulta = file_get_contents('https://api.sunat.cloud/ruc/'.$documento, false, stream_context_create($arrContextOptions));
    
      $info = json_decode($consulta,true);
    
      return $info;
      // ************************************************************************/
      
      /*  if ($consulta === '[]' || $info['Ruc'] == null) {
        $datos = array(0 => 'nada');
      }else{
        $datos = array('ruc' => $info['Ruc'], 'razon_social' => $info['RazonSocial'], 'domicilio' => $info['Direccion']);
        // $datos = json_encode($info);
      }
      // $datos = array( 0 => $dni, 1 => $partes[0], 2 => $partes[1], 3 => $partes[2]);

      return $datos;*/
      
      
      // return $consulta;
      // require_once( __DIR__ . "/src/autoload.php" );
      // $company = new \Sunat\Sunat( true, true );
      
      // $ruc = "20169004359";
      // $dni = "72537981";
      
      // $search1 = $company->search( $ruc );
      // $search2 = $company->search( $dni );

      // return $search2->json('callback');
      
    }


	#CLIENTES
	public function getClienteDocumento($documento, Request $request) {
		$cliente = Persona::where('tipoPersona','=','C')
			->where('documento','=',$documento)
			->first();
        
        if (!is_null($cliente)) {
            $data=['cliente'=>$cliente, 'estado' => true];
        } else {
			// try {
				$cliente = null;
			// 	if (strlen($documento) == 8) {
			// 		$resultado = $this->getReniec($documento);
			// 		$cliente = [
			// 			'nombres' => $resultado->nombres,
			// 			'apellidos' => $resultado->apellidoPaterno.' '.$resultado->apellidoMaterno
			// 		];
			// 	} else {
			// 		$resultado = $this->getSunat($documento);
			// 		$cliente = [
			// 			'razonSocial' => $resultado['razon_social'],
			// 			'direccion'	  => $resultado['domicilio_fiscal']
			// 		];
			// 		// dd($resultado);
			// 	}
			// } catch (\Exception $ex) {
			// 	$cliente = null;
			// }

            $data=['cliente'=>$cliente, 'estado' =>(!is_null($cliente)?true:false)];
        }

        return $data;
	}


	#PROVEEDORES
	public function getProveedorDocumento($documento, Request $request) {
		$cliente = Persona::where('tipoPersona','=','P')
			->where('documento','=',$documento)
			->first();
        
        if (!is_null($cliente)) {
            $data=['cliente'=>$cliente, 'estado' => true];
        } else {
			// try {
				$cliente = null;
			// 	if (strlen($documento) == 8) {
			// 		$resultado = $this->getReniec($documento);
			// 		$cliente = [
			// 			'nombres' => $resultado->nombres,
			// 			'apellidos' => $resultado->apellidoPaterno.' '.$resultado->apellidoMaterno
			// 		];
			// 	} else {
			// 		$resultado = $this->getSunat($documento);
			// 		$cliente = [
			// 			'razonSocial' => $resultado['razon_social'],
			// 			'direccion'	  => $resultado['domicilio_fiscal']
			// 		];
			// 		// dd($resultado);
			// 	}
			// } catch (\Exception $ex) {
			// 	$cliente = null;
			// }

            $data=['cliente'=>$cliente, 'estado' =>(!is_null($cliente)?true:false)];
        }

        return $data;
	}


	public function guardarProveedor(Request $request) {
		$errors = $this->validar($request, true);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				$documento = $request->get('documento');
				$p = Persona::where('documento','=',$documento)->first();
				if (is_null($p)) {
					$persona = new Persona;
				} else {
					$persona = $p;
				}
				$persona->documento = $documento;
				$persona->nombres  = $request->get('nombres');
				$persona->apellidos = $request->get('apellidos');
				$persona->razonSocial = $request->get('razonsocial');
				$persona->tipoPersona = 'P';
				$persona->tipoDocumento = (strlen($documento)==8?'PN':'PJ');
				$persona->direccion	= $request->get('direccion');
				$persona->correoElectronico	= $request->get('correo');
				$persona->telefono	= $request->get('telefono');
				$persona->tipoProveedor = $request->get('tipo_proveedor');
				if (is_null($p)) {
					$persona->save();
					$cad = ' Registrado';
				} else {
					$persona->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Proveedor'.$cad.' Correctamente';
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}

	public function getProveedor($documento, Request $request) {
        $proveedor = Persona::where('tipoPersona','=','P')
           		 	 ->where('documento','=',$documento);
        
        $proveedor = $proveedor->first();
        
        if (!is_null($proveedor)) {
            $data=['proveedor'=>$proveedor, 'estado' => true];
        } else {
			$data=['proveedor'=>null, 'estado' => false];
        }

        return $data;
	}

	public function validar (Request $request, $flag = false) {
		$reglas = [
            'documento'   => 'required|min:8|max:12',
            'apellidos'	  => 'max:255|'.(strlen($request->get('documento'))==8?'required':'nullable'),
			'nombres'	  => 'max:255|'.(strlen($request->get('documento'))==8?'required':'nullable'),
            'razonsocial' => 'max:255|'.(strlen($request->get('documento'))==11?'required':'nullable'),
			'direccion'   => 'required|max:255',
			'telefono'    => 'nullable|digits_between:6,9',
			'correo'      => 'nullable|max:255',
			'tipo_proveedor' => ($flag?'required|max:1': 'nullable')
        ];

        $mensajes = [
			'documento.required'	=> 'Indique Documento',
			'apellidos.required'	=> 'Indique Apellidos',
			'apellidos.max'	=> 'Apellidos debe tener como máximo 255 caracteres',
			'nombres.required'	=> 'Indique Nombres',
			'nombres.max'	=> 'Nombres debe tener como máximo 255 caracteres',
			'razonsocial.required'	=> 'Indique Razón Social',
			'razonsocial.max'	=> 'Razón Social debe tener como máximo 255 caracteres',
			'direccion.required'	=> 'Indique Dirección',
			'direccion.max'	=> 'Dirección debe tener como máximo 255 caracteres',
			'telefono.digits_between'	=> 'Teléfono Debe Tener 06 o 09 Caracteres Numéricos',
			'correo.max'	=> 'Correo Electrónico debe tener como máximo 255 caracteres',
			'tipo_proveedor.required' => 'Indique Tipo Proveedor',
			'tipo_proveedor.max' => 'Tipo Proveedor debe tener como máximo 01 caracter'
	    ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}
}
