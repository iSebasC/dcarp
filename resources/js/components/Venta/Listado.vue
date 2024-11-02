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
              <li class="breadcrumb-item active">Ventas
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right ml-1" role="group" aria-label="Agregar Venta de Autos">
          <router-link tag="a" to="/venta/crearauto" class="btn btn-danger btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-car-search-outline icon-size"></i> Agregar Venta Auto</router-link>
        </div>
    
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Venta">
          <router-link tag="a" to="/venta/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Venta</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Ventas</h4>
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
                                          @keyup.enter="busquedaVenta" @keypress="soloNumeros($event)" maxlength="11" />
                                      </div>

                                      <label :for="'cliente_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Cliente:</label>
                                      <div class="col-sm-4">
                                        <input type="text" name="cliente" class="form-control form-control-sm" :id="'cliente_'+entidad" 
                                          @keyup.enter="busquedaVenta" />
                                      </div>

                                      <label :for="'comprobante_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Comprobante:</label>
                                      <div class="col-sm-2">
                                        <input type="text" name="comprobante" class="form-control form-control-sm" :id="'comprobante_'+entidad" 
                                          @keyup.enter="busquedaVenta" />
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <label :for="'registrado_por_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Registrado Por:</label>
                                      <div class="col-sm-3">
                                        <input type="text" name="registrado_por" class="form-control form-control-sm" :id="'registrado_por_'+entidad" 
                                          @keyup.enter="busquedaVenta" />
                                      </div>

                                      <label :for="'tipo_documento_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Tipo Documento:</label>
                                      <div class="col-sm-3">
                                        <select class="custom-select custom-select-sm" :id="'tipo_documento_'+entidad" @change="busquedaVenta">
                                          <!-- <option value="" disabled="" selected="">Tipo de Documento</option> -->
                                          <option value="todo">Todos</option>
                                          <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                        </select>
                                      </div>
                                    </div>

                                    <div class="form-group row">
                                      <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                      <div class="col-sm-2">
                                        <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                          @keyup.enter="busquedaVenta" />
                                      </div>

                                      <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                      <div class="col-sm-2">
                                          <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                          @keyup.enter="busquedaVenta" />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-info mb-1" title="Buscar" @click="busquedaVenta"><i class="mdi mdi-magnify icon-size"></i></button>
                                  
                                  <button type="button" @click="excel" class="btn btn-success btn-sm mb-1" title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button>
                                </div>
                              
                                <div class="form-group row">
                                  <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                    <select class="custom-select ml-1 custom-select-sm pr-2" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaVenta">
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
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Doc. Cliente</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">T. de Comprobante</th>
                                <th class="text-center">Comprobante</th>
                                <th class="text-center">Situación</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!ventas.length">
                                  <td class="text-left text-danger" colspan="6"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in ventas" :key="p.id" :class="p.situacion !='V'?'table-danger':''">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.fecha"></td>
                                    <td class="text-center"><strong v-text="p.doc_cliente"></strong></td>
                                    <td class="text-center" v-text="p.cliente"></td>
                                    <td class="text-center"><span class="badge badge-pill badge-warning" v-text="p.tipoComprobante"></span></td>
                                    <td class="text-center" v-text="p.documento"></td>
                                    <td class="text-center"><span :class="'badge badge-pill '+(p.situacion=='V'?'badge-success':'badge-danger')" v-text="p.situacion=='V'?'Vigente':(p.situacion=='NC'?'Nota de Crédito':(p.situacion == 'A' && p.situacionSUNAT != 'R'?'Anulado':(p.situacion == 'A' && p.situacionSUNAT == 'R'?'Anulado [Rechazado por SUNAT]':'Nota de Débito')))"></span></td>
                                    <td class="text-right" v-text="p.total"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="generarDetalles(p.id,p.documento)" v-if="p.tipoComprobante == 'BOLETA' || p.tipoComprobante == 'FACTURA'" title="Ver Detalles" class="btn btn-sm btn-warning">
                                          <i class="mdi mdi-archive-check-outline icon-size"></i>
                                        </button> 
                                        <button @click="verPdf(p.idDocumentoSUNAT)" title="Ver Documento" class="btn btn-sm btn-info">
                                          <i class="mdi mdi-text-box-search-outline icon-size"></i>
                                        </button>
                                        <a :href="'/storage/xml_doc/'+p.nombreDocumentoSUNAT+'zip'" downoload="filename"  title="Ver XML de Comprobante" class="btn btn-sm btn-primary">
                                          <i class="mdi mdi-folder-zip-outline icon-size"></i>
                                        </a>
                                        <button @click="descargarCdr(p.idDocumentoSUNAT)" title="Descargar CDR" class="btn btn-sm btn-link">
                                          <i class="mdi mdi-file-sign icon-size"></i>
                                        </button>
                                       
                                        <a v-if="p.situacion == 'A'" :href="'/storage/xml_doc/'+p.documentoSUNAT02+'zip'" title="Ver XML de Anulación" class="btn btn-sm btn-success" downoload="filename">
                                          <i class="mdi mdi-file-xml-box icon-size"></i>
                                        </a>
                                        
                                        <button v-if="p.situacion == 'V' && (p.tipoComprobante == 'BOLETA' || p.tipoComprobante == 'FACTURA')" @click="anular(p.id,p.documento)" title="Anular Comprobante" class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-delete-outline icon-size"></i>
                                        </button>

                                        <!-- <button v-if="p.situacion == 'V' && (p.tipoComprobante == 'NOTA DE CREDITO' || p.tipoComprobante == 'NOTA DE DEBITO')" @click="anularNota(p.id,p.documento)" title="Anular Comprobante" class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-delete-outline-alt icon-size"></i>
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarVenta('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarVenta(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarVenta('next'):'')">
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
    <Anular ref="anular" :idventa="this.idVenta" :documento="this.documento"></Anular>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Detalles from './Detalles'
import Anular from './Anular'
import Loader from '../Loader'

export default {
  name: 'Venta',
  mixins: [misMixins],
  components: {
    Detalles,
    Anular,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      ventas: [],
      tipos: [
        {id:'B', nombre: 'Boleta'},
        {id: 'F', nombre:'Factura'},
        {id: 'NC', nombre:'Nota de Crédito'},
        {id:'ND', nombre:'Nota de Débito'}
      ],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idVenta: '',
      documento:'',
      entidad: 'venta',
      bandRender: false,
      credencialesAPI: {
        user: '20103327378',
      	pass: 'bj1R8xkhHB'
      }
    }
  },
  computed: {
  },
  methods: {
    buscarVenta: function (attr) {
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
      me.busquedaVenta()
    },
    // generarModal(id, nombre) {
    //   let me = this
    //   me.idTipo = id
    //   me.tipoUsuario = nombre
    // //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    // //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
    //    // this.$emit('abrirmodal',me.idModal)
    //   me.$refs.ventas.showModal(id)

    //   // me.mostrarModal = true
    //   // alert(me.idModal)
    //   // if (this.$store.state.mostrarModal) {
    //   // }
    //   // generarModal(id)
    // },
    async busquedaVenta() {
      let me = this
      let documento   = document.getElementById('documento_'+me.entidad).value
      let cliente     = document.getElementById('cliente_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let registrado_por = document.getElementById('registrado_por_'+me.entidad).value
      let tipo_documento = document.getElementById('tipo_documento_'+me.entidad).value
      
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/venta',
        data: {
          'documento': documento,
          'cliente': cliente,
          'comprobante': comprobante,
          'registradopor': registrado_por,
          'tipodocumento': tipo_documento,
          'fechaI': fechaI,
          'fechaF': fechaF,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.ventas = response.data.ventas
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      me.busquedaVenta()
    },
    excel () {
      let me = this
      let documento   = document.getElementById('documento_'+me.entidad).value
      let cliente     = document.getElementById('cliente_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let registrado_por = document.getElementById('registrado_por_'+me.entidad).value
      let tipo_documento = document.getElementById('tipo_documento_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      
      window.open(`/venta/excel?documento=${documento}&cliente=${cliente}&comprobante=${comprobante}&registrado=${registrado_por}&tipodoc=${tipo_documento}&fechai=${fechaI}&fechaf=${fechaF}`,
      '_blank')
  
    },
    generarDetalles(idventa, documento) {
      let me = this
      me.isValidSession()
   
      me.idVenta = idventa
      me.documento = documento
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
       // this.$emit('abrirmodal',me.idModal)
      me.$refs.detalles.showModal(idventa)

      // me.mostrarModal = true
      // alert(me.idModal)
      // if (this.$store.state.mostrarModal) {
      // }
      // generarModal(id)
    },
    verPdf(id) {
      window.open(`https://fasteinvoice.com/consultar.php?ruc=${this.credencialesAPI.user}&password=${this.credencialesAPI.pass}&id=${id}`,"_blank")
      // window.setTimeout(function () {
      //   miVentana.focus() 
      //   miVentana.print() 
      // }, 1000)
    },
    descargarCdr(id) {
      window.open(`https://fasteinvoice.com/descargarXML.php?ruc=${this.credencialesAPI.user}&password=${this.credencialesAPI.pass}&CDR=S&id=${id}`,"_blank")
    },
    anular (id,documento) {
      let me = this
      me.isValidSession()
   
      me.idVenta = id
      me.documento = documento
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
       // this.$emit('abrirmodal',me.idModal)
      me.$refs.anular.showModal(id)
    },
    async anularNota(id, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Anular El Comprobante <strong>' + nombre + '</strong> ?',
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
            url: '/anularnota',
            data: {
              'id': id,
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Anulado!',
                text: 'Comprobante Anulado con Éxito.',
                type: 'success'
              }).then(function () {
                me.cargarDatos()
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
  created() {
    this.$on('buscarComprobantes', function () {
      this.cargarDatos()
    })
    
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    me.isValidSession()
   
    if (!me.bandRender) {
      document.getElementById('fecha_i_'+me.entidad).value =  me.obtenerFechaActual()
      document.getElementById('fecha_f_'+me.entidad).value =  me.obtenerFechaActual()

      me.bandRender = true
    }
    
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
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