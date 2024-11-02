-- COMPRAS

DELIMITER $$

CREATE PROCEDURE LISTARCOMPRAS() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_compras AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dc.descripcion,
	    p.tipoProducto,
	    cl.documento as docProveedor, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres)
		                ELSE cl.razonSocial
		            END
		        ) as proveedor,
		        dc.cantidad, (dc.preciocompra * c.tipoCambio) as precio, (dc.subtotal * c.tipoCambio) as subtotal,
		        (c.igv * c.tipoCambio) as igv, (c.total * c.tipoCambio) as total,
		        DATE_FORMAT(c.fecha, '
	            )
	        END
	    ) as proveedor,
	    dc.cantidad, (dc.preciocompra * c.tipoCambio) as precio, (dc.subtotal * c.tipoCambio) as subtotal, (c.igv * c.tipoCambio) as igv, (c.total * c.tipoCambio) as total,
	    DATE_FORMAT(c.fecha, '%d/%m/%Y') as fecha,
	    dc.created_at,
	    c.tipoDocumento,
	    c.documento, (
	        CASE
	            WHEN c.deleted_at IS NULL THEN 'V'
	            ELSE 'A'
	        END
	    ) as situacion,
	    c.tipoMoneda as moneda, (
	        SELECT COUNT(dct.id) as cantidad
	        FROM detallecompra dct
	        WHERE
	            dct.idCompra = c.id
	    ) as cant_items
	FROM compra as c
	    JOIN detallecompra as dc ON dc.idCompra = c.id
	    JOIN producto as p ON p.id = dc.idProducto
	    JOIN persona as cl ON cl.id = c.idProveedor;
END$ 

$ 

DELIMITER ;

-- COMPRAS AUTO

DELIMITER $$

CREATE PROCEDURE LISTARCOMPRASAUTO() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_compras_auto AS
	SELECT
	    ma.nombre as tipo,
	    CONCAT(
	        ma.nombre,
	        ', a.version,',
	        a.transmision,
	        ', a.descripcion) as nombre,
		    dc.descripcion,
		    CONCAT(ma.nombre,',
	        a.version,
	        ', a.transmision,',
	        a.descripcion
	    ) as tipoProducto,
	    cl.documento as docProveedor, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres)
		            ELSE cl.razonSocial
		        END
		    ) as proveedor,
		    dc.cantidad, (dc.preciocompra * c.tipoCambio) as precio, (dc.subtotal * c.tipoCambio) as subtotal,
		    (c.igv * c.tipoCambio) as igv, (c.total * c.tipoCambi
	END) as proveedor, dc.cantidad,(dc.preciocompra * c
	.tipoCambio) as precio,(dc.subtotal * c.tipoCambio
	) as subtotal,(c.igv * c.tipoCambio) as igv,(c.total 
	* c.tipoCambio) as total, DATE_FORMAT(c.fecha, ' % d / % m / % Y '
	) as fecha, dc.created_at, c.tipoDocumento, c.documento
	,(CASE WHEN c.deleted_at IS NULL THEN ' V ' ELSE ' A ' 
	END) as situacion, c.tipoMoneda as moneda,(SELECT 
	COUNT(dct.id) as cantidad FROM detallecompraauto dct 
	WHERE dct.idCompra =c.id) as cant_items FROM compraauto 
	as c JOIN detallecompraauto as dc ON dc.idCompra =
	c.id JOIN auto as a ON a.id =dc.idAuto JOIN marcaauto 
	as ma ON ma.id =a.marcaId JOIN persona as cl ON cl
	.id =c.idProveedor; 
	
	END$$;


DELIMITER ;

-- NOTA DE COMPRAS DE PRODUCTOS

DELIMITER $$

CREATE PROCEDURE LISTARNOTASCOMPRA() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_compras_nota AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dtan.descripcion,
	    p.tipoProducto,
	    cl.documento as docProveedor, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as proveedor, 
		        (dtan.cantidad * -1) as cantidad, (dtan.precio * c.tipoCambio) as precio, (dtan.subtotal * c.tipoCambio * -1) as subtotal, 
		        (an.igv * c.tipoCambio) as igv, (an.total * c.tipoCambi
	END) as proveedor,(dtan.cantidad * -1) as cantidad,
	(dtan.precio * c.tipoCambio) as precio,(dtan.subtotal 
	* c.tipoCambio * -1) as subtotal,(an.igv * c.tipoCambio
	) as igv,(an.total * c.tipoCambio * -1) as total, 
	DATE_FORMAT(an.fecha, ' % d / % m / % Y ') as fecha, dtan.created_at
	, an.tipoNota as tipoDocumento, an.documentoCompra 
	as documento, an.situacion, c.tipoMoneda as moneda
	,(SELECT COUNT(dvt.id) as cantidad FROM detalleanulacionnotas 
	dvt WHERE dvt.idAnulacion =an.id) as cant_items FROM 
	anulacionnotas as an JOIN detalleanulacionnotas as 
	dtan ON dtan.idAnulacion =an.id JOIN compra as c ON 
	c.id =an.idCompra JOIN detallecompra as dc ON dc.idCompra 
	=c.id JOIN producto as p ON p.id =dc.idProducto JOIN 
	persona as cl ON cl.id =c.idProveedor WHERE dtan.idProducto 
	=dc.idProducto; 
	
	END$$;


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE REPORTEDETALLADOCOMPRAS(IN FECHAI 
DATE, IN FECHAF DATE) BEGIN 
	CALL listarCompras();
	
	CALL listarComprasAuto();
	
	CALL listarNotasCompra();
	
	CREATE TEMPORARY
	TABLE
	    tmp_reporte_detallado_compras AS (
	        SELECT *
	        FROM tmp_compras
	    )
	UNION ALL (
	    SELECT *
	    FROM tmp_compras_auto
	)
	UNION ALL (
	    SELECT *
	    FROM tmp_compras_nota
	);
	
	SELECT *
	FROM
	    tmp_reporte_detallado_compras
	WHERE
	    DATE_FORMAT(
	        created_at,
	        ') BETWEEN fechaI AND fechaF ORDER BY created_at ASC;
	END$$;


DELIMITER ;

-- ==========================================================================================================================

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLEPRODUCTONOTA`() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_producto_nota AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dtan.descripcion,
	    p.tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v.tipoCambio * -1) as subtotal, v.igv_sunat, (an.igv * v.tipoCambio) as igv,(an.total * v.tipoCambio * -1) as total, DATE_FORMAT(a
	END) as cliente,(dtan.cantidad * -1) as cantidad,(dtan
	.precio * v.tipoCambio) as precio,(dtan.subtotal * 
	v.tipoCambio * -1) as subtotal, v.igv_sunat,(an.igv 
	* v.tipoCambio) as igv,(an.total * v.tipoCambio * 
	-1) as total, DATE_FORMAT(an.fecha, ' % d / % m / % Y ') as 
	fecha, dtan.created_at, an.tipoNota as tipoComprobante
	, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie
	, 2, ' 0 '), ' - ', LPAD(an.numero, 8, ' 0 ')) as documento
	, an.situacion, v.tipoMoneda as moneda,(SELECT COUNT
	(dvt.id) as cantidad FROM detalleanulacionnotas dvt 
	WHERE dvt.idAnulacion =an.id) as cant_items,(SELECT 
	GROUP_CONCAT(c.tipoMoneda, ' - ',(dc.preciocompra*c.
	tipoCambio)) FROM compra as c JOIN detallecompra as 
	dc ON c.id =dc.idCompra JOIN lote as l ON l.idCompra 
	=c.id WHERE dc.idProducto =l.idProducto AND l.id =
	dv.idLote AND l.idProducto =dv.idProducto) as det_compra
	,(SELECT GROUP_CONCAT(g.tipoMoneda, ' - ',(dg.preciocompra*g
	.tipoCambio)) FROM guia as g JOIN detalleguia as dg 
	ON g.id =dg.idGuia JOIN lote as l ON l.idGuiaCompra 
	=g.id WHERE dg.idProducto =l.idProducto AND l.id =
	dv.idLote AND l.idProducto =dv.idProducto) as det_guia 
	FROM anulacionnotas as an JOIN detalleanulacionnotas 
	as dtan ON dtan.idAnulacion =an.id JOIN venta as v 
	ON v.id =an.idVenta JOIN detalleventa as dv ON dv.
	idVenta =v.id JOIN producto as p ON p.id =dv.idProducto 
	JOIN persona as cl ON cl.id =v.idCliente WHERE dtan
	.idProducto =dv.idProducto; 
	
	END$$


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLESERVICIONOTA`() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_servicio_nota AS
	SELECT
	    cats.nombre as tipo,
	    s.nombre,
	    dtan.descripcion,
	    s.nombre as tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as cliente, (dtan.cantidad * -1) as cantidad, (dtan.precio * v.tipoCambio) as precio, (dtan.subtotal * v
	END) as cliente,(dtan.cantidad * -1) as cantidad,(dtan
	.precio * v.tipoCambio) as precio,(dtan.subtotal * 
	v.tipoCambio * -1) as subtotal, v.igv_sunat,(an.igv 
	* v.tipoCambio) as igv,(an.total * v.tipoCambio * 
	-1) as total, DATE_FORMAT(an.fecha, ' % d / % m / % Y ') as 
	fecha, dtan.created_at, an.tipoNota as tipoComprobante
	, CONCAT(v.tipoComprobante, an.tipoNota, LPAD(an.serie
	, 2, ' 0 '), ' - ', LPAD(an.numero, 8, ' 0 ')) as documento
	, an.situacion, v.tipoMoneda as moneda,(SELECT COUNT
	(dvt.id) as cantidad FROM detalleanulacionnotas dvt 
	WHERE dvt.idAnulacion =an.id) as cant_items, NULL 
	as det_compra, NULL as det_guia FROM anulacionnotas 
	as an JOIN detalleanulacionnotas as dtan ON dtan.idAnulacion 
	=an.id JOIN venta as v ON v.id =an.idVenta JOIN detalleventa 
	as dv ON dv.idVenta =v.id JOIN servicio as s ON s.
	id =dv.idServicio JOIN categoriaservicio as cats ON 
	cats.id =s.idCategoriaServicio JOIN persona as cl 
	ON cl.id =v.idCliente WHERE dtan.idServicio =dv.idServicio; 
	
	END$$


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLEVENTAAUTO`() NO SQL 
COMMENT 'LISTA VENTAS DIRECTAS DE AUTOS' CREATE TEMPORARY 
TABLE TMP_AUTO AS 
	SELECT
	    ma.nombre as tipo,
	    CONCAT(
	        ma.nombre,
	        ', p.version,',
	        p.transmision,
	        ', p.descripcion) as nombre,
		  dv.descripcion, CONCAT(ma.nombre,',
	        p.version,
	        ', p.transmision,',
	        p.descripcion
	    ) as tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END ) as cliente,
		  dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
		  v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha, '
	            ) as fecha,
	            dv.created_at,
	            v.tipoComprobante,
	            CON
	        END
	    ) as cliente,
	    dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
	    v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total,
	    DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha,
	    dv.created_at,
	    v.tipoComprobante,
	    CONCAT (
	        v.tipoComprobante,
	        LPAD(v.serie, 3, '0'),
	        '-',
	        LPAD (v.numero, 8, '0')
	    ) as documento, (
	        CASE
	            WHEN v.situacion = 'A' THEN(
	                SELECT
	(
	                        CASE
	                            WHEN COUNT(an.id) > 0 THEN 'A'
	                            ELSE 'V'
	                        END
	                    ) as situacion
	                FROM anulacion as an
	                WHERE
	                    an.idVenta = v.id
	            )
	            ELSE v.situacion
	        END
	    ) as situacion,
	    v.tipoMoneda as moneda, (
	        SELECT COUNT(dvt.id) as cantidad
	        FROM detalleventa dvt
	        WHERE
	            dvt.idVenta = v.id
	    ) as cant_items, (
	        SELECT
	            GROUP_CONCAT(
	                c.tipoMoneda,
	                '-', (dc.preciocompra * c.tipoCambio)
	            )
	        FROM compraauto as c
	            JOIN detallecompraauto as dc ON c.id = dc.idCompra
	            JOIN loteauto as l ON l.idCompra = c.id
	        WHERE
	            dc.idAuto = l.idAuto
	            AND l.id = dv.idLoteAuto
	            AND l.idAuto = dv.idAuto
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT(
	                g.tipoMoneda,
	                '-', (dg.preciocompra * g.tipoCambio)
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN loteauto as l ON l.idGuiaCompra = g.id
	        WHERE
	            dg.idAuto = l.idAuto
	            AND l.id = dv.idLoteAuto
	            AND l.idAuto = dv.idAuto
	    ) as det_guia
	FROM venta as v
	    JOIN detalleventa as dv ON dv.idVenta = v.id
	    JOIN pagodetalle as pd ON dv.id = pd.idDetalleVenta AND pd.idVenta = v.id
	    JOIN auto as p ON p.id = dv.idAuto
	    JOIN marcaauto as ma ON ma.id = p.marcaId
	    JOIN persona as cl ON cl.id = v.idCliente
	WHERE
	    pd.idCotizacion IS NULL
	    AND pd.idOrden IS NULL$$


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLEVENTACOTIZACION`() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_producto_cot AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dv.descripcion,
	    p.tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal* v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'
	            ) as fecha,
	            dv.created_at,
	            v.tipoComprobante,
	            CONCAT(v.tipoComprobante, LPA
	        END) as cliente,
	        dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
	        v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total,
	        DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha,
	        dv.created_at,
	        v.tipoComprobante,
	        CONCAT (
	            v.tipoComprobante,
	            LPAD(v.serie, 3, '0'),
	            '-',
	            LPAD (v.numero, 8, '0')
	        ) as documento, (
	            CASE
	                WHEN v.situacion = 'A' THEN(
	                    SELECT
	(
	                            CASE
	                                WHEN COUNT(an.id) > 0 THEN 'A'
	                                ELSE 'V'
	                            END
	                        ) as situacion
	                    FROM anulacion as an
	                    WHERE
	                        an.idVenta = v.id
	                )
	                ELSE v.situacion
	            END
	        ) as situacion,
	        v.tipoMoneda as moneda, (
	            SELECT COUNT(dvt.id) as cantidad
	            FROM detalleventa dvt
	            WHERE
	                dvt.idVenta = v.id
	        ) as cant_items, (
	            SELECT
	                GROUP_CONCAT(
	                    c.tipoMoneda,
	                    '-', (dc.preciocompra * c.tipoCambio)
	                )
	            FROM compra as c
	                JOIN detallecompra as dc ON c.id = dc.idCompra
	                JOIN lote as l ON l.idCompra = c.id
	            WHERE
	                dc.idProducto = l.idProducto
	                AND l.id = dcot.idLote
	                AND l.idProducto = dcot.idProducto
	        ) as det_compra, (
	            SELECT
	                GROUP_CONCAT (
	                    g.tipoMoneda,
	                    '-', (dg.preciocompra * g.tipoCambio)
	                )
	            FROM guia as g
	                JOIN detalleguia as dg ON g.id = dg.idGuia
	                JOIN lote as l ON l.idGuiaCompra = g.id
	            WHERE
	                dg.idProducto = l.idProducto
	                AND l.id = dcot.idLote
	                AND l.idProducto = dcot.idProducto
	        ) as det_guia
	        FROM venta as v
	            JOIN detalleventa as dv ON dv.idVenta = v.id
	            JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id
	            JOIN producto as p ON p.id = dv.idProducto
	            JOIN persona as cl ON cl.id = v.idCliente
	            JOIN detallecotizacion as dcot ON dcot.idCotizacion = pd.idCotizacion
	            JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
	        WHERE
	            pd.idDetalleVenta = dv.id
	            AND pd.idCotizacion IS NOT NULL
	            AND dcot.idProducto = dv.idProducto;
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLEVENTAORDEN`() NO SQL 
COMMENT 'LISTA VENTAS POR ORDEN DE TRABAJO DE PRODUCTOS' 
BEGIN 
	CREATE TEMPORARY
	TABLE tmp_producto_od AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dv.descripcion,
	    p.tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv , (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'
	            ) as fecha,
	            dv.created_at,
	            v.tipoComprobante,
	            CONCAT(
	                v.tipoComprobante,
	                LPAD(v.serie,
	            END) as cliente,
	            dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
	            v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total,
	            DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha,
	            dv.created_at,
	            v.tipoComprobante,
	            CONCAT (
	                v.tipoComprobante,
	                LPAD(v.serie, 3, '0'),
	                '-',
	                LPAD (v.numero, 8, '0')
	            ) as documento, (
	                CASE
	                    WHEN v.situacion = 'A' THEN(
	                        SELECT
	(
	                                CASE
	                                    WHEN COUNT(an.id) > 0 THEN 'A'
	                                    ELSE 'V'
	                                END
	                            ) as situacion
	                        FROM anulacion as an
	                        WHERE
	                            an.idVenta = v.id
	                    )
	                    ELSE v.situacion
	                END
	            ) as situacion,
	            v.tipoMoneda as moneda, (
	                SELECT COUNT(dvt.id) as cantidad
	                FROM detalleventa dvt
	                WHERE
	                    dvt.idVenta = v.id
	            ) as cant_items, (
	                SELECT
	                    GROUP_CONCAT(
	                        c.tipoMoneda,
	                        '-', (dc.preciocompra * c.tipoCambio)
	                    )
	                FROM compra as c
	                    JOIN detallecompra as dc ON c.id = dc.idCompra
	                    JOIN lote as l ON l.idCompra = c.id
	                WHERE
	                    dc.idProducto = l.idProducto
	                    AND l.id = dcot.idLote
	                    AND l.idProducto = dcot.idProducto
	            ) as det_compra, (
	                SELECT
	                    GROUP_CONCAT (
	                        g.tipoMoneda,
	                        '-', (dg.preciocompra * g.tipoCambio)
	                    )
	                FROM guia as g
	                    JOIN detalleguia as dg ON g.id = dg.idGuia
	                    JOIN lote as l ON l.idGuiaCompra = g.id
	                WHERE
	                    dg.idProducto = l.idProducto
	                    AND l.id = dcot.idLote
	                    AND l.idProducto = dcot.idProducto
	            ) as det_guia
	            FROM venta as v
	                JOIN detalleventa as dv ON dv.idVenta = v.id
	                JOIN pagodetalle as pd ON pd.idVenta = v.id AND pd.idDetalleVenta = dv.id
	                JOIN producto as p ON p.id = dv.idProducto
	                JOIN persona as cl ON cl.id = v.idCliente
	                JOIN detalleordentrabajo as dot ON dot.idOrdenTrabajo = pd.idOrden
	                JOIN detallecotizacion as dcot ON dcot.idCotizacion = dot.idCotizacion
	                JOIN detallehomologacion as dhom ON dhom.idDetalleVenta = dv.id AND dhom.idDetalleCotizacion = dcot.id
	            WHERE
	                pd.idDetalleVenta = dv.id
	                AND pd.idCotizacion IS NULL
	                AND dcot.idProducto = dv.idProducto
	                AND dot.idOrdenTrabajo = pd.idOrden;
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `LISTARDETALLEVENTAPRODUCTO`() NO 
SQL COMMENT 'LISTA VENTAS DIRECTAS DE PRODUCTOS' BEGIN 
	CREATE TEMPORARY
	TABLE tmp_producto AS
	SELECT
	    p.nombre as tipo,
	    p.nombre,
	    dv.descripcion,
	    p.tipoProducto,
	    cl.documento as docCliente, (
	        CASE
	            WHEN cl.razonSocial IS NULL THEN CONCAT(
	                cl.apellidos,
	                ', cl.nombres) ELSE cl.razonSocial END) as cliente, dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal, v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total, DATE_FORMAT(v.fecha,'
	            ) as fecha,
	            dv.created_at,
	            v.tipoComprobante,
	            C
	        END
	    ) as cliente,
	    dv.cantidad, (dv.precio * v.tipoCambio) as precio, (dv.subtotal * v.tipoCambio) as subtotal,
	    v.igv_sunat, (v.igv * v.tipoCambio) as igv, (v.total * v.tipoCambio) as total,
	    DATE_FORMAT(v.fecha, '%d/%m/%Y') as fecha,
	    dv.created_at,
	    v.tipoComprobante,
	    CONCAT (
	        v.tipoComprobante,
	        LPAD(v.serie, 3, '0'),
	        '-',
	        LPAD (v.numero, 8, '0')
	    ) as documento, (
	        CASE
	            WHEN v.situacion = 'A' THEN(
	                SELECT
	(
	                        CASE
	                            WHEN COUNT(an.id) > 0 THEN 'A'
	                            ELSE 'V'
	                        END
	                    ) as situacion
	                FROM anulacion as an
	                WHERE
	                    an.idVenta = v.id
	            )
	            ELSE v.situacion
	        END
	    ) as situacion,
	    v.tipoMoneda as moneda, (
	        SELECT COUNT(dvt.id) as cantidad
	        FROM detalleventa dvt
	        WHERE
	            dvt.idVenta = v.id
	    ) as cant_items, (
	        SELECT
	            GROUP_CONCAT(
	                c.tipoMoneda,
	                '-', (dc.preciocompra * c.tipoCambio)
	            )
	        FROM compra as c
	            JOIN detallecompra as dc ON c.id = dc.idCompra
	            JOIN lote as l ON l.idCompra = c.id
	        WHERE
	            dc.idProducto = l.idProducto
	            AND l.id = dv.idLote
	            AND l.idProducto = dv.idProducto
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT (
	                g.tipoMoneda,
	                '-', (dg.preciocompra * g.tipoCambio)
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN lote as l ON l.idGuiaCompra = g.id
	        WHERE
	            dg.idProducto = l.idProducto
	            AND l.id = dv.idLote
	            AND l.idProducto = dv.idProducto
	    ) as det_guia
	FROM venta as v
	    JOIN detalleventa as dv ON dv.idVenta = v.id
	    JOIN pagodetalle as pd ON dv.id = pd.idDetalleVenta AND pd.idVenta = v.id
	    JOIN producto as p ON p.id = dv.idProducto
	    JOIN persona as cl ON cl.id = v.idCliente
	WHERE
	    pd.idCotizacion IS NULL
	    AND pd.idOrden IS NULL;
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `REPORTECOMPRASAUTOSPLE`() BEGIN 
	CREATE TEMPORARY
	TABLE
	    tmp_reporte_compras_autos AS
	SELECT
	    CONCAT(
	        DATE_FORMAT(cp.created_at, '),') as campo1,
	        CONCAT(
	            DATE_FORMAT(cp.created_at, '),') as campo2,
	            DATE_FORMAT(
	                cp.fecha,
	                ') as campo4, (CASE cp.diasCredito WHEN ' THEN ' ELSE DATE_FORMAT(cp.fechaVencimiento,'
	            )
	        END
	    ) as campo5, (
	        CASE cp.tipoDocumento
	            WHEN ' THEN '
	            ELSE ' END) as campo6, cp.documento, 
		    (CASE WHEN prov.razonSocial IS NOT NULL THEN
	END) as campo5,(CASE cp.tipoDocumento WHEN ' B ' THEN 
	' 03 ' ELSE ' 01 ' END) as campo6, cp.documento,(CASE 
	WHEN prov.razonSocial IS NOT NULL THEN ' 6 ' ELSE ' 1 ' 
	END) as campo11, prov.documento as campo12,(CASE WHEN 
	prov.razonSocial IS NULL THEN CONCAT(prov.apellidos
	, ' ', prov.nombres) ELSE prov.razonSocial END) as 
	campo13,(CASE cp.tipoMoneda WHEN ' D ' THEN(cp.total 
	* cp.tipoCambio) ELSE cp.total END) as campo20,(CASE 
	cp.tipoMoneda WHEN ' D ' THEN(cp.total * cp.tipoCambio
	) ELSE cp.total END) as campo24,(CASE cp.tipoMoneda 
	WHEN ' S ' THEN ' PEN ' ELSE ' USD ' END) as campo25, cp
	.tipoCambio as campo26, NULL as campo27, NULL as campo28
	, NULL as ref,(CASE WHEN DATE_FORMAT(cp.fecha, ' % Y - % m '
	) ! =DATE_FORMAT(cp.created_at, ' % Y - % m ') THEN ' 6 ' 
	ELSE ' 1 ' END) as campo35, cp.created_at FROM compraauto 
	as cp JOIN persona as prov ON prov.id =cp.idProveedor 
	WHERE cp.deleted_at IS NULL; 
	
	END$$


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `REPORTECOMPRASPLE`() BEGIN 
	CREATE TEMPORARY
	TABLE tmp_reporte_compras AS
	SELECT
	    CONCAT(
	        DATE_FORMAT(cp.created_at, '),') as campo1,
	        CONCAT(
	            DATE_FORMAT(cp.created_at, '),') as campo2,
	            DATE_FORMAT(
	                cp.fecha,
	                ') as campo4, (CASE cp.diasCredito WHEN ' THEN ' ELSE DATE_FORMAT(cp.fechaVencimiento,'
	            )
	        END
	    ) as campo5, (
	        CASE cp.tipoDocumento
	            WHEN ' THEN '
	            ELSE ' END) as campo6, cp.documento, 
		    (CASE WHEN prov.razonSocial IS NOT NULL THEN
	END) as campo5,(CASE cp.tipoDocumento WHEN ' B ' THEN 
	' 03 ' ELSE ' 01 ' END) as campo6, cp.documento,(CASE 
	WHEN prov.razonSocial IS NOT NULL THEN ' 6 ' ELSE ' 1 ' 
	END) as campo11, prov.documento as campo12,(CASE WHEN 
	prov.razonSocial IS NULL THEN CONCAT(prov.apellidos
	, ' ', prov.nombres) ELSE prov.razonSocial END) as 
	campo13,(CASE cp.tipoMoneda WHEN ' D ' THEN(cp.total 
	* cp.tipoCambio) ELSE cp.total END) as campo20,(CASE 
	cp.tipoMoneda WHEN ' D ' THEN(cp.total * cp.tipoCambio
	) ELSE cp.total END) as campo24,(CASE cp.tipoMoneda 
	WHEN ' S ' THEN ' PEN ' ELSE ' USD ' END) as campo25, cp
	.tipoCambio as campo26, NULL as campo27, NULL as campo28
	, NULL as ref,(CASE WHEN DATE_FORMAT(cp.fecha, ' % Y - % m '
	) ! =DATE_FORMAT(cp.created_at, ' % Y - % m ') THEN ' 6 ' 
	ELSE ' 1 ' END) as campo35, cp.created_at FROM compra 
	as cp JOIN persona as prov ON prov.id =cp.idProveedor 
	WHERE cp.deleted_at IS NULL; 
	
	END$$


DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `REPORTEINVENTARIOVALORIZADO`(IN `FECHAF` 
DATE) BEGIN 
	CREATE TEMPORARY
	TABLE
	    tmp_reporte_inventario AS
	SELECT
	    lc.direccion as almacen,
	    producto.id,
	    CONCAT( (
	            CASE
	                WHEN producto.nombre IS NULL THEN ' ELSE producto.nombre END),(CASE WHEN producto.idMarca IS NULL AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT((CASE WHEN producto.nombre IS NOT NULL THEN '
	                ELSE ' END),',
	                ml.nombre
	            )
	            ELSE (
	                CASE
	                    WHEN producto.idMarca IS NOT NULL THEN CONCAT( (
	                            CASE
	                                WHEN ml.nombre IS NOT NULL
	                                OR producto.nombre IS NOT NULL THEN ' ELSE '
	                            END
	                        ),
	                        ', ma.nombre) ELSE '
	                    END
	                )
	            END
	        ), (
	            CASE WH
	            END
	        ), (
	            CASE
	                WHEN producto.idMarca IS NULL
	                AND producto.idMarcaLlanta IS NOT NULL THEN CONCAT( (
	                        CASE
	                            WHEN producto.nombre IS NOT NULL THEN ', '
	                            ELSE ''
	                        END
	                    ),
	                    'Marca: ',
	                    ml.nombre
	                )
	                ELSE(
	                    CASE
	                        WHEN producto.idMarca IS NOT NULL THEN CONCAT( (
	                                CASE
	                                    WHEN ml.nombre IS NOT NULL
	                                    OR producto.nombre IS NOT NULL THEN ', '
	                                    ELSE ''
	                                END
	                            ),
	                            'Marca: ',
	                            ma.nombre
	                        )
	                        ELSE ''
	                    END
	                )
	            END
	        ), (
	            CASE
	                WHEN producto.idMarcaAuto IS NOT NULL THEN CONCAT ( (
	                        CASE
	                            WHEN ml.nombre IS NOT NULL
	                            OR producto.nombre IS NOT NULL
	                            OR ma.nombre IS NOT NULL THEN ', '
	                            ELSE ''
	                        END
	                    ),
	                    'Marca de Auto: ',
	                    mt.nombre
	                )
	                ELSE ''
	            END
	        )
	    ) as nombre, (
	        CASE
	            WHEN tipoProducto = 'B' THEN CONCAT (
	                'Marca: ',
	                mb.nombre,
	                ', Modelo: ',
	                modb.nombre,
	                ', Placa:',
	                producto.placaBateria,
	                ' - Tipo: ', (
	                    CASE
	                        WHEN producto.tipoBateria = 'L' THEN 'Líquida'
	                        ELSE 'Seca'
	                    END
	                )
	            )
	            ELSE NULL
	        END
	    ) as nombre2, (
	        CASE
	            WHEN producto.tipollanta IS NULL THEN '-'
	            ELSE producto.tipollanta
	        END
	    ) as tipollanta, (
	        CASE
	            WHEN producto.idMarca IS NULL
	            AND producto.idMarcaLlanta IS NOT NULL
	            AND producto.idMarcaBateria IS NULL THEN ml.nombre
	            ELSE(
	                CASE
	                    WHEN producto.idMarca IS NOT NULL
	                    AND producto.idMarcaBateria IS NULL THEN ma.nombre
	                    ELSE(
	                        CASE
	                            WHEN producto.idMarcaBateria IS NOT NULL THEN mb.nombre
	                            ELSE '-'
	                        END
	                    )
	                END
	            )
	        END
	    ) as marca, (
	        CASE
	            WHEN producto.modelo IS NULL
	            AND producto.idModeloLlanta IS NOT NULL
	            AND producto.idModeloBateria IS NULL THEN mol.nombre
	            ELSE(
	                CASE
	                    WHEN producto.modelo IS NOT NULL
	                    AND producto.idModeloBateria IS NULL THEN producto.modelo
	                    ELSE(
	                        CASE
	                            WHEN producto.idModeloBateria IS NOT NULL THEN modb.nombre
	                            ELSE '-'
	                        END
	                    )
	                END
	            )
	        END
	    ) as modelo, (
	        CASE
	            WHEN producto.idSistema IS NOT NULL THEN sa.nombre
	            ELSE '-'
	        END
	    ) as sistema, (
	        CASE
	            WHEN producto.medida IS NULL THEN '-'
	            ELSE producto.medida
	        END
	    ) as medida, (
	        CASE producto.tipoProducto
	            WHEN 'A' THEN 'Accesorio/Repuesto'
	            WHEN 'LL' THEN 'Neumáticos'
	            WHEN 'I' THEN 'Insumos'
	            WHEN 'B' THEN 'Baterías'
	            ELSE 'MUELLES'
	        END
	    ) as tipoProducto, (
	        sp.totalCompras - sp.totalVentas - sp.totalIncidencias
	    ) as stock,
	    IFNULL (spd.stock, 0) AS stock_detalle, (
	        SELECT
	            GROUP_CONCAT (
	                c.tipoMoneda,
	                '-',
	                dc.preciocompra
	            )
	        FROM compra as c
	            JOIN detallecompra as dc ON c.id = dc.idCompra
	            JOIN lote as l ON l.idCompra = c.id
	            JOIN stockproductodetalle as spd2 ON spd2.idLote = l.id
	        WHERE
	            dc.idProducto = l.idProducto
	            AND sp.idProducto = l.idProducto
	            AND spd2.id = spd.id
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT(
	                g.tipoMoneda,
	                '-',
	                dg.preciocompra
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN lote as l ON l.idGuiaCompra = g.id
	            JOIN stockproductodetalle as spd2 ON spd2.idLote = l.id
	        WHERE
	            dg.idProducto = l.idProducto
	            AND sp.idProducto = l.idProducto
	            AND spd2.id = spd.id
	    ) as det_guia
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
	WHERE
	    spd.deleted_at IS NULL
	    AND DATE_FORMAT(sp.created_at, '%Y-%m-%d') < = fechaF
	ORDER BY sp.created_at ASC;
	
	SELECT *
	FROM tmp_reporte_inventario;
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `REPORTENOTASCOMPRASAUTOSPLE`() BEGIN 
	 
		CREATE TEMPORARY
		TABLE
		    tmp_reporte_notas_compras_autos AS
		SELECT
		    CONCAT(
		        DATE_FORMAT(an.created_at, '),') as campo1,
		        CONCAT(
		            DATE_FORMAT(an.created_at, '),') as campo2,
		            DATE_FORMAT(
		                an.fecha,
		                ') as campo4, NULL as campo5, 
		    (CASE an.tipoNota WHEN ' THEN ' ELSE '
		            END
		        ) as campo6,
		        an.documentoCompra as documento, (
		            CASE
		                WHEN prov.razonSocial IS NOT NULL THEN ' ELSE '
		            END
		        ) as campo11,
		        prov.docume
	END) as campo6, an.documentoCompra as documento,(CASE 
	WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' 
	END) as campo11, prov.documento as campo12,(CASE WHEN 
	prov.razonSocial IS NULL THEN CONCAT(prov.apellidos
	, ' ', prov.nombres) ELSE prov.razonSocial END) as 
	campo13,(CASE cp.tipoMoneda WHEN 'D' THEN(an.total 
	* cp.tipoCambio * -1) ELSE an.total END) as campo20
	,(CASE cp.tipoMoneda WHEN 'D' THEN(an.total * cp.tipoCambio 
	* -1) ELSE an.total END) as campo24,(CASE cp.tipoMoneda 
	WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25, cp
	.tipoCambio as campo26, DATE_FORMAT(cp.fecha, '%d/%m/%Y'
	) as campo27,(CASE cp.tipoDocumento WHEN 'F' THEN 
	'01' ELSE '03' END) as campo28, cp.documento as ref
	,(CASE WHEN DATE_FORMAT(an.fecha, '%Y-%m') ! =DATE_FORMAT
	(an.created_at, '%Y-%m') THEN '6' ELSE '1' END) as 
	campo35, an.created_at FROM anulacionnotas as an JOIN 
	compraauto as cp ON cp.id =an.idCompraAuto JOIN persona 
	as prov ON prov.id =cp.idProveedor WHERE an.situacion 
	='V' AND an.idVenta IS NULL; 
	
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `REPORTENOTASCOMPRASPLE`() BEGIN 
	 
		CREATE TEMPORARY
		TABLE
		    tmp_reporte_notas_compras AS
		SELECT
		    CONCAT(
		        DATE_FORMAT(an.created_at, '),') as campo1,
		        CONCAT(
		            DATE_FORMAT(an.created_at, '),') as campo2,
		            DATE_FORMAT(
		                an.fecha,
		                ') as campo4, NULL as campo5, 
		    (CASE an.tipoNota WHEN ' THEN ' ELSE '
		            END
		        ) as campo6,
		        an.documentoCompra as documento, (
		            CASE
		                WHEN prov.razonSocial IS NOT NULL THEN ' ELSE '
		            END
		        ) as campo11,
		        prov.docume
	END) as campo6, an.documentoCompra as documento,(CASE 
	WHEN prov.razonSocial IS NOT NULL THEN '6' ELSE '1' 
	END) as campo11, prov.documento as campo12,(CASE WHEN 
	prov.razonSocial IS NULL THEN CONCAT(prov.apellidos
	, ' ', prov.nombres) ELSE prov.razonSocial END) as 
	campo13,(CASE cp.tipoMoneda WHEN 'D' THEN(an.total 
	* cp.tipoCambio * -1) ELSE an.total END) as campo20
	,(CASE cp.tipoMoneda WHEN 'D' THEN(an.total * cp.tipoCambio 
	* -1) ELSE an.total END) as campo24,(CASE cp.tipoMoneda 
	WHEN 'S' THEN 'PEN' ELSE 'USD' END) as campo25, cp
	.tipoCambio as campo26, DATE_FORMAT(cp.fecha, '%d/%m/%Y'
	) as campo27,(CASE cp.tipoDocumento WHEN 'F' THEN 
	'01' ELSE '03' END) as campo28, cp.documento as ref
	,(CASE WHEN DATE_FORMAT(an.fecha, '%Y-%m') ! =DATE_FORMAT
	(an.created_at, '%Y-%m') THEN '6' ELSE '1' END) as 
	campo35, an.created_at FROM anulacionnotas as an JOIN 
	compra as cp ON cp.id =an.idCompra JOIN persona as 
	prov ON prov.id =cp.idProveedor WHERE an.situacion 
	='V' AND an.idVenta IS NULL; 
	
END$ 

$ 

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE `GETEGRESOSCOMPROBANTEPRODUCTO`(IN 
`ALMACENID` BIGINT(20), IN `PRODUCTOID` BIGINT(20)
) NO SQL BEGIN 
	CREATE TEMPORARY
	TABLE tmp_egresos_prod
	select
	    `dc`.`cantidad`,
	    'SALIDA' as movimiento,
	    DATE_FORMAT(ord.fecha, '%d/%m/%Y') as fecha,
	    DATE_FORMAT(
	        do
	.created_at,
	            '%d/%m/%Y %H:%i:%s'
	    ) as fechaReg, (
	        CASE prov.tipoDocumento
	            WHEN 'PJ' THEN prov.razonSocial
	            ELSE CONCAT(
	                prov.apellidos,
	                ' ',
	                prov.nombres
	            )
	        END
	    ) as persona, (
	        CASE
	            WHEN dc.deleted_at IS NULL THEN 'A'
	            ELSE 'E'
	        END
	    ) estado,
	    'ORDEN DE TRABAJO' as tipoDoc, (
	        SELECT
	            GROUP_CONCAT(
	                c.tipoMoneda,
	                '-',
	                dc.preciocompra
	            )
	        FROM compra as c
	            JOIN detallecompra as dc ON c.id = dc.idCompra
	            JOIN lote as l ON l.idCompra = c.id
	        WHERE
	            dc.idProducto = l.idProducto
	            AND l.id = dc.idLote
	            AND l.idProducto = dc.idProducto
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT(
	                g.tipoMoneda,
	                '-',
	                dg.preciocompra
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN lote as l ON l.idGuiaCompra = g.id
	        WHERE
	            dg.idProducto = l.idProducto
	            AND l.id = dc.idLote
	            AND l.idProducto = dc.idProducto
	    ) as det_guia,
	    CONCAT( (
	            SELECT abreviatura
	            FROM tipodocumento
	            WHERE
	                id = 10
	        ),
	        LPAD(ord.serie, 2, '0'),
	        '-',
	        LPAD(ord.numero, 8, '0')
	    ) as documento,
	    `do`.`created_at`
	from `ordentrabajo` as `ord`
	    inner join `detalleordentrabajo` as `do` on `do`.`idOrdenTrabajo` = `ord`.`id`
	    inner join `detallecotizacion` as `dc` on `dc`.`idCotizacion` = `do`.`idCotizacion`
	    inner join `persona` as `prov` on `prov`.`id` = `ord`.`idCliente`
	where
	    `ord`.`idAlmacenSalida` = almacenId
	    and `dc`.`idProducto` = productoId
	UNION ALL
	select
	    `dv`.`cantidad`,
	    'SALIDA' as movimiento,
	    DATE_FORMAT(vt.fecha, '%d/%m/%Y') as fecha,
	    DATE_FORMAT(
	        dv.created_at,
	        '%d/%m/%Y %H:%i:%s'
	    ) as fechaReg, (
	        CASE prov.tipoDocumento
	            WHEN 'PJ' THEN prov.razonSocial
	            ELSE CONCAT(
	                prov.apellidos,
	                ' ',
	                prov.nombres
	            )
	        END
	    ) as persona, (
	        CASE
	            WHEN vt.situacion = 'A' THEN (
	                SELECT (
	                        CASE
	                            WHEN COUNT(an.id) > 0 THEN 'E'
	                            ELSE 'A'
	                        END
	                    ) as situacion
	                FROM anulacion as an
	                WHERE
	                    an.idVenta = vt.id
	            )
	            ELSE vt.situacion
	        END
	    ) as estado,
	    'VENTA' as tipoDoc, (
	        SELECT
	            GROUP_CONCAT(
	                c.tipoMoneda,
	                '-',
	                dc.preciocompra
	            )
	        FROM compra as c
	            JOIN detallecompra as dc ON c.id = dc.idCompra
	            JOIN lote as l ON l.idCompra = c.id
	        WHERE
	            dc.idProducto = l.idProducto
	            AND l.id = dv.idLote
	            AND l.idProducto = dv.idProducto
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT(
	                g.tipoMoneda,
	                '-',
	                dg.preciocompra
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN lote as l ON l.idGuiaCompra = g.id
	        WHERE
	            dg.idProducto = l.idProducto
	            AND l.id = dv.idLote
	            AND l.idProducto = dv.idProducto
	    ) as det_guia,
	    CONCAT(
	        vt.tipoComprobante,
	        LPAD(vt.serie, 3, '0'),
	        '-',
	        LPAD(vt.numero, 8, '0')
	    ) as documento,
	    `dv`.`created_at`
	from `venta` as `vt`
	    inner join `detalleventa` as `dv` on `dv`.`idVenta` = `vt`.`id`
	    inner join `persona` as `prov` on `prov`.`id` = `vt`.`idCliente`
	    inner join `pagodetalle` as `pd` on `pd`.`idDetalleVenta` = `dv`.`id`
	where
	    `vt`.`idAlmacenSalida` = almacenId
	    and `dv`.`idProducto` = productoId
	    and `pd`.`idCotizacion` is null
	    and `pd`.`idOrden` is null
	UNION ALL
	select
	    sds.stock as cantidad,
	    'SALIDA' as movimiento,
	    DATE_FORMAT(vt.fecha, '%d/%m/%Y') as fecha,
	    DATE_FORMAT(
	        dv.created_at,
	        '%d/%m/%Y %H:%i:%s'
	    ) as fechaReg, (
	        CASE prov.tipoDocumento
	            WHEN 'PJ' THEN prov.razonSocial
	            ELSE CONCAT(
	                prov.apellidos,
	                ' ',
	                prov.nombres
	            )
	        END
	    ) as persona, (
	        CASE
	            WHEN vt.situacion = 'A' THEN (
	                SELECT (
	                        CASE
	                            WHEN COUNT(an.id) > 0 THEN 'E'
	                            ELSE 'A'
	                        END
	                    ) as situacion
	                FROM anulacion as an
	                WHERE
	                    an.idVenta = vt.id
	            )
	            ELSE vt.situacion
	        END
	    ) as estado,
	    'VENTA' as tipoDoc, (
	        SELECT
	            GROUP_CONCAT(
	                c.tipoMoneda,
	                '-',
	                dc.preciocompra
	            )
	        FROM compra as c
	            JOIN detallecompra as dc ON c.id = dc.idCompra
	            JOIN lote as l ON l.idCompra = c.id
	            JOIN stockproductodetalle as spd ON spd.idLote = l.id
	        WHERE
	            dc.idProducto = l.idProducto
	            AND sds.idStockProductoDetalle = spd.id
	            AND l.idProducto = dcot.idProducto
	    ) as det_compra, (
	        SELECT
	            GROUP_CONCAT(
	                g.tipoMoneda,
	                '-',
	                dg.preciocompra
	            )
	        FROM guia as g
	            JOIN detalleguia as dg ON g.id = dg.idGuia
	            JOIN lote as l ON l.idGuiaCompra = g.id
	            JOIN stockproductodetalle as spd ON spd.idLote = l.id
	        WHERE
	            dg.idProducto = l.idProducto
	            AND sds.idStockProductoDetalle = spd.id
	            AND l.idProducto = dcot.idProducto
	    ) as det_guia,
	    CONCAT(
	        vt.tipoComprobante,
	        LPAD(vt.serie, 3, '0'),
	        '-',
	        LPAD(vt.numero, 8, '0')
	    ) as documento,
	    `dv`.`created_at`
	from `venta` as `vt`
	    inner join `detalleventa` as `dv` on `dv`.`idVenta` = `vt`.`id`
	    inner join `persona` as `prov` on `prov`.`id` = `vt`.`idCliente`
	    inner join `pagodetalle` as `pd` on `pd`.`idDetalleVenta` = `dv`.`id`
	    inner join `detallecotizacion` as `dcot` on `dcot`.`idCotizacion` = `pd`.`idCotizacion`
	    inner join stockproductodetallesalida sds ON sds.idProducto = dcot.idProducto
	where
	    dcot.idProducto = dv.idProducto
	    and `vt`.`idAlmacenSalida` = 2
	    and `dv`.`idProducto` = 9677
	    and `pd`.`idCotizacion` is not null
	    and `pd`.`idOrden` is null
	    and sds.idAlmacen = vt.idAlmacenSalida
	    AND sds.idVenta = vt.id
	    AND dcot.deleted_at IS NULL;
END$ 

$ 

DELIMITER ;