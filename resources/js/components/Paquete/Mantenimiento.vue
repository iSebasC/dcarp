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
                            <router-link tag="a" to="/paquete">Planes de Mantenimientos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Plan de Mantenimiento
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Plan de Mantenimiento</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height"> 
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="la la-dungeon"></i> Datos del Plan de Mantenimiento</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" name="formEv" :id="'formEnv_'+entidad" method="POST" action="/guardarpaquete" encyte="multipart/form-data">
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'nombre_'+entidad">Nombre <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'nombre_'+entidad" class="form-control form-control-sm text-left" name="nombre">
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <label :for="'select_marca_'+entidad">Marca <span class="text-danger">*</span></label>
                                                            <select class="custom-select custom-select-sm" name="select_marca" :id="'select_marca_'+entidad" @change="agregarMarca">
                                                                <option disabled="" value="" selected="">Seleccione</option>
                                                                <option v-for="f in marcas" :selected="marcaAutoId==f.id" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                                <option value="otro">Indique otra</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 col-xs-12 ocultar" :id="'marca_ocultar_'+entidad">
                                                            <div class="form-group">
                                                                <label :for="'marca_'+entidad">Marca <small class="text-muted">(Nuevo)</small><span class="text-danger">*</span></label>
                                                                <input type="text" :id="'marca_'+entidad" class="form-control form-control-sm" name="marca" maxlength="255">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-md-10 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'modelo_'+entidad">Modelo <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'modelo_'+entidad" class="form-control form-control-sm" name="modelo" maxlength="255">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row mt-2">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'kilometraje_'+entidad">Kilometraje <span class="text-danger">*</span></label>
                                                                <input type="number" :id="'kilometraje_'+entidad" class="form-control form-control-sm text-center" name="kilometraje"  
                                                                step="0.1" min="0">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'tiempo_'+entidad">Tiempo Aprox. <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'tiempo_'+entidad" class="form-control form-control-sm text-center" name="tiempo" 
                                                                maxlength="255" :value="tiempo" readonly="" @keypress="soloNumeros($event)">
                                                            </div> 
                                                        </div>
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
                                                                <label :for="'descripcion_'+entidad">Producto/Servicio <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'descripcion_'+entidad" class="form-control form-control-sm" name="descripcion" 
                                                                maxlength="255" @keyup.enter="cargarProductos" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-3 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'cantidad_'+entidad">Cantidad <span class="text-danger">*</span></label>
                                                                <input type="number" :id="'cantidad_'+entidad" class="form-control form-control-sm text-center" 
                                                                name="cantidad" step="0.01" min="0.01" value="1" @keyup.enter="agregarDetalle">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2 col-xs-12">
                                                            <div class="content-buttons btn-group pt-1 my-md-3">
                                                                <button @click="agregarDetalle" title="Agregar Detalle" class="btn btn-sm btn-danger" type="button" style="margin-top:10px;">
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
                                                                            <td class="text-center" v-text="(p.nombre != ''?(p.nombre+(p.medida != '-'?', Medida: '+p.medida:'')+(p.tipollanta != '-'?', Tipo de Llanta: '+p.tipollanta:'')+(p.modelo != '-'?', Modelo: '+p.modelo:'')+(p.sistema != '-'?' , Sistema: '+p.sistema:'')):p.nombre2)" @click="capturarDato(p)"></td>
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
                                            <hr>
                                           
                                            <div class="row d-flex justify-content-center">
                                                <input type="hidden" name="listDetalles" :id="'listDetalles_'+entidad" :value="listDetalles.join(',')" />
                                                <input type="hidden" name="listProductos" :id="'listProductos_'+entidad" :value="listProductos.join(',')" />
                                                <input type="hidden" name="listServicios" :id="'listServicios_'+entidad" :value="listServicios.join(',')" />
                                       
                                                <div class="table-responsive px-1" id="tabla-paquete-crear" style="width:98%;height:350px;margin 2rem auto;">
                                                    <table class="table mb-0">
                                                        <thead class="table-info">
                                                            <tr>
                                                                <th class="text-left" style="width:20px;">#</th>
                                                                <th class="text-center" style="width:60px;">Cantidad</th>
                                                                <th class="text-center" style="width:280px;">Producto/Servicio</th>
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
                                                                    <input type="hidden" :name="'lote'+p.id" :id="'lote'+p.id" :value="p.lote" />
                                                                </td>
                                                                <td style="width:50px;" class="text-center px-1">
                                                                    <input type="number" step="0.01" class="form-control form-control-sm text-center" :name="'txtprecio'+p.id" min="0" :id="'txtprecio'+p.id" :value="p.precio" @keyup.enter="calcularTotalItem(p.id)" @change="calcularTotalItem(p.id)"/>
                                                                
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
                                                                <th>SUB TOTAL: S/ <span class="text-right" v-text="subtotal"></span><input type="hidden" name="subtotalDoc" :value="subtotal"></th>
                                                                <th>IGV: S/ <span class="text-right" v-text="igv"></span><input type="hidden" name="igvDoc" :value="igv"></th>
                                                                <th>TOTAL: S/ <span class="text-right" v-text="total"></span><input type="hidden" name="totalDoc" :value="total"></th>
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
                                            <button type="button" @click="enviarFormDetalles(url, entidad, 'PAQ')" :id="'btnEnvio_'+entidad" class="btn btn-sm btn-success">
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
     
    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'

export default {
 name: 'MantenimientoOrden',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/paquete',
         numero: '',
         documento: '',
         bandCliente02: false,
         detalles: [],
         total: 0,
         subtotal: 0,
         igv: 0,
         listDetalles: [],
         listProductos: [],
         listServicios: [],
         listAutos: [],
         arrTipos: [],
         arrOrdenes: [],
         arrCitas: [],
         tipoProducto: '',
         producto: '',
         placaSeleccionada: '',
         clienteSeleccionado: '',
         citaSeleccionada: '',
         campoBloqueado: false,
         ultElemento: '',
         min:1,
         elementoSeleccionado: {},
         marcas: [],
         arrProducto:[],
         entidad: 'paquete_mant',
         tiempo: '',
         min: 1,
         max: 99999
     }
 },
 methods: {
    async cargarDatos () {
      let me = this
      me.accion = 'Registrar'
      me.arrOrdenes = []
 
      document.getElementById('descripcion_'+me.entidad).value  = ''
      document.getElementById('nombre_'+me.entidad).value       = ''
      document.getElementById('select_marca_'+me.entidad).value = ''
      document.getElementById('marca_'+me.entidad).value       = ''
      document.getElementById('modelo_'+me.entidad).value      = ''
      document.getElementById('kilometraje_'+me.entidad).value = ''
    },
    agregarMarca () {
        let me = this
        let val = document.getElementById('select_marca_'+me.entidad).value
        let element =  document.getElementById('marca_ocultar_'+me.entidad)
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            // document.getElementById('modelo_'+me.entidad).focus()
        }
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
        // console.log("ref", ref);
        let rpta = me.buscarInArray(ref.id, ref.tipo)

        let cantidad = document.getElementById('cantidad_'+me.entidad).value
        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        let band = false
        if (rpta) {
            if (ref.tipo != 'Auto') {
                
                if (cantidad > parseFloat(me.stockMax)) {
                    document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cantidad Indicada No Disponible</strong></a></div ></div>'
                } 

                let descripcion = null;
                if (ref.tipo == 'Servicio') {
                    descripcion = ref.nombre+(ref.medida != '-'?', Medida: '+ref.medida:'')+(ref.tipollanta != '-'?', Tipo de Llanta: '+ref.tipollanta:'')+(ref.modelo != '-'?', Modelo: '+ref.modelo:'')+(ref.sistema != '-'?' , Sistema: '+ref.sistema:'')
                } else {
                    descripcion = ref.desc_corta;
                }

                if (descripcion == '' || descripcion == null) {
                    descripcion = ref.nombre2
                }
                
                
                let unidad = ref.tiempo.split(' ')
                let consta = 1
                if (unidad[1] == 'hr') {
                    consta = 60;
                } else if (unidad[1] == 'min') {
                    consta = 1;
                } else if (unidad[1] == 'sem') {
                    consta = 60*7;
                } else {
                    consta = 30*60*7;
                }

                let tot = parseFloat(unidad[0] * consta)


                var elemento = { id: me.generarCorrelativo(), tipo: ref.tipo, precio:ref.precioS, descripcion: descripcion, 
                stock: ref.stock, cantidad: cantidad, stockMax: me.stockMax, tiempo: (ref.tipo=='Servicio'?tot:'-'),idP: ref.id, lote: ref.lote_id  }
            
                if (ref.tipo == 'Producto') {
                    me.listProductos.push(ref.id)
                } else {
                    me.listServicios.push(ref.id)
                }
                band = true
                
            } 
            /*
            else {
                let descripcion = ref.nombre+(ref.medida != '-'?', Medida: '+ref.medida:'')+(ref.tipollanta != '-'?', Tipo de Llanta: '+ref.tipollanta:'')+(ref.modelo != '-'?', Modelo: '+ref.modelo:'')+(ref.sistema != '-'?' , Sistema: '+ref.sistema:'')


                let unidad = ref.tiempo.split(' ')
                let consta = 1
                if (unidad[1] == 'hr') {
                    consta = 60;
                } else if (unidad[1] == 'min') {
                    consta = 1;
                } else if (unidad[1] == 'sem') {
                    consta = 60*7;
                } else {
                    consta = 30*60*7;
                }

                let tot = parseFloat(unidad[0] * consta)

                var elemento = { id: me.generarCorrelativo(), tipo: ref.tipo, precio:ref.precioS, descripcion: descripcion, stock: cantidad, 
                cantidad: cantidad, stockMax: cantidad, tiempo: tot, idP: ref.id }
            
                console.log(elemento);
                me.listServicios.push(ref.id)
                band = true
            }
            */
            if (band) {
                me.detalles.push(elemento)
                me.listDetalles.push(elemento.id)
                me.ultElemento = ref.id
                me.producto = ''
                me.campoBloqueado = true
                document.getElementById('cantidad_'+me.entidad).value = 1
                me.calcularTotal()
                me.calcularTiempo()
            }
        } else {
            document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto/Servicio ya antes Agregado</strong></a></div ></div>'
        }
        // console.log(me.listDetalles)
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
        me.calcularTiempo()
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
  
        me.igv = 0
        me.subtotal = me.total
  
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
    buscarInArray (id) {
        let me = this
        let band = true
        if (me.listDetalles.indexOf(id) != -1) {
            band = false
        }

        return band
    },
    capturarDato (ref) {
        let me = this
        // console.log("ref", ref);
        // alert(ref.nombre)
        let rpta = me.buscarInArray(ref.id)
        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        me.elementoSeleccionado = ref
        me.stockMax = ref.stock
        if (!rpta) {
            document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Producto ya antes Agregado</strong></a></div ></div>'
            // let element = document.getElementById('txtnumero'+ref.id)
            // element.focus()
        } else {
            document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Stock Disponible: '+me.stockMax+'</strong></a></div ></div>'
            window.setTimeout(function() {
                document.getElementById('cantidad_'+me.entidad).focus();
            }, 800);
        }


        // if (rpta) {
        //     let elemento = {'id': ref.id, 'numero': ref.numero, 'total': ref.total, 'total2': ref.total2}
        //     me.detalles.push(elemento)
        //     me.listDetalles.push(elemento.id)
        //     // console.log(elemento)
        //     // console.log(me.detalles)
        //     me.calcularTotal()
        // }
    },
    async cargarProductos () {
        let me = this
        let descripcion = document.getElementById('descripcion_'+me.entidad).value
        let productos = await axios({ 
            method: 'post', 
            url: '/obtenerproductos',
            data: {
                'descripcion': descripcion,
                '_token': this.$store.state.token
            }
        })
        me.arrProducto = productos.data.productos
        // console.log(me.arrProducto)
    },
    calcularTiempo () {
        let me = this
        let acum = 0
        me.detalles.forEach(element => {
            if (element.tiempo != '-')
                acum+=element.tiempo
        })

        // console.log("acum: "+acum);
        if (acum >= 60) {
            let hr = Math.trunc(acum/60)
            let min = 0
            if (hr > 0) {
                min = acum - (hr * 60)
            }    
            me.tiempo = (hr>0?(hr<10?'0'+hr:hr)+' Hr ':'')+ (min>0?(min<10?'0'+min:min)+' min.':'')
        } else {
            me.tiempo = acum + ' min.'
        }
    }
},
activated: function () {
    let me = this
    me.isValidSession()
    
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')
    
    me.bandCliente02 =  false
    me.detalles = []
    me.listDetalles = []
   
    me.cargarDatos()
    me.calcularTotal()
 },
 mounted () {
    
 },
 beforeDestroy: function () {
    let me =this
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

.no-rezise {
    resize:none;
}

#tabla-cita {
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