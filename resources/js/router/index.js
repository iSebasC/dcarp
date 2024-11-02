import Vue from "vue";
import Router from "vue-router";

import Login from "../components/Login";
import Header from "../components/Header";
import Menu from "../components/Menu";

import Inicio from "../components/Inicio";

import Producto from "../components/Producto/Listado";
import Mantenimiento from "../components/Producto/Mantenimiento";

import Local from "../components/Local/Listado";
import MantenimientoLocal from "../components/Local/Mantenimiento";
import RelacionTienda from "../components/Local/RelacionTienda";

import Personal from "../components/Personal/Listado";
import MantenimientoPersonal from "../components/Personal/Mantenimiento";

import Permiso from "../components/Permiso/Listado";
import MantenimientoPermiso from "../components/Permiso/Mantenimiento";

import Inventario from "../components/Inventario/Listado";

import Paquete from "../components/Paquete/Listado";
import MantenimientoPaquete from "../components/Paquete/Mantenimiento";

import Reporte from "../components/Inventario/Reporte";

import Venta from "../components/Venta/Listado";
import MantenimientoVenta from "../components/Venta/Mantenimiento";
import MantenimientoVentaAuto from "../components/Venta/MantenimientoAuto";

import Auto from "../components/Auto/Listado";
import MantenimientoAuto from "../components/Auto/Mantenimiento";
// import MantenimientoAutoEdit from "../components/Auto/Mantenimiento";

import CotizacionAuto from "../components/CotizacionAuto/Listado";
import MantenimientoCotizacionAuto from "../components/CotizacionAuto/Mantenimiento";

import Servicio from "../components/Servicio/Listado";
import MantenimientoServicio from "../components/Servicio/Mantenimiento";
// import MantenimientoServicioEdit from "../components/Servicio/Mantenimiento";

import Cita from "../components/Cita/Listado";

import Caja from "../components/Caja/Mantenimiento";
import ReporteCaja from "../components/Caja/Listado.vue";

import Cotizacion from "../components/Cotizacion/Listado";
import MantenimientoCotizacion from "../components/Cotizacion/Mantenimiento";
// import MantenimientoCotizacionEdit from "../components/Cotizacion/Mantenimiento";

import Compra from "../components/Compra/Listado";
import MantenimientoCompra from "../components/Compra/Mantenimiento";
import MantenimientoConvertCompra from "../components/PedidosCompra/MantenimientoCompra";


import MantenimientoConvertCotizacion from "../components/Oportunidades/ConvertCotizacion";

import BajaDocumento from "../components/BajaDocumento/Listado";
import MantenimientoBajaDocumento from "../components/BajaDocumento/Mantenimiento";

import CompraAuto from "../components/CompraAuto/Listado";
import MantenimientoCompraAuto from "../components/CompraAuto/Mantenimiento";

import OrdenTrabajo from "../components/OrdenTrabajo/Listado";
import MantenimientoOrdenTrabajo from "../components/OrdenTrabajo/Mantenimiento";

import Ganancia from "../components/Ganancia/Listado";

import Perfil from "../components/SocialPerfil/LineaTiempo";

// import LineaTiempo from "../components/SocialPerfil/LineaTiempo"
import EvaluacionTrabajo from "../components/EvaluacionTrabajo/Listado";

import Revision from "../components/RevisionOrden/Listado";
import Encuesta from "../components/Encuesta/Listado";

import Clientes from "../components/Clientes/Listado";
import Proveedores from "../components/Proveedores/Listado";

import ReporteES from "../components/Inventario/ReporteES";

import ListadoGuias from "../components/GuiaInventario/Listado";

import ReporteMes from "../components/Reportes/Reporte";
import ReporteHistorial from "../components/Reportes/ReporteCliente.vue"
import ReporteHistorialGeneral from "../components/Reportes/ReporteClienteGeneral.vue"
import Error500 from "../components/error500.vue";

import Reclamo from "../components/Reclamo/Listado";
import MantenimientoReclamo from "../components/Reclamo/Mantenimiento";


import CuentaXCobrarPagar from "../components/CuentasXCobrarYPagar/Listado";
import MantenimientoCuentaXCobrarPagar from "../components/CuentasXCobrarYPagar/Mantenimiento";

import Prospecto from "../components/Prospectos/Listado";
import MantenimientoProspecto from "../components/Prospectos/Mantenimiento";


import PedidoCompra from "../components/PedidosCompra/Listado";
import MantenimientoPedidoCompra from "../components/PedidosCompra/Mantenimiento";

import ListadoObsequio from "../components/Obsequio/Listado";

import ListadoOportunidad from "../components/Oportunidades/Listado";

Vue.use(Router);

const router = new Router({
    mode: "history",
    transitionOnLoad: true,
    routes: [
        {
            path: "/inicio",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Inicio,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "inicio",
        },
        {
            path: "/error500",
            component: Error500,
            props: true,
            name: "error500",
        },
        {
            path: "/producto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Producto,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "producto",
        },
        {
            path: "/producto/crear",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Mantenimiento,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "crearproducto",
        },
        {
            path: "/producto/editar/:id",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Mantenimiento,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "editarproducto",
        },
        {
            path: "/servicio",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Servicio,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "servicio",
        },
        {
            path: "/servicio/crear",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoServicio,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "crearservicio",
        },
        {
            path: "/servicio/editar/:id",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoServicio,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "editarservicio",
        },
        {
            path: "/local",
            components: { headrender: Header, menurender: Menu, render: Local },
            meta: { isAuth: true, isUrlValid: true },
            name: "local",
        },
        {
            path: "/local/crear",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoLocal,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "crearlocal",
        },
        {
            path: "/local/editar/:id",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoLocal,
            },
            meta: { isAuth: true, isUrlValid: true },
            name: "editarlocal",
        },
        {
            path: "/local/reltl/:id",
            components: {
                headrender: Header,
                menurender: Menu,
                render: RelacionTienda,
            },
            meta: { isAuth: true, isUrlValid: true },
            props: true,
            name: "relaciontienda",
        },
        {
            path: "/permisos",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Permiso,
            },
            meta: { isAuth: true, isUrlValid: true },
            props: true,
            name: "permiso",
        },
        {
            path: "/permisos/crear",
            meta: { isAuth: true, isUrlValid: true },
            name: "crearpermiso",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPermiso,
            },
        },
        {
            path: "/permisos/editar/:id",
            meta: { isAuth: true, isUrlValid: true },
            name: "editarpermiso",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPermiso,
            },
            props: true,
        },
        {
            path: "/personal",
            meta: { isAuth: true, isUrlValid: true },
            name: "personal",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Personal,
            },
        },
        {
            path: "/personal/crear",
            meta: { isAuth: true, isUrlValid: true },
            name: "crearpersonal",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPersonal,
            },
        },
        {
            path: "/personal/editar/:id",
            meta: { isAuth: true, isUrlValid: true },
            name: "editarpersonal",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPersonal,
            },
            props: true,
        },
        {
            path: "/auto",
            meta: { isAuth: true, isUrlValid: true },
            name: "auto",
            components: { headrender: Header, menurender: Menu, render: Auto },
        },
        {
            path: "/auto/crear",
            meta: { isAuth: true, isUrlValid: true },
            name: "autocrear",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoAuto,
            },
        },
        {
            path: "/auto/editar/:id",
            meta: { isAuth: true, isUrlValid: true },
            name: "editarauto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoAuto,
            },
            props: true,
        },
        {
            path: "/compraauto",
            name: "compraauto",
            meta: { isAuth: true, isUrlValid: true },
            components: {
                headrender: Header,
                menurender: Menu,
                render: CompraAuto,
            },
        },
        {
            path: "/compraauto/crear",
            name: "crearcompraauto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCompraAuto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/inventario",
            name: "inventario",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Inventario,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/repinventario",
            name: "repinventario",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Reporte,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cotizacionauto",
            name: "cotizacionauto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: CotizacionAuto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cotizacionauto/crear",
            name: "crearcotizacionauto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCotizacionAuto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cotizacion",
            name: "cotizacion",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Cotizacion,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cotizacion/editar/:id",
            meta: { isAuth: true, isUrlValid: true },
            name: "editarcotizacion",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCotizacion,
            },
            props: true,
        },
        {
            path: "/cotizacion/crear",
            name: "crearcotizacion",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCotizacion,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/paquete",
            name: "paquete",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Paquete,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/paquete/crear",
            name: "crearpaquete",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPaquete,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/compra",
            name: "compra",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Compra,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/compra/crear",
            name: "crearcompra",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCompra,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/pedidocompra",
            name: "pedidocompra",
            components: {
                headrender: Header,
                menurender: Menu,
                render: PedidoCompra,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/pedidocompra/crear",
            name: "crearpedidocompra",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoPedidoCompra,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/pedidocompra/convert/:pedidoId",
            meta: { isAuth: true, isUrlValid: true },
            name: "convertCompra",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoConvertCompra,
            },
            props: true,
        },
        {
            path: "/oportunidad/convert/:oportunidadId",
            meta: { isAuth: true, isUrlValid: true },
            name: "ConvertCotizacion",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoConvertCotizacion,
            },
            props: true,
        },
        {
            path: "/ganancia",
            name: "ganancia",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Ganancia,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/ordentrabajo",
            name: "orden",
            components: {
                headrender: Header,
                menurender: Menu,
                render: OrdenTrabajo,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/ordentrabajo/crear",
            name: "crearorden",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoOrdenTrabajo,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cita",
            name: "cita",
            components: { headrender: Header, menurender: Menu, render: Cita },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/evaluaciontrabajo",
            name: "evaluacion",
            components: {
                headrender: Header,
                menurender: Menu,
                render: EvaluacionTrabajo,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/revision",
            name: "revision",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Revision,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/venta",
            name: "venta",
            components: { headrender: Header, menurender: Menu, render: Venta },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/venta/crear",
            name: "crearventa",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoVenta,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/venta/crearauto",
            name: "crearauto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoVentaAuto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/seguimiento",
            name: "seguimiento",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Perfil,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/caja",
            name: "crearcaja",
            components: { headrender: Header, menurender: Menu, render: Caja },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/encuesta",
            name: "encuesta",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Encuesta,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/nota",
            name: "nota",
            components: {
                headrender: Header,
                menurender: Menu,
                render: BajaDocumento,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/nota/crear",
            name: "crearnota",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoBajaDocumento,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cliente",
            name: "cliente",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Clientes,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/obsequio",
            name: "obsequio",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ListadoObsequio,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/proveedor",
            name: "proveedor",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Proveedores,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/repensa",
            name: "repensa",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ReporteES,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/reportemes",
            name: "reportemes",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ReporteMes,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/rephistorial",
            name: "rephistorial",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ReporteHistorial,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/rephistorialcliente",
            name: "rephistorialcliente",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ReporteHistorialGeneral,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/repcaja",
            name: "repcaja",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ReporteCaja,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/reclamo",
            name: "reclamo",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Reclamo,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cuentaxcyp",
            name: "cuentaxcyp",
            components: {
                headrender: Header,
                menurender: Menu,
                render: CuentaXCobrarPagar,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/prospecto",
            name: "prospecto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: Prospecto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/oportunidad",
            name: "oportunidad",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ListadoOportunidad,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/reclamo/crear",
            name: "creareclamo",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoReclamo,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/cuentaxcyp/crear",
            name: "crearecuenta",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoCuentaXCobrarPagar,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/prospecto/crear",
            name: "creaprospecto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoProspecto,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/prospecto/editar/:id",
            meta: { isAuth: true, isUrlValid: true },
            name: "editarprospecto",
            components: {
                headrender: Header,
                menurender: Menu,
                render: MantenimientoProspecto,
            },
            props: true,
        },
        {
            path: "/guiainventario",
            name: "guiainventario",
            components: {
                headrender: Header,
                menurender: Menu,
                render: ListadoGuias,
            },
            meta: { isAuth: true, isUrlValid: true },
        },
        {
            path: "/",
            name: "login",
            component: Login,
            props: true,
            meta: { isUrlValid: true },
        },
        {
            path: "/*",
            beforeEnter(to) {
                window.location = `/`;
            },
        },
    ],
});

// router.beforeEach((to, from, next) => {
//     let userAuthenticado = localStorage.getItem('autenticado')

//     let authorized  = to.matched.some(record => record.meta.auth);

//     // console.log('1.'+ userAuthenticado+', 2. '+authorized);

//     // if (userAuthenticado == null && !authorized) {
//     //     next('/');
//     // } else
//     if (userAuthenticado == null && authorized && to.path != '/' && authorized) {
//         next('/');
//     } else if ( (to.path == '/' || to.path == '/login') && userAuthenticado != null && authorized) {
//         next('/inicio');
//     } else if ( (to.path == '/' || to.path == '/login') && userAuthenticado != null && !authorized) {
//         next('/inicio');
//     } else {
//         next();
//     }
// })

export default router;
