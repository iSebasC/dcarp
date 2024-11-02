<template>
  <div
    class="modal fade"
    id="modalVerG"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalVerGLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <!-- <form class="form" id="formEnv3" method="POST" action="/revisaravance"> -->
        <div class="modal-header">
          <h5 class="modal-title" id="modalVerGLabel">
            Fotos & Videos de Orden de Trabajo:
            <strong v-text="servicio"></strong>
            <br />
            <small v-text="' Placa del Vehículo: ' + placa"></small>
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
          <input type="hidden" name="id" :value="id" />
          <input type="hidden" name="_token" :value="token" />

          <div class="row">
            <div class="col-md-12 h-image">
              <div
                v-if="fileLoading.length > 0"
                id="carouselImg"
                class="carousel slide"
                data-ride="carousel"
                data-interval="5000"
              >
                <ol class="carousel-indicators">
                  <li
                    v-for="(item, index) in fileLoading"
                    :key="index"
                    data-bs-target="#carouselImg"
                    :data-slide-to="index"
                    :class="index == 0 ? 'active' : ''"
                    @click="ir(index)"
                  ></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div
                    v-for="(item, index) in fileLoading"
                    :key="index"
                    :class="'carousel-item ' + (index == 0 ? 'active' : '')"
                  >
                    <img
                      v-if="item.tipo == 'I'"
                      :src="item.url"
                      class="d-block h-image"
                      :alt="item.nombre"
                    />
                    <video
                      v-else
                      :src="item.url"
                      class="d-block h-image"
                      :alt="item.nombre"
                      controls
                    ></video>
                    <!-- <div class="carousel-caption d-none d-md-block">
                        <p v-text="item.observaciones"></p>
                      </div> -->
                  </div>
                </div>
                <a
                  class="carousel-control-prev"
                  href="javascript:void(0)"
                  role="button"
                  data-bs-slide="prev"
                  @click="prev()"
                >
                  <span
                    class="carousel-control-prev-icon"
                    aria-hidden="true"
                  ></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a
                  class="carousel-control-next"
                  href="javascript:void(0)"
                  role="button"
                  data-bs-slide="next"
                  @click="next()"
                >
                  <span
                    class="carousel-control-next-icon"
                    aria-hidden="true"
                  ></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
              <div v-else>
                <strong class="text-danger"
                  >No se encontraron resultados</strong
                >
              </div>
            </div>

            <div class="col-md-12 mt-1">
              <!-- <div class="row">
                                    <div class="col-md-5">
                                        <span>Actividad en Curso: <mark v-text="actividad_actual"></mark></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="select_estado">Motivo <span class="text-danger">*</span></label>
                                            <select class="custom-select custom-select-sm" name="select_motivo" 
                                            id="select_motivo">
                                                <option value="" disabled="disabled" selected="selected">Seleccione</option>
                                                <option v-for="motivo in motivoslibre" :key="motivo.id" :value="motivo.id"> {{motivo.nombre}}</option>
                                            </select>
                                        
                                            <input type="hidden" name="idTiempo" id="idTiempo" :value="idTiempo">
                                            <input type="hidden" name="enProceso" id="enProceso" value="">
                                            <input type="hidden" name="enTiempo" id="enTiempo" value="">
                                            <input type="hidden" name="tiempoInicioR" id="tiempoInicioR" :value="tiempoInicioR">
                                            <input type="hidden" name="tiempoFinR" id="tiempoFinR" :value="tiempoFinR">
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 col-xs-12">
                                        <p class="mt-2">
                                            <span>
                                                <strong>Inicio: </strong><span v-text="tiempoInicio"></span>
                                            </span>
                                         
                                            <br />
                                            <span v-if="enProceso==0">
                                                <strong>Fin: </strong><span v-text="tiempoFin"></span>
                                            </span>
                                         
                                            <br />
                                            <span v-if="enProceso==1">
                                                <Cronometro :ref="`cronometro${id}`"></Cronometro>
                                            </span>
                                        </p>
                                    </div>

                                    <div class="col-md-1 mt-2">
                                        <a :class="enProceso == 0?'text-danger':'text-success'" title="Empezar Trabajo" 
                                        href="javascript:void(0);" :id="'btn'+id" 
                                        @click="getTemporizador(id)">
                                            <i :id="'icon'+id" :class="enProceso == 0?'la la-play-circle':'la la-pause-circle'" 
                                            style="font-size:28px;margin-top:-5px;"></i>
                                        </a>
                                    </div>

                                    <div class="col-md-1 mt-2" v-if="enProceso==1">
                                        <a class="text-danger" title="En Espera" 
                                        href="javascript:void(0);" :id="'btnEspera'+id" 
                                        @click="getTemporizador02(id)">
                                            <i :id="'iconEspera'+id" 
                                            class="la la-stop-circle" 
                                            style="font-size:28px;margin-top:-5px;"></i>
                                        </a>
                                    </div>
                                

                                </div> -->

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="observaciones"
                      >Observaciones <span class="text-success">*</span></label
                    >
                    <textarea
                      id="observaciones"
                      class="form-control form-control-sm no-resize"
                      name="observaciones"
                      rows="2"
                      readonly=""
                      v-text="observaciones"
                    ></textarea>
                  </div>
                </div>

                <!-- <div class="col-md-3">
                    <div class="form-group">
                      <label for="select_estado"
                        >Situación <span class="text-danger">*</span></label
                      >
                      <select
                        class="custom-select custom-select-sm"
                        name="select_estado"
                        id="select_estado"
                      >
                        <option value="" disabled="" selected="">
                          Seleccione
                        </option>
                        <option value="1">Por Revisar</option>
                        <option value="2">Revisado</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="tiempomuerto"
                        >Tiempo Libre <span class="text-muted">(min)</span>
                        <span class="text-danger">*</span></label
                      >
                      <input
                        type="number"
                        value="0"
                        class="form-control text-right"
                        name="tiempomuerto"
                        min="0"
                      />
                    </div>
                  </div> -->
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12" id="data-error3"></div>
          </div>
        </div>
        <div class="modal-footer">
          <!-- <button
              type="button"
              @click="enviarFormModalAR('modalVerG', 'formEnv3', 'data-error3')"
              class="btn btn-sm btn-success"
              id="btnEnvio"
            >
              <i class="mdi mdi-check-bold icon-size"></i> Guardar
            </button> -->

          <button
            type="button"
            class="btn btn-danger btn-sm"
            @click="cerrarModal"
          >
            <i class="mdi mdi-close icon-size"></i> Cerrar
          </button>
        </div>
        <!-- </form> -->
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Cronometro from "../OrdenTrabajo/Cronometro";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "VerTrabajo",
  mixins: [misMixins],
  components: {
    Cronometro,
  },
  data() {
    return {
      id: "",
      servicio: "",
      placa: "",
      token: this.$store.state.token,
      observaciones: "",
      contador: 0,
      tiempoInicio: "",
      tiempoFin: "",
      tiempoInicioR: "",
      idTiempo: 0,
      tiempoFinR: "",
      enProceso: 1,
      fileLoading: [],
      motivoslibre: [],
      actividad_actual: "",
    };
  },
  methods: {
    prev() {
      $("#carouselImg").carousel("prev");
    },
    next() {
      $("#carouselImg").carousel("next");
    },
    ir(i) {
      $("#carouselImg").carousel(i);
    },
    cerrarModal () {
        // $('.fade').remove();
        // $('body').removeClass('modal-open');
        // $('.modal-backdrop').remove();

        $('#modalVerG').modal('toggle')
        $('#modalVerG').css('z-index', '-1')
        $('#header-sec').css('z-index','')
        $('#aside-sec').css('z-index','')         
    },
    async showModal(id, servicio, placa) {
      let me = this;
      me.id = id;
      me.servicio = servicio;
      me.placa = placa;
      me.enProceso = 1;
      me.observaciones = "";
      document.getElementById("data-error3").innerHTML = "";
      //   document.getElementById("select_motivo").value = "";
      //   document.getElementById("observaciones").value = "";
      //   document.getElementById("select_estado").value = "";

      $("#modalVerG").css("z-index", "-1");
      await me.cargarMultimedia();
      let response = await axios.get("/getmotivoslibre");
      me.motivoslibre = response.data.motivos;
      $("#carouselImg").carousel();
      $("#modalVerG").modal({ backdrop: "static", show: true, keyboard: false });
      $("#modalVerG").css("z-index", "1500");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1') 
    },
    async cargarMultimedia() {
      let me = this;
      me.fileLoading = [];
      let response = await axios({
        method: "get",
        url: "/getmultimedia/" + me.id,
      });

      me.fileLoading = response.data.arreglo;
      //   me.tiempoInicio = response.data.tiempoInicio;
      //   me.tiempoInicioR = response.data.tiempoInicioR;
      me.observaciones = response.data.observaciones;
      //   me.idTiempo = response.data.tiempo != null ? response.data.tiempo.id : 0;
      //   me.actividad_actual = response.data.motivo;
    },
    async getTemporizador(id) {
      let me = this;
      let btn = document.getElementById("btn" + id);
      let icon = document.getElementById("icon" + id);

      let hasClass = btn.classList.contains("text-danger");
      let tipo = 2;

      if (hasClass) {
        tipo = 1;
      }

      if (tipo == 1) {
        btn.disabled = true;
        btn.classList.add("link-disabled");
        btn.classList.add("text-warning");

        let seMuestra = true;
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

          me.enProceso = !me.enProceso;
        }

        let tiempoAct = me.obtenerFechaHoraActual();
        me.tiempoInicio = tiempoAct["mostrar"];
        me.tiempoInicioR = tiempoAct["hidden"];

        btn.disabled = false;
        btn.classList.remove("link-disabled");
      }
    },
    async getTemporizador02(id) {
      let me = this;
      let btn = document.getElementById("btnEspera" + id);
      // let icon  = document.getElementById('icon'+id)

      let tipo = 2;
      btn.disabled = true;
      btn.classList.add("link-disabled");
      btn.classList.add("text-warning");

      if (tipo != 1) {
        let seMuestra = true;

        if (seMuestra) {
          me.enProceso = !me.enProceso;
        }
        let tiempoAct = me.obtenerFechaHoraActual();
        me.tiempoFin = tiempoAct["mostrar"];
        me.tiempoFinR = tiempoAct["hidden"];

        btn.disabled = false;
        btn.classList.remove("link-disabled");
        btn.classList.remove("text-warning");
        btn.classList.add("text-danger");
      }
    },
  },
  created() {
    let me = this;
    me.fileLoading = [];
  },
  activated: function () {
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    me.fileLoading = [];
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
.link-disabled {
  color: gray;
  pointer-events: none;
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
.no-resize {
  resize: none;
}
.h-image {
  height: 25rem !important;
}
/* .carousel-inner {
    height: 330px;
} */
</style>