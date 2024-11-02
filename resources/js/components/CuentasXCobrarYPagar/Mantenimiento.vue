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
                            <router-link tag="a" to="/cuentaxcyp">Cuentas por Cobrar & Pagar</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Cuenta
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Cuenta</h4>
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
                                        <input type="hidden" :value="id" name="id">
                                      
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-3 col-xs-12">
                                                    <label :for="'select-tipo_cuenta_'+entidad">Tipo de Cuenta <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select-tipo_cuenta_'+entidad" 
                                                    name="tipo_cuenta" @change="selectTipoCuenta($event)">
                                                        <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                                        <option v-for="f in tipos" :selected="tipoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-5 col-xs-12" v-if="tipoId == '2'">
                                                    <div class="form-group">
                                                            <label :for="'proveedor_'+entidad">Proveedor <span class="text-danger">*</span></label>
                                                            <vue-typeahead-bootstrap
                                                                :id="'proveedor_'+entidad"
                                                                :ieCloseFix="false"
                                                                inputClass="form-control-sm"
                                                                v-model="query"
                                                                :data="proveedores"
                                                                :serializer="(item) => item.persona"
                                                                @hit="selectProveedor = $event"
                                                                @input="buscarProveedores" 
                                                                @keyup.delete="eliminarProveedor">
                                                            </vue-typeahead-bootstrap>
                                                    
                                                    </div>
                                                    <input type="hidden" name="codproveedor" :id="'codproveedor_'+entidad" :value="proveedorId">
                                                </div>

                                                <div class="col-md-5 col-xs-12" v-if="tipoId == '1'">
                                                    <div class="form-group">
                                                            <label :for="'cliente_'+entidad">Cliente <span class="text-danger">*</span></label>
                                                            <vue-typeahead-bootstrap
                                                                :id="'cliente_'+entidad"
                                                                :ieCloseFix="false"
                                                                inputClass="form-control-sm"
                                                                v-model="query2"
                                                                :data="personas"
                                                                :serializer="(item) => item.persona"
                                                                @hit="selectPersona = $event"
                                                                @input="buscarPersonas" 
                                                                @keyup.delete="eliminarPersona">
                                                            </vue-typeahead-bootstrap>
                                                    
                                                    </div>
                                                    <input type="hidden" name="codcliente" :id="'codcliente_'+entidad" :value="clienteId">
                                        
                                                </div>

                                                <div class="col-md-3 col-xs-12" v-if="tipoId == '2'">
                                                    <label :for="'select-tipo_documento_'+entidad">Tipo Documento <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select-tipo_documento_'+entidad" 
                                                    name="tipo_documento" @change="selectTipoDocumento($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in tiposDocumento2" :selected="tipoDocId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-xs-12" v-if="tipoId == '1'">
                                                    <label :for="'select-tipo_documento_'+entidad">Tipo Documento <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select-tipo_documento_'+entidad" 
                                                    name="tipo_documento" @change="selectTipoDocumento($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in tiposDocumento" :selected="tipoDocId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>
                                                    
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'serie_'+entidad">Serie <span class="text-danger">*</span></label>
                                                        <input type="text" :id="'serie_'+entidad" @keypress="soloLetrasNumeros($event)" class="form-control form-control-sm text-center" name="serie" maxlength="5">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'numero_'+entidad">Número <span class="text-danger">*</span></label>
                                                        <input type="text" :id="'numero_'+entidad" @keypress="soloNumeros($event)" maxlength="8" class="form-control form-control-sm text-center" name="numero" >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'fecha_vencimiento_'+entidad">Fecha Vencimiento <span class="text-danger">*</span></label>
                                                        <input type="date" :id="'fecha_vencimiento_'+entidad" class="form-control form-control-sm text-center" 
                                                            name="fecha_vencimiento" :min="obtenerFechaActual()">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'importe_'+entidad">Importe <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" min="0.01" :id="'importe_'+entidad" class="form-control form-control-sm text-center" name="importe">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <label :for="'select_moneda_'+entidad">Moneda <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select_moneda_'+entidad" 
                                                    name="moneda" @change="selectTipoMoneda($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in monedas" :selected="monedaId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-xs-12">
                                                    <label :for="'select_operacion_'+entidad">Operación <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select_operacion_'+entidad" 
                                                    name="operacion" @change="selectTipoOperacion($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in tipoOperacion" :selected="tipoOperacionId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>
                                        

                                                <div class="col-md-2 col-xs-12" v-if="tipoId == '2'">
                                                    <label :for="'select_tipo_'+entidad">Tipo <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select_tipo_'+entidad" 
                                                    name="tipo" @change="selectTipoCuentaPagar($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in tiposCtaPagar" :selected="tipoPagoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-2 col-xs-12" v-if="tipoId == '2'">
                                                    <label :for="'select_tipo_gasto_'+entidad">T. Gasto <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select_tipo_gasto_'+entidad" 
                                                    name="tipogasto" @change="selectTipoGasto($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in tiposGasto" :selected="tipoGastoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-xs-12" v-if="tipoId == '2'">
                                                    <label :for="'select_unidad_'+entidad">Unidad <span class="text-danger">*</span></label>
                                                    <select class="custom-select custom-select-sm" :id="'select_unidad_'+entidad" 
                                                    name="unidad" @change="selectUnidades($event)">
                                                        <option value="" disabled="" selected="">Seleccione</option>
                                                        <option v-for="f in unidades" :selected="unidadId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-xs-12" v-if="tipoId=='2'">
                                                    <div class="form-group">
                                                        <label :for="'partida_'+entidad">Partida de Cuenta</label>
                                                        <input type="text" maxlength="20" :id="'partida_'+entidad" class="form-control form-control-sm text-center" name="partida"
                                                        @keyup="soloNumeros($event)">
                                                    </div>
                                                </div>

                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'sustento_'+entidad">Sustento/Motivo <span class="text-danger">*</span></label>
                                                        <textarea :id="'sustento_'+entidad" class="form-control form-control-sm no-resize" name="sustento"
                                                         rows="3"></textarea>
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
 name: 'MantenimientoCuentaXCobrarPagar',
 mixins: [misMixins],
 components: {
    // Proveedor
 },
 data () {
     return {
         accion: 'Registrar',
         url: '/cuentaxcyp/guardar',
         url2: '/cuentaxcyp',
         tiposDocumento2: [
            { id: 'B', nombre: 'Boleta' },
            { id: 'F', nombre: 'Factura' },
            { id: 'RXH', nombre: 'RRxHH' },
            { id: 'NC', nombre: 'Nota de Credito' },
            { id: 'O', nombre: 'Otros' },
         ],
         tipos: [
            { id: '1', nombre: 'Cuenta por Cobrar' },
            { id: '2', nombre: 'Cuenta por Pagar' }
         ],
         tipoId: '2',
         monedaId: '',
         tipoDocId: '',
         unidadId: '',
         tiposDocumento: [
            { id: 'B', nombre: 'Boleta' },
            { id: 'F', nombre: 'Factura' },
            { id: 'RXH', nombre: 'RRxHH' },
            { id: 'ND', nombre: 'Nota de Debito' },
            { id: 'O', nombre: 'Otros' },
         ],
         monedas: [
            { id: "PEN", nombre: "Soles" },
            { id: "USD", nombre: "Dólares" },
         ],
         tiposCtaPagar: [
            { id: "G", nombre: "Gasto" },
            { id: "C", nombre: "Costo" },
            { id: "A", nombre: "Activo" }
         ],
         tipoOperacion: [
            { id: "C", nombre: "Contado" },
            { id: "D", nombre: "Crédito" },
         ],
         unidades: [
            { id: "001", nombre: "001" },
            { id: "002", nombre: "002" },
            { id: "003", nombre: "003" },
            { id: "004", nombre: "004" },
            { id: "005", nombre: "005" },
            { id: "006", nombre: "006" },
            { id: "007", nombre: "007" }     
         ],
         tiposGasto: [
            { id: "F", nombre: "Fijos" },
            { id: "V", nombre: "Variables" },
            { id: "FIN", nombre: "Finacieros" },
         ],
         tipoGastoId: '',
         proveedores: [],
         proveedorId: 0,
         selectProveedor: null,
         tipoPagoId: '',
         tipoOperacionId: '',
         tipo: '',
         id: 0,
         entidad: 'cuenta_x_cobrar_pagar_mant',
         bloquear_fecha:true,
         query: '',
         selectPersona: null,
         personas: [],
         clienteId: 0,
         query2: '',
         min:1,
         max:10000
     }
 },
 watch: {
    query() {
        // let me=this
        // if(me.selectPersona!=null){
        //     me.total=0.00      
        // }
    },
    query2() {
        // let me=this
        // if(me.selectProveedor!=null){
        //     me.total=0.00      
        // }
    },
    selectPersona: function() {
        let me = this;
        if (me.selectPersona != null) {
            me.clienteId = me.selectPersona.id
        } 
    },
    selectProveedor: function() {
        let me = this;
        if (me.selectProveedor != null) {
            me.proveedorId = me.selectProveedor.id
        } 
    }
 },
 methods: {
    selectTipoMoneda (el) {
        this.monedaId = el.target.value;
    },
    selectTipoDocumento (el) {
        this.tipoDocId = el.target.value;
    },
    selectTipoCuentaPagar (el) {
        this.tipoPagoId = el.target.value;
    },
    selectUnidades (el) {
        this.unidadId = el.target.value;
    },
    selectTipoOperacion (el) {
        this.tipoOperacionId = el.target.value;
    },
    selectTipoCuenta (el) {
        let me = this;
        me.tipoId = el.target.value;
    },
    async cargarDatos () {
    //   let me = this
    //   me.accion = 'Registrar'
    //   document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
    //   let response = await axios.get('/reclamo/areas');
    //   if (response.data.estado) {
    //     me.areas = response.data.areas;
    //   }
    },
    async buscarPersonas () {
      let me = this;
      
      let response = await axios({
        method: "POST",
        url: '/cuentaxcobrar/buscarpersonas',
        data: {
          query: me.query2,
          tipo: 'C'
        },
      });
      me.personas = response.data.personas;
    //   me.clienteId = 0;
    },
    async buscarProveedores () {
      let me = this;
      
      let response = await axios({
        method: "POST",
        url: '/cuentaxcobrar/buscarpersonas',
        data: {
          query: me.query,
          tipo: 'P'
        },
      });
      me.proveedores = response.data.personas;
    //   me.proveedorId = 0;
    },
    eliminarPersona (){
        this.selectPersona=null
        this.clienteId = 0
    },
    eliminarProveedor (){
        this.selectProveedor=null
        this.proveedorId = 0
    }
 },
 activated: function () {
    let me = this
    me.isValidSession()
    me.selectProveedor = null
    me.selectPersona = null
    me.proveedorId = 0;
    me.clienteId = 0;
    me.query = '';
    me.query2 = '';
    me.monedaId = '';
    me.tipoDocId = '';
    me.tipoId = '2';
    me.tipoOperacionId = '';
    me.tipoPagoId = '';
    me.unidadId = '';
    if (me.tipoId == '2') {
        let elUnidad = document.getElementById('select_unidad_'+me.entidad);
        if (elUnidad) {
            elUnidad .value = ''
        }
        let elTipo = document.getElementById('select_tipo_'+me.entidad);
        if (elTipo) {
            elTipo .value = ''
        }
        let elPartida = document.getElementById('partida_'+me.entidad);
        if (elPartida) {
            elPartida .value = ''
        }
    }
    document.getElementById('select-tipo_documento_'+me.entidad).value = ''
    document.getElementById('serie_'+me.entidad).value = ''
    document.getElementById('numero_'+me.entidad).value = ''
    document.getElementById('fecha_vencimiento_'+me.entidad).value = ''
    document.getElementById('importe_'+me.entidad).value = ''
    document.getElementById('select_moneda_'+me.entidad).value = ''
    document.getElementById('select_operacion_'+me.entidad).value = ''
    document.getElementById('sustento_'+me.entidad).value = ''
    // me.tipoId = '2';
    

    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')
    // me.detalles = []
    // me.listDetalles = []
    // me.bloquear_fecha = false
    // me.query = ''
    // me.selectPersona = null
    // me.personas = []
    // me.personal = []
    // me.personaId = 0
    // me.personalId = 0
    // me.query2 = ''
    // me.query3 = ''
    // me.selectComprobante = null
    // me.comprobantes = []
    // me.comprobanteId = 0
    // me.tipo = ''
    // me.total = 0
    // me.tipo_operacion = ''
    // me.chkSincronizar = false
    // me.arrDetalles = []
    // me.listDetalles = []
    
        
    // document.getElementById('select_tipo_operacion').val = ''
    // me.cargarDatos()
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