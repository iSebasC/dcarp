<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use App\Models\Local;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Personal;
use App\Models\User;

use App\Models\TipoUsuario;
use App\Models\RelacionPersonal;
use App\Models\Servicio;
use App\Models\CategoriaServicio;
use App\Models\ServicioUsuario;

use App\Libraries\Funciones;

use DB;
use Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

class PersonalController extends Controller
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
    	$dni 	 = $request->get('dni');
    	$personal = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');
		
    	$genero 	 = $request->get('genero');
    	$categoriaId	 = $request->get('categoriaId');
  
        $filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$personal = Personal::leftjoin('departamento as d','d.codigo','=','trabajador.idDepartamento')
    					->leftjoin('provincia as p','p.codigo','=','trabajador.idProvincia')
    					->leftjoin('distrito as dist','dist.codigo','=','trabajador.idDistrito')
    					->leftjoin('categoriapersonal as cat','cat.id','=','trabajador.idCategoriaPersonal')
						->where('trabajador.dni','LIKE', '%'.$dni.'%')
						->where(DB::raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres)"),'LIKE', $personal.'%')
						->where('trabajador.correoE','LIKE', '%'.$correo.'%')
						->where('trabajador.telefono','LIKE', '%'.$telefono.'%')
						->where('trabajador.direccion','LIKE', '%'.$direccion.'%');
    

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
        
    	return ['personal' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Persona':' Personas'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	}
	
	public function excel (Request $request) {
		$dni 	 = $request->get('dni');
    	$personal = $request->get('personal');
		$correo = $request->get('correo');
		$telefono = $request->get('telefono');
		$direccion = $request->get('direccion');
		$genero  = $request->get('genero');
		$categoriaId = $request->get('categoriaId');

    	$personal = Personal::leftjoin('departamento as d','d.codigo','=','trabajador.idDepartamento')
    					->leftjoin('provincia as p','p.codigo','=','trabajador.idProvincia')
    					->leftjoin('distrito as dist','dist.codigo','=','trabajador.idDistrito')
    					->leftjoin('categoriaPersonal as cat','cat.id','=','trabajador.idCategoriaPersonal')
						->where('trabajador.dni','LIKE', '%'.$dni.'%')
						->where(DB::raw("CONCAT(trabajador.apellidos,' ', trabajador.nombres)"),'LIKE', $personal.'%')
						->where('trabajador.correoE','LIKE', '%'.$correo.'%')
						->where('trabajador.telefono','LIKE', '%'.$telefono.'%')
						->where('trabajador.direccion','LIKE', '%'.$direccion.'%');
    
                     
    	/*if ($filtro != '' && $filtro != 'todo') {
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
    	}*/

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
		$hoja1->setTitle("Personal");
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

	public function getLocales(Request $request) {
		$tipo = $request->get('tipo');

		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
 				   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
 				   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
 				   ->where('local.tipo','=',$tipo)
 				   ->select('local.codRegistro', DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') ') as direccion"),'dep.nombre as departamento','local.id')
					->orderBy('direccion','ASC')
					->first();
		
		return ['locales' => (object)$locales, 'estado' => true];
	}	

	public function getPersonal($id, Request $request) {
		$per = Personal::leftJoin('login','login.usuarioId','=','trabajador.id')
			   ->where('trabajador.id','=',$id)
			   ->select('trabajador.*','login.usuario','login.categoriaPersonalId')
			   ->first();
    
    	if (!is_null($per)) {
			$certificaciones = (!is_null($per->certificaciones)?explode(';', $per->certificaciones):[]);
			$arrCertificaciones = [];
			foreach ($certificaciones as $cert) {
				$arrCertificaciones[] = (object)['id'=> $cert, 'nombre'=> $cert];
			}
			$respuesta = ['estado' => true, 'personal' => $per, 'certificaciones' => $arrCertificaciones];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
	}

	public function getCategorias (Request $request) {
		return ['categorias' => TipoUsuario::all()];
	}

	public function getRelacionEmpleado($id, $tipo, Request $request) {
		$locales = Local::leftjoin('departamento as dep','dep.codigo','=','local.idDepartamento')
 				   ->leftjoin('provincia as p','p.codigo','=','local.idProvincia')
 				   ->leftjoin('distrito as d','d.codigo','=','local.idDistrito')
 				   ->leftjoin('relacionempleado as rl','rl.idLocal','=','local.id')
				   ->where('rl.tipo','=',$tipo)
				   ->where('local.tipo','=',$tipo)
 				   ->where('rl.idTrabajador','=',$id)
				   ->where('rl.situacion','=','S')
				   ->select(DB::raw("CONCAT(local.direccion,' - ', d.nombre, ' (', p.nombre ,') -', dep.nombre) as label"),'local.id as value')
				   ->get();
		// $arr = [];
		foreach ($locales as $lc) {
			$arr[] = $lc->idLocal;
		}

		return ['locales' => (object)$locales];
	}

	public function guardarPersonal(Request $request) {
		// dd($request);
		$errors = $this->validar($request);

		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			try{
				$errors = [];
				$band = true;
				$existe = DB::table('trabajador')->where('dni','=',$request->get('dni'));
				$valid_user = DB::table('login')->where('usuario','=',$request->get('usuario'));
				
				if ($id != 0) {
					$existe = $existe->where('id','!=',$id);
					$valid_user = $valid_user->where('usuarioId','!=', $id);
				}
				$existe = $existe->first();

				$valid_user = $valid_user->first();
				
				if (!is_null($existe)) {
					$band = false;
				}
			    if (!is_null($valid_user)) {
					$band = false;
				}
				
				if ($band) {
					if ($id == 0) {
						$c = new Personal;
					} else {
						$c = Personal::find($id);
					}
					
					$c->dni = $request->get('dni');
					$c->apellidos = $request->get('apellidos');
					$c->nombres = $request->get('nombres');
					$c->genero  = $request->get('genero');
					// $c->tipoLocal = $request->get('tipo');
					$c->correoE   = $request->get('correo');
					$c->fechaNacimiento = $request->get('fechaNacimiento');
					$c->idDepartamento = $request->get('select_departamento');
					$c->idProvincia    = $request->get('select_provincia');
					$c->idDistrito     = $request->get('select_distrito');
					$c->direccion	   	   = $request->get('direccion');
					$c->idCategoriaPersonal = $request->get('select_categoria');
					$c->telefono	   = $request->get('telefono');
					$arreglo = json_decode($request->get('certificaciones'),true);
					$certificaciones = '';
					foreach ($arreglo as $valor) {
						$certificaciones.=$valor['nombre'].';';
					}
					$c->certificaciones = ($certificaciones!=''?trim($certificaciones,';'):null);
					$cad = '';
					if ($id == 0) {
						$c->save();
						$idRegistro = $c->id;
						$cad = ' Registrado';
						/* 
							$lista = Servicio::select('id')->get();
							foreach ($lista as $value) {
								$r = new ServicioUsuario;
								$r->idServicio  = $value->id;
								$r->idPersonal = $idRegistro;
								$r->situacion = 'N';
								$r->save();
							} 
						*/
						$this->crearHabilidades($idRegistro);

						if (is_null($valid_user)) {
							$u = new User;
							$u->usuario = $request->get('usuario');
							$u->correoElectronico = $c->correoE; 
							$u->password = bcrypt($request->get('password'));
							$u->usuarioId = $idRegistro;
							$u->categoriaPersonalId = $c->idCategoriaPersonal;
							$u->save();
						}
					} else {
						$c->update();
						$cad = ' Actualizado';
						#ACTUALIZAR RELACION CON EMPLEADO
						// RelacionPersonal::where('idTrabajador','=',$id)->update(['situacion' => 'N']);
						
						// $locales = explode(',',$request->get('listLocales'));
						#ACTUALIZAR RELACION CON EMPLEADO => S
						// RelacionPersonal::where('idTrabajador','=',$id)
						// ->whereIn('idLocal',$locales)
						// ->update(['situacion' => 'S']);
						

						$u = User::where('usuarioId','=',$id)->first();
						if (!is_null($u)) {
    						$u->usuario = $request->get('usuario');
    						$u->correoElectronico = $c->correoE;
    						$u->password = bcrypt($request->get('password'));
    						$u->usuarioId = $id;
    						$u->categoriaPersonalId = $c->idCategoriaPersonal;
    						$u->update();
						} else {
						    $u = new User;
						    $u->usuario = $request->get('usuario');
    						$u->correoElectronico = $c->correoE;
    						$u->password = bcrypt($request->get('password'));
    						$u->usuarioId = $id;
    						$u->categoriaPersonalId = $c->idCategoriaPersonal;
    						$u->save();
						}
					}
					$errors[] = 'Personal'.$cad.' Correctamente';
				} else {
					if (!is_null($existe)) {
						$errors[] = "DNI: ".$request->get('dni').' ya Antes Registrado';
					}

					if (!is_null($valid_user)) {
						$errors[] = 'Nombre de Usuario ya Antes Registrado';
					}
				}

			}catch(\Exception $ex){
				$msje = $ex->getMessage();
				$band = $msje;
				// dd($msje);
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];
		}
	}

	public function validar (Request $request) {
		$reglas = [
            'select_departamento'=> 'required|numeric',
            'select_provincia'=> 'required|numeric',
            'select_distrito'=> 'required|numeric',
			'select_categoria'=> 'required|numeric',
			'direccion'=> 'required|max:255|string',
			// 'tipo'=> 'required|string',
			'telefono' => 'required|numeric|digits_between:6,9',
			'correo' => 'required|max:255',
			'dni' 	 => 'required|min:8|max:8',
			'apellidos' => 'required|max:255',
			'nombres'	=> 'required|max:255',
			// 'listLocales' => 'required',
			'fechaNacimiento' => 'required|date',
			'genero'	=> 'required|string',
			'password'  => 'required|max:255',
			'confirmPassword'  => 'required|max:255',
			'usuario'	=> 'required|max:191',
	    ];

        $mensajes = [
            'select_departamento.required'=> 'Indique Departamento',
			'select_provincia.required'=> 'Indique Provincia',
			'select_distrito.required'=> 'Indique Distrito',
			'direccion.required'	=> 'Indique Dirección',
			'direccion.max' => 'Dirección debe tener como máximo 255 caracteres',
			// 'tipo.required'	=> 'Indique Tipo de Local',
			'telefono.required'	=> 'Indique Teléfono',
			'telefono.digits_between'=> 'Teléfono Debe Tener 06 o 09 Caracteres Numéricos',
			'correo.required'=> 'Indique Correo Electrónico',
			'correo.max'=> 'Correo Electrónico debe tener como máximo 255 caracteres',
			'dni.required' => 'Indique DNI',
			'dni.min' => 'DNI debe tener como mínimo 08 Caracteres',
			'dni.max' => 'DNI debe tener como máximo 08 Caracteres',
			'apellidos.required'	=> 'Indique Apellidos',
			'apellidos.max' => 'Apellidos debe tener como máximo 255 Caracteres',
			'nombres.required'	=> 'Indique Nombres',
			'nombres.max' => 'Nombres debe tener como máximo 255 Caracteres',
			// 'listLocales.required'	=> 'Indique Local(es) de Atención',
			'fechaNacimiento.required'	=> 'Indique Fecha de Nacimiento',
			'fechaNacimiento.date'	=> 'Fecha de Nacimiento Invalida',
			'genero.required' => 'Indique Género',
			'password.required' => 'Indique Contraseña',
			'password.max' => 'Contraseña debe tener como máximo 255 caracteres',
			'confirmPassword.required' => 'Indique Confirmación de Contraseña',
			'confirmPassword.max' => 'Confirmación de Contraseña debe tener como máximo 255 caracteres',
			'usuario.required' => 'Indique Usuario',
			'usuario.max' => 'Usuario debe tener como máximo 191 caracteres',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function crearHabilidades ($id) {
		$servicios = Servicio::select('id')->get();
		foreach ($servicios as $sr) {
			$a = new ServicioUsuario;
			$a->idPersonal = $id;
			$a->idServicio = $sr->id;
			$a->estado = 'N';
			$a->save();
		}
	}

	public function eliminarPersonal(Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$personal = Personal::find($id);
			$usuario = User::where('usuarioId','=',$personal->id)->first();
			$usuario->delete();
			$personal->delete();
	
			$errors[] = 'Personal Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

	public function guardarHabilidades (Request $request) {
		DB::beginTransaction();
		$band = true;
		$id = $request->get('id');
		$arrOpc = explode(',',$request->get('opciones'));
		$opciones = ServicioUsuario::where('idPersonal','=',$id)->get();
		$errors = [];
		try{
			foreach ($opciones as $op) {
				$m = ServicioUsuario::find($op->id);
				if (in_array($op->id,$arrOpc)) {
					$m->estado = 'S';
				} else {
					$m->estado = 'N';
				}
				$m->update();
			}
			$errors[] = 'Habilidades Actualizadas Correctamente';
		}catch(\Exception $ex){
			$msje = $ex->getMessage();
			$band = false;
			$errors[] = 'Ocurrió un Error, Vuelva a Intentarlo';
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];
	}
	
	public function getHabilidades ($id, Request $request) {

	  $categorias = CategoriaServicio::all();
	  $servicios = Servicio::leftJoin('serviciousuario as sa','sa.idServicio','=','servicio.id')
					->where('sa.idPersonal','=',$id)
					->select('sa.id',DB::Raw("CONCAT(servicio.nombre,' - ', servicio.tipoVehiculo) as servicio"),
					'servicio.idCategoriaServicio as idCategoria','sa.estado')
					->distinct()
					->get();

	  $arr = [];
	  $marcadas = [];
	  foreach ($categorias as $ct) {
		$arr2 = [];
		foreach ($servicios as $sr) {
			if ($ct->id == $sr->idCategoria) {
				$arr2[] = $sr;
				if ($sr->estado == 'S')
					$marcadas[] = $sr->id;
			}
		}

		if (count($arr2)>0) {
			$arr[] = array('categoria' => $ct, 'servicios' => (object) $arr2, 'cantServ' => count($arr2));
		} 
   	  }
		return ['opciones' => (object)$arr, 'marcadas' => implode(',', $marcadas)];
    }

}
