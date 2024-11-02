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
                            <router-link tag="a" to="/compra">Documentos de Baja</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Documento de Baja
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Documento de Baja</h4>
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
                                    <form class="form" :id="'formEnv_'+entidad" method="POST">
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'select_tipo_operacion_'+entidad">Aplicado a <span class="text-danger">*</span></label>
                                                                <select class="custom-select custom-select-sm" :disabled="arrDetalles.length>0" v-model="tipo_operacion" name="tipo_operacion" :id="'select_tipo_operacion_'+entidad" @change="capturarOperacion">
                                                                    <option value="" disabled="" selected="">Seleccione</option>
                                                                    <option v-for="f in tipoOperacion" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                                <label :for="'documento_'+entidad">Documento/ Razón Social <span class="text-danger">*</span></label>
                                                                <vue-typeahead-bootstrap
                                                                :id="'documento_'+entidad"
                                                                :ieCloseFix="false"
                                                                inputClass="form-control-sm"
                                                                v-model="query"
                                                                :data="personas"
                                                                :serializer="(item) => item.persona"
                                                                @hit="selectPersona = $event"
                                                                @input="buscarPersonas" 
                                                                :disabled="tipo_operacion==''"
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
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'refdocumento_'+entidad">Doc. de {{tipo=='V'?'Venta':'Compra'}} <span class="text-danger">*</span></label>
                                                                <vue-typeahead-bootstrap
                                                                :ieCloseFix="false"
                                                                :id="'refdocumento_'+entidad"
                                                                inputClass="form-control-sm"
                                                                v-model="query2"
                                                                :data="comprobantes"
                                                                :serializer="(item) => item.comprobante"
                                                                @hit="selectComprobante = $event"
                                                                @input="buscarComprobantes" 
                                                                @keyup.delete="eliminarComprobante">
                                                                </vue-typeahead-bootstrap>
                                                            </div>
                                                            <input type="hidden" name="idComprobante" :id="'idComprobante_'+entidad" :value="comprobanteId">
                                                        </div>

                                                        <div class="col-md-6" style="margin-top:30px;" v-if="tipo == 'V'">
                                                            <input type="checkbox" name="traer_datos" :id="'traer_datos_'+entidad" @change="sincronizar();" :checked="chkSincronizar">
                                                            <label class="text-muted" :for="'traer_datos_'+entidad"><small>Sincronizar Detalles</small></label>
                                                        </div>
                                                   

                                                        <div class="col-md-5 col-xs-12" v-if="tipo=='C'">
                                                            <label :for="'nroDocumento_'+entidad">Documento <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control form-control-sm" :id="'nroDocumento_'+entidad" name="nroDocumento" />
                                                        </div>
                                                       
                                                    </div>

                                                    <div class="row" v-if="tipo_operacion == 'V'">
                                                        <div class="col-md-10">
                                                            <label :for="'select-motivo_'+entidad">Motivo <span class="text-danger">*</span></label>
                                                            <select class="custom-select custom-select-sm" :id="'select-motivo_'+entidad" 
                                                            name="motivo">
                                                                <option value="" disabled="" selected="">Seleccione</option>
                                                                <option v-for="f in opciones" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="row my-1">
                                                        <div class="col-xs-12 col-md-12">
                                                            <span class="text-danger h2"><strong>TOTAL: </strong></span>
                                                            <span class="h4"><strong v-text="' '+ (moneda=='S'?'S/':'$')+' '+total"></strong></span>
                                                            <input type="hidden" name="total" :value="total">
                                                        </div>
                                                    </div>
                                        
                                                </div>

                                                <div class="col-md-8 col-xs-12" v-if="tipo_operacion!=''">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" :id="'listDetalles_'+entidad" name="listDetalles" :value="listDetalles.join(',')">
                                                            <small class="text-danger px-1"><strong>Detalles de Doc. de {{tipo=='V'?'Venta':'Compra'}}</strong></small>
                                                            <button type="button" v-if="tipo_operacion=='V'" class="btn btn-primary btn-sm my-0-5" 
                                                            @click="agregarDetalle" :disabled="chkSincronizar"><i class="mdi mdi-plus icon-size"></i></button>
                                                            <div class="table-responsive px-1" style="width:98%;height:300px;margin 2rem auto;" id="tabla-compra">
                                                                <table class="table table-bordered table-sm mb-0" id="tabla">
                                                                    <thead style="background: #275EE5; color: #fff;">
                                                                    <tr>
                                                                        <th class="text-center" style="width:140px;">Cantidad</th>
                                                                        <th class="text-center" style="width:120px;">Descripción</th>
                                                                        <th class="text-center" style="width:140px;">Precio</th>
                                                                        <th class="text-center" style="width:60px;">Sub Total</th>
                                                                        <th class="text-center" style="width:30px;"></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-if="!arrDetalles.length">
                                                                            <td class="text-left text-danger" colspan="5"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                                                        </tr>

                                                                        <tr v-for="(p, index) in arrDetalles" :key="index">
                                                                            <td class="text-center" style="width:140px;">
                                                                                <input type="number" min="0.01" :max="p.cantidadmax" class="text-center px-0-2 form-control form-control-sm" 
                                                                                :name="'cantidad_'+p.id" :id="'cantidad_'+p.id" step="0.01" :value="p.cantidad" @change="calcularTotalItem(p.id)" 
                                                                                @keypress.enter="calcularTotalItem(p.id)" />
                                                                            </td>
                                                                            <td class="text-justify px-0-2" style="width:120px;">
                                                                                <p style="text-align:justify;font-size:10px;" v-text="p.descripcion"  v-if="p.isAddItf=='false'"></p>
                                                                                <input type="hidden" :name="'descripcion_'+p.id" :id="'descripcion_'+p.id" :value="p.descripcion" v-if="p.isAddItf=='false'">
                                                                                <textarea :name="'descripcion_'+p.id" class="form-control form-control-sm no-resize" 
                                                                                style="width: 220px !important;"
                                                                                :id="'descripcion_'+p.id" rows="2" v-if="p.isAddItf=='true'"></textarea>
                                                                            </td>
                                                                            <td class="text-center" style="width:140px;">
                                                                                <input type="text" min="0.00" step="0.01" class="text-center form-control form-control-sm"
                                                                                :name="'preciounit_'+p.id" :id="'preciounit_'+p.id" :value="p.preciocompra" disabled="" v-if="p.isAddItf=='false'" />
                                                                              
                                                                                <input type="number" min="0.00" step="0.01" style="width:80px;" class="text-center px-0-2 form-control form-control-sm" 
                                                                                :name="'preciounit_'+p.id" :id="'preciounit_'+p.id" :value="p.preciocompra" v-if="p.isAddItf=='true'" @change="calcularTotalItem(p.id)" 
                                                                                @keypress.enter="calcularTotalItem(p.id)"  :disabled="p.isAddItf!='true'" />
                                                                            </td>
                                                                        
                                                                            <td class="text-center" style="width:60px;">
                                                                                <input type="text" min="0.00" step="0.01" class="text-center form-control form-control-sm" 
                                                                                :name="'subtotal_'+p.id" :id="'subtotal_'+p.id" :value="p.subtotal" disabled="" />
                                                                            </td>
                                                                        
                                                                           <td class="pt-2" style="width:30px;">
                                                                                <a href="javascript:void(0)" class="btn-sm btn-danger" 
                                                                                 @click="eliminar(p.id)" title="Eliminar">
                                                                                    <i class="mdi mdi-minus-thick icon-size"></i>
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>     
                                                        </div>
                                                    </div>
                                                
                                                    <div class="row mt-1">
                                                        <div class="col-md-12" :id="'data-stock_'+entidad">
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
                                            <button type="button" @click="enviarFormDetallesBaja(url, entidad, url2)" 
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
 name: 'MantenimientoCompra',
 mixins: [misMixins],
 components: {
    // Proveedor
 },
 data () {
     return {
         accion: 'Registrar',
         url: '/guardarnota',
         url2: '/nota',
         documento: '',
         total: 0,
         arrDetalles: [],
         tipo: '',
         tipoOperacion: [
            {id: 'C', nombre: 'Compra'},
            {id: 'V', nombre: 'Venta'}
         ],
         tiposCambio: [
            {id: 'S', nombre: 'Soles'},
            {id: 'D', nombre: 'Dólares'}
         ],
         tipos: [
            {id:'B', nombre: 'Boleta'},
            {id: 'F', nombre:'Factura'}
         ],
         opciones: [
            {id:'1', nombre:'Anulación de la Operación'},
            {id:'2', nombre:'Anulación por error en el RUC'},
            {id:'3', nombre:'Corrección por error en la descripción'},
            {id:'4', nombre:'Descuento global'},
            {id:'5', nombre:'Descuento por item'},
            {id:'6', nombre:'Devolución total'},
            {id:'7', nombre:'Devolución parcial'},
            {id:'8', nombre:'Bonificación'},
            {id:'9', nombre:'Disminución en el valor'}
         ],
         entidad: 'baja_mant',
         bloquear_fecha:false,
         query: '',
         selectPersona: null,
         personas: [],
         personaId: 0,
         query2: '',
         selectComprobante: null,
         comprobantes: [],
         comprobanteId: 0,
         listDetalles:[],
         moneda:'S',
         tipo_operacion: '',
         chkSincronizar: false,
         min:1,
         max:10000,
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
    selectComprobante: function() {
        let me = this;
        if (me.selectComprobante != null) {
            me.comprobanteId = me.selectComprobante.id
            if (me.tipo_operacion == 'C') {
                me.cargarDetalles()
            }
        } 
    }
 },
 methods: {
    capturarOperacion () {
        let me = this
        me.tipo = document.getElementById(`select_tipo_operacion_${me.entidad}`).value
        me.bloquear_fecha = false
        if (me.tipo == 'V') {
            me.bloquear_fecha = true
            document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
        }
    },
    async cargarDatos () {
      let me = this
      me.accion = 'Registrar'
      document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
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
      me.arrDetalles = []
      me.listDetalles = []
    },
    async buscarComprobantes () {
        let me = this
        let response = await axios({
            method: "POST",
            url: '/buscarcomprobantes',
            data: {
                query: me.query2,
                personaId: me.personaId,
                tipo: me.tipo
            },
        });
        me.comprobantes = response.data.comprobantes
        let elem = document.getElementById(`traer_datos_${me.entidad}`)
        if (elem != null) {
            elem.checked = false
        }
        me.sincronizar()
        // me.comprobanteId = null;
        // me.selectComprobante = null
    },
    eliminarPersona (){
        this.selectPersona=null
    },
    eliminarComprobante () {
        this.selectComprobante = null
    },
    async sincronizar () {
        let me = this
        let elem = document.getElementById(`traer_datos_${me.entidad}`)
        if (elem != null) {
            me.chkSincronizar = elem.checked
        }
        me.arrDetalles = [];
        me.listDetalles = [];
        me.total = 0
        if (me.chkSincronizar) {
            if (me.selectComprobante != null) {
                me.comprobanteId = me.selectComprobante.id
                await me.cargarDetalles()
            } else {
                alert('Indique Comprobante para sincronizar...')
                elem.checked = false
            }
        }  
   
    },
    async cargarDetalles () {
        let me = this
        let arr = me.comprobanteId.split('@')
        me.comprobanteId = arr[0]
        me.moneda = arr[1]
        let response = await axios({
            method: 'POST',
            url:'/cargardetallesbaja',
            data: {
                tipo: me.tipo,
                comprobanteId: me.comprobanteId,
                personaId: me.personaId
            }
        })

        me.arrDetalles = response.data.detalles
        me.arrDetalles.forEach( el => {
            me.moneda = el.tipoMoneda
            me.listDetalles.push(el.id)
        })

        me.calcularTotal()
    },
    generarCorrelativo () {
        let me = this
        let numero = me.getRandomNumber(me.min, me.max)
        let idx = me.listDetalles.indexOf(numero)
        do {
            if (idx != -1) {
                numero = me.getRandomNumber(me.min, me.max)
                idx = me.listDetalles.indexOf(numero)
            }
        } while (idx!=-1)

        return numero
    },
    agregarDetalle () {
        let me = this
        document.getElementById(`traer_datos_${me.entidad}`).disabled = true
        let id_aleatorio = me.generarCorrelativo() 
        let elemento = { id: id_aleatorio, cantidad:1, cantidadmax:1, preciocompra: 0, subtotal: 0, isAddItf:"true" }
        me.arrDetalles.push(elemento)
        me.listDetalles.push(elemento.id)
        me.calcularTotal()
        
    },
    calcularTotalItem (id) {
        let me = this
        let cantidad = document.getElementById('cantidad_'+id).value
        let precio = document.getElementById('preciounit_'+id).value
       
        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        me.arrDetalles.forEach(element => {
            if (element.id == id) {
                
                element.cantidad = cantidad
                element.preciocompra   = precio
                // alert(element.cantidad + ' <==>' +element.cantidadmax)
                if (parseFloat(element.cantidad) <= parseFloat(element.cantidadmax)) {
                    element.subtotal = (parseFloat(element.cantidad * element.preciocompra)*1000)/1000
                    element.subtotal = Math.round(element.subtotal*100)/100
                } else {
                    document.getElementById('data-stock_'+me.entidad).innerHTML = `<div style = "margin-top:10px;"><div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible:</strong>${element.cantidadmax}</a></div ></div>`
                }     
            }
        })
        me.calcularTotal()
    },
    eliminar(id) {
        let me = this
        for(let j=0; j < me.listDetalles.length; j++) {
            let e = me.listDetalles[j]
            if (e == id) {
                me.listDetalles.splice(j,1)
            }
        }
    
        
        for(let i=0; i< me.arrDetalles.length; i++) {
            let element = me.arrDetalles[i]
            if (element.id == id) {
                me.arrDetalles.splice(i,1)
            }
        }
        
        me.calcularTotal()
    },
    calcularTotal () {
        let me = this
        let acum = 0
        me.arrDetalles.forEach(element => {
            if (element.cantidad != '' && element.preciocompra != '' && 
            parseFloat(element.cantidad) <= parseFloat(element.cantidadmax)) {
                acum+=parseFloat(element.cantidad * element.preciocompra)
            }
        })

        if (me.arrDetalles.length == 0) {
            document.getElementById(`traer_datos_${me.entidad}`).disabled = false
        }
        // console.log('total_det',acum)
        me.total = (parseFloat(acum)*1000)/1000
        me.total = Math.round(me.total*100)/100
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
    me.personaId = 0
    me.query2 = ''
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
    document.getElementById(`data-stock_${me.entidad}`).innerHTML = ''
    document.getElementById(`traer_datos_${me.entidad}`).disabled = false
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