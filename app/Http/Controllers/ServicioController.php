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
use App\Models\Auto;
use App\Models\StockAuto;
use App\Models\CategoriaServicio;
use App\Models\Servicio;
use App\Models\PrecioHora;
use App\Models\Personal;	
use App\Models\ServicioUsuario;
use App\Models\MacroServicio;

use App\Libraries\Funciones;
use DB;

use Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;



class ServicioController extends Controller
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

	public function getMarcas($tipo, Request $request) {
		$marcas = MarcaAuto::where('tipo','=',$tipo)->get();
		
		return ['marcas' => $marcas];
	}

    public function getAll (Request $request) {
		$servicio 	 = $request->get('servicio');
    	$tiempo 	 = $request->get('tiempo');
    	$precio 	 = $request->get('precio');
    	$tipoId 	 = $request->get('tipoId');
		
    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
    	$servicios = Servicio::leftjoin('categoriaservicio as cs','cs.id','=','servicio.idCategoriaServicio')
					 ->leftjoin('macroservicio as mc','mc.id','=','servicio.idMacroServicio')
					 ->where('servicio.nombre','LIKE', '%'.$servicio.'%')
					 ->where(DB::Raw("CONCAT(servicio.tiempoEjecucion,' ', (CASE WHEN servicio.unidad = 'min' THEN 'Minuto(s)' ELSE (CASE WHEN servicio.tiempoEjecucion = 'hr' THEN 'Hora(s)' ELSE (CASE WHEN servicio.tiempoEjecucion = 'sem' THEN 'Semana(s)' ELSE 'Mes(es)' END) END) END))"), 'LIKE','%'.$tiempo.'%');

		if ($precio != '') {
			$servicios->where('servicio.precio','=', (float)$precio);
		}

    	if ($tipoId != '' && $tipoId != 'todo') {
    		$servicios = $servicios->where('servicio.idCategoriaServicio','=',$tipoId);
    	}
    	
    	$servicios =  $servicios->select('servicio.id','mc.descripcion as macroservicio','servicio.nombre','cs.nombre as tipo',DB::Raw("CONCAT(servicio.tiempoEjecucion,' ', (CASE WHEN servicio.unidad = 'min' THEN 'Minuto(s)' ELSE (CASE WHEN servicio.unidad = 'hr' THEN 'Hora(s)' ELSE (CASE WHEN servicio.unidad = 'sem' THEN 'Semana(s)' ELSE 'Mes(es)' END) END) END)) as tiempo"), DB::Raw("DATE_FORMAT(servicio.created_at,'%d/%m/%Y') as fechaRegistro"),DB::Raw("FORMAT(servicio.precio,2) as precio"),'servicio.tipoVehiculo')
		   ->orderBy('servicio.id','ASC');

        $lista = $servicios->get();
       
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
		
		$lista = $servicios->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		return ['servicios' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Servicio':' Servicios'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
	} 

	public function excel (Request $request) {
		$servicio 	 = $request->get('servicio');
    	$tiempo 	 = $request->get('tiempo');
    	$precio 	 = $request->get('precio');
		$tipoId 	 = $request->get('tipoId');
		
		$servicios = DB::table('servicio')
					 ->leftjoin('categoriaservicio as cs','cs.id','=','servicio.idCategoriaServicio')
					 ->where('servicio.nombre','LIKE', '%'.$servicio.'%')
					 ->where(DB::Raw("CONCAT(servicio.tiempoEjecucion,' ', (CASE WHEN servicio.unidad = 'min' THEN 'Minuto(s)' ELSE (CASE WHEN servicio.tiempoEjecucion = 'hr' THEN 'Hora(s)' ELSE (CASE WHEN servicio.tiempoEjecucion = 'sem' THEN 'Semana(s)' ELSE 'Mes(es)' END) END) END))"), 'LIKE','%'.$tiempo.'%');

		if ($precio != '') {
			$servicios->where('servicio.precio','=', (float)$precio);
		}

    	if ($tipoId != '' && $tipoId != 'todo') {
    		$servicios = $servicios->where('servicio.idCategoriaServicio','=',$tipoId);
    	}
    	
    	$servicios =  $servicios->select('servicio.id','servicio.nombre','cs.nombre as tipo',DB::Raw("CONCAT(servicio.tiempoEjecucion,' ', (CASE WHEN servicio.unidad = 'min' THEN 'Minuto(s)' ELSE (CASE WHEN servicio.unidad = 'hr' THEN 'Hora(s)' ELSE (CASE WHEN servicio.unidad = 'sem' THEN 'Semana(s)' ELSE 'Mes(es)' END) END) END)) as tiempo"), DB::Raw("(CASE WHEN servicio.deleted_at IS NOT NULL THEN DATE_FORMAT(servicio.deleted_at,'%d/%m/%Y H:i:s') ELSE '-' END) as deleted_at"),DB::Raw("FORMAT(servicio.precio,2) as precio"),'servicio.tipoVehiculo')
		   ->orderBy('servicio.id','ASC');

        $lista = $servicios->get();
       

		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("Servicios");
		$hoja1->setCellValue('A1','LISTADO DE SERVICIOS');
		$hoja1->mergeCells('A1:F1');
		$hoja1->getStyle('A1:F1')->applyFromArray($this->estilo_header);
		
		$hoja1->setCellValue('A2','TIPO DE SERVICIO');
		$hoja1->setCellValue('B2','NOMBRE');
		$hoja1->setCellValue('C2','TIPO DE VEHÍCULO');
		$hoja1->setCellValue('D2','TIEMPO DE EJECUCIÓN');
		$hoja1->setCellValue('E2','PRECIO (S/)');
		$hoja1->setCellValue('F2','ELIMINADO EL');
	
		$hoja1->getStyle('A2:F2')->applyFromArray($this->estilo_header);
		
		$j = 3;
		foreach ($lista as $value) {
			$hoja1->setCellValue('A'.$j,$value->tipo);
			$hoja1->setCellValue('B'.$j,$value->nombre);
			$hoja1->setCellValue('C'.$j,$value->tipoVehiculo);
			$hoja1->setCellValue('D'.$j,$value->tiempo);
			$hoja1->setCellValue('E'.$j,$value->precio);
			$hoja1->setCellValue('F'.$j,$value->deleted_at);
		
			$hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
			$j++;
		}

		foreach(range('A','F') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="servicios-'.date('Y-m-d H:i').'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
		
	}

    
    public function getTipoServicio (Request $request) {
    	$tipos = CategoriaServicio::all();

    	return ['tipos' => $tipos];
	}
	
	public function getServicio ($id, Request $request) {
    	$servicio = Servicio::find($id);
    
    	if (!is_null($servicio)) {
    		$respuesta = ['estado' => true, 'servicio' => $servicio];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
    }

    public function guardarServicio(Request $request) {
		$errors = $this->validar($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				if ($request->get('select_tipo') === 'otro') {
					$nombre = $request->get('tipo');
					$existe = CategoriaServicio::where('nombre','=', $nombre)->first();
					$nombre_g = strtoupper($nombre[0]).strtolower(substr($nombre, 1));
					if(is_null($existe)){
						$cat = new CategoriaServicio;
						$cat->nombre = $nombre_g;
						$cat->save();
                    	$id_tipo = $cat->id;
					}else{
						$id_tipo = $existe->id;
					}
				}else{
					$id_tipo = $request->get('select_tipo');
				}

				if ($id == 0) {
					$servicio = new Servicio;
				} else {
					$servicio = Servicio::find($id);
				}
				$servicio->idMacroServicio = $request->get('select_macroservicio');
				$servicio->nombre    = $request->get('nombre');
				$servicio->idCategoriaServicio = $id_tipo;
				$servicio->tiempoEjecucion     = $request->get('tiempo');
				$servicio->unidad        = $request->get('select_tiempo');
				$servicio->precio = $request->get('precio');
				$servicio->tipoVehiculo = $request->get('select_tipovehiculo');
				if ($id == 0) {
					$servicio->save();
					$this->crearHabilidades($servicio->id);
					$cad = ' Registrado';
				} else {
					$servicio->update();
					$cad = ' Actualizado';
				}
				
				$errors[] = 'Servicio'.$cad.' Correctamente';
	

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
			'select_macroservicio'=> 'required',
            'nombre'=> 'required|max:255',
			'select_tipo'=> 'required',
			'tipo'  => ($request->get('select_tipo')=='otro'?'required':'nullable'),
			'tiempo' => 'required|numeric',
			'select_tiempo' => 'required',
			'select_tipovehiculo' => 'required',
    		'precio' => 'numeric|required|min:1',
        ];

        $mensajes = [
            'select_macroservicio.required'=> 'Seleccione Macro Servicio',
			'nombre.required'=> 'Indique Nombre de Servicio',
        	'nombre.max'  => 'Nombre de Servicio debe tener como máximo 255 caracteres',
		    'select_tipo.required'=> 'Seleccione Tipo de Servicio',
			'tipo.required'  => 'Indique Tipo de Servicio',
			'tiempo.required' => 'Indique Tiempo de Ejecución',
        	'tiempo.numeric' => 'Tiempo de Ejecución debe ser numérico',
			'select_tiempo.required'  => 'Indique Unidad',
			'select_tipovehiculo.required' => 'Indique Tipo de Vehículo',
    		'precio.required'  => 'Indique Precio',
            'precio.numeric'  => 'Precio debe ser numérico',
            'precio.min'  => 'Precio debe ser mayor a 1',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarPrecio (Request $request) {
		$reglas = [
        	'precio' => 'numeric|required|min:1',
        ];

        $mensajes = [
        	'precio.required'  => 'Indique Precio',
            'precio.numeric'  => 'Precio debe ser numérico',
            'precio.min'  => 'Precio debe ser mayor a 1',
		];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = [];

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}


	public function getPrecioHoraHombre (Request $request) {
		$precio = PrecioHora::all();
		if (count($precio) > 0) {
			return ['estado' => true, 'precio' => PrecioHora::find(1)->precio];
		} else {
			return ['estado' => false]; 
		}
	} 

	public function guardarPrecio (Request $request) {
		$errors = $this->validarPrecio($request);
        if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			DB::beginTransaction();
			$band = true;
			$errors = [];
	
			try{
				$precio = PrecioHora::all();
				if (count($precio) > 0) {
					$p = Preciohora::find(1);
					$p->precio = $request->get('precio');
					$p->update();
					$cad = ' Actualizado';
				} else {
					$p = new PrecioHora;
					$p->precio = $request->get('precio');
					$p->save();
					$cad = ' Registrado';
				}
				
				$this->actualizarServicios($p->precio);

				$errors[] = 'Costo Hora/Hombre '.$cad.' Correctamente';
	

			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band];	
		}	
	}

	public function actualizarServicios ($precio) {
		$p = $precio/60;
		$servicios = Servicio::select('id')->get();
	
		foreach ($servicios as $serv) {
			$s = Servicio::find($serv->id);
			if ($s->unidad == 'hr') {
				$const = 60;
			} elseif ($s->unidad == 'min') {
				$const = 1;
			} elseif ($s->unidad == 'sem') {
				$const = 60*7;
			} else {
				$const = 30*60*7;
			}

			$t = $const*$s->tiempoEjecucion*$p;
			$s->precio = $t;
			$s->update();
		}
	}

	public function crearHabilidades($idservicio) {
		$personal = Personal::select('id')->get();
		foreach ($personal as $p) {
			$a = new ServicioUsuario;
			$a->idPersonal = $p->id;
			$a->idServicio = $idservicio;
			$a->estado = 'N';
			$a->save();
		}
	}

	public function eliminarServicio(Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$tipo = Servicio::find($id);
			$tipo->delete();
			$errors[] = 'Servicio Eliminado Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	}

	public function getMacroServicios(Request $request) {
		$macroservicios = MacroServicio::all();
	
		return ['lista' => $macroservicios];
	}
}
