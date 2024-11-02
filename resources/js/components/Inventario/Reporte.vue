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
              <li class="breadcrumb-item active">Movimientos de Almacén</li>
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
                      <h4 class="card-title">Entradas y Salidas de Almacén</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                              <div class="col-md-10">
                                <div class="row">
                                  <!-- <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'arrAlmacen_'+entidad" class="col-form-label">Almacén de Salida </label>
                                      <v-select taggable :clearable="false" :options="arrAlmacen" option-value="value" 
                                      @input="seleccionarAlmacenSalida" option-text="label" placeholder="Seleccione" 
                                      name="arrAlmacen" :id="'arrAlmacen_'+entidad"></v-select>
                                    </div>
                                  </div> -->

                                  <!-- <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'arrTipos_'+entidad" class="col-form-label">Tipo de Producto </label>
                                      <v-select :clearable="false" taggable  :options="arrTipos" option-value="value" 
                                      @input="seleccionarTipoProducto" option-text="label" placeholder="Seleccione" 
                                      name="arrTipos" :id="'arrTipos_'+entidad"></v-select>
                                    </div>
                                  </div> -->

                                  <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'arrAlmacen_'+entidad" class="col-form-label">Almacén de Salida <span class="text-danger">*</span></label>
                                      <select class="custom-select custom-select-sm" v-model="almacenId" name="arrAlmacen" :id="'arrAlmacen_'+entidad" @change="busquedaDetalleInventario">
                                        <option v-for="(item, index) in arrAlmacen" :key="index" :value="item.value" v-text="item.label"></option>
                                      </select>
                                    </div>
                                  </div>

                                  <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                      <label :for="'arrTipos_'+entidad" class="col-form-label">Tipo de Producto</label>
                                      <select class="custom-select custom-select-sm" v-model="tipoId" name="arrTipos" :id="'arrTipos_'+entidad" @change="busquedaDetalleInventario">
                                         <option v-for="(item, index) in arrTipos" :key="index" :value="item.value" v-text="item.label"></option>
                                      </select>
                                    </div>
                                  </div>

                                </div>

                                <div class="row mt-1">
                                  <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                  <div class="col-sm-2">
                                    <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                      @keyup.enter="busquedaDetalleInventario" />
                                  </div>

                                  <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                  <div class="col-sm-2">
                                      <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                      @keyup.enter="busquedaDetalleInventario" />
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-info mb-1" title="Buscar" 
                                  @click="busquedaDetalleInventario"><i class="mdi mdi-magnify icon-size"></i></button>
                                  <button type="button" class="btn btn-sm btn-success mb-1" title="Exportar a Excel" 
                                  @click="excel"><i class="mdi mdi-file-excel icon-size"></i></button>
                                </div>
                              
                                <div class="form-group row">
                                  <label class="col-md-5 label-control pt-1 col-form-label pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                    <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaDetalleInventario">
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
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Documento</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">T. Producto</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">F. Creación</th>
                                    <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!detalles.length">
                                  <td class="text-left text-danger" colspan="6"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in detalles" :key="p.id" :class="p.tipo=='ENTRADA'?'table-success':'table-danger'">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.fecha"></td>
                                    <td class="text-center" v-text="p.documento"></td>
                                    <td class="text-center" v-text="p.cantidad"></td>
                                    <td class="text-justify">
                                      <p class="text-justify" v-text="p.detalles"></p>
                                    </td>
                                    <td class="text-center" v-text="p.tipoProd"></td>
                                    <td class="text-center" v-text="p.tipo"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="generarDetallesProducto(p.id, p.documento)" title="Detalles de Movimiento" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-wallet-membership icon-size"></i>
                                        </button>
                                        <button @click="eliminar(p.id,p.tipo, p.documento)" title="Eliminar Operación" class="btn btn-sm btn-danger">
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
    <DetalleGuia ref="detalleinventario" :idguia="this.guiaId" :documento="this.doc"></DetalleGuia>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import DetalleGuia from './Detalles'
import Loader from '../Loader'

export default {
  name: 'Reporte',
  mixins: [misMixins],
  components: {
    DetalleGuia,
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
      almacenId: '',
      tipoId: '',
      productoId: '',
      guiaId: '',
      doc: '',
      entidad:'reporte_inv',
      bandRender: false,
      almacenId: 2
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

      me.busquedaDetalleInventario()
    },
    async seleccionarAlmacenSalida (item) {
        let me = this
        me.almacenId = item.value
        me.busquedaDetalleInventario()
    },
    async seleccionarTipoProducto (item) {
        let me = this
        console.log('item', item);
        me.tipoId = item.value
        me.busquedaDetalleInventario()
    },
    async seleccionarProducto (item) {
        let me = this
        me.productoId = item.value
        me.busquedaDetalleInventario()
    },
    async busquedaDetalleInventario() {
      let me = this
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      

      let response = await axios({
        method: 'post',
        url: '/detallesalidasentradas',
        data: {
          'almacenId': me.almacenId,
          'productoId': me.productoId,
          'tipoId': me.tipoId,
          'fechaI': fechaI,
          'fechaF': fechaF,
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
    generarDetallesProducto(idalmacen, documento) {
      let me = this
      me.almacenId = idalmacen
      me.doc = documento
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalleinventario.showModal(idalmacen)
    },
    async cargarDatos () {
      let me = this
      me.arrAlmacen = []
      let locales = await axios.get('/getalmacenes')
      let array = locales.data.locales
      let datos = []
      // datos.push({value:'',  label: 'Todos'})
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
  
      me.detalles = []

      me.busquedaDetalleInventario()
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
                me.busquedaDetalleInventario()
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
    excel () {
        let me = this
        if (me.almacenId != '') {
          me.tipoId     = (me.tipoId == ''?'null':me.tipoId)
          let fechaI = document.getElementById('fecha_i_'+me.entidad).value
          let fechaF = document.getElementById('fecha_f_'+me.entidad).value
          
          
          window.open(`/inventario/excel2/${me.almacenId}/${me.tipoId}?fechai=${fechaI}&fechaf=${fechaF}`,'_blank')
        } else {
          swal('¡Seleccione Almacén!', 'Para Generar Reporte', 'error')
        }
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
    
    if (!me.bandRender) {
      document.getElementById('fecha_i_'+me.entidad).value = me.obtenerFechaActual()
      document.getElementById('fecha_f_'+me.entidad).value = me.obtenerFechaActual()

      me.bandRender = true
    }
   
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