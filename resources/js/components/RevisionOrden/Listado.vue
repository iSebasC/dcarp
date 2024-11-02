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
                Revisión de Órdenes de Trabajo
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Revisión de Órdenes de Trabajo</h4>
            </div>
            <div class="card-content collapse show pb-2">
              <div class="card-body card-dashboard pt-1 pb-0">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="row">
                        <div class="col-md-12 col-xs-12">
                          <div class="form-group row">
                            <label
                              :for="'documento_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >DNI/RUC:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="text"
                                name="documento"
                                class="form-control form-control-sm"
                                :id="'documento_' + entidad"
                                @keyup.enter="busquedaOrden"
                                @keypress="soloNumeros($event)"
                                maxlength="11"
                              />
                            </div>

                            <label
                              :for="'cliente_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                text-center
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Cliente:</label
                            >
                            <div class="col-sm-3">
                              <input
                                type="text"
                                name="cliente"
                                class="form-control form-control-sm"
                                :id="'cliente_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>

                            <label
                              :for="'comprobante_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >N° Orden:</label
                            >
                            <div class="col-sm-3">
                              <input
                                type="text"
                                name="comprobante"
                                class="form-control form-control-sm"
                                :id="'comprobante_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>
                          </div>

                          <div class="form-group row">
                            <label
                              :for="'comprobante_cita_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >N° Cita:</label
                            >
                            <div class="col-sm-3">
                              <input
                                type="text"
                                name="comprobante_cita"
                                class="form-control form-control-sm"
                                :id="'comprobante_cita_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>

                            <label
                              :for="'registrado_por_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Registrado Por:</label
                            >
                            <div class="col-sm-3">
                              <input
                                type="text"
                                name="registrado_por"
                                class="form-control form-control-sm"
                                :id="'registrado_por_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>

                            <label
                              :for="'placa_vehiculo_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Placa V.:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="text"
                                name="placa_vehiculo"
                                class="form-control form-control-sm"
                                :id="'placa_vehiculo_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>
                          </div>

                          <div class="form-group row">
                            <label
                              :for="'fecha_i_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >F. Inicio:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="date"
                                name="fecha_i"
                                class="form-control form-control-sm pr-0"
                                :id="'fecha_i_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>

                            <label
                              :for="'fecha_f_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                text-center
                                pr-0
                                mr-0
                                pt-0
                              "
                              >F. Fin:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="date"
                                name="fecha_f"
                                class="form-control form-control-sm pr-0"
                                :id="'fecha_f_' + entidad"
                                @keyup.enter="busquedaOrden"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 pt-xs-1 pl-xs-2">
                      <div class="content-buttons btn-group">
                        <button
                          type="button"
                          class="btn btn-sm btn-info mb-1"
                          title="Buscar"
                          @click="busquedaOrden"
                        >
                          <i class="mdi mdi-magnify icon-size"></i>
                        </button>

                        <!-- <button type="button" @click="excel" class="btn btn-sm btn-success mb-1" 
                                      title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button> -->
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
                            class="custom-select custom-select-sm ml-1 pr-2"
                            :id="'cantidad_' + entidad"
                            title="Registros por Página"
                            @change="busquedaOrden"
                          >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
              <span
                class="text-info pl-2 ml-1 mb-2 col-form-label"
                :id="'loading_' + entidad"
                ><Loader /></span
              >
              <div
                class="table-responsive px-2 d-none"
                :id="'tabla_' + entidad"
              >
                <table class="table table-sm mb-0">
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">N° Orden</th>
                      <th class="text-center">N° Cita</th>
                      <th class="text-center">Placa de Auto</th>
                      <th class="text-center">Situación</th>
                      <!-- <th class="text-right">Total</th> -->
                      <th class="text-center">Verificación CheckList</th>
                      <th class="text-center">Registrado Por</th>
                      <th class="text-center">Registrado El</th>
                      <th class="text-center">Operaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!ordenes.length">
                      <td class="text-left text-danger" colspan="7">
                        <strong
                          >No se Encontraron Resultados en su Búsqueda</strong
                        >
                      </td>
                    </tr>

                    <tr v-for="(p, index) in ordenes" :key="p.id">
                      <td
                        class="text-left"
                        v-text="
                          index + 1 + inicio < 10
                            ? '0' + (index + inicio + 1)
                            : index + inicio + 1
                        "
                      ></td>
                      <td class="text-center" v-text="p.fecha"></td>
                      <td class="text-center">
                        <pre><strong v-text="p.doc+' - '+p.cliente"></strong></pre>
                      </td>
                      <td class="text-center">
                        <pre><strong v-text="p.documento"></strong></pre>
                      </td>
                      <td class="text-center">
                        <pre><strong v-text="p.documentocita"></strong></pre>
                      </td>
                      <td class="text-center" v-text="p.placa"></td>
                      <td class="text-center">
                        <span
                          :class="
                            'badge badge-pill ' +
                            (p.situacion == 'F'
                              ? 'badge-success'
                              : 'badge-danger')
                          "
                          v-text="p.situacion == 'F' ? 'Finalizado' : 'Anulado'"
                        ></span>
                      </td>
                      <!-- <td class="text-right" v-text="p.total"></td> -->
                      <td class="text-left">
                        <p class="small text-center" v-if="p.id_verificacion != null">
                          <span class="text-bold">CheckList Calidad: </span><span :class="'badge badge-pill '+ (p.rptaVerifCheckCalidad=='S'?'badge-success':'badge-danger')" v-text="p.rptaVerifCheckCalidad=='S'?'Si':'No'"></span>
                        </p>
                        <p class="small text-center" v-if="p.id_verificacion != null">
                          <span class="text-bold">CheckList Manejo: </span><span :class="'badge badge-pill '+ (p.rptaVerifCheckManejo=='S'?'badge-success':'badge-danger')" v-text="p.rptaVerifCheckManejo=='S'?'Si':'No'"></span>
                        </p>
                        <p class="small" v-if="p.id_verificacion != null">
                          <span class="text-bold">Observaciones: </span><span v-text="p.obs_checklist"></span>
                        </p>
                        <p class="small text-center" v-if="p.id_verificacion == null">
                        -
                        </p>
                      </td>
                      <!-- <td class="text-center">
                                      <span :class="'badge badge-pill '+ 
                                      (p.situacionEncuesta == 'Sin Aplicar'?'badge-warning':'badge-success')" v-text="p.situacionEncuesta">
                                      </span>
                                    </td> -->
                      <td class="text-center" v-text="p.trabajador"></td>
                      <td class="text-center" v-text="p.fechaR"></td>
                      <td class="text-center">
                        <div class="content-buttons btn-group">
                          <!-- <button @click="llenarCheckList(p.id,p.documento,p.cliente, p.placa)" title="Check List de Calidad" 
                                        class="btn btn-sm btn-info">
                                          <i class="mdi mdi-upload-circle icon-size"></i>
                                        </button>-->

                          <button
                            @click="
                              llenarCheckList(
                                p.id,
                                p.documento,
                                p.cliente,
                                p.placa
                              )
                            "
                            title="Check List de Calidad"
                            class="btn btn-sm btn-danger"
                          >
                            <i class="mdi mdi-upload-circle icon-size"></i>
                          </button>
                          <button
                            @click="
                              llenarCheckListM(
                                p.id,
                                p.documento,
                                p.cliente,
                                p.placa
                              )
                            "
                            title="Check List de Manejo"
                            class="btn btn-sm btn-success"
                          >
                            <i class="mdi mdi-checkbox-marked-circle-auto-outline icon-size"></i>
                          </button>

                          <button
                            @click="
                              verificar(p.id, p.documento, p.cliente, p.placa)
                            "
                            title="Verificar CheckList de Calidad & Manejo"
                            class="btn btn-sm btn-outline-info"
                          >
                            <i class="mdi mdi-book-check-outline icon-size"></i>
                          </button>

                          <!-- <button v-if="p.situacionEncuesta=='Sin Aplicar'" 
                                        @click="llenarEncuestaF(p.id,p.documento,p.cliente, p.placa)" 
                                        title="Llenar Encuesta de Cliente" 
                                        class="btn btn-sm btn-primary">
                                          <i class="la la-project-diagram"></i>
                                        </button> -->

                          <!-- <button @click="verFormato(p.id)" 
                                        title="Imprimir Formatos en PDF" class="btn btn-sm btn-default"><i class="la la-print icon-size"></i></button> -->
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <nav class="pl-2">
                  <ul class="pagination justify-content-right">
                    <li class="page-item">
                      <a
                        class="page-link bg-info text-white"
                        href="javascript:void(0);"
                        aria-label="total"
                        ><strong>TOTAL: </strong><span v-text="total"></span
                      ></a>
                    </li>

                    <li
                      class="page-item"
                      @click="
                        paramInicio < pageActual && paramInicio > 0
                          ? buscarOrden('prev')
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
                      v-for="op in opciones"
                      :key="op.opc"
                      :class="
                        op.opc == pageActual ? 'page-item active' : 'page-item'
                      "
                      @click="op.opc != pageActual ? buscarOrden(op.opc) : ''"
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
                        paramFin > pageActual && paramFin > 0
                          ? buscarOrden('next')
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

    <!-- <CheckList ref="checklist"></CheckList> -->
    <CheckList ref="checklist"></CheckList>
    <CheckListManejo ref="checklistmanejo"></CheckListManejo>
    <!-- <CheckListTaller ref="checklisttaller"></CheckListTaller> -->
    <Encuesta ref="encuesta"></Encuesta>
    <VerificarCheckList ref="verificarChecks"></VerificarCheckList>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import CheckList from "./CheckListCalidad";
import CheckListManejo from "./CheckListManejo";
import VerificarCheckList from "./VerificarCheckList";
// import CheckListTaller from './CheckListTaller'
import Encuesta from "./Encuesta";
import Loader from "../Loader";
export default {
  name: "OrdenTrabajo",
  mixins: [misMixins],
  components: {
    CheckList,
    CheckListManejo,
    // CheckListTaller,
    Encuesta,
    VerificarCheckList,
    Loader
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      ordenes: [],
      total: "",
      inicio: "",
      fin: "",
      paramInicio: "",
      paramFin: "",
      idVenta: "",
      documento: "",
      entidad: "revision_orden",
      bandRender: false,
    };
  },
  computed: {},
  methods: {
    buscarOrden: function (attr) {
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
      me.busquedaOrden();
    },
    async busquedaOrden() {
      let me = this;
      let documento = document.getElementById("documento_" + me.entidad).value;
      let cliente = document.getElementById("cliente_" + me.entidad).value;
      let comprobante = document.getElementById(
        "comprobante_" + me.entidad
      ).value;
      let comprobante_cita = document.getElementById(
        "comprobante_cita_" + me.entidad
      ).value;
      let registrado_por = document.getElementById(
        "registrado_por_" + me.entidad
      ).value;
      let placa_vehiculo = document.getElementById(
        "placa_vehiculo_" + me.entidad
      ).value;

      let fechaI = document.getElementById("fecha_i_" + me.entidad).value;
      let fechaF = document.getElementById("fecha_f_" + me.entidad).value;

      var el = document.getElementById("loading_" + me.entidad);
      el.classList.add("hidden");

      me.filas = document.getElementById("cantidad_" + me.entidad).value;

      let response = await axios({
        method: "post",
        url: "/ordenfinalizado",
        data: {
          documento: documento,
          cliente: cliente,
          comprobante: comprobante,
          cita: comprobante_cita,
          registrado: registrado_por,
          placa: placa_vehiculo,
          fechaI: fechaI,
          fechaF: fechaF,
          filas: me.filas,
          page: me.pageActual,
          _token: this.$store.state.token,
        },
      });

      me.ordenes = response.data.ordenes;
      me.total = response.data.cantidad;
      me.pageActual = response.data.page;
      me.opciones = response.data.paginador;
      me.inicio = response.data.inicio;
      me.fin = response.data.fin;
      me.paramInicio = response.data.paramInicio;
      me.paramFin = response.data.paramFin;

      me.renderTabla(me.entidad);
      // var el2 = document.getElementById('tabla-revision')
      // el2.classList.remove('d-none')

      // alert(filtro)
    },
    async cargarDatos() {
      let me = this;
      me.busquedaOrden();
    },
    excel() {
      let me = this;
      let filtro = document.getElementById("filtro-revision").value;
      let descripcion = document.getElementById("descripcion-revision").value;
      let tipodocumento = document.getElementById("tipo-documento").value;
      let fechaI = document.getElementById("fecha_i_" + me.entidad).value;
      let fechaF = document.getElementById("fecha_f_" + me.entidad).value;

      if (filtro == "") {
        filtro = "null";
      }
      if (descripcion == "") {
        descripcion = "null";
      }
      if (tipodocumento == "") {
        tipodocumento = "null";
      }
      if (fechaI == "") {
        fechaI = "null";
      }
      if (fechaF == "") {
        fechaF = "null";
      }

      // window.open('/venta/excel/'+filtro+'/'+descripcion+'/'+tipodocumento+'/'+fechaI+'/'+fechaF,'_blank')
    },
    llenarCheckList(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.checklist.showModal(id, documento, cliente, placa);
    },
    llenarCheckListM(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.checklistmanejo.showModal(id, documento, cliente, placa);
    },
    // llenarCheckListT (id,documento,cliente, placa) {
    //   let me = this
    //   me.isValidSession()

    //   this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   me.$refs.checklisttaller.showModal(id, documento, cliente, placa)
    // },
    llenarEncuestaF(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.encuesta.showModal(id, documento, cliente, placa);
    },
    verificar(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.verificarChecks.showModal(id, documento, cliente, placa);
    },
    llenarCheckList(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.checklist.showModal(id, documento, cliente, placa);
    },

    verFormato(id) {
      window.open(`/getcheckrevision/${id}`, "_blank");
    },
  },
  created() {
    this.$on("listarOrdenesFinalizadas", function () {
      this.busquedaOrden();
    });

    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this;
    me.isValidSession();

    this.$store.state.mostrarModal = !this.$store.state.mostrarModal; //(me.contModal%2==1?false:true)
    this.$route.meta.auth = localStorage.getItem("autenticado");
    if (!me.bandRender) {
      document.getElementById("fecha_i_" + me.entidad).value =
        me.obtenerFechaActual();
      document.getElementById("fecha_f_" + me.entidad).value =
        me.obtenerFechaActual();

      me.bandRender = true;
    }

    // window.setTimeout( function () {
    me.cargarDatos();
    // }, 1000)
    // me.generarModal(0,'')
  },
  beforeDestroy: function () {
    var element = document.getElementById(`sec_${this.entidad}`);
    element.classList.add("hidden");
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  },
};
</script>
<style scoped>
 .text-bold {
  font-weight: bold;
 }
</style>