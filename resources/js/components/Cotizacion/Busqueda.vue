<template>
  <div
    class="modal fade"
    id="modalBusqueda"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalBusquedaLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalBusquedaLabel">
            Carga Laboral de Personal
          </h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-7 col-xs-12">
              <div class="form-group">
                <label for="personal_bq"
                  >Tipo de Usuario/Personal
                  <span class="text-danger">*</span></label
                >
                <input
                  v-focus
                  type="text"
                  id="personal_bq"
                  placeholder="Presione Enter para Buscar"
                  class="form-control form-control-sm"
                  name="personal_bq"
                  maxlength="255"
                  @keyup.enter="cargarPersonal"
                />
              </div>
            </div>
            <div class="col-md-3 mt-2">
              <button
                @click="cargarPersonal"
                class="btn btn-sm btn-info"
                type="button"
                style="margin-top: 10px"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Buscar"
              >
                <i class="mdi mdi-magnify icon-size"></i>
              </button>
            </div>
          </div>

          <div class="row">
            <div
              class="table-responsive px-1 overflow-auto"
              id="tabla-buscar"
              style="height: 250px"
            >
              <table class="table table-striped table-sm mb-0">
                <thead>
                  <tr class="bg-warning text-white">
                    <th class="text-left">#</th>
                    <th class="text-center">Personal</th>
                    <!-- <th class="text-center">Tipo </th> -->
                    <th class="text-center">Orden de Trabajo</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Duración Estimada</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="!personal.length">
                    <td class="text-left text-danger" colspan="6">
                      <strong
                        >No se Encontraron Resultados en su Búsqueda</strong
                      >
                    </td>
                  </tr>

                  <tr v-for="(p, index) in personal" :key="index">
                    <td
                      class="text-left"
                      v-text="index + 1 < 10 ? '0' + (index + 1) : index + 1"
                    ></td>
                    <td class="text-center" v-text="p.personal"></td>
                    <!-- <td class="text-center"><strong v-text="p.tipo"></strong></td> -->
                    <td class="text-center" v-text="p.servicio"></td>
                    <td
                      class="text-center"
                      v-text="p.fecha == null ? 'No Asignado' : p.fecha"
                    ></td>
                    <td class="text-center" v-text="p.tiempo"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger btn-sm"
            data-dismiss="modal"
          >
            <i class="mdi mdi-close icon-size"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "DetallesCot",
  mixins: [misMixins],
  data() {
    return {
      personal: [],
      token: this.$store.state.token,
    };
  },
  methods: {
    async showModal() {
      $("#modalBusqueda").css("z-index", "-1");
      let me = this;
      // me.cargarPersonal()
      $("#modalBusqueda").modal({ backdrop: "static", show: true });
      $("#modalBusqueda").css("z-index", "1500");
    },
    async cargarPersonal() {
      let me = this;
      let busq = document.getElementById("personal_bq").value;
      let response = await axios({
        method: "post",
        url: "/getbusqueda",
        data: {
          busqueda: busq,
          _token: me.token,
        },
      });
      me.personal = response.data.personas;
    },
  },
  created() {
    let me = this;
    me.personal = [];
  },
  activated: function () {
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    me.personal = [];
    // this.$store.state.mostrarModal = false
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal
  },
};
</script>
<style scoped>
.modal-header {
  border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
  border-top: 1px solid #f2f2f2;
}
thead {
  position: sticky;
  top: 0;
}
tfoot {
  position: sticky;
  bottom: 0;
}

input[type="checkbox"] {
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
  border-radius: 30%;
  vertical-align: middle;
  position: relative;
  top: -2px;
}

/* appearance for checked radiobutton */
input[type="checkbox"]:checked {
  background-color: teal;
  cursor: pointer;
}
</style>