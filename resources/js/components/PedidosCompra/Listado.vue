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
              <li class="breadcrumb-item active">Pedidos de Compra
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Compra">
          <router-link tag="a" to="/pedidocompra/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" type="button">
          <i class="mdi mdi-plus icon-size"></i> Agregar Pedido de Compra</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Pedidos de Compra</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                      <div class="form-group row">
                                        <label :for="'documento_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">DNI/RUC:</label>
                                        <div class="col-sm-2">
                                          <input type="text" name="documento" class="form-control form-control-sm" :id="'documento_'+entidad" 
                                            @keyup.enter="busquedaCompra" @keypress="soloNumeros($event)" maxlength="11" />
                                        </div>

                                        <label :for="'proveedor_'+entidad" class="col-sm-2 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Proveedor:</label>
                                        <div class="col-sm-4">
                                          <input type="text" name="proveedor" class="form-control form-control-sm" :id="'proveedor_'+entidad" 
                                            @keyup.enter="busquedaCompra" />
                                        </div>
                                        
                                        <label :for="'tipoMoneda_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Moneda:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoMoneda_'+entidad" @change="busquedaCompra">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tiposCambio" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                        <!-- <label :for="'flete_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Flete:</label>
                                        <div class="col-sm-2">
                                          <input type="number" step="0.01" name="flete" class="form-control form-control-sm text-center" :id="'flete_'+entidad" 
                                            @keyup.enter="busquedaCompra" />
                                        </div> -->
                                      </div>
                                    
                                      <div class="form-group row">
                                        <label :for="'comprobante_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">N° Pedido:</label>
                                        <div class="col-sm-2">
                                          <input type="text" name="comprobante" class="form-control form-control-sm" :id="'comprobante_'+entidad" 
                                            @keyup.enter="busquedaCompra" />
                                        </div>

                                        <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                        <div class="col-sm-2">
                                          <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                            @keyup.enter="busquedaCompra" />
                                        </div>

                                        <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                        <div class="col-sm-2">
                                            <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                            @keyup.enter="busquedaCompra" />
                                        </div>

                                        <label :for="'tipoDocumento_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Situación:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoDocumento_'+entidad" @change="busquedaCompra">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                      </div>

                                      <!-- <div class="form-group row">
                                      </div> -->

                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 pt-xs-1 pl-xs-2">
                                    <div class="content-buttons btn-group">
                                      <button type="button" class="btn btn-icon btn-sm btn-info mb-1" title="Buscar" 
                                      @click="busquedaCompra"><i class="mdi mdi-magnify icon-size"></i></button>
                                     
                                      <!-- <button type="button" @click="excel" class="btn btn-sm btn-icon btn-success mb-1" 
                                      title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button> -->
                                    </div>
                                  

                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                        <select class="custom-select custom-select-sm ml-1 pr-2" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaCompra">
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
                      <span class="text-info pl-2 ml-1 mb-2 col-form-label" :id="'loading_'+entidad"><Loader/></span>
                      <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad">
                          <table class="table mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Ruc/Razón Social Proveedor</th>
                                <th class="text-center">Comprobante</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-center">Fecha de Servicio</th>
                                <th class="text-center">Ref. Compra</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Fecha de Registro</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!compras.length">
                                  <td class="text-left text-danger" colspan="7"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in compras" :key="p.id" :class="(p.eliminado=='S'?'table-danger':'')">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.fecha"></td>
                                    <td class="text-center" v-text="p.documentoProveedor + ' - '+p.proveedor"></td>
                                    <td class="text-center"><strong v-text="p.comprobante"></strong></td>
                                    <td class="text-center" v-text="p.tipoMoneda"></td>
                                    <td class="text-center" v-text="p.fechaServicio"></td>
                                    <td class="text-center">
                                      <span :class="'badge badge-pill ' + (p.docCompra!='-'?'badge-info':'badge-danger')" v-text="p.docCompra"></span>
                                    </td>
                                    <td class="text-right" v-text="p.total"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <!-- <button @click="generarDetalles(p.id,p.documento)" title="Ver Detalles" class="btn btn-sm btn-info">
                                          <i class="mdi mdi-archive-check-outline icon-size"></i>
                                        </button>
                                       -->
                                        <router-link v-if="p.situacion == 'P' && p.eliminado == 'N'"
                                          tag="a" title="Convertir en Compra"
                                          v-bind:to="{name: 'convertCompra', params:{pedidoId:p.id}}"
                                          class="btn btn-sm btn-success"
                                        >
                                          <i class="mdi mdi-transfer icon-size"></i>
                                        </router-link>
                                      
                                        <button v-if="p.eliminado=='N'" @click="anular(p.id,p.comprobante, p.proveedor)" title="Anular Pedido de Compra" class="btn btn-sm btn-danger">
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarCompra('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarCompra(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarCompra('next'):'')">
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

    <Detalles ref="detalles" :idventa="this.idVenta" :documento="this.documento"></Detalles>
    <!-- <NotaCredito ref="notacredito" :idventa="this.idVenta" :documento="this.documento" :proveedor="this.proveedor"></NotaCredito> -->
   
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Detalles from './Detalles'
import Loader from '../Loader'
// import NotaCredito from './NotaCredito'

export default {
  name: 'PedidoCompra',
  mixins: [misMixins],
  components: {
    Detalles
    // NotaCredito
    ,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      compras: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idVenta: '',
      documento:'',
      proveedor: '',
      tipos: [
        {id:'P', nombre: 'Pendiente'},
        {id: 'T', nombre:'Tranformado'}
      ],
      tiposCambio: [
        {id: 'S', nombre: 'Soles'},
        {id: 'D', nombre: 'Dólares'}
      ],
      entidad: 'pedido_compra',
      bandRender: false
    }
  },
  computed: {
  },
  methods: {
    buscarCompra: function (attr) {
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
      me.busquedaCompra()
    },
    async busquedaCompra() {
      let me = this
      let documento = document.getElementById('documento_'+me.entidad).value
      let proveedor = document.getElementById('proveedor_'+me.entidad).value
      // let flete = document.getElementById('flete_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      let moneda = document.getElementById('tipoMoneda_'+me.entidad).value
      let tipoDoc = document.getElementById('tipoDocumento_'+me.entidad).value
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/pedidocompra',
        data: {
          'documento': documento,
          'proveedor': proveedor,
          // 'flete': flete,
          'comprobante': comprobante,
          'fechaI': fechaI,
          'fechaF': fechaF,
          'moneda': moneda,
          'situacion': tipoDoc,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.compras = response.data.compras
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-compra')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      me.busquedaCompra()
    },
    excel () {
      let me = this
      let documento = document.getElementById('documento_'+me.entidad).value
      let proveedor = document.getElementById('proveedor_'+me.entidad).value
      // let flete = document.getElementById('flete_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      let moneda = document.getElementById('tipoMoneda_'+me.entidad).value
      // let tipoDoc = document.getElementById('tipoDocumento_'+me.entidad).value
       
    
      window.open(`/excel/pedidocompra?doc=${documento}&prov=${proveedor}&flete=${flete}&comp=${comprobante}
      &fechaI=${fechaI}&fechaF=${fechaF}&moneda=${moneda}`,'_blank')
  
    },
    generarDetalles(idventa, documento) {
      let me = this
      me.idVenta = idventa
      me.documento = documento
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalles.showModal(idventa)
    },
    anular (id,documento, proveedor) {
      let me = this
      me.idVenta = id
      me.documento = documento
      me.proveedor = proveedor
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Anular el Pedido de Compra <strong>' + me.documento + 
        '</strong> del Proveedor <strong>'+me.proveedor+'</strong>?<br><small style="color:red;"><strong>NOTA IMPORTANTE</strong><br>Pedido de Compra no debe estar transformada a Compra</small> ',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Sí, Anular',
        cancelButtonText: 'No, Cancelar',
        allowOutsideClick: false,
        closeOnConfirm: false,
        closeOnCancel: false
      }).then(async function (result) {
        if (result.value) {
          let response = await axios({
            method: 'post',
            url: `/eliminarpedidocompra/${me.idVenta}`,
            data: {
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Anulado!',
                text: 'Pedido de Compra Anulada con Éxito.',
                type: 'success'
              }).then(function () {
                me.busquedaCompra()
              })
            }, 500)
          } else {
          swal('¡Cancelado!', response.data.errores, 'error')
        }
        } 
        // else {
        //   swal('¡Cancelado!', 'Operación Cancelada', 'error')
        // }
      })

    },
  
  },
  created() {
    // this.$on('buscarComprobantes', function () {
    //   this.cargarDatos()
    // })
    
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
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
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
  
    // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  }
}
</script>