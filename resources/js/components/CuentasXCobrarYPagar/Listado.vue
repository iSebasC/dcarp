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
              <li class="breadcrumb-item active">Cuentas por Cobrar & Pagar
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Compra">
          <router-link tag="a" to="/cuentaxcyp/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" type="button">
          <i class="mdi mdi-plus icon-size"></i> Agregar Cuenta</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Cuentas por Cobrar & Pagar</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-12 col-xs-12 mt-2">
                                      <div class="form-group row">
                                        <label :for="'tipo_cuenta_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">T. Cuenta:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipo_cuenta_'+entidad" @change="buscarCuentas">
                                            <option v-for="f in tiposcuenta" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                        
                                        <label :for="'documento_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Documento:</label>
                                        <div class="col-sm-2">
                                          <input type="text" name="documento" class="form-control form-control-sm" :id="'documento_'+entidad" 
                                            @keyup.enter="buscarCuentas" @keypress="soloNumeros($event)" maxlength="12" />
                                        </div>

                                        <label :for="'razonSocial_'+entidad" class="col-sm-2 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Cliente/Proveedor:</label>
                                        <div class="col-sm-4">
                                          <input type="text" name="razon_social" class="form-control form-control-sm" :id="'razonSocial_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div>
<!-- 
                                        <label :for="'docReferencia_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">N° Reclamo:</label>
                                        <div class="col-sm-2">
                                          <input type="text" name="doc_referencia" class="form-control form-control-sm text-center" :id="'docReferencia_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div> -->
                                      </div>
                                    
                                      <div class="form-group row">
                                        <label :for="'serie_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Serie:</label>
                                        <div class="col-sm-1">
                                          <input type="text" name="serie" maxlength="5" class="form-control form-control-sm" :id="'serie_'+entidad" 
                                            @keypress="soloLetras($event)"
                                            @keyup.enter="buscarCuentas" />
                                        </div>

                                        <label :for="'numero_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Número: </label>
                                        <div class="col-sm-1">
                                          <input type="text" name="numero" class="form-control form-control-sm" :id="'numero_'+entidad" 
                                          @keypress="soloNumeros($event)" maxlength="8"  
                                          @keyup.enter="buscarCuentas" />
                                        </div>

                                        <label :for="'moneda_'+entidad" class="col-sm-1 text-md-center m-4px col-form-label pr-0 mr-0 pt-0">Moneda:</label>
                                        <div class="col-sm-1">
                                          <select class="custom-select custom-select-sm" :id="'moneda_'+entidad" @change="buscarCuentas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in monedas" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>

                                        <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                        <div class="col-sm-2">
                                          <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div>

                                        <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                        <div class="col-sm-2">
                                            <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div>
                                      </div>

                                      <div class="form-group row">
                                        <label :for="'registrado_por_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Registrado Por:</label>
                                        <div class="col-sm-3">
                                          <input type="text" name="registrado_por" class="form-control form-control-sm" :id="'registrado_por_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div>
                                    
                                        <label :for="'fecha_venc_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Venc. :</label>
                                        <div class="col-sm-2">
                                            <input type="date" name="fecha_v" class="form-control form-control-sm pr-0" :id="'fecha_venc_'+entidad" 
                                            @keyup.enter="buscarCuentas" />
                                        </div>
                                      
                                        <label :for="'operacion_'+entidad" class="col-sm-1 text-md-center m-4px col-form-label pr-0 mr-0 pt-0">Operación:</label>
                                        <div class="col-sm-1">
                                          <select class="custom-select custom-select-sm" :id="'operacion_'+entidad" @change="buscarCuentas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in operaciones" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>

                                        <label :for="'situacion_'+entidad" class="col-sm-1 text-md-center m-4px col-form-label pr-0 mr-0 pt-0">Situación:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'situacion_'+entidad" @change="buscarCuentas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in situaciones" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div>
                                        
                                        <!--
                                        <label :for="'tipoDocumento_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">T. Documento:</label>
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoDocumento_'+entidad" @change="buscarCuentas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div> -->
                                        
                                         <!-- <label :for="'tipoMoneda_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Moneda:</label> 
                                        <div class="col-sm-2">
                                          <select class="custom-select custom-select-sm" :id="'tipoMoneda_'+entidad" @change="buscarCuentas">
                                            <option value="todo">Todos</option>
                                            <option v-for="f in tiposCambio" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                          </select>
                                        </div> -->
                                      </div>

                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-2 pt-xs-1 pl-xs-2 my-2">
                                    <div class="content-buttons btn-group">
                                      <button type="button" class="btn btn-icon btn-sm btn-info mb-1" title="Buscar" 
                                      @click="buscarCuentas"><i class="mdi mdi-magnify icon-size"></i></button>
                                     
                                      <!-- <button type="button" @click="excel" class="btn btn-sm btn-icon btn-success mb-1" 
                                      title="Exportar a Excel"><i class="mdi mdi-file-excel icon-size"></i></button> -->
                                    </div>
                                  

                                <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                        <select class="custom-select custom-select-sm ml-1 pr-2" :id="'cantidad_'+entidad" title="Registros por Página" @change="buscarCuentas">
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
                                <th class="text-center">T. Cuenta</th>
                                <th class="text-center">Doc./Razón Social</th>
                                <th class="text-center">Tipo Documento</th>
                                <th class="text-center">Comprobante</th>
                                <th class="text-center">Importe</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-center">F. Vencimiento</th>
                                <th class="text-center">Situación</th>
                                <th class="text-center">Registrado Por</th>
                                <!-- <th class="text-center">Asignado A</th> -->
                                <th class="text-center">Fecha de Registro</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!cuentas.length">
                                  <td class="text-left text-danger" colspan="14"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in cuentas" :key="p.id" :class="(p.eliminado=='S'?'table-danger':'')">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center">
                                      <span class="badge badge-pill badge-primary" v-text="p.tipocuenta == '2'? 'Cuenta por Pagar': 'Cuenta por Cobrar'">
                                      </span>
                                    </td>
                                    <td class="text-center" v-text="p.documento + ' - '+p.cliente"></td>
                                    <td class="text-center">
                                      <span class="badge badge-pill badge-info" v-text="p.tipodocumento == 'B'? 'Boleta': 
                                          p.tipodocumento == 'F'?'Factura':
                                          p.tipodocumento == 'RXH'?'RRxHH': 
                                          p.tipodocumento == 'NC'?'Nota de Crédito': 
                                          p.tipodocumento == 'ND'?'Nota de Débito': 'Otro'">
                                      </span>
                                    </td>
                                    <td class="text-center"> <strong v-text="p.serie + '-'+p.numero "></strong></td>
                                    <td class="text-center" v-text="p.importeR"></td>
                                    <td class="text-center"><span class="badge badge-pill badge-warning" v-text="p.moneda == 'PEN'?'Soles': 'Dólares'"></span></td>
                                   
                                    <td class="text-center" v-text="p.fechaVenc"></td>
                              
                                    <!-- <td class="text-center"><mark v-text="(p.situacion=='A'?'A':'Compra')"></mark></td> -->
                                    <td class="text-center"><span :class="'badge badge-pill ' + (p.estado == 'P'?'badge-danger':'badge-success')"  v-text="(p.estado=='P'?'Pendiente':'Cancelado')"></span></td>
                                    <td class="text-center" v-text="p.trabajador"></td>
                                    <!-- <td class="text-center" v-text="p.asignadoA"></td> -->
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <!-- <button @click="generarDetalles(p.id,p.comprobante, p.moneda)" title="Ver Detalles" class="btn btn-sm btn-warning">
                                          <i class="mdi mdi-archive-check-outline icon-size"></i>
                                        </button> -->

                                        <!-- <button @click="verPdf(p.idDocumentoSUNAT)" title="Ver Documento" class="btn btn-sm btn-info">
                                          <i class="mdi mdi-file-pdf-box icon-size"></i>
                                        </button>
                                     -->
                                        <button v-if="p.eliminado == 'N'" @click="eliminar(p.id,`${p.serie}-${p.numero}`, `${p.documento}-${p.cliente}`)" title="Eliminar Cuenta" class="btn btn-sm btn-danger">
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarCuenta('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarCuenta(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarCuenta('next'):'')">
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

    <!-- <Detalles ref="detalles" :idventa="this.idVenta" :documento="this.documento" :moneda="this.moneda"></Detalles> -->
    <!-- <NotaCredito ref="notacredito" :idventa="this.idVenta" :documento="this.documento" :proveedor="this.proveedor"></NotaCredito> -->
   
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
// import Detalles from './Detalles'
import Loader from '../Loader'
// import NotaCredito from './NotaCredito'

export default {
  name: 'CuentaXCobrar',
  mixins: [misMixins],
  components: {
    // Detalles,
    Loader
    // NotaCredito
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      cuentas: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idVenta: '',
      documento:'',
      proveedor: '',
      tiposcuenta: [
        { id: '1', nombre: 'Cuenta por Cobrar' },
        { id: '2', nombre: 'Cuenta por Pagar' }
      ],
      operaciones: [
        { id: "C", nombre: "Contado" },
        { id: "D", nombre: "Crédito" },
      ],
      monedas: [
        { id: "PEN", nombre: "Soles" },
        { id: "USD", nombre: "Dólares" },
      ],
      tipoOperacion: [
        { id:'C', nombre: 'Compra' },
        { id: 'V', nombre:'Venta' }
      ],
      tipos: [
        {id:'B', nombre: 'Boleta'},
        {id: 'F', nombre:'Factura'}
      ],
      situaciones: [
        {id: 'P', nombre: 'Pendiente'},
        {id: 'C', nombre: 'Cancelado'}
      ],
      entidad: 'cuenta_x_cobrar_pagar',
      bandRender: false,
      moneda: '',
    }
  },
  computed: {
  },
  methods: {
    buscarCuenta: function (attr) {
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
      me.buscarCuentas()
    },
    async buscarCuentas() {
      let me = this
      let tipocuenta = document.getElementById('tipo_cuenta_'+me.entidad).value
      let documento = document.getElementById('documento_'+me.entidad).value
      let razonSocial = document.getElementById('razonSocial_'+me.entidad).value
      let registradoPor = document.getElementById('registrado_por_'+me.entidad).value
      let serie = document.getElementById('serie_'+me.entidad).value
      let numero = document.getElementById('numero_'+me.entidad).value
      let moneda = document.getElementById('moneda_'+me.entidad).value
     

      let situacion = document.getElementById('situacion_'+me.entidad).value
      
      // let comprobante = document.getElementById('comprobante_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      let fechaV = document.getElementById('fecha_venc_' + me.entidad).value
      let operacion = document.getElementById('operacion_'+me.entidad).value
      // let tipoDoc = document.getElementById('tipoDocumento_'+me.entidad).value
      
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/cuentaxcyp/getAll',
        data: {
          'tipocuenta': tipocuenta,
          'documento': documento,
          'razonSocial': razonSocial,
          'registradoPor': registradoPor,
          'serie': serie,
          'numero': numero,
          'moneda': moneda,
          'fechaV': fechaV,
          'fechaI': fechaI,
          'fechaF': fechaF,
          'situacion': situacion,
          'operacion': operacion,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.cuentas = response.data.cuentas
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
      me.buscarCuentas()
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
      // window.open("https://fasteinvoice.com/consultar.php?ruc=20603144954&password=valentinasunat123&id="+id,"_blank")
      // window.setTimeout(function () {
      //   miVentana.focus() 
      //   miVentana.print() 
      // }, 1000)
    },
    eliminar (id,documento,proveedor) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar Cuenta con comprobante <strong>' + documento + 
        '</strong> de: <strong>'+proveedor+'</strong>?',
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
            url: `/cuentaxcyp/eliminar/${id}`,
            data: {
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Cuenta Eliminada con Éxito.',
                type: 'success'
              }).then(function () {
                me.buscarCuentas()
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
      // document.getElementById('fecha_venc_'+me.entidad).value = me.obtenerFechaActual()
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