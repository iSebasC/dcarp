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
              <li class="breadcrumb-item active">Productos
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Producto">
          <router-link tag="a" to="/producto/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Producto</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"><!-- Default styling start -->
      <div class="row justify-content-center" id="default">
          <div class="col-md-12 col-xs-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Productos</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                <div class="col-md-12">
                                 <div class="form-group row">
                                    <label :for="'codProveedor_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Cod. Proveedor:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="codProveedor" class="form-control form-control-sm" :id="'codProveedor_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="100" />
                                    </div>
                                  
                                    <label :for="'producto_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Producto:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="producto" class="form-control form-control-sm" :id="'producto_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div>

                                    <label :for="'marca_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Marca:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="marca" class="form-control form-control-sm" :id="'marca_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label :for="'modelo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Modelo:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="modelo" class="form-control form-control-sm" :id="'modelo_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div>
                                    
                                    <label :for="'sistemaAuto_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Sistema de Auto:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="sistemaAuto" class="form-control form-control-sm" :id="'sistemaAuto_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div>
                                  
                                    <!-- <label :for="'precio_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Precio:</label>
                                    <div class="col-sm-2">
                                        <input type="number" step="0.01" name="precio" class="form-control text-center form-control-sm" :id="'precio_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div> -->
                                    <label :for="'medida_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Medida:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="medida" class="form-control text-center form-control-sm" :id="'medida_'+entidad" 
                                        @keyup.enter="busqueda" maxlength="255" />
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label :for="'tipobateria_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Tipo Batería:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" name="tipobateria" :id="'tipobateria_'+entidad" @change="busqueda">
                                        <!-- <option value="" disabled="" selected="">Tipo de Llanta</option> -->
                                        <option value="todo">Todas</option>
                                        <option v-for="f in tiposBateria" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                        </select>
                                    </div>
                                  
                                    <label :for="'tipollanta_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">T. Llanta:</label>
                                    <div class="col-sm-2">
                                      <select class="custom-select custom-select-sm" name="tipollanta" :id="'tipollanta_'+entidad" @change="busqueda">
                                        <!-- <option value="" disabled="" selected="">Tipo de Llanta</option> -->
                                        <option value="todo">Todas</option>
                                        <option v-for="f in tiposLlantas" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                        </select>
                                    </div>                                    

                                    <label :for="'tipo_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Tipo Producto:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" name="tipoproducto" :id="'tipo_'+entidad" @change="busqueda">
                                        <!-- <option value="" disabled="" selected="">Tipo de Producto</option> -->
                                        <option value="todo">Todos</option>
                                        <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                      </select>
                                    </div>
                              
                              
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-icon btn-info" title="Buscar" @click="busqueda">
                                    <i class="icon-size mdi mdi-magnify"></i>
                                  </button>

                                  <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>-->

                                  <button type="button" class="btn btn-sm btn-icon btn-success" @click="excel" 
                                  title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button>
 
                                  <button type="button" title="Cambiar Tipo de Cambio" class="btn btn-sm btn-icon btn-danger" 
                                  @click="abrirTipoCambio"><i class="mdi mdi-information-slab-circle-outline icon-size"></i></button>
 
                                </div>

                                <div class="form-group row">
                                  <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                      <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busqueda">
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
                                <th class="text-center">Tipo de Producto</th>
                                <th class="text-center">Código de Proveedor</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Modelo</th>
                                <th class="text-center">Sistema de Auto</th>
                                <th class="text-center">Marca de Auto</th>
                                <th class="text-center">Tipo de Neumático</th>
                                <th class="text-center">Medida</th>
                                <th class="text-center">Tipo de Batería</th>
                                <th class="text-center">Stock Mínimo</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!productos.length">
                                  <td class="text-left text-danger" colspan="14"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in productos" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))"></td>
                                    <td class="text-center" v-text="p.codInterno"></td>
                                    <td class="text-center" v-text="p.tipoProducto"></td>
                                    <td class="text-center" v-text="p.codProveedor"></td>
                                    <td class="text-center" v-text="p.nombre"></td>
                                    <td class="text-center" v-text="p.marca"></td>
                                    <td class="text-center" v-text="p.modelo"></td>
                                    <td class="text-center" v-text="p.sistema"></td>
                                    <td class="text-center" v-text="p.marcaauto"></td>
                                    <td class="text-center" v-text="p.tipollanta"></td>
                                    <td class="text-center" v-text="p.medida"></td>
                                    <td class="text-center" v-text="p.tipobateria"></td>
                                    <td class="text-right" v-text="p.stockMinimo"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <router-link
                                          tag="a" title="Editar Producto"
                                          v-bind:to="{name: 'editarproducto', params:{id:p.id}}"
                                          class="btn btn-sm btn-success"
                                        >
                                          <i class="mdi mdi-pencil icon-size"></i>
                                        </router-link>
                                        <button @click="eliminar(p.id,p.tipoProducto+' - Precio: S/ '+p.precio)" title="Eliminar Producto" class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-delete-outline icon-size"></i>
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscar('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscar(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscar('next'):'')">
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

    <GestionTipoCambio ref="gestiontipocambio" ></GestionTipoCambio>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import GestionTipoCambio from './GestionTipoCambio'
import Loader from '../Loader'


export default {
  name: 'Producto',
  mixins: [misMixins],
  components: {
    GestionTipoCambio,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      productos: [],
      tiposLlantas: [
        {id:'AT',  nombre: 'AT'},
        {id:'HT',  nombre: 'HT'},
        {id:'MT',  nombre: 'MT'},
        {id:'AY',  nombre: 'AY'},            
        {id:'Camión',  nombre: 'Camión'},
        {id:'Convencional',  nombre: 'Convencional'},
      ],
      tiposBateria: [
        {id:'PR', nombre: 'Profesional'},
        {id:'AD', nombre: 'Alto Desempeño'},
        {id:'PL', nombre: 'Platinum'}
      ],
      tipos: [
        {id:'A',  nombre: 'Accesorios/Repuestos'},
        {id:'B',  nombre: 'Baterías'},
        {id:'LL', nombre: 'Neumáticos'},
        {id:'M',  nombre: 'Muelles'},
        {id:'I',  nombre: 'Insumos'},
      ],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      token: this.$store.state.token,
      estadoListado: false,
      entidad: 'producto'
    }
  },
  methods: {
    buscar: function (attr) {
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

      me.busqueda()
    },
    async busqueda() {
      let me = this
      let codProveedor  = document.getElementById('codProveedor_'+me.entidad).value
      let producto      = document.getElementById('producto_'+me.entidad).value
      let marca         = document.getElementById('marca_'+me.entidad).value
      let modelo        = document.getElementById('modelo_'+me.entidad).value
      let sistemaAuto   = document.getElementById('sistemaAuto_'+me.entidad).value
      let tipoBateria   = document.getElementById('tipobateria_'+me.entidad).value
      let medida        = document.getElementById('medida_'+me.entidad).value
      let tipollantaId  = document.getElementById('tipollanta_'+me.entidad).value
      let tipoId = document.getElementById('tipo_'+me.entidad).value

      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
       
      let response = await axios({
        method: 'post',
        url: '/producto',
        data: {
          'codproveedor': codProveedor,
          'producto': producto,
          'marca': marca,
          'modelo': modelo,
          'sistemaauto': sistemaAuto,
          'tipobateria': tipoBateria,
          'medida': medida,
          'tipoid': tipoId,
          'tipollantaid': tipollantaId,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': me.token
        }
      })
      
      me.productos = response.data.productos
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
     
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-productos')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    },
    abrirTipoCambio () {
      let me = this
      me.isValidSession()
   
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.gestiontipocambio.showModal()
   
    },
    excel () {
      let me = this
      let codProveedor  = document.getElementById('codProveedor_'+me.entidad).value
      let producto      = document.getElementById('producto_'+me.entidad).value
      let marca         = document.getElementById('marca_'+me.entidad).value
      let modelo        = document.getElementById('modelo_'+me.entidad).value
      let sistemaAuto   = document.getElementById('sistemaAuto_'+me.entidad).value
      let tipoBateria        = document.getElementById('tipobateria_'+me.entidad).value
      let medida        = document.getElementById('medida_'+me.entidad).value
      let tipollantaId = document.getElementById('tipollanta_'+me.entidad).value
      let tipoId = document.getElementById('tipo_'+me.entidad).value
 
      window.open(`/producto/excel?codproveedor=${codProveedor}&producto=${producto}&marca=${marca}&modelo=${modelo}&sistemaauto=${sistemaAuto}&tipobateria=${tipoBateria}&medida=${medida}
      &tipoid=${tipoId}&tipollantaid=${tipollantaId}`,'_blank')
  
    },
    async cargarDatos () {
      let me = this
      
      // let tiposLlantas = await axios('/tiposLlantas')
      // let acabados = await axios.get('/acabados')
      // let tipos = await axios.get('/tipos')
      
      // me.tiposLlantas = tiposLlantas.data.tiposLlantas
      // me.acabados = acabados.data.acabados
      // me.tipos = tipos.data.tipos
  
      me.busqueda()
    },
    async eliminar(id, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar el Producto Tipo <strong>' + nombre + '</strong> ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Sí, Eliminar',
        cancelButtonText: 'No, Cancelar',
        allowOutsideClick: false,
        closeOnConfirm: false,
        closeOnCancel: false
      }).then(async function (result) {
        if (result.value) {
          let response = await axios({
            method: 'post',
            url: '/eliminarproducto',
            data: {
              'id': id,
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Producto Eliminado con Éxito.',
                type: 'success'
              }).then(function () {
                me.busqueda()
              })
            }, 500)
          } else {
            swal('¡Cancelado!', 'Operación Cancelada', 'error')
          }
        // } else {
        //   swal('¡Cancelado!', 'Operación Cancelada', 'error')
        }
      })
    }
  },
  async created () {
    // let me = this
    // me.cargarDatos()
  },
  activated: async function () {
    let me = this
    me.isValidSession()
   
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')
    // window.setTimeout( function () {
    me.cargarDatos()
    // }, 1000)
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false 
  },
  beforeDestroy: function () {
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
  }
}
</script>