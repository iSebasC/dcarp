<template>
  <div class="content-wrapper" id="sec-perfil">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Mi Perfil
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div> 
       <div class="content-body"> <!-- User Feed -->
            <section id="user-feed"> <!-- Left Side Content -->
                <div class="row">
                    <div class="col-12">
                        <img src="/images/portada.gif" class="img-fluid rounded" alt="timeline image">
                        <div class="user-data text-center bg-white rounded pb-2 mb-md-2">
                            <img src="/images/portrait/large/perfil_img.png" class="img-fluid rounded-circle width-150 profile-image shadow-lg border border-3"
                                alt="timeline image">
                            <h4 class="mt-1 mb-0" v-text="perfil.nombres"></h4>
                            <p>Cliente</p>
                        </div>
                        <nav class="navbar navbar-expand-lg nav-margin">
                            <div class="collapse navbar-collapse" id="navbarText">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item px-2 active">
                                        <a class="nav-link" href="javascript:void(0);">Inicio</a>
                                    </li>
                                    <li class="nav-item px-2">
                                        <a class="nav-link" href="javascript:void(0);">Seguimiento</a>
                                    </li>
                                </ul>
                                <div class="navbar-text">
                                    <ul class="navbar-nav">
                                        <li class="nav-item px-2">
                                            <a class="nav-link" href="javascript:void(0);">Multimedia</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
                <!-- Profile Intro -->
                <div class="row">
                    <!-- <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <h5 class="card-title">Mi Perfil</h5>
                                <hr>
                                <div class="about-me mt-2">
                                    <div class="font-small-3">
                                        <h6><strong>Documento: </strong> <span v-text="perfil.documento"></span></h6>
                                        <h6><strong>Correo Electrónico: </strong><span  v-text="perfil.correo"></span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Feed Section -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <!-- Write Post -->
                        <div class="card shadow-none">
                            <div class="catd-body">
                                <div class="row mt-2 justify-content-center">
                                    <div class="col-md-5 col-xs-12">
                                        <div class="form-group">
                                            <label for="fecha">Fecha <span class="text-danger">*</span></label>
                                            <input type="date" id="fecha" class="form-control text-center" name="fecha" @keyup.enter="cargarTrabajos()">
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="placa">Placa del Vehículo <span class="text-danger">*</span></label>
                                            <input type="text" id="placa" @keypress="soloLetrasNumeros($event)" class="form-control" name="placa" placeholder="Buscar Placa..." @keyup.enter="cargarTrabajos()">
                                        </div>
                                    </div>
                            
                                </div>
                                <div class="write-post">
                                    <hr class="m-0">
                                    <div class="row px-1">
                                        <div class="col-12 pt-1">
                                            <button type="button" class="btn btn-primary btn-min-width btn-glow mr-1 mb-1 pull-right" @click="cargarTrabajos()">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Post 1 -->
                        <div v-for="item in trabajos" :key="item.id" class="card shadow-none">
                            <div class="catd-body">
                                <div class="row p-2">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-lg-4 col-3">
                                                <img src="/images/portrait/small/avatar-s-8.png" alt="" class="img-fluid rounded-circle width-50">
                                            </div>
                                            <div class="col-lg-8 col-7 p-0">
                                                <h5 class="m-0" v-text="item.cargo"></h5>
                                                <p v-if="item.fecha != null" v-text="'Asignado el '+item.fecha"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <i class="ft-more-vertical pull-right"></i>
                                    </div>
                                </div>
                                <div class="write-post">
                                    <div class="col-sm-12 px-2">
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Documento: </strong><span v-text="item.documento"></span>
                                            </div>
                                        </div>
                                    
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Cliente: </strong><span v-text="item.cliente"></span>
                                            </div>
                                        </div>
                                    
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Servicio: </strong><span v-text="item.servicio"></span>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Asignado Por: </strong><span v-text="item.asignado"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Placa del Vehículo: </strong><span v-text="item.placaAuto"></span>
                                            </div>
                                        </div>
 
                                        <div class="row justify-content-center">
                                            <div class="col-md-8 pb-1">
                                                <strong>Duración Estimada: </strong><span v-text="item.tiempo+ ' min.'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="m-0">
                                    <div class="row p-1">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-10 pr-0">
                                                    <button class="btn btn-danger btn-xs btn-glow" title="Adjuntos & Observaciones" @click="generarModal(item.id, item.servicio, item.placaAuto)">Fotos & Observaciones</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="pull-right">
                                                <span class="pr-1 pt-2">
                                                    <a :class="item.enProceso == 0?'text-danger':'text-success'" :title="item.enProceso == 0?'Por Iniciar':'Iniciado'" href="javascript:void(0);">
                                                        <i :class="item.enProceso == 0?'mdi mdi-play-circle':'mdi mdi-stop-circle'" style="font-size:28px;margin-top:-5px;"></i>
                                                    </a>
                                                </span>
                                                <!-- <span class="pr-1"><i class="ft-corner-up-right h4 align-middle"></i> 23</span> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Birthday Card -->
                    <!-- <div class="col-lg-3 col-md-12 col-sm-12">
                       
                        <div class="card shadow-none mt-2">
                            <div class="card-body">
                                <div class="last-photos">
                                    <h5 class="card-title mb-1">Multimedia</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-1.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-2.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-3.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-4.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-5.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-6.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-7.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-8.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                        <div class="col-4">
                                            <img src="/images/backgrounds/bg-9.jpg" alt="" class="img-fluid mb-2 rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                  
                    
                    </div> -->
                </div>
            </section> 
        </div>
        <ModalFotos ref="modalfotos"></ModalFotos>>
    </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import ModalFotos from "./GaleriaImagenes"
export default {
  name: 'Perfil',
  mixins: [misMixins],
  components: {
    ModalFotos
  },
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      trabajos: [],
      perfil: {},
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idTipo: '',
      tipoUsuario: '',
      tiempo: null,
    //   idfoto: ''
    }
  },
  methods: {
    async cargarPerfil () {
      let me = this
      let response = await axios({
        method: 'post',
        url: '/getperfil',
        data: { 
          '_token': this.$store.state.token
        }
      })

      me.perfil = response.data.perfil
    },
    async cargarTrabajos () {
        let me = this
        let fecha = document.getElementById('fecha').value
        let placa = document.getElementById('placa').value
        
        let response = await axios({
            method: 'post', 
            url: '/getperfiltrabajos',
            data: {
                '_token': this.$store.state.token,
                'fecha': fecha,
                'placa': placa
            }
        })
        me.trabajos = response.data.detalles
    },
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
    generarModal(id, servicio, placa) {
      let me = this
    //   me.idfoto = id
    //   console.log(me.idfoto)
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.modalfotos.showModal(id, servicio, placa)
    },
    async busquedaPermiso() {
      let me = this
      let filtro = document.getElementById('filtro-permiso').value
      let descripcion = document.getElementById('descripcion-permiso').value
     
      
      me.filas = document.getElementById('cantidad-permiso').value
    
      let response = await axios({
        method: 'post',
        url: '/permiso',
        data: {
          'filtro': filtro,
          'descripcion': descripcion,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
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
      
      var el = document.getElementById('loading-permiso')
      el.classList.add('d-none')
     
      var el2 = document.getElementById('tabla-permiso')
      el2.classList.remove('d-none')
    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      document.getElementById('fecha').value = this.obtenerFechaActual()
      me.cargarPerfil()
      me.cargarTrabajos()
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
    },
  },
  created() {
    // this.incremKey(0)
    // this.generarModal('')
  },
  activated: async function () {
    let me = this
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    window.setTimeout( function () {
      me.cargarDatos()
    }, 1000)
  },
  beforeDestroy: function () {
    var element = document.getElementById('sec-perfil')
    element.classList.add('d-none')
  },
  deactivated: function () {
  },
  mounted () {
    // this.contarSegundos()
  }
}
</script>
<style scoped>
.img-fluid, .rounded {
    width: 100%;
}
</style>