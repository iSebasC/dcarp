<template>
  <div
    class="modal fade"
    id="modalVerificarO"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalVerificarLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalVerificarLabel">
            Verificación de CheckList de Calidad & Manejo
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
          <form
            class="form"
            :id="'formEnv_' + entidad"
            method="POST"
            action="/guardarverificacionchecklist"
          >
            <div class="row">
              <div class="col-md-8 ml-2">
                <h6>
                  <mark>N° de Orden: </mark><strong v-text="documento"></strong>
                </h6>
                <h6><mark>Placa: </mark><strong v-text="placa"></strong></h6>
                <h6>
                  <mark>Cliente: </mark><strong v-text="cliente"></strong>
                </h6>
                <input type="hidden" name="idOrden" :value="id" />
                <input type="hidden" name="id" :value="idVerificacion" />
              </div>
            </div>

            <div class="row">
              <div class="col-md-10 col-xs-12">
                <div class="row d-flex justify-content-center my-2">
                  <div class="col-md-5 col-xs-6 mt-1">
                    <h6 class="title-estrella">¿Se completó CheckList de Calidad?</h6>
                  </div>
                 
                  <div class="col-md-4 col-xs-6 mt-1">
                    <input type="radio" name="rptaCheckCalidad"
                      value="S" id="input-radio-rptaS"
                    />
                    <label for="input-radio-rptaS">Si</label>
                    <input type="radio" class="ml-3"
                      name="rptaCheckCalidad" value="N" id="input-radio-rptaN"
                    />
                    <label for="input-radio-rptaN">No</label>
                  </div>
                </div>

                <div class="row d-flex justify-content-center mb-1">
                  <div class="col-md-5 col-xs-6 mt-1">
                    <h6 class="title-estrella">¿Se completó CheckList de Manejo?</h6>
                  </div>
                  <div class="col-md-4 col-xs-6 mt-1">
                    <input type="radio" name="rptaCheckManejo"
                      value="S" id="input-radio-rptaCS"
                    />
                    <label for="input-radio-rptaCS">Si</label>
                    <input type="radio" class="ml-3"
                      name="rptaCheckManejo" value="N" id="input-radio-rptaCN"
                    />
                    <label for="input-radio-rptaCN">No</label>
                  </div>
                </div>

                <div class="row d-flex justify-content-center mb-1">
                  <div class="col-md-3 col-xs-6 mt-1">
                    <h6 class="title-estrella">Observaciones <span class="text-success">*</span></h6>
                  </div>
                  <div class="col-md-6 col-xs-6 mt-1">
                    <div class="form-group">
                      <textarea
                        :id="'observaciones_'+entidad"
                        rows="4"
                        class="form-control form-control-sm no-resize"
                        name="observaciones"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-10" id="errorMsgs04_check"></div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                :id="`btnGuardar_${entidad}`"
                @click="guardarCamposFormCheckList()"
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
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "VerificarCheckList",
  mixins: [misMixins],
  components: {},
  data() {
    return {
      checkDefault: true,
      id: 0,
      idVerificacion: 0,
      cliente: "",
      telefono: "",
      correo_electronico: "",
      placa: "",
      documento: "",
      entidad: "verificar_checksList",
    };
  },
  methods: {
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();

      $('#modalVerificarO').modal('toggle')
      $('#modalVerificarO').css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async showModal(id, documento, cliente, placa) {
      let me = this;
      me.id = id;
      me.documento = documento;
      me.cliente = cliente;
      me.placa = placa;
      me.checkDefault = true;
      await me.getOpcionesModal(id)
      $("#modalVerificarO").css("z-index", "-1");
      document.getElementById("errorMsgs04_check").innerHTML = "";
      // me.arreglo2 = response.data.encabezados
      // me.opciones = response.data.opciones02
      // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      $("#modalVerificarO").modal({ backdrop: "static", show: true, keyboard: false });
      $("#modalVerificarO").css("z-index", "1500");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1')    
    },
    async guardarCamposFormCheckList() {
      let me = this;
      let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
      // alert(url)
      let button = document.getElementById(`btnGuardar_${me.entidad}`);
      button.innerHTML = icon + " Cargando...";
      button.disabled = true;
      var form = document.getElementById(`formEnv_${me.entidad}`);
      let data = me.serializeForm(form);
      // console.log('data',data)
      // alert(form.action+'-'+form.method)
      // let formData = new FormData(form)
      // console.log("form", form);
      // console.log('DATA',formData)
      let response = await axios({
        method: form.method,
        url: form.action,
        data: data,
      });

      var arreglo = response.data.errores;
      let cadena_errors = "";
      Object.values(arreglo).forEach((val) => {
        cadena_errors += val + ", ";
      });

      if (response.data.estado == false) {
        document.getElementById("errorMsgs04_check").innerHTML =
          '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
          cadena_errors.slice(0, -2) +
          "</a></div ></div >";
      } else {
        document.getElementById("errorMsgs04_check").innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss="alert">&times;</button><strong>' +
          cadena_errors.slice(0, -1) +
          "</strong></a></div ></div>";
        this.$parent.$emit("listarOrdenesFinalizadas");
        window.setTimeout(function () {
          $("#modalVerificarO").modal("hide");
          $("body").removeClass("modal-open");
          $(".modal-backdrop").remove();
        }, 1000);
      }
      button.disabled = false;
      button.innerHTML = icon + " Guardar";
    },
    async getOpcionesModal (idOrden) {
      let response = await axios.get(`/getverificacionchecks/${idOrden}`);
      let r = response.data
      if (r.estado) {
        let objVerificacion = r.verificacion
        this.idVerificacion = objVerificacion.id
        if (objVerificacion.rptaVerifCheckCalidad == 'S') {
          document.getElementById(`input-radio-rptaS`).checked = this.checkDefault
        } else {
          document.getElementById(`input-radio-rptaN`).checked = this.checkDefault
        }

        if (objVerificacion.rptaVerifCheckManejo == 'S') {
          document.getElementById(`input-radio-rptaCS`).checked = this.checkDefault
        } else {
          document.getElementById(`input-radio-rptaCN`).checked = this.checkDefault
        }

        document.getElementById(`observaciones_${this.entidad}`).value = objVerificacion.observaciones
        // console.log('r', r.verificacion)

      } else {
        this.idVerificacion = "0"
        document.getElementById(`input-radio-rptaN`).checked = this.checkDefault
        document.getElementById(`input-radio-rptaCN`).checked = this.checkDefault
        document.getElementById(`observaciones_${this.entidad}`).value = ""
      }
    }
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
.no-resize {
  resize: none;
}
.title-estrella {
  font-weight: 500;
  /* font-size: 14px; */
}
.label-estrella {
  color: grey;
  font-size: 27px;
  cursor: pointer;
}
.clasificacion-estrellas {
  direction: rtl; /* right to left */
  unicode-bidi: bidi-override; /* bidi de bidireccional */
}

input[type="radio"] {
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
  border-radius: 50%;
}

/* appearance for checked radiobutton */
input[type="radio"]:checked {
  background-color: teal;
  cursor: pointer;
}
</style>