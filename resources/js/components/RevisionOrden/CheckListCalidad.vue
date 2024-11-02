<template>
  <div
    class="modal fade"
    id="modalCheckC"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalCheckCLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCheckCLabel">
            CheckList de Calidad
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
            <div class="col-md-4 ml-2">
              <h6>
                <mark>Documento: </mark><strong v-text="documento"></strong>
              </h6>
              <h6><mark>Placa: </mark><strong v-text="placa"></strong></h6>
              <h6><mark>Cliente: </mark><strong v-text="cliente"></strong></h6>

              <button
                type="button"
                class="btn btn-sm btn-outline-danger mb-1"
                @click="imprimir()"
              >
                Imprimir
              </button>
            </div>
          </div>

          <div
            class="table-responsive px-1"
            id="tabla-inv"
            style="height: 300px; overflow-y: scroll"
          >
            <table class="table table-hover table-sm">
              <thead>
                <tr>
                  <th class="text-center" style="width: 400px">Descripción</th>
                  <th class="text-center">Ok</th>
                  <th class="text-center">No Ok</th>
                  <th class="text-center">Observaciones</th>
                </tr>
              </thead>
              <tbody v-for="(item, index) in detalles" :key="index">
                <tr>
                  <td colspan="4">
                    <h6 style="text-align: justify; text-justify: inter-word">
                      <strong v-text="item.header.nombre"></strong>
                    </h6>
                  </td>
                </tr>
                <tr v-for="(item2, index2) in item.body" :key="index2">
                  <td style="width: 400px">
                    <h6
                      style="text-align: justify; text-justify: inter-word"
                      v-text="item2.nombre"
                    ></h6>
                  </td>
                  <td class="text-center">
                    <input
                      class="mr-1 chkB"
                      @change="obtenerValor(item2.id, 'S')"
                      type="radio"
                      :name="'chkSituacion_' + item2.id"
                      :id="'chkBueno_' + item2.id"
                    />
                  </td>
                  <td class="text-center">
                    <input
                      class="mr-1 chkM"
                      @change="obtenerValor(item2.id, 'N')"
                      type="radio"
                      :name="'chkSituacion_' + item2.id"
                      :id="'chkMalo_' + item2.id"
                    />
                  </td>
                  <td class="text-center">
                    <div class="form-group">
                      <textarea
                        rows="2"
                        @change="obtenerValorText(item2.id)"
                        class="form-control form-control-sm no-resize"
                        :name="'observaciones' + item2.id"
                        :id="'observaciones' + item2.id"
                      ></textarea>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="row mt-3 ml-2">
            <div class="col-md-5">
              <div class="form-group">
                <label for="observaciones"
                  >Observaciones <span class="text-success">*</span></label
                >
                <textarea
                  id="observaciones"
                  rows="4"
                  class="form-control form-control-sm no-resize"
                  name="observaciones"
                  v-text="observacion"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8" id="errorMsgs"></div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              id="btnGuardar"
              @click="guardarCampos()"
              class="btn btn-sm btn-success"
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
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "CheckListCalidad",
  mixins: [misMixins],
  data() {
    return {
      detalles: [],
      documento: "",
      placa: "",
      cliente: "",
      token: this.$store.state.token,
      contEliminar: 0,
      id: 0,
      arrRptas: [],
      cantElementos: 0,
      marcados: [],
      observacion: "",
    };
  },
  methods: {
    obtenerValorText(id) {
      let me = this;
      let observaciones = document.getElementById("observaciones" + id).value;
      let band = false;
      if (me.arrRptas != null) {
        me.arrRptas.forEach((element) => {
          if (element.id == id) {
            band = true;
            element.observacion = observaciones;
          }
        });
      } else {
        me.arrRptas = [];
      }

      if (!band) {
        me.arrRptas.push({ id: id, valor: "N", observacion: observaciones });
      }
    },
    obtenerValor(id, valor) {
      let me = this;
      let observaciones = document.getElementById("observaciones" + id).value;
      let band = false;
      if (me.arrRptas != null) {
        me.arrRptas.forEach((element) => {
          if (element.id == id) {
            band = true;
            element.valor = valor;
            element.observacion = observaciones;
          }
        });
      } else {
        me.arrRptas = [];
      }

      if (!band) {
        me.arrRptas.push({ id: id, valor: valor, observacion: observaciones });
      }
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalCheckC').modal('toggle')
      $('#modalCheckC').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(id, documento, cliente, placa) {
      $("#modalCheckC").css("z-index", "-1");
      document.getElementById("errorMsgs").innerHTML = "";
      let me = this;
      me.id = id;
      me.documento = documento;
      me.cliente = cliente;
      me.placa = placa;

      me.detalles = [];
      me.arrRptas = [];
      let response = await axios({
        method: "post",
        url: "/getcheckcalidad",
        data: {
          _token: me.token,
          ordenId: me.id,
        },
      });
      me.detalles = response.data.detalles;
      me.marcados = response.data.rptas;
      me.observacion = response.data.observaciones;

      me.cantElementos = 0;
      me.detalles.forEach((element) => {
        me.cantElementos += element.cantSubs;
      });

      // console.log(me.marcardos)
      // me.arreglo2 = response.data.encabezados
      // me.opciones = response.data.opciones02
      // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      window.setTimeout(function () {
        if (me.marcados.length != 0) {
          me.marcarPorDefault();
        }
      }, 500);

      $("#modalCheckC").modal({ backdrop: "static", show: true, keyboard: false });
      $("#modalCheckC").css("z-index", "1500");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1')     
    },
    marcarPorDefault() {
      let me = this;
      me.arrRptas = me.marcados;
      // console.log('Arr', me.arrRptas)
      me.arrRptas.forEach((element) => {
        if (element.valor == "S") {
          document.getElementById("chkBueno_" + element.id).checked = true;
        } else {
          document.getElementById("chkMalo_" + element.id).checked = true;
        }

        document.getElementById("observaciones" + element.id).value =
          element.observacion;
      });
    },
    imprimir() {
      window.open(`/getcheckcalidad/${this.id}`, "_blank");
    },
    async guardarCampos() {
      // alert('Ok')
      let cantidad = this.arrRptas != null ? this.arrRptas.length : 0;
      let obs = document.getElementById("observaciones").value;

      let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
      let button = document.getElementById("btnGuardar");
      button.innerHTML = icon + " Cargando...";
      button.disabled = true;

      if (this.cantElementos == cantidad) {
        let response = await axios({
          method: "post",
          url: "/guardarcheckcalidad",
          data: {
            _token: this.token,
            rptas: JSON.stringify(this.arrRptas),
            ordenid: this.id,
            observacion: obs,
          },
          cache: false,
        });

        var arreglo = response.data.errores;
        let cadena_errors = "";
        Object.values(arreglo).forEach((val) => {
          cadena_errors += val + ", ";
        });

        document.getElementById("errorMsgs").innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
          cadena_errors.slice(0, -1) +
          "</strong></a></div ></div>";

        window.setTimeout(function () {
          $("#modalCheckC").modal("hide");
          $("body").removeClass("modal-open");
          $(".modal-backdrop").remove();
        }, 1000);
      } else {
        document.getElementById("errorMsgs").innerHTML =
          '<div style = "margin-top:10px; margin-left:42px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Marque todos los Campos del CheckList, le faltan ' +
          parseInt(this.cantElementos - cantidad) +
          " Respuestas</strong></a></div ></div>";
      }

      button.innerHTML = icon + " Guardar";
      button.disabled = false;
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
@media (min-width: 576px) {
  #tabla-inv {
    overflow-x: scroll;
  }
}

@media (min-width: 768px) {
  #tabla-inv {
    overflow-x: hidden;
  }
}

@media (min-width: 992px) {
  #tabla-inv {
    overflow-x: hidden;
  }
}

@media (min-width: 1200px) {
  #tabla-inv {
    overflow-x: hidden;
  }
}

.modal-header {
  border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
  border-top: 1px solid #f2f2f2;
}
.no-resize {
  resize: none;
}
.chkB:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: #d1d3d1;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
  cursor: pointer;
}

.chkB:checked:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: green;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
}
.chkM:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: #d1d3d1;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
  cursor: pointer;
}

.chkM:checked:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: red;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
}

.no-resize {
  resize: none;
}
</style>