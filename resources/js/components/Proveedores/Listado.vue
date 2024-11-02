<template>
  <div class="content-wrapper" :id="'sec-proveedor_' + entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Proveedores</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div
          class="btn-group float-md-right"
          role="group"
          aria-label="Agregar Proveedor"
        >
          <button
            @click="agregar()"
            class="
              btn btn-info btn-sm
              btn-rounded
              box-shadow-2
              px-2
              mb-1
            "
            type="button"
          >
            <i class="mdi mdi-plus icon-size"></i> Agregar Proveedor
          </button>
        </div>
      </div>
    </div>
    <div class="content-body">
      <!-- Default styling start -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Listado de Proveedores</h4>
            </div>
            <div class="card-content collapse show pb-2">
              <div class="card-body card-dashboard p-t-1 pb-0">
                <fieldset class="form-group">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group row">
                            <label
                              :for="'dni_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Documento:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="text"
                                name="dni"
                                class="form-control form-control-sm"
                                :id="'dni_' + entidad"
                                @keyup.enter="busquedaProveedor"
                              />
                            </div>

                            <label
                              :for="'tipo_proveedor_'+entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >T. Proveedor:</label
                            >
                            <div class="col-sm-2">
                              <select name="tipo_proveedor" class="custom-select custom-select-sm" 
                              @change="busquedaProveedor"
                              :id="'tipo_proveedor_'+entidad">
                                <option v-for="f in tiposProveedores" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                              </select>
                              <!-- <input
                                type="text"
                                name="personal"
                                class="form-control form-control-sm"
                                :id="'tipo_proveedor_' + entidad"
                                @keyup.enter="busquedaProveedor"
                                placeholder="Razón Social"
                              /> -->
                            </div>

                            <label
                              :for="'telefono_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Teléfono:</label
                            >
                            <div class="col-sm-2">
                              <input
                                type="text"
                                name="telefono"
                                @keypress="soloNumeros($event)"
                                minlength="6"
                                maxlength="9"
                                class="form-control form-control-sm"
                                :id="'telefono_' + entidad"
                                @keyup.enter="busquedaProveedor"
                              />
                            </div>
                          </div>

                          <div class="form-group row">
                            <label
                              :for="'correo_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >C. Electrónico:</label
                            >
                            <div class="col-sm-3">
                              <input
                                type="text"
                                name="correo"
                                class="form-control form-control-sm"
                                :id="'correo_' + entidad"
                                @keyup.enter="busquedaProveedor"
                              />
                            </div>

                            <label
                              :for="'personal_'+entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Proveedor:</label
                            >
                            <div class="col-sm-4">
                              <input
                                type="text"
                                name="personal"
                                class="form-control form-control-sm"
                                :id="'personal_' + entidad"
                                @keyup.enter="busquedaProveedor"
                                placeholder="Razón Social"
                              />
                            </div>
                          </div>

                          <div class="form-group row">
                            <label
                              :for="'direccion_' + entidad"
                              class="
                                col-sm-2
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Dirección:</label
                            >
                            <div class="col-sm-9">
                              <input
                                type="text"
                                name="direccion"
                                class="form-control form-control-sm"
                                :id="'direccion_' + entidad"
                                @keyup.enter="busquedaProveedor"
                              />
                            </div>
                          </div>
                         
                          <!-- <div class="form-group row">
                                          <label :for="'genero_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Género:</label>
                                          <div class="col-sm-2">
                                            <select class="custom-select custom-select-sm" :id="'genero_'+entidad" @change="busquedaProveedor">
                                              <option value="todo">Todos</option>
                                              <option v-for="f in generos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                            </select>
                                          </div>        

                                          <label :for="'categoria_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0 text-md-center">T. Personal:</label>
                                          <div class="col-sm-3">
                                            <select class="custom-select custom-select-sm" :id="'categoria_'+entidad" @change="busquedaProveedor">
                                              <option value="todo">Todos</option>
                                              <option v-for="f in categorias" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                            </select>
                                          </div>        
                                        </div>               -->
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 pt-xs-1 pl-xs-2">
                      <div class="content-buttons btn-group">
                        <button
                          type="button"
                          class="btn btn-sm btn-icon btn-info mb-1"
                          title="Buscar"
                          @click="busquedaProveedor"
                        >
                          <i class="mdi mdi-magnify"></i>
                        </button>

                        <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>-->

                        <button
                          type="button"
                          class="btn btn-sm btn-icon btn-success mb-1"
                          title="Exportar a Excel"
                          @click="excel"
                        >
                          <i class="mdi mdi-file-excel "></i>
                        </button>
                      </div>

                      <div class="form-group row">
                        <label
                          class="col-md-5 label-control pt-1 pl-1 pr-0"
                          :for="'cantidad_' + entidad"
                          style="margin-top: 3px"
                          >Mostrar:
                        </label>
                        <div class="col-md-5 pt-1 pl-0">
                          <select
                            class="custom-select custom-select-sm pr-2 ml-1"
                            :id="'cantidad_' + entidad"
                            title="Registros por Página"
                            @change="busquedaProveedor"
                          >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
              <span
                class="text-info pl-2 ml-1 mb-2 col-form-label"
                :id="'loading_' + entidad"
                ><Loader /></span
              >
              <div
                class="table-responsive px-2 d-none"
                :id="'tabla_' + entidad"
              >
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-center">Tipo Proveedor</th>
                      <th class="text-center">Tipo Documento</th>
                      <th class="text-center">Documento</th>
                      <th class="text-center">Proveedor</th>
                      <th class="text-center">Correo Electrónico</th>
                      <th class="text-center">Teléfono</th>
                      <th class="text-center">Dirección</th>
                      <th class="text-center">Modificado el</th>
                      <th class="text-center">Operaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!personal.length">
                      <td class="text-left text-danger" colspan="11">
                        <strong
                          >No se Encontraron Resultados en su Búsqueda</strong
                        >
                      </td>
                    </tr>

                    <tr v-for="(p, index) in personal" :key="p.id">
                      <td
                        class="text-left"
                        v-text="
                          index + 1 + inicio < 10
                            ? '0' + (index + inicio + 1)
                            : index + inicio + 1
                        "
                      ></td>
                      <td class="text-center">
                        <span :class="'badge badge-pill ' + (p.tipoProveedor!='-'?'badge-info':'badge-danger')" 
                          v-text="p.tipoProveedor=='L'?'Local':
                          p.tipoProveedor=='N'?'Nacional':
                          p.tipoProveedor=='I'?'Internacional':
                          p.tipoProveedor"></span>
                      </td>
                     
                      <td class="text-center">
                        <strong v-text="p.tipodoc"></strong>
                      </td>
                      <td class="text-center" v-text="p.documento"></td>
                      <td class="text-center">
                        <strong v-text="p.cliente"></strong>
                      </td>
                      <td class="text-center" v-text="p.correoElectronico"></td>
                      <td class="text-center" v-text="p.telefono"></td>
                      <td class="text-center">
                        <p class="text-justify small" v-text="p.direccion"></p>
                      </td>
                      <td class="text-center" v-text="p.ultimamod"></td>
                      <td class="text-center">
                        <div class="content-buttons btn-group">
                          <button
                            @click="editar(p)"
                            title="Editar"
                            class="btn btn-sm btn-success"
                          >
                            <i class="mdi mdi-pencil icon-size"></i>
                          </button>

                          <!-- <router-link tag="a" title="Editar Personal" v-bind:to="{name: 'editarpersonal', params:{id:p.id}}" class="btn btn-sm btn-success">
                                          <i class="mdi mdi-pencil icon-size"></i>
                                        </router-link>

                                        <button @click="eliminar(p.id,p.personal)" title="Eliminar Personal" class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-delete-outline icon-size"></i>
                                        </button> -->
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <nav class="pl-2">
                  <ul class="pagination justify-content-right">
                    <li class="page-item">
                      <a
                        class="page-link bg-info text-white"
                        href="javascript:void(0);"
                        aria-label="total"
                        ><strong>TOTAL: </strong><span v-text="total"></span
                      ></a>
                    </li>

                    <li
                      class="page-item"
                      @click="
                        paramInicio < pageActual && paramInicio > 0
                          ? buscarProveedor('prev')
                          : ''
                      "
                    >
                      <a
                        class="page-link"
                        href="javascript:void(0);"
                        aria-label="Previous"
                        >«</a
                      >
                    </li>

                    <li
                      v-for="(op, index) in opciones"
                      :key="index"
                      :class="
                        op.opc == pageActual ? 'page-item active' : 'page-item'
                      "
                      @click="op.opc != pageActual ? buscarProveedor(op.opc) : ''"
                    >
                      <a
                        class="page-link"
                        href="javascript:void(0);"
                        v-text="op.opc"
                        :disabled="op.bloqueado"
                      ></a>
                    </li>
                    <li
                      class="page-item"
                      @click="
                        paramFin > pageActual && paramFin > 0
                          ? buscarProveedor('next')
                          : ''
                      "
                    >
                      <a
                        class="page-link"
                        href="javascript:void(0);"
                        aria-label="Next"
                        >»</a
                      >
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <Proveedor ref="proveedor" :documento="this.documento"></Proveedor>
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import Proveedor from "../Compra/Proveedor";
import Loader from "../Loader";

export default {
  name: "Proveedores",
  mixins: [misMixins],
  components: {
    Proveedor,
    Loader
},
data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      personal: [],
      categorias: [],
      total: "",
      inicio: "",
      fin: "",
      paramInicio: "",
      paramFin: "",
      token: localStorage.getItem("token"),
      idModal: "",
      idPersonal: "",
      persona: "",
      documento: "",
      entidad: "proveedor",
      tiposProveedores: [
          {id: '', nombre: 'Todos'},
          {id: 'L', nombre: 'Local'},
          {id: 'N', nombre: 'Nacional'},
          {id: 'I', nombre: 'Internacional'}
      ],
    };
  },
  computed: {},
  methods: {
    buscarProveedor: function (attr) {
      let me = this;
      if (attr == "next") {
        me.pageActual = me.pageActual + 1;
      } else {
        if (attr == "prev") {
          me.pageActual = me.pageActual - 1;
        } else {
          me.pageActual = attr;
        }
      }

      me.busquedaProveedor();
    },
    async busquedaProveedor() {
      let me = this;
      let dni = document.getElementById("dni_" + me.entidad).value;
      let personal = document.getElementById("personal_" + me.entidad).value;
      let correo = document.getElementById("correo_" + me.entidad).value;
      let direccion = document.getElementById("direccion_" + me.entidad).value;
      let telefono = document.getElementById("telefono_" + me.entidad).value;
      let tipo_proveedor = document.getElementById('tipo_proveedor_'+me.entidad).value;
      //   let genero = document.getElementById('genero_'+me.entidad).value

      //   let categoriaId = document.getElementById('categoria_'+me.entidad).value
      // var el = document.getElementById('loading_'+me.entidad)
      // el.classList.add('d-none')

      // me.filas = document.getElementById('cantidad_'+me.entidad).value

      let response = await axios({
        method: "post",
        url: "/proveedor",
        data: {
          dni: dni,
          personal: personal,
          correo: correo,
          direccion: direccion,
          telefono: telefono,
          tipo_proveedor: tipo_proveedor,
          filas: me.filas,
          page: me.pageActual,
          _token: me.token,
        },
      });

      me.personal = response.data.proveedor;
      me.total = response.data.cantidad;
      me.pageActual = response.data.page;
      me.opciones = response.data.paginador;
      me.inicio = response.data.inicio;
      me.fin = response.data.fin;
      me.paramInicio = response.data.paramInicio;
      me.paramFin = response.data.paramFin;

      me.renderTabla(me.entidad);

      // var el2 = document.getElementById('tabla-personal')
      // el2.classList.remove('d-none')

      // alert(filtro)
    },
    async cargarDatos() {
      let me = this;

      let categorias = await axios.get("/categorias");
      //   let tipos = await axios.get('/tipos')

      me.categorias = categorias.data.categorias;
      //   me.tipos = tipos.data.tipos

      me.busquedaProveedor();
    },
    excel() {
      let me = this;
      let dni = document.getElementById("dni_" + me.entidad).value;
      let personal = document.getElementById("personal_" + me.entidad).value;
      let correo = document.getElementById("correo_" + me.entidad).value;
      let direccion = document.getElementById("direccion_" + me.entidad).value;
      let telefono = document.getElementById("telefono_" + me.entidad).value;
      let tipo = document.getElementById("tipo_proveedor_" + me.entidad).value;
      //   let genero = document.getElementById('genero_'+me.entidad).value

      //   let categoriaId = document.getElementById('categoria_'+me.entidad).value

      // if (dni =='') {dni = 'null'}
      // if (descripcion =='') {descripcion = 'null'}
      // if (genero =='') {genero = 'null'}
      // if (categoriaId =='') {categoriaId = 'null'}

      window.open(
        `/proveedor/excel?dni=${dni}&personal=${personal}&correo=${correo}&direccion=${direccion}&telefono=${telefono}&tipo=${tipo}`,
        "_blank"
      );
    },
    agregar() {
      let me = this;
      me.isValidSession();
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      this.documento = "";
      // alert(this.$store.state.mostrarModal)
      console.log("documento", this.documento);
      me.$refs.proveedor.showModal(this.documento, 3);
    },
    editar(item) {
      let me = this;
      me.isValidSession();
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      this.documento = item.documento;
      // alert(this.$store.state.mostrarModal)
      console.log("documento", this.documento);
      me.$refs.proveedor.showModal(this.documento, 1);
    }
  },
  created() {
    this.$on("busquedaListProveedor", async function () {
      await this.busquedaProveedor();
    });

    // this.incremKey(0)
    // this.generarHabilidades('')
  },
  activated: async function () {
    let me = this;
    me.isValidSession();

    this.$route.meta.auth = localStorage.getItem("autenticado");
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    me.cargarDatos();
  },
  beforeDestroy: function () {
    let me = this;
    var element = document.getElementById("sec-proveedor_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
  deactivated: function () {
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  },
};
</script>