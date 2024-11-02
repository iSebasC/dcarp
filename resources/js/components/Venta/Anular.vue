<template>
    <div class="modal fade" id="modalAnular" tabindex="-1" role="dialog" aria-labelledby="modalAnularLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form class="form" id="formEnvModalAnulacion" method="POST" action="/guardaranulacion">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAnularLabel">Detalles de Documento: <strong v-text="this.$attrs.documento"></strong></h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="id" name="id">
                        <div class="row">
                            <label for="tipoAnul" class="ml-1">Tipo de Anulación <span class="text-danger">*</span></label>
                            <div class="col-md-12 col-xs-12 mt-1" id="tipoAnul">
                                <input type="radio" @change="capturarDato('1')" name="tipo" checked="" value="1" id="input-radio-genM">
                                <label for="input-radio-genM">Anulación Simple</label>

                                <!-- <input type="radio" @change="capturarDato('2')" class="ml-1" name="tipo" value="2" id="input-radio-genF">
                                <label for="input-radio-genF">Nota de Crédito</label> -->

                                <!-- <input type="radio" @change="capturarDato('3')" class="ml-1" name="tipo" value="3" id="input-radio-genF1">
                                <label for="input-radio-genF1">Nota de Débito</label> -->
                            </div>
                        </div>

                        <div class="row" v-if="band">
                            <div class="col-md-9 pl-1 pt-2">
                                <label for="select-motivo">Motivo <span class="text-danger">*</span></label>
                                <select class="custom-select custom-select-sm" id="select-motivo" name="motivo">
                                    <option value="" disabled="" selected="">Seleccione</option>
                                    <option v-for="f in opciones" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="data-error-modal-anulacion">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cerrar</button>
                        <button type="button" @click="enviarFormModalAnulacion('modalAnular')" id="btnGuardar" class="btn btn-success btn-sm" ><i class="mdi mdi-check-bold icon-size"></i> Guardar</button>
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
    name: 'Anular',
    mixins: [misMixins],
    data () {
        return {
            id: this.$attrs.idventa,
            documento: this.$attrs.documento,
            // detalles: [],
            token: this.$store.state.token,
            opciones: [],
            opcs: [
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
            opcs2: [
                {id:'1', nombre:'Interés por mora'},
                {id:'2', nombre:'Aumento en el valor'}
            ],
            band: false
            // total: 0
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalAnular').modal('toggle')
            $('#modalAnular').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        capturarDato (valor) {
         let me = this
         if (valor == 2) {
            me.opciones = me.opcs
            me.band = true
         } else { 
            if (valor == 3) {
               me.opciones = me.opcs2
               me.band = true
            } else {
                me.band = false
            }
         }
        },
        async showModal (attr) {
            $('#modalAnular').css('z-index','-1')
            let me = this
            me.id = attr
            // alert(me.id)
            // me.nombre = attr2
            // $('#texto').text(me.id)
            // alert (me.id)
            // me.mostrarModal = ne.isVisible()

            // alert('....' + me.id)
            // if (me.id !== 'undefined') {
            if (me.id > 0) {
                // let response = await axios({
                // method: 'post',
                //     url: '/getdetalles/'+me.id,
                //     data: {
                //         '_token': me.token
                //     }
                // })
                // me.detalles = response.data.detalles
                // me.total = response.data.total
                
                // me.arreglo2 = response.data.encabezados
                // me.opciones = response.data.opciones02
                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                document.getElementById('data-error-modal-anulacion').innerHTML = ''
                $('#modalAnular').modal({backdrop: 'static', show: true,  keyboard: false})
                $('#modalAnular').css('z-index', '1500')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')   
                // $('.modal-backdrop').css('z-index','1')
            }
                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        }
    },
    created () {
        let me  = this
        me.detalles = []
    },
    activated: function () {
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

input[type="radio"] {
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
input[type="radio"]:checked {
     background-color: teal;
     cursor: pointer;
}
</style>