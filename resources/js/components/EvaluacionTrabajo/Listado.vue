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
              <li class="breadcrumb-item active">Evaluación de Trabajo</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <!-- <div
          class="btn-group float-md-right"
          role="group"
          aria-label="Agregar Orden de Trabajo"
        >
          <router-link
            tag="a"
            to="/ordentrabajo/crear"
            class="
              btn btn-info
              round
              dropdown-menu-right
              box-shadow-2
              px-2
              mb-1
              btn-sm
            "
            type="button"
          >
            <i class="mdi mdi-plus icon-size"></i> Agregar Orden de
            Trabajo</router-link
          >
        </div> -->
      </div>
    </div>
    <div class="content-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Evaluación de Órdenes de Trabajo</h4>
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
                                text-md-center
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

                          <!-- <div class="form-group row"> -->
                          <!-- <label
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
                            </div> -->

                          <!-- <label
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
                            </div> -->

                          <!-- </div> -->

                          <div class="form-group row">
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

                            <label
                              :for="'fecha_i_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                text-md-center
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
                                text-md-center
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

                            <!-- <label
                              :for="'es_facturado_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Facturado:
                            </label>
                            <div class="col-sm-2">
                              <select
                                class="custom-select custom-select-sm"
                                :id="'es_facturado_' + entidad"
                                @change="busquedaOrden"
                              >
                                <option value="" disabled="" selected="">Tipo de Documento</option>
                                <option value="">Todos</option>
                                <option
                                  v-for="f in tiposFacturado"
                                  :key="f.id"
                                  :value="f.id"
                                >
                                  {{ f.nombre }}
                                </option>
                              </select>
                            </div> -->
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 pt-xs-1 pl-xs-2">
                      <div class="content-buttons btn-group">
                        <button
                          type="button"
                          class="btn btn-sm btn-icon btn-md btn-info mb-1"
                          title="Buscar"
                          @click="busquedaOrden"
                        >
                          <i class="mdi mdi-magnify"></i>
                        </button>
                        <button
                          type="button"
                          @click="excel"
                          class="btn btn-sm btn-success mb-1"
                          title="Exportar a Excel"
                        >
                          <i class="mdi mdi-file-excel icon-size"></i>
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
                            class="custom-select ml-1 custom-select-sm pr-2"
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
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-center">Fecha</th>
                      <!-- <th class="text-center">Doc. Cliente</th> -->
                      <th class="text-center">Cliente</th>
                      <th class="text-center">N° Orden</th>
                      <!-- <th class="text-center">N° Cita</th> -->
                      <th class="text-center">Placa de Auto</th>
                      <th class="text-center">Situación</th>
                      <!-- <th class="text-center">¿Facturado?</th> -->
                      <th class="text-center">Asignado A</th>
                      <th class="text-center">Registrado Por</th>
                      <!-- <th class="text-center">Actualizado El</th> -->
                      <th class="text-center">Operaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!ordenes.length">
                      <td class="text-left text-danger" colspan="10">
                        <strong
                          >No se Encontraron Resultados en su Búsqueda</strong
                        >
                      </td>
                    </tr>

                    <tr
                      v-for="(p, index) in ordenes"
                      :key="p.id"
                      :class="
                        p.situacion == 'F'
                          ? 'table-success'
                          : p.situacion == 'A'
                          ? 'table-danger'
                          : p.situacion == 'V'
                          ? 'table-warning'
                          : ''
                      "
                    >
                      <td
                        class="text-left"
                        v-text="
                          index + 1 + inicio < 10
                            ? '0' + (index + inicio + 1)
                            : index + inicio + 1
                        "
                      ></td>
                      <td class="text-center" v-text="p.fecha"></td>
                      <!-- <td class="text-center">
                        <strong v-text="p.doc"></strong>
                      </td> -->
                      <td class="text-justify font-bold">
                        <p class="w-100" v-text="p.doc + ' ' + p.cliente"></p>
                      </td>
                      <td class="text-center">
                        <strong
                          class="badge badge-pill badge-warning"
                          v-text="p.documento"
                        ></strong>
                      </td>
                      <!-- <td class="text-center">
                        <strong
                          class="badge badge-pill badge-link"
                          v-text="p.documentocita"
                        ></strong>
                      </td> -->
                      <td class="text-center" v-text="p.placa"></td>
                      <td class="text-center">
                        <span
                          class="badge badge-pill badge-primary"
                          v-text="
                            p.situacion == 'V'
                              ? 'Pendiente'
                              : p.situacion == 'A'
                              ? 'Anulado'
                              : p.situacion == 'I'
                              ? 'Iniciado'
                              : 'Finalizado'
                          "
                        ></span>
                      </td>
                      <!-- <td class="text-center">
                        <span
                          :class="
                            'badge badge-pill ' +
                            (p.situacionFacturado == 'N'
                              ? 'badge-danger'
                              : 'badge-success')
                          "
                          v-text="p.situacionFacturado == 'N' ? 'NO' : 'SI'"
                        ></span>
                      </td> -->
                      <td class="text-center" v-text="p.asignadoA"></td>
                      <td class="text-center" v-text="p.trabajador"></td>
                      <!-- <td class="text-center" v-text="p.fechaR"></td> -->
                      <td class="text-center">
                        <div class="content-buttons btn-group">
                          <button
                            @click="verDetalles(p)"
                            title="Ver Detalles"
                            class="btn btn-sm btn-warning"
                          >
                            <i class="mdi mdi-folder-open-outline icon-size"></i>
                          </button>

                          <button
                            @click="gestAdjuntos(p)"
                            v-if="p.situacion == 'I'"
                            title="Adjuntos & Observaciones"
                            class="btn btn-sm btn-danger"
                          >
                            <i class="mdi mdi-folder-table icon-size"></i>
                          </button>

                          <button
                            @click="verAdjuntos(p)"
                            v-if="p.situacion == 'F'"
                            title="Ver Adjuntos & Observaciones"
                            class="btn btn-sm btn-info"
                          >
                            <i class="mdi mdi-folder-play icon-size"></i>
                          </button>

                          <button
                            v-if="p.situacion == 'V'"
                            @click="iniciarTrabajos(p)"
                            title="Iniciar Trabajos"
                            class="btn btn-sm btn-info"
                          >
                            <i class="mdi mdi-play icon-size"></i>
                          </button>

                          <button
                            title="Gestionar Incidencias"
                            class="btn btn-sm btn-primary"
                            @click="gestionarIncidencias(p)"
                          >
                            <i class="mdi mdi-account-clock-outline icon-size"></i>
                          </button>

                          <button
                            @click="finalizarTrabajos(p)"
                            v-if="p.situacion != 'F' && p.situacion == 'I'"
                            title="Finalizar Trabajos"
                            class="btn btn-sm btn-danger"
                          >
                            <i class="mdi mdi-file-tree icon-size"></i>
                          </button>

                          <!-- <button
                            v-if="p.situacion == 'V'"
                            @click="anular(p.id, p.documento, p.cliente)"
                            title="Anular Orden de Trabajo"
                            class="btn btn-sm btn-danger"
                          >
                            <i class="mdi mdi-delete-outline-alt icon-size"></i>
                          </button> -->
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
    <Detalles ref="detalles" :documento="this.documento"></Detalles>
    <Incidencia ref="incidencia" :documento="this.documento"></Incidencia>
    <ModalFotos ref="modalfotos"></ModalFotos>
    <VerTrabajo ref="modaltrabajos"></VerTrabajo>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import Detalles from "./Detalles";
import Incidencia from "./Incidencias";
import ModalFotos from "./GaleriaImagenes";
import VerTrabajo from "./VerTrabajo";
import Loader from "../Loader";
// import CheckList from '../RevisionOrden/CheckListCalidad'
// import CheckListManejo from "../RevisionOrden/CheckListManejo";
// import CheckListTaller from "../RevisionOrden/CheckListTaller";

export default {
  name: "EvOrdenTrabajo",
  mixins: [misMixins],
  components: {
    Detalles,
    Incidencia,
    ModalFotos,
    VerTrabajo,
    Loader
    // Asignacion,
    // CheckListManejo,
    // CheckListTaller,
    // CheckList
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      ordenes: [],
      tiposFacturado: [
        { id: "N", nombre: "No" },
        { id: "P", nombre: "Si" },
      ],
      total: "",
      inicio: "",
      fin: "",
      paramInicio: "",
      paramFin: "",
      idVenta: "",
      documento: "",
      entidad: "orden",
      bandRender: false,
      documento: "",
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
    gestAdjuntos(item) {
      let me = this;
      me.isValidSession();
      //   me.idfoto = id
      //   console.log(me.idfoto)
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.modalfotos.showModal(item.id, item.documento, item.placa);
    },
    verAdjuntos(item) {
      let me = this;
      me.isValidSession();
      //   me.idfoto = id
      //   console.log(me.idfoto)
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.modaltrabajos.showModal(item.id, item.documento, item.placa);
    },
    async busquedaOrden() {
      let me = this;
      let documento = document.getElementById("documento_" + me.entidad).value;
      let cliente = document.getElementById("cliente_" + me.entidad).value;
      let comprobante = document.getElementById(
        "comprobante_" + me.entidad
      ).value;
      //   let comprobante_cita = document.getElementById(
      //     "comprobante_cita_" + me.entidad
      //   ).value;
      //   let registrado_por = document.getElementById(
      //     "registrado_por_" + me.entidad
      //   ).value;
      let placa_vehiculo = document.getElementById(
        "placa_vehiculo_" + me.entidad
      ).value;

      let fechaI = document.getElementById("fecha_i_" + me.entidad).value;
      let fechaF = document.getElementById("fecha_f_" + me.entidad).value;
      //   let isFacturado = document.getElementById(
      //     `es_facturado_${me.entidad}`
      //   ).value;
      var el = document.getElementById("loading_" + me.entidad);
      el.classList.add("hidden");

      me.filas = document.getElementById("cantidad_" + me.entidad).value;

      let response = await axios({
        method: "post",
        url: "/orden",
        data: {
          documento: documento,
          cliente: cliente,
          comprobante: comprobante,
          cita: "",
          trabajador: "",
          placa: placa_vehiculo,
          fechaI: fechaI,
          fechaF: fechaF,
          isFacturado: "",
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
      // var el2 = document.getElementById('tabla-orden')
      // el2.classList.remove('d-none')

      // alert(filtro)
    },
    async cargarDatos() {
      let me = this;
      me.busquedaOrden();
    },
    excel() {
      let me = this;
      let documento = document.getElementById("documento_" + me.entidad).value;
      let cliente = document.getElementById("cliente_" + me.entidad).value;
      let comprobante = document.getElementById(
        "comprobante_" + me.entidad
      ).value;
      let placa_vehiculo = document.getElementById(
        "placa_vehiculo_" + me.entidad
      ).value;

      let fechaI = document.getElementById("fecha_i_" + me.entidad).value;
      let fechaF = document.getElementById("fecha_f_" + me.entidad).value;

      // let filtro = document.getElementById('filtro-orden').value
      // let descripcion = document.getElementById('descripcion-orden').value
      // let tipodocumento = document.getElementById('tipo-documento').value
      // let fechaI = document.getElementById('fechaInicial-orden').value
      // let fechaF = document.getElementById('fecha_f_'+me.entidad).value

      // if (filtro =='') {filtro = 'null'}
      // if (descripcion =='') {descripcion = 'null'}
      // if (tipodocumento =='') {tipodocumento = 'null'}
      // if (fechaI =='') {fechaI = 'null'}
      // if (fechaF =='') {fechaF = 'null'}

      window.open(
        `/orden/productividad/excel?doc=${documento}&cli=${cliente}&comp=${comprobante}&pl=${placa_vehiculo}&fi=${fechaI}&ff=${fechaF}`,
        "_blank"
      );
    },
    verCotizaciones(idventa, documento, cliente, idcliente, placa) {
      let me = this;
      me.isValidSession();

      me.idVenta = idventa;
      me.documento = documento;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
      //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
      // this.$emit('abrirmodal',me.idModal)
      me.$refs.cotizacion.showModal(idventa, cliente, idcliente, placa);

      // me.mostrarModal = true
      // alert(me.idModal)
      // if (this.$store.state.mostrarModal) {
      // }
      // gestAdjuntos(id)
    },
    verAsignacion(idventa, documento, cliente, idcliente, placa) {
      let me = this;
      me.isValidSession();

      me.idVenta = idventa;
      me.documento = documento;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
      //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
      // this.$emit('abrirmodal',me.idModal)
      me.$refs.asignacion.showModal(idventa, cliente, idcliente, placa);

      // me.mostrarModal = true
      // alert(me.idModal)
      // if (this.$store.state.mostrarModal) {
      // }
      // gestAdjuntos(id)
    },
    verCheckList(id) {
      window.open("/checklistinv/" + id, "_blank");
    },
    verCheckListCalidad(id) {
      window.open("/checklistcalidadblank/" + id, "_blank");
    },
    verReporteCotizaciones(id) {
      window.open("/getpdfordencotizacion/" + id, "_blank");
    },
    llenarCheckListT(id, documento, cliente, placa) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.checklisttaller.showModal(id, documento, cliente, placa);
    },
    verDetalles(item) {
      this.documento = item.documento;
      this.$refs.detalles.showModal(item.id);
    },
    gestionarIncidencias(item) {
      this.documento = item.documento;
      this.$refs.incidencia.showModal(item.id);
    },
    // llenarCheckList (id,documento,cliente, placa) {
    //   let me = this
    //   me.isValidSession()

    //   this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   me.$refs.checklist.showModal(id, documento, cliente, placa)
    // },
    // llenarCheckListM (id,documento,cliente, placa) {
    //   let me = this
    //   me.isValidSession()

    //   this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   me.$refs.checklistmanejo.showModal(id, documento, cliente, placa)
    // },
    iniciarTrabajos(item) {
      let me = this;
      let token = this.$store.state.token;
      swal({
        title: "Confirmar Operación",
        html:
          "¿Seguro que desea Iniciar los trabajos de la Orden de Trabajo <strong>" +
          item.documento +
          " del Cliente " +
          item.cliente +
          "</strong> ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sí, Confirmar",
        cancelButtonText: "No, Cancelar",
        allowOutsideClick: false,
        closeOnConfirm: false,
        closeOnCancel: false,
      }).then(async function (result) {
        if (result.value) {
          let response = await axios({
            method: "post",
            url: "/actualizaorden",
            data: {
              id: item.id,
              estado: "I",
              _token: token,
            },
          });

          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: "¡Exito!",
                text: "Orden de Trabajo Iniciada con Éxito.",
                type: "success",
              }).then(function () {
                me.busquedaOrden();
              });
            }, 500);
          } else {
            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
              cadena_errors += val + ", ";
            });
            swal("¡Cancelado!", cadena_errors.slice(0, -2), "error");
          }
        }
      });
    },
    finalizarTrabajos(item) {
      let me = this;
      let token = this.$store.state.token;
      swal({
        title: "Confirmar Operación",
        html:
          "¿Seguro que desea Finalizar los trabajos de la Orden de Trabajo <strong>" +
          item.documento +
          " del Cliente " +
          item.cliente +
          "</strong> ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sí, Confirmar",
        cancelButtonText: "No, Cancelar",
        allowOutsideClick: false,
        closeOnConfirm: false,
        closeOnCancel: false,
      }).then(async function (result) {
        if (result.value) {
          let response = await axios({
            method: "post",
            url: "/actualizaorden",
            data: {
              id: item.id,
              estado: "F",
              _token: token,
            },
          });

          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: "¡Exito!",
                text: "Orden de Trabajo Finalizada con Éxito.",
                type: "success",
              }).then(function () {
                me.busquedaOrden();
              });
            }, 500);
          } else {
            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
              cadena_errors += val + ", ";
            });
            swal("¡Cancelado!", cadena_errors.slice(0, -2), "error");
          }
        }
      });
    },
    anular(id, documento, cliente) {
      let me = this;
      let token = this.$store.state.token;
      swal({
        title: "Confirmar Operación",
        html:
          "¿Seguro que desea Eliminar la Orden de Trabajo <strong>" +
          documento +
          " del Cliente " +
          cliente +
          "</strong> ?",
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
            url: "/eliminarorden",
            data: {
              id: id,
              _token: token,
            },
          });

          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: "¡Eliminado!",
                text: "Cotización Eliminada con Éxito.",
                type: "success",
              }).then(function () {
                me.busquedaOrden();
              });
            }, 500);
          } else {
            swal("¡Cancelado!", "Operación Cancelada", "error");
          }
        } 
        // else {
        //   swal("¡Cancelado!", "Operación Cancelada", "error");
        // }
      });
    },
    async anularNota(id, nombre) {
      let me = this;
      let token = this.$store.state.token;
      swal({
        title: "Confirmar Operación",
        html:
          "¿Seguro que desea Anular El Comprobante <strong>" +
          nombre +
          "</strong> ?",
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
            url: "/anularnota",
            data: {
              id: id,
              _token: token,
            },
          });

          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: "¡Anulado!",
                text: "Comprobante Anulado con Éxito.",
                type: "success",
              }).then(function () {
                me.cargarDatos();
              });
            }, 500);
          } else {
            swal("¡Cancelado!", "Operación Cancelada", "error");
          }
        } 
        // else {
        //   swal("¡Cancelado!", "Operación Cancelada", "error");
        // }
      });
    },
  },
  created() {},
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
    // me.gestAdjuntos(0,'')
  },
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById(`sec_${me.entidad}`);
    element.classList.add("hidden");
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  },
};
</script>