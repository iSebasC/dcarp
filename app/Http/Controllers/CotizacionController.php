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
use App\Models\CotizacionAuto;
use App\Models\Personal;
use App\Models\DetalleCotizacion;
use App\Models\DetalleCotizacionAuto;
use App\Models\Serie;
use App\Models\Oportunidad;

// use PDF;
use PDF;
use Fpdf;
use Karriere\PdfMerge\PdfMerge;


use App\Libraries\Funciones;
use App\Libraries\EnLetras;

use PhpOffice\PhpSpreadsheet\Spreadsheet	 as PHPExcel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx	 as PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border 	 as PHPExcel_Style_Border;
use PhpOffice\PhpSpreadsheet\Style\Fill 	 as PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as PHPExcel_Style_Alignment;

use DB;
use Auth;
use Validator;

class CotizacionController extends Controller
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

	public $estilo_header2 = array( 
		'borders' => array(
			'allborders' => array(
				'borderStyle' => PHPExcel_Style_Border::BORDER_THICK,
				'color' => array('rgb' => 'DDDDDD'),
			)
		),
		'fill' => array(
			'fillType' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'F7FAC6',
			)           
		),
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '000000'),
			'size'  => 12,
			'name'  => 'Calibri',
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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

	public function generarPdf($id){
		// {dd('esto aca'); 
		// $request->get('id');
		// $cotizacion =Cotizacion::find($id);
		// $cliente=Persona::find($cotizacion->idCliente);
		// $trabajdor=Personal::find($cotizacion->idPersonal);
		// dd($cotizacion->idCliente);
		$cotizacion=DB::table('cotizacion as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id','cot.marcamodelo' ,DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"), DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','cot.placa','cot.kilometraje','cot.vin','cot.total','cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente')
					->first();

		$detalles = DB::table('detallecotizacion as det')
					// ->leftJoin('producto as prod','prod.id','=','det.idProducto')
					// ->leftJoin('servicio as serv','serv.id','=','det.idServicio')
					->where('det.idCotizacion','=',$id)
					->whereNull('det.deleted_at')
					->select('det.*')
					->orderBy('det.idProducto','DESC')
					->get();

		$fpdf = new Fpdf();
		$fpdf::SetTitle(utf8_decode('Cotización'));
        $fpdf::AddPage('P','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,40,25);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(138, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(60,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(138);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(60,$alto,utf8_decode("COTIZACIÓN"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, $cotizacion->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(6);
        $alto = 3;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',9);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',8);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
        $fpdf::Ln(6);
	    
	    $alto = 6;
		$tam_font = 9;
		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($cotizacion->cliente)), "TR", "L");
    
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$fpdf::SetFont('Arial','B',$tam_font);
		//$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Señor(es)'), 'LT',0, "L");
		//$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
        
        //$_x = $fpdf::GetX();
        $fpdf::Cell(5, $alto2, utf8_decode(':'), 'T',0, "L");
        //$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

            
		//$_y = $fpdf::GetY();
		
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->cliente)), 'TR', "L");
		$fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);

	    // if ($alto2 > $alto) {
        //      $fpdf::SetXY(153,$fpdf::GetY()-($alto2-$alto));
        // } else {
        //      $fpdf::SetXY(153,$fpdf::GetY()-$alto2);
        // }

		$fpdf::Ln();
       	
   		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($cotizacion->doc_cliente)), 1, "L");
    
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto, utf8_decode(($cotizacion->tipo_cliente=='PN'?'D.N.I.':'R.U.C.')), 'L',0, "L");
		//$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
        
        //$_x = $fpdf::GetX();
		$fpdf::Cell(5, $alto, utf8_decode(':'),0,  0, "L");
        //$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
        $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->doc_cliente)), 'R', "L");
    	$fpdf::SetXY($_x+153, $fpdf::GetY()-$alto2);
		$fpdf::Ln();
			
   		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($cotizacion->direccion_cliente)), 1, "L");
   		
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		//$_x = $fpdf::GetX();
		$fpdf::Cell(25, $alto2, utf8_decode('Dirección'), 'L', 0,  "L");
		//$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
    
   		//$_x = $fpdf::GetX();
    	$fpdf::Cell(5, $alto2, utf8_decode(':'), 0, 0, "L");
        // $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);
    
		$_y = $fpdf::GetY();
   		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->direccion_cliente)), 'R', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
       	$fpdf::SetXY(15, ($alto2>$alto?$fpdf::GetY()+$alto:$fpdf::GetY()));

		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(($cotizacion->fecha)), 1, "L");
	   	$fpdf::SetFont('Arial','B',$tam_font);
   		//$_x = $fpdf::GetX();
        $fpdf::Cell(25, $alto2, utf8_decode('Fecha Emisión'), 'L',0, "L");
        //$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

        //$_x = $fpdf::GetX();
		$fpdf::Cell(5, $alto2, utf8_decode(':'),0, 0, "L");
        //$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		//$_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $_x = $fpdf::GetX();
    	$fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->fecha)), 'R', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		
		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper('soles')), 1, "L");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
   		$_x = $fpdf::GetX();
		$fpdf::MultiCell(25, $alto, utf8_decode('Moneda'), 'L',  "L");
        $fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

		$_x = $fpdf::GetX();
		$fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "L");
        $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		$_y = $fpdf::GetY();
		$_x = $fpdf::GetX();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper('soles')), 'R', "L");
        $fpdf::SetXY($_x+153,$fpdf::GetY()-$alto2);

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());

		$alto_1 = $fpdf::GetMultiCellHeight(80,$alto,utf8_decode(strtoupper($cotizacion->trabajador)), 1, "L");
		$alto_2 = $fpdf::GetMultiCellHeight(43,$alto,utf8_decode(strtoupper($cotizacion->telefono_tra)), 1, "L");
    
		if ($alto_1 > $alto_2) {
			$alto2 = $alto_1;
		} else {
			$alto2 = $alto_2;
		}


       	$fpdf::SetFont('Arial','B',$tam_font);
       	$_x = $fpdf::GetX();
		$fpdf::MultiCell(25, $alto, utf8_decode('Asesor'), 'L', "L");
     	$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

		$_x = $fpdf::GetX();
		$fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "L");
        $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

    	$fpdf::SetFont('Arial','',$tam_font);
		$_x = $fpdf::GetX();
	    $fpdf::MultiCell(80, $alto, utf8_decode(strtoupper($cotizacion->trabajador)), 0, "L");
		$fpdf::SetXY($_x+80, $fpdf::GetY()-$alto2);
	
		$fpdf::SetFont('Arial','B',$tam_font);
		$_x = $fpdf::GetX();
		$fpdf::MultiCell(25, $alto, utf8_decode('Teléfono'), 0, "L");
		$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);

        $_x = $fpdf::GetX();
        $fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "B");
        $fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);

		$fpdf::SetFont('Arial','',$tam_font);
		$_y = $fpdf::GetY();
    	$_x = $fpdf::GetX();
        $fpdf::MultiCell(43, $alto, utf8_decode(strtoupper($cotizacion->telefono_tra)), 'R', "L");
		$fpdf::SetXY($_x+43, $fpdf::GetY()-$alto2);
	
	
		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	// dd($fpdf::GetY());
		$alto_1 = $fpdf::GetMultiCellHeight(40,$alto,utf8_decode(strtoupper($cotizacion->placa)), 0, "L");
		$alto_2 = $fpdf::GetMultiCellHeight(30,$alto,utf8_decode(strtoupper($cotizacion->kilometraje)), 0, "L");
		$alto_3 = $fpdf::GetMultiCellHeight(38,$alto,utf8_decode(strtoupper($cotizacion->vin)), 0, "L");
    
		if ($alto_1 > $alto_2 && $alto_1 > $alto_3) {
			$alto2 = $alto_1;
		} elseif($alto_2 > $alto_1 && $alto_2 > $alto_3) {
			$alto2 = $alto_2;
		} else {
			$alto2 = $alto_3;
		}
	
		$fpdf::SetFont('Arial','B',$tam_font);
        $_x = $fpdf::GetX();
        $_y = $fpdf::GetY();
        
        $fpdf::MultiCell(25, $alto, utf8_decode('Placa'), 'L', "L");
    	$fpdf::SetXY($_x+25,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
		
		//dd($fpdf::GetY());
	
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
    
    	$fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "L");
        $fpdf::SetXY($_x+5,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
		
		$fpdf::SetFont('Arial','',$tam_font);
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
        $fpdf::MultiCell(40, $alto, utf8_decode(strtoupper($cotizacion->placa)), 0, "L");
		$fpdf::SetXY($_x+40, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);

		// $fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();

		$fpdf::MultiCell(25, $alto, utf8_decode('Kilometraje'), 0, "L");
		$fpdf::SetXY($_x+25,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
	
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
	    
	    $fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "L");
		$fpdf::SetXY($_x+5,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
		
		$fpdf::SetFont('Arial','',$tam_font);
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();

	    $fpdf::MultiCell(30, $alto, utf8_decode(strtoupper($cotizacion->kilometraje)), 0, "L");
		$fpdf::SetXY($_x+ 30, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);

		$fpdf::SetFont('Arial','B',$tam_font);
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
		$fpdf::MultiCell(10, $alto, utf8_decode('VIN'), 0, "L");
		$fpdf::SetXY($_x+10,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
	
	
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
	    $fpdf::MultiCell(5, $alto, utf8_decode(':'), 0, "L");
		$fpdf::SetXY($_x+5,$_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
		
		$fpdf::SetFont('Arial','',$tam_font);
		$_x = $fpdf::GetX();
	    $_y = $fpdf::GetY();
	    $fpdf::MultiCell(38, $alto, utf8_decode(strtoupper($cotizacion->vin)), 'R', "L");
		$fpdf::SetXY($_x+ 38, $_y>($fpdf::GetY()-$alto2)?$_y:$fpdf::GetY()-$alto2);
		$fpdf::Ln();

		$alto2 = $fpdf::GetMultiCellHeight(153,$alto,utf8_decode(strtoupper($cotizacion->marcamodelo)), 1, "L");
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$_x = $fpdf::GetX();
		$fpdf::MultiCell(25, $alto2, utf8_decode('Marca/Modelo'), 'LB', "L");
     	$fpdf::SetXY($_x+25,$fpdf::GetY()-$alto2);
     	
		$_x = $fpdf::GetX();
		$fpdf::MultiCell(5, $alto2, utf8_decode(':'), 'B', "L");
		$fpdf::SetXY($_x+5,$fpdf::GetY()-$alto2);
     	
		$fpdf::SetFont('Arial','',$tam_font);
		$_x = $fpdf::GetX();
	    $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->marcamodelo)), 'BR', "L");
		$fpdf::SetXY($_x+ 153, $fpdf::GetY()-$alto2);


		$fpdf::SetFillColor(255);
		$fpdf::Ln(12);
			
		$fpdf::SetFont('Arial','B',$tam_font);		
		$fpdf::SetFillColor(253,242,75);
   		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $fpdf::Cell(23, $alto, utf8_decode("PRODUCTOS"),0, 0, "L", true);
        // $fpdf::Cell(1);
      
		$fpdf::SetFillColor(255);
		$fpdf::Ln();
		$alto = 2;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',8);
        // $fpdf::Cell(15, $alto, utf8_decode("CÓDIGO"), 1, 0, "C");
        $fpdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C");
        $fpdf::Cell(13, $alto, utf8_decode("UND."), 1, 0, "C");
        $fpdf::Cell(69, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        $fpdf::Cell(15, $alto, utf8_decode("DESC. (%)"), 1, 0, "C");
        $fpdf::Cell(27, $alto, utf8_decode("V. REFERENCIAL"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("V. UNITARIO"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("IMPORTE"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',7);
        $alto = 4;
        $total = 0;
        $acum=0;
		foreach ($detalles as $deta) {
			if (!is_null($deta->idProducto)) {
				$alto2 = $fpdf::GetMultiCellHeight(69,$alto,utf8_decode($deta->descripcion), 1, "C");
         
				$fpdf::SetX(15);
        		// $fpdf::Cell(15, $alto2, utf8_decode("-"), 'L', 0, "C");
				$fpdf::Cell(15, $alto2, $deta->cantidad, 'L', 0, "R");
				$fpdf::Cell(13, $alto2, utf8_decode("UND"), 0, 0, "C");
			
				$_y = $fpdf::GetY();
    			$_x = $fpdf::GetX();
    			$fpdf::MultiCell(69, $alto, utf8_decode($deta->descripcion), 0, "L");
				$fpdf::setXY($_x+69,$fpdf::GetY()-$alto2);
				$fpdf::Cell(15, $alto2, number_format($deta->porcentajeDescuento,2,'.',','), 0, 0, "R");
				$fpdf::Cell(27, $alto2, number_format($deta->precioReferencial,2,'.',','), 0, 0, "R");
				$fpdf::Cell(22, $alto2, number_format($deta->precio,2,'.',','), 0, 0, "R");
				$fpdf::Cell(22, $alto2, number_format($deta->subTotal,2,'.',','), 'R', 0, "R");
				$fpdf::Ln();
				$total+=$deta->subTotal;
				$acum++;
        	}
		}

		$fpdf::SetX(15);
		if ($acum == 0) {
			$fpdf::Cell(183, $alto, utf8_decode("NO EXISTEN PRODUCTOS SELECCIONADOS"), 1, 1, "L");
		} else {
			$fpdf::Cell(158, $alto, '', 'T', 0, "L");
			// $fpdf::SetX(15);
			$fpdf::SetFont('Arial','B',9);
			$fpdf::Cell(25, $alto, number_format($total,2,'.',','), 'T', 0, "R");
		}

		$fpdf::Ln();
		$fpdf::SetFont('Arial','B',$tam_font);		
		$fpdf::SetFillColor(253,242,75);
		$alto = 6;
     	$fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $fpdf::Cell(21, $alto, utf8_decode("SERVICIOS"),0, 0, "L", true);
        // $fpdf::Cell(1);
      
		$fpdf::SetFillColor(255);
		$fpdf::Ln();

		$alto = 2;   
        $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        $_Y_list = $fpdf::GetY();
        $alto = 6;
        $fpdf::SetFont('Arial','B',8);
        // $fpdf::Cell(15, $alto, utf8_decode("CÓDIGO"), 1, 0, "C");
        $fpdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C");
        $fpdf::Cell(13, $alto, utf8_decode("UND."), 1, 0, "C");
        $fpdf::Cell(69, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
		$fpdf::Cell(15, $alto, utf8_decode("DESC. (%)"), 1, 0, "C");
		$fpdf::Cell(27, $alto, utf8_decode("V. REFERENCIAL"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("V. UNITARIO"), 1, 0, "C");
        $fpdf::Cell(22, $alto, utf8_decode("IMPORTE"), 1, 0, "C");
        $fpdf::Ln();
        $fpdf::SetFont('Arial','',7);
        $alto = 4;
        $total = 0;
        $acum = 0;

		foreach ($detalles as $deta) {
			if (!is_null($deta->idServicio)) {
				$alto2 = $fpdf::GetMultiCellHeight(69,$alto,utf8_decode($deta->descripcion), 1, "C");
        		$fpdf::SetX(15);
        		// $fpdf::Cell(15, $alto2, utf8_decode("-"), 'L', 0, "C");
				$fpdf::Cell(15, $alto2, $deta->cantidad, 'L', 0, "R");
				$fpdf::Cell(13, $alto2, utf8_decode("UND"), 0, 0, "C");
	
				$_y = $fpdf::GetY();
				$_x = $fpdf::GetX();
				$fpdf::MultiCell(69, $alto, utf8_decode($deta->descripcion), 0, "L");
				$fpdf::setXY($_x+69,$fpdf::GetY()-$alto2);
				$fpdf::Cell(15, $alto2, number_format($deta->porcentajeDescuento,2,'.',','), 0, 0, "R");
				$fpdf::Cell(27, $alto2, number_format($deta->precioReferencial,2,'.',','), 0, 0, "R");
				$fpdf::Cell(22, $alto2, number_format($deta->precio,2,'.',','), 0, 0, "R");
				$fpdf::Cell(22, $alto2, number_format($deta->subTotal,2,'.',','), 'R', 0, "R");
				$fpdf::Ln();
				$total+=$deta->subTotal;
				$acum++;
        	}
		}

		$fpdf::SetX(15);
		if ($acum == 0) {
			$fpdf::Cell(183, $alto, utf8_decode("NO EXISTEN SERVICIOS SELECCIONADOS"), 1, 1, "L");
		} else {
			$fpdf::Cell(158, $alto, '', 'T', 0, "L");
			// $fpdf::SetX(15);
			$fpdf::SetFont('Arial','B',9);
			$fpdf::Cell(25, $alto, number_format($total,2,'.',','), 'T', 0, "R");	
		}

		$alto = 6;
    	$fpdf::SetFillColor(240);
		$fpdf::Ln(12);
        $fpdf::SetX(15);
        $fpdf::SetFont('Arial','B',9);
      
		$letras = new EnLetras();
        // $fpdf::SetFont('helvetica', 'B', 8);
        $valor = $letras->ValorEnLetras(str_replace(',','',$cotizacion->total), " SOLES"); //letras

		$son = strtoupper("SON: ".$valor);
        $fpdf::MultiCell(183, $alto, utf8_decode($son), $borde, "L", true);
		   
		$fpdf::Ln(10);
        $fpdf::SetX(15);
       	$fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", true);
		$fpdf::Ln();
        
		$fpdf::SetTextColor(206,3,3);
        $fpdf::SetX(15);
       	$fpdf::Cell(183, $alto, utf8_decode("ESTE DOCUMENTO TIENE VALIDEZ DE 07 DÍAS HÁBILES"), 'RBL',0, "L");
		   
		

        $fpdf::SetXY(138, $fpdf::GetY());
        $fpdf::SetFont('Arial','',12);
        $alto = 5;
        
		$fpdf::Output("Cotizacion".$cotizacion->documento.".pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
		$fpdf::Output();

        exit;
  
	
	}



	public function generarExcel($id){
		// {dd('esto aca'); 
		// $request->get('id');
		// $cotizacion =Cotizacion::find($id);
		// $cliente=Persona::find($cotizacion->idCliente);
		// $trabajdor=Personal::find($cotizacion->idPersonal);
		// dd($cotizacion->idCliente);
		$cotizacion=DB::table('cotizacion as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id','cot.marcamodelo', DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"), DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"), DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','cot.placa','cot.vin','cot.kilometraje','cot.total','cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente')
					->first();

		$detalles = DB::table('detallecotizacion as det')
					// ->leftJoin('producto as prod','prod.id','=','det.idProducto')
					// ->leftJoin('servicio as serv','serv.id','=','det.idServicio')
					->where('det.idCotizacion','=',$id)
					->whereNull('det.deleted_at')
					->select('det.*')
					->orderBy('det.idProducto','DESC')
					->get();
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle($cotizacion->documento);
		
		$hoja1->setCellValue('E3','COTIZACIÓN N°: ');
		$hoja1->setCellValue('E4','SEÑORES: ');
		$hoja1->setCellValue('E5','DNI: ');
		$hoja1->setCellValue('E6','DIRECCIÓN: ');
		$hoja1->setCellValue('E7','FECHA DE EMISIÓN: ');
		$hoja1->setCellValue('E8','MONEDA: ');
		$hoja1->setCellValue('E9','ASESOR: ');
		$hoja1->setCellValue('E10','PLACA: ');
		$hoja1->setCellValue('E11','VIN: ');
		$hoja1->setCellValue('E12','KILOMETRAJE: ');
		$hoja1->setCellValue('E13','MARCA/MODELO: ');
		
		$hoja1->getStyle('E3:E13')->applyFromArray($this->estilo_header2);

		$hoja1->setCellValue('F3',$cotizacion->documento);
		$hoja1->setCellValue('F4',$cotizacion->cliente);
		$hoja1->setCellValue('F5',$cotizacion->doc_cliente);
		$hoja1->setCellValue('F6',$cotizacion->direccion_cliente);
		$hoja1->setCellValue('F7',$cotizacion->fecha);
		$hoja1->setCellValue('F8','SOLES');
		$hoja1->setCellValue('F9',$cotizacion->trabajador);
		$hoja1->setCellValue('F10',$cotizacion->placa);
		$hoja1->setCellValue('F11',$cotizacion->vin);
		$hoja1->setCellValue('F12',$cotizacion->kilometraje);
		$hoja1->setCellValue('F13',$cotizacion->marcamodelo);
		$hoja1->getStyle('F3:F13')->applyFromArray($this->estilo_content);
		
		$j = 17;
		$hoja1->setCellValue('B14','PRODUCTOS');
		$hoja1->getStyle('B14')->applyFromArray($this->estilo_header);

		$hoja1->setCellValue('B15','CÓDIGO');
		$hoja1->setCellValue('C15','CANTIDAD');
		$hoja1->setCellValue('D15','UND.');
		$hoja1->setCellValue('E15','DESCRIPCIÓN');
		$hoja1->setCellValue('F15','DESC. (%)');
		$hoja1->setCellValue('G15','V. REFERENCIAL');
		$hoja1->setCellValue('H15','V. UNITARIO');
		$hoja1->setCellValue('I15','IMPORTE');
		$hoja1->getStyle('B15:I15')->applyFromArray($this->estilo_header);
		$acum = 0;
		foreach ($detalles as $deta) {
			if (!is_null($deta->idProducto)) {
				$hoja1->setCellValue('B'.$j,'-');
				$hoja1->setCellValue('C'.$j,number_format($deta->cantidad,2,'.',' '));
				$hoja1->setCellValue('D'.$j,'UND');
				$hoja1->setCellValue('E'.$j,$deta->descripcion);
				$hoja1->setCellValue('F'.$j,number_format($deta->porcentajeDescuento,2,'.',' '));
				$hoja1->setCellValue('G'.$j,number_format($deta->precioReferencial,2,'.',' '));
				$hoja1->setCellValue('H'.$j,number_format($deta->precio,2,'.',' '));
				$hoja1->setCellValue('I'.$j,number_format($deta->subTotal,2,'.',' '));

				$hoja1->getStyle('B'.$j.':I'.$j)->applyFromArray($this->estilo_content);
				$acum +=$deta->subTotal;
				$j++;
			}
		
		}

		$hoja1->setCellValue('H'.$j,'TOTAL');
		$hoja1->getStyle('H'.$j)->applyFromArray($this->estilo_header);
		$hoja1->setCellValue('I'.$j,number_format($acum,2,'.',' '));
		$hoja1->getStyle('I'.$j)->applyFromArray($this->estilo_content);

		$j +=5;
		$hoja1->setCellValue('B'.$j,'SERVICIOS');
		$hoja1->getStyle('B'.$j)->applyFromArray($this->estilo_header);
		$j++;
		$hoja1->setCellValue('B'.$j,'CÓDIGO');
		$hoja1->setCellValue('C'.$j,'CANTIDAD');
		$hoja1->setCellValue('D'.$j,'UND.');
		$hoja1->setCellValue('E'.$j,'DESCRIPCIÓN');
		$hoja1->setCellValue('F'.$j,'DESC. (%)');
		$hoja1->setCellValue('G'.$j,'V. REFERENCIAL');
		$hoja1->setCellValue('H'.$j,'V. UNITARIO');
		$hoja1->setCellValue('I'.$j,'IMPORTE');
		$hoja1->getStyle('B'.$j.':I'.$j)->applyFromArray($this->estilo_header);
	
		$j++;
		$acum = 0;
		foreach ($detalles as $deta) {
			if (!is_null($deta->idServicio)) {
				$hoja1->setCellValue('B'.$j,'-');
				$hoja1->setCellValue('C'.$j,number_format($deta->cantidad,2,'.',' '));
				$hoja1->setCellValue('D'.$j,'UND');
				$hoja1->setCellValue('E'.$j,$deta->descripcion);
				$hoja1->setCellValue('F'.$j,number_format($deta->porcentajeDescuento,2,'.',' '));
				$hoja1->setCellValue('G'.$j,number_format($deta->precioReferencial,2,'.',' '));
				$hoja1->setCellValue('H'.$j,number_format($deta->precio,2,'.',' '));
				$hoja1->setCellValue('I'.$j,number_format($deta->subTotal,2,'.',' '));
				$hoja1->getStyle('B'.$j.':I'.$j)->applyFromArray($this->estilo_content);
				$j++;
				$acum+=$deta->subTotal;
			}
		}

		$hoja1->setCellValue('H'.$j,'TOTAL');
		$hoja1->getStyle('H'.$j)->applyFromArray($this->estilo_header);
		$hoja1->setCellValue('I'.$j,number_format($acum,2,'.',' '));
		$hoja1->getStyle('I'.$j)->applyFromArray($this->estilo_content);

		foreach(range('A','I') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		// foreach(range('F','H') as $columnID) 
		// { 
		// 	$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		// }

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="cotizacion-excel_'.$cotizacion->documento.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
        exit;
  
	
	}
	// http://127.0.0.1:8086/pdfcotizacionauto/1
	public function pdfCotizacionAuto ($id) {
		$cotizacion=DB::table('cotizacionauto as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id',DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"),	
						DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
						DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra',
						'tra.correoE as correo_tra','cot.total','cot.subTotal','cli.documento as documento_cli',
						DB::Raw('(SELECT da.idAuto FROM detallecotizacionauto da WHERE da.idCotizacion = cot.id AND da.idAuto IS NOT NULL) as idAuto'),
						DB::Raw("(CASE WHEN cot.tipoMoneda = 'D' THEN '$' ELSE 'S/' END) as moneda"),'cot.tipoMoneda', 'cli.tipoDocumento as tipo_cliente','cli.documento as doc_cliente','cli.direccion as direccion_cliente')
					->first();

		$auto = DB::table('auto as a')
				->leftjoin('modeloauto as mod','mod.id','=','a.modeloId')
				->leftjoin('marcaauto as ma', 'ma.id','=', 'a.marcaId')
				->select(
					DB::Raw("IFNULL(a.anio,'-') as anio"),
					DB::Raw("IFNULL(a.version,'-') as version"),
					DB::Raw("a.urlImagen as urlImagen"),
					DB::Raw("a.urlFicha as urlFicha"),
					DB::Raw("IFNULL(mod.nombre,'-') as modelo"),
					DB::Raw("IFNULL(ma.nombre,'-') as marca"))
				->where('a.id', $cotizacion->idAuto)
				->first();
		// dd($auto);

		if (!is_null($auto)) {
			$marca = trim($auto->marca);
			$modelo = trim($auto->modelo);
			$anio  = trim($auto->anio);
			$urlImagen = trim($auto->urlImagen);
			$urlFicha = trim($auto->urlFicha);
			$version = trim($auto->version);
		} else {
			$marca = '-';
			$modelo = '-';
			$anio  = '-';
			$urlImagen = null;
			$urlFicha = null;
			$version = '-';
		}

		// dd($marca, $modelo, $anio, $urlImagen, $urlFicha, $version);
		$detalles=DB::table('detallecotizacionauto')
				 ->where('idCotizacion',$cotizacion->id)
				 ->orderBy('idAuto','DESC')
				 ->get();

			
		$adicionales = [];
		$descuentos = [];
		$precioAuto = 0;
		foreach($detalles as $det) {
			if ($det->tipoDetalle == 'A') {
				$precioAuto = $det->subTotal;
			}

			if ($det->tipoDetalle == 'D') {
				if ($det->subTotal < 0) {
					$descuentos[] = $det;
				}

				if ($det->subTotal == 0) {
					$adicionales[] = $det;
				}
			}
		}
		// dd($precioAuto, $descuentos, $adicionales);

		$tipoMoneda= ($cotizacion->tipoMoneda=='D'?'DÓLARES':'SOLES');
		$fpdf = new Fpdf();
		$fpdf::SetTitle(utf8_decode('Cotización de Autos'));
        $fpdf::AddPage('P','A4');
        
		$fpdf::SetAutoPageBreak(true, 10);
        $fpdf::SetTextColor(0);
        $borde = 0;
        $fpdf::Image("images/logo-carpio.png", 15,12,40,25);
		$fpdf::SetXY(70, 12);
        $fpdf::SetFont('Arial','B',9);
        $alto = 4;
        $ancho = 66;
        $margin_left = 70;

		$fpdf::SetXY(138, 14);
        $fpdf::SetFont('Arial','B',14);
        $alto = 7;
        $fpdf::Cell(60,$alto,utf8_decode("R.U.C. 20103327378"),'RTL',0,'C');
        $fpdf::Ln($alto);
        $fpdf::SetX(138);
        $fpdf::SetFillColor(240);
        $fpdf::SetFont('Arial','B',12);
		$fpdf::Cell(60,$alto,utf8_decode("COTIZACIÓN"),'RL',0,'C');
	    $fpdf::Ln($alto);
        $fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, utf8_decode("ELECTRÓNICA"), 'RL',0,'C');
		$fpdf::Ln($alto);
		$fpdf::SetX(138);
       	$fpdf::Cell(60, $alto, $cotizacion->documento, 'RBL',0, 'C');
		
		$fpdf::Ln(6);
        $alto = 3;
        $margin_left = 15;
        $fpdf::SetFont('Arial','B',9);
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('CARPIO S.A.C'), $borde, 0, "L");
        $fpdf::SetFont('Arial','',8);
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('JR. JIMENEZ PIMENTEL NRO. 891 SAN MARTIN - SAN MARTIN - TARAPOTO'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
        $fpdf::Cell($ancho, $alto, utf8_decode('TELEFONO: 933016972'), $borde, 0, "L");
        $fpdf::Ln();
        $fpdf::SetX($margin_left);
  		$fpdf::Cell($ancho, $alto, utf8_decode('CORREO: maribel.sanchez@carpiosac.com.pe'), $borde, 0, "L");
        $fpdf::Ln(6);
	  
		$fpdf::SetXY(15, $fpdf::GetY()+$alto);
		$alto = 5;
		$tam_font = 9;
		$fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(183, $alto, '', 'T',0,'L');
		// $fpdf::Ln();
		
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::Cell(25, $alto, utf8_decode('Cliente:'), '', 0, "L");
		$fpdf::Cell(94, $alto, utf8_decode(''), '', 0, "L");
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::SetX(125);
		$fpdf::Cell(25, $alto, utf8_decode('De:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::SetX(140);
		$fpdf::Cell(58, $alto, utf8_decode($cotizacion->trabajador), '', 0, "L");


		$fpdf::Ln();
		
		// $fpdf::Cell(5, $alto, utf8_decode(':'), '', 0, "L");

		$fpdf::SetFont('Arial','',$tam_font);
		$_y = $fpdf::GetY();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::MultiCell(134, $alto, utf8_decode(strtoupper($cotizacion->cliente)), '', "L");
        $fpdf::SetXY(134,$_y);
    	$fpdf::SetFont('Arial','B',$tam_font);
        $fpdf::SetX(125);
    	$fpdf::Cell(25, $alto, utf8_decode('Teléfono:'), '', 0, "L");
    	$fpdf::SetFont('Arial','',$tam_font);
        $fpdf::SetX(140); // Ajustar hacia la derecha
    	$fpdf::Cell(58, $alto, utf8_decode($cotizacion->telefono_tra), '', 0, "L");



		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
       	$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(8, $alto, utf8_decode(($cotizacion->tipo_cliente=='PN'?'DNI':'RUC').':'), '', 0, "L");
		// $fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
    	$fpdf::SetFont('Arial','',$tam_font);
	    $fpdf::Cell(111, $alto, utf8_decode(strtoupper($cotizacion->doc_cliente)), '', 0, "L");
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::SetX(125);
		$fpdf::Cell(25, $alto, utf8_decode('Email:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::SetX(140);
		$fpdf::Cell(58, $alto, utf8_decode($cotizacion->correo_tra), '', 0, "L");
		
		
		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(119, $alto, utf8_decode(''), '', 0, "L");
		$fpdf::SetX(125);
		$fpdf::Cell(25, $alto, utf8_decode('Fecha:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::SetX(140);
		$fpdf::Cell(58, $alto, utf8_decode($cotizacion->fecha), '', 0, "L");
		

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(80, $alto, utf8_decode('De nuestra consideración'), '', 0, "L");
		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::Cell(150, $alto, utf8_decode("Por medio de la presente hacemos llegar nuestra mejor oferta para el modelo $marca solicitado:"), '', 0, "L");
		
		
		$fpdf::Ln(10);
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Marca:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(58, $alto, utf8_decode($marca), '', 0, "L");

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Modelo:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(58, $alto, utf8_decode($modelo), '', 0, "L");

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Versión:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(58, $alto, utf8_decode($version), '', 0, "L");

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(25, $alto, utf8_decode('Año:'), '', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(58, $alto, utf8_decode($anio), '', 0, "L");

		$fpdf::Ln();
		$fpdf::SetXY(15, $fpdf::GetY());
		#$fpdf::Cell(200, $alto, utf8_decode('HOLAHAIAIAIA'), '', 0, "L");

		if(!is_null($urlImagen) && $urlImagen != '') {
		    $fpdf::Image("storage$urlImagen", $fpdf::GetX() +45, $fpdf::GetY()-25, 95,120);
			##$fpdf::Image("storage$urlImagen",45,120,-120);
			##$fpdf::Cell(195,$alto, $fpdf::Image("storage$urlImagen", $fpdf::GetX(), $fpdf::GetY(),45,120),0, 'C'); 
			#$fpdf::Cell(195,$alto, $fpdf::Image("storage$urlImagen", $fpdf::GetX(), $fpdf::GetY(), 45,120),1, 'C'); 
		}
		$fpdf::Ln(5);
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::SetXY(15, $fpdf::GetY() +60);
		$fpdf::Cell(195, $alto, utf8_decode('Imagen Referencial'), '', 0, "C");

		$fpdf::Ln(10);
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font+2);
		$fpdf::Cell(50, $alto, utf8_decode('Precio Vehículo'), '', 0, "L");

		$fpdf::Ln(8);
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(50, $alto, utf8_decode('Precio Lista'),'', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(90, $alto, utf8_decode(""),'', 0, "L");
		$fpdf::Cell(10, $alto, utf8_decode("$cotizacion->moneda"),'', 0, "R");
		$fpdf::Cell(30, $alto, utf8_decode(number_format($precioAuto,2,'.',',')),'', 0, "R");
		$fpdf::Ln();

		foreach ($descuentos as $item) {
			$fpdf::SetXY(15, $fpdf::GetY());
			$fpdf::SetFont('Arial','B',$tam_font);
			$fpdf::Cell(50, $alto, utf8_decode("$item->descripcion"),'', 0, "L");
			$fpdf::SetFont('Arial','',$tam_font);
			$fpdf::Cell(90, $alto, utf8_decode(""),'', 0, "L");
			$fpdf::Cell(10, $alto, utf8_decode("$cotizacion->moneda"),'', 0, "R");
			$fpdf::Cell(30, $alto, utf8_decode(number_format($item->subTotal,2,'.',',')),'', 0, "R");
			$fpdf::Ln();
		}

		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(50, $alto, utf8_decode("Precio"),'', 0, "L");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(90, $alto, utf8_decode(""),'', 0, "L");
		$fpdf::Cell(10, $alto, utf8_decode("$cotizacion->moneda"),'', 0, "R");
		$fpdf::Cell(30, $alto, utf8_decode(number_format($cotizacion->subTotal,2,'.',',')),'', 0, "R");
		$fpdf::Ln(10);
		
		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font+2);
		$fpdf::Cell(50, $alto, utf8_decode('Adicionales'), '', 0, "L");
		$fpdf::Ln();

		foreach ($adicionales as $item) {
			$fpdf::SetXY(15, $fpdf::GetY());
			$fpdf::SetFont('Arial','B',$tam_font);
			$fpdf::Cell(50, $alto, utf8_decode("$item->descripcion"),'', 0, "L");
			$fpdf::SetFont('Arial','',$tam_font);
			$fpdf::Cell(90, $alto, utf8_decode(""),'', 0, "L");
			$fpdf::Cell(10, $alto, utf8_decode("$cotizacion->moneda"),'', 0, "R");
			$fpdf::Cell(30, $alto, utf8_decode(number_format($item->subTotal,2,'.',',')),'', 0, "R");
			$fpdf::Ln();
		}

		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(120, $alto, utf8_decode("Precio Total"),'', 0, "R");
		$fpdf::SetFont('Arial','',$tam_font);
		$fpdf::Cell(30, $alto, utf8_decode("$cotizacion->moneda"),'', 0, "R");
		$fpdf::Cell(30, $alto, utf8_decode(number_format($cotizacion->subTotal,2,'.',',')),'', 0, "R");
		$fpdf::Ln(25);



		$fpdf::SetXY(15, $fpdf::GetY());
		$fpdf::SetFont('Arial','B',$tam_font);
		$fpdf::Cell(80, $alto+2, utf8_decode("$cotizacion->trabajador"),'T', 0, "C");
		
		

		// $fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");
        // $fpdf::SetXY(153,$_y);

	
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto, utf8_decode('Dirección'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->direccion_cliente)), 'R', "L");
        // $fpdf::SetXY(153,$_y);

		// $fpdf::Ln();
		// $fpdf::SetXY(15, $fpdf::GetY());
       

		// $_y = $fpdf::GetY();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($cotizacion->fecha)), 'R', "L");
        // $fpdf::SetXY(153,$_y);

		// $fpdf::Ln();
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto, utf8_decode('Moneda'), 'L', 0, "L");
		// $fpdf::Cell(5, $alto, utf8_decode(':'), 0, 0, "L");

		// $_y = $fpdf::GetY();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(153, $alto, utf8_decode(strtoupper($tipoMoneda)), 'R', "L");
        // $fpdf::SetXY(153,$_y);

		// $fpdf::Ln();
		// $fpdf::SetXY(15, $fpdf::GetY());
       	// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto, utf8_decode('Asesor'), 'LB', 0, "L");
		// $fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "L");

		// $_y = $fpdf::GetY();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(133, $alto, utf8_decode(strtoupper($cotizacion->trabajador)), 'B', "L");
		// $fpdf::SetXY(133,$_y);
	
		// $fpdf::SetFont('Arial','B',$tam_font);
		// $fpdf::Cell(25, $alto, utf8_decode('Télefono'), 'B', 0, "L");
		// $fpdf::Cell(5, $alto, utf8_decode(':'), 'B', 0, "B");

		// $_y = $fpdf::GetY();
    	// $fpdf::SetFont('Arial','',$tam_font);
	    // $fpdf::MultiCell(35, $alto, utf8_decode(strtoupper($cotizacion->telefono_tra)), 'RB', "L");
		// $fpdf::SetXY(35,$_y);
	
		// $fpdf::SetFillColor(255);
		// $fpdf::Ln(12);
		// $alto = 2;   
        // $fpdf::SetXY(15, $fpdf::GetY()+$alto);
        // $_Y_list = $fpdf::GetY();
        // $alto = 6;
        // $fpdf::SetFont('Arial','B',8);
        // $fpdf::Cell(15, $alto, utf8_decode("CÓDIGO"), 1, 0, "C");
        // $fpdf::Cell(15, $alto, utf8_decode("CANT."), 1, 0, "C");
        // $fpdf::Cell(15, $alto, utf8_decode("UND."), 1, 0, "C");
        // $fpdf::Cell(88, $alto, utf8_decode("DESCRIPCIÓN"), 1, 0, "C");
        // $fpdf::Cell(25, $alto, utf8_decode("V. UNITARIO"), 1, 0, "C");
        // $fpdf::Cell(25, $alto, utf8_decode("IMPORTE"), 1, 0, "C");
        // $fpdf::Ln();
        // $fpdf::SetFont('Arial','',7);
        // $alto = 4;
        // $total = 0;

		// foreach ($detalles as $deta) {
		// 	// if (!is_null($deta->idProducto)) {
		// 		$alto2 = $fpdf::GetMultiCellHeight(88,$alto,utf8_decode($deta->descripcion), 1, "C");
         
		// 		$fpdf::SetX(15);
        // 		$fpdf::Cell(15, $alto2, utf8_decode("-"), 'L', 0, "C");
		// 		$fpdf::Cell(15, $alto2, $deta->cantidad, 0, 0, "R");
		// 		$fpdf::Cell(15, $alto2, utf8_decode("UND"), 0, 0, "C");
			
		// 		$_y = $fpdf::GetY();
    	// 		$fpdf::MultiCell(88, $alto, utf8_decode($deta->descripcion), 0, "L");
		// 		$fpdf::SetXY(148,$fpdf::GetY()-$alto2);
			
		// 		$fpdf::Cell(25, $alto2, number_format($deta->precio,2,'.',','), 0, 0, "R");
		// 		$fpdf::Cell(25, $alto2, number_format($deta->subTotal,2,'.',','), 'R', 0, "R");
		// 		$fpdf::Ln();
		// 		$total+=$deta->subTotal;
        // 	// }
		// }

		// $fpdf::SetX(15);
		// if ($total == 0) {
		// 	$fpdf::Cell(183, $alto, utf8_decode("NO EXISTEN ITEMS SELECCIONADOS"), 1, 1, "L");
		// } else {
		// 	$fpdf::Cell(158, $alto, '', 'T', 0, "L");
		// 	// $fpdf::SetX(15);
		// 	$fpdf::SetFont('Arial','B',9);
		// 	$fpdf::Cell(25, $alto, number_format($total,2,'.',','), 'T', 0, "R");
		// }

		// $fpdf::Ln(12);
        // $fpdf::SetX(15);
        // $fpdf::SetFont('Arial','B',9);
      
		// $letras = new EnLetras();
        // // $fpdf::SetFont('helvetica', 'B', 8);
        // $valor = $letras->ValorEnLetras(str_replace(',','',$cotizacion->total), ' '.$tipoMoneda); //letras

		// $son = strtoupper("SON: ".$valor);
        // $fpdf::MultiCell(183, $alto, utf8_decode($son), $borde, "L", true);
		   
		// $fpdf::Ln(10);
        // $fpdf::SetX(15);
       	// $fpdf::Cell(183, $alto, utf8_decode("OBSERVACIÓN"), 'RTL',0, "L", true);
		// $fpdf::Ln();
        
		// $fpdf::SetTextColor(206,3,3);
        // $fpdf::SetX(15);
       	// $fpdf::Cell(183, $alto, utf8_decode("ESTE DOCUMENTO TIENE VALIDEZ DE 07 DÍAS HÁBILES"), 'RBL',0, "L");
		   
		

        // $fpdf::SetXY(138, $fpdf::GetY());
        // $fpdf::SetFont('Arial','',12);
        // $alto = 5;
        
		$urlStorage = \Storage::disk('local_auto_cotizacion')->path('');
		$nameFile = "Cot-$cotizacion->documento.pdf";
		// echo var_dump($urlFicha);
		// dd(is_null($urlFicha) || $urlFicha == ''?'--':'ok');
		if (is_null($urlFicha) || $urlFicha == '') {
			$fpdf::Output("$urlStorage/$nameFile", 'I');
			exit;
		} else {
			$urlFicha = str_replace('/fichas/','', $urlFicha);
			// dd($urlFicha);
			$fpdf::Output("$urlStorage/$nameFile", 'F'); // Se muestra el documento .PDF en el navegador.    */

			$cotizacion = \Storage::disk('local_auto_cotizacion')->path("$nameFile");
			$fichaRelacionada = \Storage::disk('local_ficha')->path("$urlFicha");
			// dd($fileRelacionado);
			$oMerger = new PdfMerge(); //::init();
			// $oMerger->addPDF("CotizacionAutos-".$cotizacion->documento.".pdf", 'all');
			$oMerger->add($cotizacion);
			$oMerger->add($fichaRelacionada);

			$oMerger->merge("$urlStorage/$nameFile");

		
			$path = "/storage/autos_cotizacion/$nameFile"; #\Storage::disk('local_auto_cotizacion')->path("$nameFile");
			
			return Redirect::to($path);

		}
		// $pdf = PDF::loadFile($path);
		// dd($path);
        // return $pdf->stream();
		// unlink("CotizacionAutos-".$cotizacion->documento.".pdf");
		
		// dd("OK");
		// $oMerger->save('save.pdf');
		
		#$fpdf::Output();

        // exit;
    }
	
	public function getMarcas($tipo, Request $request) {
		$marcas = MarcaAuto::where('tipo','=',$tipo)->get();
		
		return ['marcas' => $marcas];
	}

    public function getAll (Request $request) {
    	$cliente 	 = $request->get('cliente');
		$comprobante = $request->get('comprobante');
		$documento = $request->get('documento');
		$placa = $request->get('placa');
		
		$fechaI 	 = $request->get('fechai');
    	$fechaF	 	 = $request->get('fechaf');

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$cotizacion = DB::table('cotizacion')
					  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente')
					  ->where(function ($qq) use ($cliente) {
							$qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
							   ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente. '%');
					  })
					  ->where('cl.documento','LIKE', '%'.$documento.'%')
					  ->where(DB::Raw("CONCAT(cotizacion.serie,'-',cotizacion.numero)"),'LIKE', '%'.$comprobante.'%')
					  ->where(DB::Raw("IFNULL(cotizacion.placa,'')"),'LIKE', '%'.$placa.'%');
		
		if ($fechaI != '') {
			$cotizacion = $cotizacion->where('cotizacion.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$cotizacion = $cotizacion->where('cotizacion.fecha','<=',$fechaF);
		}

		/* if ($filtro != '' && $filtro != 'todo') {
    		switch ($filtro) {
    			case 'cliente':
    				if ($descripcion <> '')	
						$cotizacion = $cotizacion;
						break;
				case 'ruc':
    				if ($descripcion <> '')	
						$cotizacion = $cotizacion;
						break;
				case 'documento':
					$cotizacion = $cotizacion;
					break;
				default:
					$cotizacion = $cotizacion;
					break;
			
    		}
    	}
		*/ 
		$cotizacion =  $cotizacion->select('cotizacion.id','cotizacion.total','cotizacion.placa','cotizacion.kilometraje','cotizacion.marcamodelo',
							'cotizacion.vin', DB::raw("(CASE cotizacion.situacion 
							WHEN 'V' THEN 'VIGENTE'
							WHEN 'A' THEN 'ANULADO' 
							WHEN 'N' THEN 'NO VIGENTE'
							WHEN 'U' THEN 'REVISADO' END) as situaciontexto"),
							DB::Raw("(CASE cotizacion.situacion WHEN 'U' THEN (SELECT CONCAT(ot.situacionFacturado, '@@',
							(CASE ot.situacionFacturado WHEN 'P' THEN 
							(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0'),'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idOrden = ot.id AND v.deleted_at IS NULL AND v.situacion = 'V' AND pd.idCotizacion IS NULL LIMIT 1) 
							ELSE '-'
							END),'@@OD', LPAD(ot.serie,3,'0') ,'-', LPAD(ot.numero,8,'0')) FROM ordentrabajo as ot WHERE ot.id =  (SELECT DISTINCT do.idOrdenTrabajo FROM detalleordentrabajo as do WHERE do.idCotizacion = cotizacion.id AND do.deleted_at IS NULL LIMIT 1)) 
							ELSE 
							CONCAT(cotizacion.situacionFacturado, '@@',
								(CASE cotizacion.situacionFacturado WHEN 'P' THEN
								(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0'),'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idCotizacion = cotizacion.id AND v.deleted_at IS NULL AND v.situacion = 'V' AND pd.idOrden IS NULL LIMIT 1) 
								ELSE '-' END),
								'@@-') END) as params"),
							DB::Raw("CONCAT('C', LPAD(cotizacion.serie,3,'0') ,'-', LPAD(cotizacion.numero,8,'0')) as documento"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),'cl.documento as doc', 
							DB::Raw("DATE_FORMAT(cotizacion.fecha,'%d/%m/%Y') as fecha"),
							'cotizacion.situacion'
						);
							
		$lista = $cotizacion->orderBy('cotizacion.created_at')->get();
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
		
		$lista = $cotizacion->offset(($page-1)*$filas)
				   ->limit($filas)
				   ->get();

		#NVOS PARAMETROS
		$listaF = [];
		foreach($lista as $item) {
			$params = explode('@@', (!is_null($item->params)?$item->params:'-@@-@@-'));
			$listaF[] = [
				'id' => $item->id,
				'total' => $item->total,
				'placa' => $item->placa,
				'kilometraje' => $item->kilometraje,
				'marcamodelo' => $item->marcamodelo,
				'vin' => $item->vin,
				'situaciontexto' => $item->situaciontexto,
				'situacionFacturado' => $params[0],
				'ventaRef' => $params[1],
				'ordenRef' => $params[2],
				'documento' => $item->documento,
				'cliente'=> $item->cliente,
				'doc'=> $item->doc,
				'fecha'=> $item->fecha,
				'situacion'=> $item->situacion
			];
		}
		
    	return ['cotizaciones' => $listaF, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Cotización':' Cotizaciones'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    } 

	public function getCotizacionesExcel (Request $request) {
		$cliente 	 = $request->get('cliente');
		$comprobante = $request->get('comprobante');
		$documento = $request->get('documento');
		$placa = $request->get('placa');
		
		$fechaI 	 = $request->get('fechai');
    	$fechaF	 	 = $request->get('fechaf');

    	$filas 		 = $request->get('filas');
    	$page 		 = $request->get('page');
    	
		$cotizacion = DB::table('cotizacion')
					  ->leftjoin('persona as cl','cl.id','=','cotizacion.idCliente')
					  ->where(function ($qq) use ($cliente) {
							$qq->where('cl.razonSocial','LIKE', '%'.$cliente.'%')
							   ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"),'LIKE', '%'.$cliente. '%');
					  })
					  ->where('cl.documento','LIKE', '%'.$documento.'%')
					  ->where(DB::Raw("CONCAT(cotizacion.serie,'-',cotizacion.numero)"),'LIKE', '%'.$comprobante.'%')
					  ->where(DB::Raw("IFNULL(cotizacion.placa,'')"),'LIKE', '%'.$placa.'%');
		
		if ($fechaI != '') {
			$cotizacion = $cotizacion->where('cotizacion.fecha','>=',$fechaI);
		}

		if ($fechaF != '') {
			$cotizacion = $cotizacion->where('cotizacion.fecha','<=',$fechaF);
		}

		$cotizacion =  $cotizacion->select('cotizacion.id','cotizacion.total','cotizacion.placa','cotizacion.kilometraje','cotizacion.marcamodelo',
							'cotizacion.vin', DB::raw("(CASE cotizacion.situacion 
							WHEN 'V' THEN 'VIGENTE'
							WHEN 'A' THEN 'ANULADO' 
							WHEN 'N' THEN 'NO VIGENTE'
							WHEN 'U' THEN 'REVISADO' END) as situaciontexto"),
							DB::Raw("(CASE cotizacion.situacion WHEN 'U' THEN (SELECT CONCAT(ot.situacionFacturado, '@@',
							(CASE ot.situacionFacturado WHEN 'P' THEN 
							(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0'),'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idOrden = ot.id AND v.deleted_at IS NULL AND v.situacion = 'V' AND pd.idCotizacion IS NULL LIMIT 1) 
							ELSE '-'
							END),'@@OD', LPAD(ot.serie,3,'0') ,'-', LPAD(ot.numero,8,'0')) FROM ordentrabajo as ot WHERE ot.id =  (SELECT DISTINCT do.idOrdenTrabajo FROM detalleordentrabajo as do WHERE do.idCotizacion = cotizacion.id AND do.deleted_at IS NULL)) 
							ELSE 
							CONCAT(cotizacion.situacionFacturado, '@@',
								(CASE cotizacion.situacionFacturado WHEN 'P' THEN
								(SELECT CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0'),'-', LPAD(v.numero,8,'0')) FROM pagodetalle as pd JOIN venta as v ON v.id = pd.idVenta WHERE pd.idCotizacion = cotizacion.id AND v.deleted_at IS NULL AND v.situacion = 'V' AND pd.idOrden IS NULL LIMIT 1) 
								ELSE '-' END),
								'@@-') END) as params"),
							DB::Raw("CONCAT('C', LPAD(cotizacion.serie,3,'0') ,'-', LPAD(cotizacion.numero,8,'0')) as documento"), DB::raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),'cl.documento as doc', DB::Raw("DATE_FORMAT(cotizacion.fecha,'%d/%m/%Y') as fecha"),
							'cotizacion.situacion'
						);
		
		$lista = $cotizacion->orderBy('cotizacion.created_at')->get();
		
		$listaF = [];
		foreach($lista as $item) {
			$params = explode('@@', (!is_null($item->params)?$item->params:'-@@-@@-'));
			$listaF[] = [
				'id' => $item->id,
				'total' => $item->total,
				'placa' => $item->placa,
				'kilometraje' => $item->kilometraje,
				'marcamodelo' => $item->marcamodelo,
				'vin' => $item->vin,
				'situaciontexto' => $item->situaciontexto,
				'situacionFacturado' => $params[0],
				'ventaRef' => $params[1],
				'ordenRef' => $params[2],
				'documento' => $item->documento,
				'cliente'=> $item->cliente,
				'doc'=> $item->doc,
				'fecha'=> $item->fecha,
				'situacion'=> $item->situacion
			];
			

		}
		
		$excel = new PHPExcel(); 
		$excel->setActiveSheetIndex(0);
		$hoja1 = $excel->getActiveSheet();
		$hoja1->setTitle("listado_cotizaciones");
		
		$hoja1->setCellValue('A1','LISTADO DE COTIZACIONES');
		$hoja1->mergeCells('A1:I1');
		$hoja1->getStyle('A1:I1')->applyFromArray($this->estilo_header);

		$hoja1->setCellValue('A2','FECHA');
		$hoja1->setCellValue('B2','CLIENTE');
		$hoja1->setCellValue('C2','NUMERO');
		$hoja1->setCellValue('D2','PLACA/VIN/KILOMETRAJE');
		$hoja1->setCellValue('E2','SITUACIÓN');
		$hoja1->setCellValue('F2','¿SE FACTURÓ?');
		$hoja1->setCellValue('G2','ORDEN REF.');
		$hoja1->setCellValue('H2','VENTA REF');
		$hoja1->setCellValue('I2','TOTAL');
		
		$hoja1->getStyle('A2:I2')->applyFromArray($this->estilo_header);
		$j = 3;

		foreach ($listaF as $elem) {
			// dd($item);
			$item = (Object) $elem;
			$hoja1->setCellValue('A'.$j,$item->fecha);
			$hoja1->setCellValue('B'.$j,$item->doc.'/'.$item->cliente);
			$hoja1->setCellValue('C'.$j,$item->documento);
			$hoja1->setCellValue('D'.$j,$item->placa.'/'.$item->vin.'/'.$item->kilometraje);
			$hoja1->setCellValue('E'.$j,$item->situaciontexto);
			$hoja1->setCellValue('F'.$j,($item->situacionFacturado == 'N'?'No':'Si'));
			$hoja1->setCellValue('G'.$j,$item->ordenRef);
			$hoja1->setCellValue('H'.$j,$item->ventaRef);
			$hoja1->setCellValue('I'.$j,$item->total);

			$hoja1->getStyle('A'.$j.':I'.$j)->applyFromArray($this->estilo_content);
			$j++;
		}

		foreach(range('A','I') as $columnID) 
		{ 
			$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		}

		// foreach(range('F','H') as $columnID) 
		// { 
		// 	$hoja1->getColumnDimension($columnID)->setAutoSize(true); 
		// }

		$objWriter = new PHPExcel_IOFactory($excel);		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="excel_cotizaciones.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	
        exit;
  

		// dd($listaF, $request);
	}
	
   
    public function getAll02(Request $request) {
        $filtro      = $request->get('filtro');
        $descripcion = $request->get('descripcion');
        $documento   = $request->get('documento');
        $cliente     = $request->get('cliente');
        $comprobante = $request->get('comprobante');
        
        $fechaI      = $request->get('fechaI');
        $fechaF      = $request->get('fechaF');
        $filas       = $request->get('filas');
        $page        = $request->get('page');
    
        // Obtener el id del usuario logueado
        $usuarioId = auth()->user()->usuarioId;
    
        // Relacionar la tabla login con trabajador usando el campo correcto (login.usuarioId se relaciona con trabajador.id)
        $trabajador = DB::table('login')
                        ->join('trabajador', 'trabajador.id', '=', 'login.usuarioId') // Relacionar login con trabajador
                        ->where('login.usuarioId', $usuarioId)
                        ->first();
    
        if (!$trabajador) {
            return response()->json(['error' => 'Trabajador no encontrado'], 404);
        }
    
        // Continuar con el filtrado de cotizaciones por el idPersonal del trabajador
        $cotizacion = DB::table('cotizacionauto as cotizacion')
                        ->leftjoin('persona as cl', 'cl.id', '=', 'cotizacion.idCliente')
                        ->leftjoin('trabajador as asesor', 'asesor.id', '=', 'cotizacion.idPersonal')
                        ->where('cotizacion.idPersonal', '=', $trabajador->id) // Filtrar por el id del trabajador logueado
                        ->where('cl.documento', 'LIKE', '%'.$documento.'%')
                        ->where(DB::Raw("CONCAT(cotizacion.serie,'-',cotizacion.numero)"), 'LIKE', '%'.$comprobante.'%')
                        ->where(function ($qq) use ($cliente) {
                            $qq->where('cl.razonSocial', 'LIKE', '%'.$cliente.'%')
                               ->orWhere(DB::Raw("CONCAT(cl.apellidos,' ', cl.nombres)"), 'LIKE', '%'.$cliente.'%');
                        });
        
        if ($fechaI != '') {
            $cotizacion = $cotizacion->where('cotizacion.fecha', '>=', $fechaI);
        }
    
        if ($fechaF != '') {
            $cotizacion = $cotizacion->where('cotizacion.fecha', '<=', $fechaF);
        }
    
        $cotizacion = $cotizacion->orderBy('cotizacion.fecha', 'desc')
                        ->select(
                            'cotizacion.id',
                            DB::Raw("(CASE WHEN cotizacion.tipoMoneda = 'D' THEN 'Dólares' ELSE 'Soles' END) as moneda"),
                            'cotizacion.total',
                            DB::Raw("(CASE cotizacion.situacion 
                                WHEN 'V' THEN 'VIGENTE'
                                WHEN 'A' THEN 'ANULADO' 
                                WHEN 'N' THEN 'NO VIGENTE'
                                WHEN 'U' THEN 'REVISADO' END) as situaciontexto"),
                            DB::Raw("CONCAT('C', LPAD(cotizacion.serie, 3, '0') ,'-', LPAD(cotizacion.numero, 8, '0')) as documento"),
                            DB::Raw("(CASE WHEN cl.razonSocial IS NOT NULL THEN cl.razonSocial ELSE CONCAT(cl.apellidos,' ', cl.nombres) END) as cliente"),
                            'cl.documento as doc',
                            DB::Raw("DATE_FORMAT(cotizacion.fecha,'%d/%m/%Y') as fecha"),
                            'cotizacion.situacion',
                            DB::Raw("CONCAT(asesor.apellidos, ' ', asesor.nombres) AS asesor")
                        );
    
        $lista = $cotizacion->get();
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
    
        $lista = $cotizacion->offset(($page-1) * $filas)
                       ->limit($filas)
                       ->get();
    
        return ['cotizaciones' => $lista, 'cantidad' => ($cantidad<10?'0'.$cantidad:$cantidad).($cantidad==1?' Cotización':' Cotizaciones'), 'page' => $page, 'paginador' => $arrPag, 'inicio' => $inicio, 'fin' => $fin, 'paramInicio' => $paramInicio, 'paramFin' => $paramFin];
    }



	/*public function generarPdf($id){
	// {dd('esto aca'); 
		// $request->get('id');
		// $cotizacion =Cotizacion::find($id);
		// $cliente=Persona::find($cotizacion->idCliente);
		// $trabajdor=Personal::find($cotizacion->idPersonal);
		// dd($cotizacion->idCliente);
		$cotizacion=DB::table('cotizacion as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id',DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"),
							 
					          DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
					         DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','cot.placa','cot.vin','cot.total')
					->first();
		$productos=DetalleCotizacion::withTrashed()->where('idCotizacion',$cotizacion->id)->where('tipoDetalle','P')->get();
	   $sumaProductos=0;
	   if($productos->count()>0){
		   foreach ($productos as $pro) {
			$sumaProductos+=$pro->subTotal;
		}
	   }
		
		$servicios=DetalleCotizacion::withTrashed()->where('idCotizacion',$cotizacion->id)->where('tipoDetalle','S')->get();

		$sumaServicios=0;
		if($servicios->count()>0){
			foreach ($servicios as $ser) {
			 $sumaServicios+=$ser->subTotal;
		 }
		}
		$total=number_format($cotizacion->total,3,'.','');

		$sumaProductos=number_format($sumaProductos,3,'.','');
		
		// $sumaServiciosr=round($sumaServicios,3);
		$sumaServicios=number_format($sumaServicios,3,'.','');
		
		// dd($productos);
		
		$pdf=PDF::loadView('pdf.cotizacion',compact('cotizacion','productos','sumaProductos','servicios','sumaServicios','total'));
		$pdf->setPaper("A4", "portrait");
		// dd($pdf);
		//   return PDF::loadView('pdf.cotizacion')
		//     ->download('archivo.pdf');
		return $pdf->stream('archivo.pdf');
	
	}*/

	/*public function pdfCotizacionAuto ($id) {
		$cotizacion=DB::table('cotizacionauto as cot')
		            ->leftJoin('persona as cli','cot.idCliente','=','cli.id')
					->leftJoin('trabajador as tra','cot.idPersonal','=','tra.id')
					->where('cot.id',$id)
					->select('cot.id',DB::raw("CONCAT('C', LPAD(cot.serie,3,'0') ,'-', LPAD(cot.numero,8,'0')) as documento"),
							 
					          DB::raw("(CASE WHEN cli.razonSocial IS NOT NULL THEN cli.razonSocial ELSE CONCAT(cli.apellidos,' ', cli.nombres) END) as cliente"),
					         DB::raw("DATE_FORMAT(cot.fecha,'%d/%m/%Y') as fecha"),DB::raw("CONCAT(tra.apellidos,' ',tra.nombres) as trabajador"),'tra.telefono as telefono_tra','tra.correoE as correo_tra','cot.total','cli.documento as documento_cli',DB::Raw("(CASE WHEN cot.tipoMoneda = 'D' THEN '$' ELSE 'S/' END) as moneda"))
					->first();
		$detalles=DetalleCotizacionAuto::withTrashed()->where('idCotizacion',$cotizacion->id)->get();
	    $sumaDetalles=0;
	    if($detalles->count()>0){
		   foreach ($detalles as $pro) {
			$sumaDetalles+=$pro->subTotal;
		}
	   }
		
		// $servicios=DetalleCotizacion::withTrashed()->where('idCotizacion',$cotizacion->id)->where('tipoDetalle','S')->get();

		// $sumaServicios=0;
		// if($servicios->count()>0){
		// 	foreach ($servicios as $ser) {
		// 	 $sumaServicios+=$ser->subTotal;
		//  }
		// }
		$total=number_format($cotizacion->total,3,'.','');
		$tipo  = $cotizacion->moneda;
		// $sumaProductos=number_format($sumaProductos,3,'.','');
		
		// // $sumaServiciosr=round($sumaServicios,3);
		// $sumaServicios=number_format($sumaServicios,3,'.','');
		
		// dd($productos);
		
		$pdf=PDF::loadView('pdf.cotizacionauto',compact('cotizacion','detalles','sumaDetalles','total','tipo'));
		$pdf->setPaper("A4", "portrait");
		// dd($pdf);
		//   return PDF::loadView('pdf.cotizacion')
		//     ->download('archivo.pdf');
		return $pdf->stream('archivo.pdf');
	
	}*/
	
	public function obtenerProductosCotizacion (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$psStock = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
						->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
						->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
						->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
						->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
						->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
						->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
						->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
						->where('sp.idAlmacen', $this->almacenId)
						->whereRaw('sp.idProducto = producto.id')
						->where(DB::Raw("sp.totalCompras - sp.totalVentas - sp.totalIncidencias"),'=', '0')
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
							DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
							DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
							DB::Raw("'Producto' as tipo"), 
							DB::Raw("(CASE producto.tipoProducto 
							WHEN 'A'  THEN 'Accesorio/Repuesto' 
							WHEN 'LL' THEN 'Neumáticos' 
							WHEN 'I'  THEN 'Insumos' 
							WHEN 'B'  THEN 'Baterías' 
							ELSE 'MUELLES' END) as tipoprod"),
						// DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
						DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
						DB::Raw("'0' as stock"),
						DB::Raw("'-' as precioS"),
						DB::Raw("'-' as precioD"),
						DB::Raw("'0' as precioSoles"),
						DB::Raw("'0' as precioDolares"),
						DB::Raw("'0' as lote_id"),
						'producto.nombre as desc_corta'
					)
					->orderBy('sp.id','ASC');

			$pr   = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
			   	   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->leftjoin('lote as l','l.idProducto','=','producto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idProducto','=','l.idProducto')
				   ->whereNull('spt.deleted_at')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.idLote = l.id')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
				   ->where('spt.tipo','=','P')
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
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"), 
   					   DB::Raw("(CASE producto.tipoProducto 
						  WHEN 'A'  THEN 'Accesorio/Repuesto' 
						  WHEN 'LL' THEN 'Neumáticos' 
						  WHEN 'I'  THEN 'Insumos' 
						  WHEN 'B'  THEN 'Baterías' 
						  ELSE 'MUELLES' END) as tipoprod"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD"),
					   'l.precioSoles',
					   'l.precioDolares',
					   'l.id as lote_id',
					   'producto.nombre as desc_corta'  
					)
				   ->orderBy('l.id','ASC');


			#SERVICIOS

			$sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
					  ->where('servicio.nombre','LIKE','%'.$desc.'%')
					  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), 
					  DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), 
					  DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'Servicio' as tipoprod"), DB::Raw("'-' as tipollanta"), 
					  DB::Raw("'0' as stock"), DB::Raw("FORMAT(servicio.precio,2) as precioS"), DB::Raw("'-' as precioD"), 
					  'servicio.precio as precioSoles',
					  DB::Raw("'-' as precioDolares"),
					  DB::Raw("'-' as lote_id"),
					  'servicio.nombre as desc_corta'  
					  )
					  ->unionAll($pr)
					  ->unionAll($psStock)
					  ->get();
    		// dd($sr);
			// $sr  = $sr->unionAll($pr);

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
			// 		  ->unionAll($pr)
			// 		  ->unionAll($sr)
			// 		  ->get();

			return ['productos' => $sr];
	    }
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
			   	   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->leftjoin('lote as l','l.idProducto','=','producto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idProducto','=','l.idProducto')
				   ->whereNull('spt.deleted_at')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.idLote = l.id')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
				   ->where('spt.tipo','=','P')
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
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"), 
   					   DB::Raw("(CASE producto.tipoProducto 
						  WHEN 'A'  THEN 'Accesorio/Repuesto' 
						  WHEN 'LL' THEN 'Neumáticos' 
						  WHEN 'I'  THEN 'Insumos' 
						  WHEN 'B'  THEN 'Baterías' 
						  ELSE 'MUELLES' END) as tipoprod"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD"),
					   'l.precioSoles',
					   'l.precioDolares',
					   'l.id as lote_id',
					   'producto.nombre as desc_corta'
					)
				   ->orderBy('l.id','ASC');


			#SERVICIOS

			$sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
					  ->where('servicio.nombre','LIKE','%'.$desc.'%')
					  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), 
					  DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), 
					  DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'Servicio' as tipoprod"), DB::Raw("'-' as tipollanta"), 
					  DB::Raw("'0' as stock"), DB::Raw("FORMAT(servicio.precio,2) as precioS"), DB::Raw("'-' as precioD"), 
					  'servicio.precio as precioSoles',
					  DB::Raw("'-' as precioDolares"),
					  DB::Raw("'-' as lote_id"),
					  'servicio.nombre as desc_corta')
					  ->unionAll($pr)
					  ->get();
    		// dd($sr);
			// $sr  = $sr->unionAll($pr);

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
			// 		  ->unionAll($pr)
			// 		  ->unionAll($sr)
			// 		  ->get();

			return ['productos' => $sr];
	    }
	}

	public function obtenerProductosAutos (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$pr   = Auto::leftjoin('marcaauto as mt','mt.id','=','auto.marcaId')
				   ->leftjoin('stockauto as sp','sp.idAuto','=','auto.id')
				   ->leftjoin('loteauto as l','l.idAuto','=','auto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idAuto','=','l.idAuto')
				   ->whereNull('spt.deleted_at')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.idLoteAuto = l.id')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
				   ->where('spt.tipo','=','A')
				   ->where(function ($qq) use ($desc) {
						$qq->where('auto.codproveedor','LIKE','%'.$desc.'%')
						->orwhere('auto.version','LIKE','%'.$desc.'%')
						->orwhere('auto.transmision','LIKE','%'.$desc.'%')
						->orwhere('auto.descripcion','LIKE','%'.$desc.'%')
						->orwhere('mt.nombre','LIKE','%'.$desc.'%')
						->orwhere('l.descripcionadicional','LIKE','%'.$desc.'%');
					})
				   ->select('auto.id', DB::Raw("CONCAT(auto.descripcion, ', Marca: ',mt.nombre,', Versión: ', auto.version,', Transmisión: ', auto.transmision) as descripcion"), 
				      'l.descripcionadicional',
   					   DB::Raw("'Auto' as tipo"), 
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD"),
					  'l.precioSoles',
					  'l.precioDolares',
					  'l.id as lote_id'  
					)
				   ->orderBy('l.id','ASC')
				   ->get();

					
			return ['productos' => $pr];
	    }
	
	}

    public function obtenerProductosMov (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#PRODUCTOS
			$pr   = Producto::leftjoin('marcaaccesorio as ma','ma.id','=','producto.idMarca')
				   ->leftjoin('marcaauto as mt','mt.id','=','producto.idMarcaAuto')
				   ->leftjoin('marcallanta as ml','ml.id','=','producto.idMarcaLlanta')
				   ->leftjoin('sistemaauto as sa','sa.id','=','producto.idSistema')
				   ->leftjoin('modelollanta as mol','mol.id','=','producto.idModeloLlanta')
			   	   ->leftjoin('marcabateria as mb','mb.id','=','producto.idMarcaBateria')
				   ->leftjoin('modelobateria as modb','modb.id','=','producto.idModeloBateria')
				   ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   ->leftjoin('lote as l','l.idProducto','=','producto.id')
				   ->leftjoin('stockproductodetalle as spt','spt.idProducto','=','l.idProducto')
				   ->whereNull('spt.deleted_at')
				   ->where('sp.idAlmacen','=',$this->almacenId)
				   ->where('l.idAlmacen','=',$this->almacenId)
				   ->where('l.situacion','=','V')
				   ->whereRaw('spt.idLote = l.id')
				   ->whereRaw('spt.precioSoles = l.precioSoles')
				   ->where('spt.stock','>','0')
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
				   	   DB::Raw("(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL THEN producto.modelo ELSE '-' END) END) as modelo"), DB::Raw("(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema"), DB::Raw("'-' as tiempo"),
   					   DB::Raw("(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida"),
   					   DB::Raw("'Producto' as tipo"),
					   // DB::Raw("(CASE WHEN producto.tipoProducto = 'LL' THEN 'Neumáticos' ELSE (CASE WHEN producto.tipoProducto = 'A' THEN 'Accesorios/Repuestos' ELSE 'Insumos' END) END) as tipo"),
					   DB::Raw("(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta"),
					   DB::Raw("spt.stock"),
					   DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
				 	   DB::Raw("FORMAT(l.precioDolares,2) as precioD"),
						'l.precioSoles',
						'l.precioDolares',
					 	'l.id as lote_id'  
					)
				   ->orderBy('l.id','ASC')
				   ->get();


			#SERVICIOS

			// $sr    =  Servicio::leftJoin('categoriaservicio as ct','ct.id','=','servicio.idCategoriaServicio')
			// 		  ->where('servicio.nombre','LIKE',$desc.'%')
			// 		  ->select('servicio.id', DB::Raw("CONCAT(servicio.nombre,' - ',servicio.tipoVehiculo) as nombre"), DB::Raw("'' as nombre2"), DB::Raw("'-' as modelo"), DB::Raw("'-' as sistema"), DB::Raw("CONCAT(servicio.tiempoEjecucion,' ',servicio.unidad) as tiempo"), DB::Raw("'-' as medida"), DB::Raw("'Servicio' as tipo"), DB::Raw("'-' as tipollanta"), DB::Raw("'0' as stock"), DB::Raw("FORMAT(servicio.precio,2) as precioS"), DB::Raw("'-' as precioD"))
			// 		  ->unionAll($pr)
			// 		  ->get();

			// $sr  = $sr->unionAll($pr);

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
			// 		  ->unionAll($pr)
			// 		  ->unionAll($sr)
			// 		  ->get();

			return ['productos' => $pr];
	    }
	}

	# PARA COTIZACIONES DE AUTOS
	public function obtenerAutos (Request $request) {
		$desc = $request->get('descripcion');
		#AUTOS
		if ( !is_null(trim($desc)) && $desc != '' ) {
		$at    =  Auto::leftJoin('marcaauto as mt','mt.id','=','auto.marcaId')
					->leftjoin('stockauto as sa','sa.idAuto','=','auto.id')
				 	// ->leftjoin('stockproducto as sp','sp.idProducto','=','producto.id')
				   	->leftjoin('loteauto as l','l.idAuto','=','auto.id')
				   	->leftjoin('stockproductodetalle as spt','spt.idAuto','=','l.idAuto')
					->where('sa.idAlmacen','=',$this->almacenId)
					->where('l.idAlmacen','=',$this->almacenId)
					->where('l.situacion','=','V')
					->whereRaw('spt.precioSoles = l.precioSoles')
					->where('spt.stock','>','0')
					->where('spt.tipo','=','A')
					->where(function ($qq) use ($desc) {
						$qq->where('auto.codproveedor','LIKE','%'.$desc.'%')
						->orwhere('auto.version','LIKE','%'.$desc.'%')
						->orwhere('auto.transmision','LIKE','%'.$desc.'%')
						->orwhere('auto.descripcion','LIKE','%'.$desc.'%')
						->orwhere('mt.nombre','LIKE','%'.$desc.'%')
						->orwhere('l.descripcionadicional','LIKE','%'.$desc.'%');
					})
					->select('l.id as idlote','auto.id', 'mt.nombre as tipo',
					DB::Raw("CONCAT('Cod. Proveedor:',auto.codproveedor,', Marca: ',mt.nombre,', Versión: ', auto.version,', Transmisión: ', auto.transmision,', Descripción:', auto.descripcion) as descripcion"), 
					'l.descripcionadicional', 
					DB::Raw("(sa.totalCompras-sa.totalVentas-sa.totalIncidencias) as stock"), DB::Raw("spt.stock"), 
					DB::Raw("l.precioSoles as precioS"), DB::Raw("l.precioDolares as precioD"), 'auto.id as idAuto')
					->groupBy('l.id')
					->get();

			return ['autos' => $at];
		}
	}

	public function obtenerAutosMov (Request $request) {
		$desc = $request->get('descripcion');
		if ( !is_null(trim($desc)) && $desc != '' ) {
			#AUTOS
			$pr   = Auto::leftjoin('marcaauto as mt','mt.id','=','auto.marcaId')
					->leftjoin('stockauto as sp','sp.idAuto','=','auto.id')
					->leftjoin('loteauto as l','l.idAuto','=','auto.id')
					->leftjoin('stockproductodetalle as spt','spt.idAuto','=','l.idAuto')
					->whereNull('spt.deleted_at')
					->where('sp.idAlmacen','=',$this->almacenId)
					->where('l.idAlmacen','=',$this->almacenId)
					->where('l.situacion','=','V')
					->whereRaw('spt.idLoteAuto = l.id')
					->whereRaw('spt.precioSoles = l.precioSoles')
					->where('spt.stock','>','0')
					->where('spt.tipo','=','A')
					->where(function ($qq) use ($desc) {
						$qq->where('auto.codproveedor','LIKE','%'.$desc.'%')
						->orwhere('auto.version','LIKE','%'.$desc.'%')
						->orwhere('auto.transmision','LIKE','%'.$desc.'%')
						->orwhere('auto.descripcion','LIKE','%'.$desc.'%')
						->orwhere('mt.nombre','LIKE','%'.$desc.'%')
						->orwhere('l.descripcionadicional','LIKE','%'.$desc.'%');
					})
					->select('auto.id', DB::Raw("CONCAT('Cod. Proveedor:',auto.codproveedor,', Marca: ',mt.nombre,', Versión: ', auto.version,', Transmisión: ', auto.transmision,', Descripción:', auto.descripcion) as descripcion"), 
					'l.descripcionadicional',
						DB::Raw("'Auto' as tipo"), 
						DB::Raw("spt.stock"),
						DB::Raw("FORMAT(l.precioSoles,2) as precioS"),
						DB::Raw("FORMAT(l.precioDolares,2) as precioD"),
					'l.precioSoles',
					'l.precioDolares',
					'l.id as lote_id'  
					)
					->orderBy('l.id','ASC')
					->get();


		
			return ['autos' => $pr];
	    }
	}

    public function getAuto ($id, Request $request) {
    	$auto = Auto::find($id);
    
    	if (!is_null($auto)) {
    		$respuesta = ['estado' => true, 'auto' => $auto];
    	} else {
    		$respuesta = ['estado' => false];
    	}

    	return $respuesta;
    }

	public function getDetalles ($id, Request $request) {
		$detalles = DB::table('cotizacion')
					->leftJoin('detallecotizacion as det','det.idCotizacion','=','cotizacion.id')
					->where('cotizacion.id','=',$id)
					->whereNull('det.deleted_at')
					->select('det.cantidad','det.descripcion', DB::Raw("(CASE WHEN det.tipoDetalle = 'S' THEN 'Servicio' ELSE (CASE WHEN det.tipoDetalle = 'P' THEN 'Producto' ELSE 'Auto' END) END) as tipo"),
						DB::Raw("FORMAT(det.precio,2) as precio"),
						DB::Raw("FORMAT(det.precioReferencial,2) as precioRef"),
						DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id', DB::Raw("FORMAT(cotizacion.total,2) as total"))
					->orderBy('det.tipoDetalle','ASC')
					->get();

		$total = 0;
		if (count($detalles)) {
			$total = $detalles[0]->total;
		}
 
    	return ['detalles' => $detalles,'total' => $total];
	}

	public function getDetalles02 ($id, Request $request) {
		$detalles = DB::table('cotizacionauto as cotizacion')
					->leftJoin('detallecotizacionauto as det','det.idCotizacion','=','cotizacion.id')
					->where('cotizacion.id','=',$id)
					->select('cotizacion.tipoMoneda','det.cantidad','det.descripcion', DB::Raw("(CASE WHEN det.tipoDetalle = 'O' THEN '-' ELSE 'Auto' END) as tipo"),
						DB::Raw("FORMAT(det.precio,2) as precio"),
						DB::Raw("FORMAT(det.subTotal,2) as subTotal"),
							'det.item','det.id', DB::Raw("FORMAT(cotizacion.total,2) as total"))
					->orderBy('det.item','ASC')
					->get();

		$total = 0;
		$tipo = 'D';
		if (count($detalles)) {
			$total = $detalles[0]->total;
			$tipo = $detalles[0]->tipoMoneda;
		}
 
    	return ['detalles' => $detalles,'total' => $total, 'tipo' => ($tipo == 'D'?'$':'S/')];
	}


	public function getCorrelativo (Request $request) {
		$serie = Serie::where('idLocal','=',$this->tiendaId)->where('tipoLocal','=','T')
			->where('tipoDocumento','=','C')
			->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero+1,8,'0')) as numero"))
			->first();
		
		return ['numero' => $serie->numero];
	}

	public function obtenerCotizaciones (Request $request) {  
		$placa = $request->get('placa');
		$nro   = $request->get('descripcion');
		$clienteId = $request->get('clienteId');

		$cotizacion = Cotizacion::where('placa','=',$placa)
					  ->where('situacion','=','V')
					  ->where('idCliente','=',$clienteId);
		if ($nro != '') { 
			$cotizacion  = $cotizacion->where(DB::Raw("CONCAT(serie,'-',numero)"),'LIKE', '%'.$nro.'%');
		}

		$cotizacion = $cotizacion->select(DB::Raw("CONCAT('C', LPAD(serie,2,'0') ,'-', LPAD(numero,8,'0')) as numero"), 
		DB::Raw("DATE_FORMAT(fecha,'%d/%m/%Y') as fecha"),DB::Raw("FORMAT(total,2) as total2"),'id', 'total')
		->get();
		return ['cotizaciones' => $cotizacion];
	}
	
	public function guardarCotizacion(Request $request) {
		$errors = $this->validar($request);
		$id = 0;
		if (count($errors) > 0 ) {
			return ['errores' => $errors, 'estado' => false];
		} else {
			$id = $request->get('id');
			$idCotizacion = $request->get('idCotizacion');
			$band = true;
			$errors = [];
			DB::beginTransaction();
			try{
				if ($idCotizacion != '0') {
					DetalleCotizacion::where('idCotizacion',$idCotizacion)
					->update(['deleted_at' => date('Y-m-d H:i:s')]);
				}
	
				
				$id = Auth::user()->usuarioId;
				
				if ($idCotizacion != '0') {
					$venta = Cotizacion::find($idCotizacion);
				} else {
					$venta = new Cotizacion;
				}
				$venta->fecha = $request->get('fecha');
				$venta->porcentajeDescuento = $request->get('descuento');
				$venta->totalDescuento = $request->get('total_descuento');
				$venta->placa = $request->get('placa')!=null?$request->get('placa'):'-';
				$venta->kilometraje = $request->get('kilometraje')!=null?$request->get('kilometraje'):'-';
				$venta->vin = $request->get('vin')!=null?$request->get('vin'):'-';
				$venta->marcamodelo = $request->get('marca_modelo') != null?$request->get('marca_modelo'):'-';

				$venta->subTotal = $request->get('subtotalDoc');
				$venta->igv = $request->get('igvDoc');
				$venta->total = $request->get('totalDoc');
				$venta->idTienda = $this->tiendaId;
				$venta->idAlmacenSalida = $this->almacenId;
				$cliente = Persona::where('documento','=',$request->get('documento'))
							->where('tipoPersona','=','C')
							// ->where('tipoDocumento','=',($request->get('tipodocumento') == 'B'?'PN':'PJ'))
							->select('id')
							->first();
				$venta->idCliente = $cliente->id;
				$venta->idPersonal = Auth::user()->usuarioId;

				if ($idCotizacion == '0') {
					$serie = Serie::where('idLocal','=',$venta->idTienda)->where('tipoLocal','=','T')
							->where('tipoDocumento','=','C')
							->first();

					$venta->semanaActual = date('W',strtotime($venta->fecha));
					$venta->serie = $serie->serie;
					$venta->numero = $serie->numero + 1;
					$serie->numero = $venta->numero;
					$serie->update();
					$venta->save();
				} else {
					$venta->update();
				}
				$id = $venta->id;
			

				$detalles3  = explode(',',$request->get('listDetalles'));
				$i = 1;
				if (count($detalles3) > 0 && $request->get('listDetalles') != '') {
					foreach ($detalles3 as $det) {
						$detalle = new DetalleCotizacion;
						$detalle->item = $i;
						$detalle->descripcion = $request->get('txtproducto'.$det);
						$detalle->cantidad = $request->get('txtcantidad'.$det);
						$detalle->porcentajeDescuento = $request->get('porcdescuento'.$det);
						$detalle->precioReferencial = $request->get('txtprecio'.$det);
						
						if (!is_null($detalle->porcentajeDescuento) && $detalle->porcentajeDescuento != '' && $detalle->porcentajeDescuento != '0') {
							$detalle->precio = $request->get('txtdescuentounit'.$det);
						} else {
							$detalle->precio = $detalle->precioReferencial;
						}
						$detalle->totalDescuento = $request->get('txtdescuento'.$det);
						$detalle->subTotal = $request->get('txtsubtototal'.$det);
						

						$tipo = $request->get('tipo'.$det);
						if ($tipo == 'Servicio') {
							$t = 'S';
						} elseif ($tipo == 'Producto') {
							$t = 'P';
						} else {
							$t = 'A';
						}
						$detalle->tipoDetalle = $t;

						if ($t == 'P') {
							$detalle->idProducto = $request->get('productoid'.$det);
							if ($request->get('lote'.$det) != '0') {
								$detalle->idLote = $request->get('lote'.$det);
							}
						} elseif ($t == 'A') {
							$detalle->idAuto = $request->get('productoid'.$det);
						} else {
							$detalle->idServicio = $request->get('productoid'.$det);
						}
						$detalle->idCotizacion = $id;
						$detalle->save();
						$i++;
					}
				}

				$errors[] = 'Cotización Registrada Correctamente';
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
	}

	public function guardarcotizacionAuto (Request $request) {
		$errors = $this->validarCotizacionAuto($request);
		// dd($request);
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
				
				$venta = new CotizacionAuto;
				$venta->fecha = $request->get('fecha');
				$venta->subTotal = $request->get('subtotalDoc');
				$venta->igv = $request->get('igvDoc');
				$venta->total = $request->get('totalDoc');
				$venta->idTienda = $this->tiendaId;
				$venta->idAlmacenSalida = $this->almacenId;
				$cliente = Persona::where('documento','=',$request->get('documento'))
							->where('tipoPersona','=','C')
							// ->where('tipoDocumento','=',($request->get('tipodocumento') == 'B'?'PN':'PJ'))
							->select('id')
							->first();
				$venta->idCliente = $cliente->id;

				$serie = Serie::where('idLocal','=',$venta->idTienda)->where('tipoLocal','=','T')
						->where('tipoDocumento','=','C')
						->first();
				$venta->serie = $serie->serie;
				$venta->numero = $serie->numero + 1;
				$venta->idPersonal = Auth::user()->usuarioId;
				$venta->semanaActual = date('W',strtotime($venta->fecha));
				$venta->tipoMoneda  = $request->get('tipoMoneda');
				
				if ($request->get('oportunidadId') != '0') {
					$venta->idOportunidad = $request->get('oportunidadId');
					
					// $op = Oportunidad::find($venta->idOportunidad);
					// if (!is_null($op)) {
					// 	$op->situacion = 'C';
					// 	$op->update();
					// }
				}
			
				$venta->save();
				$id = $venta->id;
			
				$serie->numero = $venta->numero;
				$serie->update();

				$detalles3  = explode(',',$request->get('listDetalles'));
				$i = 1;
				if (count($detalles3) > 0 && $request->get('listDetalles') != '') {
					foreach ($detalles3 as $det) {
						$detalle = new DetalleCotizacionAuto;
						$detalle->item = $i;
						$detalle->descripcion = $request->get('txtproducto'.$det);
						$detalle->cantidad = $request->get('txtcantidad'.$det);
						$detalle->precio = $request->get('txtprecio'.$det);
						$detalle->subTotal = $request->get('txtsubtototal'.$det);
						
						$detalle->tipoDetalle = ($request->get('tipo'.$det) == 'Auto'?'A':'D');

						if ($detalle->tipoDetalle == 'A') {
							$detalle->idAuto = $request->get('productoid'.$det);
							$detalle->idLote = $request->get('loteid'.$det);
						}
						$detalle->idCotizacion = $id;
						// dd($detalle);
						$detalle->save();
						$i++;
					}
				}

				$errors[] = 'Cotización Registrada Correctamente';
		
			}catch(\Exception $ex){
				$errors[] = $ex->getMessage();
				$band = false;
				DB::rollback();
			}
		
			DB::commit();
		
			return ['errores' => (object)$errors, 'estado' => $band, 'id' => $id];
		}
	}

	public function validar (Request $request) {
		$reglas = [
            'fecha'=>  'required',
            'numero'=> 'required',
			'documento'=> 'required|numeric|digits_between:8,11',
			'cliente'=> 'required',
			'placa'	=> 'nullable',
			'kilometraje'	=> 'nullable',
			'vin'	=> 'nullable',
			'tiempo'	=> 'nullable',
			'listProductos' => 'nullable',
			'listServicios' => 'nullable',
			'listAutos'	  => 'nullable',
			'listDetalles' => 'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric',
			'idCotizacion' => 'required',
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'numero.required'=> 'Indique N°',
            'documento.required'=> 'Indique Documento',
			'cliente.required'=> 'Registre Cliente',
			'placa.required'=> 'Indique Placa',
			'vin.required'=> 'Indique Vin',
			'tiempo.required' => 'Indique Tiempo',
    		'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoOperacion.required' => 'Indique Tipo de Operación',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
			'listDetalles.required'=> 'Indique Detalles a Cotización',
			'idCotizacion.required'	=> 'ID de Cotización no Definido'
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function validarCotizacionAuto (Request $request) {
		$reglas = [
            'fecha'=>  'required',
            'numero'=> 'required',
			'documento'=> 'required|numeric|digits_between:8,11',
			'cliente'=> 'required',
			'listProductos' => 'nullable',
			'listServicios' => 'nullable',
			'listAutos'	  => 'nullable',
			'listDetalles' => 'required',
			'tipoMoneda'	=> 'required',
			'subtotalDoc' => 'required|numeric',
            'igvDoc'      => 'required|numeric',
			'totalDoc'    => 'required|numeric'
        ];

        $mensajes = [
            'fecha.required'=> 'Indique Fecha',
            'numero.required'=> 'Indique N°',
            'documento.required'=> 'Indique Documento',
			'cliente.required'=> 'Registre Cliente',
			'subtotalDoc.required'=> 'Indique Sub Total',
			'igvDoc.required'=> 'Indique Igv',
			'totalDoc.required'	=> 'Indique Total',
			'tipoMoneda.required' => 'Indique Moneda',
    		'subtotalDoc.numeric' => 'Sub Total debe ser un número',
            'igvDoc.numeric'      => 'Igv debe ser un número',
			'totalDoc.numeric'    => 'Total debe ser un número',
			'listDetalles.required'=> 'Indique Detalles a Cotización',
        ];

        $validator = Validator::make($request->all(), $reglas, $mensajes);
		$messages = []; 

		if ($validator->fails()) {
            $messages = $validator->errors();
		}
		
		return $messages;
	}

	public function getCotizacion ($id, Request $request) {
		$cotizacion = DB::table('cotizacion as cot')
					->join('persona as cl','cl.id','=','cot.idCliente')
					->where('cot.situacionFacturado', 'N')
					->where('cot.id', $id)
					->select('cot.*', DB::Raw("(CASE cl.tipoDocumento WHEN 'PN' THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente"),
					'cl.documento', DB::Raw("CONCAT('C', LPAD(cot.serie,2,'0') ,'-', LPAD(cot.numero,8,'0')) as numero"))
					->first();
		
		$detalles = DB::table('detallecotizacion as dc')
					->whereNull('dc.deleted_at')
					->where('dc.idCotizacion', $id)
					->select('dc.*', DB::Raw("(CASE WHEN dc.idServicio IS NOT NULL THEN (SELECT CONCAT(s.tiempoEjecucion,' ',s.unidad) FROM servicio as s WHERE s.id = dc.idServicio LIMIT 1) ELSE '0' END) as tiempo"))
					->get();

		return ['cotizacion' => $cotizacion, 'detalles' => $detalles, 'estado' => true];
	}

	public function eliminar (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$cotizacion = Cotizacion::find($id);
			$detalles = DetalleCotizacion::where('idCotizacion','=',$cotizacion->id)
						->select('id')->get();
			foreach ($detalles as $det) {
				$d = DetalleCotizacion::find($det->id);
				$d->delete();	
			}
			$cotizacion->idPersonalEliminar = Auth::user()->usuarioId;
			$cotizacion->situacion = 'A';
			//$cotizacion->vigencia='N';
			$cotizacion->update();
			$cotizacion->delete();
			$errors[] = 'Cotización Eliminada Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	
	}

	public function eliminarCotizacionAuto (Request $request) {
		DB::beginTransaction();
		$errors = [];
		$band = true;
		try{
			$id = $request->get('id');
			$cotizacion = CotizacionAuto::find($id);
			$detalles = DetalleCotizacionAuto::where('idCotizacion','=',$cotizacion->id)
						->select('id')->get();
			foreach ($detalles as $det) {
				$d = DetalleCotizacionAuto::find($det->id);
				$d->delete();	
			}
			$cotizacion->idPersonalEliminar = Auth::user()->usuarioId;
			$cotizacion->situacion = 'A';
			//$cotizacion->vigencia='N';
			$cotizacion->update();
			$cotizacion->delete();
			$errors[] = 'Cotización Eliminada Correctamente';
		}catch(\Exception $ex){
			$errors[] = $ex->getMessage();
			$band = false;
			DB::rollback();
		}
		DB::commit();
	
		return ['errores' => (object)$errors, 'estado' => $band];	
	
	}


	public function situacionCotizacion()
	{
		set_time_limit(0);
		$situuacion=Cotizacion::select(DB::raw('TIMESTAMPDIFF(minute,created_at,now()) as tiempoconcurrido'),'id')->whereNotIn('situacion',['A','U'])->get();
		// dd('estoy aca');
		// dd($situuacion->count());

		// </dd>
		if($situuacion->count()>0){
			// dd('estoy aca');
			foreach ($situuacion as  $value) {
			if( $value->tiempoconcurrido>=10080){
				DB::update("update cotizacion set situacion ='N'  where id = ?", [$value->id]);

			}
		}
		}
	
	}

}
