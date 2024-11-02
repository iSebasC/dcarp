<template>
    <div class="content-wrapper" :id="'sec_' + entidad">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link tag="a" to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Servicios</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12">
                <div
                    class="btn-group float-md-right"
                    role="group"
                    aria-label="Agregar Servicio"
                >
                    <router-link
                        tag="a"
                        to="/servicio/crear"
                        class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1"
                        type="button"
                        ><i class="mdi mdi-plus icon-size"></i> Agregar
                        Servicio</router-link
                    >
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Default styling start -->
            <div class="row justify-content-center" id="default">
                <div class="col-md-12 col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Listado de Servicios</h4>
                        </div>
                        <div class="card-content collapse show pb-2">
                            <div class="card-body card-dashboard pt-1 pb-0">
                                <fieldset class="form-group">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group row">
                                                        <label
                                                            :for="
                                                                'servicio_' +
                                                                entidad
                                                            "
                                                            class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0"
                                                            >Servicio:</label
                                                        >
                                                        <div class="col-sm-3">
                                                            <input
                                                                type="text"
                                                                name="servicio"
                                                                class="form-control form-control-sm"
                                                                :id="
                                                                    'servicio_' +
                                                                    entidad
                                                                "
                                                                @keyup.enter="
                                                                    busqueda
                                                                "
                                                                maxlength="255"
                                                            />
                                                        </div>

                                                        <label
                                                            :for="
                                                                'tiempo_' +
                                                                entidad
                                                            "
                                                            class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0"
                                                            >T.
                                                            Ejecución:</label
                                                        >
                                                        <div class="col-sm-2">
                                                            <input
                                                                type="text"
                                                                name="tiempo"
                                                                class="form-control form-control-sm"
                                                                :id="
                                                                    'tiempo_' +
                                                                    entidad
                                                                "
                                                                @keyup.enter="
                                                                    busqueda
                                                                "
                                                                maxlength="255"
                                                            />
                                                        </div>

                                                        <label
                                                            :for="
                                                                'precio_' +
                                                                entidad
                                                            "
                                                            class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0"
                                                            >Precio:</label
                                                        >
                                                        <div class="col-sm-2">
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                name="precio"
                                                                class="form-control text-center form-control-sm"
                                                                :id="
                                                                    'precio_' +
                                                                    entidad
                                                                "
                                                                @keyup.enter="
                                                                    busqueda
                                                                "
                                                                maxlength="255"
                                                            />
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label
                                                            :for="
                                                                'tipo_' +
                                                                entidad
                                                            "
                                                            class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0"
                                                            >Tipo de
                                                            Servicio:</label
                                                        >
                                                        <div class="col-md-6">
                                                            <select
                                                                class="custom-select custom-select-sm"
                                                                :id="
                                                                    'tipo_' +
                                                                    entidad
                                                                "
                                                                @change="
                                                                    busqueda
                                                                "
                                                            >
                                                                <option
                                                                    value="todo"
                                                                >
                                                                    Todos
                                                                </option>
                                                                <option
                                                                    v-for="f in tipos"
                                                                    :key="f.id"
                                                                    :value="
                                                                        f.id
                                                                    "
                                                                >
                                                                    {{
                                                                        f.nombre
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <label
                                                            :for="
                                                                'macro_servicio_' +
                                                                entidad
                                                            "
                                                            class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0"
                                                            >Macro
                                                            Servicio:</label
                                                        >
                                                        <div class="col-md-2">
                                                            <select
                                                                class="custom-select custom-select-sm"
                                                                :id="
                                                                    'macro_servicio_' +
                                                                    entidad
                                                                "
                                                                @change="
                                                                    busqueda
                                                                "
                                                            >
                                                                <option
                                                                    value="todo"
                                                                >
                                                                    Todos
                                                                </option>
                                                                <option
                                                                    v-for="f in macroservicios"
                                                                    :key="f.id"
                                                                    :value="
                                                                        f.id
                                                                    "
                                                                >
                                                                    {{
                                                                        f.descripcion
                                                                    }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 pt-xs-1 pl-xs-2">
                                            <div
                                                class="content-buttons btn-group pl-1"
                                            >
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-icon btn-info mb-1"
                                                    title="Buscar"
                                                    @click="busqueda"
                                                >
                                                    <i
                                                        class="mdi mdi-magnify icon-size"
                                                    ></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-icon btn-success mb-1"
                                                    @click="excel"
                                                    title="Exportar a Excel"
                                                >
                                                    <i
                                                        class="mdi mdi-file-excel icon-size"
                                                    ></i>
                                                </button>

                                                <button
                                                    type="button"
                                                    title="Cambiar Costo de Hora/Hombre"
                                                    class="btn btn-sm btn-icon btn-primary mb-1"
                                                    @click="abrirGestion"
                                                >
                                                    <i
                                                        class="mdi mdi-information-slab-circle-outline icon-size"
                                                    ></i>
                                                </button>
                                            </div>

                                            <div class="form-group row">
                                                <label
                                                    class="col-md-5 col-form-label pt-1 pl-1 pr-0"
                                                    :for="'cantidad_' + entidad"
                                                    style="margin-top: 3px"
                                                    >Mostrar:
                                                </label>
                                                <div class="col-md-5 pt-1 pl-0">
                                                    <select
                                                        class="custom-select custom-select-sm pr-2 ml-1"
                                                        :id="
                                                            'cantidad_' +
                                                            entidad
                                                        "
                                                        title="Registros por Página"
                                                        @change="busqueda"
                                                    >
                                                        <option value="10">
                                                            10
                                                        </option>
                                                        <option value="25">
                                                            25
                                                        </option>
                                                        <option value="50">
                                                            50
                                                        </option>
                                                        <option value="100">
                                                            100
                                                        </option>
                                                        <option value="500">
                                                            500
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <span
                                class="text-info col-form-label pl-2 ml-1 mb-2"
                                :id="'loading_' + entidad"
                                ><Loader /></span
                            >
                            <div
                                class="table-responsive px-2 d-none"
                                :id="'tabla_' + entidad"
                            >
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left">#</th>
                                            <th class="text-center">
                                                Macro Servicio
                                            </th>
                                            <th class="text-center">
                                                Tipo de Servicio
                                            </th>
                                            <th
                                                class="text-center"
                                                style="width: 180px"
                                            >
                                                Servicio
                                            </th>
                                            <th class="text-center">
                                                Tipo de Vehículo
                                            </th>
                                            <th class="text-center">
                                                Tiempo de Ejecución
                                            </th>
                                            <th class="text-center">
                                                Precio (S/)
                                            </th>
                                            <th class="text-center">
                                                Actualizado El
                                            </th>
                                            <th class="text-center">
                                                Operaciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!servicios.length">
                                            <td
                                                class="text-left text-danger"
                                                colspan="8"
                                            >
                                                <strong
                                                    >No se Encontraron
                                                    Resultados en su
                                                    Búsqueda</strong
                                                >
                                            </td>
                                        </tr>

                                        <tr
                                            v-for="(p, index) in servicios"
                                            :key="p.id"
                                        >
                                            <td
                                                class="text-left"
                                                v-text="
                                                    index + 1 + inicio < 10
                                                        ? '0' +
                                                          (index + inicio + 1)
                                                        : index + inicio + 1
                                                "
                                            ></td>
                                            <td
                                                class="text-center"
                                                v-text="
                                                    p.macroservicio
                                                        ? p.macroservicio
                                                        : '-'
                                                "
                                            ></td>
                                            <td
                                                class="text-center"
                                                v-text="p.tipo"
                                            ></td>
                                            <td
                                                class="text-center"
                                                style="width: 180px"
                                                v-text="p.nombre"
                                            ></td>
                                            <td
                                                class="text-center"
                                                v-text="p.tipoVehiculo"
                                            ></td>
                                            <td
                                                class="text-center"
                                                v-text="p.tiempo"
                                            ></td>
                                            <td
                                                class="text-right"
                                                v-text="p.precio"
                                            ></td>
                                            <td
                                                class="text-center"
                                                v-text="p.fechaRegistro"
                                            ></td>
                                            <td class="text-center">
                                                <div
                                                    class="content-buttons btn-group"
                                                >
                                                    <router-link
                                                        tag="a"
                                                        title="Editar Servicio"
                                                        v-bind:to="{
                                                            name: 'editarservicio',
                                                            params: {
                                                                id: p.id,
                                                            },
                                                        }"
                                                        class="btn btn-sm btn-success"
                                                    >
                                                        <i
                                                            class="mdi mdi-pencil icon-size"
                                                        ></i>
                                                    </router-link>
                                                    <button
                                                        @click="
                                                            eliminar(
                                                                p.id,
                                                                p.nombre
                                                            )
                                                        "
                                                        title="Eliminar Servicio"
                                                        class="btn btn-sm btn-danger"
                                                    >
                                                        <i
                                                            class="mdi mdi-delete-outline icon-size"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <nav class="pl-2">
                                    <ul
                                        class="pagination justify-content-right"
                                    >
                                        <li class="page-item">
                                            <a
                                                class="page-link bg-info text-white"
                                                href="javascript:void(0);"
                                                aria-label="total"
                                                ><strong>TOTAL: </strong
                                                ><span v-text="total"></span
                                            ></a>
                                        </li>

                                        <li
                                            class="page-item"
                                            @click="
                                                paramInicio < pageActual &&
                                                paramInicio > 0
                                                    ? buscar('prev')
                                                    : ''
                                            "
                                        >
                                            <a
                                                class="page-link"
                                                href="javascript:void(0);"
                                                aria-label="Previous"
                                                >«</a
                                            >
                                        </li>

                                        <li
                                            v-for="(op, index) in opciones"
                                            :key="index"
                                            :class="
                                                op.opc == pageActual
                                                    ? 'page-item active'
                                                    : 'page-item'
                                            "
                                            @click="
                                                op.opc != pageActual
                                                    ? buscar(op.opc)
                                                    : ''
                                            "
                                        >
                                            <a
                                                class="page-link"
                                                href="javascript:void(0);"
                                                v-text="op.opc"
                                                :disabled="op.bloqueado"
                                            ></a>
                                        </li>

                                        <li
                                            class="page-item"
                                            @click="
                                                paramFin > pageActual &&
                                                paramFin > 0
                                                    ? buscar('next')
                                                    : ''
                                            "
                                        >
                                            <a
                                                class="page-link"
                                                href="javascript:void(0);"
                                                aria-label="Next"
                                                >»</a
                                            >
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <GestionPrecio ref="gestionprecio"></GestionPrecio>
    </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import GestionPrecio from "./GestionPrecio";
import Loader from "../Loader";

export default {
    name: "Servicio",
    mixins: [misMixins],
    components: {
        GestionPrecio,
        Loader
    },
    data() {
        return {
            filas: 10,
            pageActual: 1,
            opciones: [],
            servicios: [],
            marcas: [],
            tipos: [],
            macroservicios: [],
            total: "",
            inicio: "",
            fin: "",
            paramInicio: "",
            paramFin: "",
            token: this.$store.state.token,
            estadoListado: false,
            entidad: "servicio",
        };
    },
    methods: {
        buscar: function (attr) {
            let me = this;
            if (attr == "next") {
                me.pageActual = me.pageActual + 1;
            } else {
                if (attr == "prev") {
                    me.pageActual = me.pageActual - 1;
                } else {
                    me.pageActual = attr;
                }
            }

            me.busqueda();
        },
        async busqueda() {
            let me = this;
            let servicio = document.getElementById(
                "servicio_" + me.entidad
            ).value;
            let tiempo = document.getElementById("tiempo_" + me.entidad).value;
            let precio = document.getElementById("precio_" + me.entidad).value;
            let tipoId = document.getElementById("tipo_" + me.entidad).value;
            let macroServicioId = document.getElementById(
                "macro_servicio_" + me.entidad
            ).value;

            var el = document.getElementById("loading_" + me.entidad);
            el.classList.add("hidden");

            me.filas = document.getElementById("cantidad_" + me.entidad).value;

            let response = await axios({
                method: "post",
                url: "/servicio",
                data: {
                    servicio: servicio,
                    tiempo: tiempo,
                    precio: precio,
                    tipoId: tipoId,
                    macroServicioId: macroServicioId,
                    filas: me.filas,
                    page: me.pageActual,
                    _token: me.token,
                },
            });

            me.servicios = response.data.servicios;
            me.total = response.data.cantidad;
            me.pageActual = response.data.page;
            me.opciones = response.data.paginador;
            me.inicio = response.data.inicio;
            me.fin = response.data.fin;
            me.paramInicio = response.data.paramInicio;
            me.paramFin = response.data.paramFin;

            me.renderTabla(me.entidad);
            // var el2 = document.getElementById('tabla-servicios')
            // el2.classList.remove('d-none')
        },
        abrirGestion() {
            let me = this;
            me.isValidSession();

            this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
            me.$refs.gestionprecio.showModal();
        },
        excel() {
            let me = this;
            let servicio = document.getElementById(
                "servicio_" + me.entidad
            ).value;
            let tiempo = document.getElementById("tiempo_" + me.entidad).value;
            let precio = document.getElementById("precio_" + me.entidad).value;
            let tipoId = document.getElementById("tipo_" + me.entidad).value;

            window.open(
                `/servicio/excel?servicio=${servicio}&tiempo=${tiempo}&precio=${precio}&tipoid=${tipoId}`,
                "_blank"
            );
        },
        async cargarDatos() {
            let me = this;
            let tipos = await axios.get("/tiposservicio");
            me.tipos = tipos.data.tipos;

            let response = await axios.get(`/servicio/getmacroservicios`);
            me.macroservicios = response.data.lista;
            me.busqueda();
        },
        async eliminar(id, nombre) {
            let me = this;
            let token = this.$store.state.token;
            swal({
                title: "Confirmar Operación",
                html:
                    "¿Seguro que desea Eliminar el Servicio <strong>" +
                    nombre +
                    "</strong>?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sí, Eliminar",
                cancelButtonText: "No, Cancelar",
                allowOutsideClick: false,
                closeOnConfirm: false,
                closeOnCancel: false,
            }).then(async function (result) {
                if (result.value) {
                    let response = await axios({
                        method: "post",
                        url: "/eliminarservicio",
                        data: {
                            id: id,
                            _token: token,
                        },
                    });

                    if (response.data.estado === true) {
                        setTimeout(function () {
                            swal({
                                title: "¡Eliminado!",
                                text: "Servicio Eliminado con Éxito.",
                                type: "success",
                            }).then(function () {
                                me.busqueda();
                            });
                        }, 500);
                    } else {
                        swal("¡Cancelado!", "Operación Cancelada", "error");
                    }
                } 
                // else {
                //     swal("¡Cancelado!", "Operación Cancelada", "error");
                // }
            });
        },
    },
    async created() {
        this.$on("buscarServicio", function () {
            this.busqueda();
        });

        // let me = this
        // me.cargarDatos()
    },
    activated: async function () {
        let me = this;
        me.isValidSession();

        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
        this.$route.meta.auth = localStorage.getItem("autenticado");
        // window.setTimeout( function () {
        me.cargarDatos();

        // }, 1000)
    },
    deactivated: function () {
        // this.$store.state.mostrarModal = false
    },
    beforeDestroy: function () {
        var element = document.getElementById("sec-servicio");
        element.classList.add("hidden");
        // this.$store.state.mostrarModal = false
    },
};
</script>
