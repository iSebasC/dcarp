ALTER TABLE cotizacion
ADD
    COLUMN kilometraje VARCHAR(15) DEFAULT '-' NOT NULL AFTER placa;

ALTER TABLE
    cotizacion MODIFY COLUMN vin VARCHAR(30) DEFAULT '-' NOT NULL;

ALTER TABLE
    cotizacion MODIFY COLUMN placa VARCHAR(10) DEFAULT '-' NOT NULL;

ALTER TABLE cotizacion ADD COLUMN marcamodelo TEXT NULL AFTER vin;

ALTER TABLE imagendetalle
ADD
    COLUMN idOrdenTrabajo BIGINT(20) NULL AFTER id;

ALTER TABLE
    imagendetalle MODIFY COLUMN idDetalleOrdenTrabajo BIGINT(20) NULL;

ALTER TABLE ordentrabajo
ADD
    COLUMN idAsignado BIGINT(20) NULL AFTER idPersonal;

ALTER TABLE ordentrabajo
ADD
    COLUMN inicia DATETIME NULL AFTER idAsignado;

ALTER TABLE ordentrabajo
ADD
    COLUMN finaliza DATETIME NULL AFTER inicia;

ALTER TABLE ordentrabajo
ADD
    COLUMN observaciones TEXT NULL AFTER idPersonalEliminar;

ALTER TABLE cotizacion
ADD
    COLUMN porcentajeDescuento DECIMAL(10, 3) NULL AFTER marcamodelo;

ALTER TABLE cotizacion
ADD
    COLUMN totalDescuento DECIMAL(10, 3) NULL AFTER porcentajeDescuento;

ALTER TABLE detallecotizacion
ADD
    COLUMN precioReferencial DECIMAL(10, 3) NOT NULL AFTER precio;

ALTER TABLE detallecotizacion
ADD
    COLUMN porcentajeDescuento DECIMAL(10, 3) NULL AFTER precioReferencial;

ALTER TABLE detallecotizacion
ADD
    COLUMN totalDescuento DECIMAL(10, 3) NULL AFTER porcentajeDescuento;

ALTER TABLE tiempodetalle
ADD
    COLUMN idOrdenTrabajo BIGINT(20) NULL AFTER id;

ALTER TABLE
    tiempodetalle MODIFY COLUMN idDetalleOrdenTrabajo BIGINT(20) NULL;

ALTER TABLE tiempodetalle MODIFY COLUMN inicio DATETIME NULL;

ALTER TABLE tiempodetalle MODIFY COLUMN fin DATETIME NULL;

ALTER TABLE tiempodetalle
ADD
    COLUMN tiempo DECIMAL(10, 3) NULL AFTER fin;

ALTER TABLE tiempodetalle
ADD
    COLUMN unidadTiempo VARCHAR(30) NULL AFTER tiempo;

ALTER TABLE
    compra MODIFY COLUMN tipoCambio DECIMAL(10, 3) DEFAULT 1 NOT NULL;

ALTER TABLE
    compraauto MODIFY COLUMN tipoCambio DECIMAL(10, 3) DEFAULT 1 NOT NULL;

ALTER TABLE guia
ADD
    COLUMN tipoCambio DECIMAL(10, 3) DEFAULT 1 NOT NULL AFTER tipoMoneda;

UPDATE compra
SET tipoCambio = 1
WHERE
    tipoCambio = 0
    AND tipoMoneda = 'S';

UPDATE compraauto
SET tipoCambio = 1
WHERE
    tipoCambio = 0
    AND tipoMoneda = 'S';

UPDATE `detallecotizacion` SET precioReferencial = precio;

--- ===========================================================================================

/*
 PROCEDIMIENTOS MODIFICADOS
 reporteComprasPLE();
 reporteComprasAutosPLE();
 reporteNotasComprasPLE();
 reporteNotasComprasAutosPLE();
 reporteInventarioValorizado();
 listarDetalleVentaCotizacion();
 listarDetalleVentaOrden();
 listarDetalleVentaProducto();
 listarDetalleVentaAuto();
 listarDetalleProductoNota();
 listarDetalleServicioNota()
 
 */

-- ====================================================

/*
 Cambios
 CajaController.php
 CotizacionController.php 
 DocumentoBajaController.php 
 LoginController.php
 OrdenTrabajoController.php
 VentaController.php 
 modified: ClienteController.php
 modified: CotizacionController.php
 modified: DocumentoBajaController.php
 modified: InventarioController.php
 modified: OrdenTrabajoController.php
 modified: PerfilController.php
 modified: ReporteMensualController.php
 modified: VentaController.php
 modified: routes/web.php
 welcome.php
 */