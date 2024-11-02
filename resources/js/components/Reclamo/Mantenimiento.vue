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
                            <router-link tag="a" to="/reclamo">Reclamos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Reclamo
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Reclamo</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form">
                                    <i class="la la-money-check-alt"></i> Datos del Documento
                                </h4>
                            </div> -->

                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" :action="url">
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <!-- <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'select_tipo_operacion_'+entidad">Aplicado a <span class="text-danger">*</span></label>
                                                                <select class="custom-select custom-select-sm" :disabled="arrDetalles.length>0" v-model="tipo_operacion" name="tipo_operacion" :id="'select_tipo_operacion_'+entidad" @change="capturarOperacion">
                                                                    <option value="" disabled="" selected="">Seleccione</option>
                                                                    <option v-for="f in tipoOperacion" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'fecha_'+entidad">Fecha <span class="text-danger">*</span></label>
                                                                <input type="date" :id="'fecha_'+entidad" class="form-control form-control-sm text-center" name="fecha" :disabled="bloquear_fecha">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'documento_'+entidad">Cliente <span class="text-danger">*</span></label>
                                                                <vue-typeahead-bootstrap
                                                                :id="'documento_'+entidad"
                                                                :ieCloseFix="false"
                                                                inputClass="form-control-sm"
                                                                v-model="query"
                                                                :data="personas"
                                                                :serializer="(item) => item.persona"
                                                                @hit="selectPersona = $event"
                                                                @input="buscarPersonas" 
                                                                @keyup.delete="eliminarPersona">
                                                                </vue-typeahead-bootstrap>
                                                        
                                                                <!-- <label :for="'documento_'+entidad">Documento <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'documento_'+entidad" class="form-control form-control-sm" 
                                                                name="documento" maxlength="11" minlength="8" @keypress="soloNumeros($event)" @keyup.enter="busquedaProveedor"> -->
                                                            </div>
                                                            <input type="hidden" name="idPersona" :id="'idPersona_'+entidad" :value="personaId">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'refdocumento_'+entidad">N° Orden <span class="text-danger">*</span></label>
                                                                <vue-typeahead-bootstrap
                                                                :ieCloseFix="false"
                                                                :id="'refdocumento_'+entidad"
                                                                inputClass="form-control-sm"
                                                                v-model="query2"
                                                                :data="comprobantes"
                                                                :serializer="(item) => item.orden"
                                                                @hit="selectComprobante = $event"
                                                                @input="buscarComprobantes" 
                                                                @keyup.delete="eliminarComprobante">
                                                                </vue-typeahead-bootstrap>
                                                            </div>
                                                            <input type="hidden" name="idComprobante" :id="'idComprobante_'+entidad" :value="comprobanteId">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12 mb-3">
                                                            <label :for="'select-grado_'+entidad">Grado <span class="text-danger">*</span></label>
                                                            <select class="custom-select custom-select-sm" :id="'select-grado_'+entidad" 
                                                            name="grado">
                                                                <option value="" disabled="" selected="">Seleccione</option>
                                                                <option v-for="f in grados" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            </select>
                                                        </div> 
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12 mb-3">
                                                            <label :for="'select-motivo_'+entidad">Área Destino <span class="text-danger">*</span></label>
                                                            <select class="custom-select custom-select-sm" :id="'select-motivo_'+entidad" 
                                                            name="area">
                                                                <option value="" disabled="" selected="">Seleccione</option>
                                                                <option v-for="f in areas" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'refdocumento_persona_'+entidad">Asignado a <span class="text-danger">*</span></label>
                                                                <vue-typeahead-bootstrap
                                                                :ieCloseFix="false"
                                                                :id="'refdocumento_persona_'+entidad"
                                                                inputClass="form-control-sm"
                                                                v-model="query3"
                                                                :data="personal"
                                                                :serializer="(item) => item.personal"
                                                                @hit="selectPersonal = $event"
                                                                @input="buscarPersonal" 
                                                                @keyup.delete="eliminarPersonal">
                                                                </vue-typeahead-bootstrap>
                                                            </div>
                                                            <input type="hidden" name="idPersonal" :id="'idPersonal_'+entidad" :value="personalId">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label :for="'reclamo_'+entidad">Reclamo <span class="text-danger">*</span></label>
                                                            <textarea name="reclamo" class="form-control form-control-sm no-resize" :id="'reclamo_'+entidad" 
                                                                cols="30" rows="20"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                           
                                            <div class="row">
                                                <div class="col-md-12" :id="'data-error_'+entidad">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions ml-1">
                                            <button type="button" @click="enviarForm(url2, entidad)"
                                               :id="'btnEnvio_'+entidad" class="btn btn-sm btn-success">
                                                <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                            </button>
                                            <button type="button" @click="atras(url2)" class="btn btn-danger btn-sm mr-1">
                                                <i class="mdi mdi-close icon-size"></i> Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- <Proveedor ref="proveedor" :documento="this.documento"></Proveedor> -->

    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
// import Proveedor from './Proveedor'

export default {
 name: 'MantenimientoReclamo',
 mixins: [misMixins],
 components: {
    // Proveedor
 },
 data () {
     return {
         accion: 'Registrar',
         url: '/reclamo/guardar',
         url2: '/reclamo',
         documento: '',
         total: 0,
         arrDetalles: [],
         grados: [
            { id: 'U', nombre: 'Urgente' },
            { id: 'M', nombre: 'Medio' },
            { id: 'B', nombre: 'Bajo' }
         ],
         tipo: '',
         areas: [],
         entidad: 'reclamo_mant',
         bloquear_fecha:true,
         query: '',
         selectPersona: null,
         personas: [],
         personal: [],
         personaId: 0,
         personalId: 0,
         query2: '',
         query3: '',
         selectComprobante: null,
         selectPersonal: null,
         comprobantes: [],
         comprobanteId: 0,
         listDetalles:[],
         moneda:'S',
         tipo_operacion: '',
         chkSincronizar: false,
         min:1,
         max:10000
     }
 },
 watch: {
    query() {
        let me=this
        if(me.selectPersona!=null){
            me.total=0.00      
        }
    },
    query2() {
        let me=this
        if(me.selectComprobante!=null){
            me.total=0.00      
        }
    },
    selectPersona: function() {
        let me = this;
        if (me.selectPersona != null) {
            me.personaId = me.selectPersona.id
        } 
    },
    selectPersonal: function() {
        let me = this;
        if (me.selectPersonal != null) {
            me.personalId = me.selectPersonal.id
        } 
    },
    selectComprobante: function() {
        let me = this;
        if (me.selectComprobante != null) {
            me.comprobanteId = me.selectComprobante.id
        } 
    }
 },
 methods: {
    async cargarDatos () {
      let me = this
      me.accion = 'Registrar'
      document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
      let response = await axios.get('/reclamo/areas');
      if (response.data.estado) {
        me.areas = response.data.areas;
      }
    },
    async buscarPersonas () {
      let me = this;
      
      let response = await axios({
        method: "POST",
        url: '/buscarpersonas',
        data: {
          query: me.query,
          tipo: me.tipo
        },
      });
      me.personas = response.data.personas;
      me.query2 = ''
      me.selectComprobante = null
      me.comprobanteId = 0
     },
    async buscarPersonal () {
      let me = this;
      
      let response = await axios({
        method: "POST",
        url: '/reclamo/personal',
        data: {
          query: me.query3
        },
      });
      me.personal = response.data.personal;
    //   me.query3 = ''
    },
    async buscarComprobantes () {
        let me = this
        let response = await axios({
            method: "POST",
            url: '/reclamo/ordenes',
            data: {
                query: me.query2,
                personaId: me.personaId
            },
        });
        me.comprobantes = response.data.ordenes
        // me.comprobanteId = null;
        // me.selectComprobante = null
    },
    eliminarPersona (){
        this.selectPersona=null
        this.personaId = 0
    },
    eliminarPersonal (){
        this.selectPersonal=null
        this.personalId = 0
    },
    eliminarComprobante () {
        this.selectComprobante = null
        this.comprobanteId = 0
    }
 },
 activated: function () {
    let me = this
    me.isValidSession()
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')
    me.detalles = []
    me.listDetalles = []
    me.bloquear_fecha = false
    me.query = ''
    me.selectPersona = null
    me.personas = []
    me.personal = []
    me.personaId = 0
    me.personalId = 0
    me.query2 = ''
    me.query3 = ''
    me.selectComprobante = null
    me.comprobantes = []
    me.comprobanteId = 0
    me.tipo = ''
    me.total = 0
    me.tipo_operacion = ''
    me.chkSincronizar = false
    me.arrDetalles = []
    me.listDetalles = []
    
        
    // document.getElementById('select_tipo_operacion').val = ''
    me.cargarDatos()
    document.getElementById(`data-error_${me.entidad}`).innerHTML = ''
    // document.getElementById(`data-stock_${me.entidad}`).innerHTML = ''
    // document.getElementById(`traer_datos_${me.entidad}`).disabled = false
    // me.calcularTotal()
    
 },
 mounted () {
    
 },
 beforeDestroy: function () {
    let me = this
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

input[type="checkbox"] {
     visibility: hidden;
}
label {
     cursor: pointer;
}
input[type="checkbox"] + label:before {
     border: 1px solid #333;
     content: "\00a0";
     display: inline-block;
     font: 16px/1em sans-serif;
     height: 16px;
     margin: 0.20em .28em 0 0;
     padding: 0;
     vertical-align: top;
     width: 16px;
     border-radius:30%;
}
input[type="checkbox"]:checked + label:before {
     background: blue;
     color: #fff;
     content: "\2713";
     text-align: center;
}
input[type="checkbox"]:checked + label:after {
     font-weight: bold;
}

input[type="checkbox"]:focus + label::before {
    outline: rgb(59, 153, 252) auto 5px;
}

thead { position: sticky; top: 0; }
tfoot { position: sticky; bottom: 0; }

.px-0-2 {
    padding-left: 0.5em !important;
    padding-right: 0.5em !important;
}

.table-responsive {
    overflow:auto;
    padding:0 1rem;
    scrollbar-color: #b46868 rgba(0, 0, 0, 0);
    scrollbar-width: thin;

}
#tabla thead {
  position: sticky; top: 0;
}
#tabla tbody tr:hover {
    background: #C6D4F6;
}

#tabla tbody tr {
    cursor: pointer;
}
.no-resize {
    resize:none;
}
.my-0-5 {
    margin-bottom: 0.5em !important;
}
</style>