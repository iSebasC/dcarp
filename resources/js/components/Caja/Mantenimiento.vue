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
              <li class="breadcrumb-item active">Caja
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group">
          <button @click="aperturar()" :disabled="banderaAperturar" title="Aperturar Caja" class="btn btn-sm btn-success btn-rounded box-shadow-2 px-2 mb-1">
            <i class="mdi mdi-plus icon-size"></i>
             Aperturar
          </button>

          <button @click="agregarMovimiento()" :disabled="!banderaAperturar" title="Agregar Movimiento de Caja" class="btn btn-sm btn-info btn-rounded box-shadow-2 px-2 mb-1">
             <i class="mdi mdi-note-plus-outline icon-size"></i>
             Agregar Movimiento
          </button>

          <button @click="cerrar()" title="Cerrar Caja" :disabled="!banderaAperturar" class="btn btn-sm btn-danger btn-rounded box-shadow-2 px-2 mb-1">
             <i class="mdi mdi-tab-minus icon-size"></i>
             Cerrar
          </button>
        </div>
      </div>
    </div>
    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Arqueo de Caja</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                              <div class="col-md-10">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="form-group row">
                                      <label :for="'filtro_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Buscar Por:</label>
                                      <div class="col-md-2">
                                        <select class="custom-select custom-select-sm" :id="'filtro_'+entidad" @change="busquedaCaja">
                                          <!-- <option value="" disabled="" selected="">Buscar por</option> -->
                                          <option value="">Todo</option>
                                          <option value="I">Ingresos</option>
                                          <option value="E">Egresos</option>
                                        </select> 
                                      </div>
                                    </div>
                                  </div>                                       
                                  <input name="cajaId" type="hidden" :value="cajaId" />
                                </div>
                              </div>
                              <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-icon btn-info mb-1" 
                                  title="Buscar" @click="busquedaCaja"><i class="mdi mdi-magnify icon-size"></i></button>

                                  <button type="button" class="btn btn-sm btn-icon btn-danger mb-1" title="Exportar a Pdf" :disabled="!banderaAperturar" @click="pdf"><i class="mdi mdi-file-pdf-box icon-size"></i></button>  
                                </div>

                                <div class="form-group row">
                                  <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                      <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaCaja">
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
                      <span class="col-form-label text-info pl-2 ml-1 mb-2" :id="'loading_'+entidad"><Loader /></span>
                      <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad">
                          <table class="table table-hover table-compact mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Cliente/Descripción</th>
                                <th class="text-center">Ingresos</th>
                                <th class="text-center">Egresos</th>
                                <th class="text-center">Forma Pago</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!detalles.length">
                                  <td class="text-left text-danger" colspan="5"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in detalles" :key="p.id" :class="(p.tipo=='I'&&p.tipopago!='A'?'table-success':(p.tipopago=='A'?'table-warning':'table-danger'))">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center"><strong v-text="p.concepto"></strong></td>
                                    <td class="text-right" v-text="p.tipo=='I'?p.total:''"></td>
                                    <td class="text-right" v-text="p.tipo=='E'?p.total:''"></td>
                                    <td class="text-center"><strong v-text="(p.id == 0 && p.tipopago!='A'?'Venta - ':'')+p.metodoPago"></strong></td>
                                    <td class="text-center" v-text="p.moneda"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <a v-if="p.id != 0" href="javascript:void(0)" class="btn btn-sm btn-danger" @click="eliminar(p.id, p.concepto)" title="Anular Operación" style="padding:0.25rem 0.45rem;">
                                            <i class="mdi mdi-minus-thick"></i>
                                        </a>
                                      </div>
                                    </td>
                                </tr>
                            </tbody>
                          </table>

                          <nav class="pl-2 mt-1">
                            <ul class="pagination justify-content-right">
                              <li class="page-item">
                                <a class="page-link bg-info text-white" href="javascript:void(0);" aria-label="total"><strong>TOTAL: </strong><span v-text="total"></span></a>
                              </li>

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarCaja('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarCaja(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarCaja('next'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Next">»</a>
                              </li>
                            </ul>
                          </nav>
                      </div>

                      <div class="col-lg-12 mt-4" id="divTablaBalance">
                        <div class="row clearfix">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-4">
                                <table class="table table-hover table-bordered">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th colspan="3" class="text-center">TOTAL</th>
                                        </tr>
                                        <tr>
                                          <th class="text-center"></th>
                                          <th class="text-center">S/</th>
                                          <th class="text-center">$</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Ingresos:</strong></td>
                                            <td class="ingresos text-right" v-text="ingresos"></td>
                                            <td class="ingresos text-right" v-text="ingresosD"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Efectivo:</strong></td>
                                            <td class="efectivo text-right" v-text="efectivo"></td>
                                            <td class="efectivo text-right" v-text="efectivoD"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Depósitos:</strong></td>
                                            <td class="depositos text-right" v-text="depositos"></td>
                                            <td class="depositos text-right" v-text="depositosD"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tarjeta:</strong></td>
                                            <td class="tarjeta text-right" v-text="tarjeta"></td>
                                            <td class="tarjeta text-right" v-text="tarjetaD"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Egresos:</strong></td>
                                            <td class="egresos text-right" v-text="egresos"></td>
                                            <td class="egresos text-right" v-text="egresosD"></td>
                                        </tr>
                                        <tr class="bg-primary text-white">
                                            <td><strong>Saldo Neto:</strong></td>
                                            <td class="saldoNeto text-right font-weight-bold" v-text="neto"></td>
                                            <td class="saldoNeto text-right font-weight-bold" v-text="netoD"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <AperturarCaja ref="aperturar"></AperturarCaja>
    <CerrarCaja ref="cerrar"></CerrarCaja>
    <MovimientoCaja ref="movimiento"></MovimientoCaja>
    
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import AperturarCaja from './Aperturar'
import CerrarCaja from './Cerrar'
import MovimientoCaja from './Movimientos'
import Loader from '../Loader'


export default {
  name: 'Caja',
  mixins: [misMixins],
  components: {
    AperturarCaja,
    CerrarCaja,
    MovimientoCaja,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      detalles: [],
      opciones: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      token: this.$store.state.token,
      banderaAperturar: false,
      cajaId: '',
      ingresos: 0.00,
      efectivo: 0.00,
      egresos: 0.00,
      neto: 0.00,
      depositos: 0.00,
      tarjeta: 0.00,
      ingresosD: 0.00,
      efectivoD: 0.00,
      egresosD: 0.00,
      netoD: 0.00,
      depositosD: 0.00,
      tarjetaD: 0.00,
      entidad: 'mant_caja',
    }
  },
  computed: {

    // incremKey: function(val) {
    //     let increment = this.inicio
    //     // if (val === 0) {
    //     val += 1
    //     // }
    //     return ((val + increment)<10?'0'+val:val)
    // }
  },
  methods: {
    buscarCaja: function (attr) {
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

      me.busquedaCaja()
    },
    async busquedaCaja() {
      let me = this
      let filtro = document.getElementById('filtro_'+me.entidad).value
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/caja',
        data: {
          'filtro': filtro,
          'descripcion': '',
          'cajaId': me.cajaId,
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
      
      me.ingresos = response.data.ingresos
      me.egresos = response.data.egresos
      me.depositos = response.data.depositos
      me.neto = response.data.neto
      me.efectivo = response.data.efectivo
      me.tarjeta = response.data.tarjeta
     
      me.ingresosD = response.data.ingresosD
      me.egresosD = response.data.egresosD
      me.depositosD = response.data.depositosD
      me.netoD = response.data.netoD
      me.efectivoD = response.data.efectivoD
      me.tarjetaD = response.data.tarjetaD
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-caja')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      let cajaAbierta = await axios.get('/getcajaabierta');

      me.banderaAperturar =  cajaAbierta.data.abierta
      me.cajaId = cajaAbierta.data.id
      me.busquedaCaja()
    },
    aperturar () {
        let me = this
        me.isValidSession()
    
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
        //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
        // this.$emit('abrirmodal',me.idModal)
        me.$refs.aperturar.showModal()
    },
    cerrar () {
        let me = this
        me.isValidSession()
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
        //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
        // this.$emit('abrirmodal',me.idModal)
        me.$refs.cerrar.showModal(me.cajaId)
    }, 
    agregarMovimiento () {
      let me = this
      me.isValidSession()
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.movimiento.showModal(me.cajaId)
    },
    async eliminar(id, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar la Operación <strong>' + nombre + '</strong> ?',
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
            url: '/eliminarcaja',
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
                me.busquedaCaja()
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
    pdf () {
      let me = this
      if (me.cajaId != '') {
        window.open('/caja/pdf/'+me.cajaId,'_blank')
      }
    }
  },
  created() {
    this.$on('buscar', function () {
      this.cargarDatos()
    })
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal 
    me.isValidSession()
    // window.setTimeout( function () {
    me.cargarDatos()
    // }, 1000)
  },
  beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
  }
}
</script>
<style scoped="">
    button{
        cursor: pointer;
    }
</style>