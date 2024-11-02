<template>
  <div class="content-wrapper" :id="'sec_'+entidad">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 m-0">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link tag="a" to="/">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Locales de Atención
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Local">
          <router-link tag="a" to="/local/crear" class="btn btn-info btn-rounded box-shadow-2 p-x-2 m-b-1 m-t-0" type="button">
            <i class="mdi mdi-plus icon-size"></i> Agregar Local 
          </router-link>
        </div>
      </div>
    </div>
    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Locales</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard p-t-1 pb-0">
                        <fieldset class="form-group">
                            <div class="row">
                              <div class="col-md-10">
                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="form-group row">
                                      <label :for="'codigo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Código:</label>
                                      <div class="col-sm-2">
                                          <input type="text" name="codigo" class="form-control form-control-sm" :id="'codigo_'+entidad" 
                                          @keyup.enter="busquedaLocal" />
                                      </div>                                   
                                  
                                      <label :for="'direccion_'+entidad" class="col-sm-2 m-4px col-form-label text-md-right pr-0 ml-md-2 mr-0 pt-0">Dirección:</label>
                                      <div class="col-sm-5">
                                          <input type="text" name="direccion" class="form-control form-control-sm" :id="'direccion_'+entidad"
                                          @keyup.enter="busquedaLocal" />
                                      </div>                                   
                                    </div>
                                  
                                    <div class="form-group row">
                                      <label :for="'tipo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">T. Local:</label>
                                      <div class="col-sm-2">
                                        <select class="custom-select custom-select-sm" :id="'tipo_'+entidad" @change="busquedaLocal">
                                          <!-- <option value="" disabled="" selected="">Tipo</option> -->
                                          <!-- custom-select input-sm -->
                                          <option value="todo">Todos</option>
                                          <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                        </select>
                                      </div>                                   
          
                                      <label :for="'departamento_'+entidad" class="col-sm-2 m-4px col-form-label text-md-right ml-md-2 pr-0 mr-0 pt-0">Departamento:</label>
                                      <div class="col-sm-3">
                                        <select class="custom-select custom-select-sm" :id="'departamento_'+entidad" @change="busquedaLocal">
                                          <!-- <option value="" disabled="" selected="">Departamento</option> -->
                                          <option value="todo">TODOS</option>
                                          <option v-for="f in departamentos" :key="f.codigo" :value="f.codigo"> {{f.nombre}}</option>
                                        </select>
                                      </div>                                   
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-2 p-t-xs-1 p-l-xs-2">
                                  <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-icon btn-info m-b-1" title="Buscar" 
                                      @click="busquedaLocal"><i class="mdi mdi-magnify"></i></button>

                                  <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>

                                  <button type="button" class="btn btn-xs btn-icon btn-success mb-1" title="Exportar a Excel"><i class="mdi mdi-file-excel "></i></button>-->
                                  </div>

                                  <div class="form-group row">
                                    <label class="col-md-5 col-form-label pt-1 pl-1 pr-0" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                    <div class="col-md-5 pt-1 pl-0">
                                        <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busquedaLocal">
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
                      <span class="text-info pl-2 ml-1 mb-2 col-form-label" :id="'loading_'+entidad">
                        <Loader />
                      </span>
                      <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad">
                          <table class="table mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Dirección</th>
                                <th class="text-center">Tipo de Local</th>
                                <th class="text-center">Departamento</th>
                                <th class="text-center">Fecha de Creación</th>
                                <th class="text-center">Operaciones</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!locales.length">
                                  <td class="text-left text-danger" colspan="7"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in locales" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))" ></td>
                                    <td class="text-center" v-text="p.codRegistro"></td>
                                    <td class="text-center" v-text="p.direccion"></td>
                                    <td class="text-center" v-text="p.tipo"></td>
                                    <td class="text-center" v-text="p.departamento"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <router-link v-if="p.tipo === 'TIENDA'"
                                          tag="a" title="Relación entre Tiendas y Almacenes"
                                          v-bind:to="{name: 'relaciontienda', params:{id:p.id}}"
                                          class="btn btn-sm btn-info"
                                        >
                                          <i class="mdi mdi-sort icon-size"></i>
                                        </router-link>

                                        <router-link
                                          tag="a" title="Editar Local"
                                          v-bind:to="{name: 'editarlocal', params:{id:p.id}}"
                                          class="btn btn-sm btn-success"
                                        >
                                          <i class="mdi mdi-pencil icon-size"></i>
                                        </router-link>

                                        <button @click="eliminar(p.id,p.direccion)" title="Eliminar Local" class="btn btn-sm btn-danger">
                                          <i class="mdi mdi-delete-outline icon-size"></i>
                                        </button>
                                      </div>
                                    </td>
                                </tr>
                            </tbody>
                          </table>

                          <nav class="p-l-2">
                            <ul class="pagination justify-content-right">
                              <li class="page-item">
                                <a class="page-link bg-info text-white" href="javascript:void(0);" aria-label="total"><strong>TOTAL: </strong><span v-text="total"></span></a>
                              </li>

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscarLocal('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscarLocal(op.opc):'')" >
                                <a class="page-link" href="javascript:void(0);" v-text="op.opc" :disabled="op.bloqueado"></a>
                              </li>
             
                              <li class="page-item" @click="((paramFin > pageActual && paramFin >0)?buscarLocal('next'):'')">
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
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Loader from '../Loader'
export default {
    name: 'Local',
    mixins: [misMixins],
    data() {
        return {
            filas: 10,
            pageActual: 1,
            opciones: [],
            locales: [],
            departamentos: [],
            tipos: [{ 'id': 'A', 'nombre': 'Almacén' }, { 'id': 'T', 'nombre': 'Tienda' }],
            total: '',
            inicio: '',
            fin: '',
            paramInicio: '',
            paramFin: '',
            token: localStorage.getItem('token'),
            idModal: '',
            entidad: 'local'
        };
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
        buscarLocal: function (attr) {
            let me = this;
            if (attr == 'next') {
                me.pageActual = me.pageActual + 1;
            }
            else {
                if (attr == 'prev') {
                    me.pageActual = me.pageActual - 1;
                }
                else {
                    me.pageActual = attr;
                }
            }
            me.busquedaLocal();
        },
        async busquedaLocal() {
            let me = this;
            let codigo = document.getElementById('codigo_' + me.entidad).value;
            let direccion = document.getElementById('direccion_' + me.entidad).value;
            let tipoId = document.getElementById('tipo_' + me.entidad).value;
            let departamentoId = document.getElementById('departamento_' + me.entidad).value;
            var el = document.getElementById('loading_' + me.entidad);
            el.classList.add('d-none');
            me.filas = document.getElementById('cantidad_' + me.entidad).value;
            let response = await axios({
                method: 'post',
                url: '/local',
                data: {
                    'codigo': codigo,
                    'direccion': direccion,
                    'tipoId': tipoId,
                    'departamentoId': departamentoId,
                    'filas': me.filas,
                    'page': me.pageActual,
                    '_token': me.token
                }
            });
            me.locales = response.data.locales;
            me.total = response.data.cantidad;
            me.pageActual = response.data.page;
            me.opciones = response.data.paginador;
            me.inicio = response.data.inicio;
            me.fin = response.data.fin;
            me.paramInicio = response.data.paramInicio;
            me.paramFin = response.data.paramFin;
            me.renderTabla(me.entidad);
            // var el2 = document.getElementById('tabla_'+me.entidad)
            // el2.classList.remove('d-none')
            // alert(filtro)
        },
        async cargarDatos() {
            let me = this;
            let departamentos = await axios.get('/departamentos');
            //   let tipos = await axios.get('/tipos')
            me.departamentos = departamentos.data.departamentos;
            //   me.tipos = tipos.data.tipos
            me.busquedaLocal();
        },
        async eliminar(id, nombre) {
            let me = this;
            let token = this.$store.state.token;
            swal({
                title: 'Confirmar Operación',
                html: '¿Seguro que desea Eliminar el Local ubicado en <strong>' + nombre + '</strong> ?',
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
                        url: '/eliminarlocal',
                        data: {
                            'id': id,
                            '_token': token
                        }
                    });
                    if (response.data.estado === true) {
                        setTimeout(function () {
                            swal({
                                title: '¡Eliminado!',
                                text: 'Local Eliminado con Éxito.',
                                type: 'success'
                            }).then(function () {
                                me.busquedaLocal();
                            });
                        }, 500);
                    }
                    else {
                        swal('¡Cancelado!', 'Operación Cancelada', 'error');
                    }
                }
                // else {
                //     swal('¡Cancelado!', 'Operación Cancelada', 'error');
                // }
            });
        }
    },
    created() {
        // this.incremKey(0)
        // this.generarModal('')
    },
    activated: async function () {
        let me = this;
        me.isValidSession();
        this.$route.meta.auth = localStorage.getItem('autenticado');
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
        // window.setTimeout( function () {
        // me.initPages()
        me.cargarDatos();
        // }, 1000)
    },
    beforeDestroy: function () {
        let me = this;
        var element = document.getElementById('sec_' + me.entidad);
        element.classList.add('d-none');
    },
    deactivated: function () {
        // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal //(me.contModal%2==1?false:true)
        // var element = document.getElementById('sec-local')
        // element.classList.add('d-none')
    },
    components: { Loader }
}
</script>