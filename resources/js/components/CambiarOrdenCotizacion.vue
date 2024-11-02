<template>
  <div
    class="modal fade"
    :id="`formMant_${entidad}`"
    tabindex="-1"
    aria-hidden="true"
    role="dialog"
  >
    <div class="modal-dialog modal-xl">
      <!-- modal-dialog-scrollable -->
      <div class="modal-content">
        <!-- overflow-auto -->
        <form action="" :id="`form_${entidad}`" onsubmit="return false;">
          <div class="modal-header">
            <h5 class="modal-title">
              Actualizar Órdenes de Trabajo | Cotizaciones
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
              <div
                class="
                  col-xs-12 col-md-7 col-xl-9
                  width-auto
                  overflow-auto
                  border-right
                "
              >
                <div class="row mt-xl-2 mb-3">
                  <label
                    for="filtro-bq"
                    class="col-md-2 col-xl-1 col-form-label py-0 my-auto"
                    >Buscar:
                  </label>
                  <div class="col-md-5 col-xl-3">
                    <input
                      type="search"
                      autocomplete="off"
                      class="form-control form-control-sm"
                      :id="`busqueda_${entidad}`"
                      placeholder=""
                      @keyup.enter="getBusqueda()"
                    />
                  </div>
                  <div class="col-xs-12 col-xl-1 col-md-1 mt-xl-0">
                    <div class="btn-group" role="group">
                      <button
                        type="button"
                        class="btn btn-sm btn-info"
                        @click="getBusqueda()"
                        title="Buscar"
                      >
                        <i class="mdi mdi-magnify icon-size"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-xs-12 col-xl-4 col-md-4 px-0">
                    <div :id="'data-error-modal_' + entidad"></div>
                  </div>
                </div>
                <div class="row product-grid">
                  <div
                    class="col-xs-12 col-md-4 col-xl-4"
                    v-for="(item, index) in listProductos"
                    :key="index"
                  >
                    <div class="card cursor-pointer">
                      <div class="card-body">
                        <div class="card-title cursor-pointer title-render">
                          <div class="input-group-text input-cs">
                            <input
                              type="checkbox"
                              class="mr-2 cursor-pointer"
                              :id="`id_prod_${item.id}`"
                              :value="item.id"
                              @change="addElement(item)"
                            />
                            <label
                              :for="`id_prod_${item.id}`"
                              class="form-check-label cursor-pointer"
                              v-text="item.numero"
                            ></label>
                          </div>
                        </div>
                        <div class="card-text">
                          <p class="small">
                            <strong>Doc. Cliente:</strong>
                            <span v-text="item.documento"></span> <br />
                            <strong>Cliente:</strong>
                            <span v-text="item.cliente"></span> <br />
                            <strong>Fecha:</strong>
                            <span v-text="item.fecha"></span> <br />
                            <strong>Tipo:</strong>
                            <span v-text="item.tipo"></span>
                          </p>
                        </div>
                        <div class="clearfix">
                          <p class="mb-0 float-start">
                            <span>
                              S/ <strong v-text="item.total"></strong>
                            </span>
                          </p>
                          <p class="mb-0 float-end fw-bold"></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 mt-2">
                    <div :id="`formError_${entidad}`"></div>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-md-5 col-xl-3">
                <h5 class="card-title small pt-2">Detalles Agregados</h5>
                <div class="pb-1 todo-item width-auto-add overflow-auto">
                  <div id="todo-container">
                    <div
                      class="input-group mt-2"
                      v-for="(item, index) in seleccionados"
                      :key="index"
                    >
                      <input
                        type="text"
                        class="form-control form-control-sm f-xs-size"
                        :aria-label="
                          item.numero +
                          ' | ' +
                          item.fecha +
                          ' | Tipo:' +
                          item.tipo
                        "
                        :value="
                          item.numero +
                          ' | ' +
                          item.fecha +
                          ' | Tipo:' +
                          item.tipo
                        "
                      />

                      <button
                        class="
                          btn btn-outline-secondary
                          bg-danger
                          btn-sm
                          text-white
                        "
                        type="button"
                        @click="deleteItem(item)"
                      >
                        x
                      </button>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-xs-12">
                    <div class="form-group">
                      <label :for="'documento_' + entidad"
                        >Asignar a <span class="text-danger">*</span></label
                      >
                      <vue-typeahead-bootstrap
                        :id="'documento_' + entidad"
                        :ieCloseFix="false"
                        inputClass="form-control-sm"
                        v-model="query"
                        :data="personas"
                        :serializer="(item) => item.persona"
                        @hit="selectPersona = $event"
                        @input="buscarPersonas"
                        @keyup.delete="eliminarPersona"
                      >
                      </vue-typeahead-bootstrap>

                      <!-- <label :for="'documento_'+entidad">Documento <span class="text-danger">*</span></label>
                            <input type="text" :id="'documento_'+entidad" class="form-control form-control-sm" 
                            name="documento" maxlength="11" minlength="8" @keypress="soloNumeros($event)" @keyup.enter="busquedaProveedor"> -->
                    </div>
                    <input
                      type="hidden"
                      name="idPersona"
                      :id="'idPersona_' + entidad"
                      :value="personaId"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-sm btn-danger px-2"
              @click="cerrarModal"
            >
              <i class="mdi mdi-close icon-size"></i>
              Cerrar
            </button>
            <button
              type="button"
              class="btn btn-sm btn-success px-2"
              :id="`btnEnvio_${entidad}`"
              @click="enviarForm()"
            >
              <i class="mdi mdi-check-bold icon-size"></i>
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import { misMixins } from "../mixins/mixin.js";
import axios from "axios";

export default {
  name: "modalChangeOrdenCotizacion",
  mixins: [misMixins],
  data() {
    return {
      tienda: "",
      entidad: "mod_orden_cotizacion",
      estado: "",
      urlProductos: "/venta/getsearchordencot",
      listProductos: [],
      seleccionados: [],
      nombre: "",
      id_ref: "",
      almacen: "",
      tipoMov: "",
      title: "",
      // urlGuardarRelacion: "local/guardarrelacion",
      buttonHTML: `<i class="mdi mdi-check-bold icon-size"></i>Guardar`,
      urlSave: `/venta/actualizarordencot`,
      personas: [],
      selectPersona: null,
      personaId: 0,
      query: "",
      tipo: "V",
    };
  },
  watch: {
    query() {
      let me = this;
      if (me.selectPersona != null) {
      }
    },
    selectPersona: function () {
      let me = this;
      if (me.selectPersona != null) {
        me.personaId = me.selectPersona.id;
      } else {
        me.personaId = 0;
      }
    },
  },
  methods: {
    cerrarModal () {
      // $('.fade').remove();
      // $('body').removeClass('modal-open');
      // $('.modal-backdrop').remove();
      $(`#formMant_${this.entidad}`).modal('toggle')
      $(`#formMant_${this.entidad}`).css('z-index', '-1')
      $('#header-sec').css('z-index','')
      $('#aside-sec').css('z-index','')         
    },
    async buscarPersonas() {
      let me = this;

      let response = await axios({
        method: "POST",
        url: "/buscarpersonas",
        data: {
          query: me.query,
          tipo: me.tipo,
        },
      });
      me.personas = response.data.personas;
    },
    eliminarPersona() {
      this.selectPersona = null;
    },
    addElement(item) {
      let elm = document.getElementById(`id_prod_${item.id}`);
      if (elm.checked == true) {
        let isRegistrado = false;
        this.seleccionados.forEach((el) => {
          if (el.id == item.id) {
            isRegistrado = true;
          }
        });
        if (!isRegistrado) {
          this.seleccionados.push(item);
        } else {
          toastr.error("Elemento ya antes agregado.", "Mensaje del Sistema", {
            closeMethod: "slideUp",
            closeDuration: 200,
            hideDuration: 1000,
            timeOut: 2000,
            extendedTimeOut: 1000,
            closeButton: true,
            showEasing: "swing",
            closeEasing: "linear",
            preventDuplicates: true,
            progressBar: true,
            newestOnTop: false,
            positionClass: "toast-bottom-right",
            hideMethod: "fadeOut",
          });
        }
      }
    },
    deleteItem(item) {
      let pos = -1;
      this.seleccionados.forEach((el, index) => {
        if (el.id == item.id) {
          pos = index;
        }
      });

      if (pos != -1) {
        this.seleccionados.splice(pos, 1);
        let elem = document.getElementById(`id_prod_${item.id}`);
        if (elem != null) {
          elem.checked = false;
        }
      }
    },
    async mostrarModal(almacen, tipoMov) {
      this.almacen = almacen;
      this.tipoMov = tipoMov;
      this.seleccionados = [];
      this.listProductos = [];
      this.query = "";
      this.selectPersona = null;
      this.personaId = 0;
      //   this.title = `${this.tipoMov == "E" ? "Entrada" : "Salida"} de Productos`;
      document.getElementById(`busqueda_${this.entidad}`).value = "";
      $(`#formMant_${this.entidad}`).modal({ backdrop: "static", show: true,  keyboard: false });
      $(`#formMant_${this.entidad}`).css("z-index", "1500");
      $(".modal-backdrop").css("z-index", "1");
      $('#header-sec').css('z-index','1')
      $('#aside-sec').css('z-index','1')      
      //   const myModal = new bootstrap.Modal( 42293220
      //     document.getElementById(`formMant_${this.entidad}`),
      //     { backdrop: "static" }
      //   );
      document.getElementById(`formError_${this.entidad}`).innerHTML = "";
      document.getElementById("data-error-modal_" + this.entidad).innerHTML =
        "";
      //   myModal.show();

      //   let arrSeleccion = JSON.parse(localStorage.getItem("productos_carrito"));
      //   if (arrSeleccion != null) {
      this.seleccionados = [];
      //   }
      // this.seleccionados = JSON.parse(
      //   localStorage.getItem("productos_carrito")
      // );
    },
    async getBusqueda() {
      const formData = new FormData();
      this.listProductos = [];
      document.getElementById("data-error-modal_" + this.entidad).innerHTML =
        '<div><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cargando...</strong></a></div ></div>';

      let busqueda = document.getElementById(`busqueda_${this.entidad}`).value;
      formData.append("busqueda", busqueda);

      let response = await axios({
        method: "POST",
        url: this.urlProductos,
        data: formData,
      });

      let r = response.data;
      if (r.estado) {
        this.listProductos = r.lista;
        this.getBusquedaCarrito();
      }

      document.getElementById("data-error-modal_" + this.entidad).innerHTML =
        "";
    },
    getBusquedaCarrito() {
      this.$nextTick(() => {
        this.seleccionados.forEach((el) => {
          let iCheck = document.getElementById(`id_prod_${el.id}`);
          if (iCheck != null) {
            iCheck.checked = true;
          }
        });
      });
    },
    async enviarForm() {
      if (this.seleccionados.length > 0) {
        if (this.personaId != 0) {
          this.bloquearButton(`btnEnvio_${this.entidad}`);
          const formData = new FormData();
          formData.append("detalles", JSON.stringify(this.seleccionados));
          formData.append("asignadoA", this.personaId);
          let response = await axios({
            method: "POST",
            url: this.urlSave,
            data: formData,
          });

          let r = response.data;
          this.desbloquearButton(`btnEnvio_${this.entidad}`, this.buttonHTML);

          if (r.estado == true) {
            document.getElementById(
              "data-error-modal_" + this.entidad
            ).innerHTML =
              '<div><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Proceso Realizado con Exito</strong></a></div ></div>';

            window.setTimeout(
              function (el) {
                $(`#formMant_${el.entidad}`).modal("hide");
                $("body").removeClass("modal-open");
                $(".modal-backdrop").remove();
              },
              1000,
              this
            );
          } else {
            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
              cadena_errors += val + ", ";
            });

            document.getElementById(
              "data-error-modal_" + this.entidad
            ).innerHTML =
              '<div><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores: </strong>' +
              cadena_errors.slice(0, -2) +
              "</a></div ></div>";
          }
        } else {
          document.getElementById(
            "data-error-modal_" + this.entidad
          ).innerHTML =
            '<div><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Indique a quien va a Asignar.</strong></a></div ></div>';
        }
      } else {
        document.getElementById("data-error-modal_" + this.entidad).innerHTML =
          '<div><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Indique Detalles para continuar</strong></a></div ></div>';
      }
    },
  },
  activated: function () {},
};
</script>
<style scoped>
.ft-6 {
  font-size: 1.3rem;
}
.bx-xsm {
  font-size: 1.15rem;
}
.width-auto {
  height: 25rem;
  /* overflow: auto; */
}
.width-auto-add {
  height: 20rem;
}
.card:hover {
  background-color: #fffae7;
}
.input-cs {
  background-color: transparent;
  border: none;
}
.f-xs-size {
  font-size: 0.675rem;
}

.h-15 {
  height: 15rem;
}
.title-render {
  width: 14.3rem !important;
  overflow: hidden;
}
.modal-header {
  border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
  border-top: 1px solid #f2f2f2;
}
.border-right {
  border-right: 1px solid #f2f2f2 !important;
}
</style>
