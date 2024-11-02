<template>
  <div
    class="modal fade"
    id="modalDetallesEvInc"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalDetallesEvIncIncLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <form
          class="form"
          id="formEnvIncidencia"
          method="POST"
          action="/guardarincidencia"
        >
          <div class="modal-header">
            <h5 class="modal-title" id="modalDetallesEvIncIncLabel">
              Incidencias de Documento:
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
            <h6 class="ml-2">
              Tiempo(s) Registrado(s): <span v-text="textTotal"> </span>
            </h6>
            <button
              v-if="situacion == 'I'"
              type="button"
              class="ml-2 mb-2 btn btn-sm btn-outline-primary"
              @click="agregarIncidencia()"
            >
              Agregar Incidencia
            </button>
            <input type="hidden" :value="id" name="id" />
            <input
              type="hidden"
              :value="listDetallesId.join(',')"
              name="listDetalles"
              id="listDetalles"
            />

            <div class="row">
              <div class="col-11 mx-auto">
                <div
                  class="table-responsive px-1"
                  id="tabla-detalle"
                  style="height: 350px; overflow-y: scroll"
                >
                  <table class="table table-striped mb-0">
                    <thead>
                      <tr class="bg-primary text-white">
                        <th class="text-left">#</th>
                        <th class="text-center">Tipo Incidencia</th>
                        <th class="text-center">Descripción</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center"></th>
                      </tr>
                    </thead>
                    <tbody style="height: 250px">
                      <tr v-if="!detalles.length">
                        <td class="text-left text-danger" colspan="5">
                          <strong
                            >No se Encontraron Resultados en su Búsqueda</strong
                          >
                        </td>
                      </tr>

                      <tr v-for="(p, index) in detalles" :key="index">
                        <td
                          class="text-left"
                          v-text="
                            index + 1 < 10 ? '0' + (index + 1) : index + 1
                          "
                        ></td>
                        <td class="text-center">
                          <select
                            :name="`motivoId_${p.id}`"
                            :id="`motivoId_${p.id}`"
                            class="custom-select ml-1 custom-select-sm"
                            :disabled="p.retornaBD == '1'"
                          >
                            <option value="" selected="" readonly="">
                              Seleccione
                            </option>
                            <option
                              v-for="(item, index2) in motivos"
                              :key="index + '_' + index2"
                              :value="item.id"
                              :selected="p.idMotivo == item.id"
                            >
                              {{ item.nombre }}
                            </option>
                          </select>
                        </td>
                        <td class="text-center">
                          <textarea
                            class="form-control form-control-sm no-resize"
                            :readonly="p.retornaBD == '1'"
                            :name="`descripcion_${p.id}`"
                            :id="`descripcion_${p.id}`"
                            :value="p.descripcion"
                          > 
                          </textarea>
                        </td>
                        <td class="text-center">
                          <select
                            :name="`estado_${p.id}`"
                            :id="`estado_${p.id}`"
                            class="custom-select custom-select-sm"
                            :disabled="(p.retornaBD == '0' && p.estado == 'I' ) || (p.retornaBD == '1' && p.estado == 'T')"
                          >
                            <option
                              v-for="(item, index3) in estados"
                              :key="index + '_' + index3"
                              :value="item.id"
                              :selected="p.estado == item.id"
                            >
                              {{ item.nombre }}
                            </option>
                          </select>
                          
                          <input type="hidden" :id="`retornaBD_${p.id}`" :name="`retornaBD_${p.id}`" :value="p.retornaBD" />
                          <input type="hidden" :id="`refId_${p.id}`" :name="`refId_${p.id}`" :value="p.id" />
                         <!-- <tr>
                            <td class="border-0 px-1">
                              <input
                                type="number"
                                :name="`tiempo_${p.id}`"
                                class="
                                  form-control form-control-sm
                                  mr-1
                                  text-center
                                "
                                :id="`tiempo_${p.id}`"
                                min="0.00"
                                step="0.01"
                                :readonly="p.retornaBD == '1'"
                                :value="p.tiempo"
                              />
                              <input
                                type="hidden"
                                :name="`retornaBD_${p.id}`"
                                :id="`retornaBD_${p.id}`"
                                :value="p.retornaBD"
                              />
                            </td>
                            <td class="border-0 px-0">
                              <select
                                :name="`tipoTiempo_${p.id}`"
                                :id="`tipoTiempo_${p.id}`"
                                class="custom-select custom-select-sm"
                                :disabled="p.retornaBD == '1'"
                              >
                                <option value="" selected="" readonly="">
                                  Seleccione
                                </option>
                                <option
                                  v-for="(item, index3) in tiposTiempo"
                                  :key="index + '_' + index3"
                                  :value="item.id"
                                  :selected="p.unidadTiempo == item.id"
                                >
                                  {{ item.nombre }}
                                </option>
                              </select>
                            </td>
                          </tr> -->
                        </td>
                        <td style="width: 30px">
                          <a
                            href="javascript:void(0)"
                            class="btn-danger btn-sm"
                            title="Eliminar"
                            v-if="p.retornaBD == '0'"
                            @click="eliminar(p.id)"
                          >
                            <i class="mdi mdi-minus-thick icon-size"></i>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div id="data-error"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              v-if="situacion == 'I'"
              type="button"
              @click="
                enviarFormModalIncidencias(
                  'formEnvIncidencia',
                  'modalDetallesEvInc',
                  'data-error'
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
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "IncidenciasEv",
  mixins: [misMixins],
  data() {
    return {
      id: this.$attrs.idventa,
      documento: this.$attrs.documento,
      detalles: [],
      listDetallesId: [],
      token: this.$store.state.token,
      motivos: [],
      estados :[
        { id: 'I', nombre: 'Iniciado'},
        { id: 'T', nombre: 'Terminado'}
      ],
      tiposTiempo: [
        { id: "minuto(s)", nombre: "Minuto(s)" },
        { id: "hora(s)", nombre: "Hora(s)" },
        { id: "dia(s)", nombre: "Día(s)" },
      ],
      min: 0,
      max: 9999999,
      textTotal: "",
      totales: [],
      situacion: "",
    };
  },
  methods: {
    generarCorrelativo() {
      let me = this;
      let numero = me.getRandomNumber(me.min, me.max);
      let idx = me.detalles.indexOf(numero);
      do {
        if (idx != -1) {
          numero = me.getRandomNumber(me.min, me.max);
          idx = me.detalles.indexOf(numero);
        }
      } while (idx != -1);

      return numero;
    },
    eliminar(id) {
      let me = this;

      for (let i = 0; i < me.detalles.length; i++) {
        let element = me.detalles[i];
        if (element.id == id) {
          me.detalles.splice(i, 1);
          me.listDetallesId.splice(i, 1);
        }
      }
    },
    agregarIncidencia() {
      let elemento = {
        id: this.generarCorrelativo(),
        idMotivo: '',
        descripcion: '',
        retornaBD: '0',
        estado: 'I'
      };

      this.detalles.push(elemento);
      this.listDetallesId.push(elemento.id);
    },
    getStringTotal() {
      this.textTotal = ``;
      let stDias = "0 dia(s)";
      let stHoras = "0 hora(s)";
      let stMinuto = "0 minuto(s)";
      let stSegundo = "0 segundo(s)";
      let convertMin = 0;
      let convertHora = 0;
      let convertMinExtra = 0;

      this.totales.forEach((el) => {
        if (el.horas != "0") {
          let parseHora = parseFloat(el.horas)/24;
          convertHora = parseInt(parseHora);
          el.horas = parseFloat(el.horas) - (convertHora * 24);
          el.dias = parseInt(el.dias) + convertHora;
          // stHoras = `${el.horas} hora(s)`;
        }
       
        if (el.segundos != "0") {
          let parseMin = parseFloat(el.segundos)/60;
          convertMin = parseInt(parseMin);
          el.segundos = parseFloat(el.segundos) - (convertMin * 60);
          el.minutos = parseInt(el.minutos) + convertMin;
          // stSegundo = `${el.segundos} segundo(s)`;
        }
        
        if (el.minutos != "0") {
          let parseMinExtra = parseFloat(el.minutos)/60;
          convertMinExtra = parseInt(parseMinExtra);
          el.minutos = parseFloat(el.minutos) - (convertMinExtra * 60);
          // el.minutos = parseInt(el.minutos) + convertMinExtra;
          el.horas = parseInt(el.horas) + convertMinExtra;
          // el.segundos = parseInt(el.segundos) + sobMinExtra;

          // if (convertMin > 0) {
          //   stMinuto = `${parseInt(el.minutos) + convertMin} minuto(s)`;
          // } else {
            // stSegundo = `${el.segundos} segundo(s)`;
            // stMinuto = `${el.minutos} minuto(s)`;
            // stHoras = `${el.horas} hora(s)`;
            
          // }
        }

        if (el.horas != "0") {
          stHoras = `${el.horas} hora(s)`;
        }
        if (el.dias != "0") {
          // if (convertHora > 0) {
          //   stDias = `${parseInt(el.horas) + convertHora} dia(s)`;
          // } else {
            stDias = `${el.dias} dia(s)`;
          // }
        }
        if (el.minutos != "0") {
          stMinuto = `${el.minutos} minuto(s)`;
        }
        if (el.segundos != "0") {
          stSegundo = `${el.segundos} segundo(s)`;
        }
        
        // if (el.unidadTiempo == "dia(s)") {
        //   stDias = `${el.tiempo} ${el.unidadTiempo}`;
        // }

        // if (el.unidadTiempo == "hora(s)") {
        //   stHoras = `${el.tiempo} ${el.unidadTiempo}`;
        // }

        // if (el.unidadTiempo == "minuto(s)") {
        //   stMinuto = `${el.tiempo} ${el.unidadTiempo}`;
        // }
      });

      //   if (stDias == "-" && stHoras == "-" && stMinuto == "-") {
      //     this.textTotal = "-";
      //   } else {
      this.textTotal = `${stDias}, ${stHoras}, ${stMinuto}, ${stSegundo}`;
      //   }
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalDetallesEvInc').modal('toggle')
      $('#modalDetallesEvInc').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(attr) {
      document.getElementById(`data-error`).innerHTML = ``;
      $("#modalDetallesEvInc").css("z-index", "-1");
      let me = this;
      me.id = attr;
      me.situacion = "";
      // alert(me.id)
      // me.nombre = attr2
      // $('#texto').text(me.id)
      // alert (me.id)
      // me.mostrarModal = ne.isVisible()

      // alert('....' + me.id)
      // if (me.id !== 'undefined') {
      if (me.id > 0) {
        let response = await axios({
          method: "post",
          url: "/getincidencias/" + me.id,
          data: {
            _token: me.token,
          },
        });
        me.detalles = response.data.detalles;
        me.listDetallesId = response.data.detalles.map(det => det.id);
        me.motivos = response.data.motivos;
        me.totales = response.data.totales;
        me.situacion = response.data.situacion;
        this.getStringTotal();
        // me.arreglo2 = response.data.encabezados
        // me.opciones = response.data.opciones02
        // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        $("#modalDetallesEvInc").modal({ backdrop: "static", show: true, keyboard: false });
        $("#modalDetallesEvInc").css("z-index", "1500");
        $('#header-sec').css('z-index','1')
        $('#aside-sec').css('z-index','1')         
 
        // $('.modal-backdrop').css('z-index','1')
      }
      // }
      // $('#exampleModal').on('shown.bs.modal', function () {
      //     $('#myInput').trigger('focus')
      // })
    },
  },
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
.no-rezise {
  resize: none;
}
.custom-select:disabled {
  background-color: #f2f2f2 !important;
}
</style>