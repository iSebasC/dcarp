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
              <li class="breadcrumb-item active">Reporte de Entradas y Salidas</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card" style="height:100%;">
                  <div class="card-header">
                      <h4 class="card-title">Entradas y Salidas</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                              <div class="col-md-10">
                                <div class="row">
                                  <div class="col-md-5 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'almacen_'+entidad" class="col-form-label">Almacén de Salida <span class="text-danger">*</span></label>
                                      <select class="custom-select custom-select-sm" v-model="almacenId" name="arrAlmacen" :id="'almacen_'+entidad" @change="busquedaProductos()">
                                        <option v-for="(item, index) in arrAlmacen" :key="index" :value="item.value" v-text="item.label"></option>
                                      </select>
                                    </div>
                                  </div>

                                  <div class="col-md-3 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'tipo_producto_'+entidad" class="col-form-label">Tipo de Producto</label>
                                      <select class="custom-select custom-select-sm" name="arrTipos" :id="'tipo_producto_'+entidad" @change="busquedaProductos()">
                                         <option v-for="(item, index) in arrTipos" :key="index" :value="item.value" v-text="item.label"></option>
                                      </select>
                                    </div>
                                  </div>

                                  <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'producto_'+entidad" class="col-form-label">Producto </label>
                                      <input type="text" class="form-control form-control-sm" name="descProducto" :id="'producto_'+entidad" @keypress.enter="busquedaProductos()"/>
                                    </div>
                                  </div>
                                  
                                </div>

                                <!-- <div class="row">
                                  <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                  <div class="col-sm-2">
                                    <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                      @keyup.enter="busquedaProductos" />
                                  </div>

                                  <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                  <div class="col-sm-2">
                                      <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                      @keyup.enter="busquedaProductos" />
                                  </div>
                                </div> -->
                              </div>
                              <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-info mb-1" title="Buscar" 
                                  @click="busquedaProductos"><i class="mdi mdi-magnify icon-size"></i></button>
                                </div>
                              
                                <div class="form-group row">
                                  <label class="col-md-5 label-control pt-1 col-form-label pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                    <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaProductos">
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
                      <span class="text-info pl-2 ml-1 mb-2 hidden col-form-label" :id="'loading_'+entidad"><Loader /> </span>
                      <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad">
                          <table class="table table-hover table-compact mb-0">
                            <thead>
                              <tr>
                                    <th class="text-left">#</th>
                                    <th class="text-center">Tipo Producto</th>
                                    <th class="text-center">Producto</th>
                                    <th class="text-center">Modelo</th>
                                    <th class="text-center">Medida</th>
                                    <th class="text-center">Sistema</th>
                                    <th class="text-center">Stock Actual</th>
                                    <!-- <th class="text-center">Costo</th>
                                    <th class="text-center">Moneda Costo</th> -->
                                    <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!detalles.length">
                                  <td class="text-left text-danger" colspan="6"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in detalles" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center">
                                      <span class="badge badge-pill badge-success px-1" v-text="p.tipoProducto"></span>
                                    </td>
                                    <td class="text-center" v-text="p.nombre!=null?p.nombre:p.nombre2"></td>
                                    <td class="text-center" v-text="p.modelo"></td>
                                    <td class="text-center" v-text="p.medida"></td>
                                    <td class="text-center" v-text="p.sistema"></td>
                                    <td class="text-center"><strong v-text="p.stock"></strong></td>
                                    <!-- <td class="text-center" v-text="mostrarParam(p,1)"></td>
                                    <td class="text-center"><mark v-text="(mostrarParam(p,0)=='D'?'DÓLARES':'SOLES')"></mark></td> -->
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="generarDetalles(p.id, (p.nombre!=null?p.nombre:p.nombre2))" 
                                        title="Detalles de Movimiento" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-arrow-up-drop-circle icon-size"></i>
                                        </button>
                                        <!-- <button @click="eliminar(p.id,p.tipo, p.documento)" title="Eliminar Operación" class="btn btn-sm btn-danger">
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
                                <a class="page-link bg-info text-white" href="javascript:void(0);" aria-label="total"><strong>TOTAL: </strong><span v-text="total"></span></a>
                              </li>

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarInventario('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarInventario(op.opc):'')" >
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
    <DetalleProducto ref="detalleproducto"></DetalleProducto>
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import DetalleProducto from './DetallesProducto'
import Loader from '../Loader'

export default {
  name: 'ReporteES',
  mixins: [misMixins],
  components: {
    DetalleProducto,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      arrTipos: [],
      arrProducto: [],
      arrAlmacen: [],
      detalles: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      token: this.$store.state.token,
      almacenId: 2,
      tipoId: '',
      entidad:'reporte_inv_ES',
      bandRender: false
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

      me.busquedaProductos()
    },
    async seleccionarAlmacenSalida (item) {
        let me = this
        me.almacenId = item.value
        me.busquedaProductos()
    },
    async seleccionarTipoProducto (item) {
        let me = this
        console.log('item', item);
        me.tipoId = item.value
        me.busquedaProductos()
    },
    async seleccionarProducto (item) {
        let me = this
        me.productoId = item.value
        me.busquedaProductos()
    },
    async busquedaProductos() {
      let me = this
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
        //   me.almacenId = document.getElementById('almacen_'+me.entidad).value
      
      me.tipoId = document.getElementById('tipo_producto_'+me.entidad).value
      let producto = document.getElementById('producto_'+me.entidad).value
      

      let response = await axios({
        method: 'post',
        url: '/getproductorep',
        data: {
          'almacenId': me.almacenId,
          'producto': producto,
          'tipoId': me.tipoId,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': me.token
        }
      })
      
      me.detalles = response.data.detalles
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla_'+me.entidad)
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    generarDetalles(id, descripcion) {
      let me = this
    //   me.almacenId = idalmacen
    //   me.doc = documento
    //   this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalleproducto.showModal(id, descripcion, me.almacenId)
    },
    async cargarDatos () {
      let me = this
      me.arrAlmacen = []
      let locales = await axios.get('/getalmacenes')
      let array = locales.data.locales
      let datos = []
      array.forEach(element => {
        var l =  { value:element.id, label: element.direccion+'- '+element.departamento}
        datos.push(l)
      })
      me.arrAlmacen = datos
   
      let datos02 = [
        {value:'',  label: 'Todos'},
        {value:'A',  label: 'Accesorios/Repuestos'},
        {value:'LL', label: 'Neumáticos'},
        {value:'I',  label: 'Insumos'},
        {value:'M',  label: 'Muelles'},
        // {value:'At',  label: 'Autos'},
      ]
      me.arrTipos = datos02
    //   me.busquedaProductos()
    },
    async eliminar(id, tipo, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar la '+ tipo+ ' <strong>' + nombre + '</strong> ?',
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
            url: '/eliminarguia',
            data: {
              'id': id,
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Operación Eliminada con Éxito.',
                type: 'success'
              }).then(function () {
                me.busquedaProductos()
              })
            }, 500)
          } else {
            swal('¡Cancelado!', 'Operación Cancelada', 'error')
          }
        // } else {
        //   swal('¡Cancelado!', 'Operación Cancelada', 'error')
        }
      })
    },
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
    
   
    me.cargarDatos()
    window.setTimeout( function () {
        me.busquedaProductos()
    }, 500)
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