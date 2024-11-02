<?php
namespace App\Libraries;

class Funciones {
	public function generarPaginacion($lista, $pagina, $filas, $largo=5){
		$cantidadTotal = count($lista); 
		if ($filas > $cantidadTotal) { 
			$filas = $cantidadTotal;
		}
		$cantidad = $cantidadTotal * 1.0; 
		$division = $cantidad / $filas; 
		$div = ceil($division); 
		if ($pagina > $div) {
			$pagina = (int) $div;
		}

		$inicio = ($pagina - 1) * $filas; 
		$fin    = ($pagina * $filas); 
		// dd($fin, $pagina, $filas);
		if ($fin > $cantidadTotal) {
			$fin = $cantidadTotal;
		} 

		$cadenaPagina  = [];
		$puntosDelante = "";
		$puntosDetras  = "";
		// $cadenaPagina .= "<ul class=\"pagination pagination-sm\">";
		// $cadenaPagina .= "<li class=\"active\"><a href=\"#\">TOTAL DE REGISTROS " . $cantidadTotal . "</a></li>";

		$band = false;
		// $bandPaginaActual = false; 
		for ($i=1; $i <= $div ; $i++) {


			// 	3			3		5
			// $largo_arreglo = count($cadenaPagina);

			// if ($largo_arreglo == ($largo-3)) {
			// 	if ($puntosDelante == '' && !$band) {
			// 		$cadenaPagina[] = array('opc' => '...');
			// 		$band= true;
			// 		// $puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			// 		// $cadenaPagina .= $puntosDelante;
			// 	}


			// 	if ($puntosDetras == '' && !$band) {
			// 		$cadenaPagina[] = array('opc' => '...'); 
			// 		$band = true;
			// 		// $puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			// 		// $cadenaPagina .= $puntosDetras;
			// 	}

			// 	// $band = true;
			// }

			// if ($i == $pagina) { $bandPaginaActual = true; }
			// if (count($cadenaPagina) < ($largo-2)) {

				if ($i == 1) {
					// if ($i == $pagina) {
						$cadenaPagina[] = array('opc' => $i, 'bloqueado'=> ($i==$pagina?true:false) ); //"<li class=\"active\"><a>" . $i . "</a></li>";
					// } else {
						// $cadenaPagina[] = array('opc' => $i); //"<li><a onclick=\"buscarCompaginado(" . $i . ", '', '".$entidad."')\">" . $i . "</a></li>";
					// }
				}
				if ($i == $div && $i != 1) {
					// if ($i == $pagina) {
						$cadenaPagina[] = array('opc' => $i, 'bloqueado'=> ($i==$pagina?true:false) ); //"<li class=\"active\"><a>" . $i . "</a></li>";
					// } else {
					// 	$cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
					// }
				}
				if ($i != 1 && $i != $div) {
					if ($i == $pagina) {
						$cadenaPagina[] =  array('opc' => $i, 'bloqueado'=> ($i==$pagina?true:false));
						// $cadenaPagina .= "<li class=\"active\"><a>" . $i . "</a></li>";
					} else {
						if ($i == ($pagina - 1) || $i == ($pagina - 2)) {
							$cadenaPagina[] =  array('opc' => $i, 'bloqueado'=> ($i==$pagina?true:false));
							// $cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
						}
						if ($i == ($pagina + 1) || $i == ($pagina + 2)) {
							$cadenaPagina[] =  array('opc' => $i, 'bloqueado'=> ($i==$pagina?true:false));
							// $cadenaPagina .= "<li><a onclick=\"buscarCompaginado(" . $i . ",'', '".$entidad."')\">" . $i . "</a></li>";
						}
					}
				}

				if ($i > 1 && $i < ($pagina - 2)) {
					if ($puntosDelante == '') {
						$cadenaPagina[] = array('opc' => '...', 'bloqueado'=> true);
						$band= true;
						$puntosDelante = '...';
						// $puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
						// $cadenaPagina .= $puntosDelante;
					}
				}
				if ($i < $div && $i > ($pagina + 2)) {
					if ($puntosDetras == '') {
						$cadenaPagina[] = array('opc' => '...', 'bloqueado'=> true); 
						$band = true;
						$puntosDetras = '...';
						// $puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
						// $cadenaPagina .= $puntosDetras;
					}
				}
			// } else {
			// 	// dd($cadenaPagina, $largo, $i, $pagina, $puntosDetras, $puntosDelante);
			// 	// if ($i > 1 && $i < ($pagina - 2)) {
			// 		if ($puntosDelante == '') {
			// 			$cadenaPagina[] = array('opc' => '...', 'bloqueado'=> true);
			// 			// $band= true;
			// 			$puntosDelante = '...';
			// 			$cadenaPagina[] =  array('opc' => (!$bandPaginaActual?$pagina:($i+1)), 'bloqueado'=> (!$bandPaginaActual?true:false));
			// 			$bandPaginaActual = true;
			// 			// $puntosDelante =  "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			// 			// $cadenaPagina .= $puntosDelante;
			// 		}

					
			// 		if (!$bandPaginaActual) {
			// 			$cadenaPagina[] = array('opc' => '...', 'bloqueado'=> true);
			// 			// $band= true;
			// 			$puntosDelante = '...';
			// 			$cadenaPagina[] =  array('opc' => (!$bandPaginaActual?$pagina:($i+1)), 'bloqueado'=> (!$bandPaginaActual?true:false));
			// 			$bandPaginaActual = true;
			// 		}

			// 	// }
			// 	// if ($i < $div && $i > ($pagina + 2)) {
			// 		if ($puntosDetras == '') {
			// 			$cadenaPagina[] = array('opc' => '...', 'bloqueado'=> true); 
			// 			// $band = true;
			// 			$puntosDetras = '...';
			// 			$cadenaPagina[] =  array('opc' => $div, 'bloqueado'=> ($i==$pagina?true:false));
					
			// 			// $puntosDetras = "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			// 			// $cadenaPagina .= $puntosDetras;
			// 		}

				// }	
			// } 


			// if (count($cadenaPagina) == $largo) { break; }

		}

		// if (count($cadenaPagina) == 0) {
		// 	$cadenaPagina[] = array('opc' => '1'); //"<li class=\"active\"><a>" . $i . "</a></li>";		
		// }
		// $cadenaPagina .= "</ul>";
		// $fin = count($cadenaPagina) - 1;
		
		$min = 0;
		$max = count($cadenaPagina) - 1;
		
		$inicioArr = $cadenaPagina[$min]['opc'];
		$finArr    = $cadenaPagina[$max]['opc'];
		
		// dd($cadenaPagina, $pagina, $div);
		$paginacion = array(
			'cadenapaginacion' => $cadenaPagina,
			'inicio'           => $inicio,
			'fin'              => $fin,
			'nuevapagina'      => $pagina,
			'inicioArr'        => $inicioArr,
			'finArr'      	   => $finArr
		);
		//Input::replace(array('page' => $pagina));
		return $paginacion;
	}

}