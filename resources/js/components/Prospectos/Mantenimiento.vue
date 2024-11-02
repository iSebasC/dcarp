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
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/prospecto">Prospectos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Prospecto
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Prospecto</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height justify-content-md-center">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="mdi mdi-car-search-outline"></i> Datos de Auto</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body justify-content-md-center">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarprospecto">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <h6 class="text-muted title ml-2">
                                                <strong>DATOS DEL CLIENTE</strong>
                                            </h6>
                                            
                                            <div class="row mt-1">
                                                <div class="col-md-2 col-xs-12">
                                                    <label class="ml-2" :for="'select_tipodocumento_' + entidad"
                                                    >Tipo Documento <span class="text-danger">*</span></label
                                                    >
                                                    <select
                                                    class="custom-select custom-select-sm ml-2"
                                                    name="tipodocumento"
                                                    :id="'select_tipodocumento_' + entidad"
                                                    @change="tipoVenta"
                                                    >
                                                    <option
                                                        v-for="f in tipos"
                                                        :key="f.id"
                                                        :value="f.id"
                                                        :selected="f.id == tipoId"
                                                    >
                                                        {{ f.nombre }}
                                                    </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'documento_' + entidad"
                                                        >Documento <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'documento_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="documento"
                                                        autocomplete="off"
                                                        @keypress="validarNroDocumento(tipoId, $event)"
                                                        minlength="8"
                                                        maxlength="12"
                                                        @keyup.enter="busquedaCliente('E')"
                                                        @change="busquedaCliente('C')"
                                                        @keyup="aMayusculas($event)"
                                                    />
                                                    </div>
                                                </div>

                                                <div v-if="mostrarTipo" class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'apellidos_' + entidad"
                                                        >Apellidos <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'apellidos_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="apellidos"
                                                        @keypress="soloLetras($event)"
                                                    />
                                                    </div>
                                                </div>

                                                <div v-if="mostrarTipo" class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'nombres_' + entidad"
                                                        >Nombres <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'nombres_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="nombres"
                                                        @keypress="soloLetras($event)"
                                                    />
                                                    </div>
                                                </div>

                                                <div v-if="!mostrarTipo" class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'razonSocial_' + entidad"
                                                        >Razon Social <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'razonSocial_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="razonSocial"
                                                        @keypress="soloLetras($event)"
                                                    />
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-2">
                                                    <label :for="'telefono_' + entidad"
                                                        >Teléfono <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'telefono_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="telefono"
                                                        @keypress="soloNumeros($event)"
                                                        minlength="6"
                                                        maxlength="9"
                                                    />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row"> 
                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group ml-2">
                                                    <label :for="'correo_' + entidad"
                                                        >Correo Electrónico
                                                        <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="email"
                                                        :id="'correo_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="correo"
                                                    />
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'direccion_' + entidad"
                                                        >Dirección
                                                        <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="email"
                                                        :id="'direccion_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="direccion"
                                                    />
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'tiempo_estimado_' + entidad"
                                                        >Tiempo Estimado de Compra
                                                        <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="date"
                                                        :id="'tiempo_estimado_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="tiempo_estimado"
                                                    />
                                                    </div>
                                                </div>

                                                <div class="col-md-12" :id="'data-cliente_' + entidad"></div>
                                            </div>
                                       
                                            <h6 class="text-muted title ml-2 mt-2">
                                                <strong>DATOS DE CAPTACIÓN</strong>
                                            </h6>
                                            
                                            <div class="row">
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="form-group ml-2">
                                                        <label :for="'auto_'+entidad">Auto <span class="text-danger">*</span></label>
                                                        <vue-typeahead-bootstrap
                                                        :id="'auto_'+entidad"
                                                        :ieCloseFix="false"
                                                        inputClass="form-control-sm"
                                                        v-model="query"
                                                        :data="autos"
                                                        :serializer="(item) => item.auto"
                                                        @hit="selectAuto = $event"
                                                        @input="buscarAutos" 
                                                        @keyup.delete="eliminarAuto">
                                                        </vue-typeahead-bootstrap>
                                                    </div>
                                                    <input type="hidden" name="idAuto" :id="'idAuto_'+entidad" :value="autoId">
                                                </div>

                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'marca_' + entidad"
                                                        >Marca
                                                        <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'marca_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="marca"
                                                        :value="marcaAuto"
                                                        readonly=""
                                                    />
                                                    <input type="hidden" name="marcaId" :value="marcaId">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                    <label :for="'modelo_' + entidad"
                                                        >Modelo
                                                        <span class="text-danger">*</span></label
                                                    >
                                                    <input
                                                        type="text"
                                                        :id="'modelo_' + entidad"
                                                        class="form-control form-control-sm"
                                                        name="modelo"
                                                        :value="modeloAuto"
                                                        readonly=""
                                                    />
                                                    <input type="hidden" name="modeloId" :value="modeloId">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 col-xs-12">
                                                    <label class="ml-2" :for="'select_origen_' + entidad"
                                                    >Origen <span class="text-danger">*</span></label>
                                                    <select
                                                        class="custom-select custom-select-sm ml-2"
                                                        name="select_origen" :id="'select_origen_' + entidad"
                                                        @change="selectOrigen"
                                                    >
                                                        <option value="" selected="" disabled="">Seleccione</option>
                                                        <option v-for="f in origenes" key="f.id" :value="f.id" :selected="f.id == tipoId">
                                                            {{ f.nombre }}
                                                        </option>
                                                        <option value="otro">Indique Otra</option>
                                                  
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" :id="'origen_ocultar_'+entidad">
                                                    <div class="form-group">
                                                       <label :for="'origen_'+entidad">Origen <small>(Nuevo)</small> <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'origen_'+entidad" class="form-control form-control-sm" name="origen" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <label class="ml-2" :for="'select_etiqueta_' + entidad"
                                                    >Etiqueta <span class="text-danger">*</span></label>
                                                    <select
                                                        class="custom-select custom-select-sm ml-2"
                                                        name="select_etiqueta" :id="'select_etiqueta_' + entidad"
                                                        @change="selectEtiqueta"
                                                    >
                                                        <option value="" selected="" disabled="">Seleccione</option>
                                                         
                                                        <option v-for="f in etiquetas"
                                                            :key="f.id" :value="f.id" :selected="f.id == tipoId">
                                                            {{ f.nombre }}
                                                        </option>
                                                        <option value="otro">Indique Otra</option>
                                                  
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-3 col-xs-12 ocultar" :id="'etiqueta_ocultar_'+entidad">
                                                    <div class="form-group">
                                                       <label :for="'etiqueta_'+entidad">Etiqueta <small>(Nueva)</small> <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'etiqueta_'+entidad" class="form-control form-control-sm" name="etiqueta" maxlength="255">
                                                    </div>
                                                </div>

                                            </div>
                                
                                            <div class="row">
                                                <div class="col-md-12" :id="'data-error_'+entidad">
                                                </div>
                                            </div>
                                        
                                            <div class="form-actions ml-2 mt-3">
                                                <button type="button" class="btn btn-sm btn-success" :id="'btnEnvio_'+entidad" 
                                                    @click="enviarForm(url, entidad)">
                                                    <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                                </button>
                                                <button type="button" @click="atras(url)" class="btn btn-sm btn-danger mr-1">
                                                    <i class="mdi mdi-close icon-size"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>
                                   </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
export default {
 name: 'MantenimientoProspecto',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/prospecto',
         marcas: [],
         acabados: [],
         origenes: [],
         etiquetas: [],
         marcaId: 0,
         unidadMedidadId: 0,
         acabadoId: 0,
         tipoProductoId: 0,
         id:0,
         tipo: '',
         entidad: 'prospecto-mant',
         tipos: [
            { id: "DNI", nombre: "DNI" },
            { id: "CE", nombre: "CARNET EXT." },
            { id: "RUC", nombre: "RUC" },
         ],
         tipoId: 'B',
         mostrarTipo: true,
         cliente: null,
         ejecutadoBq: false,
         selectAuto: null,
         autos: [],
         autoId: 0,
         query: '',
         marcaAuto: '',
         marcaId:0,
         modeloAuto: '',
         modeloId: 0
     }
 },
 watch: {
    query() {
        let me=this
        if(me.selectAuto==null){
            me.autoId = 0
            me.marcaId = 0
            me.modeloId = 0
            me.marcaAuto = ''
            me.modeloAuto = ''
        }
    },
    selectAuto: function() {
        let me = this;
        if (me.selectAuto != null) {
            me.autoId = me.selectAuto.id
            me.marcaAuto = me.selectAuto.marca
            me.modeloAuto = me.selectAuto.modelo

            me.marcaId = me.selectAuto.marcaId
            me.modeloId = me.selectAuto.modeloId
            // console.log('me.cliente', me.cliente);
        } 
    },
 },
 methods: {
    async buscarAutos () {
      let me = this;
      
      let response = await axios({
        method: "POST",
        url: '/buscarautos',
        data: {
          query: me.query
        },
      });
      me.autos = response.data.autos;
    //   me.autoId = 0
    //   me.marcaId = 0
    //   me.modeloId = 0
    //   me.marcaAuto = ''
    //   me.modeloAuto = ''
    },
    eliminarAuto (){
        this.selectAuto=null
        this.autoId = 0
        this.marcaId = 0
        this.modeloId = 0
        this.marcaAuto = ''
        this.modeloAuto = ''
    },
    async busquedaCliente(type) {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
      document.getElementById("data-cliente_" + me.entidad).innerHTML = "";

      if (type == 'E') { me.ejecutadoBq = true; }
      if (type == 'C' && me.ejecutadoBq) {
        me.ejecutadoBq = false
      } else {
        me.ejecutadoBq = true
      }

      if (me.ejecutadoBq) {
            if (val == "") {
                document.getElementById("data-cliente_" + me.entidad).innerHTML =
                '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Seleccione Tipo de Documento Antes de Seguir...</strong></a></div ></div>';
            } else {
                let valorDoc = document.getElementById("documento_" + me.entidad).value;
                if (valorDoc.length >= 8 && valorDoc.length <= 11) {
                    document.getElementById("data-cliente_" + me.entidad).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>';

                    // let response = await axios.get('/obtenercliente/'+valorDoc+'/'+val)
                    let response = await axios({
                        method: "post",
                        url: "/getcliente/" + valorDoc,
                        data: {
                            _token: me.token,
                        },
                    });

                    if (!response.data.estado) {
                        let res = await me.consultarApi(
                            valorDoc,
                            valorDoc.length == 11 ? 2 : 1
                        );
                        let cl = res.data;
                        console.log("resp", cl);
                        if (cl.success) {
                            let client = cl.data;
                            // console.log(cl);
                            if (valorDoc.length == 11) {
                                me.cliente = {
                                    razonSocial: client.nombre_o_razon_social,
                                    direccion: client.direccion_completa !=null?client.direccion_completa:'',
                                    correoElectronico: '',
                                    telefono: ''
                                };
                            } else {
                                me.cliente = {
                                    apellidos: client.apellido_paterno + " " + client.apellido_materno,
                                    nombres: client.nombres,
                                    direccion: client.direccion_completa != null?client.direccion_completa:'',
                                    correoElectronico: '',
                                    telefono: ''
                                };
                            }
                        }
                    } else {
                        me.cliente = null;
                    }

                    let bandValidacion = true;
                    window.setTimeout(function () {
                        document.getElementById("data-cliente_" + me.entidad).innerHTML =
                        "";
                        if (response.data.estado) {
                            me.cliente = {
                                apellidos: response.data.cliente.apellidos,
                                nombres: response.data.cliente.nombres,
                                razonSocial: response.data.cliente.razonSocial,
                                direccion: response.data.cliente.direccion != null?response.data.cliente.direccion:"",
                                correoElectronico: response.data.cliente.correoElectronico !=null?response.data.cliente.correoElectronico:"",
                                telefono: response.data.cliente.telefono != null? response.data.cliente.telefono:""
                            };
                            if (
                                me.cliente.apellidos == null &&
                                me.cliente.nombres == null &&
                                me.cliente.razonSocial == null
                            ) {
                                bandValidacion = false;
                            }

                            // console.log('band', bandValidacion);
                            if (!bandValidacion) {
                                document.getElementById(
                                "data-cliente_" + me.entidad
                                ).innerHTML =
                                '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>';
                            }
                        } else {
                        // me.cliente = null
                        // console.log('cliente', me.cliente);
                            if (me.cliente == null) {
                                document.getElementById(
                                "data-cliente_" + me.entidad
                                ).innerHTML =
                                '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Documento no encontrado, Registre por favor...</strong></a></div ></div>';
                            }
                        }

                        if (me.tipoId == 'RUC') {
                            document.getElementById(`razonSocial_${me.entidad}`).value = me.cliente != null? me.cliente.razonSocial: '';
                        } else {
                            document.getElementById(`apellidos_${me.entidad}`).value = me.cliente != null? me.cliente.apellidos: '';
                            document.getElementById(`nombres_${me.entidad}`).value = me.cliente != null? me.cliente.nombres: '';
                        }
                        document.getElementById(`telefono_${me.entidad}`).value = me.cliente != null? me.cliente.telefono: '';
                        document.getElementById(`correo_${me.entidad}`).value = me.cliente != null? me.cliente.correoElectronico: '';
                        document.getElementById(`direccion_${me.entidad}`).value = me.cliente != null? me.cliente.direccion: '';
                      
                    }, 500);
                } else {
                    document.getElementById("data-cliente_" + me.entidad).innerHTML =
                        '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI o RUC</strong></a></div ></div>';
                }
            }
      }
    },
    selectEtiqueta () {
        let me = this;
        let val = document.getElementById(`select_etiqueta_${me.entidad}`).value;
        let element =  document.getElementById(`etiqueta_ocultar_${me.entidad}`)
      
        element.classList.add('ocultar')
        if (val === 'otro') {
            element.classList.remove('ocultar')
        }
    },
    selectOrigen () {
        let me = this;
        let val = document.getElementById(`select_origen_${me.entidad}`).value;
        let element =  document.getElementById(`origen_ocultar_${me.entidad}`)
      
        element.classList.add('ocultar')
        if (val === 'otro') {
            element.classList.remove('ocultar')
        }
    },
    tipoVenta() {
      let me = this;
      let val = document.getElementById(
        "select_tipodocumento_" + me.entidad
      ).value;
    
      let element = document.getElementById("documento_" + me.entidad);
      me.tipoId = val;
       
      if (val == "RUC") {
        element.setAttribute("minlength", "11");
        element.setAttribute("maxlength", "11");
        me.mostrarTipo = false;
      } else if (val == "DNI") {
        element.setAttribute("minlength", "8");
        element.setAttribute("maxlength", "8");
        me.mostrarTipo = true;
      } else {
        element.setAttribute("minlength", "8");
        element.setAttribute("maxlength", "12");
        me.mostrarTipo = true;
        // me.igv = 0
        // me.subtotal = me.total
      }
      element.value = "";
      element.focus();

    //   if (me.cita != null) {
    //     me.cita.documento = "";
    //   }
      // document.getElementById('cliente').value = ''
    },
    async cargarDatos () {
      let me = this
      me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
      document.getElementById('data-error_'+me.entidad).innerHTML = ''
      me.mostrarTipo = true;
          
      if (me.id != 0) {
        let respuesta = await axios.get('/obtenerprospecto/'+me.id)

        if (respuesta.data.estado) {
            let prospecto = respuesta.data.prospecto;
            me.accion = 'Editar'
            document.getElementById('select_tipodocumento_'+me.entidad).value = prospecto.tipodocumento;
            document.getElementById('documento_'+me.entidad).value = prospecto.documento;
            if (prospecto.tipodocumento == 'RUC') {
                me.mostrarTipo = false;
            }
          
            // document.getElementById('telefono_'+me.entidad).value = prospecto.telefono;
            // document.getElementById('correo_'+me.entidad).value = prospecto.correoElectronico;
            // document.getElementById('direccion_'+me.entidad).value = prospecto.direccion;
            document.getElementById('tiempo_estimado_'+me.entidad).value = prospecto.tiempoEstimadoCompra;
            // document.getElementById('marca_'+me.entidad).value = prospecto.marca;
            // document.getElementById('modelo_'+me.entidad).value = prospecto.modelo;
            document.getElementById('select_origen_'+me.entidad).value = prospecto.idOrigen;
            document.getElementById('select_etiqueta_'+me.entidad).value = prospecto.idEtiqueta;
        
            me.selectAuto = {
                id:   prospecto.idAuto,
                marca: prospecto.marca,
                modelo: prospecto.modelo,
                marcaId:  prospecto.idMarcaAuto,
                modeloId:  prospecto.idModeloAuto
            };

            me.query = `${prospecto.marca} ${prospecto.modelo}`;
            me.marcaId = prospecto.idMarcaAuto;
            me.modeloId = prospecto.idModeloAuto;
            me.autoId = prospecto.idAuto;
            me.marcaAuto = prospecto.marca;
            me.modeloAuto = prospecto.modelo;

            me.cliente = {
                apellidos: prospecto.apellidos,
                nombres: prospecto.nombres,
                razonSocial: prospecto.razonSocial,
                telefono: prospecto.telefono,
                correoElectronico:  prospecto.correoElectronico,
                direccion: prospecto.direccion
            }
            // console.log('cliente', me.cliente)
            if (me.tipoId == 'RUC') {
                document.getElementById(`razonSocial_${me.entidad}`).value = me.cliente != null? me.cliente.razonSocial: '';
            } else {
                document.getElementById(`apellidos_${me.entidad}`).value = me.cliente != null? me.cliente.apellidos: '';
                document.getElementById(`nombres_${me.entidad}`).value = me.cliente != null? me.cliente.nombres: '';
            }
            document.getElementById(`telefono_${me.entidad}`).value = me.cliente != null? me.cliente.telefono: '';
            document.getElementById(`correo_${me.entidad}`).value = me.cliente != null? me.cliente.correoElectronico: '';
            document.getElementById(`direccion_${me.entidad}`).value = me.cliente != null? me.cliente.direccion: '';
        } else {
            this.$router.replace(me.url)
        }
      } else {
        me.accion = 'Registrar'
        
        document.getElementById('select_tipodocumento_'+me.entidad).value = 'DNI'
        document.getElementById('documento_'+me.entidad).value = ''
        // document.getElementById('apellidos_'+me.entidad).value = ''
        // document.getElementById('nombres_'+me.entidad).value = ''
        // document.getElementById('razonSocial_'+me.entidad).value = ''
       
        // document.getElementById('telefono_'+me.entidad).value = ''
        // document.getElementById('correo_'+me.entidad).value = ''
        // document.getElementById('direccion_'+me.entidad).value = ''
        document.getElementById('tiempo_estimado_'+me.entidad).value = ''
        document.getElementById('marca_'+me.entidad).value = ''
        document.getElementById('modelo_'+me.entidad).value = ''
        document.getElementById('select_origen_'+me.entidad).value = ''
        document.getElementById('select_etiqueta_'+me.entidad).value = ''
        
        me.query = ''
        me.tipoId = 'DNI'
        me.marcaId = 0
        me.modeloId = 0
        me.autoId = 0
        me.cliente = null
        me.selectAuto = null
        if (me.tipoId == 'RUC') {
            document.getElementById(`razonSocial_${me.entidad}`).value = me.cliente != null? me.cliente.razonSocial: '';
        } else {
            document.getElementById(`apellidos_${me.entidad}`).value = me.cliente != null? me.cliente.apellidos: '';
            document.getElementById(`nombres_${me.entidad}`).value = me.cliente != null? me.cliente.nombres: '';
        }
        document.getElementById(`telefono_${me.entidad}`).value = me.cliente != null? me.cliente.telefono: '';
        document.getElementById(`correo_${me.entidad}`).value = me.cliente != null? me.cliente.correoElectronico: '';
        document.getElementById(`direccion_${me.entidad}`).value = me.cliente != null? me.cliente.direccion: '';
        // me.tipo = ''
        // me.cargarMarcas('L')
      }
      me.cargarMarcas()
      me.cargarOrigenes()
      me.cargarEtiquetas()

    //   me.capturarMarca()
    //   document.getElementById('codproveedor_'+me.entidad).focus()
    },
    async cargarMarcas () {
        let me = this
        let marcas = await axios.get('/marcasautos')
        me.marcas = marcas.data.marcas
    },
    async cargarOrigenes () {
        let me = this
        let origenes = await axios.get('/origenesprospectos')
        me.origenes = origenes.data.origenesprospecto
    },
    async cargarEtiquetas () {
        let me = this
        let etiquetas = await axios.get('/etiquetasprospectos')
        me.etiquetas = etiquetas.data.etiquetasprospecto
    },
    capturarMarca () {
        let me = this
        let val = document.getElementById('select_marca_'+me.entidad).value
        let element =  document.getElementById('marca_ocultar_'+me.entidad)
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('version_'+me.entidad).focus()
        }
    }
 },
 activated: async function () {
    let me = this
    me.isValidSession()
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    me.cargarDatos()  
 },
 beforeDestroy: function () {
    let me  = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
 }
}
</script>
<style scoped>
.ocultar {
    display:none;
}
select {
    cursor: pointer;
}
.txt-unidad{
    float:right;
    position: relative;
    margin-top:-29px;
    margin-right: 10px;
    cursor: pointer;
    font-style: italic;
}
.no-resize {
    resize: none;
}

input[type="radio"] {
     /* remove standard background appearance */
     -webkit-appearance: none;
     -moz-appearance: none;
     appearance: none;
     /* create custom radiobutton appearance */
     display: inline-block;
     width: 15px;
     height: 15px;
     padding: 2px;
     /* background-color only for content */
     background-clip: content-box;
     border: 2px solid #bbbbbb;
     background-color: #e7e6e7;
     border-radius: 50%;
}

/* appearance for checked radiobutton */
input[type="radio"]:checked {
     background-color: #182fd2;
     cursor: pointer;
}


</style>