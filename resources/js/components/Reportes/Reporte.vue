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
              <li class="breadcrumb-item active">Reporte Mensuales</li>
            </ol>
          </div>
        </div>
        <h4 class="content-header-title mb-0 mt-1">Reportes Mensuales</h4>
         
      </div>
    </div>

    <div class="content-body">
      <!-- Default styling start -->
      <div class="row">
        <div class="col-11">
          <div class="card py-4">
            <!-- <div class="card-header">
              <h4 class="card-title">Reportes Mensuales</h4>
            </div> -->
            <div class="card-content collapse show pb-2">
              <div class="card-body card-dashboard pt-1 pb-0">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-md-11">
                      <div class="row">
                        <label
                          :for="'fecha_i_' + entidad"
                          v-if="select_tipo_reporte != '4' && select_tipo_reporte!='6'"
                          class="
                            col-sm-1
                            m-4px
                            col-form-label
                            text-md-center
                            pr-0
                            mr-0
                            pt-0
                            pb-3
                          "
                          >F. Inicio:</label
                        >
                        <div class="col-sm-2 pb-3">
                          <input
                            type="date"
                            name="fecha_i"
                            v-if="select_tipo_reporte != '4' && select_tipo_reporte!='6'"
                            class="form-control form-control-sm pr-0"
                            :id="'fecha_i_' + entidad"
                          />
                        </div>

                        <label
                          :for="'fecha_f_' + entidad"
                          v-if="select_tipo_reporte!='6'"
                          class="
                            col-sm-1
                            m-4px
                            col-form-label
                            text-md-center
                            pr-0
                            mr-0
                            pt-0
                            pb-3
                          "
                          >F. Fin:</label
                        >
                        <div class="col-sm-2 pb-3">
                          <input
                            type="date"
                            name="fecha_f"
                            v-if="select_tipo_reporte!='6'"
                            class="form-control form-control-sm pr-0"
                            :id="'fecha_f_' + entidad"
                          />
                        </div>

                        <label
                          :for="'tipo_servicio_' + entidad"
                          v-if="select_tipo_reporte=='9'"
                          class="
                            col-sm-1
                            m-4px
                            col-form-label
                            text-md-center
                            pr-0
                            mr-0
                            pt-0
                          "
                          >Tipo Servicio:</label
                        >

                        <div class="col-sm-5" v-if="select_tipo_reporte=='9'">
                          <select
                            class="custom-select custom-select-sm"
                            :id="'tipo_servicio_' + entidad"
                         >
                            <option
                              v-for="(item, index) in tipoServicio"
                              :key="index"
                              :value="item.value"
                              v-text="item.label"
                            ></option>
                          </select>
                        </div>

                        <label
                          :for="'tipo_reporte_' + entidad"
                          class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0"
                          >Tipo Reporte:</label
                        >
                        <div class="col-sm-3">
                          <select
                            class="custom-select custom-select-sm"
                            :id="'tipo_reporte_' + entidad"
                            v-model="select_tipo_reporte"
                          >
                            <option value="" disabled="" selected="">
                              Seleccione
                            </option>
                            <option
                              v-for="(item, index) in arrTipos"
                              :key="index"
                              :value="item.value"
                              v-text="item.label"
                            ></option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-1 pt-xs-1 pl-xs-2">
                      <div class="content-buttons btn-group">
                        <button
                          type="button"
                          class="btn btn-sm btn-success mb-1"
                          title="Generar"
                          @click="exportar()"
                        >
                          <i class="mdi mdi-file-excel icon-size"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "ReporteMensual",
  mixins: [misMixins],
  components: {},
  data() {
    return {
      opciones: [],
      arrTipos: [
        { value: "1", label: "Reporte Detallado de Ventas" },
        { value: "5", label: "Reporte Detallado de Compras" },
        { value: "2", label: "Reporte de Ventas PLE" },
        { value: "3", label: "Reporte de Compras PLE" },
        { value: "6", label: "Reporte ABC" },
        { value: "7", label: "Reporte de Consumo Interno"},
        { value: "8", label: "Reporte CASIS"},
        { value: "9", label: "Reporte GZ"},
        { value: "4", label: "Inventario Valorizado" },
      ],
      tipoServicio: [
        { value: "", label: "Todos" },
        { value: "MP", label: "Mantenimiento Preventivo" },
        { value: "MC", label: "Mantenimiento Correctivo" },
        { value: "L",  label: "Lavado" },
        { value: "PB",  label: "Programa de Bienvenida" },
        { value: "IA",  label: "Instalación de accs" },
        { value: "PD",  label: "PDI" },
        { value: "G",  label: "Garantía" },
        { value: "C",  label: "Campaña" },
        { value: "PP",  label: "Planchado y Pintura" },
        { value: "IS",  label: "Inspección Seminuevo" },
        { value: "TR",  label: "Trabajo repetido" },
        { value: "S",  label: "Siniestro" }
      ],
      arrProducto: [],
      arrAlmacen: [],
      detalles: [],
      token: this.$store.state.token,
      almacenId: 2,
      tipoId: "",
      entidad: "reporte_mensual",
      bandRender: false,
      select_tipo_reporte: "",
    };
  },
  computed: {},
  methods: {
    async exportar() {
      let me = this;

      let tipo = document.getElementById("tipo_reporte_" + me.entidad).value;
    
      if (tipo != "6") {
        let fechaF = document.getElementById("fecha_f_" + me.entidad).value;
        let fechaI =  tipo != "4" ? document.getElementById("fecha_i_" + me.entidad).value: fechaF;
        if (fechaI != "" && fechaF != "") {
          if (tipo != "9") {
            window.open(
              `/reportemes/excel?tipo=${tipo}&fechai=${fechaI}&fechaf=${fechaF}`,
              "_blank"
            );
          } else {
            let tipoServicio = document.getElementById('tipo_servicio_'+me.entidad).value;
            window.open(
              `/reportemes/excel?tipo=${tipo}&fechai=${fechaI}&fechaf=${fechaF}&tipoServ=${tipoServicio}`,
              "_blank"
            );
          }
        }
      } else if (tipo == '6') {
        window.open(
          `/reportemes/excel?tipo=${tipo}`,
          "_blank"
        );
      }
    },
  },
  created() {
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this;
    me.isValidSession();
    this.$route.meta.auth = localStorage.getItem("autenticado");
    document.getElementById("fecha_i_" + me.entidad).value =
      me.obtenerFechaActual();
    document.getElementById("fecha_f_" + me.entidad).value =
      me.obtenerFechaActual();

    // me.generarModal(0,'')
  },
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById("sec_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  },
};
</script>
<style scoped>
select {
  cursor: pointer;
}
</style>