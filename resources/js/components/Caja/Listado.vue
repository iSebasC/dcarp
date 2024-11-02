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
              <li class="breadcrumb-item active">Arqueos de Caja Cerradas
              </li>
            </ol>
          </div>
          <h4 class="content-header-title mb-0 mt-1 ml-2">Listado de Arqueos de Caja Cerradas</h4>
        
        </div>
      </div>
      <!-- <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Cotización">
          <router-link tag="a" to="/cotizacion/crear" class="btn btn-info btn-sm round dropdown-menu-right box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Cotización</router-link>
        </div>
      </div> -->
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <!-- <div class="card-header">
                      <h4 class="card-title">Listado de Arqueos de Caja Cerradas</h4>
                  </div> -->
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row mt-4 pt-1">
                                <div class="col-md-10">
                                  <div class="row">
                                    <div class="col-md-12">
                                      <div class="form-group row">
                                        <label :for="'cliente_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Usuario:</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="usuario" class="form-control form-control-sm" :id="'usuario_'+entidad" 
                                            @keyup.enter="busquedaArqueo" />
                                        </div>
                                        
                                        <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                        <div class="col-sm-2">
                                          <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                            @keyup.enter="busquedaArqueo" />
                                        </div>

                                        <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                        <div class="col-sm-2">
                                            <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                            @keyup.enter="busquedaArqueo" />
                                        </div>
                                      </div>
                                
                                    </div>
                                  </div>
                             
                                  <!-- <div class="col-md-3 pl-2">
                                            <select class="custom-select input-sm" id="filtro-cotizacion" @change="busquedaArqueo">
                                                <option value="" disabled="" selected="">Buscar por</option>
                                                <option value="todo">Todo</option>
                                                <option value="ruc">DNI/RUC</option>
                                                <option value="cliente">Razón Social</option>
                                                <option value="documento">Número</option>
                                                <option value="placa">Placa del Vehículo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 pl-2 pt-xs-2">
                                            <input type="text" id="descripcion-cotizacion" class="form-control input-md" placeholder="Descripción..." @keyup.enter="busquedaArqueo">
                                        </div>

                                        <div class="col-md-3 pl-2 pt-xs-2">
                                            <input type="date" id="fechaInicial-cotizacion" class="form-control input-md" @keyup.enter="busquedaArqueo">
                                        </div>

                                        <div class="col-md-3 pl-2 pt-xs-2">
                                            <input type="date" id="fechaFinal-cotizacion" class="form-control input-md" @keyup.enter="busquedaArqueo">
                                        </div>
                                    </div> -->
                                </div>
                                <div class="col-md-2 pt-xs-1 pl-xs-2">
                                  <div class="content-buttons btn-group">
                                    <button type="button" class="btn btn-sm btn-info mb-1" title="Buscar" @click="busquedaArqueo">
                                      <i class="mdi mdi-magnify"></i></button>
                                    
                                    <!-- <button type="button" @click="excel" class="btn btn-xs btn-icon btn-sm btn-success mb-1" title="Exportar a Excel">
                                      <i class="mdi mdi-file-excel icon-size"></i></button> -->
                                  </div>

                                  <div class="fo1rm-group row">
                                    <label class="col-md-5 label-control pt-1 col-form-label pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                      <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaArqueo">
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
                                <th class="text-center">Monto Apertura</th>
                                <th class="text-center">Monto Cierre (S/)</th>
                                <th class="text-center">Monto Cierre ($)</th>
                                <th class="text-center">Fecha Cierre</th>
                                <th class="text-center">Fecha del Sistema</th>
                                <th class="text-center">Usuario</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!ventas.length">
                                  <td class="text-left text-danger" colspan="7"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>
 
                                <tr v-for="(p, index) in ventas" :key="p.id" :class="p.situacion">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.fechaApertura"></td>
                                    <td class="text-right"><strong v-text="p.apertura"></strong></td>
                                    <td class="text-right"><strong v-text="p.cierre"></strong></td>
                                    <td class="text-right"><strong v-text="p.cierreDolar"></strong></td>
                                    <td class="text-center"><strong class="text-info" v-text="p.fechaCierre"></strong></td>
                                    <td class="text-center" v-text="p.fechaSistema"></td> 
                                    <td class="text-center"> <span class="badge badge-pill badge-info" v-text="p.usuario"></span></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="verPdf(p.id)" title="Exportar a PDF" 
                                        class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-file-pdf-box icon-size"></i>
                                        </button>

                                        <button @click="verExcel(p.id)" title="Exportar a Excel" class="btn btn-sm btn-success">
                                          <i class="mdi mdi-file-excel icon-size"></i>
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

    <!-- <Detalles ref="detalles" :idventa="this.idVenta" :documento="this.documento"></Detalles> -->
  </div> 
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Loader from "../Loader"
// import Detalles from './Detalles'

export default {
  name: 'ReporteCaja',
  mixins: [misMixins],
  components: {
    Loader
    // Detalles
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      ventas: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idVenta: '',
      documento:'',
      entidad: 'repcaja',
      bandRender: false
    }
  },
  computed: {
  },
  methods: {
    verExcel (id) {
      window.open(`/caja/excel/${id}`);
    },
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
      me.busquedaArqueo()
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
    async busquedaArqueo() {
      let me = this
      let cliente     = document.getElementById('usuario_'+me.entidad).value
      let fecha_i       = document.getElementById('fecha_i_'+me.entidad).value
      let fecha_f       = document.getElementById('fecha_f_'+me.entidad).value
     
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    // setTimeout(async() => {
      let response = await axios({
        method: 'post',
        url: '/reparqueo',
        data: {
          'usuario': cliente,
          'fechai': fecha_i,
          'fechaf': fecha_f,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.ventas = response.data.arqueos
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
    // }, 1000);
    
      me.renderTabla(me.entidad)
      
      // var el2 = document.getElementById('tabla-cotizacion')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      me.busquedaArqueo()
    },
    excel () {
      let me = this

      let cliente     = document.getElementById('usuario_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value
      

      window.open(`/venta/excel?cliente=${cliente}&fechai=${fechaI}&fechaf=${fechaF}`,'_blank')
  
    },
    generarDetalles(idventa, documento) {
      let me = this
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
      if (id != null) {
        window.open(`/caja/pdf/${id}`)
      }
    },
  },
  created() {
    
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    this.$route.meta.auth = localStorage.getItem('autenticado')
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
<style scoped>

.V{
  background-color: #f2f2f2;
}
.A{
  background-color: #f5c6cb;
}
.N{
  background-color: #ffeeba;
}
.U{
  background-color: #c3e6cb;
}
</style>

