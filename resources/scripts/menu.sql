
ALTER TABLE menu CHANGE COLUMN icono icono VARCHAR(50) NULL DEFAULT NULL ;

UPDATE menu SET icono = 'mdi mdi-home-account' WHERE id = 1;
UPDATE menu SET icono = 'mdi mdi-cog' WHERE id = 29;
UPDATE menu SET icono = 'mdi mdi-account-wrench-outline' WHERE id = 3;
UPDATE menu SET icono = 'mdi mdi-library-outline' WHERE id = 14;
UPDATE menu SET icono = 'mdi mdi-account-group-outline' WHERE id = 13;
UPDATE menu SET icono = 'mdi mdi-cash' WHERE id = 24;
UPDATE menu SET icono = 'mdi mdi-account-network' WHERE id = 34;
UPDATE menu SET icono = 'mdi mdi-car-back' WHERE id = 18;

UPDATE menu SET icono = 'mdi mdi-car-cruise-control' WHERE id = 30;
UPDATE menu SET icono = 'mdi mdi-car-search-outline' WHERE id = 32;
UPDATE menu SET icono = 'mdi mdi-cards-outline' WHERE id = 31;
UPDATE menu SET icono = 'mdi mdi-chart-bar-stacked' WHERE id = 4;
UPDATE menu SET icono = 'mdi mdi-package-variant-closed-plus' WHERE id = 5;
UPDATE menu SET icono = 'mdi mdi-account-details-outline' WHERE id = 19;
UPDATE menu SET icono = 'mdi mdi-invoice-text-multiple-outline' WHERE id = 6;
UPDATE menu SET icono = 'mdi mdi-chart-areaspline' WHERE id = 38;

UPDATE menu SET icono = 'mdi mdi-file-document-edit-outline' WHERE id = 39;
UPDATE menu SET icono = 'mdi mdi-text-box-plus-outline' WHERE id = 35;
UPDATE menu SET icono = 'mdi mdi-book-open-variant-outline' WHERE id = 8;
UPDATE menu SET icono = 'mdi mdi-book-open-page-variant' WHERE id = 28;
UPDATE menu SET icono = 'mdi mdi-currency-usd' WHERE id = 9;
UPDATE menu SET icono = 'mdi mdi-file-document-plus' WHERE id = 21;
UPDATE menu SET icono = 'mdi mdi-check-all' WHERE id = 20;
UPDATE menu SET icono = 'mdi mdi-store-search-outline' WHERE id = 22;
UPDATE menu SET icono = 'mdi mdi-list-box-outline' WHERE id = 10;

UPDATE menu SET icono = 'mdi mdi-file-document-minus-outline' WHERE id = 36;
UPDATE menu SET icono = 'mdi mdi-store-settings-outline' WHERE id = 2;
UPDATE menu SET icono = 'mdi mdi-file-document-arrow-right-outline' WHERE id = 23;
UPDATE menu SET icono = 'mdi mdi-elevator-passenger-outline' WHERE id = 26;
UPDATE menu SET icono = 'mdi mdi-book-check-outline' WHERE id = 27;
UPDATE menu SET icono = 'mdi mdi-list-box-outline' WHERE id = 33;
UPDATE menu SET icono = 'mdi mdi-source-repository-multiple' WHERE id = 38;

UPDATE menu SET icono = 'mdi mdi-chart-bar-stacked' WHERE id = 37;
UPDATE menu SET icono = 'mdi mdi-wallet-bifold' WHERE id = 7;
UPDATE menu SET icono = 'mdi mdi-account-clock' WHERE id = 15;
UPDATE menu SET icono = 'mdi mdi-account-details' WHERE id = 16;
UPDATE menu SET icono = 'mdi mdi-calendar-account-outline' WHERE id = 25;
UPDATE menu SET icono = 'mdi mdi-briefcase-arrow-left-right' WHERE id = 17;


INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) 
VALUES ('40', 'mdi mdi-account-network', 'Proveedores', '2', '/proveedor', 'proveedor', '29', '6', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 40, mu.estado FROM menuusuario mu WHERE mu.idMenu = 34;

ALTER TABLE producto ADD codInterno VARCHAR(30) NULL;

INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) VALUES ('41', 'mdi mdi-order-bool-ascending', 'Pedidos de Compra', '2', '/pedidocompra', 'pedidocompra', '8', '8', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 41, mu.estado FROM menuusuario mu WHERE mu.idMenu = 31;


CREATE TABLE pedidocompra (
  `id` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `idProveedor` bigint(20) NOT NULL,
  `tipoMoneda` char(1) NOT NULL,
  `fechaServicio` date NOT NULL,
  `diasCredito` int(11) NOT NULL,
  `total` decimal(11,3) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE detallepedidocompra (
  `id` bigint(20) NOT NULL,
  `item` int(11) NOT NULL,
  `cantidad` decimal(10,3) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(11,3) NOT NULL,
  `total` decimal(11,3) NOT NULL,
  `idProducto` bigint(20) NULL,
  `idServicio` bigint(20) NULL,
  `idPedidoCompra` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);


ALTER TABLE pedidocompra
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPersonal` (`idPersonal`),
  ADD KEY `idProveedor` (`idProveedor`),
  ADD KEY `idPersonalEliminar` (`idPersonalEliminar`);

ALTER TABLE detallepedidocompra
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProducto` (`idProducto`),
  ADD KEY `idServicio` (`idServicio`),
  ADD KEY `idPedidoCompra` (`idPedidoCompra`);

ALTER TABLE pedidocompra
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE detallepedidocompra
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE pedidocompra
  ADD CONSTRAINT `pedido_compra_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
  ADD CONSTRAINT `pedido_compra_ibfk_2` FOREIGN KEY (`idProveedor`) REFERENCES `persona` (`id`),
  ADD CONSTRAINT `pedido_compra_ibfk_3` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE detallepedidocompra
  ADD CONSTRAINT `detalle_pedido_compra_ibfk_1` FOREIGN KEY (`idProducto`) REFERENCES `producto` (`id`)  ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `detalle_pedido_compra_ibfk_2` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`)  ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `detalle_pedido_compra_ibfk_3` FOREIGN KEY (`idPedidoCompra`) REFERENCES `pedidocompra` (`id`);


ALTER TABLE cita ADD vin VARCHAR(20) NOT NULL;
ALTER TABLE cita ADD con_soat CHAR(1) NOT NULL;
ALTER TABLE cita ADD con_seguro CHAR(1) NOT NULL;

ALTER TABLE pedidocompra ADD situacion CHAR(1) DEFAULT 'P' NOT NULL;
ALTER TABLE pedidocompra ADD idCompra BIGINT(20) NULL;
ALTER TABLE pedidocompra ADD numero BIGINT(20) NOT NULL;

ALTER TABLE pedidocompra
  ADD CONSTRAINT `pedido_compra_ibfk_4` FOREIGN KEY (`idCompra`) REFERENCES `compra` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;


INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) VALUES ('42', 'mdi mdi-account-card-outline', 'Reclamos', '2', '/reclamo', 'reclamo', '8', '9', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 42, mu.estado FROM menuusuario mu WHERE mu.idMenu = 31;

CREATE TABLE area (
 `id` bigint(20) NOT NULL,
 `nombre` varchar(255) NOT NULL,
 `idPersonal` bigint(20) NOT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE area
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPersonal` (`idPersonal`);

ALTER TABLE area
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

-- INSERT PARA AREAS
INSERT INTO area(nombre, idPersonal, created_at, updated_at)
VALUES ('Area 01', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Area 02', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


CREATE TABLE reclamo (
 `id` bigint(20) NOT NULL,
 `fecha` date NOT NULL,
 `idCliente` bigint(20) NOT NULL,
 `idOrden` bigint(20) NOT NULL,
 `idAreaDestino` bigint(20) NOT NULL,
 `idPersonalAsignadoA` bigint(20) NOT NULL,
 `reclamo` text NOT NULL,
 `idPersonal` bigint(20) NOT NULL,
 `idPersonalEliminar` bigint(20) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE reclamo
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPersonal` (`idPersonal`),
  ADD KEY `idCliente` (`idCliente`),
  ADD KEY `idOrden` (`idOrden`),
  ADD KEY `idAreaDestino` (`idAreaDestino`),
  ADD KEY `idPersonalAsignadoA` (`idPersonalAsignadoA`),
  ADD KEY `idPersonalEliminar` (`idPersonalEliminar`);

ALTER TABLE reclamo
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
  

ALTER TABLE reclamo
  ADD CONSTRAINT `reclamo_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
  ADD CONSTRAINT `reclamo_ibfk_2` FOREIGN KEY (`idCliente`) REFERENCES `persona` (`id`),
  ADD CONSTRAINT `reclamo_ibfk_3` FOREIGN KEY (`idOrden`) REFERENCES `ordentrabajo` (`id`),
  ADD CONSTRAINT `reclamo_ibfk_4` FOREIGN KEY (`idAreaDestino`) REFERENCES `area` (`id`),
  ADD CONSTRAINT `reclamo_ibfk_5` FOREIGN KEY (`idPersonalAsignadoA`) REFERENCES `trabajador` (`id`),
  ADD CONSTRAINT `reclamo_ibfk_6` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
 
ALTER TABLE reclamo ADD numero BIGINT(20) NOT NULL;
ALTER TABLE reclamo ADD situacion CHAR(1) DEFAULT 'A' NOT NULL;


ALTER TABLE tiempodetalle ADD estado CHAR(1) DEFAULT 'F' NULL;

INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) 
VALUES ('43', 'mdi mdi-account-multiple', 'Prospectos', '2', '/prospecto', 'prospecto', '30', '4', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 43, mu.estado FROM menuusuario mu WHERE mu.idMenu = 18;


CREATE TABLE modeloauto (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE modeloauto ADD PRIMARY KEY (id);
ALTER TABLE modeloauto MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE modeloauto ADD CONSTRAINT modelo_auto_nombre UNIQUE(nombre);

CREATE TABLE origenprospecto (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE origenprospecto ADD PRIMARY KEY (id);
ALTER TABLE origenprospecto MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE origenprospecto ADD CONSTRAINT origen_prospecto_nombre UNIQUE(nombre);

CREATE TABLE etiquetaprospecto (
  `id` bigint(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE etiquetaprospecto ADD PRIMARY KEY (id);
ALTER TABLE etiquetaprospecto MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE etiquetaprospecto ADD CONSTRAINT etiqueta_prospecto_nombre UNIQUE(nombre);

ALTER TABLE auto ADD modeloId BIGINT(20) NULL;
ALTER TABLE auto ADD CONSTRAINT `auto_ibfk_3` FOREIGN KEY (`modeloId`) REFERENCES `modeloauto` (`id`);


ALTER TABLE auto ADD linea CHAR(1) NULL;
ALTER TABLE auto ADD urlFicha TEXT NULL;
ALTER TABLE auto ADD urlImagen TEXT NULL;

CREATE TABLE clienteauto (
	id BIGINT(20) NOT NULL,
  tipoDocumento VARCHAR(3) NOT NULL,
  documento VARCHAR(12) NOT NULL,
  apellidos VARCHAR(255)  NULL,
  nombres VARCHAR(255)  NULL,
  razonSocial VARCHAR(255) NULL,
  telefono VARCHAR(12) NOT NULL,
  correoElectronico VARCHAR(255) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  idAsesorRegistra bigint(20) NOT NULL,
  idPersonalEliminar bigint(20) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL
);

ALTER TABLE clienteauto ADD PRIMARY KEY (id);
ALTER TABLE clienteauto MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE clienteauto ADD CONSTRAINT `cliente_auto_ibfk_1` FOREIGN KEY (`idAsesorRegistra`) REFERENCES `trabajador` (`id`),
						ADD CONSTRAINT `cliente_auto_ibfk_2` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`),
						ADD CONSTRAINT `cliente_auto_ibfk_3` UNIQUE(id, tipoDocumento, documento);


CREATE TABLE prospecto (
	id BIGINT(20) NOT NULL,
	idCliente BIGINT(20) NOT NULL,
  tiempoEstimadoCompra DATE NOT NULL,
  idAuto bigint(20) NOT NULL,
	idMarcaAuto bigint(20) NOT NULL,
  idModeloAuto bigint(20) NOT NULL,
  idOrigen bigint(20) NOT NULL,
  idEtiqueta bigint(20) NOT NULL,
  idAsesorRegistra bigint(20) NOT NULL,
	idPersonalEliminar bigint(20) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL
);


ALTER TABLE prospecto ADD PRIMARY KEY (id);
ALTER TABLE prospecto MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE prospecto ADD CONSTRAINT `prospecto_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `clienteauto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_2` FOREIGN KEY (`idAuto`) REFERENCES `auto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_3` FOREIGN KEY (`idMarcaAuto`) REFERENCES `marcaauto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_4` FOREIGN KEY (`idModeloAuto`) REFERENCES `modeloauto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_5` FOREIGN KEY (`idOrigen`) REFERENCES `origenprospecto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_6` FOREIGN KEY (`idEtiqueta`) REFERENCES `etiquetaprospecto` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_7` FOREIGN KEY (`idAsesorRegistra`) REFERENCES `trabajador` (`id`),
					  ADD CONSTRAINT `prospecto_ibfk_8` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`);


INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) 
VALUES ('44', 'mdi mdi-account-arrow-up', 'Oportunidades', '2', '/oportunidad', 'oportunidad', '30', '5', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 44, mu.estado FROM menuusuario mu WHERE mu.idMenu = 18;


ALTER TABLE prospecto ADD situacion CHAR(1) DEFAULT 'N' NOT NULL;


CREATE TABLE adicionalobsequio (
  `id` bigint(20) NOT NULL,
  `tipo` char(1) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE adicionalobsequio ADD PRIMARY KEY (id);
ALTER TABLE adicionalobsequio MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE adicionalobsequio ADD CONSTRAINT adicional_obsequio_nombre UNIQUE(tipo,nombre),
			ADD CONSTRAINT `obsequio_oportunidad_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
			ADD CONSTRAINT `obsequio_oportunidad_ibfk_2` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`);

INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) 
VALUES ('45', 'mdi mdi-gift-open', 'Adicionales & Obsequios', '2', '/obsequio', 'obsequio', '30', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 45, mu.estado FROM menuusuario mu WHERE mu.idMenu = 18;

CREATE TABLE oportunidad (
	id BIGINT(20) NOT NULL,
	cliente varchar(500) NOT NULL,
    concepto varchar(255) NOT NULL,
    fechaCierre DATE NOT NULL,
    moneda varchar(3) NOT NULL,
	linea char(1) NOT NULL,
    certeza char(1) NOT NULL,
    fase char(1) NOT NULL,
    obsequios TEXT NULL,
    adicionales TEXT NULL,
    monto decimal(10,3) NOT NULL,
	idProspecto bigint(20) NOT NULL,
    idAsesorRegistra bigint(20) NOT NULL,
	idPersonalEliminar bigint(20) DEFAULT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    deleted_at timestamp NULL DEFAULT NULL
);


ALTER TABLE oportunidad ADD PRIMARY KEY (id);
ALTER TABLE oportunidad MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE oportunidad ADD CONSTRAINT `oportunidad_ibfk_1` FOREIGN KEY (`idProspecto`) REFERENCES `prospecto` (`id`),
					  ADD CONSTRAINT `oportunidad_ibfk_2` FOREIGN KEY (`idAsesorRegistra`) REFERENCES `trabajador` (`id`),
					  ADD CONSTRAINT `oportunidad_ibfk_3` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`);

ALTER table oportunidad ADD situacion CHAR(1) DEFAULT 'N' NOT NULL;


ALTER TABLE cotizacionauto ADD idOportunidad bigint(20) NULL;


ALTER TABLE cotizacionauto
ADD CONSTRAINT `cotizacionauto_ibfk_6`
  FOREIGN KEY (`idOportunidad`)
  REFERENCES oportunidad (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE reclamo ADD grado CHAR(1) NOT NULL;

ALTER TABLE reclamo 
  ADD solucion TEXT NULL,
  ADD fechaCierre DATETIME NULL;

INSERT INTO menu (`id`, `icono`, `nombre`, `nivel`, `accion`, `idHtml`, `idMenu`, `ordenItem`, `created_at`, `updated_at`) 
VALUES ('46', 'mdi mdi-account-file-outline', 'Cuentas por Cobrar & Pagar', '2', '/cuentaxcyp', 'cuentaxcyp', '8', '10', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO menuusuario (idCategoriaPersonal, idMenu, estado)
SELECT mu.idCategoriaPersonal, 46, mu.estado FROM menuusuario mu WHERE mu.idMenu = 31;


CREATE TABLE cuenta (
  `id` bigint(20) NOT NULL,
  `tipocuenta` int NOT NULL,
  `tipodocumento` CHAR(1) NOT NULL,
  `idProveedor` bigint(20) NULL,
  `idCliente` bigint(20) NULL,
  `unidad` int NULL,
  `tipo` CHAR(1) NULL,
  `partida` VARCHAR(20) NULL,
  `serie` VARCHAR(5) NOT NULL,
  `numero` VARCHAR(8) NOT NULL,
  `fechavencimiento` DATE NOT NULL,
  `importe` DECIMAL(10,3) NOT NULL,
  `moneda` CHAR(3) NOT NULL,
  `operacion` CHAR(1) NOT NULL,
  `sustento` TEXT NOT NULL,
  `tipocambio` DECIMAL(10,3) NOT NULL,
  `importeSoles` DECIMAL(10,3) NOT NULL,
  `estado`CHAR(1) DEFAULT 'P' NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE cuenta ADD PRIMARY KEY (id);
ALTER TABLE cuenta MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE cuenta ADD CONSTRAINT `cuenta_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `persona` (`id`),
				  ADD CONSTRAINT `cuenta_ibfk_2` FOREIGN KEY (`idProveedor`) REFERENCES `persona` (`id`),
				  ADD CONSTRAINT `cuenta_ibfk_3` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
				  ADD CONSTRAINT `cuenta_ibfk_4` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`);

ALTER TABLE cuenta ADD CONSTRAINT `cuenta_ibfk_5` UNIQUE (`tipocuenta`, `tipodocumento`, `idProveedor`, `idCliente`, `serie`, `numero`, `deleted_at`);

ALTER TABLE cuenta modify tipodocumento VARCHAR(3) NOT NULL;
ALTER TABLE cuenta modify unidad CHAR(3) NULL;
ALTER TABLE cuenta ADD tipogasto VARCHAR(3) NULL;
ALTER TABLE cuenta ADD saldo DECIMAL(10,3) NOT NULL;

CREATE TABLE cuentacontable (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);
ALTER TABLE cuentacontable ADD PRIMARY KEY (id);
ALTER TABLE cuentacontable MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE cuentacontable ADD CONSTRAINT `cuenta_contable__ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
				  ADD CONSTRAINT `cuenta_contable_ibfk_2` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`);

INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('33','ACTIVOS',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('331','Terrenos',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('332','Edificaciones',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('333','Maquinarias y Equipos de Explotacion',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('334','Unidades de Transporte',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('335','Muebles y Enseres',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('336','Equipos Diversos',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('339','Construcciones y Obras en Curso',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60','COSTO VARIABLE',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6033','Compras de productos a comercializar (Repuestos)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6011','Compras de vehiculos a comercializar',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6091','Servicio de transporte de productos a comercializar (fletes de productos)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6091','Servicio de transporte de vehículos (chófer de vehículos o grúas para vehículos)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60912','Seguros(soat vehiculos venta)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60912','Repuestos y Accesorios adicionales para venta de vehículos (consumos internos, pisos, accesorios, porta placas)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60912','Tramites (placas, tarjeta de propiedad, notaria)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60912','Servicios prestados por terceros(fabricacion de repuestos, planchado y pintura, polarizados)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('60912','Materiales e insumos: combustible para venta, para limpiar piezas, lijas, etc',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6369','Servicio de vigilancia y seguridad',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('635','Alquileres (en caso hubieran)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('62','GASTOS DE PERSONAL',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6253','Planilla de personal involucrado directamente en el proceso de ventas',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6252','Planilla de personal involucrado directamente en el proceso operativo',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('621201','Gastos de personal variable: Comisiones y bonos',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('63','GASTOS DE VENTAS',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6253','Gastos de representación: atenciones a clientes , restaurantes, obsequios',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('657','Activos menores: celulares del área comercial (No sobrepasan 1/4 UIT)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6253','Pasajes de colaborador area comercial',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632','Gastos de marketing: asesorías',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('637','Gastos de promocion y publicidad: merchandising, activaciones, folletería, diseños',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('634','Servicios de mantenimiento , instalaciones y reparaciones asignable',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('657','Activos menores asinables a área no comercial (celulares, herramientas) No sobrepasan 1/4 UIT',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('624','Capacitaciones personal asignable',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('624','Pasajes personal asignable',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('654','Licencias asignable',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632','Tramites asignables',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6251','Atenciones al personal asignables',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('39','GASTOS POR AMORT. Y DEPREC. ASIGNABLE',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6211','Planilla no asignable',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('6261','Bonos no asignables',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('636801','Combustible',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('657','Celulares y activos menores No sobrepasan 1/4 UIT',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('625','Atenciones al personal',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632103','Copias, impresiones',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632701','Materiales de limpieza, oficina u otros',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('625102','Movilidad',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('335','Mobiliario y equipos',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('636801','Gasolina (motos de la empresa)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('631201','Mensajeria',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632801','Servicios prestados por terceros ( alarmas,software)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632301','Servicios de contabilidad',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632102','Servicios de limpieza',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('634','Servicios de mantenimiento , instalaciones y reparaciones.',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('626','Pasajes aéreo y/o terrestre(gerencia)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('625303','Gastos Médicos(pruebas covid, etc)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('626','Seguros (no asignables) (póliza empresarial, póliza del gerente)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('654','Licencias (no asignables)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('636','Servicios públicos',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('626','Capacitaciones Gerencia',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('632203','Trámites y gastos notariales (no asignables)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('653','Inscripciones y membresias (no asignables)',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);
INSERT INTO  cuentacontable(codigo,nombre,created_at,updated_at, idPersonal) VALUES ('39','GASTOS POR AMORT. Y DEPREC. NO ASIGNABLE',CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,1);


CREATE TABLE reciboscuenta (
	`id` bigint(20) NOT NULL,
  `tipoOperacion` char(1) NOT NULL,
  `idCuenta`  bigint(20) NULL,
  `tipoIngreso` varchar(2) NULL,
  `tipoEgreso`  varchar(2) NULL,
  `tipoGasto`  varchar(3) NULL,
  `unidadNegocio` char(3)  NULL,
  `partidaCuenta` varchar(20) NULL,
  `sustento` TEXT NULL,
  `idCuentaContable`  bigint(20) NULL,
	`idPersonaAprueba`  bigint(20) NULL,
  `medioPago`  varchar(50) NULL,
	`idCliente`  bigint(20) NULL,
	`idProveedor`  bigint(20) NULL,
  `formaIngreso`  char(1) NULL,
	`tipoDocumento`  varchar(3) NOT NULL,
  `serie`  varchar(5) NOT NULL,
  `numero`  varchar(8) NOT NULL,
  `total`  decimal(10,3) NOT NULL,
  `moneda`  char(3) NOT NULL,
	`tipopago`  varchar(10) NOT NULL,
  `montopago`  varchar(10) NOT NULL,
  `descripcion` TEXT NULL,
  `idPersonal`  bigint(20) NOT NULL,
  `idPersonalEliminar` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE reciboscuenta ADD PRIMARY KEY (id);
ALTER TABLE reciboscuenta MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE reciboscuenta ADD CONSTRAINT `reciboscuenta_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `persona` (`id`),
				  ADD CONSTRAINT `reciboscuenta_ibfk_2` FOREIGN KEY (`idProveedor`) REFERENCES `persona` (`id`),
				  ADD CONSTRAINT `reciboscuenta_ibfk_3` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
				  ADD CONSTRAINT `reciboscuenta_ibfk_4` FOREIGN KEY (`idPersonalEliminar`) REFERENCES `trabajador` (`id`),
          ADD CONSTRAINT `reciboscuenta_ibfk_5` FOREIGN KEY (`idCliente`) REFERENCES `persona` (`id`),
				  ADD CONSTRAINT `reciboscuenta_ibfk_6` FOREIGN KEY (`idPersonaAprueba`) REFERENCES `trabajador` (`id`),
          ADD CONSTRAINT `reciboscuenta_ibfk_7` FOREIGN KEY (`idCuenta`) REFERENCES `cuenta` (`id`),
				  ADD CONSTRAINT `reciboscuenta_ibfk_8` FOREIGN KEY (`idCuentaContable`) REFERENCES `cuentacontable` (`id`)
				 ;

ALTER TABLE reciboscuenta ADD tipoCuentaEgreso char(1) NULL;

ALTER TABLE detallemovimiento ADD idRecibo bigint(20) NULL;
ALTER TABLE detallemovimiento ADD CONSTRAINT `detallemovimiento_ibfk_2` FOREIGN KEY (`idRecibo`) REFERENCES `reciboscuenta` (`id`);

ALTER TABLE reciboscuenta MODIFY tipopago char(1) DEFAULT 'T' NOT NULL;
ALTER TABLE detallemovimiento ADD moneda CHAR(3) DEFAULT 'PEN' NOT NULL;



-- REPORTE DE NOTIFICACIONES DE CUENTAS POR COBRAR Y  PAGAR
DELIMITER ;;
CREATE PROCEDURE getNotificacionesCuentasXCobrarPagar ()
BEGIN
SELECT a.modalidad, a.tipocuenta, COUNT(a.modalidad) cantidad 
FROM (SELECT c.tipocuenta, 
	(CASE DATEDIFF(c.fechavencimiento, CURRENT_DATE)  
  WHEN 7 THEN 'C7' 
  WHEN 15 THEN 'C15' 
  WHEN 0 THEN 'C0' 
  ELSE '' END) modalidad  FROM cuenta as c  
  WHERE (DATEDIFF(c.fechavencimiento, CURRENT_DATE) = 7 OR 
  DATEDIFF(c.fechavencimiento, CURRENT_DATE) = 15 OR 
  DATEDIFF(c.fechavencimiento, CURRENT_DATE) = 0) AND  
  c.estado = 'P' AND c.deleted_at IS NULL)  a GROUP BY a.modalidad, a.tipocuenta
  HAVING cantidad > 0;
END;; 
DELIMITER ;


-- NOTIFICACIONES DE INVENTARIO
DELIMITER ;;
CREATE PROCEDURE getNotificacionesInventario ()
BEGIN
SELECT ax.tipoProducto, ax.tipoAlerta, COUNT(ax.tipoProducto) cantidad FROM (
SELECT prod.id, (CASE prod.tipoProducto 
					WHEN 'A'  THEN 'Accesorio/Repuesto' 
					WHEN 'LL' THEN 'Neumáticos' 
					WHEN 'I'  THEN 'Insumos' 
					WHEN 'B'  THEN 'Baterías' 
					ELSE 'MUELLES' END) as tipoProducto, 
                    (CASE (st.totalCompras-st.totalVentas-st.totalIncidencias)
                    WHEN 0 THEN 'SS'
                    WHEN prod.stockMinimo THEN 'SM'
                    ELSE ''
                    END) as tipoAlerta
FROM producto as prod JOIN stockproducto st ON st.idProducto = prod.id
WHERE prod.deleted_at IS NULL AND ((st.totalCompras-st.totalVentas-st.totalIncidencias) = 0 OR 
prod.stockMinimo = (st.totalCompras-st.totalVentas-st.totalIncidencias))) ax
GROUP BY ax.tipoProducto, ax.tipoAlerta
HAVING cantidad > 0;
END;; 
DELIMITER ;

-- NOTIFICACIONES DE PROSPECTOS
DELIMITER ;;
CREATE PROCEDURE getNotificacionesProspectos ()
BEGIN
SELECT ax.tipoAlerta, COUNT(ax.tipoAlerta) cantidad FROM (
SELECT pros.id, pros.created_at, pros.tiempoEstimadoCompra, (
CASE DATEDIFF(CURRENT_DATE, pros.created_at)
  WHEN 7 THEN 'A7'
  WHEN 15 THEN 'A15'
  WHEN 30 THEN 'A30'
  ELSE 'AV'
END) tipoAlerta FROM prospecto as pros
WHERE pros.deleted_at IS NULL AND (DATEDIFF(pros.tiempoEstimadoCompra, CURRENT_DATE) = 1 OR (
DATEDIFF(CURRENT_DATE, pros.created_at) = 7 OR 
DATEDIFF(CURRENT_DATE, pros.created_at) = 15 OR 
DATEDIFF(CURRENT_DATE, pros.created_at) = 30
))) ax
GROUP BY ax.tipoAlerta
HAVING cantidad > 0;
END;; 
DELIMITER ;

-- NOTIFICACIONES DE ORDEN DE TRABAJO
DELIMITER ;;
CREATE PROCEDURE getNotificacionesOrdenesTrabajo ()
BEGIN
SELECT DATE_FORMAT(ot.fecha, '%d/%m/%Y') as fecha,
(CASE WHEN ot.situacion = 'F' AND 
 ot.situacionFacturado = 'N' AND 
 DATEDIFF(CURRENT_DATE, ot.updated_at) > 1 THEN 'A1' ELSE 'A2' END) tipoAlerta, 
COUNT(ot.fecha) as total
FROM ordentrabajo as ot WHERE ot.deleted_at IS NULL AND 
((ot.situacion = 'F' AND ot.situacionFacturado = 'N' AND 
DATEDIFF(CURRENT_DATE, ot.updated_at) > 1) OR 
(ot.situacion = 'F' AND DATEDIFF(created_at, updated_at) = 0))
GROUP BY ot.fecha, tipoAlerta
HAVING total > 0
ORDER BY ot.fecha DESC;
END;; 
DELIMITER ;

-- NOTIFICACIONES DE COTIZACION SIN ORDEN
DELIMITER ;;
CREATE PROCEDURE getNotificacionesCotizacionSinOrden ()
BEGIN
SELECT c.fecha, (CASE DATEDIFF(CURRENT_DATE, c.created_at) 
	WHEN 1 THEN 'A1'
    WHEN 2 THEN 'A2'
    WHEN 3 THEN 'A3'
    ELSE '' END) tipoAlerta, COUNT(c.fecha) total FROM cotizacion c WHERE c.situacion = 'U' AND c.situacionFacturado = 'N'
AND NOT EXISTS (SELECT * FROM detalleordentrabajo dto WHERE dto.idCotizacion = c.id AND dto.deleted_at IS NULL)
AND DATEDIFF(CURRENT_DATE, c.created_at) BETWEEN 1 AND 3
GROUP BY c.fecha, tipoAlerta
HAVING total > 0;
END;; 
DELIMITER ;

-- NOTIFICACIONES DE ORDENES SIN INICIAR
DELIMITER ;;
CREATE PROCEDURE getNotificacionesOrdenSinIniciar ()
BEGIN
SELECT ot.fecha, 
 (CASE DATEDIFF(CURRENT_DATE, ot.created_at) 
	WHEN 1 THEN 'A1'
    WHEN 2 THEN 'A2'
    WHEN 3 THEN 'A3'
    ELSE '' END) tipoAlerta, COUNT(ot.fecha) total
 FROM ordentrabajo ot WHERE ot.situacion = 'V' AND ot.deleted_at IS NULL
AND DATEDIFF(CURRENT_DATE, ot.created_at) BETWEEN 1 AND 3
GROUP BY ot.fecha, tipoAlerta
HAVING total > 0;
END;; 
DELIMITER ;

-- NOTIFICACION DE ENCUESTAS SIN LLENAR
DELIMITER ;;
CREATE PROCEDURE getNotificacionesEncuestaSinLlenar ()
BEGIN
SELECT YEAR(ot.fecha) anio, COUNT(ot.fecha) as total FROM ordentrabajo as ot 
WHERE ot.deleted_at IS NULL AND ot.puntuacionEncuesta = -1 AND ot.situacion = 'F'
GROUP BY YEAR(ot.fecha)
HAVING total > 0;
END;; 
DELIMITER ;

CREATE TABLE mensajesistema (
  `id` bigint(20) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `idPersonal` bigint(20) NOT NULL,
  `idPersonalMostrar` bigint(20) NOT NULL,
  `idReclamo` bigint(20) NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

ALTER TABLE mensajesistema ADD PRIMARY KEY (id);
ALTER TABLE mensajesistema MODIFY id bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE mensajesistema 
	ADD CONSTRAINT `mensaje_sistema_ibfk_1` FOREIGN KEY (`idPersonal`) REFERENCES `trabajador` (`id`),
	ADD CONSTRAINT `mensaje_sistema_ibfk_2` FOREIGN KEY (`idPersonalMostrar`) REFERENCES `trabajador` (`id`),
  ADD CONSTRAINT `mensaje_sistema_ibfk_3` FOREIGN KEY (`idReclamo`) REFERENCES `reclamo` (`id`);

ALTER TABLE mensajesistema ADD tipo char(1) DEFAULT 'E' NOT NULL;

DELIMITER ;;
CREATE PROCEDURE getMensajesSistema (IN `usuarioId` BIGINT(20))
BEGIN
SELECT r.tipo, r.titulo, r.descripcion as mensaje FROM mensajesistema as r 
WHERE r.deleted_at IS NULL AND r.idPersonalMostrar=usuarioId AND 
((CURRENT_DATE BETWEEN r.fechaInicio AND r.fechaFin) OR (r.fechaInicio IS NULL AND r.fechaFin IS NULL));
END;; 
DELIMITER ;

ALTER TABLE mensajesistema ADD fechaInicio DATE NULL;
ALTER TABLE mensajesistema ADD fechaFin DATE NULL;
ALTER TABLE mensajesistema ADD idProspecto BIGINT(20) NULL;
ALTER TABLE mensajesistema ADD idOportunidad BIGINT(20) NULL;

ALTER TABLE mensajesistema 
	ADD CONSTRAINT `mensaje_sistema_ibfk_4` FOREIGN KEY (`idProspecto`) REFERENCES `prospecto` (`id`),
	ADD CONSTRAINT `mensaje_sistema_ibfk_5` FOREIGN KEY (`idOportunidad`) REFERENCES `oportunidad` (`id`);

ALTER TABLE persona ADD tipoProveedor CHAR(1) NULL;

INSERT INTO preguntasencuesta (`nombre`, `conRespuesta`, `created_at`, `updated_at`) VALUES ('Estado de Contactabilidad', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE detallecompraauto ADD vin varchar(20) NULL;
ALTER TABLE loteauto ADD vin varchar(20) NULL;

ALTER TABLE reciboscuenta ADD tipocambio DECIMAL(10,3) DEFAULT 1 NOT NULL;