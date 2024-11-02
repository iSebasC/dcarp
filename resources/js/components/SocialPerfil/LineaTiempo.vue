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
              <li class="breadcrumb-item active">Mi Perfil</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="timeline" class="timeline-center timeline-wrapper">
        <div class="row">
          <div class="col-12">
            <img
              src="/images/portada.gif"
              class="img-portada w-100"
              alt="timeline image"
            />
            <div class="user-data text-center bg-white rounded pb-2 mb-md-2">
              <img
                src="/images/portrait/large/perfil_img.png"
                class="
                  img-fluid
                  rounded-circle
                  width-150
                  profile-image
                  shadow-lg
                  border border-3
                "
                width="100"
                alt="timeline image"
              />
              <h4 class="mt-1 mb-0" v-text="perfil.nombres"></h4>
              <p v-text="isCliente ? 'Cliente' : 'Administrativo'"></p>
            </div>
            <nav class="navbar navbar-expand-lg nav-margin">
              <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav mr-auto">
                  <li class="nav-item px-2 active">
                    <a class="nav-link" href="javascript:void(0);">Inicio</a>
                  </li>
                  <li class="nav-item px-2">
                    <a class="nav-link" href="javascript:void(0);"
                      >Seguimiento</a
                    >
                  </li>
                </ul>
                <div class="navbar-text">
                  <ul class="navbar-nav">
                    <li class="nav-item px-2">
                      <a class="nav-link" href="javascript:void(0);"
                        >Multimedia</a
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
        </div>
        <h3 class="page-title text-center">{{ perfil.marc }}</h3>

        <ul class="timeline">
          <!-- <li class="timeline-line"></li> -->
          <div class="timeline-group d-flex justify-content-center">
            <button
              class="btn btn-success btn-sm position-relative "
              @click="renderCita"
            >
              <i class="mdi mdi-calendar-account"></i> Separar Cita
            </button>
          </div>
          <!-- <li class="timeline-line"></li> -->
          <div class="timeline-group mt-4">
            <!-- <a v-for=""  > Today</a> -->
            <button
              v-for="(mar, index) in marcas"
              @click="cargarTrabajos(mar.id)"
              :class="
                index == 0
                  ? 'btn btn-warning position-relative mr-1'
                  : index == 1
                  ? 'btn btn-info position-relative mr-1'
                  : 'btn btn-danger position-relative mr-1'
              "
              :key="mar.id"
            >
              <i class="mdi mdi-car-cog"></i> {{ mar.nombre }}
            </button>
            <input type="hidden" name="marca_id" id="marca_id" />
          </div>
          <div class="row justify-content-md-center justify-content-lg-center">
            <div class="col-md-6 col-sm-12 col-xs-12">
              <div class="card mt-1">
                <div class="card-body px-md-0">
                  <div class="row mt-2 justify-content-center">
                    <div class="col-md-3 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label :for="'fecha_' + entidad"
                          >F. Inicio <span class="text-danger">*</span></label
                        >
                        <input
                          type="date"
                          :id="'fecha_' + entidad"
                          class="form-control text-center form-control-sm"
                          @keyup.enter="cargarTrabajos()"
                          name="fecha"
                        />
                      </div>
                    </div>

                    <div class="col-md-3 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label :for="'fecha_f_' + entidad"
                          >F. Fin <span class="text-danger">*</span></label
                        >
                        <input
                          type="date"
                          :id="'fecha_f_' + entidad"
                          class="form-control text-center form-control-sm"
                          @keyup.enter="cargarTrabajos()"
                          name="fechaf"
                        />
                      </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label :for="'placa_' + entidad"
                          >Placa del Vehículo
                          <span
                            :class="isCliente ? 'text-danger' : 'text-success'"
                            >*</span
                          >
                        </label>
                        <input
                          type="text"
                          :id="'placa_' + entidad"
                          class="form-control form-control-sm text-center"
                          name="placa"
                          placeholder="Buscar Placa..."
                          @keyup.enter="cargarTrabajos()"
                        />
                      </div>
                    </div>
                  </div>
                  <div
                    class="row mt-0 justify-content-center"
                    v-if="!isCliente"
                  >
                    <div class="col-md-10 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label :for="'cliente_' + entidad"
                          >Cliente
                          <span class="text-success">*</span>
                        </label>
                        <input
                          type="text"
                          :id="'cliente_' + entidad"
                          class="form-control form-control-sm"
                          name="cliente"
                          placeholder="Documento/Razón Social"
                          @keyup.enter="cargarTrabajos()"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="write-post">
                    <hr class="m-0" />
                    <div class="row justify-content-end">
                      <div class="col-xs-12 col-md-3 pt-2 text-center pr-4">
                        <button
                          type="button"
                          @click="cargarTrabajos()"
                          class="
                            btn btn-danger btn-sm btn-min-width btn-glow
                            mr-1
                            mb-1
                          "
                        >
                          Buscar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <span
                class="text-info pl-2 ml-1 mb-2 col-form-label"
                :id="'loading_' + entidad"
                ><Loader /></span
              >
            </div>
          </div>
        </ul>
        <ul class="timeline" :id="'tabla_' + entidad">
          <!-- <li class="timeline-line"></li> -->
          <li
            v-for="(a, index) in trabajos"
            :key="a.id"
            :class="'timeline-item ' + (index == 0 ?'': 'mt-3 ' )+ (index%2 == 1? 'timeline-inverted':'')"
          >
            <div :class="'timeline-badge ' + ( index % 2 == 0 ? 'info': 'danger')"
                data-toggle="tooltip"
                data-placement="right"
                :title="a.fecha">
              <i class="mdi mdi-car-select"></i>
            </div>

            <div
              class="timeline-panel bg-white"
            >
              <div class="timeline-heading pb-1">
                <h5
                  class="timeline-title text-info"
                  v-text="'Orden: ' + a.orden"
                ></h5>
                <p class="timeline-subtitle text-muted mb-0 pt-1">
                  <span class="font-small-3"
                    ><strong>Cliente: </strong>{{ a.cliente }}</span
                  ><br />
                  <span class="font-small-3"
                    ><strong>Placa del Vehículo: </strong>{{ a.placa }}</span
                  >
                </p>
              </div>
              <hr class="m-1" />
              <!-- <div class="card-content"> -->
                <div class="timeline-body">
                  <div class="row">
                    <div class="col-5">
                      <div class="row">
                        <div class="col-12 pr-0">
                          <strong>Inicia: </strong>
                          <span
                            class="small"
                            v-text="a.inicia != null ? a.inicia : '-'"
                          ></span>
                        </div>

                        <div class="col-12 pr-0">
                          <strong>Fin: </strong>
                          <span
                            class="small"
                            v-text="a.finaliza != null ? a.finaliza : '-'"
                          ></span>
                        </div>

                        <div class="col-12">
                          <ul class="list-inline my-1">
                            <li class="pr-1">
                              <button
                                class="btn btn-danger btn-sm btn-glow"
                                title="Adjuntos & Observaciones"
                                @click="abrirFotos(a)"
                              >
                                Fotos & Observaciones
                              </button>
                            </li>

                            <li class="pr-1 pt-2" v-if="a.idVenta != null">
                              <button
                                class="btn btn-outline-success btn-sm btn-glow"
                                title="Ver Comprobante"
                                @click="verComprobante(a.idVenta)"
                              >
                                Ver Comprobante
                              </button>
                            </li>
                          </ul>
                        </div>
                      </div>
                      <!-- <strong>Inicia: </strong>{{ a.inicia != null ? a.inicia : "-" }} -->

                      <!-- <strong>Realiza Por:</strong> -->
                    </div>
                    <div class="col-7">
                      <div class="row">
                        <div class="col-12">
                          <strong>Detalles:</strong>
                        </div>
                        <div class="col-12 mt-1">
                          <ul class="list-group overflow-auto h-list">
                            <li
                              :class="
                                'list-group-item p-1 small ' +
                                (index == 0 ? 'active' : '')
                              "
                              v-for="(item, index) in a.servicios"
                              :key="index"
                            >
                              {{ item }}
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <p class="card-text"><strong>Cliente: </strong>{{a.cliente}}</p> -->
                  <!-- <p class="card-text">
                    <strong>Realizada por: </strong>{{ a.trabajadores }}
                  </p> -->

                  <!-- <p class="card-text"><strong>Documento: </strong>{{a.documento}}</p> -->
                  <!-- <p class="card-text"><strong>Placa: </strong>{{a.placa}}</p> -->
                  <!-- <p class="card-text"><strong>Marca: </strong>{{a.marcanombre}}</p> -->
                  <!-- <p class="card-text"></p>

                  <p class="card-text">
                    <strong>Finaliza: </strong
                    >{{ a.finaliza != null ? a.finaliza : "-" }}
                  </p> -->
                </div>
              <!-- </div> -->
            </div>
          </li>
        </ul>
      </section>
    </div>
    <ModalFotos ref="modalfotos"></ModalFotos>
    <MantenimientoCita ref="mantenimientocita" />
  </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import ModalFotos from "../EvaluacionTrabajo/VerTrabajo";
import MantenimientoCita from "../Cita/Mantenimiento";
import Loader from "../Loader";
export default {
  name: "LineaTiempo",
  mixins: [misMixins],
  components: {
    ModalFotos,
    MantenimientoCita,
    Loader
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      trabajos: [],
      cliente: {},
      perfil: {},
      total: "",
      inicio: "",
      fin: "",
      paramInicio: "",
      paramFin: "",
      idTipo: "",
      tipoUsuario: "",
      tiempo: null,
      marcas: [],
      entidad: "linea_tiempo",
      bandRender: false,
      isCliente: true,
      credencialesAPI: {
        user: "20103327378",
        pass: "bj1R8xkhHB",
      },
      // fecha: ''
    };
  },
  methods: {
    renderCita() {
      let me = this;
      // alert('Ok')
      me.generarCita(0);
    },
    generarCita(id, object) {
      let me = this;
      me.idCita = id;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.mantenimientocita.showModal(id, object);
    },
    verComprobante(id) {
      window.open(
        `https://fasteinvoice.com/consultar.php?ruc=${this.credencialesAPI.user}&password=${this.credencialesAPI.pass}&id=${id}`,
        "_blank"
      );
    },
    async cargarPerfil() {
      let me = this;
      let response = await axios({
        method: "post",
        url: "/getperfil",
        data: {
          _token: this.$store.state.token,
        },
      });

      me.perfil = response.data.perfil;
      me.marcas = [] //response.data.marcas;
      me.isCliente = response.data.isCliente;
      // console.log(me.marcas)
      var el = document.getElementById("loading_" + me.entidad);
      el.classList.add("d-none");

    },
    async cargarTrabajos(idmarca = "") {
      let me = this;
      let fechai = document.getElementById("fecha_" + me.entidad).value;
      let fechaf = document.getElementById("fecha_f_" + me.entidad).value;
      let placa = document.getElementById("placa_" + me.entidad).value;
      // let marca = document.getElementById('marca_id').value
      let elemCliente = document.getElementById("cliente_" + me.entidad);
      let cliente = "";
      if (elemCliente) {
        cliente = elemCliente.value;
      }
      var el = document.getElementById("loading_" + me.entidad);
      el.classList.add("d-none");
      let response = await axios({
        method: "post",
        url: "/getperfiltrabajos",
        data: {
          _token: this.$store.state.token,
          marca: idmarca,
          fechai: fechai,
          fechaf: fechaf,
          placa: placa,
          cliente: cliente,
        },
      });
      me.trabajos = response.data.detalles;
      //   me.cliente = response.data.cliente;

      me.renderTabla(me.entidad);
    },
    abrirFotos(item) {
      let me = this;
      //   me.idfoto = id
      //   console.log(me.idfoto)
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.modalfotos.showModal(item.id, item.orden, item.placa);
    },
  },
  activated() {
    let me = this;
    me.isValidSession();

    if (!me.bandRender) {
      document.getElementById("fecha_" + me.entidad).value =
        me.obtenerFechaActual();
      document.getElementById("fecha_f_" + me.entidad).value =
        me.obtenerFechaActual();
      me.bandRender = true;
    }

    // me.fecha  = me.obtenerFechaActual()
    // setTimeout(() => {
    me.cargarPerfil();
    // me.cargarTrabajos();
    // }, 1000);
  },
};
</script>

<style scoped>
.img-portada {
  width: 100%;
}
.h-list {
  height: 10rem !important;
}
</style>
