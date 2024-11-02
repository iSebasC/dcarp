<template>
  <div class="content-wrapper" :id="'sec-prospecto_' + entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Oportunidades</li>
            </ol>
          </div>
        </div>
      </div>
      <!-- <div class="content-header-right col-md-6 col-12">
        <div
          class="btn-group float-md-right"
          role="group"
          aria-label="Agregar Prospecto"
        >
          <router-link tag="a" to="/prospecto/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
            type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Prospecto</router-link>
     
        </div>
      </div> -->
    </div>
    <div class="content-body">
      <!-- Default styling start -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Listado de Oportunidades</h4>
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
                                @keyup.enter="busquedaOportunidades"
                              />
                            </div>

                            <label
                              :for="'personal_' + entidad"
                              class="
                                col-sm-1
                                m-4px
                                col-form-label
                                pr-0
                                mr-0
                                pt-0
                              "
                              >Cliente:</label
                            >
                            <div class="col-sm-4">
                              <input
                                type="text"
                                name="personal"
                                class="form-control form-control-sm"
                                :id="'personal_' + entidad"
                                @keyup.enter="busquedaOportunidades"
                                placeholder="Razón Social"
                              />
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
                                @keyup.enter="busquedaOportunidades"
                              />
                            </div>
                          </div>

                          <div class="form-group row">
                            <label :for="'concepto_' + entidad"
                              class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Concepto:</label>
                            <div class="col-sm-2">
                              <input
                                type="text" name="concepto" class="form-control form-control-sm"
                                :id="'concepto_' + entidad" @keyup.enter="busquedaOportunidades"
                              />
                            </div>

                            <label :for="'moneda_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Moneda:</label>
                              <div class="col-sm-3">
                                <select class="custom-select custom-select-sm" :id="'moneda_'+entidad" @change="busquedaOportunidades">
                                  <option value="todo">Todos</option>
                                  <option v-for="a in monedas" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                </select>
                            </div>

                            <label :for="'fase_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Fase:</label>
                              <div class="col-sm-3">
                                <select class="custom-select custom-select-sm" :id="'fase_'+entidad" @change="busquedaOportunidades">
                                  <option value="todo">Todos</option>
                                  <option v-for="a in fases" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                </select>
                              </div>
                          </div>

                          <div class="form-group row">
                            <label :for="'certeza_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Certeza:</label>
                              <div class="col-sm-3">
                                <select class="custom-select custom-select-sm" :id="'certeza_'+entidad" @change="busquedaOportunidades">
                                  <option value="todo">Todos</option>
                                  <option v-for="a in certezas" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                </select>
                            </div>

                            <label :for="'linea_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Línea:</label>
                              <div class="col-sm-3">
                                <select class="custom-select custom-select-sm" :id="'linea_'+entidad" @change="busquedaOportunidades">
                                  <option value="todo">Todos</option>
                                  <option v-for="a in lineas" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                </select>
                              </div>
                          </div>

                          <!-- <div class="form-group row">
                                          <label :for="'genero_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Género:</label>
                                          <div class="col-sm-2">
                                            <select class="custom-select custom-select-sm" :id="'genero_'+entidad" @change="busquedaOportunidades">
                                              <option value="todo">Todos</option>
                                              <option v-for="f in generos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                            </select>
                                          </div>        

                                          <label :for="'categoria_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0 text-md-center">T. Personal:</label>
                                          <div class="col-sm-3">
                                            <select class="custom-select custom-select-sm" :id="'categoria_'+entidad" @change="busquedaOportunidades">
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
                          @click="busquedaOportunidades"
                        >
                          <i class="mdi mdi-magnify"></i>
                        </button>

                        <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>-->

                        <!-- <button
                          type="button"
                          class="btn btn-sm btn-icon btn-success mb-1"
                          title="Exportar a Excel"
                          @click="excel"
                        >
                          <i class="mdi mdi-file-excel "></i>
                        </button> -->
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
                            @change="busquedaOportunidades"
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
                      <th class="text-center">Tipo Documento</th>
                      <th class="text-center">Documento</th>
                      <th class="text-center">Cliente</th>
                      <th class="text-center">Teléfono</th>
                      <th class="text-center">Concepto</th>
                      <th class="text-center">Monto</th>
                      <th class="text-center">Moneda</th>
  
                      <th class="text-center">Fase</th>
                      <th class="text-center">Certeza</th>
                      <th class="text-center">Línea</th>
                      <th class="text-center">Fecha Cierre</th>
                    
                      <th class="text-center">Modificado el</th>
                      <th class="text-center">Operaciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!personal.length">
                      <td class="text-left text-danger" colspan="14">
                        <strong
                          >No se Encontraron Resultados en su Búsqueda</strong
                        >
                      </td>
                    </tr>

                    <tr v-for="(p, index) in personal" :key="p.id" :class="p.eliminado == 'S'?'table-danger':(p.situacion == 'C'?'table-success':'')">
                      <td
                        class="text-left"
                        v-text="
                          index + 1 + inicio < 10
                            ? '0' + (index + inicio + 1)
                            : index + inicio + 1
                        "
                      ></td>
                      <td class="text-center">
                        <span
                          class="badge badge-pill badge-info"
                          v-text="p.tipodocumento"
                        ></span>
                      </td>
                      <td class="text-center" v-text="p.documento"></td>
                      <td class="text-center">
                        <strong v-text="p.cliente"></strong>
                      </td>
                      <td class="text-center" v-text="p.telefono"></td>
                      <td class="text-center" v-text="p.concepto"></td>
                      <td class="text-center" v-text="p.montoRedondeado"></td>
                      <td class="text-center"> <strong v-text="p.moneda =='PEN'?'Soles':'Dólares'"></strong></td>
                      <td class="text-center">
                        <span
                          :class="'badge badge-pill ' + (p.fase == 'C'?'badge-warning':p.fase == 'N'?'badge-info':'badge-success')"
                          v-text="p.fase == 'C'?'En Cotización': p.fase == 'N'?'En Negociación':'Pago Inicial'"
                        ></span>
                      </td>
                      <td class="text-center">
                        <span
                          class="badge badge-pill badge-info"
                          v-text="(p.certeza == 'P'?'10% Poco Interesado': p.certeza == 'M'?'50% Medio Interesado':'90% Muy Interesado')"
                        ></span>
                      </td>

                      <td class="text-center">
                        <strong v-text="p.linea == 'L'?'Liviano': 'Pesado'"></strong>
                      </td>
                      <td class="text-center" v-text="p.fecha"></td>
                      <td class="text-center" v-text="p.fechaRegistro"></td>
    
                      <td class="text-center">
                        <div class="content-buttons btn-group">
                        
                          <button v-if="p.eliminado == 'N' && p.situacion == 'N'" 
                            @click="editOportunidad(p)" title="Editar Oportunidad" 
                            class="btn btn-sm btn-info">
                            <i class="mdi mdi-account-arrow-up"></i>
                          </button>

                          
                          <button v-if="p.eliminado == 'N' && p.situacion == 'N'" 
                            @click="crearNotificacion(p.id, 'O')" title="Notificar" 
                            class="btn btn-sm btn-warning">
                            <i class="mdi mdi-message-flash-outline icon-size"></i>
                          </button>

                          <router-link v-if="p.situacion == 'N' && p.eliminado == 'N'"
                            tag="a" title="Convertir en Cotización"
                            v-bind:to="{name: 'ConvertCotizacion', params:{oportunidadId:p.id}}"
                            class="btn btn-sm btn-success"
                          >
                            <i class="mdi mdi-transfer icon-size"></i>
                          </router-link>
                          <button v-if="p.eliminado == 'N' && p.situacion == 'N'" @click="eliminar(p.id, ` ${p.cliente} - ${p.concepto}`)" title="Eliminar Oportunidad" 
                            class="btn btn-sm btn-danger">
                            <i class="mdi mdi-delete-outline icon-size"></i>
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
                          ? buscarOportunidad('prev')
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
                      @click="op.opc != pageActual ? buscarOportunidad(op.opc) : ''"
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
                          ? buscarOportunidad('next')
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
    <MantOportunidad ref="oportunidad"/>
    <Notificacion ref="notificacion" />
  </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";
import MantOportunidad from "./Mantenimiento";
import Loader from "../Loader";
import Notificacion from "../Notificacion"

export default {
  name: "Oportunidad",
  mixins: [misMixins],
  components: {
    MantOportunidad,
    Notificacion,
    // Cliente,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      personal: [],
      origenes: [],
      certezas: [
        { id:'P', nombre: '10% Poco Interesado' },
        { id:'M', nombre: '50% Medio Interesado' },
        { id:'I', nombre: '90% Muy Interesado' }
      ],
      fases:[
        { id:'C', nombre: 'En Cotización' },
        { id:'N', nombre: 'En Negociación' },
        { id:'I', nombre: 'Pago Inicial' }      
      ],
      lineas: [
        {id: 'L', nombre: 'Ligeros'},
        {id: 'P', nombre: 'Pesados'}
      ],
      monedas: [
        {id:'PEN', nombre: 'Soles'},
        {id: 'USD', nombre:'Dólares'}
      ],
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
      entidad: "oportunidad",
    };
  },
  computed: {},
  methods: {
    buscarOportunidad: function (attr) {
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

      me.busquedaOportunidades();
    },
    editOportunidad(item) {
      let me = this;
      me.isValidSession();

      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.oportunidad.showModal(item.idProspecto, item.id, 'E');
    },
    crearNotificacion(id, tipo) {
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      this.$refs.notificacion.showModal(id, tipo);
    },
    async busquedaOportunidades() {
      let me = this;
      let dni = document.getElementById("dni_" + me.entidad).value;
      let personal = document.getElementById("personal_" + me.entidad).value;
      // let correo = document.getElementById("concepto_" + me.entidad).value;
      let linea = document.getElementById("linea_" + me.entidad).value;
      let telefono = document.getElementById("telefono_" + me.entidad).value;
      let concepto = document.getElementById('concepto_'+me.entidad).value
      let moneda = document.getElementById('moneda_'+me.entidad).value
      let certeza = document.getElementById('certeza_'+me.entidad).value
      let fase = document.getElementById('fase_'+me.entidad).value

      //   let categoriaId = document.getElementById('categoria_'+me.entidad).value
      // var el = document.getElementById('loading_'+me.entidad)
      // el.classList.add('d-none')

      // me.filas = document.getElementById('cantidad_'+me.entidad).value

      let response = await axios({
        method: "post",
        url: "/getoportunidadesall",
        data: {
          dni: dni,
          personal: personal,
          linea: linea,
          // direccion: direccion,
          telefono: telefono,
          filas: me.filas,
          page: me.pageActual,
          concepto: concepto,
          moneda: moneda,
          certeza: certeza,
          fase: fase,
          _token: me.token,
        },
      });

      me.personal = response.data.oportunidades;
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

      let modelos = await axios.get("/modelosautos");
      me.modelos = modelos.data.modelosauto;

      let marcas = await axios.get("/marcasautosmod");
      me.marcas = marcas.data.marcas;
   
      let etiquetas = await axios.get('/etiquetasprospectos')
      me.etiquetas = etiquetas.data.etiquetasprospecto
      
      let origenes = await axios.get('/origenesprospectos')
      me.origenes = origenes.data.origenesprospecto
      //   me.tipos = tipos.data.tipos

      me.busquedaOportunidades();
    },
    generarHabilidades(id, nombre) {
      let me = this;
      me.isValidSession();

      me.idPersonal = id;
      me.persona = nombre;
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      me.$refs.habilidades.showModal(id);
    },
    excel() {
      let me = this;
      let dni = document.getElementById("dni_" + me.entidad).value;
      let personal = document.getElementById("personal_" + me.entidad).value;
      let correo = document.getElementById("concepto_" + me.entidad).value;
      let direccion = document.getElementById("direccion_" + me.entidad).value;
      let telefono = document.getElementById("telefono_" + me.entidad).value;
      //   let genero = document.getElementById('genero_'+me.entidad).value

      //   let categoriaId = document.getElementById('categoria_'+me.entidad).value

      // if (dni =='') {dni = 'null'}
      // if (descripcion =='') {descripcion = 'null'}
      // if (genero =='') {genero = 'null'}
      // if (categoriaId =='') {categoriaId = 'null'}

      window.open(
        `/cliente/excel?dni=${dni}&personal=${personal}&correo=${correo}&direccion=${direccion}&telefono=${telefono}`,
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
      me.$refs.cliente.showModal(this.documento, 3);
    },
    editar(item) {
      let me = this;
      me.isValidSession();
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
      this.documento = item.documento;
      // alert(this.$store.state.mostrarModal)
      console.log("documento", this.documento);
      me.$refs.cliente.showModal(this.documento, 1);
    },
    async eliminar(id, nombre) {
      let me = this;
      let token = this.$store.state.token;
      swal({
        title: "Confirmar Operación",
        html: "¿Seguro que desea Eliminar la oportunidad del cliente <strong>" + nombre + "</strong> ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sí, Eliminar",
        cancelButtonText: "No, Cancelar",
        allowOutsideClick: false,
        closeOnConfirm: false,
        closeOnCancel: false,
      }).then(async function (result) {
        if (result.value) {
          let response = await axios({
            method: "post",
            url: "/eliminaroportunidad",
            data: {
              id: id,
              _token: token,
            },
          });

          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: "¡Eliminado!",
                text: "Oportunidad Eliminada con Éxito.",
                type: "success",
              }).then(function () {
                me.busquedaOportunidades();
              });
            }, 500);
          } else {
            swal("¡Cancelado!", "Operación Cancelada", "error");
          }
        } 
        // else {
        //   swal("¡Cancelado!", "Operación Cancelada", "error");
        // }
      });
    },
  },
  created() {
    this.$on("busquedaListOportunidad", async function () {
      await this.busquedaOportunidades();
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
    var element = document.getElementById("sec-prospecto_" + me.entidad);
    element.classList.add("hidden");
    // this.$store.state.mostrarModal = false
  },
  deactivated: function () {
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  },
};
</script>