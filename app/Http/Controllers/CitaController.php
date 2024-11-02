<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Menu;
use App\Models\MenuUsuario;
use App\Models\TipoUsuario;
use App\Models\MarcaAuto;
use App\Models\Cita;
use App\Models\Persona;
use App\Models\Serie;

use App\Libraries\Funciones;
use DB;
use Auth;

use Validator;

#PARA EL ENVIO DE MAIL

use App\Mail\NotificacionCita;
use Illuminate\Support\Facades\Mail;

class CitaController extends Controller
{
	public $almacenId = 2;
	public $tiendaId  = 1;

    public function getAll (Request $request) {
		$citas = DB::table('cita as c')->leftJoin('persona as cl','cl.id','=','c.idCliente')
	    // ->whereNull('c.deleted_at')
		// ->leftJoin('macaauto as mc','mc.id','=','c.idMarcaAuto')
		->select(DB::Raw("(CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
		'c.id','c.fecha','c.placa','c.fecha','c.hora','c.duracion','c.situacion','c.indicaciones')
		->get();

		$arrCitas = [];
		foreach ($citas as $c) {
			$f = date('Y-m-d H:i:s',strtotime($c->fecha.' '.$c->hora));
			$ff = date('Y-m-d H:i:s',strtotime("$f + $c->duracion minute"));
			$fu = explode(' ',$ff);
			$a = [
				'id' => $c->id,
				'title' =>  $c->cliente.' - '.$c->placa.', INDICACIONES: '.$c->indicaciones,
				'start' => $c->fecha. 'T'.$c->hora,
				'end' => $fu[0].'T'.$fu[1],
				'allDay' => false,
				'color' => ($c->situacion == 'P'?'yellow':($c->situacion=='A'?'green':'red')),
				'textColor' =>($c->situacion=='P'?'#000':'#fff')
			];
			$arrCitas[] = $a;
			//     title,
      //     start: selectInfo.startStr,
      //     end: selectInfo.endStr,
      //     allDay: selectInfo.allDay
		}

		return ['citas' => $arrCitas];
		// return ['autos' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Auto':' Autos'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	} 
		 

    public function getCita ($id, Request $request) {
		$cita = DB::table('cita')->leftjoin('persona as cl','cl.id','=','cita.idCliente')
				->where('cita.id','=',$id)
				->select('cl.documento','cl.apellidos','cl.nombres','cl.correoElectronico','cl.telefono','cl.razonSocial','cita.*', DB::Raw("CONCAT('CT', LPAD(cita.serie,2,'0') ,'-', LPAD(cita.numero,8,'0')) as numeroc"))
				->first();
    
    	if (!is_null($cita)) {
    		$respuesta = ['estado' => true, 'cita' => $cita,'bloqueado' => (!is_null($cita->deleted_at)?true:false)];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
    }

    public function guardarCita(Request $request) {
		// dd($request);
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
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

				$existe = Persona::where('documento','=',$request->get('documento'))
						 ->where('tipoPersona','=','C')
						 ->first();
						 
			    
				if (is_null($existe)) {
					$cliente = new Persona;
				} else {
					$cliente = $existe;
				}
				$cliente->documento = $request->get('documento');
				$cliente->tipoDocumento = ($request->get('tipodocumento') == 'B'?'PN':'PJ');
				$cliente->tipoPersona = 'C';
				$cliente->razonSocial = $request->get('razonSocial');
				$cliente->apellidos = $request->get('apellidos');
				$cliente->nombres = $request->get('nombres');
				$cliente->correoElectronico = $request->get('correo');
				$cliente->telefono = $request->get('telefono');

				if (is_null($existe)) {
					$cliente->save();
				} else {
					$cliente->update();
				}

				$id_cliente = $cliente->id;
				$serie = null;
				
				if ($id == 0) {
					$cita = new Cita;
					$cita->situacion = 'A';
					
					// *** SERIE ***
					$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
							->where('tipoDocumento','=','CT')
							->first();
					$cita->serie = $serie->serie;
					$cita->numero = $serie->numero + 1;
					
				} else {
					$cita = Cita::find($id);
					// $cita->deleted_at = null;
					$cita->situacion = $request->get('situacion');
				}
				
				// dd($cita);
			
				$cita->idCliente   = $id_cliente;
				$cita->idMarcaAuto = $id_marca;
				$cita->modelo      = $request->get('modelo');
				$cita->anio        = $request->get('anio');
				$cita->placa     = $request->get('placa');
				$cita->kilometraje = $request->get('kilometraje');
				$cita->tipoServicio = $request->get('tipoServicio');
				$cita->fecha = $request->get('fecha');
				$cita->hora = $request->get('hora');
				$cita->duracion = $request->get('duracion');
				$cita->con_cita = $request->get('con_cita');
				$cita->indicaciones = $request->get('indicaciones');
				$cita->idUsuario = Auth::user()->usuarioId;
				$cita->con_soat	  = $request->get('con_soat');
				$cita->con_seguro = $request->get('con_seguro');
				$cita->vin = $request->get('vin');

				if ($id == 0) {
					$cita->save();
					if (!is_null($serie)) {
						$serie->numero = $cita->numero;
						$serie->update();
					}
					// $this->guardarStock($cita->id);
					$cad = ' Registrado';
				} else {
					$cita->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Cita'.$cad.' Correctamente';
	

			}catch(\Exception $ex){
			  	$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	}

    public function guardarCitaPublic (Request $request) {
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
			$id_marca = 0;
			
			try{
				if ($request->get('select_marca') === 'otro') {
					$nombre = $request->get('marca');
					$existe = MarcaAuto::where('nombre','=', $nombre)->first();
					$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
					if(!is_null($existe)){
						$id_marca = $existe->id;
					}
				}else{
					$id_marca = $request->get('select_marca');
				}

				$existe = Persona::where('documento','=',$request->get('documento'))
						 ->where('tipoPersona','=','C')
						 ->first();
						 
			    
				if (is_null($existe)) {
					$cliente = new Persona;
				} else {
					$cliente = $existe;
				}
				$cliente->documento = $request->get('documento');
				$cliente->tipoDocumento = ($request->get('tipodocumento') == 'B'?'PN':'PJ');
				$cliente->tipoPersona = 'C';
				$cliente->razonSocial = $request->get('razonSocial');
				$cliente->apellidos = $request->get('apellidos');
				$cliente->nombres = $request->get('nombres');
				$cliente->correoElectronico = $request->get('correo');
				$cliente->telefono = $request->get('telefono');

				if (is_null($existe)) {
					$cliente->save();
				} else {
					$cliente->update();
				}

				$id_cliente = $cliente->id;
				$serie = null;
				
				if ($id == 0) {
					$cita = new Cita;
					$cita->situacion = 'P';
					
					// *** SERIE ***
					$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
							->where('tipoDocumento','=','CT')
							->first();
					$cita->serie = $serie->serie;
					$cita->numero = $serie->numero + 1;
					
				} else {
					$cita = Cita::find($id);
					// $cita->deleted_at = null;
					$cita->situacion = $request->get('situacion');
				}
				
				// dd($cita);
			
				$cita->idCliente   = $id_cliente;
				$cita->idMarcaAuto = ($id_marca!=0?$id_marca:null);
				$cita->marcaAuto   = ($id_marca==0?$request->get('marca'):null);
				$cita->modelo      = $request->get('modelo');
				$cita->anio        = $request->get('anio');
				$cita->placa     = $request->get('placa');
				$cita->kilometraje = $request->get('kilometraje');
				$cita->tipoServicio = $request->get('tipoServicio');
				$cita->fecha = $request->get('fecha');
				$cita->hora = $request->get('hora');
				$cita->duracion = $request->get('duracion');
				// $cita->numOrden = $request->get('orden');
				$cita->indicaciones = $request->get('indicaciones');
				// $cita->idUsuario = Auth::user()->usuarioId;
				
				if ($id == 0) {
					$cita->save();
					if (!is_null($serie)) {
						$serie->numero = $cita->numero;
						$serie->update();
					}
					// $this->guardarStock($cita->id);
					$cad = ' Registrado';
				} else {
					$cita->update();
					$cad = ' Actualizado';
				}
				
				$this->enviarCorreo($cita);
				$errors[] = 'Cita'.$cad.' Correctamente';
	

			}catch(\Exception $ex){
			  	$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}
	
	}

	public function obtenerCitas ($id, Request $request) {
		// DB::Raw("CONCAT('Marca: ',ma.nombre,', Modelo: ', cita.modelo, '- Placa: ', cita.placa) as descripcion")
		$citas = Cita::leftJoin('marcaauto as ma','ma.id','=','cita.idMarcaAuto')
				->where('cita.idCliente','=', $id)
				->where('cita.situacion','=','A')
				->select(DB::Raw("CONCAT(cita.placa,' - Tipo: ', 
				(CASE cita.tipoServicio WHEN 'MP' THEN 'Mantenimiento Preventivo' 
				WHEN 'MC' THEN 'Mantenimiento Correctivo'
				WHEN 'L'  THEN 'Lavado'
				WHEN 'PB' THEN 'Programa de Bienvenida'
				WHEN 'IA' THEN 'Instalación de accs'
				WHEN 'PD' THEN 'PDI'
				WHEN 'G'  THEN 'Garantía'
				WHEN 'C' THEN 'Campaña'
				WHEN 'PP' THEN 'Planchado y Pintura'
				WHEN 'IS' THEN 'Inspección Seminuevo'
				WHEN 'TR' THEN 'Trabajo repetido'
				ELSE 'Siniestro' END)) as descripcion"), 
				DB::Raw("DATE_FORMAT(cita.fecha,'%d/%m/%Y') as fecha"), 
				DB::Raw("CONCAT('CT',LPAD(cita.serie,2,'0'),'-',LPAD(cita.numero,8,'0')) as numero"),'cita.placa','cita.id')
				->get();
		return ['citas' => $citas];
	}

    public function getCorrelativo (Request $request) {
		$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
			->where('tipoDocumento','=','CT')
			->select(DB::Raw("CONCAT('CT', LPAD(serie,3,'0') ,'-', LPAD(numero+1,8,'0')) as numero"))
			->first();
		
		return ['numero' => $serie->numero];
	}

    public function validar (Request $request) {
		$reglas = [
			'tipodocumento' => 'required',
			'documento' => 'required|numeric|'.($request->get('tipodocumento') == 'B'?'digits:8':'digits:11'),
			'apellidos' => (strlen($request->get('documento')) == 8?'required':'nullable'),
			'nombres' => (strlen($request->get('documento')) == 8?'required':'nullable'),
			'razonSocial' => (strlen($request->get('documento')) == 11?'required':'nullable'),
			'telefono' => 'required|numeric|digits_between:6,9',
			'correo' => 'required|string',
			'select_marca'=> 'required',
			'marca'  => ($request->get('select_marca')=='otro'?'required':'nullable'),
			'modelo' => 'required',
			'anio' => 'required|numeric',
			'placa' => 'required|string|max:10',
			'kilometraje' => 'required|numeric',
			'tipoServicio' => 'required|string',
			'fecha' => 'required',
			'hora'  => 'required',
			'duracion' => 'required|numeric',
			// 'orden' => ($request->get('id') == 0?'required':'nullable'),
			'situacion' => ($request->get('id') != 0?'required':'nullable'),
    		'indicaciones' => 'required|string',
			'con_cita'	=> 'required',
			'con_soat' => 'required',
			'con_seguro' => 'required',
			'vin' => 'required|max:20'
        ];

        $mensajes = [
            'tipodocumento.required'=> 'Indique Tipo de Persona',
			'documento.required'=> 'Indique Documento',
			'documento.numeric'=> 'Documento debe contener solo números',
			'apellidos.required'=> 'Indique Apellidos',
			'nombres.required'=> 'Indique Nombres',
			'razonSocial.required'=> 'Indique Razón Social',
			'telefono.required'=> 'Indique Teléfono',
			'telefono.numeric'=> 'Teléfono debe contener solo números',
			'correo.required'=> 'Indique Correo Electrónico',
			'select_marca.required'=> 'Seleccione Marca',
			'marca.required'  => 'Indique Marca',
			'modelo.required' => 'Indique Modelo',
			'anio.required'  => 'Indique Año',
			'anio.numeric'  => 'Año debe ser numérico',
			'placa.required' => 'Indique Placa',
			'placa.max' => 'Placa no debe exceder a 10 caracteres',
			'kilometraje.required' => 'Indique Kilometraje',
			'kilometraje.numeric'  => 'Kilometraje debe ser numérico',
			'tipoServicio.required' => 'Indique Tipo de Servicio',
			'fecha.required' => 'Indique Fecha',
			'hora.required' => 'Indique Hora',
			'duracion.required' => 'Indique Duración de Cita',
			'duracion.numeric' => 'Duración de Cita debe ser numérico',
			// 'orden.required'  => 'Indique Número de Orden',
			'situacion.required'  => 'Indique Situación de Cita',
			'con_cita.required'   => 'Indique Si llegó con Cita',
			'indicaciones.required'  => 'Indique Indicaciones para su Reparación',
			'con_soat.required' => 'Indique si cuenta con SOAT',
			'con_seguro.required' => 'Indique si cuenta con Seguro Vehicular',
			'vin.required' => 'Indique VIN',
			'vin.max' => 'VIN  debe tener como máximo 20 carácteres'
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}
	
	public function enviarCorreo () {
		// Request $request
		$cita = Cita::find(32);
		// dd($cita);
		$fecha = date('d/m/Y',strtotime($cita->fecha));
		$hora = date('h:i a',strtotime($cita->hora));
		
		$persona = Persona::find($cita->idCliente);
		if (!is_null($persona)) {
			$documento 	   = $persona->documento;
			$nombrePersona = (!is_null($persona->razonSocial)?$persona->razonSocial:$persona->nombres.' '.$persona->apellidos); 
			$telefono	   = $persona->telefono;
			$correoElectronico	   = $persona->correoElectronico;
		}

        try {
    		Mail::to('ericklizana12@gmail.com')->send(new NotificacionCita($documento,$nombrePersona, $telefono, $correoElectronico, $fecha, $hora));
         
        } catch (\Exception $ex) {
            dd($ex);
        }
		dd('Ok');
	}
	
	public function exito (Request $request) {

		$msje = 'Exito';
		return view('/exito', ['msje' => $msje]);
	}


}
