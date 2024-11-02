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
                            <router-link tag="a" to="/cotizacionauto">Cotizaciones para Autos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Cotización
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Cotización para Auto</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="la la-money-check-alt"></i> Datos de la Cotización</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body"> 
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarcotizacionauto">
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'numero_'+entidad">Número <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'numero_'+entidad" class="form-control form-control-sm text-center"
                                                                 name="numero" readonly="" :value="numero">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'fecha_'+entidad">Fecha <span class="text-danger">*</span></label>
                                                                <input type="date" :id="'fecha_'+entidad" class="form-control form-control-sm text-center" 
                                                                name="fecha">
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'documento_'+entidad">Documento <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'documento_'+entidad" class="form-control form-control-sm" 
                                                                name="documento" maxlength="11" minlength="8" @keypress="soloNumeros($event)" @keyup.enter="busquedaCliente">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-2 col-xs-12">
                                                            <div class="content-buttons btn-group mt-1 py-md-3">
                                                                <button @click="busquedaCliente"  class="btn btn-sm btn-info" type="button" style="margin-top:10px;" 
                                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Buscar Cliente">
                                                                    <i class="mdi mdi-magnify icon-size"></i>
                                                                </button>
                                                                <button v-if="bandCliente02" @click="verCliente" title="Ver Cliente" class="btn btn-sm btn-warning" 
                                                                type="button" style="margin-top:10px;">
                                                                    <i class="mdi mdi-file-eye icon-size"></i>
                                                                </button>
                                                                <button v-if="bandCliente" @click="agregarCliente" title="Buscar Cliente" class="btn btn-sm btn-danger" 
                                                                type="button" style="margin-top:10px;">
                                                                    <i class="mdi mdi-plus icon-size"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'cliente_'+entidad">Cliente <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'cliente_'+entidad" class="form-control form-control-sm" 
                                                                name="cliente" maxlength="255" readonly="" @keypress="soloLetras($event)">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-5 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'tipoMoneda_'+entidad">Moneda <span class="text-danger">*</span></label>
                                                                <select name="tipoMoneda" :id="'tipoMoneda_'+entidad" @change="seleccionarMoneda" 
                                                                class="custom-select custom-select-sm">
                                                                    <option v-for="item in arrMonedas" :key="item.id" :value="item.id" 
                                                                        :selected="item.id == monedaId"> {{ item.nombre }}</option>
                                                                </select>
                                                                <!-- <input type="text" id="placa" class="form-control" name="placa" placeholder="ABC-123" maxlength="10"> -->
                                                            </div>
                                                        </div>
                                                      
                                                        

                                                        <!--<div class="col-md-8 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="observaciones">Observaciones <span class="text-danger">*</span></label>
                                                                <textarea id="observaciones" class="form-control no-resize" name="observaciones" maxlength="255" rows="3"></textarea>
                                                            </div>
                                                        </div>-->
                                                    </div>

                                                 
                                                    <div class="row">
                                                        <div class="col-md-12" :id="'data-cliente_'+entidad">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-md-7 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'descripcion_'+entidad">Auto <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'descripcion_'+entidad" class="form-control form-control-sm" 
                                                                name="descripcion" maxlength="255" @keyup.enter="cargarProductos">
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-2 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'cantidad_'+entidad">Cantidad <span class="text-danger">*</span></label>
                                                                <input type="number" :id="'cantidad_'+entidad" class="form-control form-control-sm text-right" 
                                                                name="cantidad" min="0.01" value="1" step="0.01" @keyup.enter="agregarDetalle">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2 col-xs-12">
                                                            <div class="content-buttons btn-group" style="margin-top:1.8rem !important;">
                                                                <button @click="agregarDetalle" title="Agregar Detalle" class="btn btn-sm btn-danger" 
                                                                type="button">
                                                                    <i class="mdi mdi-plus icon-size"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive px-1" style="width:98%;height:300px;margin 2rem auto;" id="tabla-cotizacion">
                                                                <table class="table table-bordered table-sm mb-0" id="tabla" style="border: 1px solid #fff;">
                                                                    <thead style="background: #275EE5; color: #fff;">
                                                                    <tr>
                                                                        <th class="text-center" style="vertical-align:middle;" rowspan="2">Tipo</th>
                                                                        <th class="text-center" style="vertical-align:middle;" rowspan="2">Descripción</th>
                                                                        <th class="text-center" colspan="2">Precio</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th class="text-center">S/ </th>
                                                                        <th class="text-center">$</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-if="!arrProducto.length">
                                                                        <td class="text-left text-danger" colspan="4" rowspan="2"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                                                        </tr>

                                                                        <tr v-for="(p, index) in arrProducto" :key="index" :class="p.stock == 0 && p.tipo != 'Servicio'?'table-danger':''">
                                                                            <td class="text-center" @click="capturarDato(p)"><strong v-text="p.tipo"></strong></td>
                                                                            <td class="text-center" v-text="p.descripcion" @click="capturarDato(p)"></td>
                                                                            <td class="text-right" style="width:30px;" v-text="p.precioS" @click="capturarDato(p)"></td>
                                                                            <td class="text-right" style="width:30px;" v-text="p.precioD" @click="capturarDato(p)"></td>
                                                                            <!--<td class="text-right" style="width:30px;" v-text="p.tiempo"></td>-->
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>     
                                                        </div>
                                                    </div>
                                                
                                                    <div class="row">
                                                        <div class="col-md-12" :id="'data-stock_'+entidad">
                                                        </div>
                                                    </div>
                                              
                                                </div>
                                            </div>
                                           
                                            <div class="row">
                                                <div class="col-md-3 my-1 col-xs-8">
                                                    <h5>Detalles de la Cotización</h5>
                                                </div> 
                                                <div class="col-md-2 col-xs-2 px-0">
                                                    <button type="button" @click="agregarDetalle" class="btn btn-danger btn-sm mx-0">
                                                        <i class="mdi mdi-plus icon-size"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <hr>
                                            
                                            <div class="row">
                                                <input type="hidden" name="listDetalles" :id="'listDetalles_'+entidad" :value="listDetalles.join(',')" />
                                                <input type="hidden" name="" :id="'select_tipo_operacion_'+entidad" value='C'>
                                                <input type="hidden" name="listProductos" :id="'listProductos_'+entidad" :value="listProductos.join(',')" />
                                                <input type="hidden" name="listServicios" :id="'listServicios_'+entidad" :value="listServicios.join(',')" />
                                                <input type="hidden" name="listAutos" :id="'listAutos_'+entidad" :value="listAutos.join(',')" />
                                                
                                                <div class="table-responsive px-1" id="tabla-cotizacion-crear" style="width:98%;height:350px;margin 2rem auto;">
                                                    <table class="table mb-0">
                                                        <thead class="table-info">
                                                            <tr>
                                                                <th class="text-left" style="width:20px;">#</th>
                                                                <th class="text-center" style="width:60px;">Cantidad</th>
                                                                <th class="text-center" style="width:280px;">Descripción</th>
                                                                <th class="text-center" style="width:50px;">Precio</th>
                                                                <th class="text-center" style="width:50px;">Sub Total</th>
                                                                <th class="text-center" style="width:30px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="height:250px;">
                                                            <tr v-for="(p, index) in detalles" :key="index">
                                                                <td style="width:20px;" class="text-left" v-text="((index+1)<10?'0'+(index+1):(index+1))" ></td>
                                                                <td style="width:60px;" class="text-center px-1">
                                                                    <input type="number" v-focus-on-create step="0.01" class="form-control form-control-sm text-right" :name="'txtcantidad'+p.id" :id="'txtcantidad'+p.id" :value="p.cantidad" min="0.01" @keyup.enter="calcularTotalItem(p.id)" @change="calcularTotalItem(p.id)"/>
                                                                </td>
                                                                <td style="width:280px;" class="text-center">
                                                                    <textarea type="text" style="resize:none;" class="form-control form-control-sm no-rezise" :name="'txtproducto'+p.id" :id="'txtproducto'+p.id" v-text="p.descripcion" @change="calcularTotalItem(p.id)"></textarea>
                                                                    <input type="hidden" :name="'productoid'+p.id" :id="'productoid'+p.id" :value="p.idP" />
                                                                    <input type="hidden" :name="'tipo'+p.id" :id="'tipo'+p.id" :value="p.tipo" />
                                                                </td>
                                                                <td style="width:50px;" class="text-center px-1">
                                                                    <input type="number" step="0.01" class="form-control form-control-sm text-right" :name="'txtprecio'+p.id" min="0" :id="'txtprecio'+p.id" :value="p.precio" 
                                                                    @keyup.enter="calcularTotalItem(p.id)" @change="calcularTotalItem(p.id)"/>
                                                                
                                                                    <input type="hidden" :name="'stock'+p.id" :id="'stock'+p.id" :value="p.stock" />
                                                                   
                                                                </td>

                                                                <td style="width:50px;" class="text-center px-1">
                                                                    <input type="text" class="form-control form-control-sm text-right" :name="'txtsubtototal'+p.id" :id="'txtsubtototal'+p.id" :value="Math.round((p.precio*p.cantidad)*100)/100" readonly=""/>
                                                                </td>
                                                                
                                                                <td style="width:30px;">
                                                                    <a href="javascript:void(0)" class="btn-danger btn-sm" @click="eliminar(p.idP, p.id, p.tipo)" title="Eliminar">
                                                                        <i class="mdi mdi-minus-thick icon-size"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot class="table-danger">
                                                            <tr>
                                                                <th colspan="2"></th>
                                                                <th>SUB TOTAL: <span v-text="(monedaSeleccionada=='D'?'$ ':'S/ ')"></span><span class="text-right" v-text="subtotal"></span><input type="hidden" name="subtotalDoc" :value="subtotal"></th>
                                                                <th>IGV: <span v-text="(monedaSeleccionada=='D'?'$ ':'S/ ')"></span><span class="text-right" v-text="igv"></span><input type="hidden" name="igvDoc" :value="igv"></th>
                                                                <th>TOTAL: <span v-text="(monedaSeleccionada=='D'?'$ ':'S/ ')"></span><span class="text-right" v-text="total"></span><input type="hidden" name="totalDoc" :value="total"></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>     
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" :id="'data-error_'+entidad">
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="form-actions ml-1">
                                            <button type="button" @click="enviarFormDetalles(url, entidad, 'COTA')" 
                                                :id="'btnEnvio_'+entidad" class="btn btn-sm btn-success">
                                                <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                            </button>
                                            <button type="button" @click="atras(url)" class="btn btn-sm btn-danger mr-1">
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
        <Cliente ref="cliente" :documento="this.documento"></Cliente>
    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Cliente from './../Venta/Cliente'

export default {
 name: 'MantenimientoCotizacionAuto',
 mixins: [misMixins],
 components: {
    Cliente
 },
 data () {
     return {
         accion: 'Registrar',
         url: '/cotizacionauto',
         numero: '',
         documento: '',
         bandCliente: false,
         bandCliente02: false,
         arrLocales: [],
         arrAlmacen: [],
         bandTienda: false,
         tiendaId: 0,
         almacenSeleccionado:'',
         almacenId: 0,
         detalles: [],
         total: 0,
         subtotal: 0,
         igv:0,
         listDetalles: [],
         listProductos: [],
         listServicios: [],
         listAutos: [],
         arrTipos: [],
         arrProducto: [],
         tipoProducto: '',
         producto: '',
         stockMax: 1,
         campoBloqueado: false,
         ultElemento: '',
         tiempo:'',
         productos: [],
         min:1,
         max:10000,
         entidad: 'cotizacion_auto',
         arrMonedas: [
            {id: 'D', nombre: 'Dólar'},
            {id: 'S', nombre: 'Soles'}
         ],
         monedaSeleccionada: 'S',
         is_igv: true,
         monedaId: 'S',
     }
 },
 methods: {
    seleccionarMoneda () {
        let me = this
        let moneda = document.getElementById('tipoMoneda_'+me.entidad).value
        me.monedaId = moneda
        me.monedaSeleccionada = moneda
    },
    async cargarDatos () {
      let me = this
      me.accion = 'Registrar'
      me.arrProducto = []
      let response = await axios.get('/obtenercorrelativocotizacion')
      me.numero = response.data.numero
      document.getElementById('documento_'+me.entidad).value = ''
    //   document.getElementById('placa').value = ''
    //   document.getElementById('vin').value = ''
      document.getElementById('descripcion_'+me.entidad).value = ''
      document.getElementById('data-stock_'+me.entidad).innerHTML =''
      document.getElementById('data-cliente_'+me.entidad).innerHTML =''
      document.getElementById('data-error_'+me.entidad).innerHTML =''
      
      document.getElementById('cliente_'+me.entidad).value = ''
      document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
    },
    async busquedaCliente () {
        let me =this
        let valorDoc = document.getElementById('documento_'+me.entidad).value
        if (valorDoc.length == 8 || valorDoc.length == 9 || valorDoc.length == 11) { 
        document.getElementById(`data-cliente_${me.entidad}`).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>'

        let response = await axios.get('/obtenercliente02/'+valorDoc)

        window.setTimeout( function () {
            if (response.data.estado) {
                let razonSocial = ''
                if (response.data.cliente.tipoDocumento == 'PJ') {
                    razonSocial = response.data.cliente.razonSocial
                } else {
                    razonSocial = response.data.cliente.apellidos + ' '+ response.data.cliente.nombres
                }
                document.getElementById('cliente_'+me.entidad).value = razonSocial
                document.getElementById('data-cliente_'+me.entidad).innerHTML = ''
                me.bandCliente02 = true
                me.bandCliente = false
                me.calcularTotal()
            } else {
                me.bandCliente02 = false
                me.bandCliente = true
                document.getElementById('cliente_'+me.entidad).value = ''
                document.getElementById('data-cliente_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cliente no Registrado, Antes de Seguir Regístrelo.</strong></a></div ></div>'
            }
        }, 500)
        } else {
             document.getElementById('data-cliente_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI/CE o RUC</strong></a></div ></div>'
        }
    },
    busquedaPersonal () {
        let me = this
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        me.$refs.busqueda.showModal()
    },
    async seleccionarProducto (item) {
        let me = this
        let arrVal = item.value.split('@')
        me.stockMax = arrVal[3]

        document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: '+me.stockMax+'</strong></a></div ></div>'

        window.setTimeout( function () {
            document.getElementById('cantidad_'+me.entidad).focus()
        }, 500)
        // document.getElementById('cantidad').focus()
    },
    async cargarProductos () {
        let me = this
        let descripcion = document.getElementById('descripcion_'+me.entidad).value
        let productos = await axios({ 
            method: 'post', 
            url: '/obtenerautos',
            data: {
                'descripcion': descripcion,
                '_token': this.$store.state.token
            }
        })
        me.arrProducto = productos.data.autos
        // console.log(me.arrProducto)
    },
    calcularTotalItem (id) {
        let me = this
        let cantidad = document.getElementById('txtcantidad'+id).value
        let precio  = document.getElementById('txtprecio'+id).value
        let descripcion =  document.getElementById('txtproducto'+id).value
        let stock = document.getElementById('stock'+id).value

        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        if (cantidad > parseFloat(stock)) {
            document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
        }

        me.detalles.forEach(element => {
            if (element.id == id) {
                element.cantidad = cantidad
                element.precio   = precio
                element.descripcion = descripcion
            }
        })
        // console.log(me.detalles)
        me.calcularTotal()
    
        // document.getElementById('txtsubtototal'+id).value = ((cantidad*precio)*1000)/1000
        // me.calcularTotal()
    },
    eliminar(idP,id, tipo) {
        let me = this
        // alert(tipo)
        if (tipo == 'Producto') {
            for(let j=0; j < me.listProductos.length; j++) {
                let el = me.listProductos[j]
                if (el == idP) {
                    me.listProductos.splice(j,1)
                }
            }
        } else if (tipo == 'Servicio') {
            for(let j=0; j < me.listServicios.length; j++) {
                let e = me.listServicios[j]
                if (e == idP) {
                    me.listServicios.splice(j,1)
                }
            }
        } else {
            for(let j=0; j < me.listAutos.length; j++) {
                let e = me.listAutos[j]
                if (e == idP) {
                    me.listAutos.splice(j,1)
                }
            }
        }
    
        
        for(let i=0; i< me.detalles.length; i++) {
            let element = me.detalles[i]
            if (element.id == id) {
                if (element.tipo == tipo) {
                    me.detalles.splice(i,1)
                    me.listDetalles.splice(i,1)
                }
            }
        }

       
        // console.log(me.listProductos)
        // if (me.listDetalles.length == 0) {
        //     me.campoBloqueado = false
        // } else {
        //     me.campoBloqueado = true
        // }
        me.calcularTotal()
        // me.calcularTiempo()
        // me.detalles.forEach(element => {
        //     if (element.id == id) {
        //         me.detalles.slice
        //     }
        // })
    },
    calcularTotal () {
        let me = this
        let acum = 0
        me.detalles.forEach(element => {
            acum+=element.cantidad * element.precio
        })
        me.total = (parseFloat(acum)*1000)/1000
        me.total = Math.round(me.total*100)/100
      
        let doc = document.getElementById('documento_'+me.entidad).value
        if (doc.length == 11 && me.is_igv) {
            me.subtotal = Math.round((parseFloat(me.total/1.18)*100))/100
            me.igv = Math.round((parseFloat(me.total - me.subtotal)*100))/100
        } else {
            me.igv = 0
            me.subtotal = me.total
        }
    },
    buscarInArray (id, tipo) {
        let me = this
        let band = true
        if (tipo == 'Auto') {
            if (me.listAutos.indexOf(id) != -1) {
                band = false
            }
        } else if (tipo == 'Producto') {
            if (me.listProductos.indexOf(id) != -1) {
                band = false
            }
        } else {
            if (me.listServicios.indexOf(id) != -1) {
                band = false
            }
        }

        return band
    },
    capturarDato (ref) {
        let me = this
        let element = document.getElementById('cantidad_'+me.entidad)
        // alert(ref.nombre)
        let rpta = me.buscarInArray(ref.id, ref.tipo)
        document.getElementById(`data-stock_${me.entidad}`).innerHTML = ''
        me.stockMax = ref.stock
        me.elementoSeleccionado = ref
        if (rpta) {
            if (ref.tipo != 'Servicio') {
                document.getElementById(`data-stock_${me.entidad}`).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: '+me.stockMax+'</strong></a></div ></div>'
            } else {
                document.getElementById(`data-stock_${me.entidad}`).innerHTML = '<div style = "margin-top:10px;"><div class="alert   alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Tiempo Estimado: '+ref.tiempo+'</strong></a></div ></div>'
            }
        } else {
            document.getElementById(`data-stock_${me.entidad}`).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto/Servicio ya antes Agregado</strong></a></div ></div>'
        }

        element.focus()

    },
    tipoVenta () {
        // let me = this
        // let val = document.getElementById('select_tipodocumento').value
        // let element = document.getElementById('documento')
        // if (val == 'F') {
        //     element.setAttribute('minlength','11')
        //     element.setAttribute('maxlength','11')

        // } else {
        //     element.setAttribute('minlength','8')
        //     element.setAttribute('maxlength','8')

        //     me.igv = 0
        //     me.subtotal = me.total
        // }
        // element.value = ''
        // document.getElementById('cliente').value = ''
        // element.focus()
    },
    agregarCliente () {
        let me = this
        me.isValidSession()
        
        let doc = document.getElementById('documento_'+me.entidad).value
        me.documento  = doc
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        // alert(this.$store.state.mostrarModal)
        me.$refs.cliente.showModal(doc,1)
    },
    verCliente () {
        let me = this
        me.isValidSession()
        
        let doc = document.getElementById('documento_'+me.entidad).value
        me.documento  = doc
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        me.$refs.cliente.showModal(doc,2)

   
        // myWindow = window.open("/cliente/crear/"+seleccionado.trim(), "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=500,height=1200");
        // myWindow.focus();
    },
    generarCorrelativo () {
        let me = this
        let numero = me.getRandomNumber(me.min, me.max)
        let idx = me.detalles.indexOf(numero)
        do {
            if (idx != -1) {
                numero = me.getRandomNumber(me.min, me.max)
                idx = me.detalles.indexOf(numero)
            }
        } while (idx!=-1)

        return numero
    },
    agregarDetalle() {
        let me = this
        let ref = me.elementoSeleccionado
        let id_aleatorio = me.generarCorrelativo() // me.generarAleatorio(me.min, me.max, me.listDetalles) 
  
        // let rpta = me.buscarInArray(ref.id, ref.tipo)

        let cantidad = document.getElementById('cantidad_'+me.entidad).value
        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        let band = false
        // if (rpta) {
        if (cantidad > parseFloat(me.stockMax)) {
            document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
        } 

        var elemento = null
        if (ref != null) {
            elemento = { id: id_aleatorio, tipo: 'Auto', precio:ref.precioS, 
            descripcion: ref.descripcionadicional, stock: ref.stock, cantidad: cantidad, stockMax: me.stockMax, 
            tiempo: ref.tiempo, idP: ref.id }
        } else {
            elemento = {id: id_aleatorio, tipo: 'Detalle', precio:'0', 
            descripcion: '', stock: 10000, cantidad: 1, stockMax: 10000, tiempo: 0,idP: id_aleatorio}
        }

        // me.listDetalles.push(ref.id)
        band = true
            
        if (band) {
            me.detalles.push(elemento)
            me.listDetalles.push(elemento.id)
            me.elementoSeleccionado = null
            me.ultElemento = ''
            me.producto = ''
            me.campoBloqueado = true
            document.getElementById('cantidad_'+me.entidad).value = 1
            me.calcularTotal()
            // me.calcularTiempo()
        }
        // } else {
        //     document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto/Servicio ya antes Agregado</strong></a></div ></div>'
        // }
        // console.log(me.listDetalles)
    }
 },
 created () {
    this.$on('buscarCliente', function () {
      this.busquedaCliente()
    })
 },
 activated: async function () {
    let me = this
    me.is_igv = await me.isIgv()
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    me.bandCliente =  false
    me.bandCliente02 =  false
    me.bandTienda =  false
    me.campoBloqueado =  false
    me.detalles = []
    me.listDetalles = []
    me.tiendaSeleccionada = {}
  
    // document.getElementById('select_tipodocumento').val = ''
    // document.getElementById('select_tipo_operacion').val = ''
    me.cargarDatos()
    me.calcularTotal()
    // me.calcularTiempo()
 },
 mounted () {
    
 },
 beforeDestroy: function () {
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
     margin: 0 .25em 0 0;
     padding: 0;
     vertical-align: top;
     width: 16px;
     border-radius:30%;
}
input[type="checkbox"]:checked + label:before {
     background: teal;
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

/* .no-rezise {
    rezise:none;
} */

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
</style>