<template>
    <div class="modal fade" id="modalCerrar" tabindex="-1" role="dialog" aria-labelledby="modalCerrarLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form" id="formEnvModal03" method="POST" action="/cerrar">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCerrarLabel">Cerrar Caja</h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <input type="hidden" name="cajaId" :value="caja!=null?caja.id:''" />
                        <div class="row mt-1">
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label for="arrLocal">Seleccione Tienda <span class="text-danger">*</span></label>
                                    <v-select disabled="" taggable  :options="arrLocales" v-model="localSeleccionado" option-value="value" option-text="label" placeholder="Seleccione Tienda" name="arrLocal"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label for="saldo">Saldo (PEN)<span class="text-danger">*</span></label>
                                    <input type="text" readonly="" :value="caja!=null?caja.saldoCierre:'0.00'" 
                                    id="saldo" class="form-control form-control-sm text-right" name="saldo">
                                </div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label for="saldoD">Saldo (USD)<span class="text-danger">*</span></label>
                                    <input type="text" readonly="" :value="caja!=null?caja.saldoCierreD:'0.00'" 
                                    id="saldoD" class="form-control form-control-sm text-right" name="saldoD">
                                </div>
                            </div>
                         
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                <label for="fecha">Fecha <span class="text-danger">*</span></label>
                                <input type="date" id="fecha" readonly="" :value="caja!=null?caja.fecha:''" 
                                class="form-control form-control-sm text-center" name="fecha">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12" id="data-error-modal-cerrar">
                            </div>
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" @click="enviarFormModalCerrar('modalCerrar')" id="btnGuardarC" class="btn btn-success btn-sm" ><i class="mdi mdi-check-bold icon-size"></i> {{nombreBtn}}</button>
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
    name: 'Cliente',
    mixins: [misMixins],
    data () {
        return {
            bandCaja: false,
            token: this.$store.state.token,
            nombreBtn: 'Cerrar',
            id: '',
            caja: null,
            arrLocales: [],
            localSeleccionado: null
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();
            $('#modalCerrar').modal('toggle')
            $('#modalCerrar').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (id) {
            $('#modalCerrar').css('z-index','-1')
            let me = this
            me.id = id
            let caja = await axios.get('/obtenercaja/'+me.id)
            me.caja = caja.data.caja

            me.localSeleccionado = caja.data.local
         
            let locales = await axios.get('/obtenertiendas')
            let array = locales.data.locales
            let datos = []
            me.arrLocales = []
           
            array.forEach(element => {
                var l =  { value:element.id, label: element.direccion+'- '+element.departamento}
                datos.push(l)
            })
            me.arrLocales = datos

          
            document.getElementById('data-error-modal-cerrar').innerHTML = ''
            $('#modalCerrar').modal({backdrop: 'static', show: true, keyboard: false})
            $('#modalCerrar').css('z-index', '1500')
            $('.modal-backdrop').css('z-index','1')
            $('#header-sec').css('z-index','1')
            $('#aside-sec').css('z-index','1')      
        }
    },
    created () {
        let me  = this
    },
    activated: function () {
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal
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
</style>