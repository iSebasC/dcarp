<template>
  <div
    class="modal fade"
    id="modalCliente"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalClienteLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <form
          class="form"
          :id="'formEnvModal_' + entidad"
          method="POST"
          action="/guardarcliente"
        >
          <div class="modal-header">
            <h5 class="modal-title" id="modalClienteLabel">
              Datos del Cliente
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
            <input
              type="hidden"
              :value="this.$store.state.token"
              name="_token"
            />
            <div class="row mt-1">
              <div class="col-md-4 col-xs-12">
                <div class="form-group">
                  <label :for="'documento_' + entidad"
                    >Documento <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'documento_' + entidad"
                    class="form-control form-control-sm"
                    name="documento"
                    maxlength="11"
                    minlength="8"
                    :readonly="tipoRegs != 3 && documento != ''"
                    @keypress="soloNumeros($event)"
                  />
                </div>
              </div>

              <div class="col-md-2 mx-0 px-0 col-xs-12">
                <div class="content-buttons btn-group-vertical my-md-4">
                  <button
                    v-if="tipoRegs == 3"
                    title="Buscar Cliente"
                    @click="changeValorDocumento()"
                    class="btn btn-sm btn-info"
                    type="button"
                    style="margin-top: 5px"
                  >
                    <i class="mdi mdi-magnify icon-size"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
              <div v-if="bandTipoCliente" class="col-md-8 col-xs-12">
                <div class="form-group">
                  <label :for="'razonsocial_' + entidad"
                    >Razón Social <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :id="'razonsocial_' + entidad"
                    :value="cliente != null ? cliente.razonSocial : ''"
                    class="form-control form-control-sm"
                    name="razonsocial"
                    maxlength="255"
                    @keypress="soloLetras($event)"
                  />
                </div>
              </div>

              <div v-if="!bandTipoCliente" class="col-md-6 col-xs-12">
                <div class="form-group">
                  <label :for="'apellidos_' + entidad"
                    >Apellidos <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :value="cliente != null ? cliente.apellidos : ''"
                    :id="'apellidos_' + entidad"
                    class="form-control form-control-sm"
                    name="apellidos"
                    maxlength="255"
                    @keypress="soloLetras($event)"
                  />
                </div>
              </div>

              <div v-if="!bandTipoCliente" class="col-md-6 col-xs-12">
                <div class="form-group">
                  <label :for="'nombres_' + entidad"
                    >Nombres <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    :value="cliente != null ? cliente.nombres : ''"
                    :id="'nombres_' + entidad"
                    class="form-control form-control-sm"
                    name="nombres"
                    maxlength="255"
                    @keypress="soloLetras($event)"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-xs-12">
                <div class="form-group">
                  <label :for="'direccion_' + entidad"
                    >Dirección <span class="text-danger">*</span></label
                  >
                  <textarea
                    :value="cliente != null ? cliente.direccion : ''"
                    :id="'direccion_' + entidad"
                    class="form-control form-control-sm no-resize"
                    name="direccion"
                    maxlength="255"
                    rows="2"
                    @keypress="soloLetrasNumeros($event)"
                  ></textarea>
                </div>
              </div>
            </div>

            <hr />
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <div class="form-group">
                  <label :for="'telefono_' + entidad"
                    >Celular <span class="text-success">*</span></label
                  >
                  <input
                    type="text"
                    :value="cliente != null ? cliente.telefono : ''"
                    :id="'telefono_' + entidad"
                    class="form-control form-control-sm"
                    name="telefono"
                    maxlength="9"
                    minlength="6"
                    @keypress="soloNumeros($event)"
                  />
                </div>
              </div>

              <div class="col-md-8 col-xs-12">
                <div class="form-group">
                  <label :for="'correo_' + entidad"
                    >Correo Electrónico
                    <span class="text-success">*</span></label
                  >
                  <input
                    type="text"
                    :value="cliente != null ? cliente.correoElectronico : ''"
                    :id="'correo_' + entidad"
                    class="form-control form-control-sm"
                    name="correo"
                    maxlength="255"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12" :id="'data-error-modal_' + entidad"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger btn-sm"
              @click="cerrarModal"
            >
              <i class="mdi mdi-close icon-size"></i> Cancelar
            </button>

            <button
              type="button"
              :id="'btnEnvio_' + entidad"
              @click="
                enviarFormModalActCliente(
                  'modalCliente',
                  entidad,
                  'CLI',
                  tipoRegs
                )
              "
              class="btn btn-success btn-sm"
            >
              <i class="mdi mdi-check-bold icon-size"></i> {{ nombreBtn }}
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
  name: "Cliente",
  mixins: [misMixins],
  data() {
    return {
      documento: this.$attrs.documento,
      bandTipoCliente: true,
      cliente: null,
      // url: '/Cliente',
      token: this.$store.state.token,
      nombreBtn: "Guardar",
      entidad: "cliente",
      tipoRegs: 0,
    };
  },
  methods: {
    async changeValorDocumento() {
      let me = this;
      me.documento = document.getElementById(`documento_${this.entidad}`).value;
      // if (this.documento.length == 11) {
      //     this.bandTipoCliente = true
      // } else {
      //     this.bandTipoCliente = false
      // }

      if (me.documento != "") {
        document.getElementById("data-error-modal_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cargando...</strong></a></div ></div>';

        let response = await axios({
          method: "post",
          url: "/getcliente/" + me.documento,
          data: {
            _token: me.token,
          },
        });
        if (response.data.estado) {
          me.cliente = response.data.cliente;
          me.nombreBtn = "Actualizar";
        } else {
          let res = await me.consultarApi(
            me.documento,
            me.documento.length == 8 || me.documento.length == 9 ? 1 : 2
          );
          let cl = res.data;
          // console.log('resp', cl);
          if (cl.success) {
            let client = cl.data;
            // console.log(cl);
            if (me.documento.length == 11) {
              me.cliente = {
                documento: me.documento,
                razonSocial: client.nombre_o_razon_social,
                direccion: client.direccion_completa,
              };
            } else {
              me.cliente = {
                documento: me.documento,
                apellidos:
                  client.apellido_paterno + " " + client.apellido_materno,
                nombres: client.nombres,
              };
            }
          }
          // console.log('cliente', me.cliente);

          // me.cliente = null
          me.nombreBtn = "Guardar";
        }

        let tipo = me.documento.length;
        me.bandTipoCliente = true;
        if (tipo == 8 || tipo == 9) {
          me.bandTipoCliente = false;
        }

        document.getElementById("data-error-modal_" + me.entidad).innerHTML =
          "";
      }
    },
    setFocus: function () {
      // Note, you need to add a ref="input" attribute to your input.
      this.$refs.campocliente.$el.focus();
    },
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalCliente').modal('toggle')
      $('#modalCliente').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(attr, tipo) {
      $("#modalCliente").css("z-index", "-1");
      let me = this;
      let flagRender = false;
      me.documento = attr;
      me.cliente = null;
      me.tipoRegs = 0;
      if (me.documento != "") {
        let response = await axios({
          method: "post",
          url: "/getcliente/" + me.documento,
          data: {
            _token: me.token,
          },
        });
        if (response.data.estado) {
          me.cliente = response.data.cliente;
          me.tipoRegs = 3;
          me.nombreBtn = "Actualizar";
        } else {
          let res = await me.consultarApi(
            me.documento,
            me.documento.length == 8 || me.documento.length == 9 ? 1 : 2
          );
          let cl = res.data;
          // console.log('resp', cl);
          if (cl.success) {
            let client = cl.data;
            // console.log(cl);
            if (me.documento.length == 11) {
              me.cliente = {
                documento: me.documento,
                razonSocial: client.nombre_o_razon_social,
                direccion: client.direccion_completa,
              };
            } else {
              me.cliente = {
                documento: me.documento,
                apellidos:
                  client.apellido_paterno + " " + client.apellido_materno,
                nombres: client.nombres,
              };
            }
          }
          // console.log('cliente', me.cliente);

          // me.cliente = null
          me.nombreBtn = "Guardar";
        }

        let tipo = me.documento.length;
        me.bandTipoCliente = true;
        if (tipo == 8 || tipo == 9) {
          me.bandTipoCliente = false;
        }
        flagRender = true;
      } else if (tipo == 3) {
        me.nombreBtn = "Registrar";
        flagRender = true;
        this.bandTipoCliente = false;
        me.tipoRegs = tipo;
      }

      if (flagRender) {
        document.getElementById(`documento_${me.entidad}`).value = me.documento;
        document.getElementById("data-error-modal_" + me.entidad).innerHTML =
          "";
        $("#modalCliente").modal({ backdrop: "static", show: true, keyboard: false });
        $("#modalCliente").css("z-index", "1500");
        $(".modal-backdrop").css("z-index", "1");
        $('#header-sec').css('z-index','1')
        $('#aside-sec').css('z-index','1')

      }
      // }
      // $('#exampleModal').on('shown.bs.modal', function () {
      //     $('#myInput').trigger('focus')
      // })
    },
  },
  created() {
    let me = this;
    me.arreglo = [];
    me.arreglo2 = [];
    me.opciones = "";
  },
  activated: function () {
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    // setTimeout(x => {
    //      // just watch out with going too fast !!!
    // }, 1000);
  },
  deactivated: function () {},
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
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