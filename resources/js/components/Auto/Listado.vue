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
              <li class="breadcrumb-item active">Autos
              </li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12">
        <div class="btn-group float-md-right" role="group" aria-label="Agregar Auto">
          <router-link tag="a" to="/auto/crear" class="btn btn-info btn-sm btn-rounded box-shadow-2 px-2 mb-1" 
          type="button"><i class="mdi mdi-plus icon-size"></i> Agregar Auto</router-link>
        </div>
      </div>
    </div>
    <div class="content-body"><!-- Default styling start -->
      <div class="row justify-content-center" id="default">
          <div class="col-md-12 col-xs-12">
              <div class="card">
                  <div class="card-header">
                      <h4 class="card-title">Listado de Autos</h4>
                  </div>
                  <div class="card-content collapse show pb-2">
                      <div class="card-body card-dashboard pt-1 pb-0">
                        <fieldset class="form-group">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group row">
                                    <label :for="'codproveedor_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Cod. Proveedor:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="codproveedor" class="form-control form-control-sm" :id="'codproveedor_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>

                                    <label :for="'version_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Versión:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="version" class="form-control form-control-sm" :id="'version_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>

                                    <label :for="'transmision_'+entidad" class="col-sm-2 m-4px col-form-label pr-0 mr-0 pt-0">Transmisión:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="transmision" class="form-control form-control-sm" :id="'transmision_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>

                                    <!-- <label :for="'anio_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Año:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="anio" @keypress="soloNumeros($event)" minlength="6" maxlength="9" class="form-control form-control-sm" :id="'anio_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div> -->
                                  </div>   

                                  <div class="form-group row">
                                    <!-- <label :for="'precio_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Precio:</label>
                                    <div class="col-sm-2">
                                        <input type="number" name="precio" step="0.01" class="text-center form-control form-control-sm" :id="'precio_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div> -->

                                    <!-- <label :for="'tipo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">T. Auto:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" :id="'tipo_'+entidad" @change="busqueda">
                                        <option value="todo">Todos</option>
                                        <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                      </select>
                                    </div> -->

                                    <label :for="'marca_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Marca:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" :id="'marca_'+entidad" @change="busqueda">
                                        <option value="todo">Todos</option>
                                        <option v-for="a in marcas" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                      </select>
                                    </div>

                                    <label :for="'modelo_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Modelo:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" :id="'modelo_'+entidad" @change="busqueda">
                                        <option value="todo">Todos</option>
                                        <option v-for="a in modelos" :key="a.id" :value="a.id"> {{a.nombre}}</option>
                                      </select>
                                    </div>

                                    <label :for="'linea_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Línea:</label>
                                    <div class="col-sm-3">
                                      <select class="custom-select custom-select-sm" :id="'linea_'+entidad" @change="busqueda">
                                        <option value="todo">Todos</option>
                                        <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                      </select>
                                    </div>

                                  
                                  </div>

                                  <div class="form-group row">
                                    <label :for="'descripcion_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Descripción:</label>
                                    <div class="col-sm-11">
                                        <input type="text" name="descripcion" class="form-control form-control-sm" :id="'descripcion_'+entidad" 
                                        @keyup.enter="busqueda" />
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-2 pt-xs-2 pl-xs-2">
                                <div class="content-buttons btn-group">
                                  <button type="button" class="btn btn-sm btn-icon btn-info mb-1" title="Buscar" @click="busqueda">
                                    <i class="mdi mdi-magnify icon-size"></i></button>

                                  <!--<button type="button" class="btn btn-xs btn-icon btn-danger mb-1" title="Exportar a Pdf"><i class="la la-file-pdf"></i></button>-->

                                  <!--<button type="button" class="btn btn-xs btn-icon btn-success mb-1" @click="excel" title="Exportar a Excel"><i class="mdi mdi-file-excel "></i></button>-->
                                </div>

                                <div class="form-group row">
                                  <label class="col-md-5 label-control pt-1 pl-1 pr-0 col-form-label" :for="'cantidad_'+entidad" style="margin-top:3px;">Mostrar: </label>
                                  <div class="col-md-5 pt-1 pl-0">
                                    <select class="custom-select custom-select-sm pr-2 ml-1" :id="'cantidad_'+entidad" title="Registros por Página" @change="busqueda">
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
                          <table class="table table-hover mb-0">
                            <thead>
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Img. Referencial</th>
                                <th class="text-center">Cod. Proveedor</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Modelo</th>
                                <th class="text-center">Línea</th>
                                <th class="text-center">Versión</th>
                                <th class="text-center">Transmisión</th>
                                <!-- <th class="text-center">Precio</th> -->
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Fecha Registro</th>
                                <th class="text-center">Operaciones</th>                             
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!autos.length">
                                  <td class="text-left text-danger" colspan="11"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p, index) in autos" :key="p.id">
                                    <td class="text-left" v-text="((index+1)+inicio<10?'0'+(index+inicio+1):(index+inicio+1))"></td>
                                    <td class="text-center">
                                      <img v-if="p.urlImagen != null" :src="p.url" width="50" height="50" :alt="'img. Referencial de auto ' + p.marca + ' ' + p.modelo">
                                      <span v-else>-</span>
                                    </td>
                                    <td class="text-center" v-text="p.codproveedor"></td>
                                    <td class="text-center" v-text="p.marca"></td>
                                    <td class="text-center"><strong v-text="p.modelo"></strong></td>
                                    <td class="text-center">
                                      <span
                                        class="badge badge-pill badge-info"
                                        v-text="p.linea"
                                      ></span>
                                    </td>
                                    <td class="text-center" v-text="p.version"></td>
                                    <td class="text-center" v-text="p.transmision"></td>
                                    <!-- <td class="text-right" v-text="p.precio"></td> -->
                                    <td class="text-center" v-text="(p.descripcion.length>30?p.descripcion.slice(0,30) + '...':p.descripcion)"></td>
                                    <td class="text-center" v-text="p.fechaRegistro"></td>
                                    <td class="text-center">
                                      <div class="content-buttons btn-group">
                                        <router-link
                                          tag="a" title="Editar Auto"
                                          v-bind:to="{name: 'editarauto', params:{id:p.id}}"
                                          class="btn btn-sm btn-success"
                                        >
                                          <i class="mdi mdi-pencil icon-size"></i>
                                        </router-link>
                                        <button v-if="p.urlFicha != null" title="Ver Ficha Técnica de Auto" @click="verFichero(p.urlFicha)" 
                                          class="btn btn-sm btn-info icon-size">
                                          <i class="mdi mdi-link icon-size"></i>
                                        </button>
                                        <button @click="eliminar(p.id, `${p.descripcion} - ${p.marca} ${p.modelo}`)" title="Eliminar Auto" class="btn btn-sm btn-danger">
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

                              <li class="page-item" @click="((paramInicio < pageActual && paramInicio > 0)?buscar('prev'):'')">
                                <a class="page-link" href="javascript:void(0);" aria-label="Previous">«</a>
                              </li>

                              <li v-for="(op,index) in opciones" :key="index" :class="(op.opc==pageActual?'page-item active':'page-item')" @click="(op.opc!=pageActual?buscar(op.opc):'')" >
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
  </div>
</template>

<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Loader from '../Loader'
export default {
    name: 'Auto',
    mixins: [misMixins],
    data() {
        return {
            filas: 10,
            pageActual: 1,
            opciones: [],
            autos: [],
            marcas: [],
            modelos: [],
            tipos: [
              {id:'L', nombre: 'Livianos'},
              {id:'P', nombre: 'Pesados'}
            ],
            total: '',
            inicio: '',
            fin: '',
            paramInicio: '',
            paramFin: '',
            token: this.$store.state.token,
            estadoListado: false,
            entidad: 'auto'
        };
    },
    methods: {
        buscar: function (attr) {
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
            me.busqueda();
        },
        async busqueda() {
            let me = this;
            let codproveedor = document.getElementById('codproveedor_' + me.entidad).value;
            let version = document.getElementById('version_' + me.entidad).value;
            let transmision = document.getElementById('transmision_' + me.entidad).value;
            let descripcion = document.getElementById('descripcion_' + me.entidad).value;
            // let anio = document.getElementById('anio_'+me.entidad).value
            // let precio = document.getElementById('precio_'+me.entidad).value
            let linea = document.getElementById('linea_'+me.entidad).value
            let marcaId = document.getElementById('marca_' + me.entidad).value;
            let modeloId = document.getElementById('modelo_' + me.entidad).value;
          
            var el = document.getElementById('loading_' + me.entidad);
            el.classList.add('d-none');
            me.filas = document.getElementById('cantidad_' + me.entidad).value;
            let response = await axios({
                method: 'post',
                url: '/auto',
                data: {
                    'version': version,
                    'transmision': transmision,
                    'codproveedor': codproveedor,
                    'descripcion': descripcion,
                    'linea': linea,
                    'marcaId': marcaId,
                    'modeloId': modeloId,
                    'filas': me.filas,
                    'page': me.pageActual,
                    '_token': me.token
                }
            });
            me.autos = response.data.autos;
            me.total = response.data.cantidad;
            me.pageActual = response.data.page;
            me.opciones = response.data.paginador;
            me.inicio = response.data.inicio;
            me.fin = response.data.fin;
            me.paramInicio = response.data.paramInicio;
            me.paramFin = response.data.paramFin;
            me.renderTabla(me.entidad);
            let rpta = await axios.get('/marcasautosmod');
            me.marcas = rpta.data.marcas;

            let rpta2 = await axios.get('/modelosautos');
            me.modelos = rpta2.data.modelosauto;
            // alert(filtro)
        },
        excel() {
            // let me = this
            // let filtro = document.getElementById('filtro-auto').value
            // let descripcion = document.getElementById('descripcion-auto').value
            // let tipoId = document.getElementById('tipo-auto').value
            // let formaId = document.getElementById('forma-auto').value
            // let acabadoId = document.getElementById('acabado-auto').value
            // if (filtro =='') {filtro = 'null'}
            // if (descripcion =='') {descripcion = 'null'}
            // if (tipoId =='') {tipoId = 'null'}
            // if (formaId =='') {formaId = 'null'}
            // if (acabadoId =='') {acabadoId = 'null'}
            // window.open('/producto/excel/'+filtro+'/'+descripcion+'/'+tipoId+'/'+formaId+'/'+acabadoId,'_blank')
        },
        verFichero(url) {
            window.open('/storage' + url, 'download');
        },
        async cargarDatos() {
            let me = this;
            me.busqueda();
        },
        async eliminar(id, nombre) {
            let me = this;
            let token = this.$store.state.token;
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
                    });
                    if (response.data.estado === true) {
                        setTimeout(function () {
                            swal({
                                title: '¡Eliminado!',
                                text: 'Auto Eliminado con Éxito.',
                                type: 'success'
                            }).then(function () {
                                me.busqueda();
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
    async created() {
        // let me = this
        // me.cargarDatos()
    },
    activated: async function () {
        let me = this;
        me.isValidSession();
        this.$route.meta.auth = localStorage.getItem('autenticado');
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
        // window.setTimeout( function () {
        me.cargarDatos();
        // }, 1000)
    },
    deactivated: function () {
        // this.$store.state.mostrarModal = false 
    },
    beforeDestroy: function () {
        let me = this;
        var element = document.getElementById('sec_' + me.entidad);
        element.classList.add('d-none');
        // this.$store.state.mostrarModal = false 
    },
    components: { Loader }
}
</script>