<template>
    <div class="modal fade" id="modalDetallesCotizacion" tabindex="-1" role="dialog" aria-labelledby="modalDetallesCotizacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form class="form" id="formEnv" method="POST" action="/agregarcotizacion">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetallesCotizacionLabel">Detalles de Orden: <strong v-text="this.$attrs.documento"></strong></h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5 col-xs-12">
                                <div class="row ml-1">
                                    <input type="hidden" :value="id" name="id">
                                    <input type="hidden" :value="clienteId" name="clienteId">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="cliente">Cliente <span class="text-danger">*</span></label>
                                            <input type="text" id="cliente" :value="cliente" class="form-control form-control-sm" name="cliente" readonly="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="placa">Placa <span class="text-danger">*</span></label>
                                            <input type="text" id="placa" :value="placa" class="form-control form-control-sm" name="placa" readonly="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 col-xs-12">
                                <div class="row">
                                    <div class="col-md-8 ml-1 col-xs-12">
                                        <div class="form-group">
                                            <label for="descripcion">Nro de Cotización <span class="text-danger">*</span></label>
                                            <input type="text" id="descripcion" class="form-control form-control-sm" name="descripcion"  placeholder="1-8596" @keypress="soloNumerosGuion($event)" @keyup.enter="cargarOrdenes" maxlength="13">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive px-1" style="width:98%;height:300px;margin 2rem auto;" id="tabla-orden">
                                            <small><strong>Seleccione Cotización para Agregar <span class="text-danger">*</span></strong></small>
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
                                    <div class="col-md-12" id="data-stock">
                                    </div>
                                </div>
                        
                            </div>
                        </div>
                        
                        <div class="row d-flex justify-content-center">
                            <input type="hidden" name="listDetalles" id="listDetalles" :value="listDetalles.join(',')" />
                            
                            <div class="table-responsive px-1" id="tabla-orden-crear" style="width:70%;height:350px;margin 2rem auto;">
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
                                                <input type="text" class="form-control form-control-sm text-right" :name="'txtsubtototal'+p.id" :id="'txtsubtototal'+p.id" :value="p.total2" readonly=""/>

                                                <input type="hidden" :name="'idcotizacion'+p.id" :value="p.id"/>
                                            
                                            </td>
                                            
                                            <td style="width:30px;" class="pt-2">
                                                <a href="javascript:void(0)" class="btn-danger btn-sm" @click="eliminar(p.id)" title="Eliminar" v-if="p.mostrarOpc==true">
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
                            <div class="col-md-12" id="data-error">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="enviarFormModalOrden('modalDetallesCotizacion','formEnv','data-error')" id="btnEnvioG" class="btn btn-sm btn-success">
                            <i class="mdi mdi-check-bold icon-size"></i> Guardar
                        </button>
                        
                        <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cerrar</button>
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
    name: 'DetallesMovCompra',
    mixins: [misMixins],
    data () {
        return {
            id: this.$attrs.idguia,
            documento: this.$attrs.documento,
            detalles: [],
            arrOrdenes: [],
            listDetalles: [],
            placa: '',
            cliente: '',
            clienteId: 0,
            token: this.$store.state.token,
            total: 0
        }
    },
    methods: {
        async cargarOrdenes () {
            let me = this
            let descripcion = document.getElementById('descripcion').value
            let cotizaciones = await axios({
                method: 'post',
                url: '/buscarcotizaciones',
                data: {
                    'placa': me.placa,
                    'descripcion': descripcion,
                    'clienteId': me.clienteId,
                    '_token': this.$store.state.token   
                }
            })
            me.arrOrdenes = cotizaciones.data.cotizaciones
        },
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalDetallesCotizacion').modal('toggle')
            $('#modalDetallesCotizacion').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (attr, cliente, clienteId, placa) {
            $('#modalDetallesCotizacion').css('z-index','-1')
            let me = this
            me.id = attr
            me.cliente = cliente
            me.clienteId = clienteId
            me.placa = placa
            let descripcion = document.getElementById('descripcion').value
            document.getElementById('data-error').innerHTML = ''
            document.getElementById('data-stock').innerHTML = ''
      
            if (me.id > 0) {
                let response = await axios.get('/getcotizaciones/'+me.id)
                me.detalles = response.data.detalles
                me.detalles.forEach(element => {
                    me.listDetalles.push(element.id)
                })
                me.calcularTotal()
                me.cargarOrdenes()
                $('#modalDetallesCotizacion').modal({backdrop: 'static', show: true, keyboard: false })
                $('#modalDetallesCotizacion').css('z-index', '1500')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1') 
                document.getElementById('descripcion').focus()
                // $('.modal-backdrop').css('z-index','1')
            }
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
                // console.log(element)
                acum+= parseFloat(element.total)
            })
            // alert(acum)
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
            document.getElementById('data-stock').innerHTML = ''
            me.elementoSeleccionado = ref
            if (!rpta) {
                document.getElementById('data-stock').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-warning text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Cotización ya antes Agregada</strong></a></div ></div>'

                // let element = document.getElementById('txtnumero'+ref.id)
                // element.focus()
            }
        
            if (rpta) {
                let elemento = {'id': ref.id, 'numero': ref.numero, 'total': ref.total, 'total2': ref.total2, 'mostrarOpc': true }
                me.detalles.push(elemento)
                me.listDetalles.push(elemento.id)
                // console.log(elemento)
                // console.log(me.detalles)
                me.calcularTotal()
            }
        
        }
    },
    created () {
        let me  = this
        me.detalles = []
    },
    activated: function () {
        this.listDetalles = []
        this.detalles =  []
        this.arrOrdenes= []
          
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    },
    deactivated: function () {
    },
    mounted () {

    },
    beforeDestroy: function () {
        let me  = this
        me.detalles = []
        // this.$store.state.mostrarModal = false
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
thead { position: sticky; top: 0; }
tfoot { position: sticky; bottom: 0; }

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
table {
    cursor:pointer;
}
</style>