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
use App\Models\DetalleHomologacion;

use App\Models\OrdenTrabajo;
use App\Models\PagoDetalle;
use App\Models\SerieDocumento;

use App\Libraries\Funciones;

use DB;
use Validator;
use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;


class ReporteMensualController extends Controller
{
	public $estilo_content = array( 
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		)
	);

	public function reporteMesExcel (Request $request) {
        ini_set('memory_limit', '-1');
        $tipo 	 	= $request->get('tipo');
		$fechai 	= $request->get('fechai');
		$fechaf     = $request->get('fechaf');
        $tipoServ   = $request->get('tipoServ');
        $anio       = $request->get('anio');

		$excel = new PHPExcel(); 
        $fileName = $this->getReporteExcel($fechai, $fechaf, $tipo, $tipoServ, $anio, $excel);
		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
	}

    public function reporteClienteExcel(Request $request) {
        ini_set('memory_limit', '-1');
        $tipo 	 	= $request->get('tipo');
		$fechai 	= $request->get('fechai');
		$fechaf     = $request->get('fechaf');
        $cliente    = (!is_null($request->get('cli'))?$request->get('cli'):'');

		$excel = new PHPExcel(); 
        $fileName = $this->getReporteClienteExcel($fechai, $fechaf, $tipo, $cliente, $excel);
		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
    }

    public function getReporteClienteExcel($fechaI, $fechaF, $tipo, $cliente, $excel) {
        
        $data = $this->getDataCliente($fechaI, $fechaF, $tipo, $cliente);
        $excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$fileName = '';
        if ($tipo == 1) {
            $fileName = 'orden_detallada'; 
            $hoja1->setTitle("orden_detallada");

            $j = 1;
            $elem = null;
            $data_Final = [];
            $detalles = [];
            foreach ($data as $value) {
                if (is_null($elem)) { $elem = $value; }
                if ($elem->keyI == $value->keyI) {
                    $detalles[] = $value;
                } else {
                    // dd($detalles);
                    $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                    $elem = null;
                    $detalles = [];
                }
            }
            if (count($detalles)>0) {
                $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                $elem = null;
                $detalles = [];
            }
            

            foreach ($data_Final as $value) {
                $elem = $value['cabecera'];
                $hoja1->setCellValue('A'.$j,'doc_cliente');
                $hoja1->setCellValue('B'.$j,'cliente');
                $hoja1->setCellValue('C'.$j,'celular');
                $hoja1->setCellValue('D'.$j,'correo_electronico');
                // $hoja1->setCellValue('E'.$j,'placa');
                // $hoja1->setCellValue('F'.$j,'marca');
                // $hoja1->setCellValue('G'.$j,'modelo');
                // $hoja1->setCellValue('H'.$j,'vin');
                // $hoja1->setCellValue('I'.$j,'kilometraje');
                
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,$elem->documentoCliente);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->celular);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                // $hoja1->setCellValue('J'.$j,$elem->fecha);
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,'fecha_orden');
                $hoja1->setCellValue('B'.$j,'orden_trabajo');
                $hoja1->setCellValue('C'.$j,'cotizacion');
                $hoja1->setCellValue('D'.$j,'cantidad');
                $hoja1->setCellValue('E'.$j,'detalle');
                $hoja1->setCellValue('F'.$j,'inicia');
                $hoja1->setCellValue('G'.$j,'finaliza');
           
                $hoja1->setCellValue('H'.$j,'placa');
                $hoja1->setCellValue('I'.$j,'marca');
                $hoja1->setCellValue('J'.$j,'modelo');
                $hoja1->setCellValue('K'.$j,'vin');
                $hoja1->setCellValue('L'.$j,'kilometraje');
                $hoja1->setCellValue('M'.$j,'venta');
           
                $hoja1->setCellValue('N'.$j,'personal_registra');
                $hoja1->setCellValue('O'.$j,'asignado_a');
                $hoja1->setCellValue('P'.$j,'facturado');
                $hoja1->setCellValue('Q'.$j,'fecha_reg');
                $hoja1->setCellValue('R'.$j,'situacion');
                $hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $detalles = $value['detalles'];
                // dd($detalles);
                foreach ($detalles as $value2) {
                    $hoja1->setCellValue('A'.$j,$value2->fecha);
                    $hoja1->setCellValue('B'.$j,$value2->orden);
                    $hoja1->setCellValue('C'.$j,$value2->cotizacion);
                    $hoja1->setCellValue('D'.$j,number_format($value2->cantidad,2,'.',''));
                    $hoja1->setCellValue('E'.$j,$value2->detalle);
                    $hoja1->setCellValue('F'.$j,$value2->inicia);
                    $hoja1->setCellValue('G'.$j,$value2->finaliza);
           
                    $hoja1->setCellValue('H'.$j,$value2->placa);
                    if (!is_null($value2->marcamodelo)) {
                        $hoja1->setCellValue('I'.$j,$this->getExplodeStringReport($value2->marcamodelo, 0,'/'));
                        $hoja1->setCellValue('J'.$j,$this->getExplodeStringReport($value2->marcamodelo, 1,'/'));
                    }

                    $hoja1->setCellValue('K'.$j,$value2->vin);
                    $hoja1->setCellValue('L'.$j,$value2->kilometraje);
                    $hoja1->setCellValue('M'.$j,$value2->venta);
           
                    $hoja1->setCellValue('N'.$j,$value2->personal);
                    $hoja1->setCellValue('O'.$j,$value2->trabajador);
                    $hoja1->setCellValue('P'.$j,($value2->situacionFacturado=='N'?'No':'Si'));
                    $hoja1->setCellValue('Q'.$j, $value2->fechaReg);
                    $hoja1->setCellValue('R'.$j, ($value2->situacion == 'A'?'Anulado':'Vigente'));
                    
                    $hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);
                    $j++;
                }
                $j++;
            }
        } elseif($tipo == 2) {
            $fileName = 'cot_detallada'; 
            $hoja1->setTitle("cot_detallada");
            
            $j = 1;
            $elem = null;
            $data_Final = [];
            $detalles = [];
            foreach ($data as $value) {
                if (is_null($elem)) { $elem = $value; }
                if ($elem->keyI == $value->keyI) {
                    $detalles[] = $value;
                } else {
                    $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                    $elem = null;
                    $detalles = [];
                }
            }
            if (count($detalles)>0) {
                $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                $elem = null;
                $detalles = [];
            }
            
            foreach ($data_Final as $value) {
                $elem = $value['cabecera'];
                $hoja1->setCellValue('A'.$j,'doc_cliente');
                $hoja1->setCellValue('B'.$j,'cliente');
                $hoja1->setCellValue('C'.$j,'celular');
                $hoja1->setCellValue('D'.$j,'correo_electronico');
                // $hoja1->setCellValue('E'.$j,'placa');
                // $hoja1->setCellValue('F'.$j,'marca');
                // $hoja1->setCellValue('G'.$j,'modelo');
                // $hoja1->setCellValue('H'.$j,'vin');
                // $hoja1->setCellValue('I'.$j,'kilometraje');
                    // $hoja1->setCellValue('J'.$j,'fecha_orden');
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,$elem->documentoCliente);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->celular);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                // $hoja1->setCellValue('E'.$j,$elem->placa);
                // $hoja1->setCellValue('F'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,0));
                // $hoja1->setCellValue('G'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,1));
                // $hoja1->setCellValue('H'.$j,$elem->vin);
                // $hoja1->setCellValue('I'.$j,$elem->kilometraje);
                // $hoja1->setCellValue('J'.$j,$elem->fecha);
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,'fecha_cot');
                $hoja1->setCellValue('B'.$j,'cotizacion');
                $hoja1->setCellValue('C'.$j,'cantidad');
                $hoja1->setCellValue('D'.$j,'detalle');
                $hoja1->setCellValue('E'.$j,'personal_registra');
                
                $hoja1->setCellValue('F'.$j,'placa');
                $hoja1->setCellValue('G'.$j,'marca');
                $hoja1->setCellValue('H'.$j,'modelo');
                $hoja1->setCellValue('I'.$j,'vin');
                $hoja1->setCellValue('J'.$j,'kilometraje');
                $hoja1->setCellValue('K'.$j,'venta');
                
                $hoja1->setCellValue('L'.$j,'facturado');
                $hoja1->setCellValue('M'.$j,'fecha_reg');
                $hoja1->setCellValue('N'.$j,'situacion');
                $hoja1->getStyle('A'.$j.':N'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $detalles = $value['detalles'];
                foreach ($detalles as $value2) {
                    $hoja1->setCellValue('A'.$j,$value2->fecha);
                    $hoja1->setCellValue('B'.$j,$value2->cotizacion);
                    $hoja1->setCellValue('C'.$j,number_format($value2->cantidad,2,'.',''));
                    $hoja1->setCellValue('D'.$j,$value2->detalle);
                    $hoja1->setCellValue('E'.$j, $value2->personal);

                    $hoja1->setCellValue('F'.$j,$value2->placa);
                    if (!is_null($value2->marcamodelo)) {
                        $hoja1->setCellValue('G'.$j,$this->getExplodeStringReport($value2->marcamodelo, 0,'/'));
                        $hoja1->setCellValue('H'.$j,$this->getExplodeStringReport($value2->marcamodelo, 1,'/'));
                    }

                    $hoja1->setCellValue('I'.$j,$value2->vin);
                    $hoja1->setCellValue('J'.$j,$value2->kilometraje);
                    $hoja1->setCellValue('K'.$j,$value2->venta);
        

                    $hoja1->setCellValue('L'.$j,($value2->situacionFacturado=='N'?'No':'Si'));
                    $hoja1->setCellValue('M'.$j, $value2->fechaReg);
                    $hoja1->setCellValue('N'.$j, ($value2->situacion == 'A'?'Anulado':'Vigente'));
                    
                    $hoja1->getStyle('A'.$j.':N'.$j)->applyFromArray($this->estilo_content);
                    $j++;
                }
                $j++;
            }
        } elseif($tipo == 3) {
            $fileName = 'vt_detallada_cliente'; 
            $hoja1->setTitle("vt_detallada_cliente");
            $j = 1;
            $elem = null;
            $data_Final = [];
            $detalles = [];
            foreach ($data as $value) {
                if (is_null($elem)) { $elem = $value; }
                if ($elem->keyI == $value->keyI) {
                    $detalles[] = $value;
                } else {
                    $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                    $elem = null;
                    $detalles = [];
                }
            }
            if (count($detalles)>0) {
                $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                $elem = null;
                $detalles = [];
            }
            foreach ($data_Final as $value) {
                $elem = $value['cabecera'];
                $hoja1->setCellValue('A'.$j,'doc_cliente');
                $hoja1->setCellValue('B'.$j,'cliente');
                $hoja1->setCellValue('C'.$j,'celular');
                $hoja1->setCellValue('D'.$j,'correo_electronico');
                // $hoja1->setCellValue('E'.$j,'placa');
                // $hoja1->setCellValue('F'.$j,'marca');
                // $hoja1->setCellValue('G'.$j,'modelo');
                // $hoja1->setCellValue('H'.$j,'vin');
                // $hoja1->setCellValue('I'.$j,'kilometraje');
                // $hoja1->setCellValue('J'.$j,'fecha_orden');
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,$elem->documentoCliente);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->celular);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                // $hoja1->setCellValue('E'.$j,$elem->placa);
                // $hoja1->setCellValue('F'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,0));
                // $hoja1->setCellValue('G'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,1));
                // $hoja1->setCellValue('H'.$j,$elem->vin);
                // $hoja1->setCellValue('I'.$j,$elem->kilometraje);
                // $hoja1->setCellValue('J'.$j,$elem->fecha);
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,'fecha_vt');
                $hoja1->setCellValue('B'.$j,'venta');
                $hoja1->setCellValue('C'.$j,'referencia');
                $hoja1->setCellValue('D'.$j,'cantidad');
                $hoja1->setCellValue('E'.$j,'detalle');
                $hoja1->setCellValue('F'.$j,'personal_registra');

                $hoja1->setCellValue('G'.$j,'placa');
                $hoja1->setCellValue('H'.$j,'marca');
                $hoja1->setCellValue('I'.$j,'modelo');
                $hoja1->setCellValue('J'.$j,'vin');
                $hoja1->setCellValue('K'.$j,'kilometraje');
               
                $hoja1->setCellValue('L'.$j,'fecha_reg');
                $hoja1->setCellValue('M'.$j,'situacion');
                $hoja1->getStyle('A'.$j.':M'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $detalles = $value['detalles'];
                foreach ($detalles as $value2) {
                    $hoja1->setCellValue('A'.$j,$value2->fecha);
                    $hoja1->setCellValue('B'.$j,$value2->venta);
                    $hoja1->setCellValue('C'.$j,$value2->referencia);
                    $hoja1->setCellValue('D'.$j,number_format($value2->cantidad,2,'.',''));
                    $hoja1->setCellValue('E'.$j,$value2->detalle);
                    $hoja1->setCellValue('F'.$j,$value2->personal);
                    
                    if (!is_null($value2->params)) {
                        // (ct.placa,'@@', ct.kilometraje, '@@', ct.marcamodelo,'@@', ct.vin)
                        $hoja1->setCellValue('G'.$j,$this->getExplodeStringReport($value2->params, 0,'@@'));
                        $hoja1->setCellValue('H'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value2->params, 2,'@@'),0,'/'));
                        $hoja1->setCellValue('I'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value2->params, 2,'@@'),1,'/'));
                        $hoja1->setCellValue('J'.$j,$this->getExplodeStringReport($value2->params, 3,'@@'));
                        $hoja1->setCellValue('K'.$j,$this->getExplodeStringReport($value2->params, 1,'@@'));
                    }    
                    
                    $hoja1->setCellValue('L'.$j, $value2->fechaReg);
                    $hoja1->setCellValue('M'.$j, ($value2->situacion == 'A'?'Anulado':'Vigente'));
                    
                    $hoja1->getStyle('A'.$j.':M'.$j)->applyFromArray($this->estilo_content);
    
                    $j++;
                }
                $j++;
            }
            // $hoja1->setCellValue('A1','fecha_vt');
            // $hoja1->setCellValue('B1','doc_cliente');
            // $hoja1->setCellValue('C1','cliente');
            // $hoja1->setCellValue('D1','venta');
            // $hoja1->setCellValue('E1','referencia');
            // $hoja1->setCellValue('F1','cantidad');
            // $hoja1->setCellValue('G1','detalle');
            // $hoja1->setCellValue('H1','asesor');
            // $hoja1->setCellValue('I1','fecha_reg');
            // $hoja1->setCellValue('J1','situacion');
            // $hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_content);
            
            // $j = 2;
            // foreach ($data as $value) {
            //     $hoja1->setCellValue('A'.$j,$value->fecha);
            //     $hoja1->setCellValue('B'.$j,$value->documentoCliente);
            //     $hoja1->setCellValue('C'.$j,$value->cliente);
            //     $hoja1->setCellValue('D'.$j,$value->venta);
            //     $hoja1->setCellValue('E'.$j,$value->referencia);
            //     $hoja1->setCellValue('F'.$j,number_format($value->cantidad,2,'.',''));
            //     $hoja1->setCellValue('G'.$j,$value->detalle);
            //     $hoja1->setCellValue('H'.$j,$value->asesorAuto);
            //     $hoja1->setCellValue('I'.$j, $value->fechaReg);
            //     $hoja1->setCellValue('J'.$j, ($value->situacion == 'A'?'Anulado':'Vigente'));
                
            //     $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);

            //     $j++;
            // }
        } elseif($tipo == 4) {
            $fileName = 'vt_detallada_veh_cliente'; 
            $hoja1->setTitle("vt_detallada_veh_cliente");
            $j = 1;
            $elem = null;
            $data_Final = [];
            $detalles = [];
            foreach ($data as $value) {
                if (is_null($elem)) { $elem = $value; }
                if ($elem->keyI == $value->keyI) {
                    $detalles[] = $value;
                } else {
                    $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                    $elem = null;
                    $detalles = [];
                }
            }
            if (count($detalles)>0) {
                $data_Final[] = ['cabecera' => $elem, 'detalles' => $detalles];
                $elem = null;
                $detalles = [];
            }
            foreach ($data_Final as $value) {
                $elem = $value['cabecera'];
                $hoja1->setCellValue('A'.$j,'doc_cliente');
                $hoja1->setCellValue('B'.$j,'cliente');
                $hoja1->setCellValue('C'.$j,'celular');
                $hoja1->setCellValue('D'.$j,'correo_electronico');
                // $hoja1->setCellValue('E'.$j,'placa');
                // $hoja1->setCellValue('F'.$j,'marca');
                // $hoja1->setCellValue('G'.$j,'modelo');
                // $hoja1->setCellValue('H'.$j,'vin');
                // $hoja1->setCellValue('I'.$j,'kilometraje');
                // $hoja1->setCellValue('J'.$j,'fecha_orden');
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,$elem->documentoCliente);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->celular);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                // $hoja1->setCellValue('E'.$j,$elem->placa);
                // $hoja1->setCellValue('F'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,0));
                // $hoja1->setCellValue('G'.$j,$this->getExplodeMarcaModeloString($elem->marcamodelo,1));
                // $hoja1->setCellValue('H'.$j,$elem->vin);
                // $hoja1->setCellValue('I'.$j,$elem->kilometraje);
                // $hoja1->setCellValue('J'.$j,$elem->fecha);
                $hoja1->getStyle('A'.$j.':D'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $hoja1->setCellValue('A'.$j,'fecha_vt');
                $hoja1->setCellValue('B'.$j,'venta');
                // $hoja1->setCellValue('C'.$j,'referencia');
                $hoja1->setCellValue('C'.$j,'cantidad');
                $hoja1->setCellValue('D'.$j,'detalle');
                $hoja1->setCellValue('E'.$j,'personal_registra');
                $hoja1->setCellValue('F'.$j,'asesor_auto');
                $hoja1->setCellValue('G'.$j,'fecha_reg');
                $hoja1->setCellValue('H'.$j,'situacion');
                $hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
                $j++;

                $detalles = $value['detalles'];
                foreach ($detalles as $value2) {
                    $hoja1->setCellValue('A'.$j,$value2->fecha);
                    $hoja1->setCellValue('B'.$j,$value2->venta);
                    // $hoja1->setCellValue('C'.$j,$value2->referencia);
                    $hoja1->setCellValue('C'.$j,number_format($value2->cantidad,2,'.',''));
                    $hoja1->setCellValue('D'.$j,$value2->detalle);
                    $hoja1->setCellValue('E'.$j,$value2->personal);
                    $hoja1->setCellValue('F'.$j, $value2->asesorAuto);
                    $hoja1->setCellValue('G'.$j, $value2->fechaReg);
                    $hoja1->setCellValue('H'.$j, ($value2->situacion == 'A'?'Anulado':'Vigente'));
                    
                    $hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
    
                    $j++;
                }
                $j++;
            }
            // $hoja1->setCellValue('A1','fecha_vt');
            // $hoja1->setCellValue('B1','doc_cliente');
            // $hoja1->setCellValue('C1','cliente');
            // $hoja1->setCellValue('D1','venta');
            // $hoja1->setCellValue('E1','referencia');
            // $hoja1->setCellValue('F1','cantidad');
            // $hoja1->setCellValue('G1','detalle');
            // $hoja1->setCellValue('H1','asesor');
            // $hoja1->setCellValue('I1','fecha_reg');
            // $hoja1->setCellValue('J1','situacion');
            // $hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_content);
            
            // $j = 2;
            // foreach ($data as $value) {
            //     $hoja1->setCellValue('A'.$j,$value->fecha);
            //     $hoja1->setCellValue('B'.$j,$value->documentoCliente);
            //     $hoja1->setCellValue('C'.$j,$value->cliente);
            //     $hoja1->setCellValue('D'.$j,$value->venta);
            //     $hoja1->setCellValue('E'.$j,$value->referencia);
            //     $hoja1->setCellValue('F'.$j,number_format($value->cantidad,2,'.',''));
            //     $hoja1->setCellValue('G'.$j,$value->detalle);
            //     $hoja1->setCellValue('H'.$j,$value->asesorAuto);
            //     $hoja1->setCellValue('I'.$j, $value->fechaReg);
            //     $hoja1->setCellValue('J'.$j, ($value->situacion == 'A'?'Anulado':'Vigente'));
                
            //     $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);

            //     $j++;
            // }
        } elseif($tipo == 5) {
            $fileName = 'vt_cliente_marketing'; 
            $hoja1->setTitle("vt_cliente_marketing");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'doc_cliente');
            $hoja1->setCellValue('B'.$j,'cliente');
            $hoja1->setCellValue('C'.$j,'celular');
            $hoja1->setCellValue('D'.$j,'correo_electronico');
            $hoja1->setCellValue('E'.$j,'marca');
            $hoja1->setCellValue('F'.$j,'modelo');
            $hoja1->setCellValue('G'.$j,'placa');
            $hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
                 

            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->documento);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->telefono);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                $hoja1->setCellValue('E'.$j,$elem->marca);
                $hoja1->setCellValue('F'.$j,$elem->modelo);
                $hoja1->setCellValue('G'.$j,$elem->placa);
                $hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif (in_array($tipo, [11,22,33,44])) {
            $fileName = ($tipo == 11?'orden_gen_detallada':($tipo == 22?'cot_gen_detallada':($tipo == 33?'vt_detallada_gen_cliente':'vt_detallada_gen_vehi_cliente'))); 
            $hoja1->setTitle("$fileName");
            $hoja1->setCellValue('A1','doc_cliente');
            $hoja1->setCellValue('B1','cliente');
            $hoja1->setCellValue('C1','celular');
            $hoja1->setCellValue('D1','correo_electronico');
            $hoja1->setCellValue('E1','direcci贸n');
            
            if (in_array($tipo, [11,22,33])) {
                $hoja1->setCellValue('F1','placa');
                $hoja1->setCellValue('G1','kilometraje');
                $hoja1->setCellValue('H1','marca');
                $hoja1->setCellValue('I1','modelo');
                $hoja1->setCellValue('J1','fecha_registro');
                $hoja1->getStyle('A1:J1')->applyFromArray($this->estilo_content);
            } else {
                $hoja1->setCellValue('F1','asesor');
                $hoja1->setCellValue('G1','descripci贸n');
                $hoja1->setCellValue('H1','fecha');
                //$hoja1->setCellValue('I1','fecha_registro');
                $hoja1->getStyle('A1:H1')->applyFromArray($this->estilo_content);
            }
            
            $j = 2;
            foreach ($data as $value) {
                $hoja1->setCellValue('A'.$j,$value->documento);
                $hoja1->setCellValue('B'.$j,$value->cliente);
                $hoja1->setCellValue('C'.$j,$value->celular);
                $hoja1->setCellValue('D'.$j,$value->correoElectronico);
                $hoja1->setCellValue('E'.$j,$value->direccion);
                if ($tipo == 11) {
                    $hoja1->setCellValue('F'.$j,$value->placa);
                    if(!is_null($value->params)) {
                        $hoja1->setCellValue('G'.$j,$this->getExplodeStringReport($value->params, 1,'@@'));
                        $hoja1->setCellValue('H'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value->params, 2,'@@'), 0,'/'));
                        $hoja1->setCellValue('I'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value->params, 2,'@@'), 1,'/'));
                    }
                    $hoja1->setCellValue('J'.$j,$value->fechaReg);
                    $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
                } elseif ($tipo == 22) {
                    $hoja1->setCellValue('F'.$j,$value->placa);
                    $hoja1->setCellValue('G'.$j,$value->kilometraje);
                    $hoja1->setCellValue('H'.$j,$this->getExplodeStringReport($value->marcamodelo, 0,'/'));
                    $hoja1->setCellValue('I'.$j,$this->getExplodeStringReport($value->marcamodelo, 1,'/'));
                    $hoja1->setCellValue('J'.$j,$value->fechaReg);
                    $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
                } elseif ($tipo == 33) {
                    if(!is_null($value->params)) {
                        $hoja1->setCellValue('F'.$j,$this->getExplodeStringReport($value->params, 0,'@@'));
                        $hoja1->setCellValue('G'.$j,$this->getExplodeStringReport($value->params, 1,'@@'));
                        $hoja1->setCellValue('H'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value->params, 2,'@@'), 0,'/'));
                        $hoja1->setCellValue('I'.$j,$this->getExplodeStringReport($this->getExplodeStringReport($value->params, 2,'@@'), 1,'/'));
                    }
                    $hoja1->setCellValue('J'.$j,$value->fechaReg);
                    $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
                } elseif ($tipo == 44) {
                    $hoja1->setCellValue('F'.$j,$value->asesorAuto);
                    $hoja1->setCellValue('G'.$j,$value->descripcion);
                    $hoja1->setCellValue('H'.$j,$value->fecha);
                    $hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
                }

                

                $j++;
            }
        } elseif ($tipo == 55) {
            $fileName = 'reporte_citas'; 
            $hoja1->setTitle("reporte_citas");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'doc_cliente');
            $hoja1->setCellValue('B'.$j,'cliente');
            $hoja1->setCellValue('C'.$j,'celular');
            $hoja1->setCellValue('D'.$j,'correo_electronico');
            $hoja1->setCellValue('E'.$j,'marca_auto');
            $hoja1->setCellValue('F'.$j,'modelo_auto');
            $hoja1->setCellValue('G'.$j,'vin_auto');
            $hoja1->setCellValue('H'.$j,'placa');
            $hoja1->setCellValue('I'.$j,'serie');
            $hoja1->setCellValue('J'.$j,'numero');
            $hoja1->setCellValue('K'.$j,'tipo_servicio');
            $hoja1->setCellValue('L'.$j,'año');
            $hoja1->setCellValue('M'.$j,'kilometraje');
            $hoja1->setCellValue('N'.$j,'fecha');
            $hoja1->setCellValue('O'.$j,'hora');
            $hoja1->setCellValue('P'.$j,'duración');
            $hoja1->setCellValue('Q'.$j,'indicaciones');
            $hoja1->setCellValue('R'.$j,'¿con_cita?');
            $hoja1->setCellValue('S'.$j,'¿con_seguro?');
            $hoja1->setCellValue('T'.$j,'¿con_soat?');
            $hoja1->setCellValue('U'.$j,'registrar_por');
            $hoja1->setCellValue('V'.$j,'fecha_registro');
          
            $hoja1->getStyle('A'.$j.':V'.$j)->applyFromArray($this->estilo_content);
                 

            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->documento);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->celular);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                $hoja1->setCellValue('E'.$j,$elem->marca);
                $hoja1->setCellValue('F'.$j,$elem->modelo);
                $hoja1->setCellValue('F'.$j,$elem->vin);
                $hoja1->setCellValue('G'.$j,$elem->placa);
                $hoja1->setCellValue('H'.$j,$elem->serie);
                $hoja1->setCellValue('J'.$j,$elem->numero);
                $hoja1->setCellValue('K'.$j,$elem->tipoServicio);
                $hoja1->setCellValue('L'.$j,$elem->anio);
                $hoja1->setCellValue('M'.$j,$elem->kilometraje);
                $hoja1->setCellValue('N'.$j,$elem->fecha);
                $hoja1->setCellValue('O'.$j,$elem->hora);
                $hoja1->setCellValue('P'.$j,$elem->duracion);
                $hoja1->setCellValue('Q'.$j,$elem->indicaciones);
                $hoja1->setCellValue('R'.$j,$elem->con_cita);
                $hoja1->setCellValue('S'.$j,$elem->con_seguro);
                $hoja1->setCellValue('T'.$j,$elem->con_soat);
                $hoja1->setCellValue('U'.$j,$elem->trabajador);
                $hoja1->setCellValue('V'.$j,$elem->fechaRegistro);

                $hoja1->getStyle('A'.$j.':V'.$j)->applyFromArray($this->estilo_content);
            }
        }
        return $fileName;
    }
    
    public function getReporteExcel($fechaI, $fechaF, $tipo, $tipoServ, $anio, $excel) {
        
        $data = $this->getData($fechaI, $fechaF, $tipo, $anio, $tipoServ);
        $excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$fileName = '';
        if ($tipo == 1) {
            $fileName = 'vta_detallada'; 
            $hoja1->setTitle("vta_detallada");
            $hoja1->setCellValue('A1','mac_serv');
            $hoja1->setCellValue('B1','des_linp');
            $hoja1->setCellValue('C1','des_tprd');
            $hoja1->setCellValue('D1','des_prod');
            $hoja1->setCellValue('E1','des_prod_marca');
           
            $hoja1->setCellValue('F1','abr_tdoc');
            $hoja1->setCellValue('G1','num_docu');
            $hoja1->setCellValue('H1','fec_docu');
            $hoja1->setCellValue('I1','num_guia');
            $hoja1->setCellValue('J1','doc_cli');
            $hoja1->setCellValue('K1','des_cli');
            $hoja1->setCellValue('L1','cantidad');
            $hoja1->setCellValue('M1','val_unit');
            $hoja1->setCellValue('N1','val_vta');
            $hoja1->setCellValue('O1','val_igv');
            $hoja1->setCellValue('P1','val_total');
            $hoja1->setCellValue('Q1','val_moneda');
            $hoja1->setCellValue('R1','val_costo');
            // $hoja1->setCellValue('Q1','val_moneda_cost');
            $hoja1->getStyle('A1:R1')->applyFromArray($this->estilo_content);
            
            // dd($data);
            $j = 2;
            foreach ($data as $value) {
                if (in_array($value->tipoComprobante,['F','B'])) {
                    $tipo = $value->tipoComprobante.'/V';
                } else {
                    $tipo = 'N/'.$value->tipoComprobante;
                }
                $hoja1->setCellValue('A'.$j,strtoupper($value->macro));
                $hoja1->setCellValue('B'.$j,$value->descripcion);
                $hoja1->setCellValue('C'.$j,$this->getStringProducto($value->tipoProducto));
                $hoja1->setCellValue('D'.$j,$value->nombre);
                $hoja1->setCellValue('E'.$j,$value->marca);
             
                $hoja1->setCellValue('F'.$j,$tipo);
                $hoja1->setCellValue('G'.$j,$value->documento);
                $hoja1->setCellValue('H'.$j,$value->fecha);
                $hoja1->setCellValue('I'.$j,'');
                $hoja1->setCellValue('J'.$j,$value->docCliente);
                $hoja1->setCellValue('K'.$j,$value->cliente);
                $hoja1->setCellValue('L'.$j,number_format($value->cantidad,2,'.',''));
                $hoja1->setCellValue('M'.$j,number_format(($value->situacion != 'V'?0:($value->igv>0?$value->precio/1.18:$value->precio)),2,'.',''));
                $hoja1->setCellValue('N'.$j,number_format(($value->situacion != 'V'?0:($value->igv>0?$value->subtotal/1.18:$value->subtotal)),2,'.',''));
                $hoja1->setCellValue('O'.$j,number_format(($value->situacion != 'V'?0:($value->igv>0?$value->subtotal/1.18* 0.18:$value->igv)),2,'.',''));
                $hoja1->setCellValue('P'.$j,"=SUM(N$j:O$j)");
                $hoja1->setCellValue('Q'.$j, "PEN");
                // $hoja1->setCellValue('N'.$j, $value->cant_items);
                // $hoja1->setCellValue('O'.$j, $value->moneda);
                
                $textCosto = [];
                if (!is_null($value->det_compra)) {
                    $textCosto = explode('-', $value->det_compra);
                } 
                if (!is_null($value->det_guia)) {
                    $textCosto = explode('-', $value->det_guia);
                }
                $hoja1->setCellValue('R'.$j,($value->situacion != 'V'?0:(count($textCosto)>0?$textCosto[1]:'')));
                // $hoja1->setCellValue('Q'.$j,($value->situacion != 'V'?0:(count($textCosto)>0?($textCosto[0]=='S'?'PEN':'USD'):'')));
                
                $hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);

                $j++;
            }
        }  elseif($tipo == 2) {
            $fileName = 'ventas_ple'; 
            $hoja1->setTitle("ventas_ple");
            $hoja1->setCellValue('A1','CAMPO 1');
            $hoja1->setCellValue('B1','CAMPO 2');
            $hoja1->setCellValue('C1','CAMPO 3');
            $hoja1->setCellValue('D1','CAMPO 4');
            $hoja1->setCellValue('E1','CAMPO 5');
            $hoja1->setCellValue('F1','CAMPO 6');
            $hoja1->setCellValue('G1','CAMPO 7');
            $hoja1->setCellValue('H1','CAMPO 8');
            $hoja1->setCellValue('I1','CAMPO 9');
            $hoja1->setCellValue('J1','CAMPO 10');
            $hoja1->setCellValue('K1','CAMPO 11');
            $hoja1->setCellValue('L1','CAMPO 12');
            $hoja1->setCellValue('M1','CAMPO 13');
            $hoja1->setCellValue('N1','CAMPO 14');
            $hoja1->setCellValue('O1','CAMPO 15');
            $hoja1->setCellValue('P1','CAMPO 16');
            $hoja1->setCellValue('Q1','CAMPO 17');
            $hoja1->setCellValue('R1','CAMPO 18');
            $hoja1->setCellValue('S1','CAMPO 19');
            $hoja1->setCellValue('T1','CAMPO 20');
            $hoja1->setCellValue('U1','CAMPO 21');
            $hoja1->setCellValue('V1','CAMPO 22');
            $hoja1->setCellValue('W1','CAMPO 23');
            $hoja1->setCellValue('X1','CAMPO 24');
            $hoja1->setCellValue('Y1','CAMPO 25');
            $hoja1->setCellValue('Z1','CAMPO 26');
            $hoja1->setCellValue('AA1','CAMPO 27');
            $hoja1->setCellValue('AB1','CAMPO 28');
            $hoja1->setCellValue('AC1','CAMPO 29');
            $hoja1->setCellValue('AD1','CAMPO 30');
            $hoja1->setCellValue('AE1','CAMPO 31');
            $hoja1->setCellValue('AF1','CAMPO 32');
            $hoja1->setCellValue('AG1','CAMPO 33');
            $hoja1->setCellValue('AH1','CAMPO 34');
            $hoja1->setCellValue('AI1','CAMPO 35');
            $hoja1->setCellValue('AJ1','CAMPO 36');
            $hoja1->getStyle('A1:AJ1')->applyFromArray($this->estilo_content);
            
            $j = 2;
            $cont = 1;
            $constant_zero = 0.00;
            $constant_uno = 1.00;
            $contants_two = 2.00;
            foreach ($data as $value) {
                $hoja1->setCellValue('A'.$j,$value->campo1);
                $hoja1->setCellValue('B'.$j,str_pad($cont,5,'0', STR_PAD_LEFT).$value->campo2);
                $hoja1->setCellValue('C'.$j,'M'.str_pad($cont,5,'0', STR_PAD_LEFT));
                $hoja1->setCellValue('D'.$j,$value->campo4);
                $hoja1->setCellValue('E'.$j,'');
                $hoja1->setCellValue('F'.$j,$value->campo6);
                $hoja1->setCellValue('G'.$j,$value->campo7);
                $hoja1->setCellValue('H'.$j,$value->campo8);
                $hoja1->setCellValue('I'.$j,'');
                $hoja1->setCellValue('J'.$j,$value->campo10);
                $hoja1->setCellValue('K'.$j,$value->campo11);
                $hoja1->setCellValue('L'.$j,$value->campo12);
                $hoja1->setCellValue('M'.$j,$constant_zero);
                $hoja1->setCellValue('N'.$j,($value->situacion != 'V'?$constant_zero:number_format($value->campo14,2,'.','')));
                $hoja1->setCellValue('O'.$j,$constant_zero);
                $hoja1->setCellValue('P'.$j,($value->situacion != 'V'?$constant_zero:number_format($value->campo16,2,'.','')));
                $hoja1->setCellValue('Q'.$j,$constant_zero);
                $hoja1->setCellValue('R'.$j,($value->situacion != 'V'?$constant_zero:number_format($value->campo18,2,'.','')));
                $hoja1->setCellValue('S'.$j,($value->situacion != 'V'?$constant_zero:number_format($value->campo19,2,'.','')));
                $hoja1->setCellValue('T'.$j,$constant_zero);
                $hoja1->setCellValue('U'.$j,$constant_zero);
                $hoja1->setCellValue('V'.$j,$constant_zero);
                $hoja1->setCellValue('W'.$j,$constant_zero);
                $hoja1->setCellValue('X'.$j,$constant_zero);
                $hoja1->setCellValue('Y'.$j,($value->situacion != 'V'?$constant_zero:number_format($value->campo25,2,'.','')));
                $hoja1->setCellValue('Z'.$j,$value->campo26);
                $hoja1->setCellValue('AA'.$j,number_format($value->campo27,2,'.',''));
                $hoja1->setCellValue('AB'.$j,$value->campo28);
                $hoja1->setCellValue('AC'.$j,$value->campo29);
                $hoja1->setCellValue('AD'.$j,$value->campo30);
                $hoja1->setCellValue('AE'.$j,$value->campo31);
                $hoja1->setCellValue('AF'.$j,'');
                $hoja1->setCellValue('AG'.$j,'');
                $hoja1->setCellValue('AH'.$j,'');
                $hoja1->setCellValue('AI'.$j,($value->situacion!='V'?$contants_two:$constant_uno));
                $hoja1->setCellValue('AJ'.$j,'');
                $hoja1->getStyle('A'.$j.':AJ'.$j)->applyFromArray($this->estilo_content);
                $j++;
                $cont++;
            }
            
            // 
        } elseif($tipo == 3) {
            $fileName = 'compras_ple'; 
            $hoja1->setTitle("compras_ple");
            $hoja1->setCellValue('A1','ano_mes');
            $hoja1->setCellValue('B1','num_cor');
            $hoja1->setCellValue('C1','new_cor');
            $hoja1->setCellValue('D1','fec_docu');
            $hoja1->setCellValue('E1','vct_docu');
            $hoja1->setCellValue('F1','tip_comp');
            $hoja1->setCellValue('G1','ser_docu');
            $hoja1->setCellValue('H1','ano_dua');
            $hoja1->setCellValue('I1','num_docu');
            $hoja1->setCellValue('J1','imp_cero');
            $hoja1->setCellValue('K1','tip_iden');
            $hoja1->setCellValue('L1','num_iden');
            $hoja1->setCellValue('M1','des_prv');
            $hoja1->setCellValue('N1','val_cdf');
            $hoja1->setCellValue('O1','igv_cdf');
            $hoja1->setCellValue('P1','val_sdf');
            $hoja1->setCellValue('Q1','igv_sdf');
            $hoja1->setCellValue('R1','val_ded');
            $hoja1->setCellValue('S1','igv_ded');
            $hoja1->setCellValue('T1','val_exon');
            $hoja1->setCellValue('U1','val_fisc');
            $hoja1->setCellValue('V1','val_bols');
            $hoja1->setCellValue('W1','val_otro');
            $hoja1->setCellValue('X1','val_ftot');
            $hoja1->setCellValue('Y1','cdg_mon');
            $hoja1->setCellValue('Z1','cmb_docu');
            $hoja1->setCellValue('AA1','fec_otro');
            $hoja1->setCellValue('AB1','tip_otro');
            $hoja1->setCellValue('AC1','ser_otro');
            $hoja1->setCellValue('AD1','dep_dua');
            $hoja1->setCellValue('AE1','num_otro');
            $hoja1->setCellValue('AF1','fec_detr');
            $hoja1->setCellValue('AG1','num_detr');
            $hoja1->setCellValue('AH1','swt_ret');
            $hoja1->setCellValue('AI1','cls_bien');
            $hoja1->setCellValue('AJ1','ide_cont');
            $hoja1->setCellValue('AK1','err_tcmb');
            $hoja1->setCellValue('AL1','prv_nhab');
            $hoja1->setCellValue('AM1','prv_exon');
            $hoja1->setCellValue('AN1','err_dnis');
            $hoja1->setCellValue('AO1','med_pago');
            $hoja1->setCellValue('AP1','est_anot');
            $hoja1->getStyle('A1:AP1')->applyFromArray($this->estilo_content);
            
            $j = 2;
            $cont = 1;
            $constant_zero = 0.00;
            $constant_uno = 1;
            // $contants_two = 2.00;
            $contants_empty = '';
            foreach ($data as $value) {
                $hoja1->setCellValue('A'.$j,$value->campo1);
                $hoja1->setCellValue('B'.$j,str_pad($cont,5,'0', STR_PAD_LEFT).$value->campo2);
                $hoja1->setCellValue('C'.$j,'M'.str_pad($cont,5,'0', STR_PAD_LEFT));
                $hoja1->setCellValue('D'.$j,$value->campo4);
                $hoja1->setCellValue('E'.$j,$value->campo5);
                $hoja1->setCellValue('F'.$j,$value->campo6);
                $hoja1->setCellValue('G'.$j,$this->getExplodeString($value->documento,0));
                $hoja1->setCellValue('H'.$j,$contants_empty);
                $hoja1->setCellValue('I'.$j,$this->getExplodeString($value->documento,1));
                $hoja1->setCellValue('J'.$j,$contants_empty);
                $hoja1->setCellValue('K'.$j,$value->campo11);
                $hoja1->setCellValue('L'.$j,$value->campo12);
                $hoja1->setCellValue('M'.$j,$value->campo13);
                $hoja1->setCellValue('N'.$j,$contants_empty);
                $hoja1->setCellValue('O'.$j,$contants_empty);
                $hoja1->setCellValue('P'.$j,$contants_empty);
                $hoja1->setCellValue('Q'.$j,$contants_empty);
                $hoja1->setCellValue('R'.$j,$contants_empty);
                $hoja1->setCellValue('S'.$j,$contants_empty);
                $hoja1->setCellValue('T'.$j,number_format($value->campo20,2,'.',''));
                $hoja1->setCellValue('U'.$j,$constant_zero);
                $hoja1->setCellValue('V'.$j,$constant_zero);
                $hoja1->setCellValue('W'.$j,$constant_zero);
                $hoja1->setCellValue('X'.$j,number_format($value->campo24,2,'.',''));
                $hoja1->setCellValue('Y'.$j,$value->campo25);
                $hoja1->setCellValue('Z'.$j,$value->campo26);
                $hoja1->setCellValue('AA'.$j,$value->campo27);
                $hoja1->setCellValue('AB'.$j,$value->campo28);
                $hoja1->setCellValue('AC'.$j,(!is_null($value->ref)?$this->getExplodeString($value->ref,0):''));
                $hoja1->setCellValue('AD'.$j,$contants_empty);
                $hoja1->setCellValue('AE'.$j,(!is_null($value->ref)?$this->getExplodeString($value->ref,1):''));
                $hoja1->setCellValue('AF'.$j,$contants_empty);
                $hoja1->setCellValue('AG'.$j,$contants_empty);
                $hoja1->setCellValue('AH'.$j,$contants_empty);
                $hoja1->setCellValue('AI'.$j,$constant_uno);
                $hoja1->setCellValue('AJ'.$j,$contants_empty);
                $hoja1->setCellValue('AK'.$j,$contants_empty);
                $hoja1->setCellValue('AL'.$j,$contants_empty);
                $hoja1->setCellValue('AM'.$j,$contants_empty);
                $hoja1->setCellValue('AN'.$j,$contants_empty);
                $hoja1->setCellValue('AO'.$j,$contants_empty);
                $hoja1->setCellValue('AP'.$j,$value->campo35);

                $hoja1->getStyle('A'.$j.':AP'.$j)->applyFromArray($this->estilo_content);
                $j++;
                $cont++;
            }
        } elseif($tipo == 4) {
            $fileName = 'inv_valorizado'; 
            $hoja1->setTitle("inv_valorizado");
            $hoja1->setCellValue('A1','des_area');
            $hoja1->setCellValue('B1','des_mon');
            $hoja1->setCellValue('C1','des_prod');
            $hoja1->setCellValue('D1','marca_prod');
            $hoja1->setCellValue('E1','cant_sld');
            $hoja1->setCellValue('F1','pre_sld');
            $hoja1->setCellValue('G1','tot_sld');

            $hoja1->getStyle('A1:G1')->applyFromArray($this->estilo_content);
            
            $j = 2;
            foreach ($data as $value) {
                $textCosto = [];
                if (!is_null($value->det_compra)) {
                    $textCosto = explode('-', $value->det_compra);
                } 
                if (!is_null($value->det_guia)) {
                    $textCosto = explode('-', $value->det_guia);
                }
         
                $hoja1->setCellValue('A'.$j,$value->almacen);
                $hoja1->setCellValue('B'.$j,(count($textCosto)>0?($textCosto[0]=='S'?'SOLES':'DOLARES'):''));
                $hoja1->setCellValue('C'.$j,$this->getProductoConcat($value));
                $hoja1->setCellValue('D'.$j,$value->marca);
                $hoja1->setCellValue('E'.$j,$value->stock_detalle);
                $hoja1->setCellValue('F'.$j,(count($textCosto)>0?$textCosto[1]:''));
                $hoja1->setCellValue('G'.$j,(count($textCosto)>0?$textCosto[1]*$value->stock_detalle:''));
 
                $hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);

                $j++;
            }
    
        } elseif($tipo == 5) {
            $fileName = 'cpra_detallada'; 
            $hoja1->setTitle("cpra_detallada");
            $hoja1->setCellValue('A1','des_linp');
            $hoja1->setCellValue('B1','des_tprd');
            $hoja1->setCellValue('C1','des_prod');
            $hoja1->setCellValue('D1','des_prod_marca');
            $hoja1->setCellValue('E1','abr_tdoc');
            $hoja1->setCellValue('F1','num_docu');
            $hoja1->setCellValue('G1','fec_docu');
            $hoja1->setCellValue('H1','num_guia');
            $hoja1->setCellValue('I1','doc_cli');
            $hoja1->setCellValue('J1','des_cli');
            $hoja1->setCellValue('K1','cantidad');
            $hoja1->setCellValue('L1','val_unit');
            $hoja1->setCellValue('M1','val_cpra');
            $hoja1->setCellValue('N1','val_igv');
            $hoja1->setCellValue('O1','val_total');
            $hoja1->setCellValue('P1','val_moneda');
            // $hoja1->setCellValue('P1','val_costo');
            // $hoja1->setCellValue('Q1','val_moneda_cost');
            $hoja1->getStyle('A1:P1')->applyFromArray($this->estilo_content);
            
            // dd($data);
            $j = 2;
            foreach ($data as $value) {
                if (in_array($value->tipoDocumento,['F','B'])) {
                    $tipo = $value->tipoDocumento.'/C';
                } else {
                    $tipo = 'N/'.$value->tipoDocumento;
                }
                $hoja1->setCellValue('A'.$j,$value->descripcion);
                $hoja1->setCellValue('B'.$j,$this->getStringProducto($value->tipoProducto));
                $hoja1->setCellValue('C'.$j,$value->nombre);
                $hoja1->setCellValue('D'.$j,$value->marca);
                $hoja1->setCellValue('E'.$j,$tipo);
              
                $hoja1->setCellValue('F'.$j,$value->documento);
                $hoja1->setCellValue('G'.$j,$value->fecha);
                $hoja1->setCellValue('H'.$j,'');
                $hoja1->setCellValue('I'.$j,$value->docProveedor);
                $hoja1->setCellValue('J'.$j,$value->proveedor);
                $hoja1->setCellValue('K'.$j,number_format($value->cantidad,2,'.',''));
                $hoja1->setCellValue('L'.$j,number_format(($value->situacion != 'V'?0:$value->precio),2,'.',''));
                $hoja1->setCellValue('M'.$j,number_format(($value->situacion != 'V'?0:$value->subtotal),2,'.',''));
                $hoja1->setCellValue('N'.$j,number_format(($value->situacion != 'V'?0:($value->igv>0?$value->subtotal* 0.18:$value->igv)),2,'.',''));
                $hoja1->setCellValue('O' . $j, "=L$j*K$j+N$j");
                $hoja1->setCellValue('P'.$j, "PEN");
                // $hoja1->setCellValue('N'.$j, $value->cant_items);
                // $hoja1->setCellValue('O'.$j, $value->moneda);
                
                $textCosto = [];
                // if (!is_null($value->det_compra)) {
                //     $textCosto = explode('-', $value->det_compra);
                // } 
                // if (!is_null($value->det_guia)) {
                //     $textCosto = explode('-', $value->det_guia);
                // }
                // $hoja1->setCellValue('P'.$j,($value->situacion != 'V'?0:(count($textCosto)>0?$textCosto[1]:'')));
                // $hoja1->setCellValue('Q'.$j,($value->situacion != 'V'?0:(count($textCosto)>0?($textCosto[0]=='S'?'PEN':'USD'):'')));
                
                $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_content);

                $j++;
            }
        } elseif($tipo == 6) {
            $fileName = 'reporte_abc'; 
            $hoja1->setTitle("03 meses");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'tipo_producto');
            $hoja1->setCellValue('B'.$j,'descripcion');
            $hoja1->setCellValue('C'.$j,'cantidad');
            $hoja1->setCellValue('D'.$j,'precio_compra');
            $hoja1->setCellValue('E'.$j,'precio_venta');
            $hoja1->setCellValue('F'.$j,'tipo_cambio');
            $hoja1->setCellValue('G'.$j,'tipo_moneda');
            $hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->tipo == 'A3') {
                    $j++;
                    $hoja1->setCellValue('A'.$j,$elem->tipoProducto);
                    $hoja1->setCellValue('B'.$j,$elem->descripcion);
                    $hoja1->setCellValue('C'.$j,$elem->cantidad);
                    $hoja1->setCellValue('D'.$j,$elem->preciocompra);
                    $hoja1->setCellValue('E'.$j,$elem->precioventa);
                    $hoja1->setCellValue('F'.$j,$elem->tipoCambio);
                    $hoja1->setCellValue('G'.$j, ($elem->tipoMoneda == 'S'? 'Soles': 'Dólares'));
                    $hoja1->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
                }
            }

            $hoja2 = $excel->createSheet();

            // $excel->setActiveSheetIndex(1);
            // $hoja2 = $excel->getActiveSheet();
            $hoja2->setTitle("05 meses");
            $j = 1;
            $hoja2->setCellValue('A'.$j,'tipo_producto');
            $hoja2->setCellValue('B'.$j,'descripcion');
            $hoja2->setCellValue('C'.$j,'cantidad');
            $hoja2->setCellValue('D'.$j,'precio_compra');
            $hoja2->setCellValue('E'.$j,'precio_venta');
            $hoja2->setCellValue('F'.$j,'tipo_cambio');
            $hoja2->setCellValue('G'.$j,'tipo_moneda');
            $hoja2->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->tipo == 'A4') {
                    $j++;
                    $hoja2->setCellValue('A'.$j,$elem->tipoProducto);
                    $hoja2->setCellValue('B'.$j,$elem->descripcion);
                    $hoja2->setCellValue('C'.$j,$elem->cantidad);
                    $hoja2->setCellValue('D'.$j,$elem->preciocompra);
                    $hoja2->setCellValue('E'.$j,$elem->precioventa);
                    $hoja2->setCellValue('F'.$j,$elem->tipoCambio);
                    $hoja2->setCellValue('G'.$j, ($elem->tipoMoneda == 'S'? 'Soles': 'Dólares'));
                    $hoja2->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
                }
            }

            // $excel->setActiveSheetIndex(2);
            // $hoja3 = $excel->getActiveSheet();
            $hoja3 = $excel->createSheet();
            $hoja3->setTitle("+06 meses");
            $j = 1;
            $hoja3->setCellValue('A'.$j,'tipo_producto');
            $hoja3->setCellValue('B'.$j,'descripcion');
            $hoja3->setCellValue('C'.$j,'cantidad');
            $hoja3->setCellValue('D'.$j,'precio_compra');
            $hoja3->setCellValue('E'.$j,'precio_venta');
            $hoja3->setCellValue('F'.$j,'tipo_cambio');
            $hoja3->setCellValue('G'.$j,'tipo_moneda');
            $hoja3->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->tipo == 'A6') {
                    $j++;
                    $hoja3->setCellValue('A'.$j,$elem->tipoProducto);
                    $hoja3->setCellValue('B'.$j,$elem->descripcion);
                    $hoja3->setCellValue('C'.$j,$elem->cantidad);
                    $hoja3->setCellValue('D'.$j,$elem->preciocompra);
                    $hoja3->setCellValue('E'.$j,$elem->precioventa);
                    $hoja3->setCellValue('F'.$j,$elem->tipoCambio);
                    $hoja3->setCellValue('G'.$j, ($elem->tipoMoneda == 'S'? 'Soles': 'Dólares'));
                    $hoja3->getStyle('A'.$j.':G'.$j)->applyFromArray($this->estilo_content);
                }
            }
        } elseif($tipo == 7) {
            $fileName = 'reporte_cons_interno'; 
            $hoja1->setTitle("consumo interno");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'tipo_producto');
            $hoja1->setCellValue('B'.$j,'descripcion');
            $hoja1->setCellValue('C'.$j,'observacion');
            $hoja1->setCellValue('D'.$j,'cantidad');
            $hoja1->setCellValue('E'.$j,'serie');
            $hoja1->setCellValue('F'.$j,'numero');
            $hoja1->setCellValue('G'.$j,'fecha');
            $hoja1->setCellValue('H'.$j,'precio_venta');
            $hoja1->setCellValue('I'.$j,'tipo_cambio');
            $hoja1->setCellValue('J'.$j,'tipo_moneda');
            $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->tipoProducto);
                $hoja1->setCellValue('B'.$j,$elem->descripcion);
                $hoja1->setCellValue('C'.$j, $elem->observacion);
                $hoja1->setCellValue('D'.$j,$elem->cantidad);
                $hoja1->setCellValue('E'.$j,$elem->serie);
                $hoja1->setCellValue('F'.$j,$elem->numero);
                $hoja1->setCellValue('G'.$j,$elem->fecha);
                $hoja1->setCellValue('H'.$j,$elem->precioventa);
                $hoja1->setCellValue('I'.$j,$elem->tipoCambio);
                $hoja1->setCellValue('J'.$j, ($elem->tipoMoneda == 'S'? 'Soles': 'Dólares'));
                $hoja1->getStyle('A'.$j.':J'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 8) {
            // dd($data);
            $fileName = 'reporte_casis'; 
            $hoja1->setTitle("REPORTE CASIS");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'tipo_servicio');
            $hoja1->setCellValue('B'.$j,'modelo');
            $hoja1->setCellValue('C'.$j,'orden');
            $hoja1->setCellValue('D'.$j,'total_taller');
            $hoja1->setCellValue('E'.$j,'total_repuestos');
            $hoja1->setCellValue('F'.$j,'tiempo');
            $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
            $tipo = '';
            $acumTiempo = 0;
            $acumContador = 0;
            $acumTotalServicio = 0;
            $acumTotalProducto = 0;

            foreach ($data as $elem) {
                if ($j == 1) {
                    $tipo = $elem->tipoServicio;
                }

                if ($elem->tipoServicio == $tipo) {
                    $acumContador++;
                    if ($elem->tiempo != '') {
                        $acumTiempo += $elem->tiempo;
                    }
                    $acumTotalServicio += $elem->totalServicios;
                    $acumTotalProducto += $elem->totalProductos;
                } else {
                    $j++;
                    $hoja1->setCellValue('A'.$j,'TOTAL');
                    $hoja1->setCellValue('C'.$j, $acumContador);
                    $hoja1->setCellValue('D'.$j, $acumTotalServicio);
                    $hoja1->setCellValue('E'.$j, $acumTotalProducto);
                    $hoja1->setCellValue('F'.$j, ($acumTiempo > 0?$acumTiempo:''));
                    $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);

                    $acumTiempo = 0;
                    $acumContador = 0;
                    $acumTotalServicio = 0;
                    $acumTotalProducto = 0;
                    $tipo = $elem->tipoServicio;
                    $j+=2;

                    $hoja1->setCellValue('A'.$j,'tipo_servicio');
                    $hoja1->setCellValue('B'.$j,'modelo');
                    $hoja1->setCellValue('C'.$j,'orden');
                    $hoja1->setCellValue('D'.$j,'total_taller');
                    $hoja1->setCellValue('E'.$j,'total_repuestos');
                    $hoja1->setCellValue('F'.$j,'tiempo');
                    $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
                   
                }

                $j++;
                $hoja1->setCellValue('A'.$j,$elem->tipoServicioText);
                $hoja1->setCellValue('B'.$j,$elem->modelo);
                $hoja1->setCellValue('C'.$j, $elem->orden);
                $hoja1->setCellValue('D'.$j,$elem->totalServicios);
                $hoja1->setCellValue('E'.$j,$elem->totalProductos);
                $hoja1->setCellValue('F'.$j,$elem->tiempo);
                $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
            }

            if ($acumContador > 0) {
                $j++;
                $hoja1->setCellValue('A'.$j,'TOTAL');
                $hoja1->setCellValue('C'.$j, $acumContador);
                $hoja1->setCellValue('D'.$j, $acumTotalServicio);
                $hoja1->setCellValue('E'.$j, $acumTotalProducto);
                $hoja1->setCellValue('F'.$j, ($acumTiempo > 0?$acumTiempo:''));
                $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);

                $acumTiempo = 0;
                $acumContador = 0;
                $acumTotalServicio = 0;
                $acumTotalProducto = 0;
                $tipo = $elem->tipoServicio;
                $j+=2;
          }



        } elseif ($tipo == 9) {
            $fileName = 'reporte_gz'; 
            $hoja1->setTitle("reporte GZ");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'fecha_orden');
            $hoja1->setCellValue('B'.$j,'vin');
            $hoja1->setCellValue('C'.$j,'modelo');
            $hoja1->setCellValue('D'.$j,'año');
            $hoja1->setCellValue('E'.$j,'numero_orden');
            $hoja1->setCellValue('F'.$j,'cliente');
            $hoja1->setCellValue('G'.$j,'email');
            $hoja1->setCellValue('H'.$j,'telefono');
            $hoja1->setCellValue('I'.$j,'id_tecnico');
            $hoja1->setCellValue('J'.$j,'tecnico');
            $hoja1->setCellValue('K'.$j,'tipo_transaccion');
            $hoja1->getStyle('A'.$j.':K'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->fecha);
                $hoja1->setCellValue('B'.$j,$elem->vin);
                $hoja1->setCellValue('C'.$j, $elem->modelo);
                $hoja1->setCellValue('D'.$j,$elem->anio);
                $hoja1->setCellValue('E'.$j,$elem->orden);
                $hoja1->setCellValue('F'.$j,$elem->cliente);
                $hoja1->setCellValue('G'.$j,$elem->correoElectronico);
                $hoja1->setCellValue('H'.$j,$elem->telefono);
                $hoja1->setCellValue('I'.$j,$elem->idtecnico);
                $hoja1->setCellValue('J'.$j,$elem->tecnico);
                $hoja1->setCellValue('K'.$j, $elem->tipoServicio);
                $hoja1->getStyle('A'.$j.':K'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 10) {
            $fileName = 'compras_auto_con_flete'; 
            $hoja1->setTitle("compras con flete");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'fecha_compra');
            $hoja1->setCellValue('B'.$j,'tipo_documento');
            $hoja1->setCellValue('C'.$j,'num_documento');
            $hoja1->setCellValue('D'.$j,'doc_proveedor');
            $hoja1->setCellValue('E'.$j,'proveedor');
            $hoja1->setCellValue('F'.$j,'fecha_vencimiento');
            $hoja1->setCellValue('G'.$j,'tipo_cambio');
            $hoja1->setCellValue('H'.$j,'moneda');
            $hoja1->setCellValue('I'.$j,'subtotal');
            $hoja1->setCellValue('J'.$j,'igv');
            $hoja1->setCellValue('K'.$j,'total');
            $hoja1->setCellValue('L'.$j,'flete');
            $hoja1->setCellValue('M'.$j,'subtotal_soles');
            $hoja1->setCellValue('N'.$j,'igv_soles');
            $hoja1->setCellValue('O'.$j,'total_soles');
            $hoja1->setCellValue('P'.$j,'flete_soles');
            $hoja1->setCellValue('Q'.$j,'registrado_por');
            $hoja1->setCellValue('R'.$j,'fecha_registro');
            $hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->fecha);
                $hoja1->setCellValue('B'.$j,$elem->tipoDocumento);
                $hoja1->setCellValue('C'.$j,$elem->documento);
                $hoja1->setCellValue('D'.$j,$elem->docProveedor);
                $hoja1->setCellValue('E'.$j,$elem->proveedor);
                $hoja1->setCellValue('F'.$j,$elem->fechaVencimiento);
                $hoja1->setCellValue('G'.$j,$elem->tipoCambio);
                $hoja1->setCellValue('H'.$j,$elem->moneda);
                $hoja1->setCellValue('I'.$j,$elem->subtotal);
                $hoja1->setCellValue('J'.$j, $elem->igv);
                $hoja1->setCellValue('K'.$j, $elem->total);
                $hoja1->setCellValue('L'.$j, $elem->flete);
                $hoja1->setCellValue('M'.$j, $elem->subtotal*$elem->tipoCambio);
                $hoja1->setCellValue('N'.$j, $elem->igv*$elem->tipoCambio);
                $hoja1->setCellValue('O'.$j, $elem->total*$elem->tipoCambio);
                $hoja1->setCellValue('P'.$j, $elem->flete*$elem->tipoCambio);
                $hoja1->setCellValue('Q'.$j, $elem->trabajador);
                $hoja1->setCellValue('R'.$j, $elem->fechaR);
                $hoja1->getStyle('A'.$j.':R'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 11) {
            $fileName = 'venta_autos'; 
            $hoja1->setTitle("LIVIANOS");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'fecha');
            $hoja1->setCellValue('B'.$j,'tipo_documento');
            $hoja1->setCellValue('C'.$j,'serie');
            $hoja1->setCellValue('D'.$j,'numero');
            $hoja1->setCellValue('E'.$j,'descripcion');
            $hoja1->setCellValue('F'.$j,'cantidad');
            $hoja1->setCellValue('G'.$j,'precio');
            $hoja1->setCellValue('H'.$j,'subtotal');
            $hoja1->setCellValue('I'.$j,'tipo_cambio');
            $hoja1->setCellValue('J'.$j,'moneda');
            $hoja1->setCellValue('K'.$j,'cliente');
            $hoja1->setCellValue('L'.$j,'marca');
            $hoja1->setCellValue('M'.$j,'modelo');
            $hoja1->setCellValue('N'.$j,'versión');
            $hoja1->setCellValue('O'.$j,'transimisión');
            $hoja1->setCellValue('P'.$j,'registrado_por');
            $hoja1->setCellValue('Q'.$j,'fecha_registro');
          
            $hoja1->getStyle('A'.$j.':Q'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->linea == 'L') {
                    $j++;
                    $hoja1->setCellValue('A'.$j,$elem->fecha);
                    $hoja1->setCellValue('B'.$j,$elem->tipoDocumento);
                    $hoja1->setCellValue('C'.$j,$elem->serie);
                    $hoja1->setCellValue('D'.$j,$elem->numero);
                    $hoja1->setCellValue('E'.$j,$elem->descripcion);
                    $hoja1->setCellValue('F'.$j,$elem->cantidad);
                    $hoja1->setCellValue('G'.$j,$elem->precio);
                    $hoja1->setCellValue('H'.$j,$elem->subTotal);
                    $hoja1->setCellValue('I'.$j,$elem->tipoCambio);
                    $hoja1->setCellValue('J'.$j, $elem->moneda);
                    $hoja1->setCellValue('K'.$j,$elem->cliente);
                    $hoja1->setCellValue('L'.$j,$elem->marca);
                    $hoja1->setCellValue('M'.$j,$elem->modelo);
                    $hoja1->setCellValue('N'.$j,$elem->version);
                    $hoja1->setCellValue('O'.$j,$elem->transmision);
                    $hoja1->setCellValue('P'.$j,$elem->trabajador);
                    $hoja1->setCellValue('Q'.$j,$elem->fechaR);
                    $hoja1->getStyle('A'.$j.':Q'.$j)->applyFromArray($this->estilo_content);
                }
            }

            $hoja2 = $excel->createSheet();

            // $excel->setActiveSheetIndex(1);
            // $hoja2 = $excel->getActiveSheet();
            $hoja2->setTitle("PESADOS");
            $j = 1;
           
            $hoja2->setCellValue('A'.$j,'fecha');
            $hoja2->setCellValue('B'.$j,'tipo_documento');
            $hoja2->setCellValue('C'.$j,'serie');
            $hoja2->setCellValue('D'.$j,'numero');
            $hoja2->setCellValue('E'.$j,'descripcion');
            $hoja2->setCellValue('F'.$j,'cantidad');
            $hoja2->setCellValue('G'.$j,'precio');
            $hoja2->setCellValue('H'.$j,'subtotal');
            $hoja2->setCellValue('I'.$j,'tipo_cambio');
            $hoja2->setCellValue('J'.$j,'moneda');
            $hoja2->setCellValue('K'.$j,'cliente');
            $hoja2->setCellValue('L'.$j,'marca');
            $hoja2->setCellValue('M'.$j,'modelo');
            $hoja2->setCellValue('N'.$j,'versión');
            $hoja2->setCellValue('O'.$j,'transimisión');
            $hoja2->setCellValue('P'.$j,'registrado_por');
            $hoja2->setCellValue('Q'.$j,'fecha_registro');
          
            $hoja2->getStyle('A'.$j.':Q'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->linea == 'P') {
                    $j++;
                    $hoja2->setCellValue('A'.$j,$elem->fecha);
                    $hoja2->setCellValue('B'.$j,$elem->tipoDocumento);
                    $hoja2->setCellValue('C'.$j,$elem->serie);
                    $hoja2->setCellValue('D'.$j,$elem->numero);
                    $hoja2->setCellValue('E'.$j,$elem->descripcion);
                    $hoja2->setCellValue('F'.$j,$elem->cantidad);
                    $hoja2->setCellValue('G'.$j,$elem->precio);
                    $hoja2->setCellValue('H'.$j,$elem->subTotal);
                    $hoja2->setCellValue('I'.$j,$elem->tipoCambio);
                    $hoja2->setCellValue('J'.$j, $elem->moneda);
                    $hoja2->setCellValue('K'.$j,$elem->cliente);
                    $hoja2->setCellValue('L'.$j,$elem->marca);
                    $hoja2->setCellValue('M'.$j,$elem->modelo);
                    $hoja2->setCellValue('N'.$j,$elem->version);
                    $hoja2->setCellValue('O'.$j,$elem->transmision);
                    $hoja2->setCellValue('P'.$j,$elem->trabajador);
                    $hoja2->setCellValue('Q'.$j,$elem->fechaR);
                    $hoja2->getStyle('A'.$j.':Q'.$j)->applyFromArray($this->estilo_content);
               }
            }
        } elseif ($tipo == 12) {
            $fileName = 'cuentas_x_cobrar_pagar'; 
            $hoja1->setTitle("CUENTAS POR COBRAR");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'tipo_documento');
            $hoja1->setCellValue('B'.$j,'serie');
            $hoja1->setCellValue('C'.$j,'numero');
            $hoja1->setCellValue('D'.$j,'doc_cliente');
            $hoja1->setCellValue('E'.$j,'cliente');
            $hoja1->setCellValue('F'.$j,'fecha_vencimiento');
            $hoja1->setCellValue('G'.$j,'importe');
            $hoja1->setCellValue('H'.$j,'moneda');
            $hoja1->setCellValue('I'.$j,'operacion');
            $hoja1->setCellValue('J'.$j,'tipo_cambio');
            $hoja1->setCellValue('K'.$j,'importe_soles');
            $hoja1->setCellValue('L'.$j,'saldo');
            $hoja1->setCellValue('M'.$j,'estado');
            $hoja1->setCellValue('N'.$j,'sustento');
            $hoja1->setCellValue('O'.$j,'registrado_por');
            $hoja1->setCellValue('P'.$j,'fecha_registro');
          
            $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->tipocuenta == 1) {
                    $j++;
                    $hoja1->setCellValue('A'.$j,$elem->tipodocumento);
                    $hoja1->setCellValue('B'.$j,$elem->serie);
                    $hoja1->setCellValue('C'.$j,$elem->numero);
                    $hoja1->setCellValue('D'.$j,$elem->docCliente);
                    $hoja1->setCellValue('E'.$j,$elem->cliente);
                    $hoja1->setCellValue('F'.$j,$elem->fechavencimiento);
                    $hoja1->setCellValue('G'.$j,$elem->importe);
                    $hoja1->setCellValue('H'.$j,$elem->moneda);
                    $hoja1->setCellValue('I'.$j,$elem->operacion);
                    $hoja1->setCellValue('J'.$j, $elem->tipocambio);
                    $hoja1->setCellValue('K'.$j,$elem->importeSoles);
                    $hoja1->setCellValue('L'.$j,$elem->saldo);
                    $hoja1->setCellValue('M'.$j,$elem->estado);
                    $hoja1->setCellValue('N'.$j,$elem->sustento);
                    $hoja1->setCellValue('O'.$j,$elem->trabajador);
                    $hoja1->setCellValue('P'.$j,$elem->fechaR);
                    $hoja1->getStyle('A'.$j.':P'.$j)->applyFromArray($this->estilo_content);
                }
            }

            $hoja2 = $excel->createSheet();

            // $excel->setActiveSheetIndex(1);
            // $hoja2 = $excel->getActiveSheet();
            $hoja2->setTitle("CUENTAS POR PAGAR");
            $j = 1;
           
            $hoja2->setCellValue('A'.$j,'tipo_documento');
            $hoja2->setCellValue('B'.$j,'serie');
            $hoja2->setCellValue('C'.$j,'numero');
            $hoja2->setCellValue('D'.$j,'doc_proveedor');
            $hoja2->setCellValue('E'.$j,'proveedor');
            $hoja2->setCellValue('F'.$j,'fecha_vencimiento');
            $hoja2->setCellValue('G'.$j,'tipo');
            $hoja2->setCellValue('H'.$j,'tipo_gasto');
            $hoja2->setCellValue('I'.$j,'unidad');
            $hoja2->setCellValue('J'.$j,'partida');
            $hoja2->setCellValue('K'.$j,'importe');
            $hoja2->setCellValue('L'.$j,'moneda');
            $hoja2->setCellValue('M'.$j,'operacion');
            $hoja2->setCellValue('N'.$j,'tipo_cambio');
            $hoja2->setCellValue('O'.$j,'importe_soles');
            $hoja2->setCellValue('P'.$j,'saldo');
            $hoja2->setCellValue('Q'.$j,'estado');
            $hoja2->setCellValue('R'.$j,'sustento');
            $hoja2->setCellValue('S'.$j,'registrado_por');
            $hoja2->setCellValue('T'.$j,'fecha_registro');
          
            $hoja2->getStyle('A'.$j.':T'.$j)->applyFromArray($this->estilo_content);
           
            foreach ($data as $elem) {
                if ($elem->tipocuenta == 2) {
                    $j++;
                    $hoja2->setCellValue('A'.$j,$elem->tipodocumento);
                    $hoja2->setCellValue('B'.$j,$elem->serie);
                    $hoja2->setCellValue('C'.$j,$elem->numero);
                    $hoja2->setCellValue('D'.$j,$elem->docProveedor);
                    $hoja2->setCellValue('E'.$j,$elem->proveedor);
                    $hoja2->setCellValue('F'.$j,$elem->fechavencimiento);
                    $hoja2->setCellValue('G'.$j,$elem->tipo);
                    $hoja2->setCellValue('H'.$j,$elem->tipogasto);
                    $hoja2->setCellValue('I'.$j,$elem->unidad);
                    $hoja2->setCellValue('J'.$j,$elem->partida);
                    $hoja2->setCellValue('K'.$j,$elem->importe);
                    $hoja2->setCellValue('L'.$j,$elem->moneda);
                    $hoja2->setCellValue('M'.$j,$elem->operacion);
                    $hoja2->setCellValue('N'.$j, $elem->tipocambio);
                    $hoja2->setCellValue('O'.$j,$elem->importeSoles);
                    $hoja2->setCellValue('P'.$j,$elem->saldo);
                    $hoja2->setCellValue('Q'.$j,$elem->estado);
                    $hoja2->setCellValue('R'.$j,$elem->sustento);
                    $hoja2->setCellValue('S'.$j,$elem->trabajador);
                    $hoja2->setCellValue('T'.$j,$elem->fechaR);
                    $hoja2->getStyle('A'.$j.':T'.$j)->applyFromArray($this->estilo_content);
                }
            }
        } elseif ($tipo == 13) {
            $fileName = 'reporte_estado_encuestas'; 
            $hoja1->setTitle("estado de encuestas");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'fecha');
            $hoja1->setCellValue('B'.$j,'doc_cliente');
            $hoja1->setCellValue('C'.$j,'cliente');
            $hoja1->setCellValue('D'.$j,'orden');
            $hoja1->setCellValue('E'.$j,'placa');
            $hoja1->setCellValue('F'.$j,'puntuacion_encuesta');
            $hoja1->setCellValue('G'.$j,'estado_contactabilidad');
            $hoja1->setCellValue('H'.$j,'registrado_por');
            $hoja1->setCellValue('I'.$j,'fecha_registro');
            $hoja1->getStyle('A'.$j.':I'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->fecha);
                $hoja1->setCellValue('B'.$j,$elem->documento);
                $hoja1->setCellValue('C'.$j,$elem->cliente);
                $hoja1->setCellValue('D'.$j,$elem->orden);
                $hoja1->setCellValue('E'.$j,$elem->placa);
                $hoja1->setCellValue('F'.$j,($elem->puntuacionEncuesta > 0?$elem->puntuacionEncuesta:''));
                $hoja1->setCellValue('G'.$j,$elem->estadoContactabilidad);
                $hoja1->setCellValue('H'.$j, $elem->trabajador);
                $hoja1->setCellValue('I'.$j, $elem->fechaR);
                $hoja1->getStyle('A'.$j.':I'.$j)->applyFromArray($this->estilo_content);
            }

        } elseif ($tipo == 14) {
            $fileName = 'reporte_costo_totales'; 
            $hoja1->setTitle("costos totales");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'DNI/RUC');
            $hoja1->setCellValue('B'.$j,'NOMBRE Y APELLIDO');
            $hoja1->setCellValue('C'.$j,'VIN');
            $hoja1->setCellValue('D'.$j,'CORREO ELECTRONICO');
            $hoja1->setCellValue('E'.$j,'TELÉFONO');
            $hoja1->setCellValue('F'.$j,'DIRECCIÓN');
            $hoja1->setCellValue('G'.$j,'NRO DE COMPROBANTE');
            $hoja1->setCellValue('H'.$j,'FECHA FACTURACIÓN');
            $hoja1->setCellValue('I'.$j,'ASESOR');
            $hoja1->setCellValue('J'.$j,'DIVISIÓN');
            $hoja1->setCellValue('K'.$j,'MARCA');
            $hoja1->setCellValue('L'.$j,'MODELO');
            $hoja1->setCellValue('M'.$j,'VERSIÓN');
            $hoja1->setCellValue('N'.$j,'TRANSMISIÓN');
            $hoja1->setCellValue('O'.$j,'COSTO TOTAL');
            $hoja1->setCellValue('P'.$j,'PRECIO VENTA');
            $hoja1->setCellValue('Q'.$j,'DOC. COMPRA');
            $hoja1->setCellValue('R'.$j,'MONEDA');
            $hoja1->setCellValue('S'.$j,'MC');
           
            $hoja1->getStyle('A'.$j.':S'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->documento);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->vin);
                $hoja1->setCellValue('D'.$j,$elem->correoElectronico);
                $hoja1->setCellValue('E'.$j,$elem->telefono);
                $hoja1->setCellValue('F'.$j,$elem->direccion);
                $hoja1->setCellValue('G'.$j,$elem->comprobante);
                $hoja1->setCellValue('H'.$j, $elem->fecha);
                $hoja1->setCellValue('I'.$j, $elem->asesorAuto);
                $hoja1->setCellValue('J'.$j, $elem->division);
                $hoja1->setCellValue('K'.$j, $elem->marca);
                $hoja1->setCellValue('L'.$j, $elem->modelo);
                $hoja1->setCellValue('M'.$j, $elem->version);
                $hoja1->setCellValue('N'.$j, $elem->transmision);
                $hoja1->setCellValue('O'.$j, "");
                $hoja1->setCellValue('P'.$j, $elem->precioVenta);
                $hoja1->setCellValue('Q'.$j, $elem->comprobanteCompra);
                $hoja1->setCellValue('R'.$j, "USD");
                $hoja1->setCellValue('S'.$j, "=P$j-O$j");
             
                $hoja1->getStyle('A'.$j.':S'.$j)->applyFromArray($this->estilo_content);
            }

        } elseif ($tipo == 15) {
            // dd($data);
            $fileName = 'reporte_cos'; 
            $hoja1->setTitle("costos cos");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'1. CONTROL DE UNIDADES INGRESADAS '. $anio);
            $j++;
            $hoja1->setCellValue('A'.$j,'TIPO DE SERVICIO');
            $hoja1->setCellValue('B'.$j,'ENERO');
            $hoja1->setCellValue('C'.$j,'FEBRERO');
            $hoja1->setCellValue('D'.$j,'MARZO');
            $hoja1->setCellValue('E'.$j,'ABRIL');
            $hoja1->setCellValue('F'.$j,'MAYO');
            $hoja1->setCellValue('G'.$j,'JUNIO');
            $hoja1->setCellValue('H'.$j,'JULIO');
            $hoja1->setCellValue('I'.$j,'AGOSTO');
            $hoja1->setCellValue('J'.$j,'SETIEMBRE');
            $hoja1->setCellValue('K'.$j,'OCTUBRE');
            $hoja1->setCellValue('L'.$j,'NOVIEMBRE');
            $hoja1->setCellValue('M'.$j,'DICIEMBRE');
             
            $hoja1->getStyle('A'.$j.':M'.$j)->applyFromArray($this->estilo_content);
            $min = $j;
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 5000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 10000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 15000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 20000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 25000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 30000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 35000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 40000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 45000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO 50000');
            $j++;
            $hoja1->setCellValue('A'.$j,'SERVICIO >50000');
            
            // $item = 3;
            // $mes = 12;
            // $numletra = 2;
            // $maxletra = 13;
            $mes=1;
            $numletra = 2; 
            $j = 3;
                
            for ($item=1; $item <= 11; $item++) {
                $array1 = $this->filterArray($data, 'A'.$item);
                // dd($array1);
                $numletra = 2;
                $letra = $this->numeroToletra($numletra);
                $mes = 1;
                foreach ($array1 as $elem) {
                    $mesC = ($mes < 10?'0'.$mes:$mes);
                    $hoja1->setCellValue("$letra$j", $elem->mes == $mesC?$elem->cantidad:'');
                    $numletra++;
                    $letra = $this->numeroToletra($numletra);
                    $mes++;
                }
                $j++;
               
                // $numletra++;
            }
            $hoja1->setCellValue('A'.$j,'TOTAL INGRESO');
            $hoja1->setCellValue('B'.$j,'=SUM(B3:B13)');
            $hoja1->setCellValue('C'.$j,'=SUM(C3:C13)');
            $hoja1->setCellValue('D'.$j,'=SUM(D3:D13)');
            $hoja1->setCellValue('E'.$j,'=SUM(E3:E13)');
            $hoja1->setCellValue('F'.$j,'=SUM(F3:F13)');
            $hoja1->setCellValue('G'.$j,'=SUM(G3:G13)');
            $hoja1->setCellValue('H'.$j,'=SUM(H3:H13)');
            $hoja1->setCellValue('I'.$j,'=SUM(I3:I13)');
            $hoja1->setCellValue('J'.$j,'=SUM(J3:J13)');
            $hoja1->setCellValue('K'.$j,'=SUM(K3:K13)');
            $hoja1->setCellValue('L'.$j,'=SUM(L3:L13)');
            $hoja1->setCellValue('M'.$j,'=SUM(M3:M13)');
            $j+=3; 

            $data2 = $this->getData($fechaI, $fechaF, '151', $anio, $tipoServ);
            $hoja1->setCellValue('A'.$j,'2. INGRESO POR MODELO '. $anio);
            $j++;
            $hoja1->setCellValue('A'.$j,'MODELO');
            $hoja1->setCellValue('B'.$j,'ENERO');
            $hoja1->setCellValue('C'.$j,'FEBRERO');
            $hoja1->setCellValue('D'.$j,'MARZO');
            $hoja1->setCellValue('E'.$j,'ABRIL');
            $hoja1->setCellValue('F'.$j,'MAYO');
            $hoja1->setCellValue('G'.$j,'JUNIO');
            $hoja1->setCellValue('H'.$j,'JULIO');
            $hoja1->setCellValue('I'.$j,'AGOSTO');
            $hoja1->setCellValue('J'.$j,'SETIEMBRE');
            $hoja1->setCellValue('K'.$j,'OCTUBRE');
            $hoja1->setCellValue('L'.$j,'NOVIEMBRE');
            $hoja1->setCellValue('M'.$j,'DICIEMBRE');

            // dd($data2);
            $mes=1;
            $numletra = 1; 
            $min = $j+1;
            $j++;
            $modeloAux = '';
            foreach ($data2 as $itemM) {
                if ($modeloAux != $itemM->modelo) {
                    $array1 = $this->filterArray02($data2, $itemM->modelo);
                    foreach ($array1 as $key => $value) {
                        $hoja1->setCellValue('A'.$j, $array1['criterio']);
                        $array2 =  $array1['data'];
                        foreach ($array2 as $el) {
                            $hoja1->setCellValue('B'.$j, $el->mes == '01'?$el->cantidad: '');
                            $hoja1->setCellValue('C'.$j, $el->mes == '02'?$el->cantidad: '');
                            $hoja1->setCellValue('D'.$j, $el->mes == '03'?$el->cantidad: '');
                            $hoja1->setCellValue('E'.$j, $el->mes == '04'?$el->cantidad: '');
                            $hoja1->setCellValue('F'.$j, $el->mes == '05'?$el->cantidad: '');
                            $hoja1->setCellValue('G'.$j, $el->mes == '06'?$el->cantidad: '');
                            $hoja1->setCellValue('H'.$j, $el->mes == '07'?$el->cantidad: '');
                            $hoja1->setCellValue('I'.$j, $el->mes == '08'?$el->cantidad: '');
                            $hoja1->setCellValue('J'.$j, $el->mes == '09'?$el->cantidad: '');
                            $hoja1->setCellValue('K'.$j, $el->mes == '10'?$el->cantidad: '');
                            $hoja1->setCellValue('L'.$j, $el->mes == '11'?$el->cantidad: '');
                            $hoja1->setCellValue('M'.$j, $el->mes == '12'?$el->cantidad: '');
                
                        }
                        
                    }
                    $j++;
                    $modeloAux = $itemM->modelo;
                }
                
                
            }
            $hoja1->setCellValue('A'.$j,'TOTAL INGRESO');
            $hoja1->setCellValue('B'.$j,'=SUM(B'.$min.':B'.$j.')');
            $hoja1->setCellValue('C'.$j,'=SUM(C'.$min.':C'.$j.')');
            $hoja1->setCellValue('D'.$j,'=SUM(D'.$min.':D'.$j.')');
            $hoja1->setCellValue('E'.$j,'=SUM(E'.$min.':E'.$j.')');
            $hoja1->setCellValue('F'.$j,'=SUM(F'.$min.':F'.$j.')');
            $hoja1->setCellValue('G'.$j,'=SUM(G'.$min.':G'.$j.')');
            $hoja1->setCellValue('H'.$j,'=SUM(H'.$min.':H'.$j.')');
            $hoja1->setCellValue('I'.$j,'=SUM(I'.$min.':I'.$j.')');
            $hoja1->setCellValue('J'.$j,'=SUM(J'.$min.':J'.$j.')');
            $hoja1->setCellValue('K'.$j,'=SUM(K'.$min.':K'.$j.')');
            $hoja1->setCellValue('L'.$j,'=SUM(L'.$min.':L'.$j.')');
            $hoja1->setCellValue('M'.$j,'=SUM(M'.$min.':M'.$j.')');
         

        } elseif ($tipo == 16) {
            // dd($data);
            $fileName = 'mapa_estrategico'; 
            $hoja1->setTitle("mapa_estrategico");
            $j=1;
            $series = [11, 12, 13, 14, 15, 16, 17, 18];
            foreach ($series as $item) {
                $array01 = $this->getDataMapaEstrategico($data, $item);
                
                if ($item == 11) {
                    $titulo = 'NEUMÁTICOS';
                } elseif ($item == 12) {
                    $titulo = 'REPUESTOS MOSTRADOR';
                } elseif ($item == 13) {
                    $titulo = 'BATERÍAS';
                } elseif ($item == 14) {
                    $titulo = 'SERVICIOS DE TALLER';
                } elseif ($item == 15) {
                    $titulo = 'OTROS INGRESOS';
                } elseif ($item == 16) {
                    $titulo = 'VEHICULOS VW Y OTROS';
                } elseif ($item == 17) {
                    $titulo = 'VEHÍCULOS CHEVROLET E ISUZU';
                } elseif ($item == 18) {
                    $titulo = 'POSTVENTA CHEVROLET E ISUZU';
                }

                $hoja1->setCellValue('A'.$j, $titulo);
                $hoja1->mergeCells('A'.$j.':E'.$j);
		
                $j++;
                $hoja1->setCellValue('A'.$j,'MES');
                $hoja1->setCellValue('B'.$j,'UNIDADES FACTURADAS');
                $hoja1->setCellValue('C'.$j,'MONTO FACTURADO (S/)');
                $hoja1->setCellValue('D'.$j,'META');
                $hoja1->setCellValue('E'.$j,'ALCANCE (%)');

                $j++;
                $min = $j;
                $hoja1->setCellValue('A'.$j,'ENERO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'FEBRERO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'MARZO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'ABRIL');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'MAYO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'JUNIO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'JULIO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'AGOSTO');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'SETIEMBRE');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'OCTUBRE');
    
                $j++;
                $hoja1->setCellValue('A'.$j,'NOVIEMBRE');
               
                $j++;
                $hoja1->setCellValue('A'.$j,'DICIEMBRE');
              
                $serieAux = '';
                // $array01 = $this->getDataMapaEstrategico($data, '11');
                $j = $min-1;
                for ($mes = 1; $mes <=12; $mes++) {
                    $j++;
                    $mesC = ($mes < 10?'0'.$mes:$mes);
                    $array02 = $this->getDataMapaMes($array01, $mesC);
                    foreach ($array02 as $el2) {
                        $hoja1->setCellValue('B'.$j, $el2->mes == $mesC?$el2->cantidad: '');
                        $hoja1->setCellValue('C'.$j, $el2->mes == $mesC?$el2->total: '');
                        $hoja1->setCellValue('D'.$j, "");
                        $hoja1->setCellValue('E'.$j, "=C$j/D$j*100");
                    }
                }
    
                $j++;
                $hoja1->setCellValue('A'.$j,'TOTAL');
                $hoja1->setCellValue('B'.$j,'=SUM(B'.($min-1).':B'.$j.')');
                $hoja1->setCellValue('C'.$j,'=SUM(C'.($min-1).':C'.$j.')');
                $hoja1->setCellValue('D'.$j,'=SUM(D'.($min-1).':D'.$j.')');
                $hoja1->setCellValue('E'.$j,'=C'.($min-1).'/D'.$j.'*100');

                $j+=2;
            }

            /*
            $hoja1->setCellValue('A'.$j,'NEUMÁTICOS');
            $j++;
            $hoja1->setCellValue('A'.$j,'MES');
            $hoja1->setCellValue('B'.$j,'UNIDADES FACTURADAS');
            $hoja1->setCellValue('C'.$j,'MONTO FACTURADO');
            $hoja1->setCellValue('D'.$j,'META');
            $hoja1->setCellValue('E'.$j,'ALCANCE (%)');
           
            $j++;
            $min = $j;
            $hoja1->setCellValue('A'.$j,'ENERO');

            $j++;
            $hoja1->setCellValue('A'.$j,'FEBRERO');

            $j++;
            $hoja1->setCellValue('A'.$j,'MARZO');

            $j++;
            $hoja1->setCellValue('A'.$j,'ABRIL');

            $j++;
            $hoja1->setCellValue('A'.$j,'MAYO');

            $j++;
            $hoja1->setCellValue('A'.$j,'JUNIO');

            $j++;
            $hoja1->setCellValue('A'.$j,'JULIO');

            $j++;
            $hoja1->setCellValue('A'.$j,'AGOSTO');

            $j++;
            $hoja1->setCellValue('A'.$j,'SETIEMBRE');

            $j++;
            $hoja1->setCellValue('A'.$j,'OCTUBRE');

            $j++;
            $hoja1->setCellValue('A'.$j,'NOVIEMBRE');
           
            $j++;
            $hoja1->setCellValue('A'.$j,'DICIEMBRE');
          
            $serieAux = '';
            $array01 = $this->getDataMapaEstrategico($data, '11');
            $j = $min-1;
            for ($mes = 1; $mes <=12; $mes++) {
                $j++;
                $mesC = ($mes < 10?'0'.$mes:$mes);
                $array02 = $this->getDataMapaMes($array01, $mesC);
                // dd($array02);
                foreach ($array02 as $el2) {
                    $hoja1->setCellValue('B'.$j, $el2->mes == $mesC?$el2->cantidad: '');
                    $hoja1->setCellValue('C'.$j, $el2->mes == $mesC?$el2->total: '');
                    $hoja1->setCellValue('D'.$j, "");
                    $hoja1->setCellValue('E'.$j, "=C$j/D$j*100");
                    
                    // $mes++;
                }
            }

            $j++;
            $hoja1->setCellValue('A'.$j,'TOTAL');
            $hoja1->setCellValue('B'.$j,'=SUM(B'.($min-1).':B'.$j.')');
            $hoja1->setCellValue('C'.$j,'=SUM(C'.($min-1).':C'.$j.')');
            $hoja1->setCellValue('D'.$j,'=SUM(D'.($min-1).':D'.$j.')');
            $hoja1->setCellValue('E'.$j,'=C'.($min-1).'/D'.$j.'*100');
            */
          
        } elseif ($tipo == 17) {
            $fileName = 'reporte_prospectos'; 
            $hoja1->setTitle("prospectos por asesor");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'DNI');
            $hoja1->setCellValue('B'.$j,'NOMBRE Y APELLIDO');
            $hoja1->setCellValue('C'.$j,'CANTIDAD DE PROSPECTOS');
            $hoja1->setCellValue('D'.$j,'CANTIDAD DE OPORTUNIDADES');
            $hoja1->setCellValue('E'.$j,'TOTAL COSTO OPORTUNIDAD ($)');
            $hoja1->setCellValue('F'.$j,'TOTAL COSTO OPORTUNIDAD (S/)');
            $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->dni);
                $hoja1->setCellValue('B'.$j,$elem->trabajador);
                $hoja1->setCellValue('C'.$j,$elem->cantProspecto);
                $hoja1->setCellValue('D'.$j,$elem->cantOportunidad);
                $hoja1->setCellValue('E'.$j,$elem->totalCostoOportunidadD);
                $hoja1->setCellValue('F'.$j,$elem->totalCostoOportunidadS);
               
                $hoja1->getStyle('A'.$j.':F'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 18) {
            $fileName = 'reporte_oportunidades'; 
            $hoja1->setTitle("oportunidades");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'DNI');
            $hoja1->setCellValue('B'.$j,'NOMBRE Y APELLIDO');
            $hoja1->setCellValue('C'.$j,'PROBABILIDAD');
            $hoja1->setCellValue('D'.$j,'FECHA CIERRE');
            $hoja1->setCellValue('E'.$j,'MONEDA');
            $hoja1->setCellValue('F'.$j,'ESTADO');
            $hoja1->setCellValue('G'.$j,'TOTAL ($)');
            $hoja1->setCellValue('H'.$j,'TOTAL (S/)');
            $hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->dni);
                $hoja1->setCellValue('B'.$j,$elem->trabajador);
                $hoja1->setCellValue('C'.$j,$elem->probabilidad);
                $hoja1->setCellValue('D'.$j,$elem->fechaCierre);
                $hoja1->setCellValue('E'.$j,$elem->moneda);
                $hoja1->setCellValue('F'.$j,$elem->estado);
                $hoja1->setCellValue('G'.$j,$elem->moneda == 'USD'?$elem->monto:'');
                $hoja1->setCellValue('H'.$j,$elem->moneda == 'PEN'?$elem->monto:'');
               
                $hoja1->getStyle('A'.$j.':H'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 19) {
            $fileName = 'reporte_pasos'; 
            $hoja1->setTitle("reporte de pasos");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'FECHA RECEPCION');
            $hoja1->setCellValue('B'.$j,'TIEMPO EN TALLER (DIAS)');
            $hoja1->setCellValue('C'.$j,'ASESOR DE SERVICIO');
            $hoja1->setCellValue('D'.$j,'ESTADO');
            $hoja1->setCellValue('E'.$j,'ORDEN DE TRABAJO');
            $hoja1->setCellValue('F'.$j,'COTIZACIONES');
            $hoja1->setCellValue('G'.$j,'TOTAL PRODUCTOS');
            $hoja1->setCellValue('H'.$j,'TOTAL SERVICIOS');
            $hoja1->setCellValue('I'.$j,'COMPROBANTE');
            $hoja1->setCellValue('J'.$j,'TECNICO');
            $hoja1->setCellValue('K'.$j,'PLACA');
            $hoja1->setCellValue('L'.$j,'MARCA');
            $hoja1->setCellValue('M'.$j,'MODELO');
            $hoja1->setCellValue('N'.$j,'KM');
            $hoja1->setCellValue('O'.$j,'SERVICIO');
            $hoja1->setCellValue('P'.$j,'CLIENTE');
            $hoja1->setCellValue('Q'.$j,'CORREO ELECTRONICO');
            $hoja1->setCellValue('R'.$j,'TELÉFONO');
            $hoja1->setCellValue('S'.$j,'CONTACTABILIDAD');
            $hoja1->setCellValue('T'.$j,'DÍAS TRANSCURRIDOS');
            $hoja1->setCellValue('U'.$j,'P1');
            $hoja1->setCellValue('V'.$j,'P2');
            $hoja1->setCellValue('W'.$j,'P3');
            $hoja1->setCellValue('X'.$j,'P4');
            $hoja1->getStyle('A'.$j.':X'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->fecha);
                $hoja1->setCellValue('B'.$j,$elem->dias>0?$elem->dias:'');
                $hoja1->setCellValue('C'.$j,$elem->asesor);
                $hoja1->setCellValue('D'.$j,(is_null($elem->inicia)?'No Iniciado':(is_null($elem->finaliza)?'En Proceso':'Finalizado')));
                $hoja1->setCellValue('E'.$j,$elem->orden);
                $hoja1->setCellValue('F'.$j,$elem->totalcotizaciones);
                $hoja1->setCellValue('G'.$j,$elem->totalProductos);
                $hoja1->setCellValue('H'.$j,$elem->totalServicios);
                $hoja1->setCellValue('I'.$j,$elem->venta);
                $hoja1->setCellValue('J'.$j,$elem->tecnico);
                $hoja1->setCellValue('K'.$j,$elem->placa);
                $hoja1->setCellValue('L'.$j,$elem->marca);
                $hoja1->setCellValue('M'.$j,$elem->modelo);
                $hoja1->setCellValue('N'.$j,$elem->kilometraje);
                $hoja1->setCellValue('O'.$j,$elem->tipoServicio);
                $hoja1->setCellValue('P'.$j,$elem->cliente);
                $hoja1->setCellValue('Q'.$j,$elem->correoElectronico);
                $hoja1->setCellValue('R'.$j,$elem->telefono);
                $hoja1->setCellValue('S'.$j,$elem->rpta6);
                $hoja1->setCellValue('T'.$j,is_null($elem->finaliza)?$elem->diasTranscurridos:'');
                $hoja1->setCellValue('U'.$j,$elem->rpta1);
                $hoja1->setCellValue('V'.$j,$elem->rpta2);
                $hoja1->setCellValue('W'.$j,$elem->rpta3);
                $hoja1->setCellValue('X'.$j,$elem->rpta4);
            
                $hoja1->getStyle('A'.$j.':X'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 20) {
            // dd($data);
            $fileName = 'reporte_egresos_gastos'; 
            $hoja1->setTitle("reporte de egresos y gastos");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'N° COMPROBANTE');
            $hoja1->setCellValue('B'.$j,'FECHA');
            $hoja1->setCellValue('C'.$j,'MES');
            $hoja1->setCellValue('D'.$j,'TIPO DOCUMENTO');
            $hoja1->setCellValue('E'.$j,'SERIE');
            $hoja1->setCellValue('F'.$j,'NUMERO');
            $hoja1->setCellValue('G'.$j,'RUC');
            $hoja1->setCellValue('H'.$j,'PROVEEDOR');
            $hoja1->setCellValue('I'.$j,'FECHA VENCIMIENTO');
            $hoja1->setCellValue('J'.$j,'IMPORTE');
            $hoja1->setCellValue('K'.$j,'MONEDA');
            $hoja1->setCellValue('L'.$j,'TIPO DE CAMBIO REF.');
            $hoja1->setCellValue('M'.$j,'IMPORTE EN SOLES');
            $hoja1->setCellValue('N'.$j,'UN');
            $hoja1->setCellValue('O'.$j,'GASTO/COSTO');
            $hoja1->setCellValue('P'.$j,'PARTIDA DEL GASTO');
            $hoja1->setCellValue('Q'.$j,'SUSTENTO/MOTIVO');
            $hoja1->setCellValue('R'.$j,'CUENTA CONTABLE');
            $hoja1->setCellValue('S'.$j,'APROBADO POR');
            $hoja1->setCellValue('T'.$j,'CREDITO/CONTADO');
            $hoja1->setCellValue('U'.$j,'ESTADO');
            $hoja1->setCellValue('V'.$j,'FECHA DE PAGO');
            $hoja1->setCellValue('W'.$j,'CANCELACIÓN');
            $hoja1->setCellValue('X'.$j,'MEDIOS DE PAGO');
            $hoja1->setCellValue('Y'.$j,'ANTICIPOS');
            $hoja1->setCellValue('Z'.$j,'OBSERVACIONES');
        
            $hoja1->getStyle('A'.$j.':Z'.$j)->applyFromArray($this->estilo_content);
            foreach ($data as $elem) {
                // dd($elem);
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->comprobante);
                $hoja1->setCellValue('B'.$j,$elem->fecha);
                $hoja1->setCellValue('C'.$j,$elem->mes);
                $hoja1->setCellValue('D'.$j,$elem->tipodocumento);
                $hoja1->setCellValue('E'.$j,$elem->serie);
                $hoja1->setCellValue('F'.$j,$elem->numero);
                $hoja1->setCellValue('G'.$j,$elem->documento);
                $hoja1->setCellValue('H'.$j,$elem->proveedor);
                $hoja1->setCellValue('I'.$j,$elem->fechaVencimiento);
                $hoja1->setCellValue('J'.$j,$elem->total);
                $hoja1->setCellValue('K'.$j,$elem->moneda);
                $hoja1->setCellValue('L'.$j,$elem->tipocambio);
                $hoja1->setCellValue('M'.$j,$elem->importeSoles);
                $hoja1->setCellValue('N'.$j,$elem->unidadNegocio);
                $hoja1->setCellValue('O'.$j,($elem->tipoCuentaEgreso == 'G'?'GASTO':($elem->tipoCuentaEgreso == 'C'?'COSTO':'')));
                $hoja1->setCellValue('P'.$j,$elem->partidaCuenta);
                $hoja1->setCellValue('Q'.$j,$elem->sustento);
                $hoja1->setCellValue('R'.$j,$elem->cuenta);
                $hoja1->setCellValue('S'.$j,$elem->aprobadoPor);
                $hoja1->setCellValue('T'.$j,$elem->credito);
                $hoja1->setCellValue('U'.$j,$elem->vencido);
                $hoja1->setCellValue('V'.$j,$elem->fechaPago);
                $hoja1->setCellValue('W'.$j,$elem->cancelacion);
                $hoja1->setCellValue('X'.$j,$elem->medioPago);
                $hoja1->setCellValue('Y'.$j,$elem->anticipos);
                $hoja1->setCellValue('Z'.$j,$elem->observacion);
             
                $hoja1->getStyle('A'.$j.':Z'.$j)->applyFromArray($this->estilo_content);
            }
        } elseif ($tipo == 21) {

            // dd($data);
            $fileName = 'reporte_ventas_entregas'; 
            $hoja1->setTitle("reporte de ventas");
            $j = 1;
            $hoja1->setCellValue('A'.$j,'DOCUMENTO');
            $hoja1->setCellValue('B'.$j,'CLIENTE');
            $hoja1->setCellValue('C'.$j,'VIN');
            $hoja1->setCellValue('D'.$j,'COSTO');

            $letra = 5;
            $encabezados = $this->getAdicionalesObsequios();
            foreach ($encabezados as $item) {
                $cLetra = $this->numeroToletra($letra);
                $hoja1->setCellValue("$cLetra$j",$item->nombre);
                $letra++;
            }
            $cLetra = $this->numeroToletra($letra);
            $hoja1->setCellValue("$cLetra$j","COSTO TOTAL");
            $hoja1->getStyle('A'.$j.':'.$cLetra.$j)->applyFromArray($this->estilo_content);


            foreach ($data as $elem) {
                $j++;
                $hoja1->setCellValue('A'.$j,$elem->documento);
                $hoja1->setCellValue('B'.$j,$elem->cliente);
                $hoja1->setCellValue('C'.$j,$elem->vin);
                $hoja1->setCellValue('D'.$j,$elem->costo);
               
                $letra = 5;
                $encabezados = $this->getAdicionalesObsequios();
                foreach ($encabezados as $item) {
                    $cLetra = $this->numeroToletra($letra);
                    $hoja1->setCellValue("$cLetra$j","");
                    $letra++;
                }
                $cLetra = $this->numeroToletra($letra-1);
                $cLetra2 = $this->numeroToletra($letra);
                $hoja1->setCellValue("$cLetra2$j","=SUM(D$j:$cLetra$j)");
                $hoja1->getStyle('A'.$j.':'.$cLetra2.$j)->applyFromArray($this->estilo_content);
            }
            
        }

    

        return $fileName;
    }

    function getAdicionalesObsequios () {

        return DB::table('adicionalobsequio as ao')
        ->select('nombre')
        ->orderBy('tipo')
        ->orderBy('nombre')
        ->get();
    }
    function filterArray ($array, $criterio) {
        $datos = [];
        foreach($array as $elem) {
            if ($elem->tipo == $criterio) {
                $datos[] = $elem;
            }
        }

        return $datos;
    }

    function getDataMapaEstrategico ($array, $serie) {
        $datos = [];
        foreach($array as $elem) {
            if ($elem->serie == $serie) {
                $datos[] = $elem;
            }
        }

        return $datos;
    }

    function getDataMapaMes ($array, $mes) {
        $datos = [];
        foreach($array as $elem) {
            if ($elem->mes == $mes) {
                $datos[] = $elem;
            }
        }

        return $datos;
    }

    function filterArray02 ($array, $criterio) {
        $datos = [];
        foreach($array as $elem) {
            if ($elem->modelo == $criterio) {
                $datos[] = $elem;
            }
        }


        return ['criterio'=> $criterio, 'data' => $datos];
    }

    function letraTonumero($letra) {
        // Convertir a mayúsculas para simplificar
        $letra = strtoupper($letra);
        // Obtener el valor ASCII de la letra y restar el valor de 'A' (65) más 1
        // para obtener la posición en el abecedario
        return ord($letra) - ord('A') + 1;
    }

    function numeroToletra($numero) {
        // Convertir el número a la posición ASCII de la letra ('A' = 65)
        // sumando 64 para obtener la posición correcta en el abecedario
        $ascii = $numero + 64;
        // Convertir el valor ASCII a letra
        return chr($ascii);
    }
    

    public function getData($fechaI, $fechaF, $tipo, $anio, $tipoServ) {
        if ($tipo == 1) {
            $data = DB::select("CALL reporteDetalladoVentas(?,?)",[$fechaI, $fechaF]);
        } elseif($tipo == 2) {
            $data = DB::select("CALL reporteComprobantePLE(?,?)",[$fechaI, $fechaF]);
        } elseif($tipo == 3) {
            $data = DB::select("CALL reporteComprobantesComprasPLE(?,?)", [$fechaI, $fechaF]);
        } elseif($tipo == 4) {
            $data = DB::select("CALL reporteInventarioValorizado(?)",[$fechaF]);
        } elseif($tipo == 5) {
            $data = DB::select("CALL reporteDetalladoCompras(?,?)",[$fechaI, $fechaF]);
        } elseif ($tipo == 6) {
            $data = DB::table('compra as c')
                ->join('detallecompra as dt', 'dt.idcompra', '=', 'c.id')
                ->join('producto as prod', 'prod.id','=','dt.idProducto')
                ->where(function($qq) {
                    $qq->where(DB::Raw("TIMESTAMPDIFF(MONTH, dt.created_at, CURRENT_TIMESTAMP)"), '<=', 3)
                        ->orWhereBetween(DB::Raw("TIMESTAMPDIFF(MONTH, dt.created_at, CURRENT_TIMESTAMP)"), [4, 5])
                        ->orWhere(DB::Raw("TIMESTAMPDIFF(MONTH, dt.created_at, CURRENT_TIMESTAMP)"), '>=', 6);
                })
                ->whereNull('c.deleted_at')
                ->select('dt.cantidad', 'dt.descripcion', 'dt.preciocompra', 'dt.precioventa','c.tipoCambio', 'c.tipoMoneda',
                DB::Raw("(CASE prod.tipoProducto WHEN 'A'  THEN 'Accesorio/Repuesto' WHEN 'LL' THEN 'Neumáticos' WHEN 'I'  THEN 'Insumos' WHEN 'B'  THEN 'Baterías' ELSE 'MUELLES' END) as tipoProducto"),
                DB::Raw("(CASE WHEN TIMESTAMPDIFF(MONTH, dt.created_at, CURRENT_TIMESTAMP) <= 3 THEN 'A3'
                        WHEN TIMESTAMPDIFF(MONTH, dt.created_at, CURRENT_TIMESTAMP) >= 6 THEN 'A6'
                        ELSE 'A4' END) as tipo"), 'dt.created_at')
                ->orderBy('tipo')
                ->get();
        } elseif ($tipo == 7) {
            $data = DB::table('guia as g')
                ->join('detalleguia as dg','dg.idGuia','=', 'g.id')
                ->leftJoin('tipodocumento as td','g.idTipoGuia','=','td.id')
                ->leftJoin('producto as pr','pr.id','=','dg.idProducto')
                ->where('g.idTipoGuia', '4')
                ->whereNull('dg.deleted_at')
                ->whereNotNull('dg.idProducto')
                ->where(DB::Raw("UPPER(g.observacion)"), 'LIKE', '%CONSUMO INTERNO%');
         
            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(dg.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(dg.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            } 

            $data = $data->select('dg.descripcion','dg.cantidad', 
                    DB::Raw("DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha"),  
                    DB::Raw("CONCAT(td.abreviatura,LPAD(g.serie,3,'0')) as serie"), 
                    DB::Raw("LPAD(g.numero,8,'0') as numero"), 'g.tipoMoneda', 'g.tipoCambio', 
                    DB::Raw("(CASE pr.tipoProducto 
                    WHEN 'A'  THEN 'Accesorio/Repuesto' 
                    WHEN 'LL' THEN 'Neumáticos' 
                    WHEN 'I'  THEN 'Insumos' 
                    WHEN 'B'  THEN 'Baterías' 
                    ELSE 'MUELLES' END) as tipoProducto"), 'dg.precioventa', 'g.observacion')
                ->orderBy('dg.created_at')
                ->get();
        } elseif ($tipo == 8) {
            $data = DB::table('ordentrabajo as ot')
                    ->join('cita as ct','ct.id','=','ot.idCita')
                    ->whereNull('ot.deleted_at');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }
         
            $data = $data->select('ct.modelo', DB::Raw("DATE_FORMAT(ot.fecha, '%d/%m/%Y') as fecha"),
                DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden"),
                'ct.tipoServicio', DB::Raw("(CASE ct.tipoServicio 
                    WHEN 'MP' THEN 'Mantenimiento Preventivo'
                    WHEN 'MC' THEN 'Mantenimiento Correctivo'
                    WHEN 'L' THEN 'Lavado'
                    WHEN 'PB' THEN 'Programa de Bienvenida'
                    WHEN 'IA' THEN 'Instalación de accs'
                    WHEN 'PD' THEN 'PDI'
                    WHEN 'G' THEN 'Garantía'
                    WHEN 'C' THEN 'Campaña'
                    WHEN 'PP' THEN 'Planchado y Pintura'
                    WHEN 'IS' THEN 'Inspección Seminuevo'
                    WHEN 'TR' THEN 'Trabajo Repetido'
                    WHEN 'S' THEN 'Siniestro'
                    ELSE '' END) as tipoServicioText"),
                    DB::Raw("IFNULL((SELECT SUM(dtc.subTotal) as totalServicios FROM cotizacion as ct 
                    JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
                    JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
                    WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'S' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0) as totalServicios"),
                    DB::Raw("IFNULL((SELECT SUM(dtc.subTotal) as totalProductos FROM cotizacion as ct 
                    JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
                    JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
                    WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'P' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0) as totalProductos"),
                    DB::Raw("IFNULL((SELECT SUM(TIMESTAMPDIFF(SECOND,td.inicio,td.fin))/60 as tiempo FROM tiempodetalle as td WHERE td.idOrdenTrabajo = ot.id AND (td.estado = 'T' OR (td.inicio IS NOT NULL AND td.fin IS NOT NULL))),'') as tiempo")
            )->orderBy('ct.tipoServicio','ASC')->orderBy('ot.created_at','ASC')->get();

        } elseif ($tipo == 9) {
            $data = DB::table('ordentrabajo as ot')
                    ->join('cita as c', 'c.id', '=', 'ot.idCita')
                    ->join('persona as cli', 'cli.id', '=', 'ot.idCliente')
                    ->leftjoin('trabajador as trab','trab.id','=','ot.idAsignado')
                    ->whereNull('ot.deleted_at')
                    ->where('ot.situacion', '!=', 'A');
            
            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
            if ($tipoServ != '' && !is_null($tipoServ)) {
                $data = $data->where('c.tipoServicio', $tipoServ);
            }

            $data =  $data->select(DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,
                    '-', LPAD(ot.numero,8,'0')) as orden"), 'c.modelo', 'c.tipoServicio as codTipoServicio',  DB::Raw("(CASE c.tipoServicio 
                    WHEN 'MP' THEN 'Mantenimiento Preventivo'
                    WHEN 'MC' THEN 'Mantenimiento Correctivo'
                    WHEN 'L' THEN 'Lavado'
                    WHEN 'PB' THEN 'Programa de Bienvenida'
                    WHEN 'IA' THEN 'Instalación de accs'
                    WHEN 'PD' THEN 'PDI'
                    WHEN 'G' THEN 'Garantía'
                    WHEN 'C' THEN 'Campaña'
                    WHEN 'PP' THEN 'Planchado y Pintura'
                    WHEN 'IS' THEN 'Inspección Seminuevo'
                    WHEN 'TR' THEN 'Trabajo repetido'
                    WHEN 'S' THEN 'Siniestro'
                    ELSE '' END) as tipoServicio"), 'ot.total',
                    DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"), DB::Raw("(CASE WHEN cli.tipoDocumento = 'PN' THEN CONCAT(cli.nombres,' ', cli.apellidos) ELSE cli.razonSocial END) as cliente"), 'cli.correoElectronico', 'cli.telefono', 'trab.id as idtecnico', 
                    DB::Raw("CONCAT(trab.nombres,' ',trab.apellidos) as tecnico"),'c.anio','c.vin'
                )->orderBy('ot.fecha')->get();
        } elseif ($tipo == 10) {
            $data = DB::table('compraauto as ca')
                    ->join('persona as prov','prov.id','=','ca.idProveedor')
                    ->join('trabajador as tr','tr.id','=','ca.idPersonal')
                    ->whereNull('ca.deleted_at')
                    ->where('ca.flete','>',0);

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ca.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ca.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
          
            $data = $data->select(DB::Raw("(CASE ca.tipoDocumento WHEN 'F' THEN 'FACTURA' WHEN 'B' THEN 'BOLETA' ELSE '' END) as tipoDocumento"), 'prov.documento as docProveedor', 'ca.documento', DB::Raw("DATE_FORMAT(ca.fecha,'%d/%m/%Y') as fecha"), 'ca.flete',
            DB::Raw("(CASE ca.tipoMoneda WHEN 'D' THEN 'Dólares' ELSE 'Soles' END) as moneda"), 'ca.tipoCambio', DB::Raw("DATE_FORMAT(ca.fechaVencimiento,'%d/%m/%Y') as fechaVencimiento"),
            'ca.total', 'ca.subtotal', 'ca.igv', DB::Raw("CONCAT(tr.apellidos, ' ', tr.nombres) as trabajador"), DB::Raw("(CASE WHEN prov.tipoDocumento = 'PN' THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as proveedor"),  DB::Raw("DATE_FORMAT(ca.created_at,'%d/%m/%Y %h:%i %p') as fechaR")
            )->get();

        } elseif ($tipo == 11) {
            $data = DB::table('venta as v')
                    ->join('detalleventa as dv', 'dv.idVenta', '=', 'v.id')
                    ->join('auto as a','a.id','=','dv.idAuto')
                    ->leftJoin('marcaauto as ma', 'ma.id', '=', 'a.marcaId')
                    ->leftJoin('modeloauto as mda', 'mda.id', '=', 'a.modeloId')
                    ->join('persona as cli','cli.id','=','v.idCliente')
                    ->join('trabajador as tr','tr.id','=','v.idPersonal')
                    ->whereNull('v.deleted_at');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
    
            $data = $data->select(DB::Raw("DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha"), 'dv.cantidad', 'dv.subTotal', 'dv.precio', 'a.linea', 'dv.descripcion', DB::Raw("CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0')) as serie"), DB::Raw("LPAD(v.numero, 8,'0') as numero"), DB::Raw("(CASE WHEN v.tipoComprobante = 'F' THEN 'FACTURA' ELSE 'BOLETA' END) as tipoDocumento"),
            'v.tipoCambio', DB::Raw("(CASE WHEN v.tipoMoneda = 'USD' THEN 'Dólares' ELSE 'Soles' END) as moneda"), DB::Raw("(CASE WHEN cli.tipoDocumento = 'PN' THEN CONCAT(cli.apellidos,' ', cli.nombres) ELSE cli.razonSocial END) as cliente"), DB::Raw("DATE_FORMAT(v.created_at,'%d/%m/%Y %h:%i %p') as fechaR"), DB::Raw("CONCAT(tr.apellidos, ' ', tr.nombres) as trabajador"), 'ma.nombre as marca', 'mda.nombre as modelo', 'a.version', 'a.transmision')->orderBy('v.created_at')->get();
        } elseif ($tipo == 12) {
            $data = DB::table('cuenta as c')
                ->join('trabajador as tr', 'tr.id','=', 'c.idPersonal')
                ->leftjoin('persona as cl', 'cl.id' , '=', 'c.idCliente')
                ->leftjoin('persona as prov', 'prov.id' , '=', 'c.idProveedor')
                ->whereNull('c.deleted_at');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
        
            $data = $data->select('c.id','c.tipocuenta', 
            DB::Raw("(CASE c.tipodocumento WHEN 'F' THEN 'FACTURA' WHEN 'B' THEN 'BOLETA'
            WHEN 'RXH' THEN 'RRxHH' WHEN 'NC' THEN 'NOTA DE CREDITO' WHEN 'ND' THEN 'NOTA DE DEBITO'
            ELSE 'OTROS' END) as tipodocumento"), DB::Raw("(CASE WHEN cl.tipoDocumento = 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),  DB::Raw("(CASE WHEN prov.tipoDocumento = 'PN' THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as proveedor"), 'c.serie', 'c.numero', DB::Raw("DATE_FORMAT(c.fechavencimiento, '%d/%m/%Y') as fechavencimiento"), DB::Raw("(CASE c.tipo WHEN 'G' THEN 'GASTO' WHEN 'C' THEN 'COSTO' WHEN 'A' THEN 'ACTIVO' ELSE '' END) as tipo"), DB::Raw("(CASE c.tipogasto WHEN 'F' THEN 'FIJOS' WHEN 'V' THEN 'VARIABLES' WHEN 'FIN' THEN 'FINANCIEROS' ELSE '' END) as tipogasto"),
            'c.unidad', 'c.partida', 'c.importe', 'c.sustento', 'c.tipocambio', DB::Raw("(CASE 
            c.operacion WHEN 'D' THEN 'CREDITO' WHEN 'C' THEN 'CONTADO' ELSE '' END) as operacion"),
            DB::Raw("(CASE c.estado WHEN 'P' THEN 'PENDIENTE' ELSE 'CANCELADO' END) as estado"),
            'c.saldo', 'c.importeSoles','c.moneda', 'prov.documento as docProveedor', 'cl.documento as docCliente',
            DB::Raw("DATE_FORMAT(c.created_at,'%d/%m/%Y %h:%i %p') as fechaR"), 
            DB::Raw("CONCAT(tr.apellidos, ' ', tr.nombres) as trabajador")
            )->orderBy('c.created_at')->get();
        } elseif ($tipo == 13) {
            $data = DB::table('ordentrabajo as ot')
                ->join('persona as cl','cl.id','=','ot.idCliente')
                ->join('trabajador as tr', 'tr.id','=', 'ot.idPersonal')
                ->whereNull('ot.deleted_at');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select(DB::Raw("DATE_FORMAT(ot.fecha, '%d/%m/%Y') as fecha"), 
                DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden"),
                DB::Raw("(CASE WHEN cl.tipoDocumento = 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"), 'cl.documento', 'ot.placa', 'ot.puntuacionEncuesta', DB::Raw("IFNULL((SELECT (CASE re.estadoContactabilidad WHEN 'C' THEN 'Contestó' WHEN 'NC' THEN 'No Contestó' WHEN 'A' THEN 'Apagado' ELSE '' END) as estadoContactabilidad FROM rptapreguntasencuesta as re WHERE re.idPregunta = (SELECT pe.id FROM preguntasencuesta pe WHERE pe.nombre = 'Estado de Contactabilidad' LIMIT 1) AND re.idOrden = ot.id),'') as estadoContactabilidad"),
                DB::Raw("CONCAT(tr.apellidos, ' ', tr.nombres) as trabajador"),
                DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i %p') as fechaR")
            )->orderBy('ot.created_at')->get();
                
        } elseif ($tipo == 14) {
            $data = DB::table('venta as v')
                ->join('detalleventa as dv', 'dv.idVenta','=','v.id')
                ->join('loteauto as la','la.id','=','dv.idLoteAuto')
                ->join('auto as a', 'a.id','=','la.idAuto')
                ->join('marcaauto as ma', 'ma.id', '=', 'a.marcaId')
                ->join('modeloauto as moda', 'moda.id', '=', 'a.modeloId')
                ->join('persona as cli','cli.id','=','v.idCliente')
                ->join('compra as c', 'c.id','=', 'la.idCompra');


            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }
            $data = $data->select('cli.documento', DB::Raw("(CASE WHEN cli.tipoDocumento = 'PN' THEN CONCAT(cli.nombres, ' ', cli.apellidos) ELSE cli.razonSocial END) as cliente"),
            'cli.direccion', 'cli.correoElectronico','cli.telefono', DB::Raw("CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0'), '-', LPAD(v.numero, 8, '0')) as comprobante"), 'v.asesorAuto', DB::Raw("(CASE a.linea WHEN 'L' THEN 'Livianos' WHEN 'P' THEN 'Pesados' ELSE '' END) division"), 'ma.nombre as marca', 'moda.nombre as modelo', 'a.version', 'a.transmision', 'la.precioDolares as precioVenta', 'c.documento as comprobanteCompra',  DB::Raw("DATE_FORMAT(v.fecha, '%Y') as anio"), DB::Raw("DATE_FORMAT(v.fecha, '%m') as mes"), DB::Raw("DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha"), 'la.vin')->orderBy('v.created_at')->get();
                
        } elseif ($tipo == 15) {
            $data = DB::table('cita as c')
                ->join('ordentrabajo as ot','ot.idCita','=', 'c.id')
                ->whereNull('ot.deleted_at')
                ->where(DB::Raw("DATE_FORMAT(ot.fecha, '%Y')"), $anio)
                ->select(DB::Raw("(CASE
                    WHEN c.kilometraje  = 5000 THEN 'A1'
                    WHEN c.kilometraje  = 10000 THEN 'A2'
                    WHEN c.kilometraje  = 15000 THEN 'A3'
                    WHEN c.kilometraje  = 20000 THEN 'A4'
                    WHEN c.kilometraje  = 25000 THEN 'A5'
                    WHEN c.kilometraje  = 30000 THEN 'A6'
                    WHEN c.kilometraje  = 35000 THEN 'A7'
                    WHEN c.kilometraje  = 40000 THEN 'A8'
                    WHEN c.kilometraje  = 45000 THEN 'A9'
                    WHEN c.kilometraje  = 50000 THEN 'A10'
                    WHEN c.kilometraje  > 50000 THEN 'A11'
                    ELSE '' END
                ) as tipo"), DB::Raw("DATE_FORMAT(ot.fecha, '%m') as mes"), DB::Raw("COUNT(ot.fecha) as cantidad"))
                ->groupBy('tipo', 'mes')
                ->having('tipo', '!=', '')
                ->orderBy('tipo','ASC')
                ->get();
        } elseif ($tipo == 151) {
            $data = DB::table('cita as c')
                ->join('marcaauto as ma','ma.id','=','c.idMarcaAuto')
                ->join('ordentrabajo as ot','ot.idCita','=', 'c.id')
                ->whereNull('ot.deleted_at')
                ->where(DB::Raw("DATE_FORMAT(ot.fecha, '%Y')"), $anio)
                ->select(DB::Raw("CONCAT(ma.nombre, ' ', c.modelo) as modelo"), 
                    DB::Raw("DATE_FORMAT(ot.fecha, '%m') as mes"), 
                    DB::Raw("COUNT(ot.fecha) as cantidad"))
                ->groupBy('modelo', 'mes')
                ->orderBy('modelo','ASC')
                ->get();
        } elseif ($tipo == 16) {
            $data = DB::table('venta as v')
                ->where('v.situacion', '!=', 'A')
                ->whereNull('v.deleted_at')
                ->where(DB::Raw("DATE_FORMAT(v.fecha, '%Y')"), $anio)
                ->whereIn('v.tipoComprobante', ['B', 'F'])
                ->select(DB::Raw("DATE_FORMAT(v.fecha, '%m') as mes"),
                    'v.serie', DB::Raw("COUNT(v.fecha) as cantidad"),
                    DB::Raw("SUM(v.total*v.tipoCambio) as total")
                )
                ->groupBy('v.serie', 'mes')
                ->orderBy('v.serie','ASC')
                ->get();
                
        } elseif ($tipo == 17) {
            $data = DB::table('prospecto as pr')
                    ->join('trabajador as tr', 'tr.id', '=', 'pr.idAsesorRegistra')
                    ->whereNull('pr.deleted_at');

    
            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(pr.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(pr.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }
           
            $data = $data->select('tr.dni', DB::Raw("CONCAT(tr.nombres,' ', tr.apellidos) as  trabajador"),
                    DB::Raw("COUNT(tr.id) as cantProspecto"), 
                    DB::Raw("(SELECT COUNT(op.id) FROM oportunidad as op WHERE op.idAsesorRegistra = pr.idAsesorRegistra) as cantOportunidad"),
                    DB::Raw("(SELECT SUM(op.monto) FROM oportunidad as op WHERE op.idAsesorRegistra = pr.idAsesorRegistra AND op.moneda = 'USD') as totalCostoOportunidadD"),
                    DB::Raw("(SELECT SUM(op.monto) FROM oportunidad as op WHERE op.idAsesorRegistra = pr.idAsesorRegistra AND op.moneda = 'PEN') as totalCostoOportunidadS"))
                    ->orderBy('trabajador')
                    ->groupBy('tr.dni','trabajador', 'totalCostoOportunidadD', 'totalCostoOportunidadS')
                    ->get();
        } elseif ($tipo == 18) {
            $data = DB::table('oportunidad as op')
                ->join('trabajador as tr', 'tr.id', '=', 'op.idAsesorRegistra')
                ->whereNull('op.deleted_at');


            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(op.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(op.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select(DB::Raw("(CASE op.certeza WHEN 'P' THEN '10%' WHEN 'M' THEN '50&' ELSE '90%' END) as probabilidad"), DB::Raw("DATE_FORMAT(op.fechaCierre, '%d/%m/%Y') as fechaCierre"), DB::Raw("(CASE WHEN TIMESTAMPDIFF(DAY, op.fechaCierre, CURRENT_DATE) > 0 THEN 'VENCIDA' ELSE 'VIGENTE' END) as estado"), 'op.moneda', 'op.monto', 'tr.dni',
            DB::Raw("CONCAT(tr.nombres,' ', tr.apellidos) as  trabajador"))
            ->orderBy('trabajador')
            ->get();
        } elseif ($tipo == 19) {
            $data = DB::table('ordentrabajo as ot')
                ->join('cita as c', 'c.id','=','ot.idCita')
                ->join('trabajador as asig', 'asig.id','=','ot.idAsignado')
                ->join('trabajador as tr', 'tr.id','=','ot.idPersonal')
                ->join('marcaauto as ma', 'ma.id','=','c.idMarcaAuto')
                ->join('persona as cl', 'cl.id','=','c.idcliente');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select('ot.inicia','ot.finaliza', DB::Raw("DATE_FORMAT(ot.fecha, '%d-%m-%Y') as fecha"), 
            DB::Raw("CONCAT(tr.nombres,' ', tr.apellidos) as asesor"), 
            DB::Raw("CONCAT(asig.nombres,' ', asig.apellidos) as tecnico"), 
            DB::Raw("TIMESTAMPDIFF(DAY, ot.inicia, ot.finaliza) as dias"), 
            DB::Raw("TIMESTAMPDIFF(DAY, ot.fecha, CURRENT_DATE) as diasTranscurridos"), 
            DB::Raw("DATE_FORMAT(ot.finaliza, '%d-%m-%Y') as entrega"), 'ot.total as totalcotizaciones', DB::Raw("CONCAT('OD', LPAD(ot.serie, 2, '0'),'-', LPAD(ot.numero, 8,'0')) as orden"), 'ot.placa', 'c.modelo',
            'ma.nombre as marca', 'c.kilometraje',
            DB::Raw("IFNULL((SELECT SUM(dtc.subTotal) as totalServicios FROM cotizacion as ct 
            JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
            JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
            WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'S' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0) as totalServicios"),
            DB::Raw("IFNULL((SELECT SUM(dtc.subTotal) as totalProductos FROM cotizacion as ct 
            JOIN detallecotizacion as dtc ON dtc.idCotizacion = ct.id 
            JOIN detalleordentrabajo as dot ON dot.idCotizacion = ct.id
            WHERE dot.idOrdenTrabajo = ot.id AND dtc.tipoDetalle = 'P' AND dtc.deleted_at IS NULL AND ct.deleted_at IS NULL AND ct.situacion != 'A'), 0) as totalProductos"), 
            DB::Raw("(SELECT CONCAT(v.tipoComprobante,LPAD(v.serie,3,'0'),'-',LPAD(v.numero,8,'0')) FROM venta as v WHERE v.id = (SELECT DISTINCT pd.idVenta FROM pagodetalle as pd WHERE pd.idOrden = ot.id LIMIT 1)) as venta"), DB::Raw("(CASE c.tipoServicio 
            WHEN 'MP' THEN 'Mantenimiento Preventivo'
            WHEN 'MC' THEN 'Mantenimiento Correctivo'
            WHEN 'L' THEN 'Lavado'
            WHEN 'PB' THEN 'Programa de Bienvenida'
            WHEN 'IA' THEN 'Instalación de accs'
            WHEN 'PD' THEN 'PDI'
            WHEN 'G' THEN 'Garantía'
            WHEN 'C' THEN 'Campaña'
            WHEN 'PP' THEN 'Planchado y Pintura'
            WHEN 'IS' THEN 'Inspección Seminuevo'
            WHEN 'TR' THEN 'Trabajo repetido'
            WHEN 'S' THEN 'Siniestro'
            ELSE '' END) as tipoServicio"), DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"), 'cl.correoElectronico', 'cl.telefono', DB::Raw("(SELECT rpe.puntuacion FROM preguntasencuesta as pe JOIN rptapreguntasencuesta as rpe ON rpe.idPregunta = pe.id WHERE pe.id= '1' AND rpe.idOrden = ot.id) as rpta1"), DB::Raw("(SELECT rpe.puntuacion FROM preguntasencuesta as pe JOIN rptapreguntasencuesta as rpe ON rpe.idPregunta = pe.id WHERE pe.id= '2' AND rpe.idOrden = ot.id) as rpta2"),  DB::Raw("(SELECT rpe.puntuacion FROM preguntasencuesta as pe JOIN rptapreguntasencuesta as rpe ON rpe.idPregunta = pe.id WHERE pe.id= '3' AND rpe.idOrden = ot.id) as rpta3"), DB::Raw("(SELECT rpe.puntuacion FROM preguntasencuesta as pe JOIN rptapreguntasencuesta as rpe ON rpe.idPregunta = pe.id WHERE pe.id= '4' AND rpe.idOrden = ot.id) as rpta4"), DB::Raw("IFNULL((SELECT (CASE rpe.estadoContactabilidad WHEN 'C' THEN 'Contestó' WHEN 'NC' THEN 'No Contestó' WHEN 'A' THEN 'Apagado' ELSE '' END) FROM preguntasencuesta as pe JOIN rptapreguntasencuesta as rpe ON rpe.idPregunta = pe.id WHERE pe.id= '4' AND rpe.idOrden = ot.id),'') as rpta6"))
            ->orderBy('ot.fecha')
            ->get();
        } elseif ($tipo == 20) {
            $quer1 = DB::table('reciboscuenta as rc')
                ->join('persona as prov', 'prov.id', '=', 'rc.idProveedor')
                ->join('cuentacontable as cc', 'cc.id', '=','rc.idCuentaContable')
                ->join('trabajador as tr', 'tr.id', '=','rc.idPersonaAprueba')
                ->where('rc.tipoOperacion', 'E')
                ->where('rc.tipoEgreso','O')
                ->whereIn('rc.tipoCuentaEgreso',['G','C']);

                
            if ($fechaI != '') {
                $quer1 = $quer1->where(DB::Raw("DATE_FORMAT(rc.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $quer1 = $quer1->where(DB::Raw("DATE_FORMAT(rc.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $quer1 = $quer1->select(
                DB::Raw("(CASE rc.tipodocumento WHEN 'F' THEN 'FACTURA' WHEN 'B' THEN 'BOLETA'
                WHEN 'RXH' THEN 'RRxHH' WHEN 'NC' THEN 'NOTA DE CREDITO' WHEN 'ND' THEN 'NOTA DE DEBITO'
                ELSE 'OTROS' END) as tipodocumento"), 'rc.serie', 'rc.numero', 
                DB::Raw("DATE_FORMAT(rc.created_at, '%m') as mes"),
                DB::Raw("CONCAT(rc.serie,'-', rc.numero) as comprobante"), DB::Raw("DATE_FORMAT(rc.created_at,'%d/%m/%Y') as fecha"), 
                'prov.documento', DB::Raw("(CASE WHEN prov.tipoDocumento = 'PN' THEN CONCAT(prov.apellidos, ' ', prov.nombres) ELSE prov.razonSocial END) as proveedor"),
                DB::Raw("'-' as fechaVencimiento"), 'rc.total', DB::Raw("(CASE rc.moneda WHEN 'PEN' THEN 'Soles' ELSE 'Dólares' END) as moneda"), 'rc.tipocambio', 'cc.codigo as cuenta',
                DB::Raw("rc.tipocambio*rc.total as importeSoles"), 'rc.unidadNegocio', 'rc.tipoCuentaEgreso', 'rc.partidaCuenta', 'rc.sustento', DB::Raw("CONCAT(tr.nombres,' ', tr.apellidos) as aprobadoPor"),
                DB::Raw("'-' as credito"), 'rc.medioPago', DB::Raw("'' as anticipos"),
                'rc.descripcion as observacion', DB::Raw("'' as fechaPago"),  DB::Raw("'' as cancelacion"),  DB::Raw("'' as vencido"), 'rc.created_at');

            #PARA COMPRAS NORMALES
            $quer2 = DB::table('compra as c')
                ->join('persona as prov', 'prov.id', '=', 'c.idProveedor');

                
            if ($fechaI != '') {
                $quer2 = $quer2->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $quer2 = $quer2->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $quer2 = $quer2->select(DB::Raw("(CASE c.tipodocumento WHEN 'F' THEN 'FACTURA' WHEN 'B' THEN 'BOLETA' ELSE 'OTROS' END) as tipodocumento"), DB::Raw("SUBSTRING_INDEX(c.documento,'-',1) as serie"), DB::Raw("SUBSTRING_INDEX(c.documento,'-',-1) as numero"), DB::Raw("DATE_FORMAT(c.fecha, '%m') as mes"), 'c.documento as comprobante', DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha"), 
                'prov.documento', DB::Raw("(CASE WHEN prov.tipoDocumento = 'PN' THEN CONCAT(prov.apellidos, ' ', prov.nombres) ELSE prov.razonSocial END) as proveedor"),
                DB::Raw("DATE_FORMAT(c.fechaVencimiento, '%d/%m/%Y') as fechaVencimiento"), 'c.total', DB::Raw("(CASE c.tipoMoneda WHEN 'S' THEN 'Soles' ELSE 'Dólares' END) as moneda"), 'c.tipoCambio as tipocambio', DB::Raw("'' as cuenta"),
                DB::Raw("c.tipoCambio*c.total as importeSoles"), DB::Raw("'' as unidadNegocio"), DB::Raw("'' as tipoCuentaEgreso"), 
                DB::Raw("'' as partidaCuenta"), DB::Raw("'' as sustento"), DB::Raw("'' as aprobadoPor"),
                DB::Raw("(CASE WHEN c.diasCredito > 0 THEN 'CRÉDITO' ELSE 'CONTADO' END) as credito"), DB::Raw("'' as medioPago"),
                DB::Raw("(SELECT (cta.importe - cta.saldo) as anticipo FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento ORDER BY c.created_at DESC LIMIT 1) as anticipos"),
                DB::Raw("'' as observacion"), 
                DB::Raw("(SELECT (SELECT DATE_FORMAT(rc.created_at, '%d/%m/%Y') as fecha FROM reciboscuenta rc WHERE rc.idCuenta = cta.id ORDER BY rc.created_at DESC LIMIT 1) as fechaPago FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento AND cta.saldo = 0) as fechaPago"),  
                DB::Raw("(SELECT (CASE cta.saldo WHEN 0 THEN 'CANCELADO' ELSE 'PENDIENTE' END) as cancelacion FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento ORDER BY cta.created_at DESC LIMIT 1) as cancelacion"), DB::Raw("(CASE WHEN TIMESTAMPDIFF(DAY, c.fechaVencimiento, CURRENT_DATE) > 0 THEN 'VENCIDO' ELSE 'POR VENCER' END) as vencido"), 'c.created_at');
       
            #echo $quer2->toSql(); exit;
            
            #PARA COMPRAS DE AUTOS
            $data = DB::table('compraauto as c')
                ->join('persona as prov', 'prov.id', '=', 'c.idProveedor');

                
            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select(DB::Raw("(CASE c.tipodocumento WHEN 'F' THEN 'FACTURA' WHEN 'B' THEN 'BOLETA' ELSE 'OTROS' END) as tipodocumento"), DB::Raw("SUBSTRING_INDEX(c.documento,'-',1) as serie"), DB::Raw("SUBSTRING_INDEX(c.documento,'-',-1) as numero"), 
            DB::Raw("DATE_FORMAT(c.fecha, '%m') as mes"), 'c.documento as comprobante', DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha"), 
                'prov.documento', 
                DB::Raw("(CASE WHEN prov.tipoDocumento = 'PN' THEN CONCAT(prov.apellidos, ' ', prov.nombres) ELSE prov.razonSocial END) as proveedor"),
                DB::Raw("DATE_FORMAT(c.fechaVencimiento, '%d/%m/%Y') as fechaVencimiento"), 'c.total', DB::Raw("(CASE c.tipoMoneda WHEN 'S' THEN 'Soles' ELSE 'Dólares' END) as moneda"), 'c.tipoCambio as tipocambio', DB::Raw("'' as cuenta"),
                DB::Raw("c.tipoCambio*c.total as importeSoles"), DB::Raw("'' as unidadNegocio"), DB::Raw("'' as tipoCuentaEgreso"), 
                DB::Raw("'' as partidaCuenta"), DB::Raw("'' as sustento"), DB::Raw("'' as aprobadoPor"),
                DB::Raw("(CASE WHEN c.diasCredito > 0 THEN 'CRÉDITO' ELSE 'CONTADO' END) as credito"), DB::Raw("'' as medioPago"),
                DB::Raw("(SELECT (cta.importe - cta.saldo) as anticipo FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento ORDER BY c.created_at DESC LIMIT 1) as anticipos"),
                DB::Raw("'' as observacion"), DB::Raw("(SELECT (SELECT DATE_FORMAT(rc.created_at, '%d/%m/%Y') as fecha FROM reciboscuenta rc WHERE rc.idCuenta = cta.id ORDER BY rc.created_at DESC LIMIT 1) as fechaPago FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento AND cta.saldo = 0) as fechaPago"),  
                DB::Raw("(SELECT (CASE cta.saldo WHEN 0 THEN 'CANCELADO' ELSE 'PENDIENTE' END) as cancelacion FROM cuenta as cta WHERE cta.idProveedor = c.idProveedor AND CONCAT(cta.serie,'-',cta.numero) = c.documento ORDER BY cta.created_at DESC LIMIT 1) as cancelacion"), DB::Raw("(CASE WHEN TIMESTAMPDIFF(DAY, c.fechaVencimiento, CURRENT_DATE) > 0 THEN 'VENCIDO' ELSE 'POR VENCER' END) as vencido"), 'c.created_at')
                ->unionAll($quer1)
                ->unionAll($quer2)
                ->orderBy('created_at')
                ->get();
            #echo $data; exit;
        } elseif ($tipo == 21) {
            $data = DB::table('detalleventa as dv')
                    ->join('venta as v','v.id','=','dv.idVenta')
                    ->join('persona as cl', 'cl.id', '=','v.idCliente')
                    ->join('loteauto as la','la.id','=', 'dv.idLoteAuto')
                    ->whereNull('v.deleted_at');
            
            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(dv.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(dv.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }
            $data = $data->select('la.vin', 'la.precioDolares as costo', 'cl.documento',
            DB::Raw("(CASE WHEN cl.tipoDocumento = 'PN' THEN CONCAT(cl.nombres, ' ', cl.apellidos) ELSE cl.razonSocial END)  as cliente"))
            ->orderBy('v.created_at')
            ->distinct()
            ->get();
        }

        return $data;
    }

    public function getDataCliente($fechaI, $fechaF, $tipo, $cliente) {
        if ($tipo == 1) {
            $data = DB::table('ordentrabajo as ot')
                ->join('persona as cl','cl.id','=','ot.idCliente')
                ->join('trabajador as per','per.id','=','ot.idPersonal')
                ->leftjoin('trabajador as tr','tr.id','=','ot.idAsignado')
                ->join('detalleordentrabajo as detor','detor.idOrdenTrabajo','=','ot.id')
                ->join('cotizacion as ct','ct.id','=','detor.idCotizacion')
                ->join('detallecotizacion as dct','dct.idCotizacion','=','ct.id')
                ->where(function ($qq) use ($cliente) {
                    $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                    ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select(DB::Raw("CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) as orden"),
                'dct.descripcion as detalle', DB::Raw("ROUND(dct.cantidad,2) as cantidad"),
                DB::Raw("DATE_FORMAT(dct.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                DB::Raw("CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) as cotizacion"),
                DB::Raw("DATE_FORMAT(ot.fecha,'%d/%m/%Y') as fecha"),
                'ot.placa', 'ot.situacionFacturado', 'ct.kilometraje','ct.vin','ct.marcamodelo',
                DB::Raw("DATE_FORMAT(ot.inicia,'%d/%m/%Y %h:%i:%s %p') as inicia"),
                DB::Raw("DATE_FORMAT(ot.finaliza,'%d/%m/%Y %h:%i:%s %p') as finaliza"),
                DB::Raw("CONCAT(tr.apellidos,' ', tr.nombres) as trabajador"),
                'cl.documento as documentoCliente', 'ot.situacion',
                DB::Raw("CONCAT(per.apellidos,' ',per.nombres) as personal"),
                'cl.correoElectronico', 'cl.telefono as celular',
                DB::Raw("(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idOrden = ot.id LIMIT 1) as venta"),
                DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                DB::Raw("CONCAT(cl.documento, '@@@', cl.telefono) as keyI")
                )
                // ->orderBy('ot.fecha','ASC')
                // ->orderBy('cl.documento','ASC')
                // ->orderBy('ot.placa','ASC')
                ->get();
        } elseif($tipo == 2) {
            $data2 = DB::table('cotizacion as ct')
                    ->join('trabajador as per','per.id','=','ct.idPersonal')
                    ->join('persona as cl','cl.id','=','ct.idCliente')
                    ->join('detallecotizacion as dct','dct.idCotizacion','=','ct.id')
                    ->where('ct.situacion','<>','U')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data2 = $data2->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data2 = $data2->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  

            
            $data2 = $data2->select('dct.descripcion as detalle', DB::Raw("ROUND(dct.cantidad,2) as cantidad"),
                DB::Raw("DATE_FORMAT(dct.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                DB::Raw("CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) as cotizacion"),
                DB::Raw("DATE_FORMAT(ct.fecha,'%d/%m/%Y') as fecha"),
                'ct.placa', 'ct.situacionFacturado', 'ct.kilometraje','ct.vin','ct.marcamodelo',
                'cl.documento as documentoCliente', 'ct.situacion',
                DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                DB::Raw("CONCAT(per.apellidos,' ', per.nombres) as personal"),
                'cl.correoElectronico', 'cl.telefono as celular',
                DB::Raw("(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idCotizacion = ct.id LIMIT 1) as venta"),
                DB::Raw("CONCAT(cl.documento, '@@@', cl.telefono) as keyI")
            );

            // PARA COTIZACIONES DE AUTOS
            $data = DB::table('cotizacionauto as ct')
                    ->join('trabajador as per','per.id','=','ct.idPersonal')
                    ->join('persona as cl','cl.id','=','ct.idCliente')
                    ->join('detallecotizacionauto as dct','dct.idCotizacion','=','ct.id')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  

            $data = $data->select('dct.descripcion as detalle', DB::Raw("ROUND(dct.cantidad,2) as cantidad"),
                        DB::Raw("DATE_FORMAT(dct.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                        DB::Raw("CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) as cotizacion"),
                        DB::Raw("DATE_FORMAT(ct.fecha,'%d/%m/%Y') as fecha"),
                        DB::Raw("'-'  as placa"),
                        DB::Raw("'-'  as situacionFacturado"),
                        DB::Raw("'-'  as kilometraje"),
                        DB::Raw("'-'  as vin"),
                        DB::Raw("'-'  as marcamodelo"),                        
                        'cl.documento as documentoCliente', 'ct.situacion',
                        DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                        DB::Raw("CONCAT(per.apellidos,' ', per.nombres) as personal"),
                        'cl.correoElectronico', 'cl.telefono as celular',
                        DB::Raw("NULL as venta"),
                        DB::Raw("CONCAT(cl.documento, '@@@',cl.telefono) as keyI")
                    )
                    ->unionAll($data2)
                    ->get();
            // $data = DB::select("CALL reporteComprobantePLE(?,?)",[$fechaI, $fechaF]);
        } elseif($tipo == 3) {
            $data = DB::table('venta as v')
                    ->join('detalleventa as dv','dv.idVenta','=','v.id')
                    ->join('pagodetalle as pd','pd.idDetalleVenta','=','dv.id')
                    ->join('trabajador as per','per.id','=','v.idPersonal')
                    ->join('persona as cl','cl.id','=','v.idCliente')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
            $data = $data->select('dv.descripcion as detalle', DB::Raw("ROUND(dv.cantidad,2) as cantidad"),
                    DB::Raw("DATE_FORMAT(dv.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                    DB::Raw("CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as venta"),
                    DB::Raw("DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha"),
                    'cl.documento as documentoCliente', 'v.situacion',
                    DB::Raw("(CASE WHEN pd.idCotizacion IS NOT NULL 
                    THEN (SELECT CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) FROM cotizacion ct WHERE ct.id = pd.idCotizacion) 
                    ELSE (CASE WHEN pd.idOrden IS NOT NULL THEN (SELECT CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) FROM ordentrabajo as ot WHERE ot.id = pd.idOrden) 
                    ELSE NULL END) END) as referencia"),
                    DB::Raw("(CASE WHEN pd.idCotizacion IS NOT NULL 
                    THEN (SELECT CONCAT(ct.placa,'@@', ct.kilometraje, '@@', (CASE WHEN ct.marcamodelo IS NULL THEN '' ELSE ct.marcamodelo END), '@@', ct.vin) FROM cotizacion as ct WHERE ct.id = pd.idCotizacion LIMIT 1) 
                    ELSE (CASE WHEN pd.idOrden IS NOT NULL THEN (SELECT CONCAT(ct.placa,'@@', ct.kilometraje, '@@', (CASE WHEN ct.marcamodelo IS NULL THEN '' ELSE ct.marcamodelo END),'@@', ct.vin) FROM detalleordentrabajo as dto JOIN cotizacion as ct ON ct.id = dto.idCotizacion WHERE dto.idOrdenTrabajo = pd.idOrden ORDER BY dto.id ASC LIMIT 1) 
                    ELSE NULL END) END) as params"),                 
                    'v.asesorAuto',
                    DB::Raw("CONCAT(per.apellidos,' ', per.nombres) as personal"),
                    'cl.correoElectronico', 'cl.telefono as celular',
                    DB::Raw("CONCAT(cl.documento, '@@@',cl.telefono) as keyI"),
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente")
                )
                ->get();
            // $data = DB::select("CALL reporteComprobantesComprasPLE(?,?)", [$fechaI, $fechaF]);
        } elseif($tipo == 4) {
            $data = DB::table('venta as v')
                    ->join('detalleventa as dv','dv.idVenta','=','v.id')
                    ->join('pagodetalle as pd','pd.idDetalleVenta','=','dv.id')
                    ->join('trabajador as per','per.id','=','v.idPersonal')
                    ->join('persona as cl','cl.id','=','v.idCliente')
                    ->whereNotNull('dv.idAuto')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
            $data = $data->select('dv.descripcion as detalle', DB::Raw("ROUND(dv.cantidad,2) as cantidad"),
                    DB::Raw("DATE_FORMAT(dv.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                    DB::Raw("CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as venta"),
                    DB::Raw("DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha"),
                    'cl.documento as documentoCliente', 'v.situacion',
                    DB::Raw("(CASE WHEN pd.idCotizacion IS NOT NULL 
                    THEN (SELECT CONCAT('C', LPAD(ct.serie,3,'0') ,'-', LPAD(ct.numero,8,'0')) FROM cotizacion ct WHERE ct.id = pd.idCotizacion) 
                    ELSE (CASE WHEN pd.idOrden IS NOT NULL THEN (SELECT CONCAT('OD', LPAD(ot.serie,2,'0') ,'-', LPAD(ot.numero,8,'0')) FROM ordentrabajo as ot WHERE ot.id = pd.idOrden) 
                    ELSE NULL END) END) as referencia"),
                    'v.asesorAuto',
                    DB::Raw("CONCAT(per.apellidos,' ', per.nombres) as personal"),
                    'cl.correoElectronico', 'cl.telefono as celular',
                    DB::Raw("CONCAT(cl.documento, '@@@',cl.telefono) as keyI"),
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente")
                )
                ->get();
            // $data = DB::select("CALL reporteComprobantesComprasPLE(?,?)", [$fechaI, $fechaF]);
        } elseif ($tipo == 5) {
            $data = DB::select("CALL reporteClientesMarketing(?,?,?)",[$fechaI, $fechaF, '%'.$cliente.'%']);
        } elseif($tipo == 11) {
            $data = DB::table('ordentrabajo as ot')
                ->join('persona as cl','cl.id','=','ot.idCliente')
                ->where(function ($qq) use ($cliente) {
                    $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                    ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ot.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }

            $data = $data->select(
                    'ot.placa',
                    DB::Raw("(SELECT CONCAT(c.placa,'@@', c.kilometraje,'@@', c.marcamodelo) FROM cotizacion c JOIN detalleordentrabajo as dt ON dt.idCotizacion = c.id WHERE dt.idOrdenTrabajo = ot.id ORDER BY dt.id ASC LIMIT 1) as params"),
                    'cl.documento', 'cl.direccion', 'cl.correoElectronico', 'cl.telefono as celular',
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                    DB::Raw("DATE_FORMAT(ot.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                    DB::Raw("(SELECT (CASE WHEN pd.idOrden IS NOT NULL THEN (SELECT CONCAT(cot.placa, '@@', cot.kilometraje,'@@', cot.marcamodelo) FROM cotizacion as cot JOIN detalleordentrabajo as dt ON dt.idCotizacion = cot.id WHERE dt.idOrdenTrabajo = pd.idOrden ORDER BY dt.id ASC LIMIT 1) ELSE (SELECT CONCAT(ct.placa,'@@', ct.kilometraje,'@@',ct.marcamodelo) FROM cotizacion as ct WHERE ct.id = pd.idCotizacion LIMIT 1) END) FROM pagodetalle as pd WHERE pd.idOrden = ot.id AND (pd.idOrden IS NOT NULL OR pd.idCotizacion IS NOT NULL) LIMIT 1) as params")
                )
                ->distinct()
                ->orderBy('cl.documento','ASC')
                ->get();

        } elseif($tipo == 22) {
            $data2 = DB::table('cotizacion as ct')
                    ->join('persona as cl','cl.id','=','ct.idCliente')
                    ->where('ct.situacion','<>','U')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data2 = $data2->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data2 = $data2->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  

            
            $data2 = $data2->select('cl.documento','cl.direccion','cl.correoElectronico', 'cl.telefono as celular', 'ct.placa',
                'ct.kilometraje', 'ct.marcamodelo',
                DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                DB::Raw("DATE_FORMAT(ct.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg")
            );

            // PARA COTIZACIONES DE AUTOS
            $data = DB::table('cotizacionauto as ct')
                    ->join('persona as cl','cl.id','=','ct.idCliente')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(ct.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  

            $data = $data->select('cl.documento','cl.direccion','cl.correoElectronico', 'cl.telefono as celular', DB::Raw("NULL as placa"),
                        DB::Raw("NULL as kilometraje"), DB::Raw("NULL as marcamodelo"),
                        DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                        DB::Raw("DATE_FORMAT(ct.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg")
                    )
                    ->unionAll($data2)
                    ->distinct()
                    ->orderBy('documento','ASC')
                    ->get();
        } elseif($tipo == 33) {
            $data = DB::table('venta as v')
                    ->join('persona as cl','cl.id','=','v.idCliente')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
            $data = $data->select('cl.documento', 'cl.direccion', 'cl.correoElectronico', 'cl.telefono as celular',
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                    DB::Raw("DATE_FORMAT(v.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"),
                    DB::Raw("(SELECT (CASE WHEN pd.idOrden IS NOT NULL THEN (SELECT CONCAT(cot.placa, '@@', cot.kilometraje,'@@', cot.marcamodelo) FROM cotizacion as cot JOIN detalleordentrabajo as dt ON dt.idCotizacion = cot.id WHERE dt.idOrdenTrabajo = pd.idOrden ORDER BY pd.id ASC LIMIT 1) ELSE (SELECT CONCAT(ct.placa,'@@', ct.kilometraje,'@@',ct.marcamodelo) FROM cotizacion as ct WHERE ct.id = pd.idCotizacion LIMIT 1) END) FROM pagodetalle as pd WHERE pd.idVenta = v.id AND (pd.idOrden IS NOT NULL OR pd.idCotizacion IS NOT NULL) LIMIT 1) as params")
                )
                ->distinct()
                ->orderBy('cl.documento','ASC')
                ->get();
        } elseif($tipo == 44) {
            $data = DB::table('venta as v')
                    ->join('detalleventa as dv','dv.idVenta','=','v.id')
                    ->join('persona as cl','cl.id','=','v.idCliente')
                    ->whereNotNull('dv.idAuto')
                    ->where(function ($qq) use ($cliente) {
                        $qq->where('cl.documento','LIKE','%'.$cliente.'%')
                        ->orWhere(DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END)"),'LIKE', '%'.$cliente.'%');
                    });

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(v.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
            
            $data = $data->select('cl.documento','cl.direccion', 'cl.correoElectronico', 'cl.telefono as celular',
                    DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
                    DB::Raw("DATE_FORMAT(v.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaReg"), DB::Raw("DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha"),
                    'dv.descripcion', 'v.asesorAuto'
                )
                ->distinct()
                ->orderBy('cl.documento','ASC')
                ->get();

        } elseif ($tipo == 55){
            $data = DB::table('cita as c')
                ->join('trabajador as t','t.id','=','c.idUsuario')
                ->join('persona as cl','cl.id','=','c.idCliente')
                ->leftjoin('marcaauto as ma', 'ma.id','=','c.idMarcaAuto');

            if ($fechaI != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'>=', $fechaI);
            }

            if ($fechaF != '') {
                $data = $data->where(DB::Raw("DATE_FORMAT(c.created_at, '%Y-%m-%d')"),'<=', $fechaF);
            }  
    
            $data = $data->select('cl.documento','cl.direccion', 'cl.correoElectronico', 'cl.telefono as celular',
                DB::Raw("CONCAT('C',LPAD(c.serie,3,'0')) as serie"), 'c.vin', 'c.con_soat', 'c.con_seguro',
                DB::Raw("CONCAT(c.numero,8,'0') as numero"), 'ma.nombre as marca', 'c.modelo', 'c.anio',
                'c.kilometraje', 'c.hora', 'c.duracion', 'c.indicaciones', 'c.situacion', 'c.con_cita',
                DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"), 'c.placa', DB::Raw("(CASE c.tipoServicio 
                WHEN 'MP' THEN 'Mantenimiento Preventivo'
                WHEN 'MC' THEN 'Mantenimiento Correctivo'
                WHEN 'L' THEN 'Lavado'
                WHEN 'PB' THEN 'Programa de Bienvenida'
                WHEN 'IA' THEN 'Instalación de accs'
                WHEN 'PD' THEN 'PDI'
                WHEN 'G' THEN 'Garantía'
                WHEN 'C' THEN 'Campaña'
                WHEN 'PP' THEN 'Planchado y Pintura'
                WHEN 'IS' THEN 'Inspección Seminuevo'
                WHEN 'TR' THEN 'Trabajo repetido'
                WHEN 'S' THEN 'Siniestro'
                ELSE '' END) as tipoServicio"),
                DB::Raw("CONCAT(t.apellidos,' ', t.nombres) as trabajador"),
                DB::Raw("DATE_FORMAT(c.created_at,'%d/%m/%Y %h:%i:%s %p') as fechaRegistro"),
                DB::Raw("DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha")
            )
            ->distinct()
            ->orderBy('c.fecha','ASC')
            ->get();
        
        }
        
        return $data;
    }


    public function getProductoConcat ($elemento) {
        $return = $elemento->nombre2; 
        if ($elemento->nombre != '') {
            $return = $elemento->nombre;
            // if($elemento->medida != '-') {
            //     $return .= ", Medida: $elemento->medida";
            // }

            // if($elemento->tipollanta != '-') {
            //     $return .= ", Tipo de Llanta: $elemento->tipollanta";
            // }

            // if($elemento->marca != '-') {
            //     $return .= ", Marca: $elemento->marca";
            // }

            // if($elemento->modelo != '-') {
            //     $return .= ", Modelo: $elemento->modelo";
            // }
            
            // if($elemento->sistema != '-') {
            //     $return .= ", Sistema: $elemento->sistema";
            // }

            // if($elemento->medida != '-') {
            //     $return .= ", Medida: $elemento->medida";
            // }
        }
        return $return;
    }
    
    public function getStringProducto ($tipo) {
        $return = '';
        switch ($tipo) {
            case 'A':
                $return = 'Accesorios/Repuestos';
                break;
            case 'B':
                $return = 'Bater铆as';
                break;
            case 'LL':
                $return = 'Neum谩ticos';
                break;
            case 'M':
                $return = 'Muelles';
                break;
            case 'I':
                $return = 'Insumos';
                break;
            default: 
                $return = $tipo;
                break;
        }

        return $return;
    }

    public function getExplodeString($string, $index) {
        try {
            $arrString = explode('-',$string);
            return $arrString[$index];
        } catch (\Exception $ex) {
            return '-';
        }
    }

    public function getExplodeStringReport($string, $index, $separator) {
        try {
            $arrString = explode($separator,$string);
            return $arrString[$index];
        } catch (\Exception $ex) {
            return '-';
        }
    }

    public function getExplodeMarcaModeloString($string, $index) {
        try {
            $arrString = explode('/',$string);
            return $arrString[$index];
        } catch (\Exception $ex) {
            return '-';
        }
    }


    public function saveElementos (Request $request) {
        dd("Inhabilitado por seguridad");
        $pagos = PagoDetalle::whereNotNull('idCotizacion')
                    ->orWhereNotNull('idOrden')
                    ->get();
        
        foreach ($pagos as $pg) {
            $dtv = DB::table('detalleventa')
                    ->where('id',$pg->idDetalleVenta)->first();
            if (!is_null($dtv)) {
                if (!is_null($pg->idOrden)) {
                    $detalles_orden = DB::table('detalleordentrabajo')->where('idOrdenTrabajo',$pg->idOrden)
                                        ->get();
                    foreach ($detalles_orden as $d_o) {
                        if (!is_null($dtv->idProducto)) {
                            $dcot = DB::table('detallecotizacion')->where('idCotizacion',$d_o->idCotizacion)
                                    ->where('cantidad',$dtv->cantidad)
                                    ->where('idProducto',$dtv->idProducto)
                                    // ->where(function ($qq) use ($dtv) {
                                    //     $qq ->orwhere('idServicio', $dtv->idServicio);
                                    // })
                                    ->first();
                            // if (!is_null($dcot)) {
                            //     $dHom = new DetalleHomologacion;
                            //     $dHom->idDetalleVenta = $dtv->id;
                            //     $dHom->idDetalleCotizacion = $dcot->id;
                            //     $dHom->save();
                            // }
                        } else {
                            $dcot = DB::table('detallecotizacion')->where('idCotizacion',$d_o->idCotizacion)
                                    ->where('cantidad',$dtv->cantidad)
                                    ->where('idServicio',$dtv->idServicio)
                                    // ->where(function ($qq) use ($dtv) {
                                    //     $qq ->orwhere('idServicio', $dtv->idServicio);
                                    // })
                                    ->first();
                        }

                        if (!is_null($dcot)) {
                            $dHom = new DetalleHomologacion;
                            $dHom->idDetalleVenta = $dtv->id;
                            $dHom->idDetalleCotizacion = $dcot->id;
                            $dHom->save();
                        }
                
                  
                    }
                } elseif(!is_null($pg->idCotizacion)) {
                    if (!is_null($dtv->idProducto)) {
                        $dcot = DB::table('detallecotizacion')->where('idCotizacion',$pg->idCotizacion)
                            ->where('cantidad',$dtv->cantidad)
                            ->where('idProducto',$dtv->idProducto)
                            // ->where(function ($qq) use ($dtv) {
                            //     $qq ->orwhere('idServicio', $dtv->idServicio);
                            // })
                            ->first();
                    } else {
                        $dcot = DB::table('detallecotizacion')->where('idCotizacion',$pg->idCotizacion)
                                ->where('cantidad',$dtv->cantidad)
                                ->where('idServicio',$dtv->idServicio)
                                // ->where(function ($qq) use ($dtv) {
                                //     $qq ->orwhere('idServicio', $dtv->idServicio);
                                // })
                                ->first();
                    
                    }

                    if (!is_null($dcot)) {
                        $dHom = new DetalleHomologacion;
                        $dHom->idDetalleVenta = $dtv->id;
                        $dHom->idDetalleCotizacion = $dcot->id;
                        $dHom->save();
                    }
                
                }
            }
        }

        dd("Ok, terminado");
    }

    public function getNotificaciones(Request $request) {
        $esAsesor = false;
        $dataCuentas = DB::select("CALL getNotificacionesCuentasXCobrarPagar()");
        $dataInventarios = DB::select("CALL getNotificacionesInventario()");
        $dataProspectos  = DB::select("CALL getNotificacionesProspectos()");
        $dataOrdenes = DB::select("CALL getNotificacionesOrdenesTrabajo()");
        $dataCotizacionesSO = DB::select("CALL getNotificacionesCotizacionSinOrden()");
        $dataOrdenesSI = DB::select("CALL getNotificacionesOrdenSinIniciar()");
        $dataEncuestasSLL = DB::select("CALL getNotificacionesEncuestaSinLlenar()");
        
        #MENSAJES DE SISTEMA
        $dataMensajes = DB::select("CALL getMensajesSistema(?)", [ Auth::user()->usuarioId ]);
       
        if (Auth::user()->categoriaPersonalId == '15') { // ADMINISTRADOR DEL SISTEMA
			$esAsesor = true;
        }

        $dataComplete = [];
        $dataMessagesComplete = [];
        foreach ($dataCuentas as $item) {
           $dataComplete[] = [
             'color' => 'danger',
             'titulo' => $item->tipocuenta == '1'? 'Cuenta por Cobrar': 'Cuenta por Pagar',
             'mensaje' => 'Tiene '. $item->cantidad.' '.($item->cantidad==1?'cuenta':'cuentas'). ' '.
                ($item->modalidad == 'C7'? 'por vencer en 7 días': 
                ($item->modalidad == 'C15'?'por vencer en 15 días':'que vencen hoy')),
             'icono' => 'fa fa-compass',
             'hora' => date('H:m A')
           ];
        }

        foreach ($dataInventarios as $item) {
            $dataComplete[] = [
              'color' => 'success',
              'titulo' => 'Inventario de Productos',
              'mensaje' => 'Tiene '. $item->cantidad. ' '. $item->tipoProducto . ' '.($item->tipoAlerta == 'SM'? 'con Stock Mínimo':'sin Stock'),
              'icono' => 'fa fa-exclamation-circle',
              'hora' => date('H:m A')
            ];
        }

        foreach ($dataProspectos as $item) {
            $dataComplete[] = [
              'color' => 'warning',
              'titulo' => 'Prospectos',
              'mensaje' => 'Tiene '. $item->cantidad.' '.($item->cantidad == 1?'prospecto':'prospectos'). ' '.
              ($item->tipoAlerta == 'A7'? 'ha registrado en los últimos 7 días': 
              ($item->tipoAlerta == 'A15'?'ha registrado en los últimos 15 días':
              ($item->tipoAlerta == 'A30'?'ha registrado en los últimos 30 días':
                'por vencer en 1 día') )),
              'icono' => 'fa fa-clipboard',
              'hora' => date('H:m A')
            ];
        }

        foreach ($dataOrdenes as $item) {
            $dataComplete[] = [
              'color' => 'secondary',
              'titulo' => 'Órdenes de Trabajo',
              'mensaje' => 'Tiene '. $item->total.' '.($item->total == 1?'orden':'órdenes'). ' '.
              ($item->tipoAlerta == 'A1'? 'no facturadas': 'finalizadas'),
              'icono' => 'fa fa-cart-arrow-down',
              'hora' => date('H:m A')
            ];
        }

        foreach ($dataCotizacionesSO as $item) {
            $dataComplete[] = [
              'color' => 'info',
              'titulo' => 'Cotizaciones sin Orden Trabajo',
              'mensaje' => 'Tiene '. $item->total.' '.($item->total==1?'cotización':'cotizaciones'). ' sin orden '.
              ($item->tipoAlerta == 'A1'? 'de hace 1 día': 
              ($item->tipoAlerta == 'A2'? 'de hace 2 días': 'de hace 3 días')),
              'icono' => 'fa fa-file',
              'hora' => date('H:m A')
            ];
        }

        foreach ($dataOrdenesSI as $item) {
            $dataComplete[] = [
              'color' => 'light',
              'titulo' => 'Órdene de Trabajo sin iniciar',
              'mensaje' => 'Tiene '. $item->total.' '.($item->total == 1?'orden':'órdenes'). ' sin iniciar '.
              ($item->tipoAlerta == 'A1'? 'de hace 1 día': 
              ($item->tipoAlerta == 'A2'? 'de hace 2 días': 'de hace 3 días')),
              'icono' => 'fa fa-stop',
              'hora' => date('H:m A')
            ];
        }
        
        foreach ($dataEncuestasSLL as $item) {
            $dataComplete[] = [
              'color' => 'dark',
              'titulo' => 'Encuestas sin llenar',
              'mensaje' => 'Tiene '. $item->total.' '.($item->total== 1?'encuesta':'encuestas') .' sin llenar del año '. $item->anio,
              'icono' => 'fa fa-calendar',
              'hora' => date('H:m A')
            ];
        }

        if($esAsesor == true) {
            $dataComplete = [];
            foreach ($dataMensajes as $item) {
                $dataMessagesComplete[] = [
                'color' => $item->tipo == 'E'?'info':'primary',
                'titulo' => $item->titulo,
                'mensaje' => $item->mensaje,
                'icono' =>  $item->tipo == 'E'?'fa fa-comment':'fa fa-comments',
                'hora' => date('H:m A')
                ];
            }
        }

        return ['notificaciones' => $dataComplete, 'mensajes' => $dataMessagesComplete];
    }
}
