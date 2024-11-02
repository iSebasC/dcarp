<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use App\;
use DB;
use Validator;
use Auth;

use App\Models\StockProducto;
use App\Models\StockAuto;
use App\Models\StockProductoDetalleSalida;
use App\Models\StockProductoDetalle;

use App\Models\Producto;
use App\Models\Persona;
use App\Models\Serie;
use App\Models\AnulacionNotas;
use App\Models\Venta;
use App\Models\Compra;

use App\Models\DetalleAnulacionNotas;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Anulacion;
use App\Models\PagoDetalle;
use App\Models\OrdenTrabajo;
use App\Models\Cotizacion;

use App\Libraries\Funciones;

class DocumentoBajaController extends Controller
{
    public $almacenId = 2;
	public $tiendaId  = 1;
    public $user_wsdl = '20103327378';
	public $pass_wsdl = 'bj1R8xkhHB';
    
    //
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
                    ->where(DB::Raw("CONCAT(documento,' - ', (CASE WHEN tipoDocumento = 'PN' THEN CONCAT(apellidos, ' ',nombres) ELSE razonSocial END))"), 
                            'LIKE','%'.$busqueda.'%')
                    ->get();

        return ['personas' => $personas];
    }

    public function getAll (Request $request) {
		$documento 	 = $request->get('documento');
		$comprobante = $request->get('comprobante');
		$cliente     = $request->get('razonSocial');
		$tipoOperacion   = $request->get('tipoOperacion');
		$docReferencia = $request->get('docReferencia');
		
		$fechaI 	 = $request->get('fechaI');
    	$fechaF	 	 = $request->get('fechaF');
		$moneda	 	 = $request->get('moneda');
		$tipodoc	 = $request->get('tipodoc'); 	

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
        $t1 = DB::table('anulacionnotas as an')
                ->join('persona as pr','pr.id','=','an.idCliente')
                ->join('venta as v','v.id','=','an.idVenta')
                ->whereNull('an.idCompra')
                ->where(function ($qq) use($cliente) {
                    $qq->where('pr.razonSocial','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$cliente.'%');
                });

        if ($fechaI != '') {
            $t1 = $t1->where('an.fecha','>=',$fechaI);
        }
        if ($fechaF != '') {
            $t1 = $t1->where('an.fecha','<=',$fechaF);
        }
        if ($tipoOperacion != 'todo') {
            $t1 = $t1->where('an.tipoComprobante','LIKE','%'.$tipoOperacion.'%');
        }
        if ($tipodoc != 'todo') {
            $t1 = $t1->where('v.tipoComprobante','LIKE','%'.$tipodoc.'%');        
        }
        $t1 = $t1->where('pr.documento','LIKE','%'.$documento.'%')
                ->where(DB::Raw("CONCAT(an.serie,'-',an.numero)"),'LIKE', '%'.$comprobante.'%')
                ->where(DB::Raw("CONCAT(v.serie,'-',v.numero)"),'LIKE', '%'.$docReferencia.'%')
                ->select('an.id', DB::Raw("CONCAT(v.tipoComprobante,'C', LPAD(an.serie,2,'0'),'-',LPAD(an.numero,8,'0')) as comprobante"),
                'an.situacion', 'pr.documento', DB::Raw("FORMAT(an.total,2) as total"), DB::Raw("FORMAT(an.subtotal,2) as subtotal"),
                DB::Raw("FORMAT(an.igv,2) as igv"), DB::Raw("CONCAT(v.tipoComprobante,LPAD(v.serie,3,'0'),'-',LPAD(v.numero,8,'0')) as referencia"),
                DB::Raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as cliente"),
                'an.idDocumentoSUNAT',
                'an.nombreDocumentoSUNAT',
                DB::Raw("'V' as aplicada_a"),
                DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S/.' ELSE '$' END) as moneda"),
                DB::Raw("DATE_FORMAT(an.fecha,'%d/%m/%Y') as fechaAnulacion"),
                DB::Raw("DATE_FORMAT(an.created_at,'%d/%m/%Y %H:%i:%s') as fechaRegistro"),
                'an.created_at'
            );
                
        $t2 = DB::table('anulacionnotas as an')
            ->join('persona as pr','pr.id','=','an.idCliente')
            ->join('compra as c','c.id','=','an.idCompra')
            ->whereNull('an.idVenta')
            ->where(function ($qq) use($cliente) {
                $qq->where('pr.razonSocial','LIKE','%'.$cliente.'%')
                    ->orWhere(DB::Raw("CONCAT(pr.apellidos,' ', pr.nombres)"),'LIKE','%'.$cliente.'%');
            });

            if ($fechaI != '') {
                $t2 = $t2->where('an.fecha','>=',$fechaI);
            }
            if ($fechaF != '') {
                $t2 = $t2->where('an.fecha','<=',$fechaF);
            }
            if ($tipoOperacion != 'todo') {
                $t2 = $t2->where('an.tipoComprobante','LIKE','%'.$tipoOperacion.'%');
            }
            if ($tipodoc != 'todo') {
                $t2 = $t2->where('c.tipoDocumento','LIKE','%'.$tipodoc.'%');
            }
           
            $t2 = $t2->where('pr.documento','LIKE','%'.$documento.'%')
                    ->where('an.documentoCompra','LIKE', '%'.$comprobante.'%')
                    ->where('c.documento','LIKE', '%'.$docReferencia.'%')
                    ->select('an.id', DB::Raw("an.documentoCompra as comprobante"),
                    'an.situacion', 'pr.documento', DB::Raw("FORMAT(an.total,2) as total"), DB::Raw("FORMAT(an.subtotal,2) as subtotal"),
                    DB::Raw("FORMAT(an.igv,2) as igv"), DB::Raw("c.documento as referencia"),
                    DB::Raw("(CASE WHEN pr.razonSocial IS NOT NULL THEN pr.razonSocial ELSE CONCAT(pr.apellidos,' ', pr.nombres) END) as cliente"),
                    'an.idDocumentoSUNAT',
                    'an.nombreDocumentoSUNAT',
                    DB::Raw("'C' as aplicada_a"),
                    DB::Raw("(CASE c.tipoMoneda WHEN 'S' THEN 'S/.' ELSE '$' END) as moneda"),
                    DB::Raw("DATE_FORMAT(an.fecha,'%d/%m/%Y') as fechaAnulacion"),
                    DB::Raw("DATE_FORMAT(an.created_at,'%d/%m/%Y %H:%i:%s') as fechaRegistro"),
                    'an.created_at'
                );

        $query = $t2->unionall($t1);

        $querySql	= $query->toSql();
		// $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

		$lista_sl = DB::table(DB::Raw("($querySql) as a ORDER BY created_at ASC "))->mergeBindings($query);
		// $l = DB::query($lista);
		$lista = $lista_sl->get()->toArray();
		

		//    ->orderBy('compra.fecha','ASC');

		// $lista = $compras->get();
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
		
		$lista = $lista_sl->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get()
                   ->toArray();

		return ['notas' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Baja de Documento':' Baja de Documentos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

    public function declararAnulacion ($nota) {
        $band = true;
        $errors = [];
        $fecha_actual = date('Y-m-d');
        $fecha_comparar = date('Y-m-d',strtotime($fecha_actual."-7 days"));
        if(date('Y-m-d',strtotime($nota->created_at)) >= $fecha_comparar){
            if (!is_null($nota)) {
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
                $anulacion->idNota = $nota->id;
                $anulacion->numero = $correlativo_actual;
                $anulacion->tipoDocumentoAnulado = 'NC';
                $anulacion->idPersonal = Auth::user()->usuarioId;;

                if ($nota->tipoNota == 'C') {
                    $resumen = '07';
                } else {
                    $resumen = '08';
                }

                $venta = DB::table('venta')->where('id',$nota->idVenta)->first();
                if ($venta->tipoComprobante == 'B') {
                    $result = $this->anularNotaBoleta($this->user_wsdl,$this->pass_wsdl, $nota, $anulacion, $resumen);
                } elseif($venta->tipoComprobante == 'F') {
                    $result = $this->anularNotaFactura($this->user_wsdl,$this->pass_wsdl, $nota, $anulacion, $resumen);
                }
                if (!$result['estado']) {
                    $errors =  $result['errores'];//'No se Pudo Efectuar Anulación porque No se Encontró Comprobante en SUNAT';
                    $band = false;
                }
                //dd($venta->tipo_comprobante);
            }
        } else {
            $band = false;
            $errors[] = 'No se puede anular, excede en 07 días a su fecha de emisión.';
        }

        return ['errores' => $errors, 'status' => $band];
    
    }

    public function anularNotaBoleta($user,$pass,$nota,$anulacion,$tipo_resumen){
		$venta_o = DB::table('venta')->where('id','=',$nota->idVenta)->first();
		$cliente = Persona::findOrFail($nota->idCliente);

		$serie = $venta_o->tipoComprobante.$nota->tipoNota.str_pad($nota->serie,2,'0',STR_PAD_LEFT);
		$num   = str_pad($nota->numero,8,'0',STR_PAD_LEFT);
		
		$numeroboleta = $venta_o->tipoComprobante.$nota->tipoNota.str_pad($nota->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($nota->numero,8,'0',STR_PAD_LEFT);
		$numerobaja = $anulacion->serie."-".$anulacion->numero;
		
		// $numeroboleta = $anulacion->serie."-".$anulacion->numero;
		$fechaemision = date("Y-m-d");
		$tipoResumen = $tipo_resumen;//$_POST["tipoResumen"];
		$fechareferencia = date('Y-m-d',$nota->created_at); //$_POST["fecref"];
		$moneda = $venta_o->tipoMoneda; //'PEN'; //$_POST["moneda"];
		$tipodetalle = $tipo_resumen;
		$idDetalle =$nota->id;// $_POST["idDetalle"];
		$idDetalleServidor = $nota->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
		$serieDetalle = $serie;// $numero_boleta[0]; //$_POST["serieDetalle"];
		$correlativo = $num; //$numero_boleta[1]; //$_POST["correlativo"];
		$dni = $cliente->documento;//$_POST["dni"];
		$total = $nota->total; //$_POST["total"];
		$numeroReferencia = "";//$_POST["numeroReferencia"];
		$tipoReferencia = "";///$_POST["tipoReferencia"];
		$detalles = array();

		// foreach ($detalles_venta as $det) {
		//    $pr = Producto::findOrFail($det->codProducto);
		$detalles[] = array(
			"id"=>$nota->id,
			"idservidor"=>$nota->idDocumentoSUNAT,
			"tipo"=>$tipo_resumen,
			"numero"=> $serie, //$numero_boleta[0],
			"correlativo"=> $num, //$numero_boleta[1],
			"dni"=> $cliente->documento, //$dni[$key],
			"total"=>$nota->total,
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
	
	public function anularNotaFactura($user,$pass,$nota,$anulacion,$tipo_resumen){
        $venta_o = DB::table('venta')->where('id','=',$nota->idVenta)->first();
		$cliente = Persona::findOrFail($nota->idCliente);

		$serie = $venta_o->tipoComprobante.$nota->tipoNota.str_pad($nota->serie,2,'0',STR_PAD_LEFT);
		$num   = str_pad($nota->numero,8,'0',STR_PAD_LEFT);
		
		$numeroboleta = $venta_o->tipoComprobante.$nota->tipoNota.str_pad($nota->serie,2,'0',STR_PAD_LEFT).'-'.str_pad($nota->numero,8,'0',STR_PAD_LEFT);
		$numerobaja = $anulacion->serie."-".$anulacion->numero;
		
        $fechaemision = date("Y-m-d");
		$fechareferencia = date('Y-m-d',strtotime($nota->created_at)); //$_POST["fecref"];
		$tipodetalle = $tipo_resumen; //$_POST["tipodocumento"];
		$serieDetalle = $serie; //$numero_factura[0]; // $_POST["serieDetalle"];
		$correlativo = $num; //$numero_factura[1]; //$_POST["correlativo"];
		$motivo = 'Anulación de la Operación';//$_POST["motivo"];
		$idDetalles = $nota->id; //$_POST["idDetalle"];
		$idDetalleServidor = $nota->idDocumentoSUNAT; //$_POST["idDetalleServidor"];
		$detalles = array();
		
		$detalles[] = array(
			"id"=> $nota->id, //$idDetalles[$key],
			"idservidor"=> $nota->idDocumentoSUNAT, //$idDetalleServidor[$key],
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


    public function buscarComprobantes(Request $request) {
        $tipo = $request->get('tipo');
        $busqueda = $request->get('query');
        $personaId = $request->get('personaId');
        $result = [];
        if ($tipo == 'V') {
            $result = DB::table('venta as v')
                    ->where('v.idCliente','=', $personaId)
                    ->whereNull('v.deleted_at')
                    ->where(DB::Raw("CONCAT(v.tipoComprobante,LPAD(v.serie,3,'0'),'-',LPAD(v.numero,8,'0'))"),'LIKE','%'.$busqueda.'%')
                    ->select(DB::Raw("CONCAT(v.tipoComprobante,LPAD(v.serie,3,'0'),'-',LPAD(v.numero,8,'0')) as comprobante"), 
                      DB::Raw("CONCAT(v.id,'@', (CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END)) as id"),
                      DB::Raw("DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha"), DB::Raw("FORMAT(v.total, 2) as total")
                    )
                    ->get();
        } else {
            $result = DB::table('compra as c')
                //   ->where('c.situacion','=','N')
                    ->where('c.idProveedor','=', $personaId)
                    ->whereNull('c.deleted_at')
                    ->where(DB::Raw("CONCAT(c.tipoDocumento, c.documento)"),'LIKE','%'.$busqueda.'%')
                    ->select(DB::Raw("CONCAT(c.tipoDocumento, c.documento) as comprobante"),DB::Raw("CONCAT(c.id,'@',c.tipoMoneda) as id"), 
                        DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha"), DB::Raw("FORMAT(c.total, 2) as total")
                    )
                    ->get();
        }


        return ['comprobantes' => $result];
    }

    public function getDetallesComprobante (Request $request) {
        $tipo          = $request->get('tipo');
        $personaId     = $request->get('personaId');
        $comprobanteId = $request->get('comprobanteId');
        $detalles      = []; 
        if ($tipo == 'C') {
            $detalles = DB::table('lote as l')
                    ->join('detallecompra as detc','detc.idCompra','=','l.idCompra')
                    ->join('compra as c','c.id','=','l.idCompra')
                    ->join('stockproductodetalle as spd','spd.idLote','=','l.id')
                    ->where('c.id','=',$comprobanteId)
                    ->whereRaw("spd.idProducto = detc.idProducto")
                    ->where('spd.stock','>',0)
                    ->whereNull('detc.deleted_at')
                    ->select('detc.id','detc.item','detc.descripcion',
                    DB::Raw("detc.preciocompra as preciocompra"),
                    DB::Raw("spd.stock as cantidad"),
                    DB::Raw("spd.stock as cantidadmax"),
                    DB::Raw("(spd.stock * detc.preciocompra) as subtotal"),'c.tipoMoneda',
                    DB::Raw("'false' as isAddItf"))
                    ->orderBy('detc.item','ASC')
                    ->get();
        
        } else {
            // $comprobantePassed = [];
            // $tipoArreglo = 'C';
            // $detalles = DB::table('pagodetalle')
            //             ->where('idVenta',$comprobanteId)
            //             ->where(function ($qq) {
            //                 $qq->whereNotNull('idCotizacion')
            //                     ->orwhereNotNull('idOrden');
            //             })
            //             ->get();
            // if (count($detalles) == 0) {
            //     $comprobantePassed[] = $comprobanteId;
            // } 
            // else {
            //     foreach ($detalles as $dt) {
            //         if (!is_null($dt->idOrden)) {
            //             $comprobantePassed[] = $dt->idOrden;
            //             $tipoArreglo = 'O';
            //         } else {
            //             $comprobantePassed[] = $dt->idCotizacion;
            //         }
            //     }
            // }
            // $det1 = DB::select("SELECT detv.id, detv.item, detv.descripcion, FORMAT(detv.precio,2) as preciocompra, FORMAT(spds.stock,2) as cantidad, FORMAT(spds.stock,2) as cantidadmax,  FORMAT(spds.stock * detv.precio,2) as subtotal, (CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda, 'false' as isAddItf
            //         FROM stockproductodetallesalida as spds,  detalleventa as detv  INNER JOIN venta as v ON v.id = detv.idVenta INNER JOIN pagodetalle as pd ON pd.idVenta = v.id
            //         WHERE detv.id = pd.idDetalleVenta AND detv.idProducto = spds.idProducto AND 
            //         v.id = $comprobanteId AND detv.idProducto IS NOT NULL AND detv.deleted_At IS NULL AND spds.stock > 0 AND
            //         (
            //             (spds.idVenta = pd.idVenta AND spds.idOrdenTrabajo IS NULL AND pd.idOrden) OR
            //             (spds.idOrdenTrabajo = pd.idOrden AND spds.idVenta IS NULL)
            //         )
            //         ");
            
            
            // if (count($detalles) == 0) {
                $bandMovStock = false;
                $bandAnulacion = false;
                $det1 = DB::table('detalleventa as detv')
                        ->join('venta as v','v.id','=','detv.idVenta');
                        
                        $detallesMovStock = DB::table('stockproductodetallesalida as spds')
                                            ->where('idVenta',$comprobanteId)
                                            ->get();

                        if (count($detallesMovStock) == 0) {
                            $detalleAnulacion = DB::table('detalleanulacionnotas as dan')
                                                ->join('anulacionnotas as an', 'an.id','=','dan.idAnulacion')
                                                ->where('an.idVenta', $comprobanteId)
                                                ->get();
                            if (count($detalleAnulacion) > 0) {
                                $det1 = $det1->join('anulacionnotas as an','an.idVenta','=','v.id')
                                             ->leftjoin('detalleanulacionnotas as dan','dan.idAnulacion','=','an.id')
                                             ->whereNull('an.deleted_at')
                                             ->whereNull('dan.deleted_at')
                                             ->where(function ($qq) {
                                                $qq->whereRaw('dan.idProducto = detv.idProducto')
                                                    ->whereNull('dan.idServicio');
                                             })
                                             ->orWhere(function ($qqq) {
                                                $qqq->whereNull('dan.id');
                                             });
                                $bandAnulacion = true;
                            }
                        } else {
                            $det1 = $det1->join('lote as l', 'l.id','=','detv.idLote')
                                    ->join('stockproductodetallesalida as spds','spds.idStockProductoDetalle','=','l.id')
                                    ->whereRaw('detv.idProducto = spds.idProducto')
                                    ->where('spds.stock','>',0);
                            $bandMovStock = true;
                        }
                        // ->leftJoin('anulacionnotas as an','an.idVenta','=','v.id')
                        // ->join('detalleanulacionnotas as dan','dan.idAnulacion','=','an.id')
                        $det1 = $det1->where('v.id',$comprobanteId)
                                ->whereNotNull('detv.idProducto')
                                ->whereNull('detv.deleted_at');
                        if ($bandMovStock) {
                            $det1 = $det1->select('detv.id','detv.item','detv.descripcion',
                                    DB::Raw("FORMAT(detv.precio,2) as preciocompra"),
                                    DB::Raw("FORMAT(spds.stock,2) as cantidad"),
                                    DB::Raw("FORMAT(spds.stock,2) as cantidadmax"),
                                    DB::Raw("FORMAT((spds.stock * detv.precio),2) as subtotal"),
                                    DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda"),
                                    DB::Raw("'false' as isAddItf"));
                        } elseif($bandAnulacion) {
                            $det1 = $det1->select('detv.id','detv.item','detv.descripcion',
                                    DB::Raw("FORMAT(detv.precio,2) as preciocompra"),
                                    DB::Raw("FORMAT((CASE WHEN dan.id IS NOT NULL THEN (detv.cantidad - dan.cantidad) ELSE detv.cantidad END),2) as cantidad"),
                                    DB::Raw("FORMAT((CASE WHEN dan.id IS NOT NULL THEN (detv.cantidad - dan.cantidad) ELSE detv.cantidad END),2) as cantidadmax"),
                                    DB::Raw("FORMAT(((CASE WHEN dan.id IS NOT NULL THEN (detv.cantidad - dan.cantidad) ELSE detv.cantidad END) * detv.precio),2) as subtotal"),
                                    DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda"),
                                    DB::Raw("'false' as isAddItf"));
                            
                        } 
                        $det1 = $det1->distinct();
                            // dd($det1->toSql());
            // } else {
            //     $det1 = DB::table('detalleventa as detv')
            //             ->join('venta as v','v.id','=','detv.idVenta')
            //             ->join('lote as l', 'l.id','=','detv.idLote')
            //             ->join('stockproductodetallesalida as spds','spds.idStockProductoDetalle','=','l.id')
            //             ->leftJoin('anulacionnotas as an','an.idVenta','=','v.id')
            //             ->join('detalleanulacionnotas as dan','dan.idAnulacion','=','an.id')
            //             ->whereIn('v.id',$comprobantePassed)
            //             ->where(function ($qq) {
            //                 $qq->whereRaw('detv.idProducto = dan.idProducto')
            //                    ->orWhereNull('dan.idProducto');
            //             })
            //             ->whereNull('detv.deleted_at')
            //             ->select('detv.id','detv.item','detv.descripcion',
            //             DB::Raw("FORMAT(detv.precio,2) as preciocompra"),
            //             DB::Raw("FORMAT(detv.cantidad,2) as cantidad"),
            //             DB::Raw("FORMAT(detv.cantidad,2) as cantidadmax"),
            //             DB::Raw("FORMAT((detv.cantidad * detv.precio),2) as subtotal"),
            //             DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda"),
            //             DB::Raw("'false' as isAddItf"));

           
            //     /* $det1 = DB::table('detalleventa as detv')
            //             ->join('venta as v','v.id','=','detv.idVenta')
            //             ->join('pagodetalle as pd','pd.idVenta','v.id')
            //             ->join('stockproductodetallesalida as spds','spds.idProducto','=', 'detv.idProducto');

            //             if ($tipoArreglo == 'O') {
            //                 $stockDetalleLotes = DB::table('detalleordentrabajo as dot')
            //                             ->join('detallecotizacion as dc','dc.idCotizacion', '=', 'dot.idCotizacion')
            //                             ->join('lote as l','l.id','=','dc.idLote')
            //                             ->where('dc.tipoDetalle','P')
            //                             ->whereIn('dot.idOrdenTrabajo',$comprobantePassed)
            //                             ->select(DB::Raw("GROUP_CONCAT(dc.idLote) as idLotes"))
            //                             ->distinct()
            //                             ->first();
            //             } else {
            //                 $stockDetalleLotes = DB::table('detallecotizacion as dc')
            //                             ->join('lote as l','l.id','=','dc.idLote')
            //                             ->whereIn('dc.idCotizacion',$comprobantePassed)
            //                             ->where('dc.tipoDetalle','P')
            //                             ->select(DB::Raw("GROUP_CONCAT(dc.idLote) as idLotes"))
            //                             ->distinct()
            //                             ->first();
            //             }

            //             if (!is_null($stockDetalleLotes)){
            //                 $arrayL = explode(',', $stockDetalleLotes->idLotes);
            //                 $det1 = $det1->whereIn('spds.idStockProductoDetalle',$arrayL);
            //             }

            //             $det1 = $det1->whereRaw('pd.idDetalleVenta = detv.id')
            //                     ->where('v.id', $comprobanteId)
            //                     ->whereNotNull('detv.idProducto')
            //                     ->whereNull('detv.deleted_at')
            //                     ->whereRaw('detv.cantidad >= spds.stock')
            //                     ->where('spds.stock','>',0)
            //                     ->select('detv.id','detv.item','detv.descripcion',
            //                     DB::Raw("FORMAT(detv.precio,2) as preciocompra"),
            //                     DB::Raw("FORMAT(spds.stock,2) as cantidad"),
            //                     DB::Raw("FORMAT(spds.stock,2) as cantidadmax"),
            //                     DB::Raw("FORMAT(spds.stock * detv.precio,2) as subtotal"),
            //                     DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda"),
            //                     DB::Raw("'false' as isAddItf"));
            //     */
            // }
            // $det2 = DB::table('detalleventa as detv')
            //         ->join('venta as v','v.id','=','detv.idVenta')
            //         ->leftjoin('anulacionnotas as an','an.idVenta','=','v.id')
            //         ->leftjoin('detalleanulacionnotas as detan','detan.idAnulacion','=','an.id')
            //         ->where('v.id','=',$comprobanteId)
            //         ->where(function ($qq) {
            //             $qq->whereRaw('detan.idServicio = detv.idServicio')
            //                 ->orwhereNull('detan.idServicio');
            //         })
            //         ->whereNotNull('detv.idServicio')
            //         ->select('detv.id','detv.item','detv.descripcion',
            //         DB::Raw("FORMAT(detv.precio,2) as preciocompra"),
            //         DB::Raw("FORMAT((CASE WHEN detan.idServicio IS NOT NULL AND (detv.cantidad - detan.cantidad) > 0 THEN (detv.cantidad - detan.cantidad) ELSE  detv.cantidad END),2) as cantidad"),
            //         DB::Raw("FORMAT((CASE WHEN detan.idServicio IS NOT NULL AND (detv.cantidad - detan.cantidad) > 0 THEN (detv.cantidad - detan.cantidad) ELSE  detv.cantidad END),2) as cantidadmax"),
            //         DB::Raw("FORMAT((CASE WHEN detan.idServicio IS NOT NULL AND (detv.cantidad - detan.cantidad) > 0 THEN (detv.cantidad - detan.cantidad) ELSE  detv.cantidad END) * detv.precio,2) as subtotal"),
            //         DB::Raw("(CASE v.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda"),
            //         DB::Raw("'false' as isAddItf"))
            //         ->distinct();
        
            // $query    = $det2->unionAll($det1);
            // $querySql	= $query->toSql();
            //     // $lista = DB::query("SELECT * FROM ($query) as a ORDER BY a.created_at ASC");

            // $detalles = DB::table(DB::Raw("($querySql) as a ORDER BY item ASC "))->mergeBindings($query);
            // $detalles = $detalles->get()->toArray();
            $detalles = DB::select("CALL listarDetallesAnulacion(?)",[$comprobanteId]);
        }
        return ['detalles' => $detalles, 'estado' => true];
        
    }

    public function validarAnulacion (Request $request) {
		$reglas = [
            'tipo_operacion'=>  'required',
            'motivo'=> ($request->get('tipo_operacion')=='V'?'required':'nullable'),
            'fecha' => 'required',
            'idComprobante' => 'required',
            'idPersona' => 'required',
            'total'     => 'required|numeric|min:0.01',
            'traer_datos' => 'nullable',
            'listDetalles'=> 'required',
            'nroDocumento'=> ($request->get('tipo_operacion')=='C'?'required':'nullable')
		];

        $mensajes = [
            'tipo_operacion.required'=> 'Indique a que está aplicado el Documento de Baja',
			'motivo.required'=> 'Indique Motivo',
            'fecha.required'=> 'Indique Fecha',
            'idComprobante.required'=> 'Indique Comprobante',
            'idPersona.required'=> 'Indique Persona',
            'total.required'    => 'Indique Total',
            'total.numeric' => 'Total debe ser numérico',
            'total.min'     => 'Total debe ser mayor a 0',
			'listDetalles.required'=> 'Indique Detalles',
            'nroDocumento.required'=> 'Indique Documento de Baja'
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

    public function retornarStockCV($detalle, $ref, $tipo) {
        if ($tipo == 'C') {
            $lote = DB::table('lote as l')
                    ->where('l.idProducto',$detalle->idProducto)
                    ->where('l.idAlmacen',$ref->idAlmacen)
                    ->where('l.idCompra', $ref->id)
                    ->update(['deleted_at' => null]);
            
            $loteDetalle = Lote::where('idProducto',$detalle->idProducto)
                            ->where('idAlmacen',$ref->idAlmacen)
                            ->where('idCompra', $ref->id)
                            ->first();
            
            $spd = DB::table('stockproductodetalle')
                   ->where('idProducto',$detalle->idProducto)
                   ->where('idLote',$loteDetalle->id)
                   ->where('idTienda', $ref->idTienda)
                   ->where('idAlmacenSalida',$ref->idAlmacen)
                   ->update(['deleted_at' => null]);
            
            $stockDetalle = StockProductoDetalle::where('idProducto',$detalle->idProducto)
                            ->where('idTienda', $ref->idTienda)
                            ->where('idLote',$loteDetalle->id)
                            ->where('idAlmacenSalida',$ref->idAlmacen)
                            ->first();
            if (!is_null($stockDetalle)) {
                $stockDetalle->stock = $stockDetalle->stock - $detalle->cantidad;
                $stockDetalle->update();
                
                $sp = StockProducto::where('idProducto',$detalle->idProducto)
                        ->where('idAlmacen',$ref->idAlmacen)
                        ->first();
                if(!is_null($sp)) {
                    $sp->totalCompras = $sp->totalCompras - $detalle->cantidad;
                    $sp->update();
                }
            }
        } else {
            if (!is_null($detalle->idProducto)) {
                $spds = DB::table('stockproductodetallesalida as spds')
                        ->join('stockproductodetalle as spd','spd.id','=','spds.idStockProductoDetalle')
                        ->whereRaw('spds.idProducto = spd.idProducto')
                        ->where('spds.idProducto',$detalle->idProducto)
                        ->where('spd.idTienda', $ref->idTienda)
                        ->where('spds.idAlmacen',$ref->idAlmacenSalida)
                        ->where('spds.idVenta',$ref->id)
                        ->update(['spds.deleted_at' => null]);
                
                $stockProductoDetalleSalida = StockProductoDetalleSalida::where('idProducto',$detalle->idProducto)
                                                ->where('idAlmacen',$ref->idAlmacenSalida)
                                                ->where('idVenta',$ref->id)
                                                ->first();
            } else {
                $spds = DB::table('stockproductodetallesalida as spds')
                        ->join('stockproductodetalle as spd','spd.id','=','spds.idStockProductoDetalle')
                        ->whereRaw('spds.idAuto = spd.idAuto')
                        ->where('spds.idAuto',$detalle->idAuto)
                        ->where('spd.idTienda', $ref->idTienda)
                        ->where('spds.idAlmacen',$ref->idAlmacenSalida)
                        ->where('spds.idVenta',$ref->id)
                        ->update(['spds.deleted_at' => null]);
                
                $stockProductoDetalleSalida = StockProductoDetalleSalida::where('idAuto',$detalle->idAuto)
                                                ->where('idAlmacen',$ref->idAlmacenSalida)
                                                ->where('idVenta',$ref->id)
                                                ->first();
            }
           
            if (!is_null($stockProductoDetalleSalida)) {
                $stockProductoDetalleSalida->stock = $stockProductoDetalleSalida->stock - $detalle->cantidad;
                $stockProductoDetalleSalida->update();
                
                $spd = DB::table('stockproductodetalle')
                        ->where('id',$stockProductoDetalleSalida->idStockProductoDetalle)
                        ->update(['deleted_at' => null]);

                $stockDetalle = StockProductoDetalle::find($stockProductoDetalleSalida->idStockProductoDetalle);
                if (!is_null($stockDetalle)) {
                    $stockDetalle->stock = $stockDetalle->stock + $detalle->cantidad;
                    $stockDetalle->update();
                    
                    if (!is_null($detalle->idProducto)) {
                        // PARA PRODUCTOS
                        $sp = StockProducto::where('idProducto',$detalle->idProducto)
                                ->where('idAlmacen',$ref->idAlmacenSalida)
                                ->first();
                        if(!is_null($sp)) {
                            // $sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
                            $sp->totalVentas = $sp->totalVentas - $detalle->cantidad;
                            $sp->update();
                        }
                    } elseif(!is_null($detalle->idAuto)) {
                        // PARA AUTOS
                        $sp = StockAuto::where('idAuto',$detalle->idAuto)
                                ->where('idAlmacen',$ref->idAlmacenSalida)
                                ->first();
                        if(!is_null($sp)) {
                            // $sp->totalCompras = $sp->totalCompras + $detalle->cantidad;
                            $sp->totalVentas = $sp->totalVentas - $detalle->cantidad;
                            $sp->update();
                        }
                    }
                }
            }
            
        }
	}

    public function eliminarReferencia($ref, $nota, $tipo) {
        if ($tipo == 'V') {
            $detallesV = DetalleVenta::where('idVenta',$ref->id)->get();
        } else {
            $detallesV = DetalleCompra::where('idCompra',$ref->id)->get();
        }

        $detallesA = DetalleAnulacionNotas::where('idAnulacion',$nota->id)->get();
        $acumDelete = 0;
        foreach ($detallesV as $det) {
            foreach ($detallesA as $detA) {
                if ($detA->idProducto == $det->idProducto && !is_null($det->idProducto)) {
                    if ($detA->cantidad == $det->cantidad) {
                        $acumDelete++;
                        $det->delete();
                    }
                } elseif($detA->idServicio == $det->idServicio && !is_null($det->idServicio)) {
                    if ($detA->cantidad == $det->cantidad) {
                        $acumDelete++;
                        $det->delete();
                    }
                } elseif($detA->idAuto == $det->idAuto && !is_null($det->idAuto)) {
                    if ($detA->cantidad == $det->cantidad) {
                        $acumDelete++;
                        $det->delete();
                    }
                }
            }
        }

        #VALIDACION SI SE ELIMINA COMPRA O VENTA
        $total = count($detallesV); 
        if ($acumDelete == $total) {
            if ($tipo == 'V') { $ref->situacion = 'A'; }
            $ref->update();
            $ref->delete();
        }

        if($ref->total == $nota->total) {
            if ($tipo == 'V') { $ref->situacion = 'A'; }
            $ref->update();
            $ref->delete();
        }

        if ($tipo == 'V') {
            $totalAcum = AnulacionNotas::where('idVenta', $ref->id)->sum('total');
        } else {
            $totalAcum = AnulacionNotas::where('idCompra', $ref->id)->sum('total');
        }

        if($totalAcum == $ref->total) {
            if ($tipo == 'V') { $ref->situacion = 'A'; }
            $ref->update();
            $ref->delete();
        }
    }

    public function guardarNota (Request $request) {
        $errors = $this->validarAnulacion($request);
      	if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
            DB::beginTransaction();
			$band = true;
			$errors = [];
            $idResult = 0;
            $valid_venta = null;
            try{
                $tipoOperacion = $request->get('tipo_operacion');
                $refId  = $request->get('idComprobante');
                $ref = null;
                
                $nota = new AnulacionNotas;
                $nota->fecha = $request->get('fecha');
                $nota->idPersonal = Auth::user()->usuarioId;
				
                if ($tipoOperacion == 'C') {
                    $ref = Compra::find($refId);
					$nota->idCompra = $ref->id;
                    $nota->idCliente = $ref->idProveedor;
                    $nota->documentoCompra = $request->get('nroDocumento');
                } else {
                    $traer_datos = $request->get('traer_datos');
                    $ref = Venta::find($refId);
                    // if (is_null($traer_datos)) {
                    $totalAnulacion = AnulacionNotas::where('idVenta',$ref->id)
                                        ->sum('total');
                    if ($totalAnulacion + $request->get('total') > $ref->total) {
                        $band = false;
                        $errors[] = 'Verifique Monto de Nota de Crédito, Supera al valor permitido: '.$ref->total-$totalAnulacion;
                    }
                    // }
                    $tipoNota = ($ref->tipoComprobante == 'B'?'BC':'FC');

					$serie = Serie::where('idLocal','=',$ref->idTienda)
                            ->where('tipoLocal','=','T')
							->where('tipoDocumento', $tipoNota)
							->first();
                    $nota->serie = $serie->serie;
                    $nota->numero = $serie->numero + 1;
                    $nota->tipoReferencia = $ref->tipoComprobante;
					$nota->idVenta = $ref->id;
                    $nota->idCliente = $ref->idCliente;
                    $nota->motivo = $request->get('motivo');
                }
                $isIgv = ($ref->igv>0?true:false);
                $nota->total = $request->get('total');
                $nota->subtotal = $nota->total;
                $nota->igv = 0;
                $nota->tipoDocumento = 'NC';
                $nota->tipoComprobante = $tipoOperacion;

                if ($isIgv) {
                    $nota->subtotal = round(($nota->total)/1.18,2);
                    $nota->igv  = round(($nota->total - $nota->subtotal),2);
                } 

                if ($tipoOperacion == 'V') {
                    $valid_venta = DB::table('anulacionnotas')->where('serie','=',$nota->serie)
                                    ->where('numero','=',$nota->numero)
                                    ->where('tipoReferencia','=', $nota->tipoReferencia)
                                    ->whereNotNull('idVenta')
                                    ->where('tipoComprobante','V')
                                    ->first();
                }

                if ($band) {
                    if($tipoOperacion == 'V') {
                        if (is_null($valid_venta)) {
                            $serie->numero = $nota->numero;
                            $serie->update();
                        } else {
                            $band = false; 
                            $errors[] = 'Documento de Baja No se Puede Registrar, Correlativo no Disponible';
                        }
                    }

                    if ($band) {
                        $nota->save();
                        $idAnulacion = $nota->id;
                        
                        $detalles = explode(',',$request->get('listDetalles'));
                        $i = 1;
                        $bandValidS = false;
                        foreach ($detalles as $det) {
                            $detalle = new DetalleAnulacionNotas;
                            $bandValidS = false;
                            $stock = null;
                            if ($tipoOperacion == 'V') {
                                $refDet = null;
                                if (!is_null($traer_datos)) {
                                    $refDet = DB::table('detalleventa')
                                             ->where('id',$det)
                                             ->first();
                                    if (!is_null($refDet->idProducto)) {
                                        $stock = DB::table('stockproductodetallesalida as spds')
                                                ->join('stockproductodetalle as spd','spd.id','spds.idStockProductoDetalle')
                                                ->whereRaw('spd.idProducto = spds.idProducto')
                                                ->where('spd.idProducto', $refDet->idProducto)
                                                ->where('spd.idLote', $refDet->idLote)
                                                ->where('spds.idVenta',$nota->idVenta)
                                                ->where('spds.idAlmacen', $ref->idAlmacenSalida)
                                                ->select('spds.*')
                                                ->first();
                                    } elseif (!is_null($refDet->idAuto)) {
                                        $stock = DB::table('stockproductodetallesalida as spds')
                                                ->join('stockproductodetalle as spd','spd.id','spds.idStockProductoDetalle')
                                                ->whereRaw('spd.idAuto = spds.idAuto')
                                                ->where('spd.idAuto', $refDet->idAuto)
                                                ->where('spd.idLoteAuto', $refDet->idLoteAuto)
                                                ->where('spds.idVenta',$nota->idVenta)
                                                ->where('spds.idAlmacen', $ref->idAlmacenSalida)
                                                ->select('spds.*')
                                                ->first();
                                    } else {
                                        $bandValidS = true;
                                    }
                                } else {
                                    $bandValidS = true;
                                }
                                
                            } else {
                                $refDet = DetalleCompra::find($det);
                                $loteDet = DB::table('lote')
                                            ->where('idCompra', $nota->idCompra)
                                            ->where('tipoMoneda', $ref->tipoMoneda)
                                            ->where('idProducto',$refDet->idProducto)
                                            ->select('id')
                                            ->first();
                                if (!is_null($loteDet)) {
                                    // DB::table('lote')
                                    // ->where('id',$loteDet->id)
                                    // ->update(['deleted_at'=> null]);

                                    $stock = DB::table('stockproductodetalle')
                                            ->where('idLote',$loteDet->id)
                                            ->where('idProducto',$refDet->idProducto)
                                            ->first();
                                    // ->update(['deleted_at'=> null]);

                                    // $stock = StockProductoDetalle::where('idLote',$loteDet->id)
                                    //          ->first();
                                    
                                }
                            }
                            if (!is_null($stock)) {
                                if ($stock->stock >= $request->get('cantidad_'.$det)) {
                                    $bandValidS = true;
                                }
                            } else {
                                if (!is_null($traer_datos)) {
                                    if ($tipoOperacion == 'V') {
                                        $validPagosRefs = DB::table('pagodetalle as pd')
                                                            ->where('pd.idVenta', $nota->idVenta)
                                                            ->where(function($qq) {
                                                                $qq->whereNotNull('pd.idCotizacion')
                                                                    ->orWhereNotNull('pd.idOrden');
                                                            })->get();
                                        if (count($validPagosRefs) == 0){
                                            if (!is_null($refDet->idProducto)) {
                                                $band = false; 
                                                $errors[] ='Producto '.$request->get('descripcion_'.$det).' no tiene ese stock disponible, Stock Actual: '.$stock->stock;
                                                break;
                                            } elseif (!is_null($refDet->idAuto)) {
                                                $band = false; 
                                                $errors[] ='Auto '.$request->get('descripcion_'.$det).' no tiene ese stock disponible, Stock Actual: '.$stock->stock;
                                                break;
                                            } else {
                                                $bandValidS = true;
                                            }
                                        } else {
                                            $bandValidS = true;
                                        }
                                    } else {
                                        $band = false; 
                                        if (!is_null($refDet->idProducto)) {
                                            $errors[] ='Producto '.$request->get('descripcion_'.$det).' no encontrado.';
                                        } else {
                                            $errors[] ='Auto '.$request->get('descripcion_'.$det).' no encontrado.';
                                        }
                                    }
                                } else {
                                    $bandValidS = true;
                                }
                            }

                            if ($bandValidS) {
                                $detalle->item = $i;
                                $detalle->cantidad = $request->get('cantidad_'.$det);
                                $detalle->descripcion = $request->get('descripcion_'.$det);
                                $detalle->precio = $request->get('preciounit_'.$det);
                                $detalle->subTotal = $request->get('subtotal_'.$det);

                                if (!is_null($refDet)) {
                                    $detalle->idProducto = $refDet->idProducto;
                                    $detalle->idServicio = $refDet->idServicio;
                                    $detalle->idAuto = $refDet->idAuto;
                                }
                                $detalle->idAnulacion = $idAnulacion;
                                $detalle->save();
                                
                                $this->retornarStockCV($detalle, $ref, $tipoOperacion);
                                
                            }
                            $i++;

                        }

                        if ($band) {
                            $this->eliminarReferencia($ref, $nota, $tipoOperacion);       
                            if($tipoOperacion == 'V') {
                                $statusR = $this->declararNotaCredito($this->user_wsdl,$this->pass_wsdl, $nota, $ref);
                                if ($statusR) {
                                    $idResult = $nota->idDocumentoSUNAT;
                                    $errors[] = 'Documento de Baja Registrado Correctamente';
                                } else {
                                   $band = false; 
                                   $errors[] = 'Documento de Baja no se pudo Declarar';
                                }
                            } else {
                                $errors[] = 'Documento de Baja Registrado Correctamente';
                            }
                        }
                    }
                    // } else {
                    //     $band = false;
                    //     $errors[] = 'Documento de Baja No se Puede Registrar, Correlativo no Disponible';
                    // }
                }
            } catch(\Exception $ex) {
        		$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
		    }

            if ($band) {
                DB::commit();
            } else {
            	DB::rollback();
		    }
                
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $idResult];
		
        }
       
    }

    public function declararNotaCredito($user,$pass,$nota_credito,$venta){
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
        $tMotivo = $nota_credito->motivo;
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

        // $nota_credito->motivo = $motivo;
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

        $detalles_venta =  DetalleAnulacionNotas::where('idAnulacion','=',$nota_credito->id)->get();
        foreach ($detalles_venta as $det_v) {
			$pr = null;
            if (!is_null($det_v->idProducto)) {
                $pr = Producto::find($det_v->idProducto);
            }
		
			$tipo_detalle = ($det_v->precio > 0?'V':'R');
			$igv_sunat = ($tipo_detalle == 'R'?'21':$venta->igv_sunat);
			$descripcion = $det_v->descripcion;
			// $cat = CategoriaProducto::findOrFail($pr->codCategoria);
			// /*' A-554 '*/
			// $descripcion =$cat->nombre.' '. $pr->nombre.' '.$pr->forma.' '.$pr->medida.' x '. $pr->espesor.' mm '.$pr->acabado.' x '. $pr->tamaño. ' metros C:'.$pr->calidad; //$pr->unidad;
		
			$detalles[] = array(
				"tipodetalle"=>$tipo_detalle,//$tipodetalle[$key],
				"codigo"=>(!is_null($pr)?$pr->codRegistro:'-'),
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
			$nota_credito->update();
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

    public function anularBaja ($id, Request $request) {
        DB::beginTransaction();
        $band = true;
        $errors = [];
        try{
            $anulacion = AnulacionNotas::find($id);
            if (!is_null($anulacion)) {
                $detalles = DetalleAnulacionNotas::where('idAnulacion', $anulacion->id)
                            ->get();
                if ($anulacion->tipoComprobante == 'C') {
                    foreach ($detalles as $det) {
                        if (!is_null($det->idProducto)) {
                            // Actualizamos Lote
                            DB::table('lote as l')
                            ->where('idCompra', $anulacion->idCompra)
                            ->whereNull('idGuiaCompra')
                            ->where('idProducto', $det->idProducto)
                            ->where('idAlmacen',$this->almacenId)
                            ->update(['deleted_at' => null]);

                            $lote = Lote::where('idCompra', $anulacion->idCompra)
                                    ->whereNull('idGuiaCompra')
                                    ->where('idProducto', $det->idProducto)
                                    ->where('idAlmacen',$this->almacenId)
                                    ->first();
                            if (!is_null($lote)) {
                                // Actualizamos Stock
                                DB::table('stockproductodetalle as spd')
                                ->where('idLote',$lote->id)
                                ->where('idAlmacenSalida',$lote->idAlmacen)
                                ->where('idProducto', $lote->idProducto)
                                ->update(['deleted_at' => null]);
                                
                                $spd = StockProductoDetalle::where('idLote',$lote->id)
                                        ->where('idAlmacenSalida',$lote->idAlmacen)
                                        ->where('idProducto', $lote->idProducto)
                                        ->first();
                                if (!is_null($spd)) {
                                    $spd->stock = $spd->stock + $det->cantidad;
                                    $spd->update();
                                    
                                    // Actualizamos Stock General
                                    $stock = StockProducto::where('idProducto',$spd->idProducto)
                                                ->where('idAlmacen',$spd->idAlmacenSalida)
                                                ->first();
                                    if (!is_null($stock)) {
                                        $stock->totalCompras = $stock->totalCompras + $det->cantidad;
                                        $stock->update();
                                    }
                                }
                            }
                        }

                        $det->delete();
                    }
                } else {
                    /***
                     * STOCK: 20        => SM: 10            SE: 10 + 5 -5   => SM: 10
                     * V: 10            S: 10  <> 5 <> 10                            => S: 15
                     * NC: 5            E: 5                                => E: 5
                     * ANNC: 5          S:5
                     * 
                     */
                    foreach ($detalles as $det) {
                        if (!is_null($det->idProducto)) {
                            $detalleV = DB::table('detalleventa')->where('idProducto', $det->idProducto)
                                        ->where('idVenta', $anulacion->idVenta)
                                        ->whereNotNull('idLote')
                                        ->first();
                            if (!is_null($detalleV)) {
                                // Actualizamos StockProductoDetalle
                                DB::table('stockproductodetalle')
                                ->where('idProducto', $det->idProducto)
                                ->where('idAlmacenSalida',$this->almacenId)
                                ->where('idLote', $detalleV->idLote)
                                ->update(['deleted_at' => null]);

                                $spd = StockProductoDetalle::where('idProducto', $det->idProducto)
                                        ->where('idAlmacenSalida',$this->almacenId)
                                        ->where('idLote', $detalleV->idLote)
                                        ->first();

                                if (!is_null($spd)) {
                                    $spd->stock = $spd->stock - $det->cantidad;
                                    $spd->update();

                                    // Actualizamos Salidas
                                    DB::table('stockproductodetallesalida')
                                    ->where('idProducto', $det->idProducto)
                                    ->where('idAlmacen',$this->almacenId)
                                    ->where('idStockProductoDetalle', $detalleV->idLote)
                                    ->where('idVenta',$anulacion->idVenta)
                                    ->update(['deleted_at' => null]);
        
                                    $spds = StockProductoDetalleSalida::where('idProducto', $det->idProducto)
                                            ->where('idAlmacen',$this->almacenId)
                                            ->where('idStockProductoDetalle', $detalleV->idLote)
                                            ->where('idVenta',$anulacion->idVenta)
                                            ->first();
                                    if (!is_null($spds)) {
                                        $spds->stock = $spds->stock + $det->cantidad;
                                        $spds->update();

                                        $sp = StockProducto::where('idProducto', $spds->idProducto)
                                                ->where('idAlmacen', $spds->idAlmacen)
                                                ->first();
                                        if (!is_null($sp)) {
                                            // $sp->totalCompras = $sp->totalCompras - $det->cantidad;
                                            $sp->totalVentas = $sp->totalVentas + $det->cantidad;
                                            $sp->update();
                                        }

                                    }
                                }
                            }             
                        }

                        $det->delete();
                    }
                    $response = $this->declararAnulacion($anulacion);
                    if (!$response['status']) {
                        $band = false;
                        $errors[] = $response['errores'];
                    }
                }
                $anulacion->situacion = 'A';
                $anulacion->idPersonalAnula = Auth::user()->usuarioId;
                $anulacion->update();
                $anulacion->delete();
                $this->getActualizarReferencia($anulacion);
                if ($band) {
                    $errors[] = 'Documento de Baja Anulado Correctamente';
                }
            }
        } catch(\Exception $ex) {
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
    
    public function getDetalles ($id, Request $request) {
		$detalles = DB::table('anulacionnotas as an')
					->leftJoin('detalleanulacionnotas as det','det.idAnulacion','=','an.id')
					->where('an.id','=',$id)
					->select('det.cantidad','det.descripcion','det.precio','det.subTotal',
							'det.item','det.id','an.total')
					->orderBy('det.id','ASC')
					->get();

		$total = 0;
		if (count($detalles)) {
			$total = $detalles[0]->total;
		}

    	return ['detalles' => $detalles,'total' => $total];
	}

    public function getActualizarReferencia ($nota) {
        if ($nota->tipoComprobante == 'V') {
            // Actualizar Documento de Referencia
            DB::table('venta')
            ->where('id',$nota->idVenta)
            ->update(['deleted_at' => null, 'situacion' => 'V']);

            DB::table('detalleventa')
            ->where('idVenta',$nota->idVenta)
            ->update(['deleted_at' => null]);

            #ACTUALIZAR ORDEN Y COTIZACION
            $pagos = PagoDetalle::where('idVenta','=',$nota->idVenta)
                    ->where(function ($qq) {
                        $qq->whereNotNull('idCotizacion')
                            ->orWhereNotNull('idOrden');
                    })
                    ->select('idOrden','idCotizacion')
                    ->get();

            foreach ($pagos as $pg) {
                if (!is_null($pg->idOrden)) {
                    DB::table('ordentrabajo')
                    ->where('id', $pg->idOrden)
                    ->where('situacionFacturado', 'P')
                    ->update(['situacionFacturado' => 'N','updated_at' => date('Y-m-d H:i:s') ]);
                    
                } else {
                    DB::table('cotizacion')
                    ->where('id', $pg->idCotizacion)
                    ->where('situacionFacturado', 'P')
                    ->update(['situacionFacturado' => 'N','updated_at' => date('Y-m-d H:i:s') ]);
                }					
            }
        } else {
            // Actualizar Documento de Referencia
            DB::table('compra')
            ->where('id',$nota->idCompra)
            ->update(['deleted_at' => null, 'idPersonalEliminar' => null]);

            DB::table('detallecompra')
            ->where('idCompra',$nota->idCompra)
            ->update(['deleted_at' => null]);
        }
    }
	

}
