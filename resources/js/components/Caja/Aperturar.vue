<template>
    <div class="modal fade" id="modalAperturar" tabindex="-1" role="dialog" aria-labelledby="modalAperturarLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form" id="formEnvModalAperturar" method="POST" action="/aperturar">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAperturarLabel">Aperturar Caja</h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <div class="row mt-1">
                            <div class="col-md-12 col-xs-12">
                                <input type="hidden" name="cajaId" />
                                <div class="form-group">
                                    <label for="arrLocal">Seleccione Tienda <span class="text-danger">*</span></label>
                                    <v-select taggable  :options="arrLocales"  option-value="value" @input="seleccionarTienda"  option-text="label" placeholder="Seleccione Tienda" name="arrLocal"></v-select>
                                    <input type="hidden" name="tiendaId" id="tiendaId" :value="tiendaId" />
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="bandCaja">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label for="saldo">Monto Apertura <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" value="0.00" id="saldo" 
                                    class="form-control form-control-sm text-center" 
                                    name="saldo">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12" id="data-error-modal-aperturar">
                            </div>
                        </div>
                      
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" id="btnEnvioG" @click="enviarFormModalAperturar('modalAperturar')" 
                       class="btn btn-success btn-sm" ><i class="mdi mdi-check-bold icon-size"></i> {{nombreBtn}}</button>
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
            tiendaId: 0,
            token: this.$store.state.token,
            nombreBtn: 'Aperturar',
            arrLocales: []
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();
            $('#modalAperturar').modal('toggle')
            $('#modalAperturar').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal () {
            $('#modalAperturar').css('z-index','-1')
            let me = this
            let locales = await axios.get('/obtenertiendas')
            let array = locales.data.locales
            let datos = []
            me.arrLocales = []
            me.tiendaId= 0

            array.forEach(element => {
                var l =  { value:element.id, label: element.direccion+'- '+element.departamento}
                datos.push(l)
            })
            me.arrLocales = datos

            // if (me.documento != '') {
            //     let response = await axios({
            //         method: 'post',
            //         url: '/getcliente/'+me.documento,
            //         data: {
            //             '_token': me.token
            //         }
            //     })
            //     if (response.data.estado) {
            //         me.cliente = response.data.cliente
            //         me.nombreBtn = 'Actualizar'
            //     } else {
            //         me.cliente = null
            //         me.nombreBtn = 'Guardar'
            //     }
 
            //     let tipo = me.documento.length
            //     me.bandTipoCliente = true
            //     if (tipo == 8) {
            //         me.bandTipoCliente = false
            //     }
                document.getElementById('data-error-modal-aperturar').innerHTML = ''
                $('#modalAperturar').modal({backdrop: 'static', show: true, keyboard: false })
                $('#modalAperturar').css('z-index', '1500')
                $('.modal-backdrop').css('z-index','1')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1') 
            // }
                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        async seleccionarTienda (item) {
            let me = this
            me.tiendaId = item.value
            me.bandCaja = true
            window.setTimeout( function () {
                document.getElementById('saldo').focus()
            }, 500)
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