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
              <li class="breadcrumb-item active">Evaluación de Trabajo
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body"><!-- Default styling start -->
      <div class="row justify-content-center" id="default">
          <div class="col-md-12 col-xs-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Trabajos</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group row">
                                    <label :for="'documento_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">DNI/RUC:</label>
                                    <div class="col-sm-2">
                                      <input type="text" name="documento" class="form-control form-control-sm" :id="'documento_'+entidad" 
                                        @keyup.enter="busqueda" @keypress="soloNumeros($event)" maxlength="11" />
                                    </div>

                                    <label :for="'cliente_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">Cliente:</label>
                                    <div class="col-sm-4">
                                      <input type="text" name="cliente" class="form-control form-control-sm" :id="'cliente_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>

                                    <label :for="'placa_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Placa V.:</label>
                                    <div class="col-sm-2">
                                      <input type="text" name="placa" class="form-control form-control-sm text-center" :id="'placa_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label :for="'servicio_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Servicio:</label>
                                    <div class="col-sm-3">
                                      <input type="text" name="servicio" class="form-control form-control-sm" :id="'servicio_'+entidad" 
                                        @keyup.enter="busqueda" @keypress="soloLetras($event)" maxlength="11" />
                                    </div>

                                    <label :for="'fecha_i_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Inicio:</label>
                                    <div class="col-sm-2">
                                      <input type="date" name="fecha_i" class="form-control form-control-sm pr-0" :id="'fecha_i_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>

                                    <label :for="'fecha_f_'+entidad" class="col-sm-1 m-4px col-form-label text-md-center pr-0 mr-0 pt-0">F. Fin:</label>
                                    <div class="col-sm-2">
                                      <input type="date" name="fecha_f" class="form-control form-control-sm pr-0" :id="'fecha_f_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>
                                  </div>

                                </div>
                              </div>
                            </div>
                            <div class="col-md-2 pt-xs-1 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-info mb-1" title="Buscar" @click="busqueda">
                                    <i class="mdi mdi-magnify icon-size"></i>
                                  </button>

                                  <button type="button" class="btn btn-sm btn-success mb-1" @click="excel" title="Exportar a Excel">
                                    <i class="mdi mdi-file-excel icon-size"></i></button>
                                </div>

                                <div class="form-group row">
                                  <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                    <select class="custom-select custom-select-sm ml-1 pr-2" 
                                      :id="'cantidad_'+entidad" title="Registros por Página" @change="busqueda">
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
                      <span class="text-info pl-2 ml-1 col-form-label mb-2" :id="'loading_'+entidad">Cargando...</span>
                      <div class="table-responsive px-1 d-none" :id="'tabla_'+entidad">
                          <table class="table table-hover table-sm mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Servicio</th>
                                <th class="text-center">Placa del Vehículo</th>
                                <th class="text-center">Tiempo Estimado</th>
                                <th class="text-center">Asignado a</th>
                                <th class="text-center">Asignado por</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Fecha Registro</th>
                                <th class="text-center">Situación</th>
                                <th class="text-center">Operaciones</th>                             
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!trabajos.length">
                                  <td class="text-left text-danger" colspan="9"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in trabajos" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))"></td>
                                    <td class="text-center"><pre><strong v-text="p.documento+' - '+p.cliente"></strong></pre></td>
                                    <td class="text-center"><pre><mark v-text="p.servicio"></mark></pre></td>
                                    <td class="text-center" v-text="p.placaAuto"></td>
                                    <td class="text-center" v-text="p.tiempo+' min.'"></td>
                                    <td class="text-center"><pre><mark v-text="p.asignado"></mark></pre></td>
                                    <td class="text-center" v-text="p.asigna"></td>
                                    <td class="text-center" v-text="p.fecha"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center"><span :class="'badge rounded-pill '+(p.enProceso==1?'bg-warning':(p.enProceso == 0?'bg-danger':'bg-success'))" 
                                    v-text="(p.situacion == 'P' && p.enProceso == 1?'En Proceso':(p.situacion == 'P' && p.enProceso == 0?'Sin Iniciar':(p.situacion == 'E' && p.enProceso == 1?'En Revisión':'Revisado')))"></span></td>
                                 
                                    <td class="text-center">
                                      <div class="content-buttons btn-group" v-if="(p.enProceso != 2 && p.situacion != 'R')">
                                        <button @click="generarModal(p.id, p.servicio, p.placaAuto)" title="Adjuntos & Observaciones" 
                                        class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-file-cloud icon-size"></i>
                                        </button>

                                        <button @click="generarModal02(p.id, p.servicio, p.placaAuto)" 
                                        title="Adjuntos & Observaciones" class="btn btn-sm btn-info">
                                          <i class="mdi mdi-file-clock-outline icon-size"></i>
                                        </button>
                                      </div>

                                      <div v-if="(p.enProceso == 2 && p.situacion == 'R')">
                                        -
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

                              <li v-for="op in opciones" :key="op.opc" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscar(op.opc):'')" >
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
    <ModalFotos ref="modalfotos"></ModalFotos>
    <VerTrabajo ref="vertrabajo"></VerTrabajo>
   
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import ModalFotos from "./GaleriaImagenes"
import VerTrabajo from "./VerTrabajo"

export default {
  name: 'EvaluacionTrabajo',
  mixins: [misMixins],
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      trabajos: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      token: this.$store.state.token,
      estadoListado: false,
      entidad: 'evaluacion_trabajo',
      bandRender: false
    }
  },
  components: {
    ModalFotos, 
    VerTrabajo
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

      let documento = document.getElementById('documento_'+me.entidad).value
      let cliente   = document.getElementById('cliente_'+me.entidad).value
      let placa     = document.getElementById('placa_'+me.entidad).value
      let servicio  = document.getElementById('servicio_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value

      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
       
      let response = await axios({
        method: 'post',
        url: '/trabajos',
        data: {
          'documento': documento,
          'cliente': cliente,
          'placa': placa,
          'servicio': servicio,
          'fechaI': fechaI,
          'fechaF': fechaF,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': me.token,
          'tipo': '1'
        }
      })
      
      me.trabajos = response.data.trabajos
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
     
      me.renderTabla(me.entidad)

      // var el2 = document.getElementById('tabla-evaluacion')
      // el2.classList.remove('d-none')
      // alert(filtro)
    },
    generarModal(id, servicio, placa) {
      let me = this
      me.isValidSession()
      //   me.idfoto = id
      //   console.log(me.idfoto)
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.modalfotos.showModal(id, servicio, placa)
    },
    generarModal02 (id, servicio, placa) {
      let me = this
      me.isValidSession()
    
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.vertrabajo.showModal(id, servicio, placa)
    },
    excel () {
      let me = this
      let documento = document.getElementById('documento_'+me.entidad).value
      let cliente   = document.getElementById('cliente_'+me.entidad).value
      let placa     = document.getElementById('placa_'+me.entidad).value
      let servicio  = document.getElementById('servicio_'+me.entidad).value
      let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      let fechaF = document.getElementById('fecha_f_'+me.entidad).value

      // let filtro = document.getElementById('filtro-evaluacion').value
      // let descripcion = document.getElementById('descripcion-evaluacion').value
      // let fechaI = document.getElementById('fecha_i_'+me.entidad).value
      // let fechaF = document.getElementById('fechaFinal-evaluacion').value

      // if (filtro =='') {filtro = 'null'}
      // if (descripcion =='') {descripcion = 'null'}
      // if (fechaI =='') {fechaI = 'null'}
      // if (fechaF =='') {fechaF = 'null'}

      window.open(`/evaluacion/excel?documento=${documento}&cliente=${cliente}&placa=${placa}&servicio=${servicio}&fechaI=${fechaI}&fechaF=${fechaF}`,'_blank')
    },
    async cargarDatos () {
      let me = this
      me.busqueda()
    },
    async eliminar(id, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar el Auto Tipo <strong>' + nombre + '</strong> ?',
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
            url: '/eliminarauto',
            data: {
              'id': id,
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Auto Eliminado con Éxito.',
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
    this.$on('buscarEvaluacion', function () {
      this.cargarDatos()
    })
    
    // let me = this
    // me.cargarDatos()
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
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false 
  },
  beforeDestroy: function () {
    var element = document.getElementById('sec-evaluacion')
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
  }
}
</script>
<style  scoped>
    pre {
        overflow: hidden;
        font-size:1rem;
    }
</style>