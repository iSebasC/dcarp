<template>
  <div class="content-wrapper" :id="'sec_'+entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Margen de Ganancia
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Márgenes de Ganancia</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard p-t-1 pb-0">
                        <fieldset class="form-group">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group row">
                                    <label :for="'nombre_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Nombre:</label>
                                    <div class="col-sm-4">
                                      <input type="text" name="ganancia" class="form-control form-control-sm" :id="'nombre_'+entidad" 
                                        @keyup.enter="busquedaGanancia" maxlength="255" />
                                    </div>
                                
                                    <label :for="'porcentaje_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Porcentaje:</label>
                                    <div class="col-sm-2">
                                      <input type="number" name="porcentaje" step="0.01" class="form-control form-control-sm text-center" :id="'porcentaje_'+entidad" 
                                        @keyup.enter="busquedaGanancia" />
                                    </div>
                                
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-md-2 pt-xs-1 pl-xs-2">
                              <div class="content-buttons btn-group">
                                <button type="button" class="btn btn-sm btn-icon btn-md btn-info mb-1" title="Buscar" 
                                @click="busquedaGanancia">
                                  <i class="mdi mdi-magnify icon-size"></i>
                                </button>

                                <button type="button" title="Cambiar Configuración de Igv" class="btn btn-sm btn-icon btn-danger btn-md mb-1" 
                                  @click="abrirModalIgv"><i class="mdi mdi-information-slab-circle-outline icon-size"></i></button>
 
                              </div>

                              <div class="form-group row">
                                <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" 
                                  style="margin-top:3px;">Mostrar: </label>
                                <div class="col-md-5 pt-1 pl-0">
                                  <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" 
                                  title="Registros por Página" @change="busquedaGanancia">
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
                      <span class="text-info pl-2 ml-1 mb-2 col-form-label" :id="'loading_'+entidad"><Loader /></span>
                      <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad">
                          <table class="table mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Margen (%)</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!ganancias.length">
                                  <td class="text-left text-danger" colspan="7"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in ganancias" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.codigo"></td>
                                    <td class="text-center" v-text="p.nombre"></td>
                                    <td class="text-center" v-text="p.porcentaje"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="editarPorcentaje(p.id,p.codigo, p.porcentaje)" 
                                        title="Ver GestionPorcentaje" class="btn btn-sm btn-warning">
                                          <i class="mdi mdi-view-stream-outline icon-size"></i>
                                        </button>
                                      </div>
                                    </td>
                                </tr>
                            </tbody>
                          </table>

                          <nav class="pl-2">
                            <ul class="pagination justify-content-right">
                              <li class="page-item">
                                <a class="page-link bg-info text-white" href="javascript:void(0);" aria-label="total"><strong>TOTAL: </strong><span v-text="total"></span></a>
                              </li>

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarFactor('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarFactor(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
             
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarFactor('next'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Next">»</a>
                              </li>
                            </ul>
                          </nav>
  
                      </div>     
                  </div>
              </div>
          </div>
      </div>
    </div>

    <GestionPorcentaje ref="gestion"></GestionPorcentaje>
    <ConfiguracionIgv ref="configuracionigv"></ConfiguracionIgv>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import GestionPorcentaje from './GestionPorcentaje'
import ConfiguracionIgv from './ConfiguracionIgv'
import Loader from '../Loader'

export default {
  name: 'Ganancia',
  mixins: [misMixins],
  components: {
    GestionPorcentaje,
    ConfiguracionIgv,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      ganancias: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idGanancia: '',
      porcentaje:'',
      codigo: '',
      entidad: 'ganancia'
    }
  },
  computed: {
  },
  methods: {
    buscarFactor: function (attr) {
      let me = this
      if (attr == 'next') {
        me.pageActual = me.pageActual + 1       
      } else {
        if (attr == 'prev') {
          me.pageActual = me.pageActual - 1
        } else {
          me.pageActual = attr 
        }
      }
      me.busquedaGanancia()
    },
    // generarModal(id, nombre) {
    //   let me = this
    //   me.idTipo = id
    //   me.tipoUsuario = nombre
    // //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    // //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
    //    // this.$emit('abrirmodal',me.idModal)
    //   me.$refs.ganancias.showModal(id)

    //   // me.mostrarModal = true
    //   // alert(me.idModal)
    //   // if (this.$store.state.mostrarModal) {
    //   // }
    //   // generarModal(id)
    // },
    async busquedaGanancia() {
      let me = this
      let nombre = document.getElementById('nombre_'+me.entidad).value
      let porcentaje = document.getElementById('porcentaje_'+me.entidad).value
     
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/ganancia',
        data: {
          'nombre': nombre,
          'porcentaje': porcentaje,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.ganancias = response.data.ganancias
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-ganancia')
      // el2.classList.remove('d-none')

    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      me.busquedaGanancia()
    },
    editarPorcentaje(idganancia,codigo, porcentaje) {
      let me = this
      me.isValidSession()
    
      me.idGanancia = idganancia
      me.codigo     = codigo
      me.porcentaje = porcentaje
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
 
      me.$refs.gestion.showModal(idganancia,codigo, porcentaje)
    },
    abrirModalIgv () {
      let me = this
      me.isValidSession()
   
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.configuracionigv.showModal()
   
    },
  
  },
  created() {
    this.$on('buscarGanancia', function () {
      this.busquedaGanancia()
    })
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    this.$route.meta.auth = localStorage.getItem('autenticado')
    // window.setTimeout( function () {
    me.cargarDatos()
    // }, 1000)
    // me.generarModal(0,'')
  },
  beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
  
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  }
}
</script>