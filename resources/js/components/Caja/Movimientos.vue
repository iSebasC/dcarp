<template>
    <div class="modal fade" id="modalMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalMovimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form" id="formEnvModal02" method="POST" action="/movimiento">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMovimientoLabel">Movimientos de Caja</h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <div class="row mt-1">
                            <div class="col-md-6 col-xs-12">
                                <input type="hidden" name="cajaId" :value="cajaId" />
                                <label :for="'select_tipoperacion_'+entidad">Tipo de Operación <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="tipoperacion" :id="'select_tipoperacion_'+entidad"
                                 @change="selectTipoOperacion($event)">
                                    <!-- <option value="" disabled="" selected="">Seleccione</option> -->
                                    <option v-for="f in tipos" :key="f.id" :value="f.id" :selected="tipoOperacionId == f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" id="sec-ingreso" v-if="tipoOperacionId == 'I'">
                            <div class="col-md-6 col-xs-12">
                                <label :for="'select_tipo_'+entidad">Tipo <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="tipoIngreso" :id="'select_tipo_'+entidad" @change="selectTipoIngreso($event)">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposIngreso" :selected="tipoIngresoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                        <label :for="'cliente_'+entidad">Cliente <span class="text-danger">*</span></label>
                                        <vue-typeahead-bootstrap
                                            :id="'cliente_'+entidad"
                                            :ieCloseFix="false"
                                            inputClass="form-control-sm"
                                            v-model="query"
                                            :data="personas"
                                            :serializer="(item) => item.persona"
                                            @hit="selectPersona = $event"
                                            @input="buscarPersonas" 
                                            @keyup.delete="eliminarPersona">
                                        </vue-typeahead-bootstrap>
                                
                                </div>
                                <input type="hidden" name="codcliente" :id="'codcliente_'+entidad" :value="clienteId">
                                <input type="hidden" name="referencia" :id="'referencia_'+entidad" :value="referencia">
                            </div>
                        </div>

                        <div class="row mt-1" v-if="tipoOperacionId == 'I'">
                            <div class="col-md-6 col-xs-12" v-if="tipoIngresoId=='CC'">
                                <div class="form-group">
                                        <label :for="'cuenta_x_cobrar_'+entidad">Cuenta por Cobrar <span class="text-danger">*</span></label>
                                        <vue-typeahead-bootstrap
                                            :id="'cuenta_x_cobrar_'+entidad"
                                            :ieCloseFix="false"
                                            inputClass="form-control-sm"
                                            v-model="query2"
                                            :data="cuentas"
                                            :serializer="(item) => item.cuenta"
                                            @hit="selectCuenta = $event"
                                            @input="buscarCuentas" 
                                            @keyup.delete="eliminarCuenta">
                                        </vue-typeahead-bootstrap>
                                
                                </div>
                                <input type="hidden" name="codcuenta" :id="'codcuenta_'+entidad" :value="cuentaId">
                            </div>
    
                            <div class="col-md-6 col-xs-12">
                                <label :for="'select_tipopago_'+entidad">Forma de Ingreso <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="formapago" :id="'select_tipopago_'+entidad">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposP" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3" v-if="tipoOperacionId == 'I'">
                            <div class="col-md-6 col-xs-12">
                                <label :for="'select-tipo_documento_'+entidad">Tipo Documento <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select-tipo_documento_'+entidad" 
                                name="tipo_documento" @change="selectTipoDocumento($event)" :disabled="tipoIngresoId=='CC'">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposDocumento" :selected="tipoDocId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                                
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'serie_'+entidad">Serie <span class="text-danger">*</span></label>
                                    <input type="text" :id="'serie_'+entidad" @keypress="soloLetrasNumeros($event)" :readOnly="tipoIngresoId=='CC'" class="form-control form-control-sm text-center" name="serie" maxlength="5">
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'numero_'+entidad">Número <span class="text-danger">*</span></label>
                                    <input type="text" :id="'numero_'+entidad" @keypress="soloNumeros($event)" maxlength="8" :readOnly="tipoIngresoId=='CC'" class="form-control form-control-sm text-center" name="numero" >
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1" v-if="tipoOperacionId == 'I'">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'total_'+entidad">Total <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" :readOnly="tipoIngresoId=='CC'" :id="'total_'+entidad" class="form-control form-control-sm text-center" 
                                        name="total">
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <label :for="'select_moneda_'+entidad">Moneda <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select_moneda_'+entidad" 
                                name="moneda" @change="selectTipoMoneda($event)" :disabled="tipoIngresoId=='CC'">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in monedas" :selected="monedaId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>

                        <!-- PARA EGRESOS -->
                        <div class="row mt-3" id="sec-egreso" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-6 col-xs-12">
                                <label :for="'select_tipo_'+entidad">Tipo <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="tipoEgreso" :id="'select_tipo_'+entidad" @change="selectTipoEgreso($event)">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposEgreso" :selected="tipoEgresoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                        <label :for="'proveedor_'+entidad">Proveedor <span class="text-danger">*</span></label>
                                        <vue-typeahead-bootstrap
                                            :id="'proveedor_'+entidad"
                                            :ieCloseFix="false"
                                            inputClass="form-control-sm"
                                            v-model="query3"
                                            :data="proveedores"
                                            :serializer="(item) => item.persona"
                                            @hit="selectProveedor = $event"
                                            @input="buscarProveedores" 
                                            @keyup.delete="eliminarProveedor">
                                        </vue-typeahead-bootstrap>
                                
                                </div>
                                <input type="hidden" name="codproveedor" :id="'codproveedor_'+entidad" :value="proveedorId">
                                <input type="hidden" name="referencia" :id="'referencia_'+entidad" :value="referencia">
                           </div>
                        </div>

                        <div class="row mt-1" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-6 col-xs-12" v-if="tipoEgresoId=='CP'">
                                <div class="form-group">
                                        <label :for="'cuenta_x_pagar_'+entidad">Cuenta por Pagar <span class="text-danger">*</span></label>
                                        <vue-typeahead-bootstrap
                                            :id="'cuenta_x_pagar_'+entidad"
                                            :ieCloseFix="false"
                                            inputClass="form-control-sm"
                                            v-model="query4"
                                            :data="cuentas"
                                            :serializer="(item) => item.cuenta"
                                            @hit="selectCuentaP = $event"
                                            @input="buscarCuentasP" 
                                            @keyup.delete="eliminarCuentaP">
                                        </vue-typeahead-bootstrap>
                                
                                </div>
                                <input type="hidden" name="codcuenta" :id="'codcuenta_'+entidad" :value="cuentaId">
                            </div>
    
                            <div class="col-md-3 col-xs-12">
                                <label :for="'select_tipo_egreso_'+entidad">Tipo Egreso <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="tipo_egreso" :id="'select_tipo_egreso_'+entidad" 
                                :disabled="tipoEgresoId=='CP'" @change="selectTipoEg($event)">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tipoEgreso" :key="f.id" :selected="tipoEgId == f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <label :for="'select_tipo_gasto_'+entidad">Tipo Gasto <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" name="tipogasto" :id="'select_tipo_gasto_'+entidad"
                                :disabled="tipoEgresoId=='CP'" @change="selectTipoGasto($event)">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposGasto" :selected="tipoGastoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-6 col-xs-12">
                                <label :for="'select-tipo_documento_'+entidad">Tipo Documento <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select-tipo_documento_'+entidad" 
                                name="tipo_documento" @change="selectTipoDocumento2($event)" :disabled="tipoEgresoId=='CP'">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in tiposDocumento2" :selected="tipoDocId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                                
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'serie_'+entidad">Serie <span class="text-danger">*</span></label>
                                    <input type="text" :id="'serie_'+entidad" @keypress="soloLetrasNumeros($event)" :readOnly="tipoEgresoId=='CP'" 
                                        class="form-control form-control-sm text-center" name="serie" maxlength="5">
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'numero_'+entidad">Número <span class="text-danger">*</span></label>
                                    <input type="text" :id="'numero_'+entidad" @keypress="soloNumeros($event)" maxlength="8" :readOnly="tipoEgresoId=='CP'" 
                                        class="form-control form-control-sm text-center" name="numero" >
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'total_'+entidad">Total <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" :readOnly="tipoEgresoId=='CP'" :id="'total_'+entidad" class="form-control form-control-sm text-center" 
                                        name="total">
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <label :for="'select_moneda_'+entidad">Moneda <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select_moneda_'+entidad" 
                                name="moneda" @change="selectTipoMoneda($event)" :disabled="tipoEgresoId=='CP'">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in monedas" :selected="monedaId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <label :for="'select_unidad_negocio_'+entidad">Unidad Negocio <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select_unidad_negocio_'+entidad" 
                                name="unidadNegocio" @change="selectUnidadNegocio($event)" :disabled="tipoEgresoId=='CP'">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in unidades" :selected="unidadId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'partida_'+entidad">Partida de Cuenta</label>
                                    <input type="text" maxlength="20" :readOnly="tipoEgresoId=='CP'" :id="'partida_'+entidad" class="form-control form-control-sm text-center" name="partida"
                                    @keyup="soloNumeros($event)">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label :for="'sustento_'+entidad">Sustento/Motivo <span class="text-danger">*</span></label>
                                    <textarea :id="'sustento_'+entidad" class="form-control form-control-sm no-resize" :readOnly="tipoEgresoId=='CP'" name="sustento"
                                        rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-1" v-if="tipoOperacionId == 'E'">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'cuenta_contable_'+entidad">Cuenta Contable <span class="text-danger">*</span></label>
                                    <vue-typeahead-bootstrap
                                        :id="'cuenta_contable_'+entidad"
                                        :ieCloseFix="false"
                                        inputClass="form-control-sm"
                                        v-model="query5"
                                        :data="cuentasContables"
                                        :serializer="(item) => item.cuenta"
                                        @hit="selectCuentaContable = $event"
                                        @input="buscarCuentasContable" 
                                        @keyup.delete="eliminarCuentaContable">
                                    </vue-typeahead-bootstrap>
                                </div>
                                <input type="hidden" name="codcuentacontable" :id="'codcuentacontable_'+entidad" :value="cuentaContableId">
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'aprobado_por_'+entidad">Aprobado por <span class="text-danger">*</span></label>
                                    <vue-typeahead-bootstrap
                                        :id="'aprobado_por_'+entidad"
                                        :ieCloseFix="false"
                                        inputClass="form-control-sm"
                                        v-model="query6"
                                        :data="trabajadores"
                                        :serializer="(item) => item.personal"
                                        @hit="selectTrabajador = $event"
                                        @input="buscarTrabajador" 
                                        @keyup.delete="eliminarTrabajador">
                                    </vue-typeahead-bootstrap>
                                </div>
                                <input type="hidden" name="codtrabajador" :id="'codtrabajador_'+entidad" :value="trabajadorId">
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <label :for="'select_medio_pago_'+entidad">Medio de Pago <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select_medio_pago_'+entidad" 
                                name="medioPago" @change="selectMedioPago($event)">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in mediosPago" :selected="medioPagoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>

                        </div>

                        <!--  -->
                        <div class="row mt-3">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción </label>
                                    <textarea id="descripcion" class="form-control form-control-sm no-resize" name="descripcion"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-1">
                            <div class="col-md-3 col-xs-12" v-if="(tipoIngresoId=='CC' && tipoOperacionId == 'I') || (tipoOperacionId == 'E' && tipoEgresoId=='CP')">
                                <label :for="'select_tipo_pago_'+entidad">T. Pago <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" :id="'select_tipo_pago_'+entidad" 
                                name="tipopago" @change="selectTipoPago($event)">
                                     <option v-for="f in tipoPagos" :selected="tipoPagoId == f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12" v-if="(tipoIngresoId=='CC' && tipoOperacionId == 'I') || (tipoOperacionId == 'E' && tipoEgresoId=='CP')">
                                <div class="form-group">
                                    <label :for="'monto_pagar_'+entidad">Monto a Pagar<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" :id="'monto_pagar_'+entidad" class="form-control form-control-sm text-center" 
                                    name="monto" min="0.10">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="data-error-modal-mov">
                            </div>
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                           <i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" @click="enviarFormModalMov('modalMovimiento')" 
                       id="btnGuardar" class="btn btn-success btn-sm" >
                        <i class="mdi mdi-check-bold icon-size"></i> {{nombreBtn}}
                       </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'

export default {
    name: 'Movimientos',
    mixins: [misMixins],
    data () {
        return {
            token: this.$store.state.token,
            nombreBtn: 'Guardar',
            tipos: [ 
                { id:'I', nombre:'Ingreso de Caja' },
                { id:'E', nombre:'Egreso de Caja' },
            ],
            tiposIngreso: [
                { id:'CC', nombre:'Cuenta por Cobrar' },
                { id:'O', nombre:'Otros' }
            ],
            tiposEgreso: [
                { id:'CP', nombre:'Cuenta por Pagar' },
                { id:'O', nombre:'Otros' }
            ],
            tipoEgreso: [
                { id: "G", nombre: "Gasto" },
                { id: "C", nombre: "Costo" },
                { id: "A", nombre: "Activo" }
            ],
            tiposP: [ 
                {id:'D', nombre:'Depósito'},
                {id:'E', nombre:'Efectivo'},
            ],
            tiposGasto: [
                {id:'F', nombre:'Fijos'},
                {id:'V', nombre:'Variables'},
                {id:'FIN', nombre:'Finacieros'},
            ],
            tiposDocumento: [
                { id: 'B', nombre: 'Boleta' },
                { id: 'F', nombre: 'Factura' },
                { id: 'RXH', nombre: 'RRxHH' },
                { id: 'ND', nombre: 'Nota de Debito' },
                { id: 'O', nombre: 'Otros' },
            ],
            tiposDocumento2: [
                { id: 'B', nombre: 'Boleta' },
                { id: 'F', nombre: 'Factura' },
                { id: 'RXH', nombre: 'RRxHH' },
                { id: 'NC', nombre: 'Nota de Crédito' },
                { id: 'O', nombre: 'Otros' },
            ],
            monedas: [
                { id: "PEN", nombre: "Soles" },
                { id: "USD", nombre: "Dólares" },
            ],
            mediosPago: [
                { id:'Caja - Efectivo', nombre: 'Caja - Efectivo' },
                { id:'Tarjeta - Gerencia', nombre: 'Tarjeta - Gerencia' },
                { id:'BCP - Soles', nombre: 'BCP - Soles' },
                { id:'BBVA - Soles', nombre: 'BBVA - Soles' },
                { id:'SCTBK - Soles', nombre: 'SCTBK - Soles' },
                { id:'ITBK - Soles', nombre: 'ITBK - Soles'} ,
                { id:'BCP - Dólares', nombre: 'BCP - Dólares' },
                { id:'BBVA - Dólares', nombre: 'BBVA - Dólares' },
                { id:'SCTBK - Dólares', nombre: 'SCTBK - Dólares' },
                { id:'ITBK - Dólares', nombre: 'ITBK - Dólares' },
                { id:'Nota de Crédito', nombre: 'Nota de Crédito' },
            ],
            medioPagoId: '',
            tipoPagos: [
                { id: 'T', nombre: 'Total' },
                { id: 'A', nombre: 'Adelanto' },
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
            unidadId: '',
            tipoEgId: '',
            tipoPagoId: 'T',
            tipoGastoId: '',
            monedaId: '',
            cajaId: 0,
            tipoDocId: 0,
            cargar: 'busquedaCaja',
            query2: '',
            query3: '',
            tipoOperacionId: 'I',
            entidad: 'mov_caja',
            query: '',
            query6: '',
            query4: '',
            query5: '',
            selectCuentaContable: null,
            selectPersona: null,
            selectProveedor: null,
            selectCuenta: null,
            selectCuentaP: null,
            selectTrabajador: null,
            trabajadores: [],
            personas: [],
            proveedores: [],
            cuentasContables: [],
            cuentas: [],
            clienteId: 0,
            cuentaId: 0,
            trabajadorId: 0,
            cuentaContableId: 0,
            proveedorId: 0,
            tipoIngresoId: 'CC',
            tipoEgresoId: 'CP',
            referencia: ''
        }
    },
    watch: {
        selectPersona: function() {
            let me = this;
            if (me.selectPersona != null) {
                me.clienteId = me.selectPersona.id
                me.referencia = me.selectPersona.persona
            } 
        },
        selectCuenta: function () {
            let me = this;
            if (me.selectCuenta != null) {
                me.cuentaId = me.selectCuenta.id
                me.tipoDocId = me.selectCuenta.tipodocumento;
                me.monedaId  = me.selectCuenta.moneda;
                document.getElementById('serie_' + me.entidad).value = me.selectCuenta.serie;
                document.getElementById('numero_' + me.entidad).value = me.selectCuenta.numero;
                document.getElementById('total_' + me.entidad).value = me.selectCuenta.saldo;      
            } 
        },
        selectCuentaP: function () {
            let me = this;
            if (me.selectCuentaP != null) {
                me.cuentaId = me.selectCuentaP.id
                me.tipoDocId = me.selectCuentaP.tipodocumento;
                me.monedaId  = me.selectCuentaP.moneda;
                me.tipoGastoId = me.selectCuentaP.tipogasto;
                me.tipoEgId = me.selectCuentaP.tipo; 
                me.unidadId = me.selectCuentaP.unidad;
                document.getElementById('serie_' + me.entidad).value = me.selectCuentaP.serie;
                document.getElementById('numero_' + me.entidad).value = me.selectCuentaP.numero;
                document.getElementById('total_' + me.entidad).value = me.selectCuentaP.saldo;  
                document.getElementById('partida_' + me.entidad).value = me.selectCuentaP.partida; 
                document.getElementById('sustento_' + me.entidad).value = me.selectCuentaP.sustento;           
            } 
        },
        selectProveedor: function() {
            let me = this;
            if (me.selectProveedor != null) {
                me.proveedorId = me.selectProveedor.id
                me.referencia = me.selectProveedor.persona
            } 
        },
        selectCuentaContable: function() {
            let me = this;
            if (me.selectCuentaContable != null) {
                me.cuentaContableId = me.selectCuentaContable.id
            } 
        },
        selectTrabajador: function() {
            let me = this;
            if (me.selectTrabajador != null) {
                me.trabajadorId = me.selectTrabajador.id
            } 
        },
    },
    methods: {
        async buscarPersonas () {
            let me = this;
            let response = await axios({
                method: "POST",
                url: '/cuentaxcobrar/buscarpersonas',
                data: {
                    query: me.query,
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
                    query: me.query3,
                    tipo: 'P'
                },
            });
            me.proveedores = response.data.personas;
            //   me.clienteId = 0;
        },
        async buscarCuentas () {
            let me = this;
            let response = await axios({
                method: "POST",
                url: '/cuentaxcobrar/buscarcuentas',
                data: {
                    query: me.query2,
                    tipo: '1',
                    ref: me.clienteId
                },
            });
            me.cuentas = response.data.cuentas;
            // console.log('....');
            //   me.clienteId = 0;
        },
        async buscarCuentasP () {
            let me = this;
            let response = await axios({
                method: "POST",
                url: '/cuentaxcobrar/buscarcuentas',
                data: {
                    query: me.query4,
                    tipo: '2',
                    ref: me.proveedorId
                },
            });
            me.cuentas = response.data.cuentas;
            //   me.clienteId = 0;
        },
        async buscarTrabajador () {
            let me = this;
            let response = await axios({
                method: "POST",
                url: '/reclamo/personal',
                data: {
                    query: me.query6
                },
            });
            me.trabajadores = response.data.personal;
            //   me.clienteId = 0;
        },
        async buscarCuentasContable () {
            let me = this;
            let response = await axios({
                method: "POST",
                url: '/cuentaxcobrar/buscarcuentascontables',
                data: {
                    query: me.query5
                },
            });
            me.cuentasContables = response.data.cuentascontables;
            //   me.clienteId = 0;
        },
        eliminarPersona (){
            this.selectPersona=null
            this.clienteId = 0
            this.referencia = ''
        },
        eliminarProveedor (){
            this.selectProveedor=null
            this.proveedorId = 0
            this.referencia = ''
        },
        eliminarCuenta () {
            this.selectCuenta=null
            this.cuentaId = 0
            this.tipoDocId = 0
            this.monedaId = ''
            this.query2 = ''
            document.getElementById('serie_' + this.entidad).value = ''
            document.getElementById('numero_' + this.entidad).value = ''
            document.getElementById('total_' + this.entidad).value = ''
        },
        eliminarCuentaContable () {
            this.selectCuentaContable = null;
            this.cuentaContableId = 0;
        },
        eliminarTrabajador () {
            this.selectTrabajador = null;
            this.trabajadorId  = 0;
        },
        eliminarCuentaP () {
            this.selectCuentaP=null
            this.cuentaId = 0
            this.tipoDocId = 0
            this.monedaId = ''
            this.query4 = ''
            this.tipoGastoId = ''
            this.tipoEgId = ''
            this.unidadId = ''
            
            document.getElementById('serie_' + this.entidad).value = ''
            document.getElementById('numero_' + this.entidad).value = ''
            document.getElementById('total_' + this.entidad).value = ''
            document.getElementById('partida_' + this.entidad).value = ''
            document.getElementById('sustento_' + this.entidad).value = ''         
          
        },
        selectTipoOperacion (el) {
            this.tipoOperacionId = el.target.value;
        },
        selectTipoDocumento (el) {
            this.tipoDocId = el.target.value;
        },
        selectTipoDocumento2 (el) {
            this.tipoDocId = el.target.value;
        },
        selectTipoGasto (el) {
            this.tipoGastoId = el.target.value;
        },
        selectTipoEg(el) {
            this.tipoEgId = el.target.value;
        },
        selectTipoMoneda(el) {
            this.monedaId = el.target.value;
        },
        selectUnidadNegocio(el) {
            this.unidadId = el.target.value;
        },
        selectTipoIngreso (el) {
            this.tipoIngresoId = el.target.value;
            this.eliminarCuenta()
        },
        selectTipoEgreso (el) {
            this.tipoEgresoId = el.target.value;
            this.eliminarCuentaP()
        },
        selectMedioPago(el) {
            this.medioPagoId = el.target.value;
        },
        selectTipoPago (el) {
            this.tipoPagoId = el.target.value;
        },
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();
            $('#modalMovimiento').modal('toggle')
            $('#modalMovimiento').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (id) {
            let me = this
            document.getElementById('formEnvModal02').reset();
            me.cajaId = id
            me.isValidSession()
            me.tipoOperacionId = 'I'
            me.query2 = ''
            
            me.unidadId = ''
            me.tipoEgId = ''
            me.tipoPagoId = 'T'
            me.tipoGastoId = ''
            me.monedaId = ''
            me.tipoDocId = 0
            me.cargar = 'busquedaCaja'
            me.query2 = ''
            me.query3 = ''
            me.tipoOperacionId = 'I'
            me.entidad = 'mov_caja'
            me.query = ''
            me.query6 = ''
            me.query4 = ''
            me.query5 = ''
            me.selectCuentaContable = null
            me.selectPersona = null
            me.selectProveedor = null
            me.selectCuenta = null
            me.selectCuentaP = null
            me.selectTrabajador = null
            me.trabajadores = []
            me.personas = []
            me.proveedores = []
            me.cuentasContables = []
            me.cuentas = []
            me.clienteId = 0
            me.cuentaId = 0
            me.trabajadorId = 0
            me.cuentaContableId = 0
            me.proveedorId = 0
            me.tipoIngresoId = 'CC'
            me.tipoEgresoId = 'CP'
            me.referencia = ''
            document.getElementById('select_tipo_'+ me.entidad).value = 'CC';
            $('#modalMovimiento').css('z-index','-1')
            document.getElementById('data-error-modal-mov').innerHTML = ''
            // document.getElementById('descripcion').value = ''
            // document.getElementById('select_tipoperacion').value = ''
            // document.getElementById('select_tipopago').value = ''
            // document.getElementById('monto').value = ''
            
            
            $('#modalMovimiento').modal({backdrop: 'static', show: true,  keyboard: false})
            $('#modalMovimiento').css('z-index', '1500')
            $('.modal-backdrop').css('z-index','1')
            $('#header-sec').css('z-index','1')
            $('#aside-sec').css('z-index','1') 
        }
    },
    created () {
        let me  = this
    },
    activated: function () {
        let me = this
        me.isValidSession()
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        this.$route.meta.auth = localStorage.getItem('autenticado')
    
        // setTimeout(x => {
        //      // just watch out with going too fast !!!
        // }, 1000);

    },
    deactivated: function () {
    },
    mounted () {

    },
    beforeDestroy: function () {
        let me  = this
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal
    }
}
</script>
<style scoped>
.modal-header { 
    border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
    border-top: 1px solid #f2f2f2;
}
input[type="checkbox"] {
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
     border-radius:30%;
     vertical-align: middle;
     position: relative;
     top: -2px;

}

/* appearance for checked radiobutton */
input[type="checkbox"]:checked {
     background-color: teal;
     cursor: pointer;
}
.no-resize {
    resize: none;
}
</style>