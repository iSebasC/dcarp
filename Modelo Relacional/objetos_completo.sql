

DELIMITER ;;
CREATE FUNCTION `getMonedaPrecio`(`fecha` VARCHAR(30), `tipo_detalle` CHAR(1), `producto` BIGINT(20), `guia` BIGINT(20)) RETURNS varchar(20) CHARSET latin1
BEGIN
DECLARE returnDat VARCHAR(20);
SET returnDat = NULL;

IF tipo_detalle IS NOT NULL THEN

    IF tipo_detalle = 'G' THEN
        SET returnDat = (SELECT GROUP_CONCAT(A2.tipoMoneda,'-', A2.preciocompra) FROM (SELECT DISTINCT g.tipoMoneda, dg.preciocompra FROM guia as g 
                           JOIN detalleguia as dg ON g.id = dg.idGuia 
                           JOIN lote as l ON l.idGuiaCompra = g.id
                           JOIN stockproductodetallesalida as spds ON spds.idProducto = dg.idProducto
                           WHERE spds.idGuia = guia AND dg.idProducto = l.idProducto AND g.idTipoGuia = 2 AND dg.created_at <= fecha
                          AND dg.id IS NOT NULL AND dg.idProducto = producto ORDER BY dg.id LIMIT 1) as A2);
    ELSE
        SET returnDat = (SELECT GROUP_CONCAT(A2.tipoMoneda,'-', A2.preciocompra) FROM (SELECT DISTINCT c.tipoMoneda, dc.preciocompra FROM compra as c 
                            JOIN detallecompra as dc ON c.id = dc.idCompra 
                            JOIN lote as l ON l.idCompra = c.id 
                            JOIN stockproductodetallesalida as spds ON spds.idProducto = dc.idProducto
                          WHERE spds.idGuia =guia AND dc.idProducto = l.idProducto AND dc.created_at <= fecha
                          AND dc.id IS NOT NULL AND dc.idProducto = producto ORDER BY dc.id LIMIT 1) as A2);
    END IF;
END IF;
RETURN returnDat;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `getEgresosComprobanteProducto`(IN `almacenId` BIGINT(20), IN `productoId` BIGINT(20))
    NO SQL
BEGIN
CREATE TEMPORARY TABLE tmp_egresos_prod select `dc`.`cantidad`, 'SALIDA' as movimiento, DATE_FORMAT(ord.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(do.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, (CASE WHEN dc.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado, 'ORDEN DE TRABAJO' as tipoDoc, (SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
l.id = dc.idLote AND l.idProducto = dc.idProducto) as det_compra, (SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND l.id = dc.idLote AND l.idProducto = dc.idProducto) as det_guia, CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = 10),LPAD(ord.serie,2,'0'),'-',LPAD(ord.numero,8,'0')) as documento, `do`.`created_at` from `ordentrabajo` as `ord` inner join `detalleordentrabajo` as `do` on `do`.`idOrdenTrabajo` = `ord`.`id` inner join `detallecotizacion` as `dc` on `dc`.`idCotizacion` = `do`.`idCotizacion` inner join `persona` as `prov` on `prov`.`id` = `ord`.`idCliente` where `ord`.`idAlmacenSalida` = almacenId and `dc`.`idProducto` = productoId

UNION ALL
select `dv`.`cantidad`, 'SALIDA' as movimiento, DATE_FORMAT(vt.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dv.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, 
(CASE WHEN vt.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'E' ELSE 'A' END) as situacion FROM anulacion as an WHERE an.idVenta = vt.id) ELSE vt.situacion END) as estado,
'VENTA' as tipoDoc, (SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra, (SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia, CONCAT(vt.tipoComprobante,LPAD(vt.serie,3,'0'),'-',LPAD(vt.numero,8,'0')) as documento, `dv`.`created_at` from `venta` as `vt` inner join `detalleventa` as `dv` on `dv`.`idVenta` = `vt`.`id` inner join `persona` as `prov` on `prov`.`id` = `vt`.`idCliente` inner join `pagodetalle` as `pd` on `pd`.`idDetalleVenta` = `dv`.`id` where `vt`.`idAlmacenSalida` = almacenId and `dv`.`idProducto` = productoId and `pd`.`idCotizacion` is null and `pd`.`idOrden` is null

UNION ALL

select sds.stock as cantidad, 'SALIDA' as movimiento, DATE_FORMAT(vt.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dv.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, 
(CASE WHEN vt.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'E' ELSE 'A' END) as situacion FROM anulacion as an WHERE an.idVenta = vt.id) ELSE vt.situacion END) as estado, 'VENTA' as tipoDoc, 

(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
JOIN lote as l ON l.idCompra = c.id JOIN stockproductodetalle as spd ON spd.idLote = l.id
 WHERE dc.idProducto = l.idProducto AND sds.idStockProductoDetalle = spd.id AND l.idProducto = dcot.idProducto) as det_compra, 

(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia
JOIN lote as l ON l.idGuiaCompra = g.id JOIN stockproductodetalle as spd ON spd.idLote = l.id
WHERE dg.idProducto = l.idProducto AND sds.idStockProductoDetalle = spd.id AND l.idProducto = dcot.idProducto) as det_guia,

CONCAT(vt.tipoComprobante,LPAD(vt.serie,3,'0'),'-',LPAD(vt.numero,8,'0')) as documento, `dv`.`created_at` from `venta` as `vt` inner join `detalleventa` as `dv` on `dv`.`idVenta` = `vt`.`id` inner join `persona` as `prov` on `prov`.`id` = `vt`.`idCliente` inner join `pagodetalle` as `pd` on `pd`.`idDetalleVenta` = `dv`.`id` 
inner join `detallecotizacion` as `dcot` on `dcot`.`idCotizacion` = `pd`.`idCotizacion` 
inner join stockproductodetallesalida sds ON sds.idProducto =  dcot.idProducto
where dcot.idProducto = dv.idProducto and `vt`.`idAlmacenSalida` = 2 and `dv`.`idProducto` = 9677 and `pd`.`idCotizacion` is not null and `pd`.`idOrden` is null
and sds.idAlmacen = vt.idAlmacenSalida AND sds.idVenta = vt.id
AND dcot.deleted_at IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `getEgresosGuiaProducto`(IN `almacenId` BIGINT(20), IN `productoId` BIGINT(20))
    NO SQL
BEGIN
CREATE TEMPORARY TABLE tmp_egresos_guia AS SELECT A.cantidad, A.movimiento, A.fecha, A.fechaReg, A.persona, A.estado, A.tipoDoc, (CASE WHEN A.tipo_detalle = 'C' THEN getMonedaPrecio(A.created_at, A.tipo_detalle, productoId, A.id) ELSE NULL END) as det_compra, 
(CASE WHEN A.tipo_detalle = 'G' THEN getMonedaPrecio(A.created_at, A.tipo_detalle, productoId, A.id) ELSE NULL END) as det_guia, A.documento, A.created_at 
                      FROM (select DISTINCT `dg`.`cantidad`, `spd`.`id` as id_2, 
                            (CASE g.idTipoGuia WHEN 2 THEN 'ENTRADA' ELSE 'SALIDA' END) as movimiento, 
                            DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha,DATE_FORMAT(dg.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, 
                            '-' as persona, (CASE WHEN dg.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado, 'GUIA DE EGRESO' as tipoDoc, 
                 (SELECT (CASE WHEN dc.id IS NOT NULL THEN 'C' ELSE NULL END) as det FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
JOIN lote as l ON l.idCompra = c.id JOIN stockproductodetalle spd ON spd.idProducto = l.idProducto WHERE 
dc.idProducto = l.idProducto AND spd.idLote = l.id AND l.idProducto = dg.idProducto AND dc.created_at <= dg.created_at
AND dc.id IS NOT NULL UNION ALL
                        SELECT (CASE WHEN dg.id IS NOT NULL THEN 'G' ELSE NULL END) as det FROM guia as g JOIN detalleguia as dg2 ON g.id = dg2.idGuia 
JOIN lote as l ON l.idGuiaCompra = g.id JOIN stockproductodetalle spd ON spd.idProducto = l.idProducto WHERE 
dg2.idProducto = l.idProducto AND spd.idLote = l.id AND l.idProducto = dg2.idProducto AND dg2.idProducto = dg.idProducto AND dg2.created_at <= dg.created_at 
 AND dg.id IS NOT NULL LIMIT 1) as tipo_detalle, NULL as det_guia, CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = g.idTipoGuia),LPAD(g.serie,2,'0'),'-',LPAD(g.numero,8,'0')) as documento, `dg`.`created_at`,`g`.`id`
                            from `guia` as `g` 
                            inner join `detalleguia` as `dg` on `dg`.`idGuia` = `g`.`id`
                            inner join `stockproductodetallesalida` as `spd` on `spd`.`idGuia` = `g`.`id`
                            where `spd`.`idProducto` = `dg`.`idProducto` and `spd`.`stock` = `dg`.`cantidad` and `g`.`idAlmacen` = almacenId and `dg`.`idProducto` = productoId and `g`.`idTipoGuia` = 4
                           ) as A WHERE A.tipo_detalle is not null;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `getIngresosEgresosNotaProducto`(IN `almacenId` BIGINT(20), IN `productoId` BIGINT(20))
    NO SQL
BEGIN
CREATE TEMPORARY TABLE tmp_ingresos_eg_nota AS select `dan`.`cantidad`, 'SALIDA' as movimiento, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dan.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, (CASE WHEN dan.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado, (CASE an.tipoNota WHEN 'C' THEN 'NOTA DE CRÉDITO' ELSE 'NOTA DE DÉBITO' END) as tipoDoc, CONCAT(c.tipoMoneda,'-',dc.preciocompra) as det_compra, NULL as det_guia, an.documentoCompra as documento, `dan`.`created_at` from `anulacionnotas` as `an` inner join `detalleanulacionnotas` as `dan` on `dan`.`idAnulacion` = `an`.`id` inner join `compra` as `c` on `c`.`id` = `an`.`idCompra` inner join `detallecompra` as `dc` on `dc`.`idCompra` = `c`.`id` inner join `persona` as `prov` on `prov`.`id` = `c`.`idProveedor` where dan.idProducto = dc.idProducto and `c`.`idalmacen` = almacenId and `dc`.`idProducto` = productoId 

UNION ALL

select `dan`.`cantidad`, 'ENTRADA' as movimiento, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dan.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, 
(CASE an.situacion WHEN 'V' THEN 'A' ELSE 'E' END) estado, (CASE an.tipoNota WHEN 'C' THEN 'NOTA DE CRÉDITO' ELSE 'NOTA DE DÉBITO' END) as tipoDoc, (SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra, (SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia, CONCAT(vt.tipoComprobante,an.tipoNota, LPAD(an.serie,2,'0'),'-',LPAD(an.numero,8,'0')) as documento, `dan`.`created_at` from `anulacionnotas` as `an` inner join `detalleanulacionnotas` as `dan` on `dan`.`idAnulacion` = `an`.`id` inner join `venta` as `vt` on `vt`.`id` = `an`.`idVenta` inner join `detalleventa` as `dv` on `dv`.`idVenta` = `vt`.`id` inner join `persona` as `prov` on `prov`.`id` = `vt`.`idCliente` where dan.idProducto = dv.idProducto and `vt`.`idAlmacenSalida` = almacenId and `dv`.`idProducto` = productoId;


END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `getIngresosProducto`(IN `almacenId` BIGINT(20), IN `productoId` BIGINT(20))
    NO SQL
BEGIN
	CREATE TEMPORARY TABLE tmp_ingresos_prod AS select `dc`.`cantidad`, 'ENTRADA' as movimiento, DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dc.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, (CASE prov.tipoDocumento WHEN 'PJ' THEN prov.razonSocial ELSE CONCAT(prov.apellidos,' ', prov.nombres) END) as persona, (CASE WHEN dc.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado, (CASE c.tipoDocumento WHEN 'F' THEN 'FACTURA' ELSE 'BOLETA' END) as tipoDoc, CONCAT(c.tipoMoneda,'-',dc.preciocompra) as det_compra, NULL as det_guia, CONCAT(c.tipoDocumento,c.documento) as documento, `dc`.`created_at` from `compra` as `c` inner join `detallecompra` as `dc` on `dc`.`idCompra` = `c`.`id` inner join `persona` as `prov` on `prov`.`id` = `c`.`idProveedor` where `c`.`idalmacen` = almacenId and `dc`.`idProducto` = productoId
UNION ALL
select `dg`.`cantidad`, (CASE g.idTipoGuia WHEN 2 THEN 'ENTRADA' ELSE 'SALIDA' END) as movimiento, DATE_FORMAT(g.fecha,'%d/%m/%Y') as fecha, DATE_FORMAT(dg.created_at,'%d/%m/%Y %H:%i:%s') as fechaReg, '-' as persona, (CASE WHEN dg.deleted_at IS NULL THEN 'A' ELSE 'E' END) estado, 'GUIA DE INGRESO' as tipoDoc, NULL as det_compra, CONCAT(g.tipoMoneda,'-',dg.preciocompra) as det_guia, CONCAT((SELECT abreviatura FROM tipodocumento WHERE id = g.idTipoGuia),LPAD(g.serie,2,'0'),'-',LPAD(g.numero,8,'0')) as documento, `dg`.`created_at` from `guia` as `g` inner join `detalleguia` as `dg` on `dg`.`idGuia` = `g`.`id` where `g`.`idAlmacen` = almacenId and `dg`.`idProducto` = productoId and `g`.`idTipoGuia` = 2;


END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarCompras`()
BEGIN 
    CREATE TEMPORARY TABLE tmp_compras AS SELECT
        p.nombre as tipo,
        p.nombre,
        dc.descripcion,
        p.tipoProducto,
        cl.documento as docProveedor, (
            CASE
                WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos, ' ', cl.nombres)
                ELSE cl.razonSocial
            END
        ) as proveedor,
        dc.cantidad, (dc.preciocompra * c.tipoCambio) as precio, (dc.subtotal * c.tipoCambio) as subtotal,
        (c.igv * c.tipoCambio) as igv, (c.total * c.tipoCambio) as total,
        DATE_FORMAT(c.fecha, '%d/%m/%Y') as fecha,
        dc.created_at,
        c.tipoDocumento,
        c.documento, (
            CASE
                WHEN c.deleted_at IS NULL THEN 'V'
                ELSE 'A'
            END
        ) as situacion,
        c.tipoMoneda as moneda,
        (SELECT COUNT(dct.id) as cantidad FROM detallecompra dct WHERE dct.idCompra = c.id) as cant_items
    FROM compra as c
        JOIN detallecompra as dc ON dc.idCompra = c.id
        JOIN producto as p ON p.id = dc.idProducto
        JOIN persona as cl ON cl.id = c.idProveedor;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarComprasAuto`()
BEGIN 
 CREATE TEMPORARY TABLE tmp_compras_auto AS SELECT
    ma.nombre as tipo,
    CONCAT(ma.nombre,'-', a.version,'-', a.transmision,'-', a.descripcion) as nombre,
    dc.descripcion,
    CONCAT(ma.nombre,'-', a.version,'-', a.transmision,'-', a.descripcion) as tipoProducto,
    cl.documento as docProveedor, (
        CASE
            WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos, ' ', cl.nombres)
            ELSE cl.razonSocial
        END
    ) as proveedor,
    dc.cantidad, (dc.preciocompra * c.tipoCambio) as precio, (dc.subtotal * c.tipoCambio) as subtotal,
    (c.igv * c.tipoCambio) as igv, (c.total * c.tipoCambio) as total,
    DATE_FORMAT(c.fecha, '%d/%m/%Y') as fecha,
    dc.created_at,
    c.tipoDocumento,
    c.documento, (
        CASE
            WHEN c.deleted_at IS NULL THEN 'V'
            ELSE 'A'
        END
    ) as situacion,
    c.tipoMoneda as moneda,
    (SELECT COUNT(dct.id) as cantidad FROM detallecompraauto dct WHERE dct.idCompra = c.id) as cant_items
    FROM compraauto as c
    JOIN detallecompraauto as dc ON dc.idCompra = c.id
    JOIN auto as a ON a.id = dc.idAuto
    JOIN marcaauto as ma ON ma.id = a.marcaId
    JOIN persona as cl ON cl.id = c.idProveedor;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleAutoNota`()
BEGIN
CREATE TEMPORARY TABLE tmp_auto_nota AS SELECT ma.nombre as macro, p.descripcion as tipo, p.descripcion as nombre, dtan.descripcion, 'Auto' as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v.tipoCambio * -1) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv,(an.total * v.tipoCambio * -1) as total, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, dtan.created_at, an.tipoNota as tipoComprobante, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento, an.situacion, v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas dvt WHERE dvt.idAnulacion = an.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compraauto as c JOIN detallecompraauto as dc ON c.id = dc.idCompra 
              JOIN loteauto as l ON l.idCompra = c.id WHERE dc.idAuto = l.idAuto AND 
              l.id = dv.idLoteAuto AND l.idAuto = dv.idAuto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN loteauto as l ON l.idGuiaCompra = g.id WHERE dg.idAuto = l.idAuto AND 
              l.id = dv.idLoteAuto AND l.idAuto = dv.idAuto) as det_guia
FROM anulacionnotas as an
JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion = an.id
JOIN venta as v ON v.id = an.idVenta
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN auto as p ON p.id = dv.idAuto
JOIN marcaauto as ma ON ma.id = p.marcaId
JOIN persona as cl ON cl.id = v.idCliente
WHERE dtan.idAuto = dv.idAuto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleOtrosNota`()
BEGIN 
CREATE TEMPORARY TABLE tmp_otros_nota AS 
SELECT NULL as macro, dtan.descripcion as tipo, dtan.descripcion as nombre, dtan.descripcion, dtan.descripcion as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v.tipoCambio * -1) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv,(an.total * v.tipoCambio * -1) as total, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, dtan.created_at, an.tipoNota as tipoComprobante, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento, an.situacion, v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas dvt WHERE dvt.idAnulacion = an.id) as cant_items,
NULL as det_compra, NULL as det_guia
FROM anulacionnotas as an
JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion = an.id
JOIN venta as v ON v.id = an.idVenta
JOIN persona as cl ON cl.id = v.idCliente
WHERE dtan.idServicio IS NULL AND dtan.idProducto IS NULL;


END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleProductoNota`()
BEGIN
CREATE TEMPORARY TABLE tmp_producto_nota AS SELECT tp.nombre as macro, p.nombre as tipo, p.nombre, dtan.descripcion, p.tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v.tipoCambio * -1) as subtotal, v.igv_sunat, (an.igv * v.tipoCambio) as igv,(an.total * v.tipoCambio * -1) as total, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, dtan.created_at, an.tipoNota as tipoComprobante, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento, an.situacion, v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas dvt WHERE dvt.idAnulacion = an.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', (dc.preciocompra*c.tipoCambio)) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
              l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', (dg.preciocompra*g.tipoCambio)) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
              l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia
FROM anulacionnotas as an
JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion = an.id
JOIN venta as v ON v.id = an.idVenta
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN producto as p ON p.id = dv.idProducto
JOIN persona as cl ON cl.id = v.idCliente
JOIN tipoproducto as tp ON tp.id = p.idMargenGanancia
WHERE dtan.idProducto = dv.idProducto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetallesAnulacion`(IN `idVenta` BIGINT(20))
BEGIN
SELECT A.id, A.item, A.descripcion, ROUND(A.precio,2) as preciocompra, 
ROUND(A.cantidad - (CASE WHEN A.disminuido IS NULL THEN 0 ELSE A.disminuido END),2) as cantidad, 
ROUND(A.cantidad - (CASE WHEN A.disminuido IS NULL THEN 0 ELSE A.disminuido END),2) as cantidadmax,
ROUND(((A.cantidad - (CASE WHEN A.disminuido IS NULL THEN 0 ELSE A.disminuido END))* A.precio),3) as subtotal,
(CASE A.tipoMoneda WHEN 'PEN' THEN 'S' ELSE 'D' END) as tipoMoneda,
'false' as isAddItf
FROM (SELECT v.tipomoneda, dt.item, dt.descripcion, dt.id, dt.cantidad, dt.precio as precio, 
      (SELECT SUM(dan.cantidad) FROM detalleanulacionnotas as dan JOIN anulacionnotas as an ON an.id = dan.idAnulacion 
       WHERE an.idVenta = v.id AND (dan.idProducto = dt.idProducto OR dan.idServicio = dt.idServicio OR dan.idAuto = dt.idAuto)) as disminuido 
  FROM venta as v JOIN detalleventa as dt ON dt.idVenta = v.id WHERE v.id = idVenta ORDER BY dt.item) as A
  WHERE FORMAT(A.cantidad - (CASE WHEN A.disminuido IS NULL THEN 0 ELSE A.disminuido END),2) > 0;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleServicioCotizacion`()
BEGIN
CREATE TEMPORARY TABLE tmp_servicio_cot AS SELECT ms.descripcion as macro, cats.nombre as tipo, s.nombre, dv.descripcion, s.nombre as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion, 
v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
NULL as det_compra, NULL as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id 
JOIN servicio as s ON s.id = dv.idServicio
JOIN categoriaservicio as cats ON cats.id = s.idCategoriaServicio
JOIN persona as cl ON cl.id = v.idCliente
JOIN detallecotizacion as dcot ON dcot.idCotizacion = pd.idCotizacion
JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
LEFT JOIN macroservicio as ms ON ms.id = s.idMacroServicio
WHERE pd.idDetalleVenta = dv.id AND pd.idCotizacion IS NOT NULL AND dcot.idServicio = dv.idServicio;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleServicioNota`()
BEGIN
CREATE TEMPORARY TABLE tmp_servicio_nota AS SELECT ms.descripcion as macro, cats.nombre as tipo, s.nombre, dtan.descripcion, s.nombre as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v.tipoCambio * -1) as subtotal, v.igv_sunat, (an.igv * v.tipoCambio) as igv,(an.total * v.tipoCambio * -1) as total, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, dtan.created_at, an.tipoNota as tipoComprobante, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie,2,'0') ,'-', LPAD(an.numero,8,'0')) as documento, an.situacion, v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas dvt WHERE dvt.idAnulacion = an.id) as cant_items,
NULL as det_compra, NULL as det_guia
FROM anulacionnotas as an
JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion = an.id
JOIN venta as v ON v.id = an.idVenta
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN servicio as s ON s.id = dv.idServicio
JOIN categoriaservicio as cats ON cats.id = s.idCategoriaServicio
JOIN persona as cl ON cl.id = v.idCliente
LEFT JOIN macroservicio as ms ON ms.id = s.idMacroServicio
WHERE dtan.idServicio = dv.idServicio;

END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleServicioOrden`()
BEGIN
CREATE TEMPORARY TABLE tmp_servicio_od AS SELECT ms.descripcion as macro, cats.nombre as tipo, s.nombre, dv.descripcion, s.nombre as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion, 
v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
NULL as det_compra, NULL as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id 
JOIN servicio as s ON s.id = dv.idServicio
JOIN categoriaservicio as cats ON cats.id = s.idCategoriaServicio
JOIN persona as cl ON cl.id = v.idCliente
JOIN detalleordentrabajo as dot ON dot.idOrdenTrabajo = pd.idOrden
JOIN detallecotizacion as dcot ON dcot.idCotizacion = dot.idCotizacion
JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
LEFT JOIN macroservicio as ms ON ms.id = s.idMacroServicio
WHERE pd.idDetalleVenta = dv.id AND pd.idCotizacion IS NULL AND dcot.idServicio = dv.idServicio
AND dot.idOrdenTrabajo = pd.idOrden;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaAuto`()
    NO SQL
    COMMENT 'Lista ventas directas de autos'
CREATE TEMPORARY TABLE tmp_auto AS SELECT ma.nombre as macro, ma.nombre as tipo, CONCAT(ma.nombre,'-', p.version,'-', p.transmision,'-', p.descripcion) as nombre,
  dv.descripcion, CONCAT(ma.nombre,'-', p.version,'-', p.transmision,'-', p.descripcion) as tipoProducto, cl.documento as docCliente,
  (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos, ' ', cl.nombres) ELSE cl.razonSocial END ) as cliente,
  dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
  v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha,
  dv.created_at, v.tipoComprobante, CONCAT( v.tipoComprobante, LPAD(v.serie, 3, '0'), '-', LPAD(v.numero, 8, '0')) as documento, (CASE WHEN v.situacion = 'A' THEN ( SELECT ( CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END ) as situacion FROM anulacion as an WHERE an.idVenta = v.id )   ELSE v.situacion END ) as situacion,
  v.tipoMoneda as moneda, (SELECT COUNT(dvt.id) as cantidad FROM   detalleventa dvt WHERE   dvt.idVenta = v.id ) as cant_items, ( SELECT GROUP_CONCAT(c.tipoMoneda, '-', (dc.preciocompra * c.tipoCambio)) FROM   compraauto as c   JOIN detallecompraauto as dc ON c.id = dc.idCompra   JOIN loteauto as l ON l.idCompra = c.id WHERE   dc.idAuto = l.idAuto   AND l.id = dv.idLoteAuto   AND l.idAuto = dv.idAuto
  ) as det_compra, (SELECT GROUP_CONCAT(g.tipoMoneda, '-', (dg.preciocompra*g.tipoCambio)) FROM   guia as g   JOIN detalleguia as dg ON g.id = dg.idGuia   JOIN loteauto as l ON l.idGuiaCompra = g.id WHERE   dg.idAuto = l.idAuto   AND l.id = dv.idLoteAuto   AND l.idAuto = dv.idAuto
  ) as det_guia
FROM venta as v
  JOIN detalleventa as dv ON dv.idVenta = v.id
  JOIN pagodetalle as pd ON dv.id = pd.idDetalleVenta
  AND pd.idVenta = v.id
  JOIN auto as p ON p.id = dv.idAuto
  JOIN marcaauto as ma ON ma.id = p.marcaId
  JOIN persona as cl ON cl.id = v.idCliente
WHERE
  pd.idCotizacion IS NULL
  AND pd.idOrden IS NULL ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaCotizacion`()
BEGIN
CREATE TEMPORARY TABLE tmp_producto_cot AS SELECT tp.nombre as macro, p.nombre as tipo, p.nombre, dv.descripcion, p.tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal* v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion, 
v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', (dc.preciocompra * c.tipoCambio)) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', (dg.preciocompra * g.tipoCambio)) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id 
JOIN producto as p ON p.id = dv.idProducto
JOIN persona as cl ON cl.id = v.idCliente
JOIN detallecotizacion as dcot ON dcot.idCotizacion = pd.idCotizacion
JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
JOIN tipoproducto as tp ON tp.id = p.idMargenGanancia
WHERE pd.idDetalleVenta = dv.id AND pd.idCotizacion IS NOT NULL AND dcot.idProducto = dv.idProducto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaOrden`()
    NO SQL
    COMMENT 'Lista ventas por orden de trabajo de productos'
BEGIN
CREATE TEMPORARY TABLE tmp_producto_od AS SELECT tp.nombre as macro, p.nombre as tipo, p.nombre, dv.descripcion, p.tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv , (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion,
v.tipoMoneda as moneda, 
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', (dc.preciocompra * c.tipoCambio)) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', (dg.preciocompra*g.tipoCambio)) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id 
JOIN producto as p ON p.id = dv.idProducto
JOIN persona as cl ON cl.id = v.idCliente
JOIN detalleordentrabajo as dot ON dot.idOrdenTrabajo = pd.idOrden
JOIN detallecotizacion as dcot ON dcot.idCotizacion = dot.idCotizacion
JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
JOIN tipoproducto as tp ON tp.id = p.idMargenGanancia
WHERE pd.idDetalleVenta = dv.id AND pd.idCotizacion IS NULL AND dcot.idProducto = dv.idProducto 
AND dot.idOrdenTrabajo = pd.idOrden;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaOrdenNoHomologado`()
    NO SQL
    COMMENT 'Lista ventas por orden de trabajo de productos no homologado'
BEGIN
CREATE TEMPORARY TABLE tmp_producto_od_nh AS SELECT DISTINCT tp.nombre as macro, p.nombre as tipo, p.nombre, dv.descripcion, p.tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv , (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion,
v.tipoMoneda as moneda, 
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
              l.id = dcot.idLote AND l.idProducto = dcot.idProducto) as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id 
JOIN producto as p ON p.id = dv.idProducto
JOIN persona as cl ON cl.id = v.idCliente
JOIN detalleordentrabajo as dot ON dot.idOrdenTrabajo = pd.idOrden
JOIN detallecotizacion as dcot ON dcot.idCotizacion = dot.idCotizacion
LEFT JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
JOIN tipoproducto as tp ON tp.id = p.idMargenGanancia
WHERE pd.idDetalleVenta = dv.id AND pd.idCotizacion IS NULL AND dcot.idProducto = dv.idProducto 
AND dot.idOrdenTrabajo = pd.idOrden AND dhom.id IS NULL AND (SELECT COUNT(dhom.id) as cantidad FROM detallehomologacion dhom WHERE dhom.idDetalleVenta = dv.id) = 0;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaProducto`()
    NO SQL
    COMMENT 'Lista ventas directas de productos'
BEGIN
CREATE TEMPORARY TABLE tmp_producto AS SELECT tp.nombre as macro, p.nombre as tipo, p.nombre, dv.descripcion, p.tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion,
v.tipoMoneda as moneda, 
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', (dc.preciocompra*c.tipoCambio)) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id WHERE dc.idProducto = l.idProducto AND 
              l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_compra,
             (SELECT GROUP_CONCAT(g.tipoMoneda,'-', (dg.preciocompra * g.tipoCambio)) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id WHERE dg.idProducto = l.idProducto AND 
              l.id = dv.idLote AND l.idProducto = dv.idProducto) as det_guia
FROM venta as v
JOIN detalleventa as dv ON dv.idVenta = v.id
JOIN pagodetalle as pd ON dv.id = pd.idDetalleVenta AND pd.idVenta = v.id
JOIN producto as p ON p.id = dv.idProducto
JOIN persona as cl ON cl.id = v.idCliente
JOIN tipoproducto as tp ON tp.id = p.idMargenGanancia
WHERE pd.idCotizacion IS NULL AND pd.idOrden IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarDetalleVentaServicio`()
BEGIN
CREATE TEMPORARY TABLE tmp_servicio AS SELECT ms.descripcion as macro, cats.nombre as tipo, s.nombre, dv.descripcion, s.nombre as tipoProducto, cl.documento as docCliente, (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'%d/%m/%Y') as fecha, dv.created_at, v.tipoComprobante, CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0') ,'-', LPAD(v.numero,8,'0')) as documento, 
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion, 
v.tipoMoneda as moneda,
(SELECT COUNT(dvt.id) as cantidad FROM detalleventa dvt WHERE dvt.idVenta = v.id) as cant_items,
NULL as det_compra, NULL as det_guia FROM venta as v JOIN detalleventa as dv ON dv.idVenta = v.id JOIN pagodetalle as pd ON dv.id = pd.idDetalleVenta AND pd.idVenta = v.id JOIN servicio as s ON s.id = dv.idServicio JOIN categoriaservicio as cats ON cats.id = s.idCategoriaServicio JOIN persona as cl ON cl.id = v.idCliente LEFT JOIN macroservicio as ms ON ms.id = s.idMacroServicio
WHERE pd.idCotizacion IS NULL AND pd.idOrden IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarNotaPLE`()
BEGIN
CREATE TEMPORARY TABLE tmp_nota_ple AS SELECT CONCAT(DATE_FORMAT(an.fecha,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(an.fecha,'%m%y'),'V') as campo2, 
DATE_FORMAT(an.fecha,'%d/%m/%Y') as campo4, '07' as campo6, 
CONCAT(v.tipoComprobante,an.tipoNota, LPAD(an.serie,2,'0')) as campo7, an.numero as campo8,
(CASE WHEN cl.razonSocial IS NULL THEN '6' ELSE '1' END) as campo10, cl.documento as campo11, 
(CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as campo12, 
((CASE v.igv_sunat WHEN 10 THEN an.total/1.18 ELSE 0 END) * v.tipoCambio) as campo14, 
(an.igv * v.tipoCambio) as campo16,
((CASE v.igv_sunat WHEN 20 THEN an.total ELSE 0 END) * v.tipoCambio * -1) as campo18, 
((CASE v.igv_sunat WHEN 30 THEN an.total ELSE 0 END) * v.tipoCambio * -1) as campo19, 
(an.total * v.tipoCambio * -1) as campo25,
v.tipoMoneda as campo26,
v.tipoCambio as campo27,
DATE_FORMAT(v.fecha,'%d/%m/%Y') as campo28,
(CASE v.tipoComprobante WHEN 'B' THEN '03' ELSE '01' END) as campo29,
CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0')) as campo30,
v.numero as campo31,
an.situacion,
an.created_at
FROM anulacionnotas as an JOIN venta as v ON v.id = an.idVenta
JOIN persona as cl ON cl.id = v.idCliente;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarNotasCompra`()
BEGIN
    CREATE TEMPORARY TABLE tmp_compras_nota AS SELECT p.nombre as tipo, p.nombre, dtan.descripcion, p.tipoProducto, cl.documento as docProveedor, 
        (CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as proveedor, 
        (dtan.cantidad * -1) as cantidad, (dtan.precio * c.tipoCambio) as precio, (dtan.subtotal * c.tipoCambio * -1) as subtotal, 
        (an.igv * c.tipoCambio) as igv, (an.total * c.tipoCambio * -1) as total, DATE_FORMAT(an.fecha,'%d/%m/%Y') as fecha, 
        dtan.created_at, 
        an.tipoNota as tipoDocumento, 
        an.documentoCompra as documento, 
        an.situacion, c.tipoMoneda as moneda,
        (SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas dvt WHERE dvt.idAnulacion = an.id) as cant_items
        FROM anulacionnotas as an
        JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion = an.id
        JOIN compra as c ON c.id = an.idCompra
        JOIN detallecompra as dc ON dc.idCompra = c.id
        JOIN producto as p ON p.id = dc.idProducto
        JOIN persona as cl ON cl.id = c.idProveedor
        WHERE dtan.idProducto = dc.idProducto;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `listarVentaPLE`()
BEGIN
CREATE TEMPORARY TABLE tmp_venta_ple AS SELECT CONCAT(DATE_FORMAT(v.fecha,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(fecha,'%m%y'),'V') as campo2, 
DATE_FORMAT(v.fecha,'%d/%m/%Y') as campo4, (CASE v.tipoComprobante WHEN 'B' THEN '03' ELSE '01' END) as campo6, 
CONCAT(v.tipoComprobante, LPAD(v.serie,3,'0')) as campo7, v.numero as campo8,
(CASE WHEN cl.razonSocial IS NULL THEN '6' ELSE '1' END) as campo10, cl.documento as campo11, 
(CASE WHEN cl.razonSocial IS NULL THEN CONCAT(cl.apellidos,' ', cl.nombres) ELSE cl.razonSocial END) as campo12, 
((CASE v.igv_sunat WHEN 10 THEN v.total/1.18 ELSE 0 END) * v.tipoCambio) as campo14, 
(v.igv * v.tipoCambio) as campo16,
((CASE v.igv_sunat WHEN 20 THEN v.total ELSE 0 END) * v.tipoCambio) as campo18, 
((CASE v.igv_sunat WHEN 30 THEN v.total ELSE 0 END) * v.tipoCambio) as campo19, 
(v.total * v.tipoCambio) as campo25,
v.tipoMoneda as campo26,
v.tipoCambio as campo27,
NULL as campo28,
NULL as campo29,
NULL as campo30,
NULL as campo31,
(CASE WHEN v.situacion = 'A' THEN (SELECT (CASE WHEN COUNT(an.id) > 0 THEN 'A' ELSE 'V' END) as situacion FROM anulacion as an WHERE an.idVenta = v.id) ELSE v.situacion END) as situacion,
v.created_at
FROM venta as v
JOIN persona as cl ON cl.id = v.idCliente;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteComprasAutosPLE`()
BEGIN
    CREATE TEMPORARY TABLE tmp_reporte_compras_autos AS SELECT CONCAT(DATE_FORMAT(cp.created_at,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(cp.created_at,'%m%y'),'C') as campo2,
    DATE_FORMAT(cp.fecha,'%d/%m/%Y') as campo4, (CASE cp.diasCredito WHEN '0' THEN '' ELSE DATE_FORMAT(cp.fechaVencimiento,'%d/%m/%Y') END) as campo5, (CASE cp.tipoDocumento WHEN 'B' THEN '03' ELSE '01' END) as campo6, cp.documento, 
    (CASE WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' END) as campo11,
    prov.documento as campo12,
    (CASE WHEN prov.razonSocial IS NULL THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as campo13,
    (CASE cp.tipoMoneda WHEN 'D' THEN (cp.total * cp.tipoCambio) ELSE cp.total END) as campo20,
    (CASE cp.tipoMoneda WHEN 'D' THEN (cp.total * cp.tipoCambio) ELSE cp.total END) as campo24,
    (CASE cp.tipoMoneda WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25,
    cp.tipoCambio as campo26,
    NULL as campo27,
    NULL as campo28,
    NULL as ref,
    (CASE WHEN DATE_FORMAT(cp.fecha,'%Y-%m') != DATE_FORMAT(cp.created_at,'%Y-%m') THEN '6' ELSE '1' END) as campo35,
    cp.created_at 
    FROM compraauto as cp 
    JOIN persona as prov ON prov.id = cp.idProveedor
    WHERE cp.deleted_at IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteComprasPLE`()
BEGIN
    CREATE TEMPORARY TABLE tmp_reporte_compras AS SELECT CONCAT(DATE_FORMAT(cp.created_at,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(cp.created_at,'%m%y'),'C') as campo2,
    DATE_FORMAT(cp.fecha,'%d/%m/%Y') as campo4, (CASE cp.diasCredito WHEN '0' THEN '' ELSE DATE_FORMAT(cp.fechaVencimiento,'%d/%m/%Y') END) as campo5, (CASE cp.tipoDocumento WHEN 'B' THEN '03' ELSE '01' END) as campo6, cp.documento, 
    (CASE WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' END) as campo11,
    prov.documento as campo12,
    (CASE WHEN prov.razonSocial IS NULL THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as campo13,
    (CASE cp.tipoMoneda WHEN 'D' THEN (cp.total * cp.tipoCambio) ELSE cp.total END) as campo20,
    (CASE cp.tipoMoneda WHEN 'D' THEN (cp.total * cp.tipoCambio) ELSE cp.total END) as campo24,
    (CASE cp.tipoMoneda WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25,
    cp.tipoCambio as campo26,
    NULL as campo27,
    NULL as campo28,
    NULL as ref,
    (CASE WHEN DATE_FORMAT(cp.fecha,'%Y-%m') != DATE_FORMAT(cp.created_at,'%Y-%m') THEN '6' ELSE '1' END) as campo35,
    cp.created_at 
    FROM compra as cp 
    JOIN persona as prov ON prov.id = cp.idProveedor
    WHERE cp.deleted_at IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteComprobantePLE`(IN `fechaI` DATE, IN `fechaF` DATE)
BEGIN
        CALL listarNotaPLE();
   	    CALL listarVentaPLE();

		CREATE TEMPORARY TABLE tmp_reporte_ple AS 
        (SELECT * FROM tmp_nota_ple) UNION ALL
        (SELECT * FROM tmp_venta_ple);
     
        SELECT * FROM tmp_reporte_ple WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN fechaI AND fechaF ORDER BY created_at ASC;
	END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteComprobantesComprasPLE`(IN `fechaI` DATE, IN `fechaF` DATE)
BEGIN
        CALL reporteComprasPLE();
        CALL reporteComprasAutosPLE();
        CALL reporteNotasComprasPLE();
        CALL reporteNotasComprasAutosPLE();

		CREATE TEMPORARY TABLE tmp_reporte_compras_ple AS 
        (SELECT * FROM tmp_reporte_compras) UNION ALL
        (SELECT * FROM tmp_reporte_compras_autos) UNION ALL
        (SELECT * FROM tmp_reporte_notas_compras) UNION ALL
        (SELECT * FROM tmp_reporte_notas_compras_autos);
     
        SELECT * FROM tmp_reporte_compras_ple WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN fechaI AND fechaF ORDER BY created_at ASC;
	END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteDetalladoCompras`(IN fechaI DATE, IN fechaF DATE)
BEGIN
    CALL listarCompras();
    CALL listarComprasAuto();
    CALL listarNotasCompra();
  
    CREATE TEMPORARY TABLE tmp_reporte_detallado_compras AS 
        (SELECT * FROM tmp_compras) UNION ALL
        (SELECT * FROM tmp_compras_auto) UNION ALL
     	(SELECT * FROM tmp_compras_nota);
        SELECT * FROM tmp_reporte_detallado_compras WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN fechaI AND fechaF ORDER BY created_at ASC;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteDetalladoVentas`(IN `fechaI` DATE, IN `fechaF` DATE)
BEGIN
        CALL listarDetalleServicioCotizacion();
   	    CALL listarDetalleServicioOrden();
        CALL listarDetalleVentaServicio();
        CALL listarDetalleVentaCotizacion();
        CALL listarDetalleVentaOrden();
        CALL listarDetalleVentaOrdenNoHomologado();
        CALL listarDetalleVentaProducto();
        CALL listarDetalleVentaAuto();
        CALL listarDetalleProductoNota();
        CALL listarDetalleServicioNota();
        CALL listarDetalleAutoNota();
        CALL listarDetalleOtrosNota();

        
        CREATE TEMPORARY TABLE tmp_reporte_detallado_ventas AS 
        (SELECT * FROM tmp_servicio_cot) UNION ALL
        (SELECT * FROM tmp_servicio_od) UNION ALL
        (SELECT * FROM tmp_servicio) UNION ALL
        (SELECT * FROM tmp_producto_cot) UNION ALL
        (SELECT * FROM tmp_producto_od) UNION ALL
        (SELECT * FROM tmp_producto_od_nh) UNION ALL
        (SELECT * FROM tmp_producto) UNION ALL
        (SELECT * FROM tmp_auto) UNION ALL
        (SELECT * FROM tmp_producto_nota) UNION ALL
        (SELECT * FROM tmp_servicio_nota) UNION ALL
        (SELECT * FROM tmp_auto_nota) UNION ALL
        (SELECT * FROM tmp_otros_nota);
        SELECT * FROM tmp_reporte_detallado_ventas WHERE DATE_FORMAT(created_at,'%Y-%m-%d') BETWEEN fechaI AND fechaF ORDER BY created_at ASC;
	END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteHistorialProducto`(IN `almacenId` BIGINT(20), IN `productoId` BIGINT(20))
    NO SQL
BEGIN
	CALL getEgresosComprobanteProducto(almacenId, productoId);
	CALL getEgresosGuiaProducto(almacenId, productoId);
	CALL getIngresosEgresosNotaProducto(almacenId, productoId);
	CALL getIngresosProducto(almacenId, productoId);

 CREATE TEMPORARY TABLE tmp_reporte_historial AS
  (SELECT * FROM tmp_egresos_prod) UNION ALL
  (SELECT * FROM tmp_egresos_guia) UNION ALL
  (SELECT * FROM tmp_ingresos_eg_nota) UNION ALL
  (SELECT * FROM tmp_ingresos_prod);
  
  SELECT * FROM tmp_reporte_historial ORDER BY created_at ASC;
	
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteInventarioValorizado`(IN `fechaF` DATE)
BEGIN
CREATE TEMPORARY TABLE tmp_reporte_inventario AS SELECT lc.direccion as almacen, producto.id, 
CONCAT((CASE WHEN producto.nombre IS NULL THEN '' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca: ', ml.nombre) ELSE (CASE WHEN producto.idMarca IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL THEN ', ' ELSE '' END), 'Marca: ', ma.nombre) ELSE '' END) END), (CASE WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT((CASE WHEN ml.nombre IS NOT NULL OR producto.nombre IS NOT NULL OR ma.nombre IS NOT NULL THEN ', ' ELSE '' END),'Marca de Auto: ', mt.nombre) ELSE '' END)) as nombre, (CASE WHEN tipoProducto = 'B' THEN CONCAT('Marca: ', mb.nombre, ', Modelo: ',modb.nombre,', Placa:', producto.placaBateria, ' - Tipo: ', (CASE WHEN producto.tipoBateria = 'L' THEN 'Líquida' ELSE 'Seca' END)) ELSE NULL END) as nombre2,
(CASE WHEN producto.tipollanta IS NULL THEN '-' ELSE producto.tipollanta END) as tipollanta,
(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ml.nombre ELSE (CASE WHEN producto.idMarca IS NOT NULL AND producto.idMarcaBateria IS NULL THEN ma.nombre ELSE (CASE WHEN producto.idMarcaBateria IS NOT NULL THEN mb.nombre ELSE '-' END) END) END) as marca,
(CASE WHEN producto.modelo IS NULL AND producto.idModeloLlanta IS NOT NULL AND producto.idModeloBateria IS NULL THEN mol.nombre ELSE (CASE WHEN producto.modelo IS NOT NULL AND producto.idModeloBateria IS NULL THEN producto.modelo ELSE (CASE WHEN producto.idModeloBateria IS NOT NULL THEN modb.nombre ELSE '-' END) END) END) as modelo,
(CASE WHEN producto.idSistema IS NOT NULL THEN sa.nombre ELSE '-' END) as sistema,
(CASE WHEN producto.medida IS NULL THEN '-' ELSE producto.medida END) as medida,
(CASE producto.tipoProducto 
WHEN 'A'  THEN 'Accesorio/Repuesto' 
WHEN 'LL' THEN 'Neumáticos' 
WHEN 'I'  THEN 'Insumos' 
WHEN 'B'  THEN 'Baterías' 
ELSE 'MUELLES' END) as tipoProducto,
(sp.totalCompras - sp.totalVentas - sp.totalIncidencias) as stock, IFNULL(spd.stock,0) AS stock_detalle,
(SELECT GROUP_CONCAT(c.tipoMoneda,'-', dc.preciocompra) FROM compra as c JOIN detallecompra as dc ON c.id = dc.idCompra 
              JOIN lote as l ON l.idCompra = c.id 
 			  JOIN stockproductodetalle as spd2 ON spd2.idLote = l.id
 			  WHERE dc.idProducto = l.idProducto AND sp.idProducto = l.idProducto AND spd2.id = spd.id) as det_compra,
(SELECT GROUP_CONCAT(g.tipoMoneda,'-', dg.preciocompra) FROM guia as g JOIN detalleguia as dg ON g.id = dg.idGuia 
              JOIN lote as l ON l.idGuiaCompra = g.id 
 			  JOIN stockproductodetalle as spd2 ON spd2.idLote = l.id
 			  WHERE dg.idProducto = l.idProducto AND sp.idProducto = l.idProducto AND spd2.id = spd.id) as det_guia
FROM stockproducto sp 
JOIN local as lc ON lc.id = sp.idAlmacen
JOIN producto ON producto.id = sp.idProducto
LEFT JOIN marcaaccesorio as ma ON ma.id = producto.idMarca
LEFT JOIN marcaauto as mt ON mt.id = producto.idMarcaAuto
LEFT JOIN marcallanta as ml ON ml.id = producto.idMarcaLlanta
LEFT JOIN marcabateria as mb ON mb.id = producto.idMarcaBateria
LEFT JOIN modelobateria as modb ON modb.id = producto.idModeloBateria
LEFT JOIN sistemaauto as sa ON sa.id = producto.idSistema
LEFT JOIN modelollanta as mol ON mol.id = producto.idModeloLlanta				
LEFT JOIN stockproductodetalle spd ON spd.idProducto = sp.idProducto  
WHERE spd.deleted_at IS NULL AND DATE_FORMAT(sp.created_at,'%Y-%m-%d') <= fechaF
ORDER BY sp.created_at ASC;

SELECT * FROM tmp_reporte_inventario;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteNotasComprasAutosPLE`()
BEGIN
    CREATE TEMPORARY TABLE tmp_reporte_notas_compras_autos AS SELECT CONCAT(DATE_FORMAT(an.created_at,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(an.created_at,'%m%y'),'C') as campo2,
    DATE_FORMAT(an.fecha,'%d/%m/%Y') as campo4, NULL as campo5, 
    (CASE an.tipoNota WHEN 'C' THEN '07' ELSE '08' END) as campo6, an.documentoCompra as documento, 
    (CASE WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' END) as campo11,
    prov.documento as campo12,
    (CASE WHEN prov.razonSocial IS NULL THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as campo13,
    (CASE cp.tipoMoneda WHEN 'D' THEN (an.total * cp.tipoCambio * -1) ELSE an.total END) as campo20,
    (CASE cp.tipoMoneda WHEN 'D' THEN (an.total * cp.tipoCambio * -1) ELSE an.total END) as campo24,
    (CASE cp.tipoMoneda WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25,
    cp.tipoCambio as campo26,
    DATE_FORMAT(cp.fecha,'%d/%m/%Y') as campo27,
	(CASE cp.tipoDocumento WHEN 'F' THEN '01' ELSE '03' END) as campo28,
	cp.documento as ref,
	(CASE WHEN DATE_FORMAT(an.fecha,'%Y-%m') != DATE_FORMAT(an.created_at,'%Y-%m') THEN '6' ELSE '1' END) as campo35,
    an.created_at 
    FROM anulacionnotas as an  
    JOIN compraauto as cp ON cp.id = an.idCompraAuto
    JOIN persona as prov ON prov.id = cp.idProveedor
    WHERE an.situacion = 'V' AND an.idVenta IS NULL;
END ;;
DELIMITER ;
DELIMITER ;;
CREATE PROCEDURE `reporteNotasComprasPLE`()
BEGIN
    CREATE TEMPORARY TABLE tmp_reporte_notas_compras AS SELECT CONCAT(DATE_FORMAT(an.created_at,'%Y%m'),'00') as campo1, CONCAT(DATE_FORMAT(an.created_at,'%m%y'),'C') as campo2,
    DATE_FORMAT(an.fecha,'%d/%m/%Y') as campo4, NULL as campo5, 
    (CASE an.tipoNota WHEN 'C' THEN '07' ELSE '08' END) as campo6, an.documentoCompra as documento, 
    (CASE WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' END) as campo11,
    prov.documento as campo12,
    (CASE WHEN prov.razonSocial IS NULL THEN CONCAT(prov.apellidos,' ', prov.nombres) ELSE prov.razonSocial END) as campo13,
    (CASE cp.tipoMoneda WHEN 'D' THEN (an.total * cp.tipoCambio * -1) ELSE an.total END) as campo20,
    (CASE cp.tipoMoneda WHEN 'D' THEN (an.total * cp.tipoCambio * -1) ELSE an.total END) as campo24,
    (CASE cp.tipoMoneda WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25,
    cp.tipoCambio as campo26,
    DATE_FORMAT(cp.fecha,'%d/%m/%Y') as campo27,
	(CASE cp.tipoDocumento WHEN 'F' THEN '01' ELSE '03' END) as campo28,
	cp.documento as ref,
	(CASE WHEN DATE_FORMAT(an.fecha,'%Y-%m') != DATE_FORMAT(an.created_at,'%Y-%m') THEN '6' ELSE '1' END) as campo35,
    an.created_at 
    FROM anulacionnotas as an  
    JOIN compra as cp ON cp.id = an.idCompra
    JOIN persona as prov ON prov.id = cp.idProveedor
    WHERE an.situacion = 'V' AND an.idVenta IS NULL;
END ;;
DELIMITER ;