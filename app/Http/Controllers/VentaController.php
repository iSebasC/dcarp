<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Forma;
use App\Models\CategoriaProducto;
use App\Models\Acabado;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\UnidadMedida;
use App\Models\Local;
use App\Models\StockProducto;
use App\Models\StockAuto;
use App\Models\StockProductoDetalleSalida;
use App\Models\StockProductoDetalle;

use App\Models\Producto;
use App\Models\Persona;
use App\Models\Serie;
use App\Models\MovimientoCaja;
use App\Models\Cotizacion;
use App\Models\Anulacion;
use App\Models\AnulacionNotas;
use App\Models\DetalleCotizacion;
use App\Models\DetalleOrdenTrabajo;

use App\Models\OrdenTrabajo;
use App\Models\PagoDetalle;
use App\Models\SerieDocumento;
use App\Models\TipoCambio;
use App\Models\DetalleHomologacion;
use App\Models\Cuenta;
use App\Libraries\Funciones;

use DB;
use Validator;
use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;


class VentaController extends Controller
{
    public $servicioAnticipo = 'ANTICIPO'; 
	public $almacenId = 2;
	public $tiendaId  = 1;
	public $idUsuarioCajaMaestra = 21;

	public $user_wsdl = '20103327378';
	public $pass_wsdl = 'bj1R8xkhHB';
    // public $ruta      = "/home/eduiyvoou2zd/public_html/carpio.ayluby.com/xml/docs/";
    public $ruta      = "/";
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

	#INI CUENTAS POR COBRAR
	public function getPersonas(Request $request) {
        $tipo = $request->get('tipo');
        $busqueda = $request->get('query');

        // if ($tipo == 'V') {
        // } else {
        //     $personas = DB::table('persona')->where('tipoPersona','=','P');       
        // }

        $personas = DB::table('persona')
                    ->select(
                        DB::Raw("CONCAT(documento,' - ', (CASE WHEN tipoDocumento = 'PN' THEN CONCAT(apellidos, ' ',nombres) ELSE razonSocial END)) as persona"),
                    'id')
					->where('tipoPersona', $tipo)
                    ->where(DB::Raw("CONCAT(documento,' - ', (CASE WHEN tipoDocumento = 'PN' THEN CONCAT(apellidos, ' ',nombres) ELSE razonSocial END))"), 
                            'LIKE','%'.$busqueda.'%')
                    ->get();

        return ['personas' => $personas];
    }

	public function getCuentasContables (Request $request) {
		$busqueda = $request->get('query');
		$cuentas  = DB::table('cuentacontable as c')
					->whereNull('c.deleted_at')
					->where(DB::Raw("CONCAT(c.codigo,' ', c.nombre)"), 'LIKE', '%'.$busqueda.'%')
					->select('c.id', DB::Raw("CONCAT(c.codigo,' ', c.nombre) as cuenta"))
					->get();
		return ['cuentascontables' => $cuentas];
	}


	public function getCuentas (Request $request) {
		$tipo = $request->get('tipo');
        $busqueda = $request->get('query');
		$ref = $request->get('ref');
        $cuentas = DB::table('cuenta as c')
                    ->select('c.*', DB::Raw("CONCAT((CASE c.tipodocumento WHEN 'B' THEN 'Boleta' WHEN 'F' THEN 'Factura' WHEN 'RXH' THEN 'RRxHH' 
					WHEN 'NC' THEN 'Nota de Crédito' WHEN 'ND' THEN 'Nota de Débito' ELSE 'Otros' END), ' ', c.serie,'-',c.numero) as cuenta"))
					->where('c.tipocuenta', $tipo)
					->whereNull('c.deleted_at')
					->where('c.estado', 'P');

		if ($tipo == '1') {
			$cuentas =  $cuentas->where('c.idCliente', $ref);
		} else {
			$cuentas =  $cuentas->where('c.idProveedor', $ref);	
		}
					
        $cuentas = $cuentas->where(DB::Raw("CONCAT((CASE c.tipodocumento WHEN 'B' THEN 'Boleta' WHEN 'F' THEN 'Factura' WHEN 'RXH' THEN 'RRxHH' 
						WHEN 'NC' THEN 'Nota de Crédito' WHEN 'ND' THEN 'Nota de Débito' ELSE 'Otros' END), ' ', c.serie,'-',c.numero)"), 'LIKE','%'.$busqueda.'%')
                    ->get();
		// dd($cuentas);
        return ['cuentas' => $cuentas];
	}

	public function validarCuenta(Request $request) {
		$reglas = [
            'id'=>  'required',
            'tipo_cuenta'=> 'required',
			'codcliente'=> ($request->get('tipo_cuenta') == '1'?'required|numeric|min:1': 'nullable'),
			'codproveedor'=> ($request->get('tipo_cuenta') == '2'?'required|numeric|min:1': 'nullable'),
			'tipo'=> ($request->get('tipo_cuenta') == '2'?'required': 'nullable'),
			'unidad'=> ($request->get('tipo_cuenta') == '2'?'required': 'nullable'),
			'tipogasto' => ($request->get('tipo_cuenta') == '2' && $request->get('tipo') == 'G'?'required': 'nullable'),
			'tipo_documento' => 'required',
			'serie' => 'required',
			'numero' => 'required',
			// 'almacenId' => 'required',
			'fecha_vencimiento'=> 'required',
            'importe'=>  'required|numeric',
			'moneda' => 'required|max:3',
            'operacion'      => 'required|max:1',
            'fecha_emision'=> 'required',
			'sustento'    => 'required',
        ];

        $mensajes = [
            'id.required'=> 'Indique ID',
			'tipo_cuenta.required' => 'Indique Tipo de Cuenta',
            'codcliente.required'=> 'Indique Cliente',
			'codcliente.min' => 'Cliente no válido',
            'codproveedor.required'=> 'Indique Proveedor',
			'codproveedor.min' => 'Proveedor no válido',
            'tipo_documento.required'=> 'Indique Tipo de Documento',
            'serie.required'=> 'Indique Serie',
			'numero.required' => 'Indique Número',
            'fecha_vencimiento.required'=> 'Indique Fecha de Vencimiento',
			'importe.required' => 'Indique Importe',
			'moneda.required'  => 'Indique Moneda',
			'moneda.max'	=> 'Moneda debe tener 03 caracteres exactamente',
			'tipo.required' => 'Indique Tipo',
			'unidad.required' => 'Indique Unidad',
			'tipogasto.required' => 'Indique Tipo Gasto',
		    'operacion.required' => 'Indique Operacion',
			'operacion.max' => 'Operación debe tener como máximo 01 caracter',
		    'fecha_emision.required'=> 'Indique Fecha de Emisión',
			'sustento.required' => 'Indique Sustento',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function guardarCuenta(Request $request) {
		$errors = $this->validarCuenta($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			// dd($request);
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				################################
				# VALIDACION DE CUENTAS UNICAS
				################################
				$esRegistroValido = DB::table('cuenta');
				if($request->get('tipo_cuenta') == '1') {
					$esRegistroValido = $esRegistroValido->where('tipocuenta', '1')
								->where('idCliente', $request->get('codcliente'));
				} else {
					$esRegistroValido = $esRegistroValido->where('tipocuenta', '2')
								->where('idProveedor', $request->get('codproveedor'));
				}

				$esRegistroValido = $esRegistroValido->where('tipodocumento', $request->get('tipo_documento'))
									->where('serie', $request->get('serie'))
									->where('numero', $request->get('numero'))
									->whereNull('deleted_at')
									->first();

				if (is_null($esRegistroValido)) {
					$cuenta = new Cuenta;
					$cuenta->tipocuenta = $request->get('tipo_cuenta');
					
					if ($cuenta->tipocuenta == '1') {
						$cuenta->idCliente  = $request->get('codcliente');
					} else {
						$cuenta->idProveedor = $request->get('codproveedor');
						$cuenta->unidad		 = $request->get('unidad');
						$cuenta->tipo      = $request->get('tipo');
						$cuenta->partida   = $request->get('partida');
						$cuenta->tipogasto   = $request->get('tipogasto');
					}

					$cuenta->tipodocumento = $request->get('tipo_documento');
					$cuenta->serie 		 = $request->get('serie');
					$cuenta->numero		 = $request->get('numero');
					$cuenta->fechavencimiento = $request->get('fecha_vencimiento');
					$cuenta->fechaemision = $request->get('fecha_emision');
					$cuenta->importe	 = $request->get('importe');
					$cuenta->moneda		 = $request->get('moneda');
					$cuenta->saldo       = $cuenta->importe;

					$pCambio = 	TipoCambio::find(1);
					if ($cuenta->moneda == 'USD') {
						$cuenta->tipoCambio =  $pCambio->factorVenta;
						$cuenta->importeSoles = $cuenta->tipoCambio * $cuenta->importe;
					} else {
						$cuenta->tipoCambio =  1; // $pCambio->factorVenta;	
						$cuenta->importeSoles = $cuenta->importe;
					}
					
					$cuenta->operacion   = $request->get('operacion');
					$cuenta->sustento    = $request->get('sustento');
					$cuenta->idPersonal  = Auth::user()->usuarioId;
					$cuenta->save();

					if ($band) {
						$errors[] = 'Cuenta Registrada Correctamente';
					}
				} else {
					$band = false;	
					$errors[] = 'Cuenta Registrada Anteriormente';
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
	
	public function eliminarCuenta ($id, Request $request) {
		DB::beginTransaction();
		$errors = '';
		$band = true;
		try{
			$cuenta = Cuenta::find($id);
			if (!is_null($cuenta)) {
				if ($cuenta->estado == 'P') {
					$cuenta->idPersonalEliminar = Auth::user()->usuarioId;
					$cuenta->update();
					$cuenta->delete();	
				} else {
					$errors = ['No es posible eliminar Cuenta porque está Cancelada'];
					$band = false;	
				}	
			} else {
				$errors = ['Cuenta no Encontrada, ya antes Eliminada'];
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

	public function getAllCuentas (Request $request) {
		$tipocuenta		= $request->get('tipocuenta');
		$documento 		= $request->get('documento');
		$razonSocial 	= $request->get('razonSocial');
		$trabajador 	= $request->get('registradoPor');
		$serie 			= $request->get('serie');
		$numero 		= $request->get('numero');
		$moneda 		= $request->get('moneda');
		$fechaV 		= $request->get('fechaV');
		$estado 		= $request->get('situacion');
		$operacion 			= $request->get('operacion');
		
		// $tipodocumento = $request->get('tipodocumento');

		// 'documento': documento,
		// 'razonSocial': razonSocial,
		// 'registradoPor': registradoPor,
		// 'serie': serie,
		// 'numero': numero,
		// 'moneda': moneda,
		// 'fechaV': fechaV,
		// 'fechaI': fechaI,
		// 'fechaF': fechaF,
		// 'situacion': situacion,
		// 'operacion': operacion,


		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$cuentasC = DB::table('cuenta as c')
				  ->join('persona as cl','cl.id','=','c.idCliente')
				  ->join('trabajador as t','t.id','=', 'c.idPersonal')
				  ->where('c.tipocuenta', $tipocuenta)
				  ->where('cl.documento', 'LIKE', '%'. $documento. '%')
				  ->where(function ($qq) use ($razonSocial) {
						$qq->where('cl.razonSocial','LIKE', $razonSocial.'%')
							->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $razonSocial. '%');
				   })
				  ->where(DB::Raw("CONCAT(t.apellidos, ' ', t.nombres)"), 'LIKE', '%'. $trabajador. '%')
				  ->where('c.serie','LIKE', '%'. $serie. '%')
				  ->where('c.numero','LIKE', '%'. $numero. '%');
				
		$cuentasP  = DB::table('cuenta as c')
					->join('persona as cl','cl.id','=','c.idProveedor')
					->join('trabajador as t','t.id','=', 'c.idPersonal')
					->where('c.tipocuenta', $tipocuenta)
					->where('cl.documento', 'LIKE', '%'. $documento. '%')
					->where(function ($qq) use ($razonSocial) {
						$qq->where('cl.razonSocial','LIKE', $razonSocial.'%')
							->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $razonSocial. '%');
					})
					->where(DB::Raw("CONCAT(t.apellidos, ' ', t.nombres)"), 'LIKE', '%'. $trabajador. '%')
					->where('c.serie','LIKE', '%'. $serie. '%')
					->where('c.numero','LIKE', '%'. $numero. '%');
				
		if ($fechaI != '') {
			$cuentasC = $cuentasC->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=',$fechaI);
			$cuentasP = $cuentasP->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$cuentasC = $cuentasC->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=',$fechaF);
			$cuentasP = $cuentasP->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=',$fechaF);
		}

		if ($fechaV != '') {
			$cuentasC = $cuentasC->where(DB::Raw("DATE_FORMAT(c.fechavencimiento, '%Y-%m-%d')"), $fechaV);
			$cuentasP = $cuentasP->where(DB::Raw("DATE_FORMAT(c.fechavencimiento, '%Y-%m-%d')"), $fechaV);
		}

    	if ($moneda != '' && $moneda != 'todo') {
			$cuentasC = $cuentasC->where('c.moneda', $moneda);
			$cuentasP = $cuentasP->where('c.moneda', $moneda);	
		}
	
		if ($operacion != '' && $operacion != 'todo') {
			$cuentasC = $cuentasC->where('c.operacion', $operacion);
			$cuentasP = $cuentasP->where('c.operacion', $operacion);	
		}

		if ($estado != '' && $estado != 'todo') {
			$cuentasC = $cuentasC->where('c.estado', $estado);
			$cuentasP = $cuentasP->where('c.estado', $estado);	
		}
		
		$cuentasC = $cuentasC->select('c.*', DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos, ' ', cl.nombres) ELSE cl.razonSocial END) cliente"), 
						'cl.documento', DB::Raw("DATE_FORMAT(c.created_at, '%d/%m/%Y') fechaRegistro"), DB::Raw("DATE_FORMAT(c.fechavencimiento, '%d/%m/%Y') fechaVenc"),
						DB::Raw("CONCAT(t.apellidos,' ', t.nombres) as trabajador"), DB::Raw("(CASE WHEN c.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
						DB::Raw("ROUND(c.importe,2) importeR"));
		
		$cuentasP =  $cuentasP->select('c.*', DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos, ' ', cl.nombres) ELSE cl.razonSocial END) cliente"), 
							'cl.documento', DB::Raw("DATE_FORMAT(c.created_at, '%d/%m/%Y') fechaRegistro"),  DB::Raw("DATE_FORMAT(c.fechavencimiento, '%d/%m/%Y') fechaVenc"),
							DB::Raw("CONCAT(t.apellidos,' ', t.nombres) as trabajador"), DB::Raw("(CASE WHEN c.deleted_at IS NULL THEN 'N' ELSE 'S' END) as eliminado"),
							DB::Raw("ROUND(c.importe,2) importeR"))
		->unionAll($cuentasC)
		->orderBy('created_at','ASC');
		
		$lista = $cuentasP->get();
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
		
		$lista = $cuentasP->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();
		
    	return ['cuentas' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Cuenta':' Cuentas'), 'page' => $page, 
				'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];

	}

	#FIN CUENTAS POR COBRAR
	
    public function getAll (Request $request) {
    	$cliente 	 	= $request->get('cliente');
		$comprobante 	= $request->get('comprobante');
		$documento 		= $request->get('documento');
		$registrado_por = $request->get('registradopor');
		$tipodocumento = $request->get('tipodocumento');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$ventas = DB::table('venta')->leftjoin('persona as cl','cl.id','=','venta.idCliente')
				  ->leftJoin('anulacion as a','a.idVenta','=','venta.id')
				  ->whereNotNull('venta.tipoComprobante')
				  ->where('cl.documento','LIKE','%'.$documento.'%')
				  ->where(function ($qq) use ($cliente) {
					 $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
					 	->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente. '%');
				  })
				  ->where(DB::Raw("CONCAT(venta.serie,'-',venta.numero)"),'LIKE', '%'.$comprobante.'%');
		
		$notas  = DB::table('anulacionnotas as an')
				  ->leftjoin('persona as cl','cl.id','=','an.idCliente')
				  ->leftjoin('venta as v','v.id','=','an.idVenta')
				  ->leftJoin('anulacion as b','b.idVenta','=','an.id')
				  ->where('an.tipoComprobante','V')
				  ->where('cl.documento','LIKE','%'.$documento.'%')
				  ->where(function ($qq) use ($cliente) {
					$qq->where('cl.razonSocial','LIKE', $cliente.'%')
						->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $cliente. '%');
				  })
				  ->where(DB::Raw("CONCAT(an.serie,'-',an.numero)"),'LIKE', '%'.$comprobante.'%');

		if ($fechaI != '') {
			$ventas = $ventas->where('venta.fecha','>=',$fechaI);
			$notas  = $notas->where(DB::Raw("DATE_FORMAT(an.created_at,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$ventas = $ventas->where('venta.fecha','<=',$fechaF);
			$notas  = $notas->where(DB::Raw("DATE_FORMAT(an.created_at,'%Y-%m-%d')"),'<=',$fechaF);
		}


    	if ($tipodocumento != '' && $tipodocumento != 'todo') {
			$ventas = $ventas->where('venta.tipocomprobante','=',$tipodocumento);
			$notas = $notas->where('an.tipoDocumento','=',$tipodocumento);	
		}
		
		$notas = $notas->select('an.id',DB::Raw("NULL as situacionSUNAT"),'v.total', 'cl.documento as doc_cliente', DB::Raw("CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento"), DB::raw("(CASE WHEN an.tipoDocumento='F' THEN 'FACTURA' ELSE (CASE WHEN an.tipoDocumento = 'B' THEN 'BOLETA' ELSE (CASE WHEN an.tipoDocumento = 'NC' THEN 'NOTA DE CREDITO' ELSE 'NOTA DE DEBITO' END) END) END) as tipoComprobante"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"), DB::Raw("DATE_FORMAT(an.created_at,'%d/%m/%Y') as fecha"),'an.idDocumentoSUNAT','an.situacion','an.nombreDocumentoSUNAT','b.nombreDocumentoSUNAT as documentoSUNAT02','an.created_at');
		
		$ventas =  $ventas->select('venta.id','venta.situacionSUNAT','venta.total','cl.documento as doc_cliente', DB::Raw("CONCAT(venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as documento"), DB::raw("(CASE WHEN venta.tipoComprobante='F' THEN 'FACTURA' ELSE (CASE WHEN venta.tipoComprobante = 'B' THEN 'BOLETA' ELSE (CASE WHEN venta.tipoComprobante = 'NC' THEN 'NOTA DE CREDITO' ELSE 'NOTA DE DEBITO' END) END) END) as tipoComprobante"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"), DB::Raw("DATE_FORMAT(venta.fecha,'%d/%m/%Y') as fecha"),'venta.idDocumentoSUNAT','venta.situacion','venta.nombreDocumentoSUNAT','a.nombreDocumentoSUNAT as documentoSUNAT02','venta.created_at')
		->unionAll($notas)
		->orderBy('created_at','ASC');
		

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
			$arrPag = [['opc' => '1', 'bloqueado' => true]];
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

	public function excel (Request $request) {
		$cliente 	 	= $request->get('cliente');
		$comprobante 	= $request->get('comprobante');
		$documento 		= $request->get('documento');
		$registrado_por = $request->get('registradopor');
		$tipodocumento = $request->get('tipodocumento');
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 = $request->get('fechaF');

		
		$ventas = DB::table('venta')->leftjoin('persona as cl','cl.id','=','venta.idCliente')
				  ->leftJoin('anulacion as a','a.idVenta','=','venta.id')
				  ->whereNotNull('venta.tipoComprobante')
				  ->where('cl.documento','LIKE','%'.$documento.'%')
				  ->where(function ($qq) use ($cliente) {
					 $qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
					 	->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente. '%');
				  })
				  ->where(DB::Raw("CONCAT(venta.serie,'-',venta.numero)"),'LIKE', '%'.$comprobante.'%');
		
		$notas  = DB::table('anulacionnotas as an')
				  ->leftjoin('persona as cl','cl.id','=','an.idCliente')
				  ->leftjoin('venta as v','v.id','=','an.idVenta')
				  ->leftJoin('anulacion as b','b.idVenta','=','an.id')
				  ->whereNotNull('an.tipoComprobante')
				  ->where('cl.documento','LIKE','%'.$documento.'%')
				  ->where(function ($qq) use ($cliente) {
					$qq->where('cl.razonSocial','LIKE', $cliente.'%')
						->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $cliente. '%');
				  })
				  ->where(DB::Raw("CONCAT(an.serie,'-',an.numero)"),'LIKE', '%'.$comprobante.'%');

		if ($fechaI != '') {
			$ventas = $ventas->where('venta.fecha','>=',$fechaI);
			$notas  = $notas->where(DB::Raw("DATE_FORMAT(an.created_at,'%Y-%m-%d')"),'>=',$fechaI);
		}

		if ($fechaF != '') {
			$ventas = $ventas->where('venta.fecha','<=',$fechaF);
			$notas  = $notas->where(DB::Raw("DATE_FORMAT(an.created_at,'%Y-%m-%d')"),'<=',$fechaF);
		}


    	if ($tipodocumento != '' && $tipodocumento != 'todo') {
			$ventas = $ventas->where('venta.tipocomprobante','=',$tipodocumento);
			$notas = $notas->where('an.tipoDocumento','=',$tipodocumento);	
		}
		
		$notas = $notas->select('an.id','v.total',DB::Raw("CONCAT(v.tipoComprobante, an.tipoComprobante, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento"), DB::raw("(CASE WHEN an.tipoDocumento='F' THEN 'FACTURA' ELSE (CASE WHEN an.tipoDocumento = 'B' THEN 'BOLETA' ELSE (CASE WHEN an.tipoDocumento = 'NC' THEN 'NOTA DE CREDITO' ELSE 'NOTA DE DEBITO' END) END) END) as tipoComprobante"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"), DB::Raw("DATE_FORMAT(an.created_at,'%d/%m/%Y') as fecha"),'an.idDocumentoSUNAT','an.situacion','an.nombreDocumentoSUNAT','b.nombreDocumentoSUNAT as documentoSUNAT02','an.created_at');
		


		$ventas =  $ventas->select('venta.id','venta.total',DB::Raw("CONCAT(venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as documento"), DB::raw("(CASE WHEN venta.tipoComprobante='F' THEN 'FACTURA' ELSE (CASE WHEN venta.tipoComprobante = 'B' THEN 'BOLETA' ELSE (CASE WHEN venta.tipoComprobante = 'NC' THEN 'NOTA DE CREDITO' ELSE 'NOTA DE DEBITO' END) END) END) as tipoComprobante"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"), DB::Raw("DATE_FORMAT(venta.fecha,'%d/%m/%Y') as fecha"),'venta.idDocumentoSUNAT','venta.situacion','venta.nombreDocumentoSUNAT','a.nombreDocumentoSUNAT as documentoSUNAT02','venta.created_at')
		->unionAll($notas);
		
		$lista = $ventas->orderBy('created_at')->get();

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Ventas");
		$hoja1->setCellValue('A1','LISTADO DE VENTAS');
		$hoja1->mergeCells('A1:G1');
		$hoja1->getStyle('A1:G1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','#');
		$hoja1->setCellValue('B2','FECHA');
		$hoja1->setCellValue('C2','CLIENTE');
		$hoja1->setCellValue('D2','T. DE COMPROBANTE');
		$hoja1->setCellValue('E2','COMPROBANTE');
		$hoja1->setCellValue('F2','SITUACIÓN');
		$hoja1->setCellValue('G2','TOTAL');
	
		$hoja1->getStyle('A2:G2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		$cont = 1;
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$cont);
			$hoja1->setCellValue('B'.$j,$value->fecha);
			$hoja1->setCellValue('C'.$j,$value->cliente);
			$hoja1->setCellValue('D'.$j,$value->tipoComprobante);
			$hoja1->setCellValue('E'.$j,$value->documento);
			$hoja1->setCellValue('F'.$j,$value->situacion=='V'?'VIGENTE':($value->situacion=='NC'?'NOTA DE CREDITO':($value->situacion == 'A'?'ANULADO':'NOTA DE DEBITO')));
			$hoja1->setCellValue('G'.$j,number_format($value->total,2,'.',' '));
		
			$hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
			$hoja1->getStyle('G'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
		
			$cont++;
			$j++;
		}

		foreach(range('A','G') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ventas.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

    public function getFormas (Request $request) {
    	$formas = Forma::all();
  		return ['formas' => $formas];
    }

    public function getTipos (Request $request) {
    	$categorias = CategoriaProducto::all();

    	return ['tipos' => $categorias];
    }

    public function getDetalles ($id, Request $request) {
		$detalles = DB::table('venta')
					->leftJoin('detalleventa as det','det.idVenta','=','venta.id')
					->where('venta.id','=',$id)
					->select('det.cantidad','det.descripcion','det.precio','det.subTotal',
							'det.item','det.id','venta.total')
					->orderBy('det.id','ASC')
					->get();

		$total = 0;
		if (count($detalles)) {
			$total = $detalles[0]->total;
		}

    	return ['detalles' => $detalles,'total' => $total];
	}
	
	public function getUnidadMedidas (Request $request) {
    	$unidades = UnidadMedida::all();

    	return ['unidades' => $unidades];
	}
	
	public function getProducto($id, Request $request) {
		$prod = Producto::leftJoin('unidadmedida as ud','ud.id','producto.idUnidadMedida')
				->where('producto.id','=',$id)
				->select('producto.*','ud.nombre as unidadMedida')
				->first();
    
    	if (!is_null($prod)) {
    		$respuesta = ['estado' => true, 'producto' => $prod];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
	}

	public function getSeriesDocumento($tipo_doc) {
		$series = SerieDocumento::where('tipo_documento','=',$tipo_doc)
				// ->where('serie','!=','16')
				->select(DB::Raw("CONCAT(LPAD(serie,3,'0'),'-',descripcion) as serie"),'id')
				->get();

		return ['series' => $series];
	}

	public function getSeriesDocumentoAuto ($tipo_doc) {
		$series = SerieDocumento::where('tipo_documento','=',$tipo_doc)
				  ->whereIn('serie',['16','17'])
				  ->select(DB::Raw("CONCAT(LPAD(serie,3,'0'),'-',descripcion) as serie"),'id')
				  ->get();

		return ['series' => $series];
	}
	
    
  public function guardarVenta(Request $request)
  {
    $isAperturadoCaja = $request->get('chkcaja');
    $errors = $this->validar($request);
    $id = 0;
    if (count($errors) > 0) {
      return ['errores' => $errors, 'estado' => false];
    } else {
      $id = $request->get('id');
      $band = true;
      $errors = [];
      DB::beginTransaction();
      try {

        $idUsuarioCaja = Auth::user()->usuarioId;
        if ($isAperturadoCaja == true) {
          $idUsuarioCaja = $this->idUsuarioCajaMaestra;
        }

        $caja =  MovimientoCaja::where('idUsuario', '=', $idUsuarioCaja)
          ->whereNull('saldoCierre')
          ->orderBy('fecha', 'DESC')
          ->first();

        if (!is_null($caja)) {
          $venta = new Venta;
          $venta->fecha = $request->get('fecha');
          $venta->subTotal = $request->get('subtotalDoc');
          $venta->igv = $request->get('igvDoc');
          $venta->total = $request->get('totalDoc');
          $venta->idTienda = $this->tiendaId;
          $venta->idAlmacenSalida = $this->almacenId;
          $venta->tipoComprobante = $request->get('tipodocumento');
          $venta->tipoPago = $request->get('tipopago');
          $venta->metodoPago = $request->get('formapago');
          $venta->igv_sunat = $request->get('tipoIgvVenta');
          $venta->tipoMoneda = $request->get('monedaVenta');
          if ($venta->tipoMoneda == 'USD') {
            $pCambio =  TipoCambio::find(1);
            $venta->tipoCambio =  $pCambio->factorCompra;
          }

          $venta->observaciones = $request->get('observaciones');
          $fecha_v = null;
          if ($venta->tipoPago == 'D') {
            if (!is_null($request->get('nrodias'))) {
              $dias = (int) $request->get('nrodias');
              $fecha_v = date('Y-m-d', strtotime(date('Y-m-d') . "+ $dias days"));
            }
          } else {
            if (!is_null($request->get('nrodias'))) {
              $dias = (int) $request->get('nrodias');
              $fecha_v = date('Y-m-d', strtotime(date('Y-m-d') . "+ $dias days"));
            }
          }
          $venta->fechaVencimiento = $fecha_v;
          $cliente = Persona::where('documento', '=', $request->get('documento'))
            ->where('tipoPersona', '=', 'C')
            ->where('tipoDocumento', '=', ($request->get('tipodocumento') == 'B' ? 'PN' : 'PJ'))
            ->select('id')
            ->first();
          $venta->idCliente = $cliente->id;

          $serie = SerieDocumento::where('tienda_id', '=', $venta->idTienda)
            ->where('id', '=', $request->get('serie'))
            ->where('tipo_documento', '=', $request->get('tipodocumento'))
            ->first();

          $venta->serie = $serie->serie;
          $venta->numero = $serie->numero + 1;
          $venta->idPersonal = Auth::user()->usuarioId;
          $venta->idMovimiento  = $caja->id;
          $venta->semanaActual = date('W');

          $valid_venta = DB::table('venta')->where('serie', '=', $venta->serie)
            ->where('numero', '=', $venta->numero)
            ->where('tipoComprobante', '=', $venta->tipoComprobante)
            ->first();

          if (is_null($valid_venta)) {
            $venta->save();
            $id = $venta->id;
            $anticipos = [];

            $serie->numero = $venta->numero;
            $serie->update();

            $detalles = explode(',', $request->get('listDetalles'));

            $i = 1;
            foreach ($detalles as $det) {
              if ($request->get('productoid' . $det) == $this->servicioAnticipo) {
                $monto = floatval($request->get('txtprecio' . $det));
                $documento =  $request->get('txtproducto' . $det);
                if ($monto < 0) {
                  $monto *= -1;
                }

                if (!in_array($documento[0], ['B', 'F'])) {
                  $band = false;
                  $errors[] = 'El documento de Anticipo no presenta serie completa con B/F';
                  break;
                }

                $anticipos[] = ['monto' => $monto, 'documento' => $documento, 'tipo' => $documento[0]];

                

              } else {
                $id_rel = $request->get('enlace' . $det);
                $id_ref_detalle = $request->get('detalle_ref' . $det);
                $detalle = new DetalleVenta;
                $detalle->item = $i;
                $detalle->descripcion = $request->get('txtproducto' . $det);
                $detalle->cantidad = $request->get('txtcantidad' . $det);
                $detalle->precio = $request->get('txtprecio' . $det);
                $detalle->subTotal = $request->get('txtsubtototal' . $det);
                $detalle->idProducto = ($request->get('tipo' . $det) == 'Producto' ? $request->get('productoid' . $det) : null);
                $detalle->idServicio = ($request->get('tipo' . $det) == 'Servicio' ? $request->get('productoid' . $det) : null);
                $detalle->idVenta = $id;

                if (in_array($request->get('tipoVenta'), ['P', 'C'])) {
                  if (!is_null($detalle->idProducto)) {
                    $detalle->idLote = ($request->get('lote' . $det) != '0' ? $request->get('lote' . $det) : null);
                  }
                  $detalle->save();
                  $validDets = 0;
                  if ($request->get('tipoVenta') == 'C') {
                    $validDets = DetalleOrdenTrabajo::where('idCotizacion', $id_rel)
                      ->count();
                  }
                  // foreach ($detprods as $dp) {
                  # QUITAR PARA LOS DETALLES DE PRECIOS
                  if ($validDets == 0 && $request->get('tipo' . $det) == 'Producto') {
                    if ($request->get('lote' . $det) != '0') {
                      $s = StockProductoDetalle::where('idProducto', $detalle->idProducto)
                        ->where('idTienda', $this->tiendaId)
                        ->where('idAlmacenSalida', $this->almacenId)
                        ->where('idLote', $request->get('lote' . $det))
                        ->first();
                      $acumDism = $detalle->cantidad;

                      if (!is_null($s)) {
                        if ($acumDism <= $s->stock) {
                          $s->stock = $s->stock - $acumDism;
                          $s->update();

                          $spds = new StockProductoDetalleSalida;
                          $spds->idStockProductoDetalle = $s->id;
                          $spds->idProducto = $s->idProducto;
                          $spds->idAlmacen = $s->idAlmacenSalida;
                          $spds->idVenta = $venta->id;
                          $spds->stock = $acumDism;
                          $spds->save();

                          #QUITAR AL STOCK GENERAL
                          $stg = StockProducto::where('idProducto', '=', $detalle->idProducto)
                            ->where('idAlmacen', '=', $this->almacenId)
                            ->first();

                          if (!is_null($stg)) {
                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                              $stg->totalVentas = $stg->totalVentas + $detalle->cantidad;
                              if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                                $stg->update();
                              } else {
                                $band = false;
                                $errors[] = "Producto $detalle->descripcion no cuenta con stock, faltan: $acumDism";
                                break;
                              }
                            } else {
                              $band = false;
                              $errors[] = "Producto $detalle->descripcion no cuenta con stock, faltan: $acumDism";
                              break;
                            }
                          }
                        } else {
                          $band = false;
                          $errors[] = "Producto $detalle->descripcion no cuenta con stock actual: $acumDism";
                          break;
                        }
                      } else {
                        $s = StockProductoDetalle::where('idProducto', '=', $detalle->idProducto)
                          ->where('idTienda', '=', $this->tiendaId)
                          ->where('idAlmacenSalida', '=', $this->almacenId)
                          ->where('stock', '>', 0)
                          ->orderBy('created_at', 'ASC')
                          ->select('stock', 'id')
                          ->get();

                        $acumDism = $detalle->cantidad;
                        $aux = 0;
                        foreach ($s as $sd) {
                          if ($acumDism > 0) {
                            $a = StockProductoDetalle::find($sd->id);
                            if ($acumDism > $a->stock) {
                              $aux = $a->stock;
                              $acumDism -= $a->stock;
                              $a->stock = 0;
                            } else {
                              $aux = $acumDism;
                              $a->stock = $a->stock - $acumDism;
                              $acumDism = 0;
                            }

                            $a->update();
                            $spds = new StockProductoDetalleSalida;
                            $spds->idStockProductoDetalle = $a->id;
                            $spds->idProducto = $a->idProducto;
                            $spds->idAlmacen = $a->idAlmacenSalida;
                            $spds->idVenta = $venta->id;
                            $spds->stock = $aux;
                            $spds->save();
                          }
                        }

                        if ($acumDism > 0) {
                          $band = false;
                          $errors[] = "Producto $detalle->descripcion no cuenta con stock, faltan: $acumDism productos";
                          break;
                        }
                        #QUITAR AL STOCK GENERAL
                        $stg = StockProducto::where('idProducto', '=', $detalle->idProducto)
                          ->where('idAlmacen', '=', $this->almacenId)
                          ->first();

                        if (!is_null($stg)) {
                          if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) > 0) {
                            $stg->totalVentas = $stg->totalVentas + $detalle->cantidad;
                            if (bcsub(bcsub($stg->totalCompras, $stg->totalVentas, 3), $stg->totalIncidencias, 3) >= 0) {
                              $stg->update();
                            } else {
                              $band = false;
                              $errors[] = "Producto $detalle->descripcion no cuenta con stock, faltan: $acumDism productos";
                              break;
                            }
                          } else {
                            $band = false;
                            $errors[] = "Producto $detalle->descripcion no cuenta con stock, faltan: $acumDism productos";
                            break;
                          }
                        }
                      }
                    }
                  }
                } else {
                  $detalle->save();
                }

                $pago = new PagoDetalle;
                $pago->idDetalleVenta = $detalle->id;
                $pago->idVenta = $id;
                $pago->idCotizacion = $request->get('tipoVenta') == 'C' ? $id_rel : null;
                $pago->idOrden = $request->get('tipoVenta') == 'O' ? $id_rel : null;
                $pago->totalPago = $detalle->subTotal;
                $pago->save();

                # GUARDAR HOMOLOGADOS
                if ($id_ref_detalle != '') {
                  $dHom = new DetalleHomologacion;
                  $dHom->idDetalleVenta = $detalle->id;
                  $dHom->idDetalleCotizacion = $id_ref_detalle;
                  $dHom->save();
                }
                $i++;
              }
            }

            $pagos = PagoDetalle::where('idVenta', '=', $id)
              ->where(function ($qq) {
                $qq->whereNotNull('idCotizacion')
                  ->orWhereNotNull('idOrden');
              })
              ->select(DB::Raw("SUM(totalPago) as totalPagado"), 'idOrden', 'idCotizacion')
              ->groupBy('idOrden', 'idCotizacion')
              ->get();

            foreach ($pagos as $pg) {
              if (!is_null($pg->idOrden)) {
                $orden = OrdenTrabajo::find($pg->idOrden);
                if (!is_null($orden)) {
                  if ($orden->total == $pg->totalPagado) {
                    $orden->situacionFacturado = 'P';
                    $orden->update();
                  }
                }
              } else {
                $cotizacion = Cotizacion::find($pg->idCotizacion);
                if (!is_null($cotizacion)) {
                  if ($cotizacion->total == $pg->totalPagado) {
                    $cotizacion->situacionFacturado = 'P';
                    $cotizacion->update();
                  }
                }
              }
            }

            #INI PARA OBTENER PLACA
            $pg = PagoDetalle::where('idVenta', $id)
              ->where(function ($qq) {
                $qq->whereNotNull('idCotizacion')
                  ->orWhereNotNull('idOrden');
              })
              ->first();

            $orden = null;
            $cotizacion = null;
            if (!is_null($pg)) {
              if (!is_null($pg->idOrden)) {
                $orden = DB::table('ordentrabajo')
                  ->where('id', $pg->idOrden)
                  ->select('placa')
                  ->first();
              } elseif (!is_null($pg->idCotizacion)) {
                $cotizacion = DB::table('cotizacion')
                  ->where('id', $pg->idCotizacion)
                  ->select('placa')
                  ->first();
              }
            }
            if (!is_null($orden) || !is_null($cotizacion)) {
              if (!is_null($orden)) {
                $venta->placa = $orden->placa;
              }

              if (!is_null($cotizacion)) {
                $venta->placa = $cotizacion->placa;
              }

              $venta->update();
            }
            #FIN PARA OBTENER PLACA


            if ($venta->tipoComprobante == 'B') {
              $this->declararBoleta($this->user_wsdl, $this->pass_wsdl, $venta);
            } else {
              $this->declararFactura($this->user_wsdl, $this->pass_wsdl, $venta, $anticipos);
            }
            $id = $venta->idDocumentoSUNAT;

            # INI ACTUALIZACION POR ANTICIPOS
            /*if (count($anticipos) > 0) {
                            foreach($anticipos as $anticipo) {
                                $venta->total = number_format($venta->total - $anticipo['monto'], 2, '.', '');
                                
                                if ($venta->igv > 0) {
                                    $venta->subTotal =  number_format($venta->total/1.18, 2, '.', '');
                                    $venta->igv = number_format($venta->total - $venta->subTotal, 2, '.', '');
                                } else {
                                    $venta->subTotal = $venta->total;
                                }
                                $venta->update();
                            }
                        }*/

            # FIN

            #INI GENERAR CUENTA POR PAGAR

            if (($venta->tipoPago == 'D' && $venta->tipoComprobante == 'F') ||
              ($venta->metodoPago == 'Tarjeta' && $venta->tipoComprobante == 'B')
            ) {

              $cuenta = new Cuenta;
              $cuenta->tipocuenta = '1';

              if ($cuenta->tipocuenta == '1') {
                $cuenta->idCliente  = $venta->idCliente;
              }

              $serie = $venta->tipoComprobante . str_pad($venta->serie, 3, '0', STR_PAD_LEFT);
              $numero = str_pad($venta->numero, 8, '0', STR_PAD_LEFT);

              $cuenta->tipodocumento = $venta->tipoComprobante;
              $cuenta->serie          = $serie;
              $cuenta->numero         = $numero;
              $cuenta->fechavencimiento = $venta->fechaVencimiento;
              $cuenta->fechaemision   = $venta->fecha;
              $cuenta->importe        = $venta->total;
              $cuenta->moneda         = $venta->tipoMoneda;
              $cuenta->saldo          = $cuenta->importe;
              $pCambio =  TipoCambio::find(1);

              if ($cuenta->moneda == 'USD') {
                $cuenta->tipoCambio =  $pCambio->factorVenta;
                $cuenta->importeSoles = $cuenta->tipoCambio * $cuenta->importe;
              } else {
                $cuenta->tipoCambio =  1;
                $cuenta->importeSoles = $cuenta->importe;
              }

              $cuenta->operacion   = 'D';
              $cuenta->idPersonal  = Auth::user()->usuarioId;
              $cuenta->save();
            }

            #FIN
            if ($band) {
              $errors[] = 'Venta Registrada Correctamente';
            }
          } else {
            $band = false;
            $errors[] = 'Venta No se Puede Registrar, Correlativo no Disponible';
          }
        } else {
          $band = false;
          $errors[] = 'Es necesario Aperturar Caja para realizar una Venta';
        }
      } catch (\Exception $ex) {
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


	public function guardarVentaAuto(Request $request) {
		$isAperturadoCaja = $request->get('chkcaja');
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
				if($request->get('asesor') == '') {
					$band = false;
					$errors[] = 'Indique Asesor';
				}

				$idUsuarioCaja = Auth::user()->usuarioId;
				if ($isAperturadoCaja == true) {
					$idUsuarioCaja = $this->idUsuarioCajaMaestra;
				}

				$caja =  MovimientoCaja::where('idUsuario','=',$idUsuarioCaja)
						->whereNull('saldoCierre')
						->orderBy('fecha','DESC')
						->first();

				if (!is_null($caja) && $band) {
					$venta = new Venta;
					$venta->fecha = $request->get('fecha');
					$venta->subTotal = $request->get('subtotalDoc');
					$venta->igv = $request->get('igvDoc');
					$venta->total = $request->get('totalDoc');
					$venta->idTienda = $this->tiendaId;
					$venta->idAlmacenSalida = $this->almacenId;
					$venta->tipoComprobante = $request->get('tipodocumento');
					$venta->tipoPago = $request->get('tipopago');
					$venta->metodoPago = $request->get('formapago');
					$venta->igv_sunat = $request->get('tipoIgvVenta');
					$venta->tipoMoneda = $request->get('monedaVenta');
					$venta->asesorAuto = $request->get('asesor');

					if ($venta->tipoMoneda == 'USD') {
						$pCambio = 	TipoCambio::find(1);
						$venta->tipoCambio =  $pCambio->factorCompra;
					}
					
					$venta->observaciones = $request->get('observaciones');
					$fecha_v = null;
					if ($venta->tipoPago == 'D') {
						if (!is_null($request->get('nrodias'))) {
							$dias = (int) $request->get('nrodias');
							$fecha_v = date('Y-m-d',strtotime(date('Y-m-d'). "+ $dias days"));
						}
					} else {
						if (!is_null($request->get('nrodias'))) {
							$dias = (int) $request->get('nrodias');
							$fecha_v = date('Y-m-d',strtotime(date('Y-m-d'). "+ $dias days"));
						}
					}
					
					$venta->fechaVencimiento = $fecha_v; 
					$cliente = Persona::where('documento','=',$request->get('documento'))
								->where('tipoPersona','=','C')
								->where('tipoDocumento','=',($request->get('tipodocumento') == 'B'?'PN':'PJ'))
								->select('id')
								->first();
					$venta->idCliente = $cliente->id;

	                $serie = SerieDocumento::where('tienda_id','=',$venta->idTienda)
							->where('id','=',$request->get('serie'))
							->where('tipo_documento','=',$request->get('tipodocumento'))
							->first();

					$venta->serie = $serie->serie;
					$venta->numero = $serie->numero + 1;
					$venta->idPersonal = Auth::user()->usuarioId;
					$venta->idMovimiento  = $caja->id;
					$venta->semanaActual = date('W');
		
					$valid_venta = DB::table('venta')->where('serie','=',$venta->serie)
						->where('numero','=',$venta->numero)
						->where('tipoComprobante','=',$venta->tipoComprobante)
						->first();
					
					if (is_null($valid_venta)) {
						$venta->save();
						$id = $venta->id;
					
						$serie->numero = $venta->numero;
						$serie->update();

						$detalles = explode(',',$request->get('listDetalles'));
							
						$i = 1;
						foreach ($detalles as $det) {
							$id_rel = $request->get('enlace'.$det);
							$detalle = new DetalleVenta;
							$detalle->item = $i;
							$detalle->descripcion = $request->get('txtproducto'.$det);
							$detalle->cantidad = $request->get('txtcantidad'.$det);
							$detalle->precio = $request->get('txtprecio'.$det);
							$detalle->subTotal = $request->get('txtsubtototal'.$det);
							$detalle->idAuto = ($request->get('tipo'.$det) == 'Auto'?$request->get('productoid'.$det):null);
							// $detalle->idServicio = null;
							// $detalle->idProducto = null;
							$detalle->idVenta = $id;
						
							if (in_array($request->get('tipoVenta'),['A'])) {
								if (!is_null($detalle->idAuto)) {
									$detalle->idLoteAuto = $request->get('lote'.$det);
								}
								$detalle->save();
								// foreach ($detprods as $dp) {
								# QUITAR PARA LOS DETALLES DE PRECIOS
								if ($request->get('tipo'.$det) == 'Auto') {
									if ( $request->get('lote'.$det) != '0') {
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
												$spds->idVenta = $venta->id;
												$spds->stock = $acumDism;
												$spds->save();

												#QUITAR AL STOCK GENERAL
												$stg = StockAuto::where('idAuto','=',$detalle->idAuto)
														->where('idAlmacen','=',$this->almacenId)
														->first();

												if (!is_null($stg)) {
													$stg->totalVentas = $stg->totalVentas + $detalle->cantidad;
													$stg->update();	
												}
											} else {
												$band = false;
												$errors[] = "Auto $detalle->descripcion no cuenta con stock: $acumDism";
												break;
											}
										}
									} 
								}
							} else {
								$detalle->save();
							}

							$pago = new PagoDetalle;
							$pago->idDetalleVenta = $detalle->id;
							$pago->idVenta = $id;
							$pago->idCotizacion = null;
							$pago->idOrden = null;
							$pago->totalPago = $detalle->subTotal;
							$pago->save();

							$i++;

						}

						if ($venta->tipoComprobante == 'B') {
							$this->declararBoletaAuto($this->user_wsdl,$this->pass_wsdl,$venta);
						} else {
							$this->declararFacturaAuto($this->user_wsdl,$this->pass_wsdl,$venta);
						}
						$id = $venta->idDocumentoSUNAT;
						
						#INI GENERAR CUENTA POR PAGAR
						if (($venta->tipoPago == 'D' && $venta->tipoComprobante == 'F') || 
							($venta->metodoPago == 'Tarjeta' && $venta->tipoComprobante == 'B')) {
							$cuenta = new Cuenta;
							$cuenta->tipocuenta = '1';

							if ($cuenta->tipocuenta == '1') {
								$cuenta->idCliente  = $venta->idCliente;
							} else {
								// $cuenta->idProveedor = $venta->idProveedor;
								// $cuenta->unidad		 = $venta->unidad;
								// $cuenta->tipo      = 'C';
								// $cuenta->partida   = '';
								// $cuenta->tipogasto   = 'C';
							}

							$serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
							$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);
                            
							$cuenta->tipodocumento = $venta->tipoComprobante;
							$cuenta->serie 		 = $serie;
							$cuenta->numero		 = $numero;
							$cuenta->fechavencimiento = $venta->fechaVencimiento;
							$cuenta->fechaemision= $venta->fecha;
							$cuenta->importe	 = $venta->total;
							$cuenta->moneda		 = $venta->tipoMoneda;
							$cuenta->saldo       = $cuenta->importe;

							$pCambio = 	TipoCambio::find(1);
							if ($cuenta->moneda == 'USD') {
								$cuenta->tipoCambio =  $pCambio->factorVenta;
								$cuenta->importeSoles = $cuenta->tipoCambio * $cuenta->importe;
							} else {
								$cuenta->tipoCambio =  1; // $pCambio->factorVenta;	
								$cuenta->importeSoles = $cuenta->importe;
							}

							$cuenta->operacion   = 'D';
							// $cuenta->sustento    = $request->get('sustento');
							$cuenta->idPersonal  = Auth::user()->usuarioId;
							$cuenta->save();
						}

						#FIN


						$errors[] = 'Venta Registrada Correctamente';
					} else {
						$band = false;
                		$errors[] = 'Venta No se Puede Registrar, Correlativo no Disponible';
                	}
				} else {
					$band = false;
					$errors[] = 'Es necesario Aperturar Caja para realizar una Venta';
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

	public function getSearchOrdenCot (Request $request) {
		$busqueda = $request->get('busqueda');
		$orden = DB::table('ordentrabajo as ot')
				->leftjoin('persona as cl', 'cl.id','=','ot.idCliente')
				->where('ot.situacionFacturado', 'N')
				->where('ot.situacion', 'F')
				->whereNull('ot.deleted_at')
				->where(function ($qq) use ($busqueda) {
					$qq->where(DB::Raw("CONCAT('O', ot.serie,'-', ot.numero)"),'LIKE', '%'.$busqueda.'%')
						->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ',cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$busqueda.'%')
						->orWhere('cl.documento','LIKE','%'.$busqueda.'%');
				})
				->select(DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),
				DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ',cl.nombres) ELSE cl.razonSocial END) as cliente"),
				DB::Raw("ROUND(ot.total,2) as total"),
				DB::Raw("'ORDEN DE TRABAJO' as tipo"),
				DB::Raw("'O' as tipo2"),
				'cl.documento',
				DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as numero"),
					'ot.id'
				);

		$cotizacion = DB::table('cotizacion as c')
				->leftjoin('persona as cl', 'cl.id','=','c.idCliente')
				->where('c.situacionFacturado', 'N')
				->where('c.situacion', 'V')
				->whereNull('c.deleted_at')
				->where(function ($qq) use ($busqueda) {
					$qq->where(DB::Raw("CONCAT('C', c.serie,'-', c.numero)"),'LIKE', '%'.$busqueda.'%')
						->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ',cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$busqueda.'%')
						->orWhere('cl.documento','LIKE','%'.$busqueda.'%');
					})
				->select(DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha"),
				DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ',cl.nombres) ELSE cl.razonSocial END) as cliente"),
				DB::Raw("ROUND(c.total,2) as total"),
				DB::Raw("'COTIZACIÓN' as tipo"),
				DB::Raw("'C' as tipo2"),
				'cl.documento',
				DB::Raw("CONCAT('C', LPAD(c.serie,3,'0') ,'-', LPAD(c.numero,8,'0')) as numero"),
					'c.id'
				)->unionAll($orden)
				->orderBy('fecha','DESC')
				->get();

		return ['lista' => $cotizacion, 'estado' => true];

	}

	public function actualizarOrdenCot (Request $request) {
		$band = true;
		$errors = [];
		DB::beginTransaction();
		try{
			$detalles = json_decode($request->get('detalles'), true);
			$asignado = $request->get('asignadoA');

			if ($asignado != null && $asignado != '0') {
				foreach($detalles as $det) {
					if ($det['tipo2'] == 'C') {
						$valid = DB::table('cotizacion')
								->where('id',$det['id'])
								->where('situacion','V')
								->where('situacionFacturado','N')
								->first();
						if (!is_null($valid)) {
							DB::table('cotizacion')
							->where('id',$det['id'])
							->where('situacion','V')
							->where('situacionFacturado','N')
							->update(['idCliente' => $asignado]);
						} else {
							$band = false;
							$errors[] = "Cotización ". $det['numero']. ' no disponible para su actualización.';
							break;
						}
					} elseif($det['tipo2'] == 'O') {
						$valid = OrdenTrabajo::where('id',$det['id'])
								->where('situacion','F')
								->where('situacionFacturado','N')
								->first();
						if (!is_null($valid)) {
							$cotRels = DB::table('detalleordentrabajo as dot')
								->join('cotizacion as cot','cot.id','=','dot.idCotizacion')
								->where('dot.idOrdenTrabajo', $det['id'])
								->where('cot.situacion','U')
								// ->where('cot.situacionFacturado','N')
								->select('cot.id')
								->get();	
							foreach($cotRels as $ct) {
								Cotizacion::where('id',$ct->id)
								->update(['idCliente' => $asignado]); 
							}

							DB::table('cita')
							->where('id', $valid->idCita)
							->update(['idCliente' => $asignado]);

							DB::table('ordentrabajo')
							->where('id', $det['id'])
							->update(['idCliente' => $asignado]);

							$errors[] = "Proceso realizado con Exito";
						} else {
							$band = false;
							$errors[] = "Orden de Trabajo ". $det['numero']. ' no disponible para su actualización.';
							break;
						}
					} else {
						$band = false;
						$errors[] = "Actualización no Permitida";
					}
				}
			} else {
				$band = false;
				$errors[] = "Cliente a Asignar no Especificado";
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

	public function validar (Request $request) {
		$reglas = [
            'fecha'=>  'required',
            'tipodocumento'=> 'required',
			'documento'=> 'required|numeric|digits_between:8,11',
			'tipopago' => 'required',
			// 'asesor' => 'required',
			// 'tiendaId' => 'required',
			// 'almacenId' => 'required',
			'formapago'=> 'required',
            'listDetalles'=>  'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric',
			'chk_declarar' => 'nullable',
			'serie'		  => 'required',
			'monedaVenta' => 'required',
			'nrodias'	  => ($request->get('tipopago') == 'D'?'required':'nullable')
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
			'tipopago.required' => 'Indique Tipo de Pago',
            'tipodocumento.max'=> 'Indique Tipo de Documento',
            'documento.required'=> 'Indique Documento',
            // 'asesor.required'=> 'Indique Asesor',
            // 'almacenId.required'=> 'Indique Almacén',
            'tipopago.required'=> 'Indique Método de Pago',
			'formapago.required'=> 'Indique Forma de Pago',
		    'listDetalles.required'=> 'Indique Detalles a Venta',
			'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
			'serie.required'	  => 'Indique Serie',
			'monedaVenta.required'=> 'Indique Moneda',
			'nrodias.required'	  => 'Indique Nro de Días'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarAnulacion (Request $request) {
		$reglas = [
            'tipo'=>  'required',
            'motivo'=> ($request->get('tipo')!='1'?'required':'nullable'),
            'id'=> 'required'
		];

        $mensajes = [
            'tipo.required'=> 'Indique Tipo de Anulación',
			'motivo.required'=> 'Indique Motivo',
			'id.required'=> 'Documento no Encontrado',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function getReporte ($tipo, Request $request) {
		if ($tipo == 'Mis Ventas Diarias') {
			$ff = date('d/m/Y');
			$fi = date('Y-m-d', strtotime('-6 days'));
			$band = 0;
		} elseif ($tipo == 'Mis Ventas Por Semana') {
			$ff = date('d/m/Y',strtotime('monday -1 week'));
			$fi = date('Y-m-d', strtotime('monday -7 weeks'));
			$band = 1;
		} else {
			$ff = date('d/m/Y');
			$fi = date('d/m/Y', strtotime('-6 months'));
			$band = 2;
		}
	
		$labels = [];
		$contador = 0; 
		
		if ($band == 2) {
			$fInit = \DateTime::createFromFormat('d/m/Y', $fi)->format('Y-m-d');
		}

		do{
			if ($band == 0) {
				$fa = date('d/m/Y',strtotime("$fi + $contador days"));
				$labels[] = $fa;
			} elseif ($band == 1) {
				$fa = date('d/m/Y',strtotime("$fi + $contador weeks"));
				$labels[] = $fa;
			} else {
				if ($contador != 0) {
					$fa = date('d/m/Y', strtotime("$fInit + $contador months"));
					$labels[] = $this->returnMes($fa);
				} else {
					$labels[] = $this->returnMes($fi);
				}
			}
			$contador++;
		} while(!(count($labels) == 7));


		$ffF = \DateTime::createFromFormat('d/m/Y', $ff)->format('Y-m-d');
		if ($band == 2) {
			$fiF = \DateTime::createFromFormat('d/m/Y', $fi)->format('Y-m-d');
			$ventas = Venta::whereBetween(DB::Raw("DATE_FORMAT(fecha,'%Y-%m-%d')"),[$fiF,$ffF]);
		} elseif($band == 1) {
			$ventas = Venta::where(DB::Raw("DATE_FORMAT(fecha,'%Y-%m-%d')"),'>=',$fi)
					  ->orWhere(DB::Raw("DATE_FORMAT(fecha,'%Y-%m-%d')"),'>=',$ffF);
		} else {
			$ventas = Venta::whereBetween(DB::Raw("DATE_FORMAT(fecha,'%Y-%m-%d')"),[$fi,$ffF]);
		}
		if ($band == 0) {
			$ventas = $ventas->select(DB::Raw("SUM(total) as total"),
					  DB::Raw("DATE_FORMAT(created_at,'%d/%m/%Y') as fecha"))
					  ->groupBy('fecha')
					  ->orderBy('fecha','ASC')
					  ->get();
		} elseif($band == 1) {
			$ventas = $ventas->select(DB::Raw("SUM(total) as total"),
					DB::Raw("CONCAT(semanaActual,'-',YEAR(created_at)) as fecha"))
					->groupBy('fecha')
					->orderBy('fecha','ASC')
					->get();
		} else {
			$ventas = $ventas->select(DB::Raw("SUM(total) as total"),
					DB::Raw("DATE_FORMAT(created_at,'%m-%Y') as fecha"))
					->groupBy('fecha')
					->orderBy('fecha','ASC')
					->get();
		
		}
		$datos = [];
		foreach ($labels as $lb) {
			$acum = 0;
			foreach ($ventas as $v) {
				if ($band == 2) {
					if ($v->fecha == $this->returnMesNum($lb)) {
						$acum+=$v->total;
					}
				} elseif ($band == 1) {
					$const = explode('-',$v->fecha);
					$a = \DateTime::createFromFormat('d/m/Y', $lb)->format('Y-m-d');
					$c2 = date('W',strtotime($a));
					if ($const[0] == $c2) {
						$acum+=$v->total;
					}
				} else {
					if ($v->fecha == $lb) {
						$acum+=$v->total;
					}
				}
			}

			// if (!$bandE) {
				$datos[] = $acum;
			// }
		}
	
		return ['labels' => $labels, 'datos' => $datos];

	}

	public function returnMes($mes) {
		$arrM = explode('-',$mes); // d/m/Y -> Y-m-d
		$m1 = $arrM[1];
		$a1 = $arrM[0];
		$cad = '';
		switch ($m1) {
			case '01':
				$cad = 'Enero - '.$a1;
				break;
			case '02':
				$cad = 'Febrero - '.$a1;
				break;
			case '03':
				$cad = 'Marzo - '.$a1;
				break;
			case '04':
				$cad = 'Abril - '.$a1;
				break;
			case '05':
				$cad = 'Mayo - '.$a1;
				break;
			case '06':
				$cad = 'Junio - '.$a1;
				break;
			case '07':
				$cad = 'Julio - '.$a1;
				break;
			case '08':
				$cad = 'Agosto - '.$a1;
				break;
			case '09':
				$cad = 'Setiembre - '.$a1;
				break;
			case '10':
				$cad = 'Octubre - '.$a1;
				break;
			case '11':
				$cad = 'Noviembre - '.$a1;
				break;	
			default:
				$cad = 'Diciembre - '.$a1;
				break;
		}

		return $cad;
	}

	public function returnMesNum($mes) {
		$arrM = explode('-',$mes);
		$m1 = trim($arrM[0]);
		$a1 = trim($arrM[1]);
		$cad = '';
		switch ($m1) {
			case 'Enero':
				$cad = '01';
				break;
			case 'Febrero':
				$cad = '02';
				break;
			case 'Marzo':
				$cad = '03';
				break;
			case 'Abril':
				$cad = '04';
				break;
			case 'Mayo':
				$cad = '05';
				break;
			case 'Junio':
				$cad = '06';
				break;
			case 'Julio':
				$cad = '07';
				break;
			case 'Agosto':
				$cad = '08';
				break;
			case 'Setiembre':
				$cad = '09';
				break;
			case 'Octubre':
				$cad = '10';
				break;
			case 'Noviembre':
				$cad = '11';
				break;	
			default:
				$cad = '12';
				break;
		}

		return $cad.'-'.$a1;
	}

	public function retornarStock($detalle, $venta) {
		$idAlmacen = $venta->idAlmacenSalida;
		$idTienda  = $venta->idTienda;
		$bandValid = false;
		if ($venta->serie != 16) {
			$idProducto = $detalle->idProducto;
			$detalleStock = DB::table('stockproductodetallesalida')
							->where('idVenta', $venta->id)
							->where('idAlmacen', $idAlmacen)
							->where('idProducto', $idProducto)
							->select('id','idStockProductoDetalle','stock')
							->get();
		} else {
			$idAuto = $detalle->idAuto;
			$detalleStock = DB::table('stockproductodetallesalida')
							->where('idVenta', $venta->id)
							->where('idAlmacen', $idAlmacen)
							->where('idAuto', $idAuto)
							->select('id','idStockProductoDetalle','stock')
							->get();
		}
	
		foreach($detalleStock as $dt) {
			DB::table('stockproductodetallesalida')
			->where('id', $dt->id)
			->update(['deleted_at'=> null]);

			$spds = StockProductoDetalleSalida::find($dt->id);
					
			if (!is_null($spds)) {
				$spd_v = DB::table('stockproductodetalle')
					->where('id',$dt->idStockProductoDetalle)
					->where('idTienda',$idTienda)
					->where('idAlmacenSalida',$idAlmacen)
					->first();
				if (!is_null($spd_v)) {
					DB::table('stockproductodetalle')
					->where('id',$spd_v->id)
					->update(['deleted_at' => null]);

					$spd = StockProductoDetalle::find($spd_v->id);
					$spd->stock = $spd->stock + $dt->stock;
					$spd->update();
					$bandValid = true;
				}
				$spds->stock = $spds->stock - $dt->stock; 
				$spds->update();
				$spds->delete();
			} else {
				$bandValid = false;
			}

			if (!$bandValid) {
				break;
			}
		}

		return $bandValid;
	}

	public function guardarAnulacion(Request $request) {
		$errors = $this->validarAnulacion($request);
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
			
				$venta = Venta::find($request->get('id'));
				if ($request->get('tipo') == 1) {
					$fecha_actual = date('Y-m-d');
					$fecha_comparar = date('Y-m-d',strtotime($fecha_actual."-7 days"));
					if($venta->fecha >= $fecha_comparar){
						if (!is_null($venta)) {
							$correlativo_actual = DB::table('anulacion')
												  ->where('serie','=',date('Ymd'))
												  ->max('numero');
							
							if (is_null($correlativo_actual)){
								$correlativo_actual = 1;
							}else{
								$correlativo_actual = (int)$correlativo_actual+1;
							}

							$correlativo_actual = 	str_pad($correlativo_actual,3,'0',STR_PAD_LEFT);
							$anulacion = new Anulacion;
							$anulacion->serie = date('Ymd');
							$anulacion->idVenta = $venta->id;
							$anulacion->numero = $correlativo_actual;
							$anulacion->tipoDocumentoAnulado = $venta->tipoComprobante;
							$anulacion->idPersonal = $id;
							// dd($venta->tipo_comprobante);
							if ($venta->tipoComprobante == 'B') {
								$rpta = $this->anularBoleta($this->user_wsdl,$this->pass_wsdl,$venta,$anulacion);
							}else{
								$rpta = $this->anularFactura($this->user_wsdl,$this->pass_wsdl,$venta,$anulacion);
							}
							// $rpta['estado'] = true;
							if ($rpta['estado']) {
								$detalles = DetalleVenta::where('idVenta','=',$venta->id)->get();
								foreach ($detalles as $det) {
									$result = $this->retornarStock($det, $venta); 
									$d = DetalleVenta::find($det->id);	
									if ($result) {
										if ($venta->serie != 16) {
											$sp = StockProducto::where('idAlmacen','=',$venta->idAlmacenSalida)
													->where('idProducto','=',$d->idProducto)
													->first();
											if (!is_null($sp)) {
												$sp->totalVentas = $sp->totalVentas - $d->cantidad;
												$sp->update();
											}
										} else {
											$sp = StockAuto::where('idAlmacen','=',$venta->idAlmacenSalida)
													->where('idAuto','=',$d->idAuto)
													->first();
											if (!is_null($sp)) {
												$sp->totalVentas = $sp->totalVentas - $d->cantidad;
												$sp->update();
											}
										}

									}
									$d->delete();
								}
								$venta->situacion = 'A';
								$venta->update();
								$venta->delete();
								
								// ACTUALIZAR PAGOS DETALLE
								$pagos = PagoDetalle::where('idVenta','=',$venta->id)
								->where(function ($qq) {
									$qq->whereNotNull('idCotizacion')
										->orWhereNotNull('idOrden');
								})
								->get();

								foreach ($pagos as $pg) {
									if (!is_null($pg->idOrden)) {
										$orden = OrdenTrabajo::find($pg->idOrden);
										if (!is_null($orden)) {
											$orden->situacionFacturado = 'N';
											$orden->update();
										}
									} else {
										$cotizacion = Cotizacion::find($pg->idCotizacion);
										if (!is_null($cotizacion)) {
											$cotizacion->situacionFacturado = 'N';
											$cotizacion->update();
										}
									}
									$pg->delete();					
								}

								$errors[] = 'Comprobante Anulado Correctamente';
								$band = true;
							} else {
								$errors[] =  $rpta['errores'];//'No se Pudo Efectuar Anulación porque No se Encontró Comprobante en SUNAT';
								$band = false;
							}
						}
					}else{
						$errors[] = 'No se Pudo Efectuar Anulación porque sólo se PERMITE este Tipo de Anulación si el Comprobante tiene fecha de Emisión (MAX: 07 Días)';
						$band = false;
					}
				} elseif ($request->get('tipo') == 2) {
					$nota = new AnulacionNotas;
					$tipoNota = ($venta->tipoComprobante == 'B'?'BC':'FC');
					$serie = Serie::where('idLocal','=',$venta->idTienda)->where('tipoLocal','=','T')
							->where('tipoDocumento', $tipoNota)
							->first();
					$nota->serie = $serie->serie;
					$nota->numero = $serie->numero + 1;
					$nota->idPersonal = Auth::user()->usuarioId;
					$nota->idVenta  = $venta->id;
					$nota->idCliente = $venta->idCliente;
					$nota->tipoComprobante = 'C';
					$nota->tipoDocumento = 'NC';
			
					$rpta = $this->declararNotaCredito($this->user_wsdl,$this->pass_wsdl,$nota,$venta, $request->get('motivo'));

					if ($rpta) {
						$serie->numero = $nota->numero;
						$serie->update();
						$detalles = DetalleVenta::where('idVenta','=',$venta->id)->get();
						foreach ($detalles as $det) {
							$result = $this->retornarStock($det, $venta); 
							$d = DetalleVenta::find($det->id);	
							if ($result) {
								$sp = StockProducto::where('idAlmacen','=',$venta->idAlmacenSalida)
										->where('idProducto','=',$d->idProducto)
										->first();
								if (!is_null($sp)) {
									$sp->totalVentas = $sp->totalVentas - $d->cantidad;
									$sp->update();
								}	
							}
							$d->delete();
						}
						$venta->situacion = 'NC';
						$venta->update();
						$venta->delete();
						$errors[] = 'Nota de Crédito Generada Correctamente';
						$band = true;
					} else {
						$band = false;
						$errors[] = 'No se pudo Generar Nota de Crédito';
					}
				} else {
					$nota = new AnulacionNotas;
					$tipoNota = ($venta->tipoComprobante == 'B'?'BD':'FD');
					$serie = Serie::where('idLocal','=',$venta->idTienda)->where('tipoLocal','=','T')
							->where('tipoDocumento',$tipoNota)
							->first();
					$nota->serie = $serie->serie;
					$nota->numero = $serie->numero + 1;
					$nota->idPersonal = Auth::user()->usuarioId;
					$nota->idVenta  = $venta->id;
					$nota->idCliente = $venta->idCliente;
					$nota->tipoComprobante = 'D';
					$nota->tipoDocumento = 'ND';
			
					$rpta = $this->declararNotaDebito($this->user_wsdl,$this->pass_wsdl,$nota,$venta, $request->get('motivo'));

					if ($rpta) {
						$serie->numero = $nota->numero;
						$serie->update();
						$detalles = DetalleVenta::where('idVenta','=',$venta->id)->get();
						foreach ($detalles as $det) {
							$result = $this->retornarStock($det, $venta); 
							$d = DetalleVenta::find($det->id);	
							if ($result) {
								$sp = StockProducto::where('idAlmacen','=',$venta->idAlmacenSalida)
										->where('idProducto','=',$d->idProducto)
										->first();
								if (!is_null($sp)) {
									$sp->totalVentas = $sp->totalVentas - $d->cantidad;
									$sp->update();
								}
							}	
							$d->delete();
						}
						$venta->situacion = 'ND';
						$venta->update();
						$venta->delete();
						$errors[] = 'Nota de Débito Generada Correctamente';
						$band = true;
					} else {
						$band = false;
						$errors[] = 'No se pudo Generar Nota de Débito';
					}
				
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

	public function anularNota (Request $request) {
		$band = true;
		$errors = [];
		DB::beginTransaction();
		try{
		
			$id = Auth::user()->usuarioId;
			$an = AnulacionNotas::find($request->get('id'));
			$venta = DB::table('venta')->where('id','=',$an->idVenta)->first();
		
			$fecha_actual = date('Y-m-d');
			$fecha_comparar = date('Y-m-d',strtotime($fecha_actual."-7 days"));
			if(date('Y-m-d',strtotime($an->created_at)) >= $fecha_comparar){
				if (!is_null($an)) {
					$correlativo_actual = DB::table('anulacion')
											->where('serie','=',date('Ymd'))
											->max('numero');
					
					if (is_null($correlativo_actual)){
						$correlativo_actual = 1;
					}else{
						$correlativo_actual = (int)$correlativo_actual+1;
					}

					$correlativo_actual = 	str_pad($correlativo_actual,3,'0',STR_PAD_LEFT);
					$anulacion = new Anulacion;
					$anulacion->serie = date('Ymd');
					$anulacion->idVenta = $an->id;
					$anulacion->numero = $correlativo_actual;
					$anulacion->tipoDocumentoAnulado = $an->tipoDocumento;
					$anulacion->idPersonal = $id;
					//dd($venta->tipo_comprobante);
				}
			
				if ($an->tipoDocumento == 'NC' && $venta->tipoComprobante == 'B') {
					$rpta = $this->anularNotaBoleta($this->user_wsdl,$this->pass_wsdl,$an,$anulacion,'07');
				} elseif($an->tipoDocumento == 'ND' && $venta->tipoComprobante == 'B') {
					$rpta = $this->anularNotaBoleta($this->user_wsdl,$this->pass_wsdl,$an,$anulacion,'08');
				} elseif($an->tipoDocumento == 'NC' && $venta->tipoComprobante == 'F') {
					$rpta = $this->anularNotaFactura($this->user_wsdl,$this->pass_wsdl,$an,$anulacion,'07');
				}else {
					$rpta = $this->anularNotaFactura($this->user_wsdl,$this->pass_wsdl,$an,$anulacion,'08');
				}

				if ($rpta['estado']) {
					DB::table('venta')->where('id','=',$an->idVenta)->update(['deleted_at'=>NULL,'situacion' => 'V']);
					DB::table('detalleventa')->where('idVenta','=',$an->idVenta)->update(['deleted_at'=>NULL]);
					
					$venta = Venta::find($an->idVenta);
					$detalles = DetalleVenta::where('idVenta','=',$an->idVenta)->get();
					foreach ($detalles as $det) {
						$sp = StockProducto::where('idAlmacen','=',$venta->idAlmacenSalida)
								->where('idProducto','=',$det->idProducto)
								->first();
						$sp->totalVentas = $sp->totalVentas + $detalle->cantidad;
						$sp->update();
					}
					$errors[] = 'Documento Anulado con Éxito';
					$band = true;
				} else {
					$errors[] = $rpta['errores'];
					$band = false;
				}
			}
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
	
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];
		
	}

	public function declarar () {
		$venta = Venta::find(4);
		// dd($this->declararBoleta($this->user_wsdl,$this->pass_wsdl,$venta));
			
	}

	public function redeclarar ($id, Request $request) {
		$venta = Venta::find($id);
		if ($venta->tipoComprobante == 'B') {
			$this->declararBoleta($this->user_wsdl,$this->pass_wsdl,$venta);
		} else {
			$this->declararFactura($this->user_wsdl,$this->pass_wsdl,$venta);
		}
		dd("Ok proceso correctamente, Tipo: $venta->tipoComprobante, Serie: $venta->serie, Número: $venta->numero");
	}
	/************************************************ */
	/* 			FACTURACION ELECTRONICA
	/************************************************ */

	public function declararBoleta($user,$pass,$venta){
		// estructura del documento
		$numeroboleta = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
        $fechaemision = $venta->fecha; //'2019-04-03';//$request["fechaemision"];
        $horaemision = date('H:i:s', strtotime($venta->created_at));//'21:39:00';//$_POST["horaemision"];

		$serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		// ******************************************************************
		//  PARA EL CLIENTE 
		// ******************************************************************
		
		$cliente = Persona::where('id','=',$venta->idCliente)
				   ->where('tipoPersona','=','C')
				   ->where('tipoDocumento','=','PN')
				   ->first();
		
        $nombre = $cliente->apellidos.' '.$cliente->nombres; //'Cualquiera';//$_POST["nombre"];
        $tipodoc =  (strlen($cliente->documento)==8?'1':'4');//$_POST["tipodoc"]; // RUC: 6 DNI : 1
        $codUbigeo = '0000'; //$_POST["codUbigeo"]; si tengo sucursales, solicitar este codigo
        $dni = $cliente->documento; //'-';//$_POST["dni"];
        $moneda = $venta->tipoMoneda; //'PEN';//$_POST["moneda"];
        $direccion = $cliente->direccion; //'-';//$_POST["direccion"];
        $descuentototal = '0';//$_POST["descuentoglobal"];
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        
        $detalles = array();
        // ------------------------------------------------------------------------------------------------------
		$detalles_venta = DetalleVenta::where('idVenta','=',$venta->id)
						  ->get();

        foreach ($detalles_venta as $det) {
			if ($det->precio > 0) {
				$tipo_detalle = ($det->precio > 0?'V':'R');
				$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
				$descripcion = $det->descripcion;
				// $pr = Producto::findOrFail($det->idProducto);
				// $cat = CategoriaProducto::findOrFail($pr->idCategoriaProducto);
				/*' A-554 '*/
				// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
			
				$detalles[] = array(
					"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key], // tipo_detalle= Regalo
					"codigo"=> '-', //$pr->codRegistro, //'-', //$codigo[$key],
					"unidadmedida"=>'NIU',//$unidades[$key],
					"cantidad"=> $det->cantidad, //'3',//$cantidad[$key],
					"descripcion"=> $descripcion, //'Clavos',//$descripcion[$key],
					"precioventaunitarioxitem"=> $det->precio, //'0.5',//$precio[$key],
					"descuentoxitem"=>'0',//$descuentoxitem[$key],
					"tipoigv"=>$igv_sunat,//$tipoigv[$key], tipos de igv () -> declarando igv // cod 20: exonerado// cod: 30: inafecto
					"tasaisc"=>"0", //impuesto selectivo al consumo
					"aplicacionisc"=>"",
					"precioventasugeridoxitem"=>"",
				);
			}
		}

        $factura = array(
            "numeroboleta"=>$numeroboleta,
            "fechaemision"=>$fechaemision,
            "horaemision"=>$horaemision,
            "usuario"=>$nombre,
            "codubigeo"=>$codUbigeo,
            "tipodoc"=>$tipodoc,
            "dni"=>$dni,
            "moneda"=>$moneda,
            "descuentototal"=>$descuentototal,
            "percepcion"=>$percepcion,
            "aplicacionpercepcion"=>$aplicacionpercepcion,
            "documentosanexos"=>$documentosanexos,
			"obs_comprobante"=>$venta->observaciones,
			"forma_pago" => $venta->metodoPago,
			"direccion_cliente" => $direccion,
            "detalles"=>$detalles
        );

        // para el envio -- estructura anular directamente  -- 07 dias --
		$datos_enviar = array(
			"serieboleta"=>$serie,//'001',//$serie["numero_serie"],
			"correlativoboleta"=> $numero, //'00000001',//$_POST["numfac"],
			"doc"=>$dni,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total,//$_POST["totalDocumento"],
			"comprobante"=> json_encode($factura)
		);
		$datos_enviar = json_encode($datos_enviar);
		//dd($datos_enviar);
		//throw new Exception($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_boleta.php");
		//$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}

		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));
		//   error_log(json_encode($result));

		$result = json_decode($result);
		//dd($result);
		if($result->code=="0"){
		//   Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$venta->nombreDocumentoSUNAT = $nombre_documento;
			$venta->idDocumentoSUNAT = $id_solicitud;
			$venta->update();
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			// file_put_contents($filename_zip, $file_ZIP);
		}else{
			return ['errores' => $result->mensaje];
		}
	}

	public function declararFactura($user,$pass,$venta,$anticipos = []){
		// estructura del documento
		$cuotas_doc = [];
		if ($venta->tipoPago == 'D') {
			$cuotas_doc[] = (Object) ['monto' => $venta->total, 'fecha' => $venta->fechaVencimiento]; 
		}

		$numerofactura = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		$fechaemision = $venta->fecha; //'2019-04-03';//$request["fechaemision"];
		$horaemision = date('H:i:s', strtotime($venta->created_at));//'21:39:00';//$_POST["horaemision"];

		$serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		//   --------------- PARA EL CLIENTE ----------------------------
		$cliente = Persona::where('id','=',$venta->idCliente)
				->where('tipoPersona','=','C')
				->where('tipoDocumento','=','PJ')
				->first();
	
		$nombre = $cliente->razonSocial; //'Cualquiera';//$_POST["nombre"];
		$tipodoc =  '6';//$_POST["tipodoc"]; // RUC: 6 DNI : 1
		$codUbigeo = '0000'; //$_POST["codUbigeo"]; si tengo sucursales, solicitar este codigo
		$ruc = $cliente->documento; //'-';//$_POST["dni"];
		$moneda = $venta->tipoMoneda; //'PEN';//$_POST["moneda"];
		$direccion = $cliente->direccion; //'-';//$_POST["direccion"];
		$descuentototal = '0';//$_POST["descuentoglobal"];
		$percepcion = "";
		$aplicacionpercepcion = "";
		$documentosanexos = array();
	
		$detalles = array();
		// ------------------------------------------------------------------------------------------------------
		$detalles_venta = DetalleVenta::where('idVenta','=',$venta->id)
						->get();

		$k= 0;
		foreach ($detalles_venta as $det) {
			if ($det->precio > 0) {
				$tipo_detalle = ($det->precio > 0?'V':'R');
				$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
				
				//$pr = Producto::findOrFail($det->idProducto);
				// $cat = CategoriaProducto::findOrFail($pr->codCategoria);
				/*' A-554 '*/
				// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
				$descripcion = $det->descripcion;
				
				/*$descripcion =$cat->nombre.' ';
				switch($pr->forma){
					case 'RED': $descripcion.='redondo ';break;
					case 'CUA': $descripcion.='cuadrado ';break;
					case 'CUAD': $descripcion.='cuadrado ';break;
					case 'CUADR': $descripcion.='cuadrado ';break;
					case 'REC': $descripcion.='rectangular ';break;
					case 'RECT': $descripcion.='rectangular ';break;
				}
				
				$descripcion.=$pr->medida.' ';
				switch($pr->acabado){
					case 'BRILL': $descripcion.='brillante ';break;
					case 'BRIL': $descripcion.='brillante ';break;
					case 'BRI': $descripcion.='brillante ';break;
					case 'SAT': $descripcion.='satinado ';break;
				}
				
				$descripcion.=$pr->calidad.' '.strtolower($pr->nombre).' '. $pr->espesor.'mm '.$pr->tamaño.' '.$pr->unidad;
				
				*/
				
				if ($k==0) {
			    	$detalles[] = array(
    					"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key], // tipo_detalle= Regalo
    					"codigo"=> '-', //$pr->codRegistro, //'-', //$codigo[$key],
    					"unidadmedida"=>'NIU',//$unidades[$key],
    					"cantidad"=> $det->cantidad, //'3',//$cantidad[$key],
    					"descripcion"=> $descripcion, //$det->descripcion_producto, //'Clavos',//$descripcion[$key],
    					"precioventaunitarioxitem"=> $det->precio, //'0.5',//$precio[$key],
    					"descuentoxitem"=>'0',//$descuentoxitem[$key],
    					"tipoigv"=>$igv_sunat,//$tipoigv[$key], tipos de igv () -> declarando igv // cod 20: exonerado// cod: 30: inafecto
    					"tasaisc"=>"0", //impuesto selectivo al consumo
    					"aplicacionisc"=>"",
    					"precioventasugeridoxitem"=>"",
    					"placa" => $venta->placa
    				);
				
				} else {
    				$detalles[] = array(
    					"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key], // tipo_detalle= Regalo
    					"codigo"=> '-', //$pr->codRegistro, //'-', //$codigo[$key],
    					"unidadmedida"=>'NIU',//$unidades[$key],
    					"cantidad"=> $det->cantidad, //'3',//$cantidad[$key],
    					"descripcion"=> $descripcion, //$det->descripcion_producto, //'Clavos',//$descripcion[$key],
    					"precioventaunitarioxitem"=> $det->precio, //'0.5',//$precio[$key],
    					"descuentoxitem"=>'0',//$descuentoxitem[$key],
    					"tipoigv"=>$igv_sunat,//$tipoigv[$key], tipos de igv () -> declarando igv // cod 20: exonerado// cod: 30: inafecto
    					"tasaisc"=>"0", //impuesto selectivo al consumo
    					"aplicacionisc"=>"",
    					"precioventasugeridoxitem"=>"",
    				);
				}
				$k++;
			}
		}

		$factura = array(
			"numerofactura"=>$numerofactura,
			"fechaemision"=>$fechaemision,
			"horaemision"=>$horaemision,
			"usuario"=>$nombre,
			"codubigeo"=>$codUbigeo,
			"tipodoc"=>$tipodoc,
			"ruc"=>$ruc,
			"moneda"=>$moneda,
			"descuentototal"=>$descuentototal,
			"percepcion"=>$percepcion,
			"aplicacionpercepcion"=>$aplicacionpercepcion,
			"documentosanexos"=>$documentosanexos,
			"obs_comprobante"=>$venta->observaciones,
			"formapago_doc" => $venta->tipoPago,
			"forma_pago" => $venta->metodoPago,
			"direccion_cliente" => $direccion,
        	"cuotas_doc" => $cuotas_doc,
			"detalles"=>$detalles,
			"anticipos" => $anticipos
		);

		// para el envio -- estructura anular directamente  -- 07 dias --
		$datos_enviar = array(
			//"token"=>$token,
			"seriefactura"=>$serie,//'001',//$serie["numero_serie"],
			"correlativofactura"=> $numero, //'00000001',//$_POST["numfac"],
			"doc"=>$ruc,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total,//$_POST["totalDocumento"],
			"comprobante"=> json_encode($factura)
		);

		$datos_enviar = json_encode($datos_enviar);
		//throw new Exception($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_factura.php");
		//$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}

		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));
		error_log(json_encode($result));

		$result = json_decode($result);
		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;


			$venta->nombreDocumentoSUNAT = $nombre_documento;
			$venta->idDocumentoSUNAT = $id_solicitud;
			$venta->update();
		
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			
			// file_put_contents($filename_zip, $file_ZIP);
		}else{
			return ['errores' => $result->mensaje];
		}
	}

	public function declararBoletaAuto($user,$pass,$venta){
		// estructura del documento
		$numeroboleta = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
        $fechaemision = $venta->fecha; //'2019-04-03';//$request["fechaemision"];
        $horaemision = date('H:i:s', strtotime($venta->created_at));//'21:39:00';//$_POST["horaemision"];

		$serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		// ******************************************************************
		//  PARA EL CLIENTE 
		// ******************************************************************
		
		$cliente = Persona::where('id','=',$venta->idCliente)
				   ->where('tipoPersona','=','C')
				   ->where('tipoDocumento','=','PN')
				   ->first();
		
        $nombre = $cliente->apellidos.' '.$cliente->nombres; //'Cualquiera';//$_POST["nombre"];
        $tipodoc =  (strlen($cliente->documento)==8?'1':'4');//$_POST["tipodoc"]; // RUC: 6 DNI : 1
        $codUbigeo = '0000'; //$_POST["codUbigeo"]; si tengo sucursales, solicitar este codigo
        $dni = $cliente->documento; //'-';//$_POST["dni"];
        $moneda = $venta->tipoMoneda; //'PEN';//$_POST["moneda"];
        $direccion = $cliente->direccion; //'-';//$_POST["direccion"];
        $descuentototal = '0';//$_POST["descuentoglobal"];
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        
        $detalles = array();
        // ------------------------------------------------------------------------------------------------------
		$detalles_venta = DetalleVenta::join('auto as a','a.id','=','detalleventa.idAuto')
							->where('detalleventa.idVenta','=',$venta->id)
							->select('detalleventa.*','a.codproveedor')
							->get();

        foreach ($detalles_venta as $det) {
			if ($det->precio > 0) {
				$tipo_detalle = ($det->precio > 0?'V':'R');
				$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
				$descripcion = $det->descripcion;
				// $pr = Producto::findOrFail($det->idProducto);
				// $cat = CategoriaProducto::findOrFail($pr->idCategoriaProducto);
				/*' A-554 '*/
				// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
			
				$detalles[] = array(
					"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key], // tipo_detalle= Regalo
					"codigo"=> $det->codproveedor, //$pr->codRegistro, //'-', //$codigo[$key],
					"unidadmedida"=>'NIU',//$unidades[$key],
					"cantidad"=> $det->cantidad, //'3',//$cantidad[$key],
					"descripcion"=> $descripcion, //'Clavos',//$descripcion[$key],
					"precioventaunitarioxitem"=> $det->precio, //'0.5',//$precio[$key],
					"descuentoxitem"=>'0',//$descuentoxitem[$key],
					"tipoigv"=>$igv_sunat,//$tipoigv[$key], tipos de igv () -> declarando igv // cod 20: exonerado// cod: 30: inafecto
					"tasaisc"=>"0", //impuesto selectivo al consumo
					"aplicacionisc"=>"",
					"precioventasugeridoxitem"=>"",
				);
			}
		}
        $factura = array(
            "numeroboleta"=>$numeroboleta,
            "fechaemision"=>$fechaemision,
            "horaemision"=>$horaemision,
            "usuario"=>$nombre,
            "codubigeo"=>$codUbigeo,
            "tipodoc"=>$tipodoc,
            "dni"=>$dni,
            "moneda"=>$moneda,
            "descuentototal"=>$descuentototal,
            "percepcion"=>$percepcion,
            "aplicacionpercepcion"=>$aplicacionpercepcion,
            "documentosanexos"=>$documentosanexos,
			"obs_comprobante"=>$venta->observaciones,
			"forma_pago" => $venta->metodoPago,
			"direccion_cliente" => $direccion,
            "detalles"=>$detalles
        );

        // para el envio -- estructura anular directamente  -- 07 dias --
		$datos_enviar = array(
			"serieboleta"=>$serie,//'001',//$serie["numero_serie"],
			"correlativoboleta"=> $numero, //'00000001',//$_POST["numfac"],
			"doc"=>$dni,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total,//$_POST["totalDocumento"],
			"comprobante"=> json_encode($factura)
		);
		$datos_enviar = json_encode($datos_enviar);
		//dd($datos_enviar);
		//throw new Exception($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_boleta.php");
		//$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}

		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));
		//   error_log(json_encode($result));

		$result = json_decode($result);
		//dd($result);
		if($result->code=="0"){
		//   Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$venta->nombreDocumentoSUNAT = $nombre_documento;
			$venta->idDocumentoSUNAT = $id_solicitud;
			$venta->update();
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			// file_put_contents($filename_zip, $file_ZIP);
		}else{
			return ['errores' => $result->mensaje];
		}
	}

	public function declararFacturaAuto($user,$pass,$venta){
		// estructura del documento
		$cuotas_doc = [];
		if ($venta->tipoPago == 'D') {
			$cuotas_doc[] = (Object) ['monto' => $venta->total, 'fecha' => $venta->fechaVencimiento]; 
		}

		$numerofactura = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		$fechaemision = $venta->fecha; //'2019-04-03';//$request["fechaemision"];
		$horaemision = date('H:i:s', strtotime($venta->created_at));//'21:39:00';//$_POST["horaemision"];

		$serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		//   --------------- PARA EL CLIENTE ----------------------------
		$cliente = Persona::where('id','=',$venta->idCliente)
				->where('tipoPersona','=','C')
				->where('tipoDocumento','=','PJ')
				->first();
	
		$nombre = $cliente->razonSocial; //'Cualquiera';//$_POST["nombre"];
		$tipodoc =  '6';//$_POST["tipodoc"]; // RUC: 6 DNI : 1
		$codUbigeo = '0000'; //$_POST["codUbigeo"]; si tengo sucursales, solicitar este codigo
		$ruc = $cliente->documento; //'-';//$_POST["dni"];
		$moneda = $venta->tipoMoneda; //'PEN';//$_POST["moneda"];
		$direccion = $cliente->direccion; //'-';//$_POST["direccion"];
		$descuentototal = '0';//$_POST["descuentoglobal"];
		$percepcion = "";
		$aplicacionpercepcion = "";
		$documentosanexos = array();
	
		$detalles = array();
		// ------------------------------------------------------------------------------------------------------
		$detalles_venta = DetalleVenta::join('auto as a','a.id','=','detalleventa.idAuto')
						->where('detalleventa.idVenta','=',$venta->id)
						->select('detalleventa.*','a.codproveedor')
						->get();

		foreach ($detalles_venta as $det) {
			if ($det->precio > 0) {
				$tipo_detalle = ($det->precio > 0?'V':'R');
				$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
				
				//$pr = Producto::findOrFail($det->idProducto);
				// $cat = CategoriaProducto::findOrFail($pr->codCategoria);
				/*' A-554 '*/
				// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
				$descripcion = $det->descripcion;
				
				/*$descripcion =$cat->nombre.' ';
				switch($pr->forma){
					case 'RED': $descripcion.='redondo ';break;
					case 'CUA': $descripcion.='cuadrado ';break;
					case 'CUAD': $descripcion.='cuadrado ';break;
					case 'CUADR': $descripcion.='cuadrado ';break;
					case 'REC': $descripcion.='rectangular ';break;
					case 'RECT': $descripcion.='rectangular ';break;
				}
				
				$descripcion.=$pr->medida.' ';
				switch($pr->acabado){
					case 'BRILL': $descripcion.='brillante ';break;
					case 'BRIL': $descripcion.='brillante ';break;
					case 'BRI': $descripcion.='brillante ';break;
					case 'SAT': $descripcion.='satinado ';break;
				}
				
				$descripcion.=$pr->calidad.' '.strtolower($pr->nombre).' '. $pr->espesor.'mm '.$pr->tamaño.' '.$pr->unidad;
				
				*/
				
				$detalles[] = array(
					"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key], // tipo_detalle= Regalo
					"codigo"=> $det->codproveedor, //$pr->codRegistro, //'-', //$codigo[$key],
					"unidadmedida"=>'NIU',//$unidades[$key],
					"cantidad"=> $det->cantidad, //'3',//$cantidad[$key],
					"descripcion"=> $descripcion, //$det->descripcion_producto, //'Clavos',//$descripcion[$key],
					"precioventaunitarioxitem"=> $det->precio, //'0.5',//$precio[$key],
					"descuentoxitem"=>'0',//$descuentoxitem[$key],
					"tipoigv"=>$igv_sunat,//$tipoigv[$key], tipos de igv () -> declarando igv // cod 20: exonerado// cod: 30: inafecto
					"tasaisc"=>"0", //impuesto selectivo al consumo
					"aplicacionisc"=>"",
					"precioventasugeridoxitem"=>"",
				);
			}
		}

		$factura = array(
			"numerofactura"=>$numerofactura,
			"fechaemision"=>$fechaemision,
			"horaemision"=>$horaemision,
			"usuario"=>$nombre,
			"codubigeo"=>$codUbigeo,
			"tipodoc"=>$tipodoc,
			"ruc"=>$ruc,
			"moneda"=>$moneda,
			"descuentototal"=>$descuentototal,
			"percepcion"=>$percepcion,
			"aplicacionpercepcion"=>$aplicacionpercepcion,
			"documentosanexos"=>$documentosanexos,
			"obs_comprobante"=>$venta->observaciones,
			"formapago_doc" => $venta->tipoPago,
			"forma_pago" => $venta->metodoPago,
			"direccion_cliente" => $direccion,
        	"cuotas_doc" => $cuotas_doc,
			"detalles"=>$detalles
		);

		// para el envio -- estructura anular directamente  -- 07 dias --
		$datos_enviar = array(
			//"token"=>$token,
			"seriefactura"=>$serie,//'001',//$serie["numero_serie"],
			"correlativofactura"=> $numero, //'00000001',//$_POST["numfac"],
			"doc"=>$ruc,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total,//$_POST["totalDocumento"],
			"comprobante"=> json_encode($factura)
		);

		$datos_enviar = json_encode($datos_enviar);
		//throw new Exception($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_factura.php");
		//$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}

		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));
		error_log(json_encode($result));

		$result = json_decode($result);
		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;


			$venta->nombreDocumentoSUNAT = $nombre_documento;
			$venta->idDocumentoSUNAT = $id_solicitud;
			$venta->update();
		
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			
			// file_put_contents($filename_zip, $file_ZIP);
		}else{
			return ['errores' => $result->mensaje];
		}
	}

  	public function anularBoleta($user,$pass,$venta,$anulacion){
		// $numeroboleta = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
        $serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		$cliente = Persona::where('id','=',$venta->idCliente)
				   ->where('tipoPersona','=','C')
				   ->where('tipoDocumento','=','PN')
				   ->first();
		

		$numeroboleta = $anulacion->serie."-".$anulacion->numero;
		$fechaemision = date('Y-m-d');
		$tipoResumen = '03';//$_POST["tipoResumen"];
		$fechareferencia = $venta->fecha; //$_POST["fecref"];
		$moneda = $venta->tipoMoneda; //'PEN'; //$_POST["moneda"];
		$tipodetalle = '03';
		$idDetalle =$venta->id;// $_POST["idDetalle"];
		$idDetalleServidor = $venta->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
		$serieDetalle = $serie; //$_POST["serieDetalle"];
		$correlativo = $numero; //$_POST["correlativo"];
		$dni = $cliente->documento;//$_POST["dni"];
		$total = $venta->total; //$_POST["total"];
		$numeroReferencia = "";//$_POST["numeroReferencia"];
		$tipoReferencia = "";///$_POST["tipoReferencia"];
		$detalles = array();

		// foreach ($detalles_venta as $det) {
			//    $pr = Producto::findOrFail($det->codProducto);
			$detalles[] = array(
				"id"=>$venta->id,
				"idservidor"=>$venta->idDocumentoSUNAT,
				"tipo"=>'03',
				"numero"=>$serie,
				"correlativo"=>$numero,
				"dni"=> $cliente->documento, //$dni[$key],
				"total"=>$venta->total,
				"numeroReferencia"=>'',//$venta->numero_comprobante,
				"tipoReferencia"=>'',//$tipoReferencia[$key],
			);
		// }


		if(count($detalles)==0){
			throw new \Exception("NO TIENE NINGUN DETALLE");
		}


		$factura = array(
			"numeroboleta"=>$numeroboleta,
			"tiporesumen"=>$tipoResumen,
			"fechaemision"=>$fechaemision,
			"fechareferencia"=>$fechareferencia,
			"moneda"=>$moneda,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//"token"=>$token,
			"serieboleta"=>$anulacion->serie,
			"correlativoboleta"=>$anulacion->numero,
			"comprobante"=> json_encode($factura)
		);


		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_resumenboletas.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

		$result = json_decode($result);
		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$anulacion->nombreDocumentoSUNAT = $nombre_documento;
			$anulacion->idDocumentoSUNAT = $id_solicitud;
			$anulacion->save();
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			
			// file_put_contents($filename_zip, $file_ZIP);

			return ['estado' => true];
		}else{
			return ['errores' => $result->mensaje, 'estado' => false];
		}
    }

	public function anularFactura($user,$pass,$venta,$anulacion){
	    $serie = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT);
		$numero = str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		
		$cliente = Persona::where('id','=',$venta->idCliente)
				   ->where('tipoPersona','=','C')
				   ->where('tipoDocumento','=','PJ')
				   ->first();
		
        $numerobaja = $anulacion->serie.'-'.$anulacion->numero;
        $fechaemision = date("Y-m-d");

        $fechareferencia = $venta->fecha; //$_POST["fecref"];
        $tipodetalle = '01'; //$_POST["tipodocumento"];
        $serieDetalle = $serie; // $_POST["serieDetalle"];
        $correlativo = $numero; //$_POST["correlativo"];
        $motivo = 'Anulación de la Operación';//$_POST["motivo"];
        $idDetalles = $venta->id; //$_POST["idDetalle"];
        $idDetalleServidor = $venta->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
        $detalles = array();
        //foreach ($idDetalles as $key => $value) {
          //  if($value>0){
		$detalles[] = array(
			"id"=> $venta->id, //$idDetalles[$key],
			"idservidor"=> $venta->idDocumentoSUNAT, //$idDetalleServidor[$key],
			"tipo"=>'01', //$tipodetalle[$key],
			"numero"=>$serie,//$serieDetalle[$key],
			"correlativo"=>$numero, //$correlativo[$key],
			"motivo"=> 'Anulación de la Operación',//$motivo[$key],
		);
		//}
		//  }
		// dd($detalles);
		if(count($detalles)==0){
			throw new \Exception("NO TIENE NINGUN DETALLE");
		}
		$factura = array(
			"numerobaja"=>$numerobaja,
			"fechaemision"=>$fechaemision,
			"fechareferencia"=>$fechareferencia,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//  "token"=>$token,
			"seriebaja"=> $anulacion->serie,//$serie["numero_serie"],
			"correlativobaja"=>$anulacion->numero, //$_POST["numfac"],
			"comprobante"=> json_encode($factura)
		);

		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_comunicacionbajas.php");

		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

		$result = json_decode($result);
		// error_log($result);
		//dd($result);
		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$anulacion->nombreDocumentoSUNAT = $nombre_documento;
			$anulacion->idDocumentoSUNAT = $id_solicitud;
			$anulacion->save();

			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			// file_put_contents($filename_zip, $file_ZIP);

			return ['estado' => true];
		}else{
			return ['errores' => $result->mensaje, 'estado' => false];
		}
    }

	public function anularNotaBoleta($user,$pass,$venta,$anulacion,$tipo_resumen){
		// $numero_boleta= explode("-",$venta->numero_comprobante);
		$venta_o = DB::table('venta')->where('id','=',$venta->idVenta)->first();
		$cliente = Persona::findOrFail($venta_o->idCliente);

		$serie = $venta_o->tipoComprobante.$venta->tipoComprobante.str_pad($venta->serie,2,'0',STR_PAD_LEFT);
		$num   = str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		

		$numeroboleta = $venta_o->tipoComprobante.$venta->tipoComprobante.str_pad($venta->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		
		$numerobaja = $anulacion->serie."-".$anulacion->numero;
		
		// $numeroboleta = $anulacion->serie."-".$anulacion->numero;
		$fechaemision = date("Y-m-d");
		$tipoResumen = $tipo_resumen;//$_POST["tipoResumen"];
		$fechareferencia = date('Y-m-d',$venta->created_at); //$_POST["fecref"];
		$moneda = $venta->tipoMoneda; //'PEN'; //$_POST["moneda"];
		$tipodetalle = $tipo_resumen;
		$idDetalle =$venta->id;// $_POST["idDetalle"];
		$idDetalleServidor = $venta->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
		$serieDetalle = $serie;// $numero_boleta[0]; //$_POST["serieDetalle"];
		$correlativo = $num; //$numero_boleta[1]; //$_POST["correlativo"];
		$dni = $cliente->documento;//$_POST["dni"];
		$total = $venta_o->total; //$_POST["total"];
		$numeroReferencia = "";//$_POST["numeroReferencia"];
		$tipoReferencia = "";///$_POST["tipoReferencia"];
		$detalles = array();

		// foreach ($detalles_venta as $det) {
		//    $pr = Producto::findOrFail($det->codProducto);
		$detalles[] = array(
			"id"=>$venta->id,
			"idservidor"=>$venta->idDocumentoSUNAT,
			"tipo"=>$tipo_resumen,
			"numero"=> $serie, //$numero_boleta[0],
			"correlativo"=> $num, //$numero_boleta[1],
			"dni"=> $cliente->documento, //$dni[$key],
			"total"=>$venta_o->total,
			"numeroReferencia"=> $numeroboleta,// $venta->numero_comprobante,
			"tipoReferencia"=>'',//$tipoReferencia[$key],
		);
		// }


		if(count($detalles)==0){
			throw new Exception("NO TIENE NINGUN DETALLE");
		}

		$factura = array(
			"numeroboleta"=>$numerobaja,
			"tiporesumen"=>$tipoResumen,
			"fechaemision"=>$fechaemision,
			"fechareferencia"=>$fechareferencia,
			"moneda"=>$moneda,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//"token"=>$token,
			"serieboleta"=>$anulacion->serie,
			"correlativoboleta"=>$anulacion->numero,
			"comprobante"=> json_encode($factura)
		);


		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_resumenboletas.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

		$result = json_decode($result);

		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$anulacion->nombreDocumentoSUNAT = $nombre_documento;
			$anulacion->idDocumentoSUNAT = $id_solicitud;
			$anulacion->save();
			
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			// file_put_contents($filename_zip, $file_ZIP);

			return ['estado'=>true];
		}else{
			return ['errores' => $result->mensaje, 'estado' => false];
		}
	}
	
	public function anularNotaFactura($user,$pass,$venta,$anulacion,$tipo_resumen){
	
		// dd($venta,$anulacion);
		$venta_o = DB::table('venta')->where('id','=',$venta->idVenta)->first();
		$cliente = Persona::findOrFail($venta_o->idCliente);
		
		$serie = $venta_o->tipoComprobante.$venta->tipoComprobante.str_pad($venta->serie,2,'0',STR_PAD_LEFT);
		$num   = str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		

		$numeroboleta = $venta_o->tipoComprobante.$venta->tipoComprobante.str_pad($venta->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);
		
		// $numero_factura= explode("-",$venta->numero_comprobante);
		// $cliente = Cliente::findOrFail($venta->codCliente);

		$numerobaja = $anulacion->serie."-".$anulacion->numero;
		
		$fechaemision = date("Y-m-d");
		$fechareferencia = date('Y-m-d',strtotime($venta->created_at)); //$_POST["fecref"];
		$tipodetalle = $tipo_resumen; //$_POST["tipodocumento"];
		$serieDetalle = $serie; //$numero_factura[0]; // $_POST["serieDetalle"];
		$correlativo = $num; //$numero_factura[1]; //$_POST["correlativo"];
		$motivo = 'Anulación de la Operación';//$_POST["motivo"];
		$idDetalles = $venta->id; //$_POST["idDetalle"];
		$idDetalleServidor = $venta->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
		$detalles = array();
		
		$detalles[] = array(
			"id"=> $venta->id, //$idDetalles[$key],
			"idservidor"=> $venta->idDocumentoSUNAT, //$idDetalleServidor[$key],
			"tipo"=> $tipo_resumen, //'01', //$tipodetalle[$key],
			"numero"=> $serie, //$numero_factura[0],//$serieDetalle[$key],
			"correlativo"=> $num,//$numero_factura[1], //$correlativo[$key],
			"motivo"=> 'Anulación de la Operación',//$motivo[$key],
		);
	
		if(count($detalles)==0){
			throw new \Exception("NO TIENE NINGUN DETALLE");
		}
		$factura = array(
			"numerobaja"=>$numerobaja,
			"fechaemision"=>$fechaemision,
			"fechareferencia"=>$fechareferencia,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//"token"=>$token,
			"seriebaja"=> $anulacion->serie,//$serie["numero_serie"],
			"correlativobaja"=>$anulacion->numero, //$_POST["numfac"],
			"comprobante"=> json_encode($factura)
		);

		// dd($datos_enviar);
		
		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_comunicacionbajas.php");

		
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

		$result = json_decode($result);
		
		// dd($result);
		if($result->code=="0"){
		//    Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$anulacion->nombreDocumentoSUNAT = $nombre_documento;
			$anulacion->idDocumentoSUNAT = $id_solicitud;

			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			$anulacion->save();
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			
			// file_put_contents($filename_zip, $file_ZIP);
			return ['estado' => true];
		}else{
			return ['errores' => $result->mensaje, 'estado' => false];
		}
	}

	public function declararNotaCredito($user,$pass,$nota_credito,$venta, $tMotivo){
		$numeroref = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		// $nota_credito->serie_anulacion = substr($venta->numero_comprobante,0,1).$nota_credito->serie_anulacion; 
		$serienotacredito = $venta->tipoComprobante.'C'.str_pad($nota_credito->serie,2,'0',STR_PAD_LEFT);
		$numnotacredito   = str_pad($nota_credito->numero,8,'0',STR_PAD_LEFT);
		$numeronotacredito = $venta->tipoComprobante.'C'.str_pad($nota_credito->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($nota_credito->numero,8,'0',STR_PAD_LEFT); //$nota_credito->serie_anulacion.'-'.$nota_credito->numero_comprobante; //$serie["numero_serie"]."-".$_POST["numfac"];
        $fechaemision =  date('Y-m-d'); //$nota_credito->fechaNota; //$_POST["fechaemision"];

        $cliente = Persona::findOrFail($nota_credito->idCliente);
		if ($venta->tipoComprobante == 'B') {
			$nombre = $cliente->apellidos.' '.$cliente->nombres; //$_POST["nombre"];
        } else {
			$nombre = $cliente->razonSocial;
		}
		$direccion = $cliente->direccion; //$_POST["direccion"];
        $doc = $cliente->documento; //$_POST["doc"];
        $moneda = $venta->tipoMoneda; //"PEN"; //$_POST["moneda"];

        // dd($venta->id_documento_SUNAT);
        $idreferencia = $venta->idDocumentoSUNAT; //$_POST["idreferencia"];
        $numreferencia = $numeroref; //strtoupper($_POST["numreferencia"]);
        $prefreferencia = $venta->tipoComprobante;
        $tiporeferencia = "";
        if($prefreferencia=="F"){
            $tiporeferencia = "01";
            //$tipo_doc = "6";
        }elseif($prefreferencia=="B"){
            $tiporeferencia = "03";
            //$tipo_doc = "1";
        }else{
            throw new \Exception("EL NUMERO DE REFERENCIA ES DESCONOCIDO", 5);
        }

        $motivo = '';
        switch ($tMotivo) {
          case 1:
            $motivo = 'Anulación de la operación';
            break;
          case 2:
            $motivo = 'Anulación por error en el RUC';
              break;
          case 3:
            $motivo = 'Corrección por error en la descripción';
            break;
          case 4:
            $motivo = 'Descuento global';
            break;
          case 5:
            $motivo = 'Descuento por Item';
            break;
          case 6:
            $motivo = 'Devolución total';
            break;
          case 7:
            $motivo = 'Devolución parcial';
            break;
          case 8:
            $motivo = 'Bonificación';
            break;
          default:
            $motivo = 'Disminución en el valor';
            break;
        }

        $nota_credito->motivo = $motivo;
        // $motivo = $_POST["motivo"];
        $descuentototal = "0";//$_POST["descuentoglobal"];
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        // $tipodetalle = $_POST["tipodetalle"];

        // if(empty($tipodetalle)){
        //     $tipodetalle = array();
        // }
        // $unidades = "NIU";//$_POST["unidades"];
        // $tipoigv = "10";//$_POST["tipoigv"];
        // $descuentoxitem = "0"//$_POST["descuentoxitem"];
        // $codigo = $_POST["codigo"];
        // $cantidad = $_POST["cantidad"];
        // $descripcion = $_POST["descripcion"];
        // $precio = $_POST["precio"];
        $detalles = array();

        $detalles_venta =  DetalleVenta::where('idVenta','=',$venta->id)->get();
		foreach ($detalles_venta as $det_v) {
			$pr = Producto::findOrFail($det_v->idProducto);
		
			$tipo_detalle = ($det_v->precio > 0?'V':'R');
			$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
			$descripcion = $det_v->descripcion;
			// $cat = CategoriaProducto::findOrFail($pr->codCategoria);
			// /*' A-554 '*/
			// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
		
			$detalles[] = array(
				"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key],
				"codigo"=>$pr->codRegistro,
				"unidadmedida"=>"NIU",//$unidades[$key],
				"cantidad"=>$det_v->cantidad, //$cantidad[$key],
				"descripcion"=> $descripcion, //$det_v->descripcion_producto, //$descripcion[$key],
				"precioventaunitarioxitem"=> $det_v->precio, //$precio[$key],
				"descuentoxitem"=>"0",//$descuentoxitem[$key],
				"tipoigv"=>$igv_sunat,//$tipoigv[$key],
				"tasaisc"=>"0",
				"aplicacionisc"=>"",
				"precioventasugeridoxitem"=>"",
			);
		}

		// dd($detalles);
		if(count($detalles)==0){
			throw new \Exception("NO TIENE NINGUN DETALLE");
		}
		if($prefreferencia=="F" && (strlen(trim($nombre))<=0 || strlen(trim($doc))<=0)){
			throw new \Exception("EL NOMBRE ".strlen(trim($nombre))." O RUC ".strlen(trim($doc))." DEL CLIENTE ESTAN VACIOS");
		}
		$factura = array(
			"numeronotacredito"=>$numeronotacredito,
			"fechaemision"=>$fechaemision,
			"tipo"=>(int)$tMotivo,
			"motivo"=>$motivo,
			"idreferencia"=>$idreferencia,
			"numeroreferencia"=>$numreferencia,
			"tiporeferencia"=>$tiporeferencia,
			"usuario"=>$nombre,
			"doc"=>$doc,
			"moneda"=>$moneda,
			"descuentototal"=>$descuentototal,
			"percepcion"=>$percepcion,
			"aplicacionpercepcion"=>$aplicacionpercepcion,
			"documentosanexos"=>$documentosanexos,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//"token"=>$token,
			"serienota"=> $serienotacredito,//$serie["numero_serie"],
			"doc"=>$doc,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total, //$_POST["totalDocumento"],
			"correlativonota"=>$numnotacredito, //$_POST["numfac"],
			"comprobante"=> json_encode($factura)
		);
		
		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_notacredito.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

	    //    dd($result);
		$result = json_decode($result);
		// dd($result);

		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			// dd($nombre_documento);

			$nota_credito->nombreDocumentoSUNAT = $nombre_documento;
			$nota_credito->idDocumentoSUNAT = $id_solicitud;
			$nota_credito->save();
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			
			// file_put_contents($filename_zip, $file_ZIP);
		
			return true;
		}else{
			
			return false;
			//return redirect()->back()->withErrors(['errores' => $result->mensaje])->withInput(request(['errores']));
		}
	}

	public function declararNotaDebito($user,$pass,$nota_debito,$venta, $tMotivo){
		// $numero_debito = explode('-',$nota_debito->numero_comprobante);
		// $numeronotacredito = $nota_credito->numero_comprobante; //$serie["numero_serie"]."-".$_POST["numfac"];
		// $fechaemision = $nota_credito->fechaNota; //$_POST["fechaemision"];
		$numeroref = $venta->tipoComprobante.str_pad($venta->serie,3,'0',STR_PAD_LEFT).'-'.str_pad($venta->numero,8,'0',STR_PAD_LEFT);

		$serienotadebito = $venta->tipoComprobante.'D'.str_pad($nota_debito->serie,2,'0',STR_PAD_LEFT);
		$numnotadebito   = str_pad($nota_debito->numero,8,'0',STR_PAD_LEFT);
		$numeronotadebito = $venta->tipoComprobante.'D'.str_pad($nota_debito->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($nota_debito->numero,8,'0',STR_PAD_LEFT); //$nota_credito->serie_anulacion.'-'.
		// $numeronotadebito = $nota_debito->serie_anulacion.'-'.$nota_debito->numero_comprobante; //$serie["numero_serie"]."-".$_POST["numfac"];
		$fechaemision = date('Y-m-d'); //$_POST["fechaemision"];

		$cliente = Persona::findOrFail($nota_debito->idCliente);
		if ($venta->tipoComprobante == 'B') {
			$nombre = $cliente->apellidos.' '.$cliente->nombres; //$_POST["nombre"];
		} else {
			$nombre = $cliente->razonSocial;
		}
		
		$direccion = $cliente->direccion; //$_POST["direccion"];
		$doc = $cliente->documento; //$_POST["doc"];
		$moneda = $venta->tipoMoneda; //'PEN'; //$_POST["moneda"];


		$idreferencia = $venta->idDocumentoSUNAT;//$_POST["idreferencia"];
		$numreferencia = $numeroref;
		$prefreferencia = $venta->tipoComprobante;
		$tiporeferencia = "";
		
		if($prefreferencia=="F"){
			$tiporeferencia = "01";
		}elseif($prefreferencia=="B"){
			$tiporeferencia = "03";
		}else{
			throw new \Exception("EL NUMERO DE REFERENCIA ES DESCONOCIDO", 5);
		}
		$motivo = '';//$_POST["motivo"];

		switch ($tMotivo) {
			case 1:
				$motivo = 'Interes por mora';
			break;
			default:
				$motivo = 'Aumento en el valor';
			break;
		}

		$nota_debito->motivo = $motivo;
		$descuentototal = '0';//$_POST["descuentoglobal"];
		$percepcion = "";
		$aplicacionpercepcion = "";
		$documentosanexos = array();

		// $tipodetalle = $_POST["tipodetalle"];
		// if(empty($tipodetalle)){
		//     $tipodetalle = array();
		// }
		//
		// $unidades = $_POST["unidades"];
		// $tipoigv = $_POST["tipoigv"];
		// $descuentoxitem = $_POST["descuentoxitem"];
		// $codigo = $_POST["codigo"];
		// $cantidad = $_POST["cantidad"];
		// $descripcion = $_POST["descripcion"];
		// $precio = $_POST["precio"];
		$detalles = array();
		$detalles_venta = DetalleVenta::where('idVenta','=',$venta->id)->get();

		foreach ($detalles_venta as $det_v) {
			$tipo_detalle = ($det_v->precio > 0?'V':'R');
			$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);

			$pr = Producto::findOrFail($det_v->idProducto);
			// $cat = CategoriaProducto::findOrFail($pr->codCategoria);
			/*' A-554 '*/
			// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
			$descripcion = $det_v->descripcion;
			$detalles[] = array(
				"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key],
				"codigo"=>$pr->codRegistro, //$codigo[$key],
				"unidadmedida"=>'NIU', //$unidades[$key],
				"cantidad"=>$det_v->cantidad, //$cantidad[$key],
				"descripcion"=>$descripcion, //$det_v->descripcion_producto,
				"precioventaunitarioxitem"=> $det_v->precio, //$precio[$key],
				"descuentoxitem"=>"0",//$descuentoxitem[$key],
				"tipoigv"=>$igv_sunat,//$tipoigv[$key],
				"tasaisc"=>"0",
				"aplicacionisc"=>"",
				"precioventasugeridoxitem"=>"",
			);
		}

		if(count($detalles)==0){
			throw new \Exception("NO TIENE NINGUN DETALLE");
		}
		if($prefreferencia=="F" && (strlen(trim($nombre))<=0 || strlen(trim($doc))<=0)){
			throw new \Exception("EL NOMBRE ".strlen(trim($nombre))." O RUC ".strlen(trim($doc))." DEL CLIENTE ESTAN VACIOS");
		}
		$factura = array(
			"numeronotadebito"=>$numeronotadebito,
			"fechaemision"=>$fechaemision,
			"tipo"=>(int)$tMotivo,
			"motivo"=>$motivo,
			"idreferencia"=>$idreferencia,
			"numeroreferencia"=>$numreferencia,
			"tiporeferencia"=>$tiporeferencia,
			"usuario"=>$nombre,
			"doc"=>$doc,
			"moneda"=>$moneda,
			"descuentototal"=>$descuentototal,
			"percepcion"=>$percepcion,
			"aplicacionpercepcion"=>$aplicacionpercepcion,
			"documentosanexos"=>$documentosanexos,
			"detalles"=>$detalles
		);

		$datos_enviar = array(
			//"token"=>$token,
			"serienota"=> $serienotadebito, //$serie["numero_serie"],
			"doc"=>$doc,
			"nombre"=>$nombre,
			"direccion"=>$direccion,
			"total"=>$venta->total, //$_POST["totalDocumento"],
			"correlativonota"=>$numeronotadebito, //$_POST["numfac"],
			"comprobante"=> json_encode($factura)
		);
		$datos_enviar = json_encode($datos_enviar);
		$cliente2 = new \nusoap_client("http://api.fasteinvoice.com/wsdl_notadebito.php");
		$error = $cliente2->getError();
		if ($error) {
			throw new \Exception(json_encode($error));
		}
		$result = $cliente2->call("enviar", array("ruc"=> $user, "password" =>$pass,"json" => $datos_enviar));

		$result = json_decode($result);

		
		if($result->code=="0"){
			//Session::put("token",$result->mensaje);
			$file_ZIP_BASE64 = $result->fileZIPBASE64;
			$nombre_documento = $result->nombre_documento;
			$id_solicitud = $result->id_solicitud;

			$nota_debito->nombreDocumentoSUNAT = $nombre_documento;
			$nota_debito->idDocumentoSUNAT = $id_solicitud;
			$nota_debito->save();
			$file_ZIP = base64_decode($file_ZIP_BASE64);
			$filename_zip = $nombre_documento."zip";
			\Storage::disk('local_xml')->put($filename_zip, $file_ZIP);
			// file_put_contents($filename_zip, $file_ZIP);
			
			return true;
		}else{
			return false;
		}
	}

	public function obtenerCotizaciones (Request $request) {  
		$nro   = $request->get('descripcion');
		$clienteId = $request->get('clienteId');

		$cotizacion = Cotizacion::where('situacion','=','V')
					  ->where('situacionFacturado','=','N')
					  ->where('idCliente','=',$clienteId);
		if ($nro != '') { 
			$cotizacion  = $cotizacion->where(DB::Raw("CONCAT(serie,'-',numero)"),'LIKE', '%'.$nro.'%');
		}

		$cotizacion = $cotizacion->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero,8,'0')) as numero"), 
		DB::Raw("DATE_FORMAT(fecha,'%d/%m/%Y') as fecha"),DB::Raw("FORMAT(total,2) as total2"),'id', 'total','placa')
		->get();
		return ['cotizaciones' => $cotizacion];
	}

	public function obtenerOrdenes (Request $request) {
		$nro   = $request->get('descripcion');
		$clienteId = $request->get('clienteId');

		$cotizacion = DB::table('ordentrabajo as ot')
					  ->join('verificacionchecklist as vc','vc.idOrdenTrabajo','=','ot.id')
					  ->where('ot.situacion','=','F')
					  ->where('ot.situacionFacturado','=','N')
					  ->where('vc.rptaVerifCheckCalidad','S')
					  ->where('vc.rptaVerifCheckManejo','S')
					  ->whereNull('ot.deleted_at')
					  ->where('ot.idCliente','=',$clienteId);
		if ($nro != '') { 
			$cotizacion  = $cotizacion->where(DB::Raw("CONCAT(serie,'-',numero)"),'LIKE', '%'.$nro.'%');
		}

		$cotizacion = $cotizacion->select(DB::Raw("CONCAT('O', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as numero"), 
		DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),DB::Raw("FORMAT(ot.total,2) as total2"),'ot.id', 'ot.total','ot.placa')
		->get();

		return ['ordenes' => $cotizacion];
	
	}

	public function getDetallesCotizacionVenta ($idCotizacion, Request $request) {
		$detalles = DetalleCotizacion::where('idCotizacion','=',$idCotizacion)
		->select(DB::Raw("(CASE WHEN idProducto IS NOT NULL THEN 'Producto' ELSE 'Servicio' END) as tipo"), DB::Raw("FORMAT(precio,2) as precio"),
		'descripcion',DB::Raw("cantidad as stock"), 'idCotizacion as id_enlace', 
		'cantidad',DB::Raw("(CASE WHEN idProducto IS NOT NULL THEN idProducto ELSE idServicio END) as id"), 
		DB::Raw("(CASE WHEN idLote IS NULL THEN 0 ELSE idLote END) as lote_id"),'id as detalle_ref')
		->get();

		return ['detalles' => $detalles]; 
	}

	public function getDetallesOrdenVenta ($idOrden, Request $request) {
		$detalles = DetalleOrdenTrabajo::leftjoin('detallecotizacion as detcot','detcot.idCotizacion','=','detalleordentrabajo.idCotizacion')
		->whereNull('detcot.deleted_at')
		->where('detalleordentrabajo.idOrdenTrabajo','=',$idOrden)
		->select(DB::Raw("(CASE WHEN detcot.idProducto IS NOT NULL THEN 'Producto' ELSE 'Servicio' END) as tipo"), 
		DB::Raw("ROUND(detcot.precio,2) as precio"),
		'detcot.descripcion',DB::Raw("detcot.cantidad as stock"), 'detcot.cantidad', 
		'detalleordentrabajo.idOrdenTrabajo as id_enlace',
		DB::Raw("(CASE WHEN detcot.idProducto IS NOT NULL THEN detcot.idProducto ELSE detcot.idServicio END) as id"),
		DB::Raw("(CASE WHEN detcot.idLote IS NULL THEN 0 ELSE detcot.idLote END) as lote_id"),
		'detcot.id as detalle_ref'
		)->get();

		return ['detalles' => $detalles];
	}

	

 
	/**************************************************** */
	/*	AUTENTICACION PARA API
	/**************************************************** */
	public function obtenerTOKENAutorizacion($user_wsdl,$pass_wsdl){
		$cliente = new \nusoap_client("http://fasteinvoice.com/facturacion/wsdl/autenticacion.php");
		//throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
		$error = $cliente->getError();
		if ($error) {
			throw new Exception($error);
		}
		$result = $cliente->call("getAutorizacion", array("ruc"=>$user_wsdl,"password" => $pass_wsdl));
		if ($cliente->fault) {
			throw new \Exception($result);
		} else {
			$error = $cliente->getError();
			if ($error) {
				throw new \Exception($error);
			} else {
				$result = json_decode($result);
				if($result->code=="0"){
					$token = $result->mensaje;
					return $token;
				}else{
					throw new \Exception($result->mensaje);
				}
			}
		}
	}

	public function ComprobarTOKENAutorizacion($token){
		$band = true;
		try{
			$cliente = new \nusoap_client("http://fasteinvoice.com/facturacion/wsdl/autenticacion.php");
			//throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
			$error = $cliente->getError();
			if ($error) {
				throw new \Exception($error);
				$band = false;
			}
			$result = $cliente->call("comprobarTOKEN", array("token"=>$token));
			if ($cliente->fault) {
				throw new \Exception($result);
				$band = false;
			} else {
				$error = $cliente->getError();
				if ($error) {
					throw new \Exception($error);
					$band = false;
				} else {
					$result = json_decode($result);
					if($result->code=="0"){
						$band = true;
					}else{
						$band = false;
					}
				}
			}
		} catch (\Exception $e){
			$band = false;
		}

		return $band;
	}
 
	/********************************************************* */
	// REPORTES DE INICIO
	/********************************************************* */

	public function getReporteInicio(Request $request) {
		// $ventas = Venta::sum('total');
		// $productos = Producto::count('id');
		$clientes = Persona::where('tipoPersona','=','C')->count();
		$dataOrdenes = $this->reporteOrdenes();
		$dataDia = $this->reporteVentasContadoCredito('dia');
		$dataSemana = $this->reporteVentasContadoCredito('semana');
		$dataMes = $this->reporteVentasContadoCredito('mes');

		$result0 = $this->reporteVentasPorUN('dia');
		$result1 = $this->reporteVentasPorUN('semana');
		$result2 = $this->reporteVentasPorUN('mes');
		$resultProductos = $this->getStocksProducto();
		$resultReclamos = $this->getReporteReclamos();
		$resultProspectos = $this->getReporteProspectos();

		return ['clientes' => $clientes, 'dataOrdenes' => $dataOrdenes, 'dataDia' => $dataDia, 'dataMes' => $dataMes, 'dataSemana' => $dataSemana, 
		'dataDiasUN' => $result0, 'dataSemanaUN' => $result1, 'dataMesUN' => $result2,
		'productos' => $resultProductos, 'reclamos' => $resultReclamos, 
		'prospectos' => $resultProspectos
		];
		// $fechaAnt = date('Y-m-d',strtotime('- 1 week'));

		// $satisfaccion = Persona::where('tipoPersona','=','C')
		// 				->where(DB::Raw("DATE_FORMAT(created_at,'%Y-%m-%d')"),'>=',$fechaAnt)
		// 				->count();

		// return ['ventas' => $ventas, 'productos' => $productos, 'clientes' => $clientes, 'satisfaccion' => $satisfaccion];
		
	}


	##################################################################
	#	REPORTES
	#
	##################################################################

	public function reporteOrdenes () {
		$periodo = date('m-Y');
		$total = DB::table('ordentrabajo as ot')
				->where('ot.situacionFacturado','P')
				->whereNull('ot.deleted_at')
				->where('ot.situacion', '!=', 'A')
				->where(DB::Raw("DATE_FORMAT(ot.created_at, '%m-%Y')"), $periodo)
				->sum('ot.total');

		$totalDetalle = DB::table('ordentrabajo as ot')
				->where('ot.situacionFacturado','P')
				->whereNull('ot.deleted_at')
				->where('ot.situacion', '!=', 'A')
				->where(DB::Raw("DATE_FORMAT(ot.created_at, '%m-%Y')"), $periodo)
				->select(DB::Raw("SUM(IFNULL((SELECT SUM(dtc.subTotal) as totalServicios FROM cotizacion as ct 
				JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
				JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
				WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'S' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0)) as totalServicios"),
				DB::Raw("SUM(IFNULL((SELECT SUM(dtc.subTotal) as totalProductos FROM cotizacion as ct 
				JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
				JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
				WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'P' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0)) as totalProductos")
				)->first();

		return [ 'totalFacturado' => number_format($total, 2,'.',', '),
				 'totalProductos' => number_format($totalDetalle->totalProductos, 2,'.',', '),
				 'totalServicios' => number_format($totalDetalle->totalServicios, 2,'.',', ') ];
		

	}

	public function reporteVentasContadoCredito ($tipo) {
		if ($tipo == 'dia') {
			$ff = date('Y-m-d');
			$fi = date('Y-m-d', strtotime('-6 days'));
			$band = 0;
		} elseif ($tipo == 'semana') {
			$ff = date('Y-m-d',strtotime('monday -1 week'));
			$fi = date('Y-m-d', strtotime('monday -7 weeks'));
			$band = 1;
		} else {
			$ff = date('Y-m-d');
			$fi = date('Y-m-d', strtotime('-6 months'));
			$band = 2;
		}
	
		$labels = [];
		$contador = 0; 
		$fiCont = date('Y', strtotime("$fi")).'-'.date('m', strtotime("$fi")) .'-01';
		
		do{
			if ($band == 0) {
				$fa = date('d/m/Y',strtotime("$fi + $contador days"));
				$labels[] = $fa;
			} elseif ($band == 1) {
				$fa = date('d/m/Y',strtotime("$fi + $contador weeks"));
				$labels[] = $fa;
			} else {
				if ($contador != 0) {
					$fa = date('Y-m-d', strtotime("$fiCont + $contador months"));
					$labels[] = $this->returnMes($fa);
				} else {
					$labels[] = $this->returnMes($fiCont);
				}
			}
			$contador++;
		} while(!(count($labels) == 7));


		// $ffF = \DateTime::createFromFormat('d/m/Y', $ff)->format('Y-m-d');
		$ventas = DB::table('venta as v')
				->where('v.situacion', '!=','A')
				->whereNull('v.deleted_at')
				->whereBetween(DB::Raw("DATE_FORMAT(v.created_at,'%Y-%m-%d')"),[$fi,$ff]);


		if ($band == 0) {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					  DB::Raw("DATE_FORMAT(v.created_at,'%d/%m/%Y') as fecha"), 'v.tipoPago');
					
		} elseif($band == 1) {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					DB::Raw("CONCAT(v.semanaActual,'-',YEAR(v.created_at)) as fecha"), 'v.tipoPago');		
		} else {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					DB::Raw("DATE_FORMAT(v.created_at,'%m-%Y') as fecha"), 'v.tipoPago');
		
		}
		$ventas = $ventas->groupBy('v.fecha', 'v.tipoPago')
						 ->orderBy('v.fecha')
						 ->get();

		/*if($band == 1) {
			foreach($labels as $lb) {

				$date = \DateTime::createFromFormat('d/m/Y', $lb);

				$a = $date->format('Y-m-d');
				#$a = date('Y-m-d', strtotime("$lb"));
				$c2 = date('W',strtotime($a));

				dd($lb, $a, $c2);
			}

			dd($ventas, $labels);
		}*/

		$datos = [];
		$datos2 = [];
		foreach ($labels as $lb) {
			$acum = 0;
			$acumC= 0;
			$acumD= 0;
			foreach ($ventas as $v) {
				$acum = 0;
				if ($band == 2) {
					if ($v->fecha == $this->returnMesNum($lb)) {
						$acum+=$v->total;
					}
				} elseif ($band == 1) {
					$const = explode('-',$v->fecha);
					
					// $a = \DateTime::createFromFormat('d/m/Y', $lb)->format('Y-m-d');
					// dd($a, $lb);
					// $a = date('Y-m-d', strtotime("$lb"));
					$dateAux = \DateTime::createFromFormat('d/m/Y', $lb);
					$a = $dateAux->format('Y-m-d');
					$c2 = date('W',strtotime($a));
					if ($const[0] == $c2) {
						$acum+=$v->total;
					}
				} else {
					if ($v->fecha == $lb) {
						$acum+=$v->total;
					}
				}
				
				if ($v->tipoPago == 'C') {
					$acumC += $acum;
				} else {
					$acumD += $acum;	
				}
			}

			// if (!$bandE) {
				$datos[]  = number_format($acumC, 2,'.','');
				$datos2[] = number_format($acumD, 2,'.','');
			// }
		}
	
		return ['labels' => $labels, 'datosContado' => $datos, 'datosCredito' => $datos2];

	}

	public function generateColor () {
		$rojo = rand(0, 255);
		$verde = rand(0, 255);
		$azul = rand(0, 255);

		$color = sprintf("#%02x%02x%02x", $rojo, $verde, $azul);

		return $color;
	}
 
	public function reporteVentasPorUN ($tipo) {
		if ($tipo == 'dia') {
			$ff = date('Y-m-d');
			$fi = date('Y-m-d', strtotime('-6 days'));
			$band = 0;
		} elseif ($tipo == 'semana') {
			$ff = date('Y-m-d',strtotime('monday -1 week'));
			$fi = date('Y-m-d', strtotime('monday -7 weeks'));
			$band = 1;
		} else {
			$ff = date('Y-m-d');
			$fi = date('Y-m-d', strtotime('-6 months'));
			$band = 2;
		}
	
		$labels = [];
		$contador = 0; 
		$fiCont = date('Y', strtotime("$fi")).'-'.date('m', strtotime("$fi")) .'-01';
		
		do{
			if ($band == 0) {
				$fa = date('d/m/Y',strtotime("$fi + $contador days"));
				$labels[] = $fa;
			} elseif ($band == 1) {
				$fa = date('d/m/Y',strtotime("$fi + $contador weeks"));
				$labels[] = $fa;
			} else {
				if ($contador != 0) {
					$fa = date('Y-m-d', strtotime("$fiCont + $contador months"));
					$labels[] = $this->returnMes($fa);
				} else {
					$labels[] = $this->returnMes($fiCont);
				}
			}
			$contador++;
		} while(!(count($labels) == 7));


		// $ffF = \DateTime::createFromFormat('d/m/Y', $ff)->format('Y-m-d');
		$ventas = DB::table('venta as v')
				->where('v.situacion', '!=','A')
				->whereNull('v.deleted_at')
				->whereBetween(DB::Raw("DATE_FORMAT(v.created_at,'%Y-%m-%d')"),[$fi,$ff]);

		if ($band == 0) {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					DB::Raw("DATE_FORMAT(v.created_at,'%d/%m/%Y') as fecha"), 'v.serie');
					
		} elseif($band == 1) {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					DB::Raw("CONCAT(v.semanaActual,'-',YEAR(v.created_at)) as fecha"), 'v.serie');		
		} else {
			$ventas = $ventas->select(DB::Raw("SUM(v.total*v.tipoCambio) as total"),
					DB::Raw("DATE_FORMAT(v.created_at,'%m-%Y') as fecha"), 'v.serie');
		
		}
		$ventas = $ventas->groupBy('v.fecha', 'v.serie')
						 ->orderBy('v.fecha')
						 ->orderBy('v.serie')
						 ->get();

		$acumSerie = [];
		$ykeys = [];
		$labelsR = [];
		$colors = [];

		$series = DB::table('seriedocumento')
					->select('serie')
					->distinct('serie')
					->get();
		// dd($labels);
		foreach($series as $iserie) {
			$color = $this->generateColor();
			$ykeys[] = strval($iserie->serie);
			foreach ($labels as $lb) {
				$acumSerie[strval($iserie->serie)][$lb] = 0;
			}

			switch ($iserie->serie) {
				case 11: $label = 'Neumáticos'; break;
				case 12: $label = 'Mostrador'; break;
				case 13: $label = 'Baterías'; break;
				case 14: $label = 'Servicios de Taller'; break;
				case 15: $label = 'Otros Ingresos'; break;
				case 16: $label = 'Vehículos VW y Otros'; break;
				case 17: $label = 'Vehículos Chevrolet e Isuzu'; break;
				case 18: $label = 'PostVenta Chevrolet e Isuzu'; break;
				case 19: $label = 'Planchado y Pintura'; break;
				default:
					$label = $iserie->serie;
					break;
			}
			$labelsR[] = $label;
			$colors[] = $color;
		}
		// dd($ventas, $colors);
		// dd($acumSerie, $labels);
		foreach ($labels as $lb) {
			$acum = 0;
			foreach ($ventas as $v) {
				// dd($v);
				$key = $v->serie;
				$acum = 0;
				$auxLabel = null;
				if ($band == 2) {
					if ($v->fecha == $this->returnMesNum($lb)) {
						$auxLabel = $lb;
						$acum+=$v->total;
					}
				} elseif ($band == 1) {
					$const = explode('-',$v->fecha);
					// echo var_dump($const);
					// dd($lb);
					// $a = \DateTime::createFromFormat('d/m/Y', $lb)->format('Y-m-d');
					// dd($a, $lb);
					// $auxLbl = explode('/', $lb);
					// $a = date('Y-m-d', strtotime("$lb"));
					$dateAux = \DateTime::createFromFormat('d/m/Y', $lb);
					$a = $dateAux->format('Y-m-d');
					$c2 = date('W',strtotime($a));
					// dd($a, $c2, $const);
					if ($const[0] == $c2) {
						$auxLabel = $lb; //$const[0];
						$acum+=$v->total;
					}
					// dd($auxLabel);
				} else {
					if ($v->fecha == $lb) {
						$auxLabel = $v->fecha;
						$acum+=$v->total;
					}
				}
				
				// dd($acumSerie);
				if (!is_null($auxLabel)) {
					// echo "key: $key, auxLabel: $auxLabel <br>";
					$acumSerie[strval($key)][$auxLabel] += $acum;
				}
			}
		}
		// echo "OK";
		// exit;
	
		// $labelsR = $result['labels'];
		// $acumsR  = $result['acumSeries'];

		// YA NO VA - SE CAMBIA REPORTE
		// $arrGeneral = [];
		// foreach($labels as $item) {
		// 	$temp = [];
		// 	foreach($acumSerie as $key => $value) {
		// 		// $temp['label'] = $item;
		// 		// dd($acumSerie[$key], $acumSerie[$key][$item], $key, $item);
		// 		foreach($acumSerie[$key] as $key2 => $value2) {
		// 			// dd($key2, $value2);
		// 			if($key2 == $item) {
		// 				// dd($acumsR[$key], $item);
		// 				$temp[$key] = $value2;
		// 			}
		// 		}
		// 	}
		// 	// dd($temp);
		// 	$arrGeneral[] = $temp;
		// 	// echo var_dump($temp);
		// 	// $arrGeneral[] = $temp;
		// 	// dd($temp);
		// }
		
		$data = [];
		foreach($ykeys as $item) {
			foreach($acumSerie as $key => $value) {
				// echo "key: $key, $item: $item <br>";
				if ($item == $key) {
					foreach ($value as $iterador) {
						$data[$key][] = $iterador; 
					}
				}
			}
		}
		return ['labels' => $labels, 'labelsR' => $labelsR, 'data' => $data, 'ykeys' => $ykeys, 'colors' => $colors]; 
		// return ['ykeys' => $ykeys, 'data' => $arrGeneral, 'labels' => $labelsR, 'colors' => $colors];

	}

	public function getStocksProducto () {
		$productos = DB::table('producto as prod')
			->join('stockproducto as st','st.idProducto', '=', 'prod.id')
			->whereNull('prod.deleted_at')
			->select(DB::Raw("(CASE prod.tipoProducto 
			WHEN 'A'  THEN 'Accesorio/Repuesto' 
			WHEN 'LL' THEN 'Neumáticos' 
			WHEN 'I'  THEN 'Insumos' 
			WHEN 'B'  THEN 'Baterías' 
			ELSE 'Muelles' END) as tipo"), 
			DB::Raw("SUM(st.totalCompras-st.totalVentas-st.totalIncidencias) as stock"))
			->groupBy('tipo')
			->get();

		$labels = [];
		$datos  = [];
		$colors = [];
		foreach ($productos as $prod) {
			$colors[] = $this->generateColor();
			$labels[] = $prod->tipo;
			$datos[]  = number_format($prod->stock,2,'.','');
		}

		return ['labels' => $labels, 'datos' => $datos, 'colors' => $colors];	
	} 

	public function getReporteReclamos () {
		$ff = date('Y-m-d');
		$fi = date('Y-m-d', strtotime("$ff - 2 months"));
		$contador = 0;
		$fiCont = date('Y', strtotime("$fi")).'-'.date('m', strtotime("$fi")) .'-01';
		do{
			if ($contador != 0) {
				$fa = date('Y-m-d', strtotime("$fiCont + $contador months"));
				$labels[] = $this->returnMes($fa);
			} else {
				$labels[] = $this->returnMes($fiCont);
			}
			$contador++;
		} while(!(count($labels) == 3));
	
		// dd($fiCont, $ff, $labels);

		$reclamos = DB::table('reclamo as r')
				->whereNull('r.deleted_at')
				->whereBetween('r.fecha', [$fi, $ff])
				->select(
					DB::Raw("(CASE r.situacion WHEN 'A' THEN 'PENDIENTES DE SOLUCION' ELSE 'CERRADOS' END) as situacion"), 
					DB::Raw("COUNT(r.situacion) as cantidad"),
					DB::Raw("DATE_FORMAT(r.created_at,'%m-%Y') as fecha"))
				->groupBy('situacion', 'fecha')
				->orderBy('fecha')
				->get();

		// $labels = [];
		$datos  = [];
		$datos2  = [];
		$colors = [];
		
		foreach($labels as $lb) {
			$acum = 0;
			$acumC = 0;
			$acumP = 0;
			// dd($reclamos, $lb);
			foreach ($reclamos as $prod) {
				$acum = 0;
				if ($prod->fecha == $this->returnMesNum($lb)) {
					$acum+=$prod->cantidad;
				}

				if ($prod->situacion == 'CERRADOS') {
					$acumC +=$acum;
				} else {
					$acumP += $acum;
				}
			}
			$datos[]  = number_format($acumC, 2,'.','');
			$datos2[] = number_format($acumP, 2,'.','');
		
		}
		$colors[] = $this->generateColor();
		$colors[] = $this->generateColor();

		return ['labels' => $labels, 'datos1' => $datos, 'datos2'=>$datos2, 'colors' => $colors];	
	}

	public function getReporteProspectos () {
		$ff = date('Y-m-d');
		$fi = date('Y-m-d', strtotime("$ff - 5 months"));

		$prospectos = DB::table('prospecto as pr')
				// ->whereNull('pr.deleted_at')
				->whereBetween(DB::Raw("DATE_FORMAT(pr.created_at,'%Y-%m-%d')"), [$fi, $ff])
				->select(
					DB::Raw("(CASE WHEN pr.situacion = 'N' AND pr.deleted_at IS NULL THEN 'REGISTRADOS' WHEN pr.situacion = 'C' AND pr.deleted_at IS NULL THEN  'CONVERTIDOS A OPORTUNIDAD' ELSE 'ANULADOS' END) as situacion"),
					DB::Raw("COUNT(pr.situacion) as cantidad"))
				->groupBy('situacion', 'pr.deleted_at')
				->get();

		// $labels = [];
		$datos  = [];
		
		foreach ($prospectos as $prod) {
			$datos[] = ['label' => $prod->situacion, 'value' => $prod->cantidad];
		}
		
		return ['data' => $datos];	
	}
	
	##################################################################
	#	REPORTES GRAFICOS PARA AUTOS
	#
	##################################################################


	public function getReporteAutoInicio(Request $request) {
		$dataOportunidades = $this->getReporteOportunidades();
		$dataCertezaOportunidades = $this->getReporteCertezaOportunidades();
		$dataOportunidadesAvance = $this->getReporteOportunidadesAvance();
		$dataReporteConversiones = $this->getReporteConversiones();

		// dd($dataReporteConversiones);
		return ['dataOportunidades' => $dataOportunidades, 
				'dataCertezaOportunidades' => $dataCertezaOportunidades , 
				'dataOportunidadesAvance' => $dataOportunidadesAvance,
				'dataReporteConversiones' => $dataReporteConversiones];
	}

	public function getReporteOportunidades () {
		$fi = date('Y-m-d'); //'2024-05-01';
		$ff = date('Y-m-d', strtotime('+6 months'));
	
		$labels = [];
		$labels[] = "Vencidos";
		$contador = 0; 
		$fiCont = date('Y', strtotime("$fi")).'-'.date('m', strtotime("$fi")) .'-01';
		
		do{
			if ($contador != 0) {
				$fa = date('Y-m-d', strtotime("$fiCont + $contador months"));
				$labels[] = $this->returnMes($fa);
			} else {
				$labels[] = $this->returnMes($fiCont);
			}
			$contador++;
		} while(!(count($labels) == 7));

		$labels[] = "Futuros";
	
		$data = DB::select("CALL getEstimacionOportunidades(?,?)",[$fi, $ff]);

		$dataT = DB::table('trabajador as t')
				->where('t.idCategoriaPersonal', '15')
				#->whereNull('t.deleted_at')
				->select('t.id', DB::Raw("CONCAT(t.nombres,' ', t.apellidos) as asesor"))
				->get();
		
		$acumuladores = [];
		$ykeys = [];
		$labelsR = [];
		$colors = [];

		foreach($dataT as $item) {
			$color = $this->generateColor();
			$ykeys[] = strval($item->id);

			foreach ($labels as $lb) {
				$acumuladores[strval($item->id)][$lb] = 0;
			}

			$labelsR[] = $item->asesor;
			$colors[] = $color;
		}

		#dd($colors, $labels, $acumuladores, $data);
    
		foreach ($labels as $lb) {
			foreach ($data as $v) {
				$key = $v->id;
				$acum = 0;
				$auxLabel = null;
				// echo "lb: $lb || status: $v->status || periodo: $v->periodo <br>";
				// if (in_array($lb, ['Vencidos','Futuros']) /*&& in_array($v->status,['V', 'F'])*/) {
				// 	if (!is_null($v->monto) && $v->status != 'A') {
				// 		$acum+=$v->monto;
				// 	}
				// } 
				
				
				if (!in_array($lb, ['Vencidos', 'Futuros'])) {
					// dd($v->periodo, $this->returnMesNum($lb), $v);
					if ($v->periodo == $this->returnMesNum($lb)) {
						if (!is_null($v->monto)) {
							$acum = ($v->status == 'A'?$v->monto:0);
						}
					}
				} else {
					if (!is_null($v->monto) && $v->status != 'A') {
						$acum+=$v->monto;
					}
				}


				$auxLabel = $lb;

				// dd($acumSerie);
				if (!is_null($auxLabel)) {
					// echo "key: $key, auxLabel: $auxLabel <br>";
					$acumuladores[strval($key)][$auxLabel] += $acum;
				}
			}
		}

		$dataResult = [];
		foreach($ykeys as $item) {
			foreach($acumuladores as $key => $value) {
				// echo "key: $key, $item: $item <br>";
				if ($item == $key) {
					foreach ($value as $iterador) {
						$dataResult[$key][] = $iterador; 
					}
				}
			}
		}

		// dd($acumuladores, $labels, $labelsR, $data);
		return ['labels' => $labels, 'labelsR' => $labelsR, 'data' => $dataResult, 'list' => $data, 'ykeys' => $ykeys, 'colors' => $colors]; 
	
	}

	public function getReporteCertezaOportunidades () {
		$fi = date('Y-m-d'); //'2024-05-01'; //
		$ff = date('Y-m-d', strtotime('+6 months'));
	
		$labels = [];
		$labels[] = "Vencidos";
		$contador = 0; 
		$fiCont = date('Y', strtotime("$fi")).'-'.date('m', strtotime("$fi")) .'-01';
		
		do{
			if ($contador != 0) {
				$fa = date('Y-m-d', strtotime("$fiCont + $contador months"));
				$labels[] = $this->returnMes($fa);
			} else {
				$labels[] = $this->returnMes($fiCont);
			}
			$contador++;
		} while(!(count($labels) == 7));

		$labels[] = "Futuros";
	
		$data = DB::select("CALL getEstimacionCertezaOportunidades(?,?)",[$fi, $ff]);

		$dataT = [
			['id' => 'P' , 'certeza'=> 'Baja', 'color' => 'red'],
			['id' => 'M' , 'certeza'=> 'Media', 'color' => 'yellow'],
			['id' => 'I' , 'certeza'=> 'Alta', 'color' => 'green']
		];
		
		$acumuladores = [];
		$ykeys = [];
		$labelsR = [];
		$colors = [];

		foreach($dataT as $item) {
			$ykeys[] = strval($item['id']);

			foreach ($labels as $lb) {
				$acumuladores[strval($item['id'])][$lb] = 0;
			}

			$labelsR[] = $item['certeza'];
			$colors[] = $item['color'];
		}

		// dd($colors, $labels, $acumuladores, $data);
    
		foreach ($labels as $lb) {
			foreach ($data as $v) {
				$key = $v->certeza;
				$acum = 0;
				$auxLabel = null;
				// echo "lb: $lb || status: $v->status || periodo: $v->periodo <br>";
				// if (in_array($lb, ['Vencidos','Futuros']) /*&& in_array($v->status,['V', 'F'])*/) {
				// 	if (!is_null($v->monto) && $v->status != 'A') {
				// 		$acum+=$v->monto;
				// 	}
				// } 
				
				
				if (!in_array($lb, ['Vencidos', 'Futuros'])) {
					// dd($v->periodo, $this->returnMesNum($lb), $v);
					if ($v->periodo == $this->returnMesNum($lb)) {
						if (!is_null($v->monto)) {
							$acum = ($v->status == 'A'?$v->monto:0);
						}
					}
				} else {
					if (!is_null($v->monto) && $v->status != 'A') {
						$acum+=$v->monto;
					}
				}


				$auxLabel = $lb;

				// dd($acumSerie);
				if (!is_null($auxLabel)) {
					// echo "key: $key, auxLabel: $auxLabel <br>";
					$acumuladores[strval($key)][$auxLabel] += $acum;
				}
			}
		}

		$dataResult = [];
		foreach($ykeys as $item) {
			foreach($acumuladores as $key => $value) {
				// echo "key: $key, $item: $item <br>";
				if ($item == $key) {
					foreach ($value as $iterador) {
						$dataResult[$key][] = $iterador; 
					}
				}
			}
		}

		// dd($acumuladores, $labels, $labelsR, $data);
		return ['labels' => $labels, 'labelsR' => $labelsR, 'data' => $dataResult, 'list' => $data, 'ykeys' => $ykeys, 'colors' => $colors]; 
	
	}

	public function getReporteOportunidadesAvance () {
		
		$labels = ["Cotizado", "En Negociación", "Pago Inicial"];
		
	
		$data = DB::select("CALL getEstimacionOportunidadesAvance()");

		$dataT = DB::table('trabajador as t')
				->where('t.idCategoriaPersonal', '15')
				#->whereNull('t.deleted_at')
				->select('t.id', DB::Raw("CONCAT(t.nombres,' ', t.apellidos) as asesor"))
				->get();
		
		$acumuladores = [];
		$ykeys = [];
		$labelsR = [];
		$colors = [];

		foreach($dataT as $item) {
			$color = $this->generateColor();
			$ykeys[] = strval($item->id);

			foreach ($labels as $lb) {
				$acumuladores[strval($item->id)][$lb] = 0;
			}

			$labelsR[] = $item->asesor;
			$colors[] = $color;
		}

		// dd($colors, $labels, $acumuladores, $data);
    
		foreach ($labels as $lb) {
			foreach ($data as $v) {
				$key = $v->id;
				$acum = 0;
				// echo "lb: $lb || status: $v->status || periodo: $v->periodo <br>";
				// if (in_array($lb, ['Vencidos','Futuros']) /*&& in_array($v->status,['V', 'F'])*/) {
				// 	if (!is_null($v->monto) && $v->status != 'A') {
				// 		$acum+=$v->monto;
				// 	}
				// } 
				$auxLbl = $v->fase == 'C'?'Cotizado':($v->fase == 'N'?'En Negociación': 'Pago Inicial');
				
				if ($lb == $auxLbl) {
					if (!is_null($v->cantidad)) {
						$acum = $v->cantidad;
					}
				}


				$auxLabel = $lb;

				// dd($acumSerie);
				if (!is_null($auxLabel)) {
					// echo "key: $key, auxLabel: $auxLabel <br>";
					$acumuladores[strval($key)][$auxLabel] += $acum;
				}
			}
		}

		$dataResult = [];
		foreach($ykeys as $item) {
			foreach($acumuladores as $key => $value) {
				// echo "key: $key, $item: $item <br>";
				if ($item == $key) {
					foreach ($value as $iterador) {
						$dataResult[$key][] = $iterador; 
					}
				}
			}
		}

		// dd($acumuladores, $labels, $labelsR, $data);
		return ['labels' => $labels, 'labelsR' => $labelsR, 'data' => $dataResult, 'list' => $data, 'ykeys' => $ykeys, 'colors' => $colors]; 
	
	}

	public function getReporteConversiones () {
		
		$labelsList = ["Prospectos", "Conversión", "Oportunidades"];
		$labels = ["Prospectos", "Oportunidades"];
		
		$periodo = date('Y-m'); //'2024-05';//
		#dd($periodo);
		$data = DB::select("CALL getVentasConversiones(?)", [$periodo]);

		$dataT = DB::table('trabajador as t')
				->where('t.idCategoriaPersonal', '15')
				#->whereNull('t.deleted_at')
				->select('t.id', DB::Raw("CONCAT(t.nombres,' ', t.apellidos) as asesor"))
				->get();
		
		$acumuladores = [];
		$ykeys = [];
		$labelsR = [];
		$colors = [];

		foreach($dataT as $item) {
			$color = $this->generateColor();
			$ykeys[] = strval($item->id);

			foreach ($labels as $lb) {
				$acumuladores[strval($item->id)][$lb] = 0;
			}

			$labelsR[] = $item->asesor;
			$colors[] = $color;
		}

		// dd($colors, $labels, $acumuladores, $data);
    
		foreach ($labels as $lb) {
			foreach ($data as $v) {
				$key = $v->id;
				$acum = 0;
				// echo "lb: $lb || status: $v->status || periodo: $v->periodo <br>";
				// if (in_array($lb, ['Vencidos','Futuros']) /*&& in_array($v->status,['V', 'F'])*/) {
				// 	if (!is_null($v->monto) && $v->status != 'A') {
				// 		$acum+=$v->monto;
				// 	}
				// } 
				// $auxLbl = $v->fase == 'C'?'Cotizado':($v->fase == 'N'?'En Negociación': 'Pago Inicial');
				
				// if ($lb == $auxLbl) {
				// 	if (!is_null($v->cantidad)) {
				// 		$acum = $v->cantidad;
				// 	}
				// }
				if ($lb == 'Prospectos') {
					$acum = $v->prospectos;
				} elseif($lb == 'Oportunidades') {
					$acum = $v->oportunidades;
				}

				$auxLabel = $lb;

				// dd($acumSerie);
				if (!is_null($auxLabel)) {
					// echo "key: $key, auxLabel: $auxLabel <br>";
					$acumuladores[strval($key)][$auxLabel] += $acum;
				}
			}
		}

		$dataResult = [];
		foreach($ykeys as $item) {
			foreach($acumuladores as $key => $value) {
				// echo "key: $key, $item: $item <br>";
				if ($item == $key) {
					foreach ($value as $iterador) {
						$dataResult[$key][] = $iterador; 
					}
				}
			}
		}

		// dd($acumuladores, $labels, $labelsR, $data);
		return ['labels' => $labels, 'labelsR' => $labelsR, 'labelsList' => $labelsList, 'data' => $dataResult, 'list' => $data, 'ykeys' => $ykeys, 'colors' => $colors]; 
	
	}

}
