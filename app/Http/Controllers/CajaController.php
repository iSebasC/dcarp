<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Forma;
// use App\Models\CategoriaProducto;
use App\Models\Acabado;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\UnidadMedida;
use App\Models\Local;
use App\Models\StockProducto;
use App\Models\Persona;
use App\Models\Serie;
use App\Models\MovimientoCaja;
use App\Models\DetalleMovimientoCaja;
use App\Models\ReciboCaja;
use App\Models\Cuenta;
use App\Models\TipoCambio;

use App\Libraries\Funciones;

use DB;
use Validator;
use Auth;
use Fpdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;


class CajaController extends Controller
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
    
    public function getCaja ($tiendaId, Request $request) {
        $id = Auth::user()->usuarioId;
        $caja =  MovimientoCaja::where('idTienda','=',$tiendaId)
                ->where('idUsuario','=',$id)
                ->whereNull('saldoCierre')
                ->orderBy('fecha','DESC')
                ->first();

        if (!is_null($caja)) {
            $efectivo = Venta::where('idMovimiento','=',$caja->id)
                        ->where('metodoPago','Efectivo') 
                        // ->where('tipoPago','=','C')
                        ->sum('total');

            $efectivoMov = DetalleMovimiento::where('idMovimiento','=',$caja->id)
                            ->where('tipo','=','I')
                            ->sum('monto');

            $egresosMov = DetalleMovimiento::where('idMovimiento','=',$caja->id)
                            ->where('tipo','=','E')
                            ->sum('monto');
                        
            return ['caja' => $caja, 'saldo' => $efectivo+$efectivoMov-$egresosMov, 'estado' => true];
        } else {
            return ['estado' => false];
        }
    }

    public function getCajaAbierta (Request $request) {
        $id = Auth::user()->usuarioId;
        $caja =  MovimientoCaja::where('idUsuario','=',$id)
                ->whereNull('saldoCierre')
                ->orderBy('fecha','DESC')
                ->first();

        if (!is_null($caja)) {
            return ['abierta' => true, 'id' => $caja->id];
        } else {
            return ['abierta' => false , 'id' => ''];
        }
    }

    public function buscar ($id, Request $request) {
        if (!is_null($det)) {
            return array('detalles'=> $det,'ingresos'=> number_format($ingresos,2,'.',''), 'egresos' => number_format($egresos,2,'.',''),'neto' => number_format($efectivo-$egresos,2,'.',''), 'efectivo' => number_format($efectivo,2,'.',''), 'depositos' => number_format($depositos,2,'.',''), 'estado' => true);
        } else {
            return array('estado' => false);
        }
    }

    public function obtenerCaja ($id, Request $request) {
        $mov = MovimientoCaja::find($id);

        $ingresos02   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->sum('monto');
        // $v_ingresos = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','PEN')->sum('total');
        // $v_ingresosD = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','USD')->sum('total');

        // $ingresos = $ingresos02+$v_ingresos+$mov->saldoApertura;
        $egresos  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','E')->where('tipopago','E')->where('moneda', 'PEN')->sum('monto');
        $egresosD  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','E')->where('tipopago','E')->where('moneda', 'USD')->sum('monto');
       
        $efectivo = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
        $efectivoD = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
        
        $ingresos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','I')->where('moneda', 'PEN')->sum('monto');
        $ingresos02D = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','I')->where('moneda', 'USD')->sum('monto');
        
        $efectivo+=$ingresos02+$mov->saldoApertura;
        $efectivoD += $ingresos02D;
        // $depositos = Venta::where('tipoPago','=','D')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
        // $depositosD = Venta::where('tipoPago','=','D')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
        // $depositos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','D')->where('tipo','=','I')->sum('monto');
        
        // $depositos +=$depositos02;
        

        $neto = $efectivo - $egresos;
        $netoD = $efectivoD - $egresosD;

        $caja = $mov;

        $caja->saldoCierre = number_format($neto,2,'.','');
        $caja->saldoCierreD = number_format($netoD,2,'.','');
        
        $local = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
                        ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
						->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
                        ->where('local.tipo','=','T')
                        ->where('id','=',$caja->idTienda)
                        ->select(DB::raw("CONCAT(local.direccion,' - ', dep.nombre) as label"),'local.id as value')
                        ->first();
                        
        return ['caja' => $caja,'local' => $local];
    }

    public function cerrarCaja (Request $request) {
        // dd($request);
        
        DB::beginTransaction();
        $band = true;
        try{
            $mov = MovimientoCaja::find($request->get('cajaId'));
            $params = $this->obtenerCaja($mov->id, $request);
            $s = $params['caja'];
          
            if ($s->saldoCierre == $request->get('saldo') && $s->saldoCierreD == $request->get('saldoD')) {
                $mov->saldoCierre = $request->get('saldo');
                $mov->saldoCierreD = $request->get('saldoD');
                $mov->fechaCierre = date('Y-m-d H:i:s');
                $mov->update();
                $errors[] = 'Caja Cerrada Correctamente';
            } else {
                $errors[] = 'No se Puede Cerrar Caja, No manipule los Datos.';
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

    public function aperturarCaja (Request $request) {
        $errors = $this->validarApertura($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
		    DB::beginTransaction();
            $band = true;
            try{
                $id = Auth::user()->usuarioId;

                $mov = MovimientoCaja::where('idTienda','=',$request->get('tiendaId'))
                    ->where('idUsuario','=',$id)
                    ->whereNull('saldoCierre')
                    ->first();
                
                if (is_null($mov)) {
                    $a = new MovimientoCaja;
                    $a->idUsuario = $id;
                    $a->fecha = date('Y-m-d');
                    $a->saldoApertura = $request->get('saldo');
                    $a->idTienda = $request->get('tiendaId');
                    $a->save();

                    $errors[] = 'Caja Aperturada Correctamente';
                } else {
                    $errors[] = 'No se Puede Aperturar Caja.';
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
	}

    public function repArqueo (Request $request) {
        $usuario 	 = $request->get('usuario');
		
		$fechaI 	 = $request->get('fechai');
    	$fechaF	 	 = $request->get('fechaf');

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$cotizacion = DB::table('movimientocaja as mc')
					  ->leftjoin('trabajador as cl','cl.id','=','mc.idUsuario')
					  ->where(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$usuario. '%')
                      ->whereNotNull('mc.fechaCierre');

    	if ($fechaI != '') {
			$cotizacion = $cotizacion->where('mc.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$cotizacion = $cotizacion->where('mc.fecha','<=',$fechaF);
		}

        $cotizacion =  $cotizacion->select('mc.id',
            DB::Raw("DATE_FORMAT(mc.fecha,'%d/%m/%Y') as fechaApertura"),
            DB::Raw("ROUND(mc.saldoApertura,2) as apertura"),
            DB::Raw("ROUND(mc.saldoCierre,2) as cierre"),
            DB::Raw("ROUND(mc.saldoCierreD,2) as cierreDolar"),
            DB::Raw("DATE_FORMAT(mc.fechaCierre,'%d/%m/%Y') as fechaCierre"),
            DB::Raw("DATE_FORMAT(mc.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaSistema"),
            DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres) as usuario")    
        );
		
		$lista = $cotizacion->orderBy('mc.created_at')->get();
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
		
		$lista = $cotizacion->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

    	return ['arqueos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Caja Cerrada':' Cajas Cerradas'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    
    }

    public function getAll (Request $request) {
    	$filtro 	 = $request->get('filtro');
        $id          = $request->get('cajaId');
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
        
        $ingresos02 = 0;
        $v_ingresos = 0;
        $ingresos = 0;
        $egresos  = 0;
        $egresosD = 0;
        $efectivo = 0;
        $tarjeta = 0;
        $depositos = 0;
        $cantidad = 0;
        
        $ingresosD = 0;
        $efectivoD = 0;
        $depositosD = 0;
        $tarjetaD = 0;
               
        if ($id != '') {
            $mov = MovimientoCaja::find($id);
            $movApertura = MovimientoCaja::where('id',$id)
                          ->select(DB::Raw("'APERTURA DE CAJA' as concepto"), DB::Raw("ROUND(saldoApertura,2) as total"), 
                          DB::Raw("'I' as tipo"), DB::Raw("'0' as id"), DB::Raw("'A' as tipopago"),'created_at',
                          DB::Raw("'PEN' as moneda"), DB::Raw("'Efectivo' as metodoPago"));

            $ventas = Venta::leftJoin('persona as cl','cl.id','=','venta.idCliente')
                        ->where('venta.idMovimiento','=',$id);
                        
            $detallesCaja = DetalleMovimientoCaja::where('idMovimiento','=',$id);

            if ($filtro != '') {
                if ($filtro != 'I') {
                    $ventas = $ventas->where('tipoComprobante','=',$filtro);
                }
                $detallesCaja = $detallesCaja->where('tipo','=',$filtro);
            }

            $ventas  = $ventas->select(DB::Raw("CONCAT((CASE WHEN venta.tipoComprobante = 'B' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END), ', REF. ', venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as concepto"),
            DB::Raw("ROUND(venta.total,2) as total"), DB::raw("'I' as tipo"), DB::raw("'0' as id"),'venta.tipoPago as tipopago', 'venta.created_at', 'venta.tipoMoneda as moneda','venta.metodoPago');

            $detallesCaja = $detallesCaja->select('descripcion as concepto',DB::Raw("ROUND(monto,2) as total"),'tipo','id','tipopago', 'created_at','moneda', DB::Raw("(CASE tipopago WHEN 'E' THEN 'Efectivo' ELSE 'Depósito' END) as metodoPago"));

            $query  =  $detallesCaja->unionAll($ventas)
                        ->unionAll($movApertura);
        
            $querySql	= $query->toSql();
            // $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

            $lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))
            ->mergeBindings($query->getQuery());
            // $l = DB::query($lista);
            $lista = $lista->get()->toArray();
            $cantidad = count($lista);

            $ingresos02   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','PEN')->sum('monto');
            $ingresos02D   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','USD')->sum('monto');
            $v_ingresos = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','PEN')->sum('total');
            $v_ingresosD = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','USD')->sum('total');

            $ingresos = $ingresos02+$v_ingresos+$mov->saldoApertura;
            $ingresosD = $v_ingresosD + $ingresos02D;
  
            $egresos  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('tipopago','E')->where('moneda','PEN')->sum('monto');
            $egresosD  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('tipopago','E')->where('moneda','USD')->sum('monto');
            
            $efectivo = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $efectivoD = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            $ingresos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','=','I')->sum('monto');
            $efectivo+=$ingresos02+$mov->saldoApertura;
        
            $depositos = Venta::where('metodoPago','Depósito')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $depositosD = Venta::where('metodoPago','Depósito')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            $depositos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','D')->where('tipo','I')->where('moneda','PEN')->sum('monto');
            $depositos02D = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','D')->where('tipo','I')->where('moneda','USD')->sum('monto');
           
            $tarjeta = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $tarjetaD = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
        
            $depositos +=$depositos02;
            $depositosD += $depositos02D;
        
        }

        if ($cantidad > 0) {
            $paginador = new Funciones();
            $paramPaginador = $paginador->generarPaginacion($lista, $page, $filas);
            $arrPag = $paramPaginador['cadenapaginacion'];
            $page = $paramPaginador['nuevapagina'];
            $inicio = $paramPaginador['inicio'];
            $fin = $paramPaginador['fin'];
            $paramInicio = $paramPaginador['inicioArr'];
            $paramFin = $paramPaginador['finArr'];

            $lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))
            ->mergeBindings($query->getQuery());
            // $l = DB::query($lista);
            $lista = $lista->offset(($page-1)*$filas)
                    ->limit($filas)
                    ->get()
                    ->toArray();
        } else {
            $arrPag = [['opc' => '1', 'bloqueado' => true]];
            $page = '1';
            $inicio = '1';
            $fin = '1';
            $paramInicio = '1';
            $paramFin = '1';
            $lista = [];
        }
             
        return ['detalles' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Movimiento':' Movimientos'), 
        'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin, 
        'ingresos'=> number_format($ingresos,2,'.',''), 'ingresosD'=> number_format($ingresosD,2,'.',''), 
        'egresos' => number_format($egresos,2,'.',''), 'egresosD' => number_format($egresosD,2,'.',''),
        'neto' => number_format($efectivo-$egresos,2,'.',''), 'netoD' => number_format($efectivoD-$egresosD,2,'.',''), 
        'efectivo' => number_format($efectivo,2,'.',''), 'efectivoD' => number_format($efectivoD,2,'.',''), 
        'tarjeta' => number_format($tarjeta,2,'.',''), 'tarjetaD' => number_format($tarjetaD,2,'.',''), 
        'depositos' => number_format($depositos,2,'.',''), 'depositosD' => number_format($depositosD,2,'.','') ];

    

    } 

	public function guardarVenta(Request $request) {
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				$venta = new Venta;
				$venta->fecha = $request->get('fecha');
				$venta->subTotal = $request->get('subtotalDoc');
				$venta->igv = $request->get('igvDoc');
				$venta->total = $request->get('totalDoc');
				$venta->idTienda = $request->get('tiendaId');
				$venta->idAlmacenSalida = $request->get('almacenId');
				$venta->tipoComprobante = $request->get('tipodocumento');
				$venta->tipoPago = $request->get('tipopago');
				$cliente = Persona::where('documento','=',$request->get('documento'))
							->where('tipoPersona','=','C')
							->where('tipoDocumento','=',($request->get('tipodocumento') == 'B'?'PN':'PJ'))
							->select('id')
							->first();
				$venta->idCliente = $cliente->id;

				$serie = Serie::where('idLocal','=',$venta->idTienda)->where('tipoLocal','=','T')
						 ->where('tipoDocumento','=',$request->get('tipodocumento'))
						 ->first();
				$venta->serie = $serie->serie;
				$venta->numero = $serie->numero + 1;
				$venta->idPersonal = 1;
				$venta->save();
				$id = $venta->id;
			
				$serie->numero = $venta->numero;
				$serie->update();

				$detalles = explode(',',$request->get('listDetalles'));
					
				$i = 1;
				foreach ($detalles as $det) {
					$detalle = new DetalleVenta;
					$detalle->item = $i;
					$detalle->descripcion = $request->get('txtproducto'.$det);
					$detalle->cantidad = $request->get('txtcantidad'.$det);
					$detalle->precio = $request->get('txtprecio'.$det);
					$detalle->subTotal = $request->get('txtsubtototal'.$det);
					$detalle->idProducto = $request->get('productoid'.$det);
					$detalle->idVenta = $id;
					$detalle->save();

					$sp = StockProducto::where('idAlmacen','=',$ventas->idAlmacenSalida)
							->where('idProducto','=',$detalle->idProducto)
							->first();
					$sp->totalVentas = $sp->totalVentas + $detalle->cantidad;
					
					$sp->update();
					$i++;
				}
		
				$errors[] = 'Venta Registrada Correctamente';
			}catch(\Exception $ex){
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
            'fecha'=>  'required',
            'tipodocumento'=> 'required',
			'documento'=> 'required|numeric|digits_between:8,11',
			'tiendaId' => 'required',
			'almacenId' => 'required',
			'tipopago'=> 'required',
            'listDetalles'=>  'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric',
			'chk_declarar' => 'nullable'
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'tipodocumento.max'=> 'Indique Tipo de Documento',
            'documento.required'=> 'Indique Documento',
            'tiendaId.required'=> 'Indique Tienda',
            'almacenId.required'=> 'Indique Almacén',
            'tipopago.required'=> 'Indique Método de Pago',
		    'listDetalles.required'=> 'Indique Detalles a Venta',
			'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function guardarMovimiento (Request $request) {
        // dd($request);
        $errors = $this->validarMovimiento($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('cajaId');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
                // dd($request);
                $fa = date('Y-m-d');
				$tipoCambio = TipoCambio::where(
                    DB::Raw("DATE_FORMAT(fechaActualizacion, '%Y-%m-%d')"), '=',$fa)
                    ->select('factorCompra','factorVenta')->first();
                $recibo = new ReciboCaja;
                $recibo->tipoOperacion = $request->get('tipoperacion');
                $recibo->descripcion = $request->get('descripcion');
                $recibo->idPersonal = Auth::user()->usuarioId;
                if ($recibo->tipoOperacion == 'I') {
                    $recibo->formaIngreso = $request->get('formapago');
                    $recibo->tipoIngreso = $request->get('tipoIngreso');
                    $recibo->idCliente = $request->get('codcliente');
                    $recibo->tipoDocumento = $request->get('tipo_documento');
                    $recibo->serie = $request->get('serie');
                    $recibo->numero = $request->get('numero');
                    $recibo->total  = $request->get('total');
                    $recibo->moneda = $request->get('moneda');

                    if ($recibo->tipoIngreso == 'CC') {
                        $recibo->idCuenta = $request->get('codcuenta');
                        $recibo->tipopago = $request->get('tipopago');
                        $recibo->montopago = $request->get('monto');

                        $cuenta = Cuenta::find($recibo->idCuenta);
                        if (!is_null($cuenta)) {
                            if ($cuenta->saldo - $recibo->montopago >= 0) {
                                $cuenta->saldo = $cuenta->saldo - $recibo->montopago;
                                if ($cuenta->saldo == 0) {
                                    $cuenta->estado = 'C';
                                }
                                $cuenta->update();
                            } else {
                                $band = false;
                                $errors[] = 'Monto indicado no válido';
                            }
                        } else {
                            $band = false;
                            $errors[] = 'Cuenta por Cobrar no encontrada';
                        }
                    } else {
                        $recibo->montopago = $recibo->total;
                    }

                    $tipopagodet = $recibo->formaIngreso;
                    $descripcion = "INGRESO - COMP. ". $recibo->serie .'-'.$recibo->numero. " de Clte. " .$request->get('referencia');
                } elseif ($recibo->tipoOperacion == 'E') {
                    $recibo->tipoEgreso = $request->get('tipoEgreso');
                    $recibo->idProveedor = $request->get('codproveedor');
                    $recibo->tipoDocumento = $request->get('tipo_documento');
                    $recibo->tipoGasto = $request->get('tipogasto');
                    $recibo->serie = $request->get('serie');
                    $recibo->numero = $request->get('numero');
                    $recibo->total  = $request->get('total');
                    $recibo->moneda = $request->get('moneda');
                    $recibo->unidadNegocio = $request->get('unidadNegocio');
                    $recibo->partidaCuenta = $request->get('partida');
                    $recibo->medioPago = $request->get('medioPago');
                    $recibo->sustento = $request->get('sustento');
                    $recibo->idCuentaContable = $request->get('codcuentacontable');
                    $recibo->idPersonaAprueba = $request->get('codtrabajador');
                    $recibo->tipoCuentaEgreso = $request->get('tipo_egreso');

                    if ($recibo->tipoEgreso == 'CP') {
                        $recibo->idCuenta = $request->get('codcuenta');
                        $recibo->tipopago = $request->get('tipopago');
                        $recibo->montopago = $request->get('monto');

                        $cuenta = Cuenta::find($recibo->idCuenta);
                        if (!is_null($cuenta)) {
                            if ($cuenta->saldo - $recibo->montopago >= 0) {
                                $cuenta->saldo = $cuenta->saldo - $recibo->montopago;
                                if ($cuenta->saldo == 0) {
                                    $cuenta->estado = 'C';
                                }
                                $cuenta->update();
                            } else {
                                $band = false;
                                $errors[] = 'Monto indicado no válido';
                            }
                        } else {
                            $band = false;
                            $errors[] = 'Cuenta por Pagar no encontrada';
                        }
                    } else {
                        $recibo->montopago = $recibo->total;
                    }

                    $tipopagodet = $recibo->medioPago;
                    $descripcion = "EGRESO - COMP. ". $recibo->serie .'-'.$recibo->numero . " de Prov. " .$request->get('referencia');
                }

                if ($recibo->tipoMoneda == 'USD') {
                    $recibo->tipoCambio =  $tipoCambio->factorVenta;
                }

                $recibo->save();
                $idRecibo = $recibo->id;

                $mov = new DetalleMovimientoCaja;
                $mov->tipo = $request->get('tipoperacion');
                $mov->moneda = $request->get('moneda');
                $mov->descripcion = $descripcion; //$request->get('descripcion');
                $mov->monto = (!is_null($recibo->idCuenta)?$request->get('monto'): $request->get('total'));
                $mov->tipopago = (in_array($tipopagodet, ['Caja - Efectivo', 'E'])?'E':'D'); //$request->get('tipopago');
                $mov->idMovimiento = $id;
                $mov->idRecibo = $idRecibo;
                $mov->save();
                if ($band) {
                    $errors[] = 'Movimiento de Caja Registrado Correctamente';
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
    }

    public function validarMovimiento (Request $request) {
		$reglas = [
            'cajaId'=>  'required',
            'tipoperacion'=> 'required',
            'tipoIngreso' => $request->get('tipoperacion') == 'I'? 'required':'nullable',
            'codcliente' => $request->get('tipoperacion') == 'I'?'required|numeric|min:1':'nullable',
            'codcuenta' => (($request->get('tipoperacion') == 'I' && $request->get('tipoIngreso') == 'CC') || 
                        ($request->get('tipoperacion') == 'E' && $request->get('tipoEgreso') == 'CP'))?'required|numeric|min:1': 'nullable',
            'formapago' => $request->get('tipoperacion') == 'I'? 'required': 'nullable',
            'moneda' => 'required',
            'total' => 'required|numeric',
            'tipo_documento' => 'required',
            'serie' =>'required',
            'numero' =>'required',
            'tipopago'=> (($request->get('tipoperacion') == 'I' && $request->get('tipoIngreso') == 'CC') || 
                        ($request->get('tipoperacion') == 'E' && $request->get('tipoEgreso') == 'CP'))?'required':'nullable',
            'monto' => (($request->get('tipoperacion') == 'I' && $request->get('tipoIngreso') == 'CC') || 
                        ($request->get('tipoperacion') == 'E' && $request->get('tipoEgreso') == 'CP'))?'required|numeric':'nullable',
			'descripcion'=> 'nullable',
			
            'tipoEgreso' => $request->get('tipoperacion') == 'E'? 'required':'nullable',
            'codproveedor' => $request->get('tipoperacion') == 'E'?'required|numeric|min:1':'nullable',
            'tipo_egreso' => $request->get('tipoperacion') == 'E'? 'required':'nullable',
            'tipogasto'   => $request->get('tipoperacion') == 'E' && $request->get('tipo_egreso') == 'G'? 'required':'nullable',
            'unidadNegocio' => $request->get('tipoperacion') == 'E'? 'required':'nullable',
            'partida' => 'nullable', //$request->get('tipoperacion') == 'E'? 'required':'nullable',
            'sustento' => $request->get('tipoperacion') == 'E'? 'required':'nullable',
            'codcuentacontable' => $request->get('tipoperacion') == 'E'? 'required|numeric|min:1':'nullable',
            'codtrabajador' => $request->get('tipoperacion') == 'E'? 'required|numeric|min:1':'nullable',
            'medioPago' => $request->get('tipoperacion') == 'E'? 'required':'nullable',
        ];

        $mensajes = [
            'cajaId.required'=> 'Indique Caja',
            'tipoperacion.required'=> 'Indique Tipo de Operación',
            'tipoIngreso.required' => 'Indique Tipo de Ingreso',
            'codcliente.required' => 'Indique Cliente',
            'codcliente.min' => 'Indique Cliente',
            'codcuenta.required' => 'Indique Cuenta',
            'codcuenta.min' => 'Indique Cuenta',
            'formapago.required' => 'Indique Forma de Pago',
            'moneda.required' => 'Indique Moneda',
            'total.required' => 'Indique Total',
            'tipo_documento.required' => 'Indique Tipo Documento',
            'serie.required' => 'Indique Serie',
            'numero.required' => 'Indique Número',
            'tipopago.required'=> 'Indique Tipo de Pago',
            'monto.required' => 'Indique Monto',
            'tipoEgreso.required' => 'Indiquer Tipo',
            'codproveedor.required' => 'Indique Proveedor',
            'codproveedor.min' => 'Indique Proveedor',
            'tipo_egreso.required' => 'Indique Tipo de Egreso',
            'tipogasto.required' => 'Indique Tipo de Gasto',
            'unidadNegocio.required' => 'Indique Unidad de Negocio',
            'partida.required' => 'Indique Partida',
            'sustento.required' => 'Seleccione Sustento',
            'codcuentacontable.required' => 'Indique Cuenta Contable',
            'codcuentacontable.min' => 'Indique Cuenta Contable',
            'codtrabajador.required' => 'Indique Trabajador que aprueba',
            'codtrabajador.min' => 'Indique Trabajador que aprueba',
            // 'descripcion.required'=> 'Indique Concepto',
            // 'monto.required'=> 'Indique Monto',
            'monto.numeric'=> 'Monto debe ser numérico',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function validarApertura (Request $request) {
		$reglas = [
            'tiendaId'=> 'required|numeric',
            'saldo'=> 'required|numeric',
        ];

        $mensajes = [
            'tiendaId.required'=> 'Indique Tienda',
            'tiendaId.numeric'=> 'Tienda deber ser un número',
            'saldo.required'=> 'Indique Monto de Apertura',
            'saldo.required'=> 'Monto de Apertura debe ser un número',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function eliminarCaja (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$det = DetalleMovimientoCaja::find($id);
            if (!is_null($det)) {
                if (!is_null($det->idRecibo)) {
                    $recibo = ReciboCaja::find($det->idRecibo);
                    if (!is_null($recibo)) {
                        if (!is_null($recibo->idCuenta)) {
                            $cuenta = Cuenta::find($recibo->idCuenta);
                            if (!is_null($cuenta)) {
                                $cuenta->saldo = $cuenta->saldo + $recibo->montopago;
                                if ($cuenta->saldo <= $cuenta->importe) {
                                    $cuenta->estado = 'P';
                                    $cuenta->update();
                                } else {
                                    $band = false;
                                    $errors[] = 'No se puede actualizar Cuenta, comuniquese con el Administrador';
                                }
                            }     
                        }
                      }
                }

                $recibo->idPersonalEliminar = Auth::user()->usuarioId;
                $recibo->update();
                $recibo->delete();
                $det->delete();
                if ($band) {
                    $errors[] = 'Registro de Caja Eliminado Correctamente';
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

    public function pdf($id) {
        if ($id != '') {
            // $mov = MovimientoCaja::find($id);
            $mov = DB::table('movimientocaja as m')
                    ->leftjoin('trabajador as cl','cl.id','=','m.idUsuario')
                    ->where('m.id',$id)
                    ->select(DB::Raw("CONCAT(cl.apellidos,' ',cl.nombres) as usuario"),'m.*')
                    ->first();
            $movApertura = MovimientoCaja::where('id',$id)
                            ->select(DB::Raw("'APERTURA DE CAJA' as concepto"), DB::Raw("ROUND(saldoApertura,2) as total"), 
                            DB::Raw("'I' as tipo"), DB::Raw("'0' as id"), DB::Raw("'A' as tipopago"),'created_at', DB::Raw("'PEN' as moneda"), DB::Raw("'Efectivo' as metodoPago"));

            $ventas = Venta::leftJoin('persona as cl','cl.id','=','venta.idCliente')
                        ->where('venta.idMovimiento','=',$id);
                        
            $detallesCaja = DetalleMovimientoCaja::where('idMovimiento','=',$id);

            $ventas  = $ventas->select(DB::Raw("CONCAT((CASE WHEN venta.tipoComprobante = 'B' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END),', REF. ', venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as concepto"),
            DB::Raw("ROUND(venta.total, 2) as total"), DB::raw("'I' as tipo"), DB::raw("'0' as id"),'venta.tipoPago as tipopago', 'venta.created_at', 'venta.tipoMoneda as moneda', 'venta.metodoPago');

            $detallesCaja = $detallesCaja->select('descripcion as concepto',DB::Raw("ROUND(monto,2) as total"),'tipo','id','tipopago', 
                    'created_at', 'moneda', DB::Raw("(CASE tipopago WHEN 'E' THEN 'Efectivo' ELSE 'Depósito' END) as metodoPago"));

            $query  =  $detallesCaja->unionAll($ventas)
                        ->unionAll($movApertura);
        
            $querySql	= $query->toSql();
            // $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

            $lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))
            ->mergeBindings($query->getQuery());
            // $l = DB::query($lista);
            $lista = $lista->get()->toArray();
            $cantidad = count($lista);

            $ingresos02   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','I')->where('moneda','PEN')->sum('monto');
            $ingresos02D   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','I')->where('moneda','USD')->sum('monto');
            $v_ingresos = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','PEN')->sum('total');
            $v_ingresosD = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','USD')->sum('total');

            $ingresos = $ingresos02+$v_ingresos+$mov->saldoApertura;
            $ingresosD = $v_ingresosD + $ingresos02D;
           
            $egresos  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('tipopago','E')->where('moneda','PEN')->sum('monto');
            $egresosD  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('tipopago','E')->where('moneda','USD')->sum('monto');

            $efectivo = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $efectivoD = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            $ingresos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','=','I')->sum('monto');
            $efectivo+=$ingresos02+$mov->saldoApertura;
        
            $depositos = Venta::where('metodoPago','Depósito')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $depositosD = Venta::where('metodoPago','Depósito')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            $depositos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','D')->where('moneda','PEN')->where('tipo','=','I')->sum('monto');
            $depositos02D = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','D')->where('moneda','USD')->where('tipo','=','I')->sum('monto');
        
            $tarjeta = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $tarjetaD = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            
            $depositos +=$depositos02;
            $depositosD +=$depositos02D;
            $pdf = new Fpdf();
            $pdf::AddPage();
            $pdf::SetTitle('ARQUEO DE CAJA');
            $pdf::Cell(130,7,"",0,0,'C');
            $pdf::Image("images/logo-carpio.png", 10, 5, 40, 25);
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln();
            
            $pdf::SetFont('helvetica','B',13);
            $pdf::Cell(60);
            $pdf::Cell(80,10,'ARQUEO DE CAJA',0,0,'C');
            $pdf::Ln();
        
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(39,6,utf8_decode("FECHA DE IMPRESIÓN: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(110,6,utf8_decode(date('d/m/Y h:i a')),0,0,'L');
            $pdf::Ln();
            
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(39,6,utf8_decode("USUARIO: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(110,6,utf8_decode($mov->usuario),0,0,'L');
            $pdf::Ln();
          
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(39,6,utf8_decode("FECHA DE APERTURA: "),0,0,'L');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(110,6,utf8_decode(date('d/m/Y',strtotime($mov->fecha))),0,0,'L');
            $pdf::Ln();
            $pdf::Ln();

            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(10,5,("Item"),1,0,'C');
            $pdf::Cell(80,5,utf8_decode("Cliente/Descripción"),1,0,'C');
            $pdf::Cell(20,5,utf8_decode("Ingresos"),1,0,'C');
            $pdf::Cell(20,5,("Egresos"),1,0,'C');
            $pdf::Cell(30,5,("Forma de Pago"),1,0,'C');
            $pdf::Cell(30,5,("Moneda"),1,0,'C');
            $pdf::Ln();
            $pdf::SetFont('helvetica','',9);
            $i = 1;
            $alto = 5;
            foreach ($lista as $value) {
                $alto2 = $pdf::GetMultiCellHeight(80,$alto,utf8_decode(strtoupper($value->concepto)),1,"L");
                $pdf::Cell(10,$alto2,utf8_decode($i),1,0,'C');
                $_x = $pdf::GetX();
                $_y = $pdf::GetY();
    	        $pdf::MultiCell(80, $alto, utf8_decode(strtoupper($value->concepto)), 1, "L");
                $pdf::SetXY($_x+80,$pdf::GetY()-$alto2);
                $pdf::Cell(20,$alto2,utf8_decode($value->tipo == 'I'?number_format($value->total,2,'.',' '):''),1,0,'R');
                $pdf::Cell(20,$alto2,utf8_decode($value->tipo == 'E'?number_format($value->total,2,'.',' '):''),1,0,'R');
                $pdf::Cell(30,$alto2,utf8_decode(($value->id == 0?'Venta - ':'').$value->metodoPago),1,0,'C');
                $pdf::Cell(30,$alto2,utf8_decode($value->moneda),1,0,'C');
                $pdf::Ln();
                $i++;
            }

            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln();
            
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(50);
            $pdf::Cell(90,6,utf8_decode("TOTAL"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(50);
            $pdf::Cell(30,6,utf8_decode(""),1,0,'C');
            $pdf::Cell(30,6,utf8_decode("S/"),1,0,'C');
            $pdf::Cell(30,6,utf8_decode("$"),1,0,'C');
            $pdf::Ln();
            $pdf::Cell(50);
            $pdf::Cell(30,6,utf8_decode("INGRESOS "),1,0,'C');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,6,number_format($ingresos,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($ingresosD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Cell(50);
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,utf8_decode("EFECTIVO "),1,0,'C');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,6,number_format($efectivo,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($efectivoD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Cell(50);
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,utf8_decode("DEPÓSITOS "),1,0,'C');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,6,number_format($depositos,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($depositosD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Cell(50);
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,utf8_decode("TARJETA "),1,0,'C');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,6,number_format($tarjeta,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($tarjetaD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Cell(50);
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,utf8_decode("EGRESOS "),1,0,'C');
            $pdf::SetFont('helvetica','',9);
            $pdf::Cell(30,6,number_format($egresos,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($egresosD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Cell(50);
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,utf8_decode("SALDO "),1,0,'C');
            $pdf::SetFont('helvetica','B',9);
            $pdf::Cell(30,6,number_format($efectivo-$egresos,2,'.',' '),1,0,'R');
            $pdf::Cell(30,6,number_format($efectivoD-$egresosD,2,'.',' '),1,0,'R');
            $pdf::Ln();
         
            $pdf::Ln();
        
            //   header('Content-type: application/pdf');
            $pdf::Output('ArqueoCaja - '.date('Y-m-d H:i:s').'.pdf','I');
            exit;

        }

    }

    public function excelAll(Request $request) {
        $usuario = $request->get('usuario');
        $fechaI = $request->get('fechai');
        $fechaF = $request->get('fechaf');
        
        $movimientos  = DB::table('movimientocaja as mov')
                        ->leftjoin('trabajador as cl','cl.id','=','mov.idUsuario')
                        ->where(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', $usuario. '%')
                        ->whereNull('mov.deleted_at');

        if ($fechaI != '') {
            $movimientos = $movimientos->where('mov.fecha','>=',$fechaI);
        }

        if ($fechaF != '') {
            $movimientos = $movimientos->where('mov.fecha','<=',$fechaF);
        }

        $movimientos = $movimientos->select('mov.id')->orderBy('mov.created_at')->get();

        $excel = new PHPExcel(); 
        $excel->setActiveSheetIndex(0);
        $hoja1 = $excel->getActiveSheet();
        $hoja1->setTitle("Arqueo de Caja por Fechas");
        $item = 1;
        foreach($movimientos as $itemMov) {
            $this->renderDataCaja($itemMov->id, $hoja1, $item);
            $item+=3;
        }

        
        foreach(range('A','F') as $columnID) 
        { 
            $hoja1->getColumnDimension($columnID)->setAutoSize(true); 
        }

        foreach(range('J','L') as $columnID) 
        { 
            $hoja1->getColumnDimension($columnID)->setAutoSize(true); 
        }

        $objWriter = new PHPExcel_IOFactory($excel);		
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="arqueo_cajas_fechas.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

    public function renderDataCaja($id, $hoja1, &$j) {
        $min = $j;
        $mov = DB::table('movimientocaja as m')
                ->leftjoin('trabajador as cl','cl.id','=','m.idUsuario')
                ->where('m.id',$id)
                ->select(DB::Raw("CONCAT(cl.apellidos,' ',cl.nombres) as usuario"),'m.*')
                ->first();
        $movApertura = MovimientoCaja::where('id',$id)
                        ->select(DB::Raw("'APERTURA DE CAJA' as concepto"), DB::Raw("ROUND(saldoApertura,2) as total"), 
                        DB::Raw("'I' as tipo"), DB::Raw("'0' as id"), DB::Raw("'A' as tipopago"),'created_at', DB::Raw("'PEN' as moneda"), DB::Raw("'Efectivo' as metodoPago"));

        $ventas = Venta::leftJoin('persona as cl','cl.id','=','venta.idCliente')
                    ->where('venta.idMovimiento','=',$id);
                    
        $detallesCaja = DetalleMovimientoCaja::where('idMovimiento','=',$id);

        $ventas  = $ventas->select(DB::Raw("CONCAT((CASE WHEN venta.tipoComprobante = 'B' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END),', REF. ', venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as concepto"),
        DB::Raw("ROUND(venta.total, 2) as total"), DB::raw("'I' as tipo"), DB::raw("'0' as id"),'venta.tipoPago as tipopago', 'venta.created_at', 'venta.tipoMoneda as moneda', 'venta.metodoPago');

        $detallesCaja = $detallesCaja->select('descripcion as concepto',DB::Raw("ROUND(monto,2) as total"),'tipo','id','tipopago', 'created_at', 'moneda', DB::Raw("(CASE tipopago WHEN 'E' THEN 'Efectivo' ELSE 'Depósito' END) as metodoPago"));

        $query  =  $detallesCaja->unionAll($ventas)
                    ->unionAll($movApertura);

        $querySql	= $query->toSql();
        // $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

        $lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))
        ->mergeBindings($query->getQuery());
        // $l = DB::query($lista);
        $lista = $lista->get()->toArray();
        $cantidad = count($lista);

        $ingresos02   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','PEN')->sum('monto');
        $ingresos02D   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','USD')->sum('monto');

        $v_ingresos = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','PEN')->sum('total');
        $v_ingresosD = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','USD')->sum('total');

        $ingresos = $ingresos02+$v_ingresos+$mov->saldoApertura;
        $ingresosD = $v_ingresosD +  $ingresos02D;

        $egresos  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('moneda','PEN')->sum('monto');
        $egresosD = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('moneda','USD')->sum('monto');

        $efectivo = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
        $efectivoD = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');

        $ingresos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','=','I')->sum('monto');
        $efectivo+=$ingresos02+$mov->saldoApertura;

        $depositos = Venta::where('metodoPago','Depósito')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
        $depositosD = Venta::where('metodoPago','Depósito')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');

        $depositos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','D')->where('moneda','PEN')->where('tipo','=','I')->sum('monto');
        $depositos02D = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','D')->where('moneda','USD')->where('tipo','=','I')->sum('monto');

        $tarjeta = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
        $tarjetaD = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');


        $depositos +=$depositos02;
        $depositosD +=$depositos02D;

        $hoja1->setCellValue('A'.$j,'ARQUEO DE CAJA');
        $hoja1->mergeCells('A'.$j.':F'.$j);
        $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_header);
        $j++;

        $hoja1->setCellValue('A'.$j,'FECHA DE IMPRESIÓN');
        $hoja1->getStyle('A'.$j)->applyFromArray($this->estilo_header);
      
        $hoja1->setCellValue('B'.$j, date('d/m/Y h:i a'));
        $hoja1->mergeCells('B'.$j.':F'.$j);
        $hoja1->getStyle('B'.$j)->applyFromArray($this->estilo_content);
        $j++;

        $hoja1->setCellValue('A'.$j,'USUARIO');
        $hoja1->getStyle('A'.$j)->applyFromArray($this->estilo_header);
       
        $hoja1->setCellValue('B'.$j, utf8_decode($mov->usuario));
        $hoja1->mergeCells('B'.$j.':F'.$j);
        $hoja1->getStyle('B'.$j)->applyFromArray($this->estilo_content);
        $j++;

        $hoja1->setCellValue('A'.$j,'FECHA APERTURA');
        $hoja1->getStyle('A'.$j)->applyFromArray($this->estilo_header);
        
        $hoja1->setCellValue('B'.$j, date('d/m/Y',strtotime($mov->fecha)));
        $hoja1->mergeCells('B'.$j.':F'.$j);
        $hoja1->getStyle('B'.$j)->applyFromArray($this->estilo_content);
        $j++;
       

        $hoja1->setCellValue('A'.$j,'ITEM');
        $hoja1->setCellValue('B'.$j,'CLIENTE/DESCRIPCIÓN');
        $hoja1->setCellValue('C'.$j,'INGRESOS');
        $hoja1->setCellValue('D'.$j,'EGRESOS');
        $hoja1->setCellValue('E'.$j,'FORMA DE PAGO');
        $hoja1->setCellValue('F'.$j,'MONEDA');
    
        $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_header);
        
        $j++;
        $cont = 1;
        foreach ($lista as $value) {
            $hoja1->setCellValue('A'.$j,$cont);
            $hoja1->setCellValue('B'.$j,strtoupper($value->concepto));
            $hoja1->setCellValue('C'.$j,utf8_decode($value->tipo == 'I'?number_format($value->total,2,'.',' '):''));
            $hoja1->setCellValue('D'.$j,utf8_decode($value->tipo == 'E'?number_format($value->total,2,'.',' '):''));
            $hoja1->setCellValue('E'.$j,($value->id == 0?'Venta - ':'').$value->metodoPago);
            $hoja1->setCellValue('F'.$j,utf8_decode($value->moneda));
            
            $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
            $hoja1->getStyle('C'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
            $hoja1->getStyle('D'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
        
            $cont++;
            $j++;
        }

        $hoja1->setCellValue('J'.$min, 'TOTAL');
        $hoja1->mergeCells('J'.$min.':L'.$min);
        $hoja1->getStyle('J'.$min.':L'.$min)->applyFromArray($this->estilo_header);
        
        $min++;

        $hoja1->setCellValue('J'.$min, '');
        $hoja1->setCellValue('K'.$min, 'S/');
        $hoja1->setCellValue('L'.$min, '$');
        $hoja1->getStyle('J'.$min.':L'.$min)->applyFromArray($this->estilo_header);
        $min++;

        $hoja1->setCellValue('J'.$min, 'INGRESOS');
        $hoja1->setCellValue('K'.$min, $ingresos);
        $hoja1->setCellValue('L'.$min, $ingresosD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
        $min++;

        $hoja1->setCellValue('J'.$min, 'EFECTIVO');
        $hoja1->setCellValue('K'.$min, $efectivo);
        $hoja1->setCellValue('L'.$min, $efectivoD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
        $min++;

        $hoja1->setCellValue('J'.$min, 'DEPOSITOS');
        $hoja1->setCellValue('K'.$min, $depositos);
        $hoja1->setCellValue('L'.$min, $depositosD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
        $min++;

        $hoja1->setCellValue('J'.$min, 'TARJETA');
        $hoja1->setCellValue('K'.$min, $tarjeta);
        $hoja1->setCellValue('L'.$min, $tarjetaD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
        $min++;

        $hoja1->setCellValue('J'.$min, 'EGRESOS');
        $hoja1->setCellValue('K'.$min, $egresos);
        $hoja1->setCellValue('L'.$min, $egresosD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
        $min++;

        $hoja1->setCellValue('J'.$min, 'SALDO');
        $hoja1->setCellValue('K'.$min, $efectivo-$egresos);
        $hoja1->setCellValue('L'.$min, $efectivoD-$egresosD);
        $hoja1->getStyle('J'.$min)->applyFromArray($this->estilo_header);
        $hoja1->getStyle('K'.$min.':L'.$min)->applyFromArray($this->estilo_content);
        $hoja1->getStyle('K'.$min.':L'.$min)->getNumberFormat()->setFormatCode('#,##0.00');
    
    }

    public function excel($id) {
        if ($id != '') {
            $mov = DB::table('movimientocaja as m')
                    ->leftjoin('trabajador as cl','cl.id','=','m.idUsuario')
                    ->where('m.id',$id)
                    ->select(DB::Raw("CONCAT(cl.apellidos,' ',cl.nombres) as usuario"),'m.*')
                    ->first();
            $movApertura = MovimientoCaja::where('id',$id)
                            ->select(DB::Raw("'APERTURA DE CAJA' as concepto"), DB::Raw("ROUND(saldoApertura,2) as total"), 
                            DB::Raw("'I' as tipo"), DB::Raw("'0' as id"), DB::Raw("'A' as tipopago"),'created_at', DB::Raw("'PEN' as moneda"), DB::Raw("'Efectivo' as metodoPago"));

            $ventas = Venta::leftJoin('persona as cl','cl.id','=','venta.idCliente')
                        ->where('venta.idMovimiento','=',$id);
                        
            $detallesCaja = DetalleMovimientoCaja::where('idMovimiento','=',$id);

            $ventas  = $ventas->select(DB::Raw("CONCAT((CASE WHEN venta.tipoComprobante = 'B' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END),', REF. ', venta.tipoComprobante, LPAD(venta.serie,3,'0') ,'-', LPAD(venta.numero,8,'0')) as concepto"),
            DB::Raw("ROUND(venta.total, 2) as total"), DB::raw("'I' as tipo"), DB::raw("'0' as id"),'venta.tipoPago as tipopago', 'venta.created_at', 'venta.tipoMoneda as moneda', 'venta.metodoPago');

            $detallesCaja = $detallesCaja->select('descripcion as concepto',DB::Raw("ROUND(monto,2) as total"),'tipo','id','tipopago', 'created_at', 'moneda', DB::Raw("(CASE tipopago WHEN 'E' THEN 'Efectivo' ELSE 'Depósito' END) as metodoPago"));

            $query  =  $detallesCaja->unionAll($ventas)
                        ->unionAll($movApertura);
        
            $querySql	= $query->toSql();
            // $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

            $lista = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))
            ->mergeBindings($query->getQuery());
            // $l = DB::query($lista);
            $lista = $lista->get()->toArray();
            $cantidad = count($lista);

            $ingresos02   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','PEN')->sum('monto');
            $ingresos02D   = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','I')->where('moneda','USD')->sum('monto');
          
            $v_ingresos = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','PEN')->sum('total');
            $v_ingresosD = Venta::where('idMovimiento','=',$id)->where('tipoMoneda','USD')->sum('total');

            $ingresos = $ingresos02+$v_ingresos+$mov->saldoApertura;
            $ingresosD = $v_ingresosD +  $ingresos02D;
           
            $egresos  = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('moneda','PEN')->sum('monto');
            $egresosD = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipo','=','E')->where('moneda','USD')->sum('monto');
            
            $efectivo = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $efectivoD = Venta::where('metodoPago','Efectivo')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            $ingresos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','E')->where('tipo','=','I')->sum('monto');
            $efectivo+=$ingresos02+$mov->saldoApertura;
        
            $depositos = Venta::where('metodoPago','Depósito')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $depositosD = Venta::where('metodoPago','Depósito')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            $depositos02 = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','D')->where('moneda','PEN')->where('tipo','=','I')->sum('monto');
            $depositos02D = DetalleMovimientoCaja::where('idMovimiento','=',$id)->where('tipopago','=','D')->where('moneda','USD')->where('tipo','=','I')->sum('monto');
        
            $tarjeta = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','PEN')->where('idMovimiento','=',$id)->sum('total');
            $tarjetaD = Venta::where('metodoPago','Tarjeta')->where('tipoMoneda','USD')->where('idMovimiento','=',$id)->sum('total');
            
            
            $depositos +=$depositos02;
            $depositosD +=$depositos02D;
            
            $excel = new PHPExcel(); 
            $excel->setActiveSheetIndex(0);
            $hoja1 = $excel->getActiveSheet();
            $hoja1->setTitle("Arqueo de Caja");
            $hoja1->setCellValue('A1','ARQUEO DE CAJA');
            $hoja1->mergeCells('A1:B1');
            $hoja1->getStyle('A1:B1')->applyFromArray($this->estilo_header);
            
            $hoja1->setCellValue('A2','FECHA DE IMPRESIÓN');
            $hoja1->setCellValue('B2', date('d/m/Y h:i a'));

            $hoja1->setCellValue('A3','USUARIO');
            $hoja1->setCellValue('B3', utf8_decode($mov->usuario));

            $hoja1->setCellValue('A4','FECHA APERTURA');
            $hoja1->setCellValue('B4', date('d/m/Y',strtotime($mov->fecha)));

            $hoja1->getStyle('A2:A4')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('B2:B4')->applyFromArray($this->estilo_content);

            $hoja1->setCellValue('A7','ITEM');
            $hoja1->setCellValue('B7','CLIENTE/DESCRIPCIÓN');
            $hoja1->setCellValue('C7','INGRESOS');
            $hoja1->setCellValue('D7','EGRESOS');
            $hoja1->setCellValue('E7','FORMA DE PAGO');
            $hoja1->setCellValue('F7','MONEDA');
            // $hoja1->setCellValue('G7','TOTAL');
        
            $hoja1->getStyle('A7:F7')->applyFromArray($this->estilo_header);
            
            $j = 8;
            $cont = 1;
            foreach ($lista as $value) {
                $hoja1->setCellValue('A'.$j,$cont);
                $hoja1->setCellValue('B'.$j,strtoupper($value->concepto));
                $hoja1->setCellValue('C'.$j,utf8_decode($value->tipo == 'I'?number_format($value->total,2,'.',' '):''));
                $hoja1->setCellValue('D'.$j,utf8_decode($value->tipo == 'E'?number_format($value->total,2,'.',' '):''));
                $hoja1->setCellValue('E'.$j,($value->id == 0?'Venta - ':'').$value->metodoPago);
                $hoja1->setCellValue('F'.$j,utf8_decode($value->moneda));
                
                $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
                $hoja1->getStyle('C'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
                $hoja1->getStyle('D'.$j)->getNumberFormat()->setFormatCode('#,##0.00');
            
                $cont++;
                $j++;
            }
    
            $hoja1->setCellValue('J3', 'TOTAL');
            $hoja1->mergeCells('J3:L3');
            $hoja1->getStyle('J3:L3')->applyFromArray($this->estilo_header);
            
            $hoja1->setCellValue('J4', '');
            $hoja1->setCellValue('K4', 'S/');
            $hoja1->setCellValue('L4', '$');
            $hoja1->getStyle('J4:L4')->applyFromArray($this->estilo_header);

            $hoja1->setCellValue('J5', 'INGRESOS');
            $hoja1->setCellValue('K5', $ingresos);
            $hoja1->setCellValue('L5', $ingresosD);
            $hoja1->getStyle('J5')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K5:L5')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K5:L5')->getNumberFormat()->setFormatCode('#,##0.00');
            
            $hoja1->setCellValue('J6', 'EFECTIVO');
            $hoja1->setCellValue('K6', $efectivo);
            $hoja1->setCellValue('L6', $efectivoD);
            $hoja1->getStyle('J6')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K6:L6')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K6:L6')->getNumberFormat()->setFormatCode('#,##0.00');
            
            $hoja1->setCellValue('J7', 'DEPOSITOS');
            $hoja1->setCellValue('K7', $depositos);
            $hoja1->setCellValue('L7', $depositosD);
            $hoja1->getStyle('J7')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K7:L7')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K7:L7')->getNumberFormat()->setFormatCode('#,##0.00');
        
            $hoja1->setCellValue('J8', 'TARJETA');
            $hoja1->setCellValue('K8', $tarjeta);
            $hoja1->setCellValue('L8', $tarjetaD);
            $hoja1->getStyle('J8')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K8:L8')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K8:L8')->getNumberFormat()->setFormatCode('#,##0.00');
        
            $hoja1->setCellValue('J9', 'EGRESOS');
            $hoja1->setCellValue('K9', $egresos);
            $hoja1->setCellValue('L9', $egresosD);
            $hoja1->getStyle('J9')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K9:L9')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K9:L9')->getNumberFormat()->setFormatCode('#,##0.00');
        
            $hoja1->setCellValue('J10', 'SALDO');
            $hoja1->setCellValue('K10', $efectivo-$egresos);
            $hoja1->setCellValue('L10', $efectivoD-$egresosD);
            $hoja1->getStyle('J10')->applyFromArray($this->estilo_header);
            $hoja1->getStyle('K10:L10')->applyFromArray($this->estilo_content);
            $hoja1->getStyle('K10:L10')->getNumberFormat()->setFormatCode('#,##0.00');
        

            foreach(range('A','F') as $columnID) 
            { 
                $hoja1->getColumnDimension($columnID)->setAutoSize(true); 
            }

            foreach(range('J','L') as $columnID) 
            { 
                $hoja1->getColumnDimension($columnID)->setAutoSize(true); 
            }

            $objWriter = new PHPExcel_IOFactory($excel);		
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="arqueo_caja.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
        }
      
    }
}
