<template>
    <div class="content-wrapper" id="sec-servicio-mant">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link tag="a" to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link tag="a" to="/servicio"
                                    >Servicios</router-link
                                >
                            </li>
                            <li class="breadcrumb-item active">
                                {{ accion }} Servicio
                            </li>
                        </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">
                    {{ accion }} Servicio
                </h4>
            </div>
        </div>
        <div class="content-body">
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height justify-content-md-center">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form">
                                    <i class="la la-scroll"></i> Datos de
                                    Servicio
                                </h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div
                                    class="card-body justify-content-md-center"
                                >
                                    <form
                                        class="form"
                                        :id="'formEnv_' + entidad"
                                        method="POST"
                                        action="/guardarservicio"
                                    >
                                        <input
                                            type="hidden"
                                            :value="this.id"
                                            name="id"
                                        />
                                        <input
                                            type="hidden"
                                            :value="this.$store.state.token"
                                            name="_token"
                                        />
                                        <div class="form-body">
                                            <div class="row">
                                                <div
                                                    class="col-md-2 col-xs-12 ml-2"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'select_macro_servicio' +
                                                                entidad
                                                            "
                                                            >Macro Servicio
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <select
                                                            name="select_macroservicio"
                                                            class="custom-select custom-select-sm"
                                                            :id="
                                                                'select_macro_servicio' +
                                                                entidad
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                selected=""
                                                                disabled=""
                                                            >
                                                                Seleccione
                                                            </option>
                                                            <option
                                                                v-for="f in listMacroservicios"
                                                                :selected="
                                                                    f.id ==
                                                                    macroServicioId
                                                                "
                                                                :key="f.id"
                                                                :value="f.id"
                                                            >
                                                                {{
                                                                    f.descripcion
                                                                }}
                                                            </option>
                                                            <!-- <option value="otro">Indique Otro</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'nombre_' +
                                                                entidad
                                                            "
                                                            >Nombre
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <input
                                                            type="text"
                                                            v-focus-on-create
                                                            :id="
                                                                'nombre_' +
                                                                entidad
                                                            "
                                                            class="form-control form-control-sm"
                                                            name="nombre"
                                                            maxlength="255"
                                                            @keypress="
                                                                soloLetras(
                                                                    $event
                                                                )
                                                            "
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div
                                                    class="col-md-4 col-xs-12 ml-2"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'select_tipo_' +
                                                                entidad
                                                            "
                                                            >Tipo de Servicio
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <select
                                                            name="select_tipo"
                                                            class="custom-select custom-select-sm"
                                                            :id="
                                                                'select_tipo_' +
                                                                entidad
                                                            "
                                                            @change="
                                                                capturarTipo
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                selected=""
                                                                disabled=""
                                                            >
                                                                Seleccione
                                                            </option>
                                                            <option
                                                                v-for="f in tipos"
                                                                :selected="
                                                                    f.id ==
                                                                    tipoId
                                                                "
                                                                :key="f.id"
                                                                :value="f.id"
                                                            >
                                                                {{ f.nombre }}
                                                            </option>
                                                            <option
                                                                value="otro"
                                                            >
                                                                Indique Otro
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div
                                                    class="col-md-3 col-xs-12 ocultar"
                                                    id="tipo_ocultar"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'tipo_' +
                                                                entidad
                                                            "
                                                            >Tipo de Servicio
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <input
                                                            type="text"
                                                            :id="
                                                                'tipo_' +
                                                                entidad
                                                            "
                                                            class="form-control form-control-sm"
                                                            name="tipo"
                                                            maxlength="255"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div
                                                    class="col-md-3 col-xs-12 ml-2"
                                                >
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'select_tipo_vehiculo_' +
                                                                entidad
                                                            "
                                                            >Tipo de Vehículo
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <select
                                                            name="select_tipovehiculo"
                                                            class="custom-select custom-select-sm"
                                                            :id="
                                                                'select_tipo_vehiculo_' +
                                                                entidad
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                selected=""
                                                                disabled=""
                                                            >
                                                                Seleccione
                                                            </option>
                                                            <option
                                                                v-for="f in tiposV"
                                                                :selected="
                                                                    f.id ==
                                                                    tipoVId
                                                                "
                                                                :key="f.id"
                                                                :value="f.id"
                                                            >
                                                                {{ f.nombre }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 col-xs-12">
                                                    <div
                                                        class="form-group ml-2"
                                                    >
                                                        <label
                                                            :for="
                                                                'tiempo_' +
                                                                entidad
                                                            "
                                                            >T. Ejecución
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <input
                                                            type="number"
                                                            :id="
                                                                'tiempo_' +
                                                                entidad
                                                            "
                                                            @change="
                                                                calcularPrecio
                                                            "
                                                            class="form-control form-control-sm text-center"
                                                            name="tiempo"
                                                            min="1"
                                                            value="1"
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label
                                                            :for="
                                                                'select_tiempo_' +
                                                                entidad
                                                            "
                                                            >Unidad
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <select
                                                            name="select_tiempo"
                                                            class="custom-select custom-select-sm"
                                                            :id="
                                                                'select_tiempo_' +
                                                                entidad
                                                            "
                                                            @change="
                                                                calcularPrecio
                                                            "
                                                        >
                                                            <option
                                                                value=""
                                                                selected=""
                                                                disabled=""
                                                            >
                                                                Seleccione
                                                            </option>
                                                            <option
                                                                v-for="f in unidades"
                                                                :selected="
                                                                    f.id ==
                                                                    unidadId
                                                                "
                                                                :key="f.id"
                                                                :value="f.id"
                                                            >
                                                                {{ f.nombre }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div
                                                        class="form-group ml-2"
                                                    >
                                                        <label
                                                            :for="
                                                                'precio_' +
                                                                entidad
                                                            "
                                                            >Precio
                                                            <span
                                                                class="text-danger"
                                                                >*</span
                                                            ></label
                                                        >
                                                        <input
                                                            type="number"
                                                            readonly=""
                                                            min="1"
                                                            step="0.01"
                                                            :id="
                                                                'precio_' +
                                                                entidad
                                                            "
                                                            class="form-control form-control-sm text-center"
                                                            name="precio"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div
                                                    class="col-md-12"
                                                    :id="
                                                        'data-error_' + entidad
                                                    "
                                                ></div>
                                            </div>

                                            <div class="form-actions ml-1">
                                                <button
                                                    type="button"
                                                    class="btn btn-success btn-sm"
                                                    :id="'btnEnvio_' + entidad"
                                                    @click="
                                                        enviarForm(url, entidad)
                                                    "
                                                >
                                                    <i
                                                        class="mdi mdi-check-bold icon-size"
                                                    ></i>
                                                    Guardar
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="atras(url)"
                                                    class="btn btn-danger btn-sm mr-1"
                                                >
                                                    <i
                                                        class="mdi mdi-close icon-size"
                                                    ></i>
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
export default {
    name: "MantenimientoAuto",
    mixins: [misMixins],
    data() {
        return {
            accion: "Registrar",
            url: "/servicio",
            unidades: [
                { id: "min", nombre: "Minuto(s)" },
                { id: "hr", nombre: "Hora(s)" },
                // {id: 'sem', nombre: 'Semana(s)'},
                // {id: 'mes', nombre: 'Mes(es)'}
            ],
            tipos: [],
            tiposV: [
                { id: "Auto", nombre: "Auto" },
                { id: "Camioneta", nombre: "Camioneta" },
                { id: "VAN", nombre: "VAN" },
                { id: "Camión 1.5 TN", nombre: "Camión 1.5 TN" },
                { id: "Camión 3 a 5 TN", nombre: "Camión 3 a 5 TN" },
                {
                    id: "Camión y Bus 10 TN a más",
                    nombre: "Camión y Bus 10 TN a más",
                },
                { id: "Otro", nombre: "Otro" },
            ],
            tipoVId: 0,
            tipoId: 0,
            macroServicioId: 0,
            unidadId: 0,
            id: 0,
            precio: 0,
            token: this.$store.state.token,
            entidad: "mant_servicio",
            listMacroservicios: [],
        };
    },
    methods: {
        async cargarDatos() {
            let me = this;
            await this.getMacroServicios();
            me.id =
                this.$route.params.id == undefined ? 0 : this.$route.params.id;
            document.getElementById("data-error_" + me.entidad).innerHTML = "";
            let tipos = await axios.get("/tiposservicio");
            me.tipos = tipos.data.tipos;

            document.getElementById(
                "select_macro_servicio" + me.entidad
            ).value = "";

            if (me.id != 0) {
                let respuesta = await axios.get("/obtenerservicio/" + me.id);

                if (respuesta.data.estado) {
                    me.accion = "Editar";
                    me.tipoId = respuesta.data.servicio.idCategoriaServicio;
                    me.macroServicioId =
                        respuesta.data.servicio.idMacroServicio;
                    me.tipoVId = respuesta.data.servicio.tipoVehiculo;
                    me.unidadId = respuesta.data.servicio.unidad;
                    document.getElementById("nombre_" + me.entidad).value =
                        respuesta.data.servicio.nombre;
                    document.getElementById("tiempo_" + me.entidad).value =
                        respuesta.data.servicio.tiempoEjecucion;
                    document.getElementById("precio_" + me.entidad).value =
                        respuesta.data.servicio.precio;
                } else {
                    this.$router.replace(me.url);
                }
            } else {
                me.accion = "Registrar";

                document.getElementById("tiempo_" + me.entidad).value = "1";
                document.getElementById("precio_" + me.entidad).value = "";
                document.getElementById("nombre_" + me.entidad).value = "";
                document.getElementById("select_tipo_" + me.entidad).value = "";
                document.getElementById("select_tiempo_" + me.entidad).value =
                    "";

                me.tipoId = "";
                me.macroServicioId = "";
                me.unidadId = "";
                me.tipoVId = 0;
            }
            me.capturarTipo();
        },
        capturarTipo() {
            let me = this;
            let val = document.getElementById(
                "select_tipo_" + me.entidad
            ).value;
            let element = document.getElementById("tipo_ocultar");
            if (val == "otro") {
                element.classList.remove("ocultar");
                document.getElementById("tipo_" + me.entidad).focus();
            } else {
                element.classList.add("ocultar");
                // document.getElementById('select_tipo_'+me.entidad).focus()
            }
        },
        async getMacroServicios() {
            let response = await axios.get(`/servicio/getmacroservicios`);

            this.listMacroservicios = response.data.lista;
        },
        async calcularPrecio() {
            let me = this;
            let tiempo = document.getElementById("tiempo_" + me.entidad).value;
            let unidad = document.getElementById(
                "select_tiempo_" + me.entidad
            ).value;
            let consta = 1;

            let response = await axios({
                method: "post",
                url: "/getprecio",
                data: {
                    _token: me.token,
                },
            });
            if (response.data.estado) {
                me.precio = response.data.precio;
            } else {
                me.precio = 0;
            }

            if (unidad == "hr") {
                consta = 60;
            } else if (unidad == "min") {
                consta = 1;
            } else if (unidad == "sem") {
                consta = 60 * 7;
            } else {
                consta = 30 * 60 * 7;
            }

            let tot = parseFloat((tiempo * consta * me.precio) / 60);

            document.getElementById("precio_" + me.entidad).value = tot;
        },
    },
    activated: async function () {
        let me = this;
        me.isValidSession();

        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
        this.$route.meta.auth = localStorage.getItem("autenticado");

        me.cargarDatos();
    },
    beforeDestroy: function () {
        let me = this;
        var element = document.getElementById("sec_" + me.entidad);
        element.classList.add("hidden");
        // this.$store.state.mostrarModal = false
    },
};
</script>
<style scoped>
.ocultar {
    display: none;
}
select {
    cursor: pointer;
}
.txt-unidad {
    float: right;
    position: relative;
    margin-top: -29px;
    margin-right: 10px;
    cursor: pointer;
    font-style: italic;
}

input[type="radio"] {
    /* remove standard background appearance */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    /* create custom radiobutton appearance */
    display: inline-block;
    width: 15px;
    height: 15px;
    padding: 2px;
    /* background-color only for content */
    background-clip: content-box;
    border: 2px solid #bbbbbb;
    background-color: #e7e6e7;
    border-radius: 50%;
}

/* appearance for checked radiobutton */
input[type="radio"]:checked {
    background-color: #182fd2;
    cursor: pointer;
}
</style>
