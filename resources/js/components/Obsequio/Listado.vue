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
              <li class="breadcrumb-item active">Adicionales & Obsequios
              </li>
            </ol>
          </div>
        </div>
      </div>

      <div class="content-header-right col-md-6 col-12">
        <div
          class="btn-group float-md-right"
          role="group"
          aria-label="Agregar Adicional/Obsequio"
        >
          <button class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
            type="button" @click="gestion"><i class="mdi mdi-plus icon-size"></i> Agregar Adicional/Obsequio</button>
     
        </div>
      </div>
    </div>
    <div class="content-body"> 
        <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Adicionales & Obsequios</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard p-t-1 pb-0">
                        <fieldset class="form-group">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group row">
                                    <label :for="'tipo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Tipo:</label>
                                    <div class="col-sm-2">
                                      <select class="custom-select custom-select-sm" :id="'tipo_'+entidad" @change="busquedaObsequio">
                                        <option value="todo">Todos</option>
                                        <option v-for="a in tipos" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                      </select>
                                    </div>


                                    <label :for="'nombre_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Nombre:</label>
                                    <div class="col-sm-4">
                                      <input type="text" name="nombre" class="form-control form-control-sm" :id="'nombre_'+entidad" 
                                        @keyup.enter="busquedaObsequio" maxlength="255" />
                                    </div>
                        
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-md-2 pt-xs-1 pl-xs-2">
                              <div class="content-buttons btn-group">
                                <button type="button" class="btn btn-sm btn-icon btn-md btn-info mb-1" title="Buscar" 
                                @click="busquedaObsequio">
                                  <i class="mdi mdi-magnify icon-size"></i>
                                </button>
                              </div>

                              <div class="form-group row">
                                <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" 
                                  style="margin-top:3px;">Mostrar: </label>
                                <div class="col-md-5 pt-1 pl-0">
                                  <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" 
                                  title="Registros por Página" @change="busquedaObsequio">
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
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!obsequios.length">
                                  <td class="text-left text-danger" colspan="4"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in obsequios" :key="p.id" :class="p.eliminado=='S'?'table-danger':''">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center">
                                      <span
                                        :class="'badge badge-pill ' + (p.tipo == 'O'?'badge-info': 'badge-primary')"
                                        v-text="p.tipo == 'O'?'Obsequio':'Adicional'"
                                      ></span>
                                    </td>
                                    <td class="text-center" v-text="p.nombre"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <button @click="editar(p.id)" 
                                        title="Editar" class="btn btn-sm btn-success">
                                          <i class="mdi mdi-pencil icon-size"></i>
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarObsequio('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarObsequio(op.opc):'')" >
                                  <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
             
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarObsequio('next'):'')">
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

    <GestionObsequio ref="gestion"></GestionObsequio>

  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import GestionObsequio from './GestionObsequio'
import Loader from '../Loader'

export default {
  name: 'Obsequio',
  mixins: [misMixins],
  components: {
    GestionObsequio,
    Loader
},
  data() {
    return {
      filas: 10,
      pageActual: 1,
      opciones: [],
      obsequios: [],
      tipos: [
        { id: 'A', nombre: 'Adicional' },
        { id: 'O', nombre: 'Obsequio' }
      ],
      total: '',
      inicio: '',
      fin: '',
      paramInicio:'',
      paramFin: '',
      idGanancia: '',
      porcentaje:'',
      codigo: '',
      entidad: 'obsequio'
    }
  },
  computed: {
  },
  methods: {
    buscarObsequio: function (attr) {
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
      me.busquedaObsequio()
    },
    // generarModal(id, nombre) {
    //   let me = this
    //   me.idTipo = id
    //   me.tipoUsuario = nombre
    // //   this.$store.state.mostrarModal = true //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    // //   alert(me.idModal+'-'+me.tipoUsuario+'-'+this.$store.state.mostrarModal)
    //    // this.$emit('abrirmodal',me.idModal)
    //   me.$refs.obsequios.showModal(id)

    //   // me.mostrarModal = true
    //   // alert(me.idModal)
    //   // if (this.$store.state.mostrarModal) {
    //   // }
    //   // generarModal(id)
    // },
    async busquedaObsequio() {
      let me = this
      let nombre = document.getElementById('nombre_'+me.entidad).value
      let tipo = document.getElementById('tipo_'+me.entidad).value
     
      var el = document.getElementById('loading_'+me.entidad)
      el.classList.add('d-none')

      me.filas = document.getElementById('cantidad_'+me.entidad).value
    
      let response = await axios({
        method: 'post',
        url: '/obsequioall',
        data: {
          'nombre': nombre,
          'tipo': tipo,
          'filas': me.filas,
          'page': me.pageActual,
          '_token': this.$store.state.token
        }
      })
      
      me.obsequios = response.data.obsequios
      me.total = response.data.cantidad
      me.pageActual = response.data.page
      me.opciones = response.data.paginador
      me.inicio = response.data.inicio
      me.fin = response.data.fin
      me.paramInicio = response.data.paramInicio
      me.paramFin = response.data.paramFin
      
      me.renderTabla(me.entidad)
      // var el2 = document.getElementById('tabla-ganancia')
      // el2.classList.remove('d-none')

    
      // alert(filtro)
    }, 
    async cargarDatos () {
      let me = this
      me.busquedaObsequio()
    },
    editar(id) {
      let me = this
      me.isValidSession()
    
      // me.idGanancia = idganancia
      // me.codigo     = codigo
      // me.porcentaje = porcentaje
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.gestion.showModal(id);
      // me.$refs.gestion.showModal(idganancia,codigo, porcentaje)
    },
    gestion () {
      let me = this
      me.isValidSession()
   
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.gestion.showModal(0)
   
    },
  
  },
  created() {
    this.$on('buscarAdicional', function () {
      this.busquedaObsequio()
    })
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
    this.$route.meta.auth = localStorage.getItem('autenticado')
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