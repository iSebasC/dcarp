<template>
  <div
    class="modal fade"
    id="modalCheckT"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalCheckTLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCheckTLabel">CheckList de Taller</h5>
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
                <mark>N° de Orden: </mark><strong v-text="documento"></strong>
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
                  <!-- <th
                    class="text-center"
                    style="width: 20px; vertical-align: middle"
                  >
                    Según<br />Indicación
                  </th>
                  <th
                    class="text-center"
                    style="width: 20px; vertical-align: middle"
                  >
                    25,35,<br />45,55,<br />65,75,<br />85,95
                  </th>
                  <th
                    class="text-center"
                    style="width: 20px; vertical-align: middle"
                  >
                    10,30,<br />50,70,<br />90
                  </th>
                  <th
                    class="text-center"
                    style="width: 20px; vertical-align: middle"
                  >
                    20,40,<br />60,80,<br />100
                  </th> -->
                  <th
                    class="text-center"
                    style="width: 400px; vertical-align: middle"
                  >
                    C = Cambiar &nbsp;&nbsp;&nbsp;&nbsp; I= Inspeccionar
                  </th>
                  <th class="text-center" style="vertical-align: middle">Ok</th>
                  <th class="text-center" style="vertical-align: middle">
                    No Ok
                  </th>
                  <th class="text-center" style="vertical-align: middle">
                    Corregido
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item2, index) in detalles" :key="index">
                  <!-- <td style="width: 20px; vertical-align: middle">
                    <input
                      type="text"
                      class="form-control text-center input-md input-may"
                      :name="'indicacion' + item2.id"
                      :id="'indicacion' + item2.id"
                      placeholder="C"
                      @keypress="soloCL($event)"
                      maxlength="1"
                      @change="obtenerValorText(item2.id)"
                    />
                  </td>
                  <td style="width: 20px; vertical-align: middle">
                    <input
                      type="text"
                      class="form-control input-md input-may"
                      :name="'indicacion1' + item2.id"
                      :id="'indicacion1' + item2.id"
                      placeholder="C"
                      @keypress="soloCL($event)"
                      maxlength="1"
                      @change="obtenerValorText(item2.id)"
                    />
                  </td>
                  <td style="width: 20px; vertical-align: middle">
                    <input
                      type="text"
                      class="form-control input-md input-may"
                      :name="'indicacion2' + item2.id"
                      :id="'indicacion2' + item2.id"
                      placeholder="C"
                      @keypress="soloCL($event)"
                      maxlength="1"
                      @change="obtenerValorText(item2.id)"
                    />
                  </td>
                  <td style="width: 20px; vertical-align: middle">
                    <input
                      type="text"
                      class="form-control input-md input-may"
                      :name="'indicacion3' + item2.id"
                      :id="'indicacion3' + item2.id"
                      placeholder="C"
                      @keypress="soloCL($event)"
                      maxlength="1"
                      @change="obtenerValorText(item2.id)"
                    />
                  </td> -->

                  <td style="width: 400px; vertical-align: middle">
                    <h6
                      style="text-align: justify; text-justify: inter-word"
                      v-text="item2.nombre"
                    ></h6>
                  </td>
                  <td class="text-center" style="vertical-align: middle">
                    <input
                      class="mr-1 chkB"
                      @change="obtenerValor(item2.id, 'S')"
                      type="radio"
                      :name="'chkSituacion_' + item2.id"
                      :id="'chkBueno_' + item2.id"
                    />
                  </td>
                  <td class="text-center" style="vertical-align: middle">
                    <input
                      class="mr-1 chkM"
                      @change="obtenerValor(item2.id, 'N')"
                      type="radio"
                      :name="'chkSituacion_' + item2.id"
                      :id="'chkMalo_' + item2.id"
                    />
                  </td>
                  <td class="text-center" style="vertical-align: middle">
                    <input
                      class="mr-1 chkC"
                      @change="obtenerValor(item2.id, 'C')"
                      type="radio"
                      :name="'chkSituacion_' + item2.id"
                      :id="'chkCorregido_' + item2.id"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="row mt-3 ml-2">
            <div class="col-md-5">
              <div class="form-group">
                <label for="observaciones_chkT"
                  >Observaciones <span class="text-success">*</span></label
                >
                <textarea
                  id="observaciones_chkT"
                  rows="4"
                  class="form-control form-control-sm no-resize"
                  name="observaciones_chkT"
                  v-text="observacion"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8" id="errorMsgs03"></div>
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
  name: "CheckListTaller",
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
    obtenerValor(id, valor) {
      // let e = document.getElementById("indicacion" + id).value;
      // let e1 = document.getElementById("indicacion1" + id).value;
      // let e2 = document.getElementById("indicacion2" + id).value;
      // let e3 = document.getElementById("indicacion3" + id).value;

      let me = this;
      let band = false;
      if (me.arrRptas != null) {
        me.arrRptas.forEach((element) => {
          if (element.id == id) {
            band = true;
            element.valor = valor;
            // element.indicacion = e;
            // element.indicacion1 = e1;
            // element.indicacion2 = e2;
            // element.indicacion3 = e3;
          }
        });
      } else {
        me.arrRptas = [];
      }

      if (!band) {
        me.arrRptas.push({
          id: id,
          valor: valor,
          // indicacion: e,
          // indicacion1: e1,
          // indicacion2: e2,
          // indicacion3: e3,
        });
      }
    },
    obtenerValorText(id) {
      let e = document.getElementById("indicacion" + id).value;
      let e1 = document.getElementById("indicacion1" + id).value;
      let e2 = document.getElementById("indicacion2" + id).value;
      let e3 = document.getElementById("indicacion3" + id).value;

      let me = this;
      let band = false;

      if (me.arrRptas != null) {
        me.arrRptas.forEach((element) => {
          if (element.id == id) {
            band = true;
            element.indicacion = e;
            element.indicacion1 = e1;
            element.indicacion2 = e2;
            element.indicacion3 = e3;
          }
        });
      } else {
        me.arrRptas = [];
      }

      if (!band) {
        me.arrRptas.push({
          id: id,
          valor: "N",
          // indicacion: e,
          // indicacion1: e1,
          // indicacion2: e2,
          // indicacion3: e3,
        });
      }
    },
    imprimir() {
      window.open(`/getchecktaller/${this.id}`, "_blank");
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalCheckT').modal('toggle')
      $('#modalCheckT').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(id, documento, cliente, placa) {
      let me = this;
      me.id = id;
      me.documento = documento;
      me.cliente = cliente;
      me.placa = placa;

      me.detalles = [];
      me.arrRptas = [];
      me.cantElementos = 0;

      $("#modalCheckT").css("z-index", "-1");
      document.getElementById("errorMsgs03").innerHTML = "";

      let response = await axios({
        method: "post",
        url: "/getchecktaller",
        data: {
          _token: me.token,
          ordenId: me.id,
        },
      });
      me.detalles = response.data.detalles;
      me.marcados = response.data.rptas;
      me.observacion = response.data.observaciones;
      me.cantElementos = me.detalles.length;
      // console.log(me.marcardos)
      window.setTimeout(function () {
        if (me.marcados.length != 0) {
          me.marcarPorDefault();
        }
      }, 500);

      // me.arreglo2 = response.data.encabezados
      // me.opciones = response.data.opciones02
      // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      $("#modalCheckT").modal({ backdrop: "static", show: true, keyboard: false });
      $("#modalCheckT").css("z-index", "1500");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1') 
    },
    marcarPorDefault() {
      let me = this;
      me.arrRptas = me.marcados;
      me.arrRptas.forEach((element) => {
        // console.log('e: ', element)
        if (element.valor == "S") {
          document.getElementById("chkBueno_" + element.id).checked = true;
        } else if (element.valor == "N") {
          document.getElementById("chkMalo_" + element.id).checked = true;
        } else {
          document.getElementById("chkCorregido_" + element.id).checked = true;
        }

        // document.getElementById("indicacion" + element.id).value =
        //   element.indicacion;
        // document.getElementById("indicacion1" + element.id).value =
        //   element.indicacion1;
        // document.getElementById("indicacion2" + element.id).value =
        //   element.indicacion2;
        // document.getElementById("indicacion3" + element.id).value =
        //   element.indicacion3;
      });
    },
    async guardarCampos() {
      // alert('Ok')
      // console.log('firma', this.firmaDigital)
      let cantidad = this.arrRptas != null ? this.arrRptas.length : 0;
      let obs = document.getElementById("observaciones_chkT").value;

      let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
      let button = document.getElementById("btnGuardar");
      button.innerHTML = icon + " Cargando...";
      button.disabled = true;

      console.log("cantidad", cantidad);
      console.log("cantElementos", this.cantElementos);

      if (this.cantElementos == cantidad) {
        let response = await axios({
          method: "post",
          url: "/guardarchecktaller",
          data: {
            rptas: JSON.stringify(this.arrRptas),
            _token: this.token,
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

        document.getElementById("errorMsgs03").innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' +
          cadena_errors.slice(0, -1) +
          "</strong></a></div ></div>";

        window.setTimeout(function () {
          $("#modalCheckT").modal("hide");
          $("body").removeClass("modal-open");
          $(".modal-backdrop").remove();
        }, 1000);
      } else {
        document.getElementById("errorMsgs03").innerHTML =
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
.chkC:after {
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

.chkC:checked:after {
  width: 15px;
  height: 15px;
  border-radius: 15px;
  top: -2px;
  left: -1px;
  position: relative;
  background-color: #0e5cb9;
  content: "";
  display: inline-block;
  visibility: visible;
  border: 2px solid white;
}

.input-may {
  text-transform: uppercase;
}
</style>