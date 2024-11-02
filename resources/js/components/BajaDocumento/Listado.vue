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
              <li class="breadcrumb-item active">Documentos de Baja
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Compra">
          <router-link tag="a" to="/nota/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" type="button">
          <i class="mdi mdi-plus icon-size"></i> Agregar Documento de Baja</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Documentos de Baja</h4>
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
                                            @keyup.enter="busquedaBajas" @keypress="soloNumeros($event)" maxlength="11" />
                                        </div>

                                        <label :for="'razonSocial_'+entidad" class="col-sm-2 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Razón Social:</label>
                                        <div class="col-sm-4">
                                          <input type="text" name="razon_social" class="form-control form-control-sm" :id="'razonSocial_'+entidad" 
                                            @keyup.enter="busquedaBajas" />
                                        </div>

                                        <label :for="'docReferencia_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Doc. Ref.:</label>
                                        <div class="col-sm-2">
                                          <input type="text" name="doc_referencia" class="form-control form-control-sm text-center" :id="'docReferencia_'+entidad" 
                                            @keyup.enter="busquedaBajas" />
                                        </div>
                                      </div>
                                    
                                      <div class="form-group row">
                                        <label :for="'comprobante_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Comprobante:</label>
                                        <div class="col-sm-4">
                                          <input type="text" name="comprobante" class="form-control form-control-sm" :id="'comprobante_'+entidad" 
                                            @keyup.enter="busquedaBajas" />
                                        </div>

                                        <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                        <div class="col-sm-2">
                                          <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                            @keyup.enter="busquedaBajas" />
                                        </div>

                                        <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                        <div class="col-sm-2">
                                            <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                            @keyup.enter="busquedaBajas" />
                                        </div>
                                      </div>

                                      <div class="form-group row">
                                        <label :for="'tipoOperacion_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Aplicada a:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoOperacion_'+entidad" @change="busquedaBajas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tipoOperacion" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                        
                                        
                                        <label :for="'tipoDocumento_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">T. Documento:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoDocumento_'+entidad" @change="busquedaBajas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                        
                                         <!-- <label :for="'tipoMoneda_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Moneda:</label> 
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoMoneda_'+entidad" @change="busquedaBajas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tiposCambio" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div> -->
                                      </div>

                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 pt-xs-1 pl-xs-2">
                                    <div class="content-buttons btn-group">
                                      <button type="button" class="btn btn-icon btn-sm btn-info mb-1" title="Buscar" 
                                      @click="busquedaBajas"><i class="mdi mdi-magnify icon-size"></i></button>
                                     
                                      <!-- <button type="button" @click="excel" class="btn btn-sm btn-icon btn-success mb-1" 
                                      title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button> -->
                                    </div>
                                  

                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                        <select class="custom-select custom-select-sm ml-1 pr-2" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaBajas">
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
                      <div class="table-responsive px-1 d-none" :id="'tabla_'+entidad">
                          <table class="table mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Ruc/Razón Social</th>
                                <th class="text-center">Comprobante</th>
                                <th class="text-center">Doc. Referencia</th>
                                <th class="text-center">Aplicada a</th>
                                <th class="text-center">Situación</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Fecha de Registro</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!notas.length">
                                  <td class="text-left text-danger" colspan="9"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in notas" :key="p.id" :class="(p.situacion=='A'?'table-danger':'')">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.fechaAnulacion"></td>
                                    <td class="text-center" v-text="p.documento + ' - '+p.cliente"></td>
                                    <td class="text-center" v-text="p.comprobante"></td>
                                    <td class="text-center" v-text="p.referencia"></td>
                                    <td class="text-center"><mark v-text="(p.aplicada_a=='V'?'Venta':'Compra')"></mark></td>
                                    <td class="text-center"><span class="badge badge-pill badge-info"  v-text="(p.situacion=='V'?'Vigente':'Anulado')"></span></td>
                                    <td class="text-right" v-text="p.total"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="generarDetalles(p.id,p.comprobante, p.moneda)" title="Ver Detalles" class="btn btn-sm btn-warning">
                                          <i class="mdi mdi-archive-check-outline icon-size"></i>
                                        </button>

                                        <button @click="verPdf(p.idDocumentoSUNAT)" v-if="p.aplicada_a=='V'" title="Ver Documento" class="btn btn-sm btn-info">
                                          <i class="mdi mdi-file-pdf-box icon-size"></i>
                                        </button>
                                        <a :href="'/storage/xml_doc/'+p.nombreDocumentoSUNAT+'zip'" v-if="p.aplicada_a=='V'" downoload="filename"  title="Ver XML de Comprobante" class="btn btn-sm btn-primary">
                                          <i class="mdi mdi-file-xml-box icon-size"></i>
                                        </a>
                                        <button @click="descargarCdr(p.idDocumentoSUNAT)" v-if="p.aplicada_a=='V'" title="Descargar CDR" class="btn btn-sm bg-teal text-white">
                                          <i class="mdi mdi-check-all icon-size"></i>
                                        </button>
                                  
                                        <button v-if="p.situacion == 'V'" @click="anular(p.id,p.documento, p.cliente)" title="Anular Baja" class="btn btn-sm btn-danger">
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarBaja('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarBaja(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarBaja('next'):'')">
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

    <Detalles ref="detalles" :idventa="this.idVenta" :documento="this.documento" :moneda="this.moneda"></Detalles>
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
  name: 'DocumentoBaja',
  mixins: [misMixins],
  components: {
    Detalles,
    Loader
    // NotaCredito
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      notas: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idVenta: '',
      documento:'',
      proveedor: '',
      tipoOperacion: [
        {id:'C', nombre: 'Compra'},
        {id: 'V', nombre:'Venta'}
      ],
      tipos: [
        {id:'B', nombre: 'Boleta'},
        {id: 'F', nombre:'Factura'}
      ],
      tiposCambio: [
        {id: 'S', nombre: 'Soles'},
        {id: 'D', nombre: 'Dólares'}
      ],
      entidad: 'baja',
      bandRender: false,
      moneda: '',
      credencialesAPI: {
        user: '20103327378',
      	pass: 'bj1R8xkhHB'
      }
    }
  },
  computed: {
  },
  methods: {
    buscarBaja: function (attr) {
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
      me.busquedaBajas()
    },
    async busquedaBajas() {
      let me = this
      let documento = document.getElementById('documento_'+me.entidad).value
      let razonSocial = document.getElementById('razonSocial_'+me.entidad).value
      let tipoOp = document.getElementById('tipoOperacion_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let docRef = document.getElementById('docReferencia_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      // let moneda = document.getElementById('tipoMoneda_'+me.entidad).value
      let tipoDoc = document.getElementById('tipoDocumento_'+me.entidad).value
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/baja',
        data: {
          'documento': documento,
          'razonSocial': razonSocial,
          'tipoOperacion': tipoOp,
          'comprobante': comprobante,
          'docReferencia': docRef,
          'fechaI': fechaI,
          'fechaF': fechaF,
          // 'moneda': moneda,
          'tipodoc': tipoDoc,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.notas = response.data.notas
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
      me.busquedaBajas()
    },
    excel () {
      let me = this
      let documento = document.getElementById('documento_'+me.entidad).value
      let proveedor = document.getElementById('proveedor_'+me.entidad).value
      let flete = document.getElementById('flete_'+me.entidad).value
      let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      // let moneda = document.getElementById('tipoMoneda_'+me.entidad).value
      let tipoDoc = document.getElementById('tipoDocumento_'+me.entidad).value
      
    

      // if (filtro =='') {filtro = 'null'}
      // if (descripcion =='') {descripcion = 'null'}
      // if (tipodocumento =='') {tipodocumento = 'null'}
      // if (fechaI =='') {fechaI = 'null'}
      // if (fechaF =='') {fechaF = 'null'}

      // window.open('/venta/excel/'+filtro+'/'+descripcion+'/'+tipodocumento+'/'+fechaI+'/'+fechaF,'_blank')
  
    },
    generarDetalles(idventa, documento, moneda) {
      let me = this
      me.idVenta = idventa
      me.documento = documento
      me.moneda = moneda
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.detalles.showModal(idventa)
    },
    verPdf(id) {
      window.open("https://fasteinvoice.com/consultar.php?ruc=20603144954&password=valentinasunat123&id="+id,"_blank")
      // window.setTimeout(function () {
      //   miVentana.focus() 
      //   miVentana.print() 
      // }, 1000)
    },
    descargarCdr(id) {
       window.open(`https://fasteinvoice.com/descargarXML.php?ruc=${this.credencialesAPI.user}&password=${this.credencialesAPI.pass}&CDR=S&id=${id}`,"_blank")
    },
    anular (id,documento,proveedor) {
      let me = this
      me.idVenta = id
      me.documento = documento
      me.proveedor = proveedor
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar el Documento de Baja <strong>' + me.documento + 
        '</strong> del Proveedor <strong>'+me.proveedor+'</strong>?<br><small style="color:red;"><strong>NOTA IMPORTANTE</strong><br>No debe tener movimientos de stock en sus detalles.</small> ',
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
            url: `/eliminarbaja/${me.idVenta}`,
            data: {
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Documento de Baja Anulado con Éxito.',
                type: 'success'
              }).then(function () {
                me.busquedaBajas()
              })
            }, 500)
          } else {
              var arreglo = response.data.errores
              let cadena_errors = '';
              Object.values(arreglo).forEach(val => {
                  cadena_errors += val + ', '
              })

            swal('¡Cancelado!', cadena_errors, 'error')
          }
        // } else {
        //   swal('¡Cancelado!', 'Operación Cancelada', 'error')
        }
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