<template>
  <div class="content-wrapper" :id="'sec-permiso_'+entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Tipos de Usuarios
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Tipo de Usuario">
          <router-link tag="a" to="/permisos/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Tipo de Usuario</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Tipos de Usuario</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard p-t-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group row">
                                          <label :for="'nombre_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Nombre:</label>
                                          <div class="col-sm-3">
                                            <input type="text" name="nombre" class="form-control form-control-sm" :id="'nombre_'+entidad" 
                                              @keyup.enter="busquedaPermiso" />
                                          </div>                                   
                                       
                                          <label :for="'descripcion_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0 text-md-right">Descripción:</label>
                                          <div class="col-sm-5">
                                            <input type="text" name="descripcion" class="form-control form-control-sm" :id="'descripcion_'+entidad" 
                                              @keyup.enter="busquedaPermiso" />
                                          </div>                                   
                                       
                                        </div>  
                                      </div>
                                    </div>
                                </div>
                                <div class="col-md-2 pt-xs-1 pl-xs-2">
                                    <div class="content-buttons btn-group">
                                    <button type="button" class="btn btn-sm btn-icon btn-info mb-1" title="Buscar" @click="busquedaPermiso"><i class="mdi mdi-magnify"></i></button>

                                    <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>

                                    <button type="button" class="btn btn-xs btn-icon btn-success mb-1" title="Exportar a Excel"><i class="mdi mdi-file-excel "></i></button>-->
                                    </div>

                                    <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                        <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaPermiso">
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
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Fecha de Creación</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!permisos.length">
                                  <td class="text-left text-danger" colspan="5"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in permisos" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.nombre"></td>
                                    <td class="text-center" v-text="p.descripcion==null?'-':p.descripcion"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="generarModal(p.id, p.nombre)" title="Gestión de Permisos" 
                                        class="btn btn-sm btn-info">
                                          <i class="mdi mdi-folder-account-outline icon-size"></i>
                                        </button>
                                        <router-link
                                          tag="a" title="Editar Tipo de Usuario"
                                          v-bind:to="{name: 'editarpermiso', params:{id:p.id}}"
                                          class="btn btn-sm btn-success"
                                        >
                                          <i class="mdi mdi-pencil icon-size"></i>
                                        </router-link>
                                        <button @click="eliminar(p.id,p.nombre)" title="Eliminar Tipo de Usuario" class="btn btn-sm btn-danger">
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarPermiso('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarPermiso(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
                            
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarPermiso('next'):'')">
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
    <AdminPermiso ref="adminpermiso" :idmodal="this.idTipo" :tipousuario="this.tipoUsuario"></AdminPermiso>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import AdminPermiso from './Permisos'
import Loader from '../Loader'

export default {
  name: 'Permiso',
  mixins: [misMixins],
  components: {
    AdminPermiso,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      permisos: [],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idTipo: '',
      tipoUsuario: '',
      entidad: 'persmiso'
    }
  },
  computed: {
  },
  methods: {
    buscarPermiso: function (attr) {
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
      me.busquedaPermiso()
    },
    generarModal(id, nombre) {
      let me = this
      me.isValidSession()
   
      me.idTipo = id
      me.tipoUsuario = nombre
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
       // this.$emit('abrirmodal',me.idModal)
      me.$refs.adminpermiso.showModal(id)

      // me.mostrarModal = true
      // alert(me.idModal)
      // if (this.$store.state.mostrarModal) {
      // }
      // generarModal(id)
    },
    async busquedaPermiso() {
      let me = this
      let nombre      = document.getElementById('nombre_'+me.entidad).value
      let descripcion = document.getElementById('descripcion_'+me.entidad).value
     
     
      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/permiso',
        data: {
          'nombre': nombre,
          'descripcion': descripcion,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })

      me.permisos = response.data.permisos
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
      me.busquedaPermiso()
    },
    async eliminar(id, nombre) {
      let me = this
      let token = this.$store.state.token
      swal({
        title: 'Confirmar Operación',
        html: '¿Seguro que desea Eliminar el Tipo de Usuario <strong>' + nombre + '</strong> ?',
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
            url: '/eliminarpermisousuario',
            data: {
              'id': id,
              '_token': token
            }
          })
          
          if (response.data.estado === true) {
            setTimeout(function () {
              swal({
                title: '¡Eliminado!',
                text: 'Tipo de Usuario Eliminado con Éxito.',
                type: 'success'
              }).then(function () {
                me.busquedaPermiso()
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
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    me.isValidSession()
   
    // this.$store.state.mostrarModal = false 
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    // window.setTimeout( function () {
    me.cargarDatos()
    // }, 1000)
    
    //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    
    // me.generarModal(0,'')
  },
  beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec-permiso_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    // this.$store.state.mostrarModal = false 
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
  
    // this.$store.state.mostrarModal = false 
      // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  }
}
</script>