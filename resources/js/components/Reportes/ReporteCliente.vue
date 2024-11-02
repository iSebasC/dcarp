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
              <li class="breadcrumb-item active">
                Reporte de Historial del Cliente
              </li>
            </ol>
          </div>
        </div>
        <h4 class="content-header-title mb-0 mt-1">Reporte de Historial del Cliente</h4>
        
      </div>
    </div>

    <div class="content-body">
      <!-- Default styling start -->
      <div class="row">
        <div class="col-8">
          <div class="card py-4">
            <!-- <div class="card-header">
              <h4 class="card-title">Reporte de Historial del Cliente</h4>
            </div> -->
            <div class="card-content collapse show pb-2">
              <div class="card-body card-dashboard pt-1 pb-0">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="row">
                        <!-- <div class="col-md-5 col-xs-12">
                          <div class="form-group">
                            <label
                              :for="'almacen_' + entidad"
                              class="col-form-label"
                              >Almacén de Salida
                              <span class="text-danger">*</span></label
                            >
                            <select
                              class="custom-select custom-select-sm"
                              v-model="almacenId"
                              name="arrAlmacen"
                              :id="'almacen_' + entidad"
                              @change="exportar()()"
                            >
                              <option
                                v-for="(item, index) in arrAlmacen"
                                :key="index"
                                :value="item.value"
                                v-text="item.label"
                              ></option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-3 col-xs-12">
                          <div class="form-group">
                            <label
                              :for="'tipo_reporte_' + entidad"
                              class="col-form-label"
                              >Tipo de Producto</label
                            >
                            <select
                              class="custom-select custom-select-sm"
                              name="arrTipos"
                              :id="'tipo_reporte_' + entidad"
                              @change="exportar()()"
                            >
                              <option
                                v-for="(item, index) in arrTipos"
                                :key="index"
                                :value="item.value"
                                v-text="item.label"
                              ></option>
                            </select>
                          </div>
                        </div> -->

                        <label
                          :for="'cliente_' + entidad"
                          class="
                            col-sm-2
                            m-4px
                            col-form-label
                            text-md-right
                            pr-0
                            mr-0
                            pt-0
                          "
                          >Cliente:</label
                        >
                        <div class="col-sm-8">
                          <input
                            type="text"
                            placeholder="Documento/Razón Social"
                            class="form-control form-control-sm"
                            name="cliente"
                            :id="'cliente_' + entidad"
                          />
                        </div>
                      </div>

                      <div class="row mt-2">
                        <label
                          :for="'fecha_i_' + entidad"
                          class="
                            col-sm-2
                            m-4px
                            col-form-label
                            text-md-right
                            pr-0
                            mr-0
                            pt-0
                          "
                          >F. Inicio:</label
                        >
                        <div class="col-sm-3">
                          <input
                            type="date"
                            name="fecha_i"
                            class="form-control form-control-sm pr-0"
                            :id="'fecha_i_' + entidad"
                          />
                        </div>

                        <label
                          :for="'fecha_f_' + entidad"
                          class="
                            col-sm-2
                            m-4px
                            col-form-label
                            text-md-center
                            pr-0
                            mr-0
                            pt-0
                          "
                          >F. Fin:</label
                        >
                        <div class="col-sm-3">
                          <input
                            type="date"
                            name="fecha_f"
                            class="form-control form-control-sm pr-0"
                            :id="'fecha_f_' + entidad"
                          />
                        </div>
                      </div>

                      <div class="row mt-2">
                        <label
                          :for="'tipo_reporte_' + entidad"
                          class="
                            col-sm-2
                            m-4px
                            col-form-label
                            text-md-right
                            pr-0
                            mr-0
                            pt-0
                          "
                          >T. Reporte:</label
                        >
                        <div class="col-sm-4">
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
                    <div class="col-md-2 pt-xs-1 pl-xs-2">
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
  name: "ReporteCliente",
  mixins: [misMixins],
  components: {},
  data() {
    return {
      opciones: [],
      arrTipos: [
        { value: "1", label: "Ordenes de Trabajo" },
        { value: "2", label: "Cotizaciones" },
        { value: "3", label: "Post Ventas" },
        { value: "4", label: "Ventas - Vehículos" },
        { value: "5", label: "Clientes - Marketing"}
      ],
      arrProducto: [],
      arrAlmacen: [],
      detalles: [],
      token: this.$store.state.token,
      almacenId: 2,
      tipoId: "",
      entidad: "reporte_cliente",
      bandRender: false,
      select_tipo_reporte: "",
    };
  },
  computed: {},
  methods: {
    async exportar() {
      let me = this;

      let cliente = document.getElementById("cliente_" + me.entidad).value;
      let tipo = document.getElementById("tipo_reporte_" + me.entidad).value;
      let fechaF = document.getElementById("fecha_f_" + me.entidad).value;
      let fechaI = document.getElementById("fecha_i_" + me.entidad).value;

      if (tipo == '5') {
        window.open(
            `/reportecliente/excel?cli=${cliente}&tipo=${tipo}&fechai=${fechaI}&fechaf=${fechaF}`,
            "_blank"
          );
      } else {
        if (/*fechaI != "" && fechaF != "" &&*/ tipo != "" && cliente != "") {
          window.open(
            `/reportecliente/excel?cli=${cliente}&tipo=${tipo}&fechai=${fechaI}&fechaf=${fechaF}`,
            "_blank"
          );
        } else {
        }
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