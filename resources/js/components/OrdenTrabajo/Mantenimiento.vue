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
                            <router-link tag="a" to="/ordentrabajo">Órdenes de Trabajo</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Orden de Trabajo
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Orden de Trabajo</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height"> 
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="la la-dungeon"></i> Datos de la Orden</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" name="formEv" :id="'formEnv_'+entidad" method="POST" action="/guardarorden" encyte="multipart/form-data">
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
                                                                <input type="date" :id="'fecha_'+entidad" class="form-control form-control-sm text-center" name="fecha">
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="row">
                                                        <div class="col-md-6 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'documento_'+entidad">Documento <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'documento_'+entidad" class="form-control form-control-sm" name="documento" maxlength="11" minlength="8" @keypress="soloNumeros($event)" @keyup.enter="busquedaCliente">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-2 col-xs-12">
                                                            <div class="content-buttons btn-group my-md-4">
                                                                <button @click="busquedaCliente" title="Buscar Cliente" class="btn btn-sm btn-info" 
                                                                type="button" style="margin-top:5px;">
                                                                    <i class="mdi mdi-magnify icon-size"></i>
                                                                </button>
                                                                <button v-if="bandCliente02" @click="verCliente" title="Ver Cliente" class="btn btn-sm btn-warning" 
                                                                type="button" style="margin-top:5px;">
                                                                    <i class="mdi mdi-file-eye icon-size"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'cliente_'+entidad">Cliente <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'cliente_'+entidad" class="form-control form-control-sm" name="cliente" 
                                                                maxlength="255" readonly="" @keypress="soloLetras($event)">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label :for="'placa_'+entidad">Placa <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'placa_'+entidad" class="form-control form-control-sm" name="placa" readonly="" :value="placaSeleccionada">
                                                                <input type="hidden" :id="'idCliente_'+entidad"  name="idCliente" :value="clienteSeleccionado">
                                                                <input type="hidden" :id="'idCita_'+entidad" name="idCita" :value="citaSeleccionada">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-7 my-1">
                                                            <div class="form-group my-4">
                                                                <button type="button" @click="llenarCheckList()" title="Llenar CheckList de Inventario" 
                                                                class="btn btn-sm btn-danger btn-outline" id="checklist"><i class="mdi mdi-upload-circle icon-size"></i></button>
                                                                <!--<input type="file" id="checklist" class="form-control" name="checklist" accept="image/x-png,image/jpeg">-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!--<div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">-->
                                                                <!--<label for="checklist">Checklist <span class="text-danger">*</span></label>
                                                                <button type="button" class="btn btn-danger btn-outline" id="checklist">Ver Checklist</button>-->
                                                                <!--<input type="file" id="checklist" class="form-control" name="checklist" accept="image/x-png,image/jpeg">-->
                                                           <!-- </div>
                                                        </div>
                                                    </div>-->
                                                    
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 px-0">
                                                            <small class="ml-3"><strong>Seleccione Cita <span class="text-danger">*</span></strong></small>
                                                            <div class="table-responsive" style="width:100%;height:150px;" id="tabla-cita">
                                                                <table class="table table-bordered table-sm mb-0" id="tabla" style="border: 1px solid #fff;">
                                                                    <thead style="background: #275EE5; color: #fff;">
                                                                        <tr>
                                                                            <th class="text-center">Número</th>
                                                                            <th class="text-center">Placa</th>
                                                                            <th class="text-center">Fecha</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-if="!arrCitas.length">
                                                                        <td class="text-left text-danger" colspan="3"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                                                        </tr>

                                                                        <tr v-for="(p, index) in arrCitas" :key="index">
                                                                            <td class="text-center" @click="capturarCita(p)"><strong v-text="p.numero"></strong></td>
                                                                            <td class="text-center" v-text="p.descripcion" @click="capturarCita(p)"></td>
                                                                            <td class="text-center" v-text="p.fecha" @click="capturarCita(p)"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
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
                                                        <div class="col-md-8 ml-1 col-xs-12">
                                                            <div class="form-group">
                                                                <label :for="'descripcion_'+entidad">Nro de Cotización <span class="text-danger">*</span></label>
                                                                <input type="text" :id="'descripcion_'+entidad" class="form-control form-control-sm" 
                                                                name="descripcion"  placeholder="1-8596" @keypress="soloNumerosGuion($event)" 
                                                                @keyup.enter="cargarOrdenes" maxlength="13">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive px-1" style="width:98%;height:300px;margin 2rem auto;" id="tabla-orden">
                                                                <table class="table table-bordered table-sm mb-0" id="tabla" style="border: 1px solid #fff;">
                                                                    <thead style="background: #081331; color: #fff;">
                                                                        <tr>
                                                                            <th class="text-center">Número</th>
                                                                            <th class="text-center">Total</th>
                                                                            <th class="text-center">Fecha</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-if="!arrOrdenes.length">
                                                                        <td class="text-left text-danger" colspan="3" rowspan="2"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                                                        </tr>

                                                                        <tr v-for="(p, index) in arrOrdenes" :key="index">
                                                                            <td class="text-center" v-text="p.numero" @click="capturarDato(p)"></td>
                                                                            <td class="text-right" style="width:30px;" v-text="p.total2" @click="capturarDato(p)"></td>
                                                                            <td class="text-right" style="width:30px;" v-text="p.fecha" @click="capturarDato(p)"></td>
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
                                                <input type="hidden" name="listDetalles" id="listDetalles" :value="listDetalles.join(',')" />
                                                <input type="hidden" name="listItemCheckList" id="listItemCheckList" />
                                                
                                                <div class="table-responsive px-1" id="tabla-orden-crear" style="width:80%;height:350px;margin 2rem auto;">
                                                    <table class="table mb-0 mx-auto">
                                                        <thead class="table-info">
                                                            <tr>
                                                                <th colspan="4" class="text-center">Cotizaciones Agregadas</th>
                                                            </tr>
                                                            <tr>
                                                                <th class="text-left" style="width:20px;">#</th>
                                                                <th class="text-center" style="width:100px;">N° de Cotización</th>
                                                                <th class="text-center" style="width:50px;">Sub Total</th>
                                                                <th class="text-center" style="width:30px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="height:250px;">
                                                            <tr v-for="(p, index) in detalles" :key="index">
                                                                <td style="width:20px;" class="text-left" v-text="((index+1)<10?'0'+(index+1):(index+1))" ></td>
                                                                
                                                                <td class="text-center" style="width:100px;">
                                                                    <input type="text" class="form-control form-control-sm text-center" readonly="" :name="'txtnumero'+p.id" :id="'txtprecio'+p.id" :value="p.numero" />
                                                                </td>
                                                                
                                                                <td style="width:50px;" class="text-center px-1">
                                                                    <input type="text" class="form-control form-control-sm text-center" :name="'txtsubtototal'+p.id" :id="'txtsubtototal'+p.id" :value="p.total2" readonly=""/>

                                                                    <input type="hidden" :name="'idcotizacion'+p.id" :value="p.id"/>
                                                              
                                                                </td>
                                                                
                                                                <td style="width:30px;">
                                                                    <a href="javascript:void(0)" class="btn-danger btn-sm" @click="eliminar(p.id)" 
                                                                    title="Eliminar">
                                                                        <i class="mdi mdi-minus-thick icon-size"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot class="table-danger">
                                                            <tr>
                                                                <th colspan="3"></th>
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
                                            <button type="button" @click="enviarForm(url,entidad)" :id="'btnEnvio_'+entidad" class="btn btn-sm btn-success">
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
        <CheckList ref="checklist"></CheckList>

    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import CheckList from './CheckListInventario'

export default {
 name: 'MantenimientoOrden',
 mixins: [misMixins],
 components: {
    CheckList
 },
 data () {
     return {
         accion: 'Registrar',
         url: '/ordentrabajo',
         numero: '',
         documento: '',
         bandCliente02: false,
         detalles: [],
         total: 0,
         listDetalles: [],
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
         entidad: 'orden_mant',
         elementoSeleccionado: {},
     }
 },
 methods: {
    async cargarDatos () {
      let me = this
      me.accion = 'Registrar'
      me.arrOrdenes = []
      me.arrCitas = []
      let response = await axios.get('/obtenercorrelativoorden')
      me.numero = response.data.numero
      document.getElementById('descripcion_'+me.entidad).value = ''
      document.getElementById('documento_'+me.entidad).value = ''
      document.getElementById('placa_'+me.entidad).value = ''
      document.getElementById('cliente_'+me.entidad).value = ''
      document.getElementById('fecha_'+me.entidad).value = me.obtenerFechaActual()
    },
    async capturarCita (p) {
        let me = this
        me.placaSeleccionada = p.placa
        me.citaSeleccionada = p.id
        me.cargarOrdenes()
    }, 
    async buscarCitas (valor) {
        let me = this
        let response2 = await axios.get('/buscarcitas/'+valor)
        me.clienteSeleccionado = valor
        me.arrCitas =  response2.data.citas
    },
    async busquedaCliente () {
        let me =this
        let valorDoc = document.getElementById('documento_'+me.entidad).value
        if (valorDoc.length == 8 || valorDoc.length == 9 || valorDoc.length == 11) { 
            document.getElementById('data-cliente_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Espere...</strong></a></div ></div>'

            let response = await axios.get('/obtenercliente02/'+valorDoc)

            window.setTimeout( function () {
                if (response.data.estado) {
                    let razonSocial = ''
                    if (response.data.cliente.tipoDocumento == 'PJ') {
                        razonSocial = response.data.cliente.razonSocial
                    } else {
                        razonSocial = response.data.cliente.apellidos + ' '+ response.data.cliente.nombres
                    }
                    let id  =  response.data.cliente.id
                    document.getElementById('cliente_'+me.entidad).value = razonSocial
                    document.getElementById('data-cliente_'+me.entidad).innerHTML = ''
                    me.bandCliente02 = true
                    me.buscarCitas(id)
                    me.calcularTotal()

                } else {
                    me.bandCliente02 = false
                    document.getElementById('cliente_'+me.entidad).value = ''
                    document.getElementById('data-cliente_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cliente no Registrado, Antes de Seguir Regístrelo.</strong></a></div ></div>'
                }
            }, 500)
        } else {
             document.getElementById('data-cliente_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>El Documento debe ser DNI/CE o RUC</strong></a></div ></div>'
        }
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
    async cargarOrdenes () {
        let me = this
        let descripcion = document.getElementById('descripcion_'+me.entidad).value
        let cotizaciones = await axios({
            method: 'post',
            url: '/buscarcotizaciones', 
            data: {
                'placa': me.placaSeleccionada,
                'descripcion': descripcion,
                'clienteId': me.clienteSeleccionado,
                '_token': this.$store.state.token
            }
        })
        me.arrOrdenes = cotizaciones.data.cotizaciones
    },
    eliminar(id) {
        let me = this
        
        for(let i=0; i< me.detalles.length; i++) {
            let element = me.detalles[i]
            if (element.id == id) {
                me.detalles.splice(i,1)
                me.listDetalles.splice(i,1)
            }
        }
        me.calcularTotal()
    },
    calcularTotal () {
        let me = this
        let acum = 0
        me.detalles.forEach(element => {
            acum+=parseFloat(element.total)
        })
        me.total = Math.round(acum*100)/100
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
        // alert(ref.nombre)
        let rpta = me.buscarInArray(ref.id)
        document.getElementById('data-stock_'+me.entidad).innerHTML = ''
        me.elementoSeleccionado = ref
        if (!rpta) {
            document.getElementById('data-stock_'+me.entidad).innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cotización ya antes Agregada</strong></a></div ></div>'

            // let element = document.getElementById('txtnumero'+ref.id)
            // element.focus()
        }

        if (rpta) {
            // console.log('ref', ref)
            let elemento = {'id': ref.id, 'numero': ref.numero, 'total': ref.total, 'total2': ref.total2}
            me.detalles.push(elemento)
            me.listDetalles.push(elemento.id)
            // console.log(elemento)
            // console.log(me.detalles)
            me.calcularTotal()
        }
    
    },
    verCliente () {
        let me = this
        me.isValidSession()
        let doc = document.getElementById('documento_'+me.entidad).value
        me.documento  = doc
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        me.$refs.cliente.showModal(doc,2)
    },
    llenarCheckList () {
        let me = this
        me.isValidSession()
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
        me.$refs.checklist.showModal()
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
    me.clienteSeleccionado = ''
    me.placaSeleccionada = ''
    me.cargarDatos()
    me.calcularTotal()
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