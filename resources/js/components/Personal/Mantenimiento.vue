<template>
  <div class="content-wrapper" :id="'sec-personal_' + entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item">
                <router-link tag="a" to="/personal">Personal</router-link>
              </li>
              <li class="breadcrumb-item active">{{ accion }} Personal</li>
            </ol>
          </div>
        </div>
        <h4 class="content-header-title mb-0 mt-1">{{ accion }} Personal</h4>
      </div>
    </div>
    <div class="content-body">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <div class="row match-height">
          <div class="col-md-11 col-xs-12">
            <div class="card">
              <!-- <div class="card-header pb-1">
                <h4 class="card-title" id="basic-layout-form">
                  <i class="la la-users"></i> Datos de Personal
                </h4>
              </div> -->
              <div class="card-content collapse show">
                <div class="card-body">
                  <form
                    class="form"
                    :id="'formEnv_' + entidad"
                    method="POST"
                    action="/guardarpersonal"
                  >
                    <input type="hidden" :value="this.id" name="id" />
                    <input
                      type="hidden"
                      :value="this.$store.state.token"
                      name="_token"
                    />

                    <div class="form-body">
                      <div class="row">
                        <div class="col-md-2 col-xs-12">
                          <div class="form-group">
                            <label :for="'dni_' + entidad"
                              >DNI <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              :id="'dni_' + entidad"
                              class="form-control form-control-sm"
                              name="dni"
                              maxlength="8"
                              minlength="8"
                              @keypress="soloNumeros($event)"
                            />
                          </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'apellidos_' + entidad"
                              >Apellidos
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              :id="'apellidos_' + entidad"
                              class="form-control form-control-sm"
                              name="apellidos"
                              maxlength="255"
                              @keypress="soloLetras($event)"
                            />
                          </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'nombres_' + entidad"
                              >Nombres <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              :id="'nombres_' + entidad"
                              class="form-control form-control-sm"
                              name="nombres"
                              maxlength="255"
                              @keypress="soloLetras($event)"
                            />
                          </div>
                        </div>

                        <div class="col-md-2 col-xs-12">
                          <div class="form-group">
                            <label :for="'telefono_' + entidad"
                              >Teléfono
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              :id="'telefono_' + entidad"
                              @keypress="soloNumeros($event)"
                              class="form-control form-control-sm"
                              name="telefono"
                              maxlength="9"
                              minlength="6"
                            />
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <label :for="'genero_' + entidad" class="ml-1"
                          >Género <span class="text-danger">*</span></label
                        >
                        <div
                          class="col-md-4 col-xs-4 mt-3"
                          :id="'genero_' + entidad"
                        >
                          <input
                            type="radio"
                            name="genero"
                            :checked="genero == 'M' || genero == ''"
                            value="M"
                            id="input-radio-genM"
                          />
                          <label for="input-radio-genM">Masculino</label>

                          <input
                            type="radio"
                            class="ml-3"
                            name="genero"
                            :checked="genero == 'F'"
                            value="F"
                            id="input-radio-genF"
                          />
                          <label for="input-radio-genF">Femenino</label>
                        </div>

                        <div class="col-md-3 col-xs-12">
                          <div class="form-group">
                            <label :for="'fechaNacimiento_' + entidad"
                              >Fecha de Nacimiento
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="date"
                              :id="'fechaNacimiento_' + entidad"
                              class="form-control form-control-sm text-center"
                              name="fechaNacimiento"
                            />
                          </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'correo_' + entidad"
                              >Correo Electrónico
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="email"
                              :id="'correo_' + entidad"
                              class="form-control form-control-sm"
                              name="correo"
                              maxlength="255"
                            />
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'select_departamento_' + entidad"
                              >Departamento
                              <span class="text-danger">*</span></label
                            >
                            <select
                              name="select_departamento"
                              class="custom-select custom-select-sm col-10"
                              :id="'select_departamento_' + entidad"
                              @change="getProvincias"
                            >
                              <option value="" selected="" disabled="">
                                Seleccione
                              </option>
                              <option
                                v-for="f in departamentos"
                                :selected="f.codigo == departamentoId"
                                :key="f.codigo"
                                :value="f.codigo"
                              >
                                {{ f.nombre }}
                              </option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'select_provincia_' + entidad"
                              >Provincia
                              <span class="text-danger">*</span></label
                            >
                            <select
                              name="select_provincia"
                              @change="getDistritos"
                              class="custom-select custom-select-sm col-10"
                              :id="'select_provincia_' + entidad"
                            >
                              <option value="" selected="" disabled="">
                                Seleccione
                              </option>
                              <option
                                v-for="p in provincias"
                                :selected="p.codigo == provinciaId"
                                :key="p.codigo"
                                :value="p.codigo"
                              >
                                {{ p.nombre }}
                              </option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                          <div class="form-group">
                            <label :for="'select_distrito_' + entidad"
                              >Distrito
                              <span class="text-danger">*</span></label
                            >
                            <select
                              name="select_distrito"
                              class="custom-select custom-select-sm col-10"
                              :id="'select_distrito_' + entidad"
                            >
                              <option value="" selected="" disabled="">
                                Seleccione
                              </option>
                              <option
                                v-for="a in distritos"
                                :key="a.codigo"
                                :selected="a.codigo == distritoId"
                                :value="a.codigo"
                              >
                                {{ a.nombre }}
                              </option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-12 col-xs-12">
                          <div class="form-group">
                            <label :for="'direccion_' + entidad"
                              >Dirección
                              <span class="text-danger">*</span></label
                            >
                            <input
                              type="text"
                              :id="'direccion_' + entidad"
                              class="form-control form-control-sm col-9"
                              name="direccion"
                              maxlength="255"
                              @keypress="soloLetrasNumeros($event)"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3 col-xs-12">
                        <div class="form-group">
                          <label :for="'select_categoria_' + entidad"
                            >Tipo de Usuario
                            <span class="text-danger">*</span></label
                          >
                          <select
                            name="select_categoria"
                            class="custom-select custom-select-sm"
                            :id="'select_categoria_' + entidad"
                          >
                            <option value="" selected="" disabled="">
                              Seleccione
                            </option>
                            <option
                              v-for="f in categorias"
                              :selected="f.id == categoriaId"
                              :key="f.id"
                              :value="f.id"
                            >
                              {{ f.nombre }}
                            </option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                          <label :for="'certificacion_' + entidad"
                            >Certificaciones
                            <span class="text-danger">*</span></label
                          >
                          <input
                            type="text"
                            placeholder="Para Agregar presione Enter.."
                            :id="'certificacion_' + entidad"
                            class="form-control form-control-sm"
                            name="certificacion"
                            maxlength="255"
                            @keyup.enter="agregarCertificacion"
                          />
                        </div>

                        <div class="my-0">
                          <a
                            v-for="(f, index) in certificaciones"
                            :key="index"
                            class="text-muted ml-0 mr-1"
                          >
                            <span class="text-info"
                              ><strong v-text="f.nombre"></strong
                            ></span>
                            <a
                              href="javascript:void(0)"
                              class="text-danger"
                              @click="eliminarCertificacion(f.id)"
                              ><strong>x</strong></a
                            >
                          </a>
                        </div>
                      </div>
                    </div>
                    <hr />
                    <div class="row">
                      <div class="col-md-12">
                        <h6 class="card-title">
                          <i class="mdi mdi-account-key"></i> Credenciales de Acceso
                        </h6>
                      </div>
                      <div class="col-md-3 col-xs-12">
                        <div class="form-group">
                          <label :for="'usuario_' + entidad"
                            >Usuario <span class="text-danger">*</span></label
                          >
                          <input
                            type="text"
                            :id="'usuario_' + entidad"
                            class="form-control form-control-sm"
                            name="usuario"
                            maxlength="191"
                          />
                        </div>
                      </div>

                      <div class="col-md-3 col-xs-12">
                        <div class="form-group">
                          <label :for="'password_' + entidad"
                            >Contraseña
                            <span class="text-danger">*</span></label
                          >
                          <input
                            type="password"
                            :id="'password_' + entidad"
                            @keyup="confirmarPass($event)"
                            class="form-control form-control-sm"
                            name="password"
                            maxlength="255"
                          />
                        </div>
                      </div>

                      <div class="col-md-3 col-xs-12">
                        <div class="form-group">
                          <label :for="'confirmPassword_' + entidad"
                            >Confirmar Contraseña
                            <span class="text-danger">*</span></label
                          >
                          <input
                            type="password"
                            @keyup="confirmarPass($event)"
                            :id="'confirmPassword_' + entidad"
                            class="form-control form-control-sm"
                            name="confirmPassword"
                            maxlength="255"
                          />
                        </div>
                      </div>
                    </div>
                    <input
                      type="hidden"
                      name="certificaciones"
                      id="certificaciones"
                      :value="
                        JSON.stringify(Array.from(certificaciones.values()))
                      "
                    />
                    <div class="row">
                      <div
                        class="col-md-12"
                        :id="'data-error_' + entidad"
                      ></div>
                    </div>

                    <div class="form-actions ml-1">
                      <button
                        type="button"
                        @click="enviarForm(url, entidad)"
                        :id="'btnEnvio_' + entidad"
                        class="btn btn-sm btn-success"
                      >
                        <i class="mdi mdi-check-bold icon-size"></i> Guardar
                      </button>
                      <button
                        type="button"
                        @click="atras(url)"
                        class="btn btn-sm btn-danger mr-1"
                      >
                        <i class="mdi mdi-close icon-size"></i> Cancelar
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

export default {
  name: "MantenimientoPersonal",
  mixins: [misMixins],
  data() {
    return {
      accion: "Registrar",
      url: "/personal",
      departamentos: [],
      provincias: [],
      distritos: [],
      arrLocales: [],
      idLocales: [],
      categorias: [],
      id: 0,
      genero: "",
      departamentoId: 0,
      categoriaId: 0,
      provinciaId: 0,
      distritoId: 0,
      tipoLocal: "",
      certificaciones: [],
      entidad: "personal_mant",
    };
  },
  methods: {
    async cargarDatos() {
      let me = this;
      me.id = this.$route.params.id == undefined ? 0 : this.$route.params.id;
      document.getElementById("data-error_" + me.entidad).innerHTML = "";

      let departamentos = await axios.get("/departamentos");
      me.departamentos = departamentos.data.departamentos;
      let categorias = await axios.get("/categorias");
      me.categorias = categorias.data.categorias;
      me.certificaciones = [];
      //   let certificaciones = await axios.get('/certificaciones')
      //   me.certificaciones = certificaciones.data.certificaciones

      if (me.id != 0) {
        let respuesta = await axios.get("/obtenerpersonal/" + me.id);

        if (respuesta.data.estado) {
          me.accion = "Editar";
          document.getElementById("dni_" + me.entidad).value =
            respuesta.data.personal.dni;
          document.getElementById("apellidos_" + me.entidad).value =
            respuesta.data.personal.apellidos;
          document.getElementById("nombres_" + me.entidad).value =
            respuesta.data.personal.nombres;
          document.getElementById("telefono_" + me.entidad).value =
            respuesta.data.personal.telefono;
          document.getElementById("correo_" + me.entidad).value =
            respuesta.data.personal.correoE;
          document.getElementById("fechaNacimiento_" + me.entidad).value =
            respuesta.data.personal.fechaNacimiento;
          document.getElementById("direccion_" + me.entidad).value =
            respuesta.data.personal.direccion;
          document.getElementById("usuario_" + me.entidad).value =
            respuesta.data.personal.usuario;

          me.genero = respuesta.data.personal.genero;
          me.departamentoId = respuesta.data.personal.idDepartamento;
          await me.getProvinciasA(me.departamentoId);
          me.provinciaId = respuesta.data.personal.idProvincia;
          await me.getDistritosA(me.provinciaId);
          me.distritoId = respuesta.data.personal.idDistrito;
          me.tipoLocal = respuesta.data.personal.tipoLocal;
          me.categoriaId = respuesta.data.personal.categoriaPersonalId;
          me.certificaciones = respuesta.data.certificaciones;

          // let arrIdLocales = await axios.get('/obtenerlocalrelacion/'+me.id+'/'+me.tipoLocal)
          // me.idLocales = arrIdLocales.data.locales
          // me.seleccionarLocal(me.tipoLocal)
        } else {
          this.$router.replace(me.url);
        }
      } else {
        me.accion = "Registrar";
        document.getElementById("dni_" + me.entidad).value = "";
        document.getElementById("apellidos_" + me.entidad).value = "";
        document.getElementById("nombres_" + me.entidad).value = "";
        document.getElementById("telefono_" + me.entidad).value = "";
        document.getElementById("correo_" + me.entidad).value = "";
        document.getElementById("direccion_" + me.entidad).value = "";

        document.getElementById("fechaNacimiento_" + me.entidad).value =
          me.obtenerFechaActual();
        document.getElementById("select_departamento_" + me.entidad).value = "";
        document.getElementById("select_provincia_" + me.entidad).value = "";
        document.getElementById("select_distrito_" + me.entidad).value = "";
        document.getElementById("usuario_" + me.entidad).value = "";

        me.genero = "";
        me.departamentoId = 0;
        me.provinciaId = 0;
        me.distritoId = 0;
        me.categoriaId = 0;
        me.tipoLocal = "";
        me.idLocales = [];
        me.provincias = [];
        me.distritos = [];

        // me.seleccionarLocal('A')
      }
      //   $('#arrLocal').selectivity()
    },
    // getValor (value) {
    //     let me = this
    //     console.log('Object:', value)
    // me.idLocales.push(value)
    // console.log(value[1].value)
    // alert(key)
    // },
    async getProvincias() {
      let me = this;
      let dep = document.getElementById(
        "select_departamento_" + me.entidad
      ).value;

      let provincias = await axios.get("/provincias/" + dep);
      me.provincias = provincias.data.provincias;

      if (me.distritos.length > 0) {
        me.distritos = [];
        var element = document.getElementById("select_distrito_" + me.entidad);
        element.value = "";
      }
    },
    async getDistritos() {
      let me = this;
      let prov = document.getElementById(
        "select_provincia_" + me.entidad
      ).value;

      let distritos = await axios.get("/distritos/" + prov);
      me.distritos = distritos.data.distritos;
    },
    async getProvinciasA(id) {
      let me = this;
      let dep = id;

      let provincias = await axios.get("/provincias/" + dep);
      me.provincias = provincias.data.provincias;

      if (me.distritos.length > 0) {
        me.distritos = [];
        var element = document.getElementById("select_distrito_" + me.entidad);
        element.value = "";
      }
    },
    async getDistritosA(id) {
      let prov = id;
      let me = this;

      let distritos = await axios.get("/distritos/" + prov);
      me.distritos = distritos.data.distritos;
    },
    async seleccionarLocal(val) {
      // let val = element.value
      let placeh = "Seleccione Local(es) de Atención donde Laborará";

      let me = this;
      me.arrLocales = [];
      // document.getElementById('arrLocal').value=''
      let locales = await axios.get("/obtenerlocaltipo/" + val);
      let array = locales.data.locales;
      let datos = [];
      let datos02 = [];
      array.forEach((element) => {
        var l = {
          value: element.id,
          label: element.direccion + "- " + element.departamento,
        };
        datos.push(l);
        datos02.push(element.id);
      });
      me.arrLocales = datos;
      document.getElementById("listLocales_" + me.entidad).value =
        datos02.join();
      // alert(me.arrLocales)
      // const singleInput = new selectivity.Inputs.Single({
      //     element: document.querySelector('#arrLocal'),
      //     allowClear:true,
      //     item:datos,
      //     placeholder: placeh
      // })

      // $('#arrLocal').selectivity('clear')
      // console.log('Datos:', datos)
      // $('#arrLocal').selectivity({
      //     allowClear: true,
      //     items: datos,
      //     placeholder: placeh,
      // })
      // $('#arrLocal').selectivity('value',me.idLocales)
    },
    async seleccionarLocal02(val) {
      // let val = element.value
      let placeh = "Seleccione Local(es) de Atención donde Laborará";

      let me = this;
      me.arrLocales = [];
      me.idLocales = [];
      // document.getElementById('arrLocal').value=''
      let locales = await axios.get("/obtenerlocaltipo/" + val);
      let array = locales.data.locales;
      let datos = [];
      array.forEach((element) => {
        var l = {
          value: element.id,
          label: element.direccion + "- " + element.departamento,
        };
        datos.push(l);
      });
      me.arrLocales = datos;
      // alert(me.arrLocales)
      // const singleInput = new selectivity.Inputs.Single({
      //     element: document.querySelector('#arrLocal'),
      //     allowClear:true,
      //     item:datos,
      //     placeholder: placeh
      // })

      // $('#arrLocal').selectivity('clear')
      // console.log('Datos:', datos)
      // $('#arrLocal').selectivity({
      //     allowClear: true,
      //     items: datos,
      //     placeholder: placeh,
      // })
      // $('#arrLocal').selectivity('value',me.idLocales)
    },
    eliminarCertificacion(item) {
      let me = this;
      for (let i = 0; i < me.certificaciones.length; i++) {
        const element = me.certificaciones[i];
        if (element.id == item) {
          me.certificaciones.splice(i, 1);
        }
      }
      document.getElementById("certificacion_" + me.entidad).focus();
      // console.log(item)
    },
    agregarCertificacion() {
      let me = this;
      document.getElementById("data-error_" + me.entidad).innerHTML = "";
      let valor = document.getElementById("certificacion_" + me.entidad).value;
      if (valor.trim() != null) {
        let band = true;
        for (let i = 0; i < me.certificaciones.length; i++) {
          const element = me.certificaciones[i];
          if (element.id == valor) {
            band = false;
          }
        }

        if (band) {
          me.certificaciones.push({ id: valor, nombre: valor });
          document.getElementById("certificacion_" + me.entidad).value = "";
        } else {
          document.getElementById("data-error_" + me.entidad).innerHTML =
            '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Certificación ya antes Agregada</strong></a></div ></div>';
        }
      } else {
        document.getElementById("data-error_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>No se puede agregar Certificación Vacía</strong></a></div ></div>';
      }
    },
    confirmarPass() {
      let me = this;
      let cp = document.getElementById("confirmPassword_" + me.entidad).value;
      let p = document.getElementById("password_" + me.entidad).value;
      document.getElementById("data-error_" + me.entidad).innerHTML = "";
      if (cp != p) {
        document.getElementById("data-error_" + me.entidad).innerHTML =
          '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Contraseñas no Coinciden</strong></a></div ></div>';
      }
    },
  },
  activated: async function () {
    let me = this;
    me.isValidSession();

    this.$route.meta.auth = localStorage.getItem("autenticado");
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    me.cargarDatos();
  },
  mounted() {},
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById("sec-personal_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
};
</script>
<style scoped>
.ocultar {
  display: none;
}
select {
  cursor: pointer;
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
  background-color: #182fd2;
  cursor: pointer;
}
</style>