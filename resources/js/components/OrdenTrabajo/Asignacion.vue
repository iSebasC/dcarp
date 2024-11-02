<template>
  <div
    class="modal fade"
    id="modalAsignacion"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalAsignacionLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <form
          class="form"
          id="formEnv2"
          method="POST"
          action="/guardarasignacion"
        >
          <div class="modal-header">
            <h5 class="modal-title" id="modalAsignacionLabel">
              Detalles de Orden:
              <strong v-text="this.$attrs.documento"></strong>
            </h5>
            <button
              type="button"
              class="close"
              @click="cerrarModal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-5 col-xs-12">
                <div class="row">
                  <input type="hidden" :value="id" name="id" />
                  <input type="hidden" :value="clienteId" name="clienteId" />
                  <input
                    type="hidden"
                    :value="listDetalles"
                    name="listDetalles"
                  />
                  <div class="col-md-8 col-xs-12">
                    <div class="form-group">
                      <label for="cliente"
                        >Cliente <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        id="cliente"
                        :value="cliente"
                        class="form-control form-control-sm"
                        name="cliente"
                        readonly=""
                      />
                    </div>
                  </div>

                  <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                      <label for="placa"
                        >Placa <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        id="placa"
                        :value="placa"
                        class="form-control form-control-sm"
                        name="placa"
                        readonly=""
                      />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-7 col-xs-12">
                    <div class="form-group">
                      <label for="asignadoA"
                        >Asignar a <span class="text-danger">*</span></label
                      >
                      <vue-typeahead-bootstrap
                        id="asignadoA"
                        :ieCloseFix="false"
                        inputClass="form-control-sm"
                        v-model="queryT"
                        :data="trabajadores"
                        :serializer="(s) => s.personal"
                        @hit="selectTrabajador = $event"
                        @input="getTrabajadores"
                        @keyup.delete="eliminarTrabajador"
                        placeholder="Indique Personal..."
                      />
                    </div>
                    <input
                      type="hidden"
                      name="trabajadorId"
                      id="trabajadorId"
                      :value="trabajadorId"
                    />
                  </div>
                </div>
              </div>

              <div class="col-md-7 col-xs-12">
                <div class="row pl-auto-x">
                  <div class="col-md-8 col-xs-12">
                    <div class="form-group">
                      <label for="personal_bq"
                        >Tipo de Usuario/Personal
                        <span class="text-danger">*</span></label
                      >
                      <input
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
                  <div class="col-md-2 my-4">
                    <button
                      @click="cargarPersonal"
                      class="btn btn-sm btn-info"
                      type="button"
                      style="margin-top: 5px"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="Buscar"
                    >
                      <i class="mdi mdi-magnify icon-size"></i>
                    </button>
                  </div>
                </div>

                <div class="row mx-0 px-0">
                  <div class="col-md-12 col-xs-12">
                    <div
                      class="table-responsive"
                      id="tabla-buscar"
                      style="height: 150px; overflow-y: scroll"
                    >
                      <table
                        class="table table-striped table-sm table-hover mb-0"
                      >
                        <thead>
                          <tr style="background: #062fa0; border-radius: 5px">
                            <!-- <th class="text-left" style="color:#fff;">#</th> -->
                            <th class="text-center" style="color: #fff">
                              Personal
                            </th>
                            <!-- <th class="text-center" style="color:#fff;">Tipo </th> -->
                            <th class="text-center" style="color: #fff">
                              Descripción
                            </th>
                            <th class="text-center" style="color: #fff">
                              Fecha
                            </th>
                            <th class="text-center" style="color: #fff">
                              Duración
                            </th>
                          </tr>
                        </thead>
                        <tbody style="height: 150px">
                          <tr v-if="!personal.length">
                            <td class="text-left text-danger" colspan="6">
                              <strong
                                >No se Encontraron Resultados en su
                                Búsqueda</strong
                              >
                            </td>
                          </tr>

                          <tr v-for="(p, index) in personal" :key="index">
                            <!-- <td class="text-left" v-text="((index+1)<10?'0'+(index+1):(index+1))" ></td> -->
                            <td class="text-center">
                              <small v-text="p.personal"></small>
                            </td>
                            <!-- <td class="text-center"><small><strong v-text="p.tipo"></strong></small></td> -->
                            <td class="text-center">
                              <p style="text-align: justify">
                                <small
                                  ><strong>Orden: </strong
                                  ><span v-text="p.servicio"></span>
                                </small>
                              </p>
                            </td>
                            <td class="text-center">
                              <small
                                ><strong :class="p.fecha == null ?'text-danger': 'text-info'"
                                  v-text="
                                    p.fecha == null ? 'No Asignado' : p.fecha
                                  "
                                ></strong
                              ></small>
                            </td>
                            <td class="text-center">
                              <small v-text="p.tiempo + ' min.'"></small>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8 col-xs-12 mx-auto">
                <div
                  class="table-responsive px-1"
                  style="height:390px;margin 2rem auto; overflow-x:hidden;"
                  id="tabla-orden"
                >
                  <small
                    ><strong
                      >Detalle de Servicios
                      <span class="text-danger">*</span></strong
                    ></small
                  >
                  <table class="table table-hover" style="">
                    <thead style="background: #081331; border-radius: 5px">
                      <tr>
                        <th class="text-white">#</th>
                        <th class="text-white text-center">Servicio</th>
                        <th class="text-white text-center">Duración</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="" v-if="!arrDetalles.length">
                        <th colspan="3" class="text-danger">
                          No se Encontraron Resultados en su Búsqueda
                        </th>
                      </tr>
                      <tr v-for="(p, index) in arrDetalles" :key="index">
                        <input
                          type="hidden"
                          :name="'detalleId' + p.detalles.id"
                          :value="p.detalles.id"
                        />
                        <th
                          v-text="
                            index + 1 < 10 ? '0' + (index + 1) : index + 1
                          "
                        ></th>
                        <td class="text-justify">
                          <p v-text="p.detalles.descripcion"></p>
                        </td>
                        <td class="text-center">
                          <span
                            class="badge badge-pill badge-info"
                            v-text="p.detalles.tiempoEstimado + ' min.'"
                          ></span>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- <div
                    class="row"
                    style="background: #081331; border-radius: 5px"
                  >
                    <div
                      class="col-md-1 col-xs-12 text-center"
                      style="padding-top: 0.5rem; padding-top: 0.5rem"
                    >
                      <h4 style="color: #fff">#</h4>
                    </div>
                    <div
                      class="col-md-5 col-xs-12 text-center"
                      style="padding-top: 0.5rem; padding-top: 0.5rem"
                    >
                      <h4 style="color: #fff">Servicio</h4>
                    </div>
                    <div
                      class="col-md-3 col-xs-12 text-center"
                      style="padding-top: 0.5rem; padding-top: 0.5rem"
                    >
                      <h4 style="color: #fff">Personal</h4>
                    </div>
                    <div
                      class="col-md-4 col-xs-12 text-center"
                      style="padding-top: 0.5rem; padding-top: 0.5rem"
                    >
                      <h4 style="color: #fff">Fecha/Hora de Inicio</h4>
                    </div>
                    <div
                      class="col-md-2 col-xs-12 text-center"
                      style="padding-top: 0.5rem; padding-top: 0.5rem"
                    >
                      <h4 style="color: #fff">Duración</h4>
                    </div>
                  </div>

                  <div v-if="!arrDetalles.length">
                    <div class="col-md-12 col-xs-12 text-left text-danger">
                      <strong
                        >No se Encontraron Resultados en su Búsqueda</strong
                      >
                    </div>
                  </div>

                  <div
                    v-for="(p, index) in arrDetalles"
                    :key="index"
                    class="row"
                  >
                    <div
                      class="col-md-1 col-xs-12 text-center"
                      style="padding-top: 1rem"
                    >
                      <h6
                        v-text="index + 1 < 10 ? '0' + (index + 1) : index + 1"
                      ></h6>
                    </div>

                    <div
                      class="col-md-5 col-xs-12 text-center"
                      style="padding-top: 1rem"
                    >
                      <p style="text-align: center">
                        <mark v-text="p.detalles.descripcion"></mark>
                      </p>
                    </div>

                    <div class="col-md-3 col-xs-12" style="padding-top: 1rem">
                      <vue-bootstrap-typeahead
                        v-model="buscarTrabajador"
                        placeholder="Indique Personal..."
                        :serializer="(s) => s.personal"
                        size="sm"
                        :data="trabajadores"
                        @hit="
                          guardarSeleccion($event, p.personal, p.listPersonal)
                        "
                        :id="'select-vue' + p.id"
                      />

                      <div class="my-0">
                        <a
                          v-for="f in p.personal"
                          class="text-muted ml-0 mr-1"
                          :key="f.id"
                        >
                          <span class="text-info"
                            ><strong v-text="f.trabajador"></strong
                          ></span>
                          <a
                            href="javascript:void(0)"
                            class="text-danger"
                            @click="
                              eliminarPersonal(f.id, p.personal, p.listPersonal)
                            "
                            ><strong>x</strong></a
                          >

                          <input
                            type="hidden"
                            :name="'usuarioId' + p.detalles.id + '_' + f.id"
                            :id="'usuarioId' + p.detalles.id + '_' + f.id"
                            :value="f.id"
                          /><br />
                        </a>
                      </div>

                      <input
                        type="hidden"
                        :name="'listPersonal' + p.detalles.id"
                        :id="'listPersonal' + p.detalles.id"
                        :value="p.listPersonal.join(',')"
                      />
                    </div>
                    <input
                      type="hidden"
                      :name="'detalleId' + p.detalles.id"
                      :value="p.detalles.id"
                    />

                    <div
                      class="col-md-4 col-xs-12 text-center"
                      style="padding-top: 15px"
                    >
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <input
                              type="date"
                              :id="'fecha' + p.detalles.id"
                              class="form-control text-center form-control-sm"
                              :name="'fecha' + p.detalles.id"
                              :value="p.detalles.fecha"
                            />
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input
                              type="time"
                              :id="'hora' + p.detalles.id"
                              class="form-control text-center form-control-sm"
                              :name="'hora' + p.detalles.id"
                              :value="
                                p.detalles.hora == null
                                  ? horaActual
                                  : p.detalles.hora
                              "
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      class="col-md-2 col-xs-12 text-center"
                      style="padding-top: 7px"
                    >
                      <div class="row mt-1">
                        <div class="col-md-7">
                          <p>
                            <span
                              v-text="p.detalles.tiempoEstimado + ' min.'"
                            ></span>
                            <span v-if="p.detalles.enProceso == 1">
                              <Cronometro
                                :ref="`cronometro${p.detalles.id}`"
                              ></Cronometro>
                            </span>
                          </p>
                        </div>

                        <div class="col-md-1">
                          <a
                            :class="
                              p.detalles.enProceso == 0
                                ? 'text-danger'
                                : 'text-success'
                            "
                            title="Empezar Trabajo"
                            href="javascript:void(0);"
                            :id="'btn' + p.detalles.id"
                            @click="getTemporizador(p.detalles.id)"
                          >
                            <i
                              :id="'icon' + p.detalles.id"
                              :class="
                                p.detalles.enProceso == 0
                                  ? 'la la-play-circle'
                                  : 'la la-pause-circle'
                              "
                              style="font-size: 28px; margin-top: -5px"
                            ></i>
                          </a>
                        </div>

                        <div class="col-md-1" v-if="p.detalles.enProceso == 1">
                          <a
                            class="text-danger"
                            title="En Espera"
                            href="javascript:void(0);"
                            :id="'btnEspera' + p.detalles.id"
                            @click="getTemporizador02(p.detalles.id)"
                          >
                            <i
                              :id="'iconEspera' + p.detalles.id"
                              class="la la-stop-circle"
                              style="font-size: 28px; margin-top: -5px"
                            ></i>
                          </a>
                        </div>
                        <div class="col-md-2">
                          <div :id="'msje' + p.detalles.id"></div>
                        </div>
                      </div>
                    </div>
                  </div> -->
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12" id="data-error2"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="
                enviarFormModalOrden(
                  'modalAsignacion',
                  'formEnv2',
                  'data-error2'
                )
              "
              class="btn btn-sm btn-success"
              id="btnEnvioG"
            >
              <i class="mdi mdi-check-bold icon-size"></i> Guardar
            </button>

            <button
              type="button"
              class="btn btn-danger btn-sm"
              @click="cerrarModal"
            >
              <i class="mdi mdi-close icon-size"></i> Cerrar
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- <Busqueda ref="busqueda"></Busqueda> -->
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import { _ } from "vue-underscore";
import Cronometro from "./Cronometro";

const API_URL = "/trabajadores/:query";
export default {
  name: "DetallesAsignacion",
  mixins: [misMixins],
  components: {
    Cronometro,
    // Busqueda
  },
  data() {
    return {
      id: this.$attrs.idguia,
      documento: this.$attrs.documento,
      token: this.$store.state.token,
      arrDetalles: [],
      listDetalles: "",
      trabajadorId: 0,
      placa: "",
      cliente: "",
      clienteId: 0,
      orden: null,
      trabajadores: [],
      buscarTrabajador: "",
      trabajadorSeleccionado: null,
      personal: [],
      horaActual: this.obtenerHoraActual(),
      speed: 1000,
      queryT: "",
      selectTrabajador: null,
    };
  },
  watch: {
    queryT() {},
    selectTrabajador: function () {
      // console.log("Entre---");
      let me = this;
      if (me.selectTrabajador != null) {
        me.trabajadorId = me.selectTrabajador.id;
      } else {
        me.trabajadorId = 0;
      }

      //   this.trabajadorId =
      //     this.selectTrabajador != null ? this.selectTrabajador.id : 0;
    },
  },
  filters: {
    prettify: function (value) {
      let data = value.split(":");
      let minutes = data[0];
      let secondes = data[1];
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (secondes < 10) {
        secondes = "0" + secondes;
      }
      return minutes + ":" + secondes;
    },
  },
  methods: {
    async eliminarPersonal(id, arrP, lista) {
      for (let i = 0; i < arrP.length; i++) {
        const element = arrP[i];
        if (element.id == id) {
          arrP.splice(i, 1);
          lista.splice(i, 1);
        }
      }
    },
    eliminarTrabajador() {
      this.selectTrabajador = null;
    },
    async getTemporizador(id) {
      let me = this;
      let btn = document.getElementById("btn" + id);
      let icon = document.getElementById("icon" + id);
      let fecha = document.getElementById("fecha" + id).value;
      let hora = document.getElementById("hora" + id).value;
      let arrPersonal = document.getElementById("listPersonal" + id).value;

      let hasClass = btn.classList.contains("text-danger");
      let tipo = 2;
      let response = null;

      if (hasClass) {
        tipo = 1;
      }

      if (tipo == 1) {
        btn.disabled = true;
        btn.classList.add("link-disabled");
        btn.classList.add("text-warning");

        response = await axios({
          method: "post",
          url: "/getstartactividad/" + id,
          data: {
            fecha: fecha,
            hora: hora,
            personal: arrPersonal,
            tipo: tipo,
            _token: this.$store.state.token,
          },
        });

        let cadena_errors = "";
        Object.values(response.data.errores).forEach((val) => {
          cadena_errors += val + ", ";
        });

        alert(cadena_errors.slice(0, -2));

        let seMuestra = false;
        if (response != null) {
          if (response.data.estado) {
            seMuestra = true;
          }
        } else if (tipo == 2) {
          seMuestra = true;
        }

        if (seMuestra) {
          btn.classList.remove("text-warning");
          if (hasClass) {
            btn.classList.remove("text-danger");
            btn.classList.add("text-success");

            icon.classList.remove("la-play-circle");
            icon.classList.add("la-pause-circle");
          } else {
            btn.classList.remove("text-success");
            btn.classList.add("text-danger");

            icon.classList.remove("la-pause-circle");
            icon.classList.add("la-play-circle");
          }

          me.arrDetalles.forEach((element) => {
            // console.log('e', element.detalles);
            // console.log('en_proceso', element.enProceso);
            if (element.detalles.id == id) {
              element.detalles.enProceso = !element.detalles.enProceso;
              element.detalles.fecha = fecha;
            }
          });
        }
        btn.disabled = false;
        btn.classList.remove("link-disabled");
      }
    },
    async getTemporizador02(id) {
      let me = this;
      let btn = document.getElementById("btnEspera" + id);
      // let icon  = document.getElementById('icon'+id)
      let fecha = document.getElementById("fecha" + id).value;
      let hora = document.getElementById("hora" + id).value;
      let arrPersonal = document.getElementById("listPersonal" + id).value;

      let tipo = 2;
      let response = null;
      btn.disabled = true;
      btn.classList.add("link-disabled");
      btn.classList.add("text-warning");

      if (tipo != 1) {
        response = await axios({
          method: "post",
          url: "/getstartactividad/" + id,
          data: {
            fecha: fecha,
            hora: hora,
            personal: arrPersonal,
            tipo: tipo,
            _token: this.$store.state.token,
          },
        });

        let cadena_errors = "";
        Object.values(response.data.errores).forEach((val) => {
          cadena_errors += val + ", ";
        });

        alert(cadena_errors.slice(0, -2));

        let seMuestra = false;
        if (response != null) {
          if (response.data.estado) {
            seMuestra = true;
          }
        } else if (tipo == 2) {
          seMuestra = true;
        }

        if (seMuestra) {
          me.arrDetalles.forEach((element) => {
            // console.log('e', element.detalles);
            // console.log('en_proceso', element.enProceso);
            if (element.detalles.id == id) {
              element.detalles.enProceso = !element.detalles.enProceso;
            }
          });
        }

        btn.disabled = false;
        btn.classList.remove("link-disabled");
        btn.classList.remove("text-warning");
        btn.classList.add("text-danger");
      }
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
    async getTrabajadores() {
      //   const res = await fetch(API_URL.replace(":query", query));
      //   const result = await res.json();
      //   this.trabajadores = result.personal;
      //  async buscarPersonas () {
      let me = this;

      let response = await axios({
        method: "POST",
        url: "/trabajadores",
        data: {
          query: me.queryT,
        },
      });
      me.trabajadores = response.data.personal;
    },
    async cargarActividades() {
      let me = this;
      let detalles = await axios({
        method: "post",
        url: "/buscardetalleorden",
        data: {
          placa: me.placa,
          id: me.id,
          _token: this.$store.state.token,
        },
      });
      me.orden = detalles.data.orden;
      me.cliente = me.orden != null ? me.orden.cliente : "-";
      me.queryT = me.orden != null ? me.orden.asignado : "-";
      me.trabajadorId = me.orden != null ? me.orden.id : 0;
      me.arrDetalles = detalles.data.detalles;
      me.listDetalles = detalles.data.listDetalles;
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalAsignacion').modal('toggle')
      $('#modalAsignacion').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(attr, cliente, clienteId, placa) {
      $("#modalAsignacion").css("z-index", "-1");
      let me = this;
      me.id = attr;
      me.cliente = cliente;
      me.clienteId = clienteId;
      me.placa = placa;
      document.getElementById("data-error2").innerHTML = "";

      if (me.id > 0) {
        await me.cargarActividades();
        $("#modalAsignacion").modal({ backdrop: "static", show: true, keyboard: false });
        $("#modalAsignacion").css("z-index", "1500");
        $('#header-sec').css('z-index','1')
        $('#aside-sec').css('z-index','1')         
   
      }
    },
    guardarSeleccion(event, arrP, lista) {
      let me = this;
      let band = true;
      document.getElementById("data-error2").innerHTML = "";
      lista.forEach((element) => {
        if (element == event.id) {
          band = false;
        }
      });

      if (band) {
        arrP.push({ id: event.id, trabajador: event.personal });
        lista.push(event.id);
        me.buscarTrabajador = "";
      } else {
        document.getElementById("data-error").innerHTML =
          '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong>Personal ya Antes Asignado a la Tarea</a></div ></div>';
      }
    },
  },
  //   watch: {
  //     buscarTrabajador: _.debounce(function (qq) {
  //       this.getTrabajadores(qq);
  //     }, 500),
  //   },
  created() {
    let me = this;
    me.detalles = [];
  },
  activated: function () {
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    me.detalles = [];
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
table {
  cursor: pointer;
}

.link-disabled {
  color: gray;
  pointer-events: none;
}
.pl-auto-x {
  padding-left: 2rem;
}
</style>