<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

# PARA LOGIN
Route::post('/login','\App\Http\Controllers\LoginController@login')->name('login');
Route::get('/logout','\App\Http\Controllers\LoginController@logout')->name('logout');
Route::get('/validarsession','\App\Http\Controllers\LoginController@isValidSession');
Route::get('/login','\App\Http\Controllers\LoginController@login')->name('login');

#PRODUCTO
Route::post('/producto','\App\Http\Controllers\ProductoController@getAll');
Route::get('/obtenerproducto/{id}','\App\Http\Controllers\ProductoController@getProducto');
Route::post('/guardarproducto','\App\Http\Controllers\ProductoController@guardarProducto');
Route::get('/obtenerproductotipo/{tipo}/{almacenId}','\App\Http\Controllers\ProductoController@getProductos');
Route::get('/obtenerproductotipoinv/{tipo}/{almacenId}','\App\Http\Controllers\ProductoController@getProductosI');
Route::get('/obtenerproductotipo/{tipo}','\App\Http\Controllers\ProductoController@getProductosTipo');
Route::post('/eliminarproducto','\App\Http\Controllers\ProductoController@eliminarProducto');
Route::get('/producto/excel','\App\Http\Controllers\ProductoController@excel');
Route::get('/marcasrepuestos','\App\Http\Controllers\ProductoController@getMarcasRepuestos');
Route::get('/marcasauto','\App\Http\Controllers\ProductoController@getMarcasAuto');
Route::get('/sistemasauto','\App\Http\Controllers\ProductoController@getSistemaAuto');
Route::get('/modelosauto/{tipo}','\App\Http\Controllers\ProductoController@getModelosAuto');
Route::get('/marcasllanta','\App\Http\Controllers\ProductoController@getMarcasLlanta');
Route::get('/modelosllanta','\App\Http\Controllers\ProductoController@getModelosLlanta');

Route::get('/modelosbateria','\App\Http\Controllers\ProductoController@getModelosBateria');
Route::get('/marcasbateria','\App\Http\Controllers\ProductoController@getMarcasBateria');

#COTIZACION
Route::post('/obtenerproductos','\App\Http\Controllers\CotizacionController@obtenerProductos');
Route::post('/obtenerproductoscotizacion','\App\Http\Controllers\CotizacionController@obtenerProductosCotizacion');
Route::post('/obtenerproductosmov','\App\Http\Controllers\CotizacionController@obtenerProductosMov');
Route::post('/obtenerproductosautos','\App\Http\Controllers\CotizacionController@obtenerProductosAutos');
Route::post('/obtenerautosmov','\App\Http\Controllers\CotizacionController@obtenerAutosMov');
Route::post('/obtenercotizacion/{id}','\App\Http\Controllers\CotizacionController@getCotizacion');

Route::post('/guardarcotizacion','\App\Http\Controllers\CotizacionController@guardarCotizacion'); 
Route::post('/cotizacion','\App\Http\Controllers\CotizacionController@getAll');
Route::get('/cotizacion/excel','\App\Http\Controllers\CotizacionController@getCotizacionesExcel');

Route::get('/obtenercorrelativocotizacion','\App\Http\Controllers\CotizacionController@getCorrelativo');
Route::post('/getdetallescot/{id}','\App\Http\Controllers\CotizacionController@getDetalles');
Route::post('/eliminarcotizacion','\App\Http\Controllers\CotizacionController@eliminar');
Route::post('/buscarcotizaciones','\App\Http\Controllers\CotizacionController@obtenerCotizaciones');
Route::get('/situacionCotizacion','\App\Http\Controllers\CotizacionController@situacionCotizacion');
Route::get('/cotizacionpdf/{id}','\App\Http\Controllers\CotizacionController@generarPdf');
Route::get('/cotizacionexcel/{id}','\App\Http\Controllers\CotizacionController@generarExcel');

#ORDEN DE TRABAJO
Route::get('/obtenercorrelativoorden','\App\Http\Controllers\OrdenTrabajoController@getCorrelativo');
Route::post('/guardarorden','\App\Http\Controllers\OrdenTrabajoController@guardarOrden');
Route::post('/orden','\App\Http\Controllers\OrdenTrabajoController@getAll');
Route::post('/ordenfinalizado','\App\Http\Controllers\OrdenTrabajoController@getOrdenF');
Route::get('/getcheckrevision/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfRevision');
Route::get('/getchecktaller/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfCheckTaller');
Route::get('/getcheckcalidad/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfCheckCalidad');
Route::get('/getcheckmanejo/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfCheckManejo');

Route::post('/actualizaorden','\App\Http\Controllers\OrdenTrabajoController@actualizarEstadoOrden');
Route::get('/getverificacionchecks/{id}', '\App\Http\Controllers\OrdenTrabajoController@getVerificacionCheckList');
Route::post('/guardarverificacionchecklist','\App\Http\Controllers\OrdenTrabajoController@guardarVerificacionCheckList');
Route::post('/getcheckinventario','\App\Http\Controllers\OrdenTrabajoController@getcheckinventario');
Route::post('/getcheckcalidad','\App\Http\Controllers\OrdenTrabajoController@getcheckcalidad');
Route::post('/guardarcheckcalidad','\App\Http\Controllers\OrdenTrabajoController@guardarcheckcalidad');
Route::get('/checklistinv/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfCheckListInventario');
Route::get('/checklistcalidadblank/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfCheckListCalidad');
Route::get('/getpdfordencotizacion/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfOrdenCotizacion');

Route::post('/getcheckmanejo','\App\Http\Controllers\OrdenTrabajoController@getcheckmanejo');
Route::post('/guardarcheckmanejo','\App\Http\Controllers\OrdenTrabajoController@guardarcheckmanejo');

Route::post('/getchecktaller','\App\Http\Controllers\OrdenTrabajoController@getchecktaller');
Route::post('/guardarchecktaller','\App\Http\Controllers\OrdenTrabajoController@guardarchecktaller');


Route::get('/gettemporal','\App\Http\Controllers\OrdenTrabajoController@getTemporal');
Route::post('/cargartemporal','\App\Http\Controllers\OrdenTrabajoController@guardarTemporal');
Route::post('/eliminartemporal','\App\Http\Controllers\OrdenTrabajoController@eliminarTemporal');
Route::get('/getcotizaciones/{id}','\App\Http\Controllers\OrdenTrabajoController@getcotizaciones');
Route::post('/agregarcotizacion','\App\Http\Controllers\OrdenTrabajoController@agregarCotizacion');
Route::post('/buscardetalleorden','\App\Http\Controllers\OrdenTrabajoController@buscardetalleorden');
Route::get('/trabajadores/{search}','\App\Http\Controllers\OrdenTrabajoController@getTrabajadores');
Route::post('/trabajadores','\App\Http\Controllers\OrdenTrabajoController@getTrabajadoresQuery');
Route::post('/trabajadores/asesor','\App\Http\Controllers\OrdenTrabajoController@getTrabajadoresAsesorQuery');

Route::post('/guardarfirma','\App\Http\Controllers\OrdenTrabajoController@guardarFirma');

Route::post('/guardarasignacion','\App\Http\Controllers\OrdenTrabajoController@agregarAsignacion');
 
Route::post('/getbusqueda','\App\Http\Controllers\OrdenTrabajoController@getBusquedaPersonal');
Route::post('/getstartactividad/{id}','\App\Http\Controllers\OrdenTrabajoController@getInicioFin');
Route::post('/eliminarorden','\App\Http\Controllers\OrdenTrabajoController@eliminar');

#COMPRAS
Route::post('/guardarcompra','\App\Http\Controllers\CompraController@guardarCompra');
Route::post('/obtenerproductoscompra','\App\Http\Controllers\CompraController@obtenerProductos');
Route::post('/compra','\App\Http\Controllers\CompraController@getAll');
Route::post('/eliminarcompra/{id}','\App\Http\Controllers\CompraController@eliminar');
Route::post('/getdetallescompra/{id}','\App\Http\Controllers\CompraController@getDetalles');
Route::post('/darbaja/{id}','\App\Http\Controllers\CompraController@darBaja');
Route::get('/excel/compra','\App\Http\Controllers\CompraController@excel');
Route::get('/excel2/compra','\App\Http\Controllers\CompraController@excel2');

#PEDIDO DE COMPRAS
Route::post('/guardarpedidocompra','\App\Http\Controllers\CompraController@guardarPedidoCompra');
Route::post('/obtenerproductospedidoscompra','\App\Http\Controllers\CompraController@obtenerPedidoProductos');
Route::post('/pedidocompra','\App\Http\Controllers\CompraController@getPedidoAll');
Route::post('/eliminarpedidocompra/{id}','\App\Http\Controllers\CompraController@eliminarPedidoCompra');
// Route::post('/getdetallespedidocompra/{id}','\App\Http\Controllers\CompraController@getDetalles');
Route::post('/darbaja/{id}','\App\Http\Controllers\CompraController@darBaja');
Route::get('/excel/pedidocompra','\App\Http\Controllers\CompraController@excelPedidoCompra');
Route::get('/obtenerpedidocompra/{id}','\App\Http\Controllers\CompraController@obtenerPedidoCompra');

#SERVICIO
Route::get('/tiposservicio','\App\Http\Controllers\ServicioController@getTipoServicio');
Route::post('/guardarservicio','\App\Http\Controllers\ServicioController@guardarServicio');
Route::post('/servicio','\App\Http\Controllers\ServicioController@getAll');
Route::get('/obtenerservicio/{id}','\App\Http\Controllers\ServicioController@getServicio');
Route::post('/eliminarservicio','\App\Http\Controllers\ServicioController@eliminarServicio');
Route::post('/getprecio','\App\Http\Controllers\ServicioController@getPrecioHoraHombre');
Route::post('/guardarprecio','\App\Http\Controllers\ServicioController@guardarPrecio');
Route::get('/servicio/excel','\App\Http\Controllers\ServicioController@excel');
Route::get('/servicio/getmacroservicios', '\App\Http\Controllers\ServicioController@getMacroServicios');

#AUTOS
Route::get('/marcasautosmod','\App\Http\Controllers\AutoController@getMarcas');
Route::post('/auto','\App\Http\Controllers\AutoController@getAll');
Route::get('/obtenerauto/{id}','\App\Http\Controllers\AutoController@getAuto');
Route::post('/guardarauto','\App\Http\Controllers\AutoController@guardarAuto');
Route::post('/eliminarauto','\App\Http\Controllers\AutoController@eliminarAuto');


#LOCAL
Route::post('/local','\App\Http\Controllers\LocalController@getAll');
Route::get('/obtenerlocal/{id}','\App\Http\Controllers\LocalController@getLocal');
Route::get('/obtenerlocaltipo/{tipo}','\App\Http\Controllers\LocalController@getLocalTipo');
Route::get('/obtenertiendas','\App\Http\Controllers\LocalController@getTiendas');
Route::get('/obteneralmacenes/{id}','\App\Http\Controllers\LocalControlle@getAlmacenes','\App\Http\Controllers\InventarioController@getAlmacenes');;
Route::post('/guardarlocal','\App\Http\Controllers\LocalController@guardarLocal');
Route::post('/guardarlocalrelacion','\App\Http\Controllers\LocalController@guardarLocalRelacion');
Route::post('/eliminarlocal','\App\Http\Controllers\LocalController@eliminarLocal');

#PERSONAL
Route::post('/personal','\App\Http\Controllers\PersonalController@getAll');
Route::get('/obtenerpersonal/{id}','\App\Http\Controllers\PersonalController@getPersonal');
Route::get('/obtenerlocalrelacion/{id}/{tipo}','\App\Http\Controllers\PersonalController@getRelacionEmpleado');
Route::post('/guardarpersonal','\App\Http\Controllers\PersonalController@guardarPersonal');
Route::post('/eliminarpersonal','\App\Http\Controllers\PersonalController@eliminarPersonal');

Route::get('/categorias','\App\Http\Controllers\PersonalController@getCategorias');
Route::post('/obteneralmacen','\App\Http\Controllers\LocalController@obtenerAlmacen');
Route::get('/personal/excel','\App\Http\Controllers\PersonalController@excel');

#HABILIDADES DE PERSONAL 
Route::post('/habilidades/{id}','\App\Http\Controllers\PersonalController@getHabilidades');
Route::post('/guardarhabilidades','\App\Http\Controllers\PersonalController@guardarHabilidades');

#PERMISOS
Route::post('/permiso','\App\Http\Controllers\PermisoController@getAll'); 
Route::get('/obtenerpermiso/{id}','\App\Http\Controllers\PermisoController@getPermiso');
Route::post('/guardarpermiso','\App\Http\Controllers\PermisoController@guardarPermiso');
Route::post('/guardarpermisousuario','\App\Http\Controllers\PermisoController@guardarPermisoUsuario');
Route::post('/eliminarpermisousuario','\App\Http\Controllers\PermisoController@eliminarPermisoUsuario');

#INVENTARIO
Route::post('/inventario','\App\Http\Controllers\InventarioController@getAll');
Route::post('/guardarmovalmacen','\App\Http\Controllers\InventarioController@guardar');
Route::post('/guardarmovalmacenauto','\App\Http\Controllers\InventarioController@guardarAuto');

Route::get('/getalmacenes','\App\Http\Controllers\InventarioController@getAlmacenes');
Route::post('/detallesalidasentradas','\App\Http\Controllers\InventarioController@reporte');
Route::post('/getdetallesguia/{id}','\App\Http\Controllers\InventarioController@getDetalles');
Route::post('/eliminarguia','\App\Http\Controllers\InventarioController@eliminarGuia');
Route::get('/inventario/excel/{id}','\App\Http\Controllers\InventarioController@excel');
Route::get('/inventarioauto/excel/{id}','\App\Http\Controllers\InventarioController@excelAuto');
Route::get('/inventario/excel2/{almacenId}/{tipoId}','\App\Http\Controllers\InventarioController@excel02');
Route::post('/getproductorep','\App\Http\Controllers\InventarioController@getReporteES');
Route::post('/getmovimientosES','\App\Http\Controllers\InventarioController@getMovimientosES');
Route::get('/repensa/excel','\App\Http\Controllers\InventarioController@excelReporte');

#VENTA
Route::post('/venta','\App\Http\Controllers\VentaController@getAll');
Route::post('/getdetalles/{id}','\App\Http\Controllers\VentaController@getDetalles');
Route::post('/guardarventa','\App\Http\Controllers\VentaController@guardarVenta');
Route::post('/guardarventaauto','\App\Http\Controllers\VentaController@guardarVentaAuto');
Route::post('/buscarcotizacionesventa','\App\Http\Controllers\VentaController@obtenerCotizaciones');
Route::post('/buscarordenesventa','\App\Http\Controllers\VentaController@obtenerOrdenes');
Route::post('/venta/getsearchordencot', '\App\Http\Controllers\VentaController@getSearchOrdenCot');
Route::post('/venta/actualizarordencot', '\App\Http\Controllers\VentaController@actualizarOrdenCot');


Route::get('/getdetallescotizacionventa/{idcotizacion}','\App\Http\Controllers\VentaController@getDetallesCotizacionVenta');
Route::get('/getdetallesordenventa/{idorden}','\App\Http\Controllers\VentaController@getDetallesOrdenVenta');

Route::post('/guardaranulacion','\App\Http\Controllers\VentaController@guardarAnulacion');
Route::post('/anularnota','\App\Http\Controllers\VentaController@anularNota');
Route::get('/venta/excel','\App\Http\Controllers\VentaController@excel');
Route::get('/seriesdocumento/{tipo_doc}','\App\Http\Controllers\VentaController@getSeriesDocumento');
Route::get('/seriesdocumentoauto/{tipo_doc}','\App\Http\Controllers\VentaController@getSeriesDocumentoAuto');

Route::get('/venta/redeclarar/{id}','\App\Http\Controllers\VentaController@redeclarar');

#CLIENTE
Route::get('/obtenercliente/{documento}/{tipoDoc}','\App\Http\Controllers\ClienteController@getCliente');
Route::get('/obtenercliente02/{documento}','\App\Http\Controllers\ClienteController@getCliente02');
Route::post('/getcliente/{documento}','\App\Http\Controllers\ClienteController@getClienteDocumento');
Route::post('/guardarcliente','\App\Http\Controllers\ClienteController@guardarCliente');
Route::get('/getclientecita/{id}','\App\Http\Controllers\ClienteController@getClienteCita');
Route::get('/cliente/excel','\App\Http\Controllers\ClienteController@excel');
Route::post('/cliente','\App\Http\Controllers\ClienteController@getAllInterface');

#PROVEEDOR
Route::get('/obtenerproveedor/{documento}','\App\Http\Controllers\ClienteController@getProveedor');
Route::post('/getproveedor/{documento}','\App\Http\Controllers\ClienteController@getProveedorDocumento');
Route::post('/guardarproveedor','\App\Http\Controllers\ClienteController@guardarProveedor');
Route::post('/proveedor','\App\Http\Controllers\ClienteController@getAllInterfaceProveedor');
Route::get('/proveedor/excel','\App\Http\Controllers\ClienteController@excelProveedor');


#TIPO CAMBIO
Route::post('/gettipocambio','\App\Http\Controllers\CompraController@getTipoCambio');
Route::post('/guardartipocambio','\App\Http\Controllers\CompraController@guardarTipoCambio');
Route::get('/gettipocambio','\App\Http\Controllers\CompraController@getValidTipoCambio');
Route::post('/guardartipocambioapi','\App\Http\Controllers\CompraController@guardarTipoCambioAPI');

#CITA
Route::post('/guardarcita','\App\Http\Controllers\CitaController@guardarCita');
Route::get('/cargarcitas','\App\Http\Controllers\CitaController@getAll');
Route::post('/getcita/{id}','\App\Http\Controllers\CitaController@getCita');
Route::get('/obtenercorrelativocita','\App\Http\Controllers\CitaController@getCorrelativo');
Route::get('/buscarcitas/{id}','\App\Http\Controllers\CitaController@obtenerCitas');

Route::get('/exito','\App\Http\Controllers\CitaController@exito');
Route::get('/enviarcorreo','\App\Http\Controllers\CitaController@enviarCorreo');

Route::post('/guardarcitapublic','\App\Http\Controllers\CitaController@guardarCitaPublic');
#CAJA
Route::get('/getcaja/{id}','\App\Http\Controllers\CajaController@getCaja');
Route::get('/getcajaabierta','\App\Http\Controllers\CajaController@getCajaAbierta');
Route::post('/caja','\App\Http\Controllers\CajaController@getAll');
Route::get('/obtenercaja/{id}','\App\Http\Controllers\CajaController@obtenerCaja');
Route::post('/movimiento','\App\Http\Controllers\CajaController@guardarMovimiento');
Route::post('/cerrar','\App\Http\Controllers\CajaController@cerrarCaja');
Route::post('/aperturar','\App\Http\Controllers\CajaController@aperturarCaja');
Route::post('/eliminarcaja','\App\Http\Controllers\CajaController@eliminarCaja');
Route::get('/caja/pdf/{id}','\App\Http\Controllers\CajaController@pdf');
Route::get('/caja/excel/{id}','\App\Http\Controllers\CajaController@excel');
Route::post('/reparqueo','\App\Http\Controllers\CajaController@repArqueo');
Route::get('/caja/excelAll','\App\Http\Controllers\CajaController@excelAll');

#REPORTE 
Route::get('/reporteproducto','\App\Http\Controllers\ProductoController@getReporte');
Route::get('/reporteventas/{opc}','\App\Http\Controllers\VentaController@getReporte');
Route::get('/reporteinicio','\App\Http\Controllers\VentaController@getReporteInicio');
Route::get('/auto/reporteinicio', '\App\Http\Controllers\VentaController@getReporteAutoInicio');

Route::post('/opciones/{id}','\App\Http\Controllers\PermisoController@getMenu');
Route::get('/acabados','\App\Http\Controllers\ProductoController@getAcabados');
Route::get('/tipos','\App\Http\Controllers\ProductoController@getTipos');
Route::get('/formas','\App\Http\Controllers\ProductoController@getFormas');
Route::get('/unidadMedidas','\App\Http\Controllers\ProductoController@getUnidadMedidas');

Route::get('/departamentos','\App\Http\Controllers\LocalController@getDepartamentos');
Route::get('/categorias','\App\Http\Controllers\PersonalController@getCategorias');

Route::get('/provincias/{id}','\App\Http\Controllers\LocalController@getProvincias');
Route::get('/distritos/{id}','\App\Http\Controllers\LocalController@getDistritos');
Route::get('/certificaciones','\App\Http\Controllers\PersonalController@getCertificaciones');

#GANANCIAS
Route::post('/ganancia','\App\Http\Controllers\GananciaController@getAll');
Route::post('/guardarporcentaje','\App\Http\Controllers\GananciaController@guardar');

#PERFIL
Route::post('/getperfil','\App\Http\Controllers\PerfilController@getPerfil');
Route::post('/getperfiltrabajos','\App\Http\Controllers\PerfilController@getPerfilTrabajos');
  
Route::post('/cargarmultimedia/{id}','\App\Http\Controllers\OrdenTrabajoController@cargarMultimedia');
Route::post('/eliminarimagen/{id}','\App\Http\Controllers\OrdenTrabajoController@eliminarMultimedia');

Route::get('/getmultimedia/{id}','\App\Http\Controllers\OrdenTrabajoController@getMultimedia');

#TRABAJOS
Route::post('/trabajos','\App\Http\Controllers\OrdenTrabajoController@getTrabajos');
Route::post('/guardaravance','\App\Http\Controllers\OrdenTrabajoController@guarAvance');
Route::post('/revisaravance','\App\Http\Controllers\OrdenTrabajoController@revisarAvance');
Route::post('/getdetallesev/{id}', '\App\Http\Controllers\OrdenTrabajoController@getDetallesEvaluacion');
Route::post('/getincidencias/{id}', '\App\Http\Controllers\OrdenTrabajoController@getIncidenciasEvaluacion');
Route::post('/guardarincidencia', '\App\Http\Controllers\OrdenTrabajoController@guardarIncidencia');

#MOTIVOS
Route::get('/getmotivoslibre','\App\Http\Controllers\OrdenTrabajoController@getMotivosLibre');

#PAQUETES
Route::post('/guardarpaquete','\App\Http\Controllers\PaqueteController@guardarPaquete');
Route::post('/paquete','\App\Http\Controllers\PaqueteController@getAll');
Route::post('/getdetallespaquete/{id}','\App\Http\Controllers\PaqueteController@getDetalles');
Route::post('/obtenerpaquetes','\App\Http\Controllers\PaqueteController@obtenerPaquetes');
Route::post('/cargardetallespaquete','\App\Http\Controllers\PaqueteController@cargarDetalles');
Route::post('/eliminarpaquete','\App\Http\Controllers\PaqueteController@eliminarPaquete');

#COMPRAS AUTO
Route::post('/obtenerautoscompra','\App\Http\Controllers\CompraController@obtenerAutosCompra');
Route::post('/guardarcompraauto','\App\Http\Controllers\CompraController@guardarCompraAuto');
Route::post('/compraauto','\App\Http\Controllers\CompraController@getAll02');
Route::post('/getdetallescompraauto/{id}','\App\Http\Controllers\CompraController@getDetalles02');
Route::post('/darbajaauto/{id}','\App\Http\Controllers\CompraController@darBaja02');
Route::post('/eliminarcompraauto/{id}','\App\Http\Controllers\CompraController@eliminarAuto');

#COTIZACIONES AUTOS
Route::post('/obtenerautos','\App\Http\Controllers\CotizacionController@obtenerAutos');
Route::post('/guardarcotizacionauto','\App\Http\Controllers\CotizacionController@guardarCotizacionAuto');
Route::post('/cotizacionauto','\App\Http\Controllers\CotizacionController@cc');
Route::post('/getdetallescotauto/{id}','\App\Http\Controllers\CotizacionController@getDetalles02');
Route::post('/eliminarcotizacionauto','\App\Http\Controllers\CotizacionController@eliminarCotizacionAuto');
Route::get('/pdfcotizacionauto/{id}','\App\Http\Controllers\CotizacionController@pdfCotizacionAuto');

#REPORTE DE PRODUCTIVIDAD
Route::get('/evaluacion/excel','\App\Http\Controllers\OrdenTrabajoController@excel');
Route::get('/orden/productividad/excel', '\App\Http\Controllers\OrdenTrabafjoController@excelProductividad');
#ENCUESTAS
Route::get('/getpreguntasencuesta','\App\Http\Controllers\OrdenTrabajoController@getPreguntasEncuestas');
Route::post('/guardarencuesta','\App\Http\Controllers\OrdenTrabajoController@guardarEncuesta');
Route::get('/getencuesta/{id}','\App\Http\Controllers\OrdenTrabajoController@getPdfEncuesta');
Route::get('/encuesta/excel','\App\Http\Controllers\OrdenTrabajoController@getReporteEncuesta');

#DOCUMENTOS DE BAJA
Route::post('/buscarpersonas','\App\Http\Controllers\DocumentoBajaController@getPersonas');
Route::post('/buscarcomprobantes','\App\Http\Controllers\DocumentoBajaController@buscarComprobantes');
Route::post('/cargardetallesbaja','\App\Http\Controllers\DocumentoBajaController@getDetallesComprobante');
Route::post('/guardarnota','\App\Http\Controllers\DocumentoBajaController@guardarNota');
Route::post('/baja','\App\Http\Controllers\DocumentoBajaController@getAll');
Route::post('/getdetallesanulacion/{id}', '\App\Http\Controllers\DocumentoBajaController@getDetalles');
Route::post('/eliminarbaja/{id}', '\App\Http\Controllers\DocumentoBajaController@anularBaja');

#PROSPECTOS
Route::post('/buscarautos','\App\Http\Controllers\AutoController@getAutos');
Route::get('/origenesprospectos', '\App\Http\Controllers\AutoController@getOrigenesProspecto');
Route::get('/etiquetasprospectos', '\App\Http\Controllers\AutoController@getEtiquetasProspecto');
Route::get('/modelosautos','\App\Http\Controllers\AutoController@getModelosAuto');
Route::post('/guardarprospecto','\App\Http\Controllers\AutoController@guardarProspecto');
Route::post('/getprospectosall', '\App\Http\Controllers\AutoController@getProspectosAll');
Route::get('/obtenerprospecto/{id}', '\App\Http\Controllers\AutoController@obtenerProspecto');
Route::post('/eliminarprospecto', '\App\Http\Controllers\AutoController@eliminarProspecto');

Route::post('/buscarAsesor', '\App\Http\Controllers\AutoController@buscarAsesor');
Route::post('/guardarAsesor', '\App\Http\Controllers\AutoController@guardarAsesor');


#OPORTUNIDADES
Route::post('/buscarobsequios','\App\Http\Controllers\AutoController@getObsequios');
Route::post('/buscaradicionales','\App\Http\Controllers\AutoController@getAdicionales');
Route::post('/guardaroportunidad','\App\Http\Controllers\AutoController@guardarOportunidad');
Route::post('/getoportunidadesall', '\App\Http\Controllers\AutoController@getOportunidadesAll');
Route::get('/obteneroportunidad/{id}', '\App\Http\Controllers\AutoController@obtenerOportunidad');
Route::post('/eliminaroportunidad', '\App\Http\Controllers\AutoController@eliminarOportunidad');
Route::get('/findoportunidad/{id}', '\App\Http\Controllers\AutoController@findOportunidad');

Route::post('/guardarobsequio','\App\Http\Controllers\AutoController@guardarobsequio');
Route::get('/autos/getnotificaciones/{id}/{tipo}', '\App\Http\Controllers\AutoController@getMensajesSistema');
Route::post('/autos/marcarnotificacion', '\App\Http\Controllers\AutoController@marcarNotificacion');
Route::post('/autos/eliminarnotificacion', '\App\Http\Controllers\AutoController@eliminarNotificacion');

#TAREAS
Route::get('/tareas', '\App\Http\Controllers\AutoController@getMensajes');

#OBSEQUIOS Y ADICIONALES
Route::post('/obsequioall' ,'\App\Http\Controllers\AutoController@getObsequiosAll');
Route::get('/obtenerobsequio/{id}','\App\Http\Controllers\AutoController@obtenerObsequio');


#NOTIFICACION
Route::post('/guardarnotificacion' ,'\App\Http\Controllers\OrdenTrabajoController@guardarNotificacion');

#REPORTE MENSUAL
Route::get('/reportemes/excel','\App\Http\Controllers\ReporteMensualController@reporteMesExcel');
Route::get('/reportecliente/excel','\App\Http\Controllers\ReporteMensualController@reporteClienteExcel');

#LLENAR TABLA
Route::get('/llenartabla','\App\Http\Controllers\ReporteMensualController@saveElementos');

#CONFIGURACION DE IGV
Route::post('/getconfiguracion','\App\Http\Controllers\GananciaController@getConfiguracionIgv');
Route::post('/guardarconfiguracion','\App\Http\Controllers\GananciaController@guardarConfiguracion');
Route::get('/getconfigigv','\App\Http\Controllers\GananciaController@getMostrarIgv');
Route::get('/generar', function () {
    symlink('/home/eduiyvoou2zd/appAutos/storage/app/public','storage'); 
    
    echo 'Ok';
});

#GUIAS DE ALMACENES
Route::get('/gettiposdocumentosguias','\App\Http\Controllers\InventarioController@getTiposDocumentosGuia');
Route::post('/guiasinventario','\App\Http\Controllers\InventarioController@getGuiasInventario');
Route::get('/guia/pdf/{id}','\App\Http\Controllers\InventarioController@generarPdfGuia');
Route::get('/guia/excel', '\App\Http\Controllers\InventarioController@guiasExcel');
Route::get('/public_path', function () {
    echo Storage::path('local_xml');    
 });
 
#RECLAMOS
Route::get('/reclamo/areas', '\App\Http\Controllers\OrdenTrabajoController@getAreas');
Route::post('/reclamo/ordenes', '\App\Http\Controllers\OrdenTrabajoController@getOrdenes');
Route::post('/reclamo/personal', '\App\Http\Controllers\OrdenTrabajoController@getPersonal');
Route::post('/reclamo/guardar', '\App\Http\Controllers\OrdenTrabajoController@guardarReclamo');
Route::post('/reclamo/getAll', '\App\Http\Controllers\OrdenTrabajoController@getReclamosAll');
Route::post('/reclamo/eliminar/{id}', '\App\Http\Controllers\OrdenTrabajoController@eliminarReclamo');
Route::get('/reclamo/ver/{id}', '\App\Http\Controllers\OrdenTrabajoController@verReclamo');
Route::post('/reclamo/responder', '\App\Http\Controllers\OrdenTrabajoController@responderReclamo');
Route::post('/reclamo/cerrar/{id}', '\App\Http\Controllers\OrdenTrabajoController@cerrarReclamo');
Route::get('/reclamo/excel', '\App\Http\Controllers\OrdenTrabajoController@excelReclamos');

#CUENTAS_POR_PAGAR_Y_COBRAR
Route::post('/cuentaxcobrar/buscarpersonas', '\App\Http\Controllers\VentaController@getPersonas');
Route::post('/cuentaxcobrar/buscarcuentas', '\App\Http\Controllers\VentaController@getCuentas');
Route::post('/cuentaxcyp/guardar', '\App\Http\Controllers\VentaController@guardarCuenta');
Route::post('/cuentaxcyp/getAll',  '\App\Http\Controllers\VentaController@getAllCuentas');
Route::post('/cuentaxcyp/eliminar/{id}', '\App\Http\Controllers\VentaController@eliminarCuenta');
Route::post('/cuentaxcobrar/buscarcuentascontables', '\App\Http\Controllers\VentaController@getCuentasContables');

#NOTIFICACIONES
Route::get('/notificaciones/all', '\App\Http\Controllers\ReporteMensualController@getNotificaciones');

Route::get('/cita_public', function () {
    return view('cita');
});

Route::get('/arreglar', '\App\Http\Controllers\OrdenTrabajoController@arreglar');
Route::get('/limpiarcache', function () {
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    //Artisan::call('optimize');
    // Artisan::call('clear:compiled');
    
    echo 'Ok';
});


// Route::get('/exito', function () {
//     return view('exito');
// });

Route::any('{url_all}', function () {
    // if (Auth::check()) {
        return view('welcome');
    // } else {
    //     return view('login');
    // }	
})->where('url_all','.*');


// if (Auth::check()) {
// 	Route::group(['namespace' => 'appValentina'], function () {
// 		return view('welcome');
// 	});
// }
