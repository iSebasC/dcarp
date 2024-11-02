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
              <li class="breadcrumb-item active">Inventario</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Inventario por Almacén</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 mt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                              <div class="col-md-10 pl-0">
                                <div class="col-md-12">
                                  <div class="form-group row">
                                    <label :for="'filtro_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Buscar por:</label>
                                    <div class="col-sm-2">
                                      <select class="custom-select custom-select-sm" :id="'filtro_'+entidad" @change="busquedaInventario">
                                          <!-- <option value="" disabled="" selected="">Buscar por</option> -->
                                          <!-- <option value="todo" selected="">Todo</option> -->
                                          <option value="codigo">Código</option>
                                          <option value="direccion">Dirección</option>
                                      </select> 
                                    </div>
                                    
                                    <label :for="'descripcion_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Descripción:</label>
                                    <div class="col-sm-4">
                                      <input type="text" :id="'descripcion_'+entidad" class="form-control form-control-sm" @keyup.enter="busquedaInventario">
                                    </div>
                                  </div>
                                  <div class="form-group row">
                                    <label :for="'departamento_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Departamento:</label>
                                    <div class="col-md-3">
                                      <select class="custom-select custom-select-sm" :id="'departamento_'+entidad" @change="busquedaInventario">
                                        <!-- <option value="" disabled="" selected="">Departamento</option> -->
                                        <option value="todo">Todos</option>
                                        <option v-for="f in departamentos" :key="f.codigo" :value="f.codigo"> {{f.nombre}}</option>
                                      </select>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2 pt-xs-1 pl-xs-2">
                                  <div class="content-buttons btn-group">
                                    <button type="button" class="btn btn-sm btn-icon btn-info btn-sm mb-1" title="Buscar" 
                                    @click="busquedaInventario"><i class="mdi mdi-magnify icon-size"></i></button>
                                  </div>
                                
                                  <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                      <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaInventario">
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
                          <table class="table table-hover mb-0">
                            <thead>
                              <tr>
                                    <th class="text-left">#</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">Departamento</th>
                                    <th class="text-center">Fecha de Creación</th>
                                    <th class="text-center">Ver Inventario</th>          
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!inventarios.length">
                                  <td class="text-left text-danger" colspan="6"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in inventarios" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.codRegistro"></td>
                                    <td class="text-center" v-text="p.direccion"></td>
                                    <td class="text-center" v-text="p.departamento"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="verInventario(p.id)" title="Ver Inventario" class="btn btn-sm btn-warning">
                                          <i class="mdi mdi-content-paste icon-size"></i>
                                        </button>
                                        <button @click="verInventarioAuto(p.id)" title="Ver Inventario Auto" class="btn btn-sm btn-primary">
                                          <i class="la mdi mdi-car-search-outline icon-size"></i>
                                        </button>
                                        <button @click="generarDetallesProducto(p.id, p.direccion)" title="Operaciones para Productos" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-wallet-membership icon-size"></i>
                                        </button>
                                        <button @click="generarDetallesAuto(p.id, p.direccion)" title="Operaciones para Autos" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-car-side icon-size"></i>
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarInventario('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarInventario(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                          
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarInventario('next'):'')">
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
    <DetalleInventario ref="detalleinventario" :idalmacen="this.idAlmacen" :lugar="this.lugar"></DetalleInventario>
    <DetalleInventarioAuto ref="detalleinventarioauto" :idalmacen="this.idAlmacen" :lugar="this.lugar"></DetalleInventarioAuto>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import DetalleInventario from './Productos'
import DetalleInventarioAuto from './ProductosAuto'
import Loader from '../Loader'


export default {
  name: 'Inventario',
  mixins: [misMixins],
  components: {
    DetalleInventario,
    DetalleInventarioAuto,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      inventarios: [],
      departamentos: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      token: this.$store.state.token,
      idAlmacen: '',
      lugar: '',
      entidad: 'inventario'
    }
  },
  computed: {
  },
  methods: {
    buscarInventario: function (attr) {
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
      me.busquedaInventario()
    },
    async busquedaInventario() {
      let me = this
      let filtro = document.getElementById('filtro_'+me.entidad).value
      let descripcion = document.getElementById('descripcion_'+me.entidad).value
      let departamentoId = document.getElementById('departamento_'+me.entidad).value
     
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/inventario',
        data: {
          'filtro': filtro,
          'descripcion': descripcion,
          'departamentoId': departamentoId,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': me.token
        }
      })
      
      me.inventarios = response.data.inventarios
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-inventario')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    generarDetallesProducto(idalmacen, direccion) {
      let me = this
      me.isValidSession()
    
      me.idAlmacen = idalmacen
      me.lugar = direccion
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalleinventario.showModal(idalmacen)
    },
    generarDetallesAuto(idalmacen, direccion) {
      let me = this
      me.isValidSession()
    
      me.idAlmacen = idalmacen
      me.lugar = direccion
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalleinventarioauto.showModal(idalmacen)
    },
    async cargarDatos () {
      let me = this
      let departamentos = await axios.get('/departamentos')
      me.departamentos = departamentos.data.departamentos
     
      me.busquedaInventario()
    },
    verInventario (id) {
      window.open('/inventario/excel/'+id,'_blank')
    },
    verInventarioAuto (id) {
      window.open('/inventarioauto/excel/'+id,'_blank')
    }
  },
  created() {
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    // window.setTimeout( function () {
    me.cargarDatos()
    // }, 1000)
    // me.generarModal(0,'')
  },
  beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false 
      // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  }
}
</script>
<style scoped>
 select {
   cursor: pointer;
 }
</style>