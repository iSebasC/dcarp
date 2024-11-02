<template>
    <div class="modal fade" id="modalGestionPorc" tabindex="-1" role="dialog" aria-labelledby="modalGestionLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form class="form" :id="'formEnvModal_'+entidad" method="POST" action="/guardarporcentaje">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGestionLabel">Márgen de Ganancia</h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <input type="hidden" :value="id" id="id" name="id">
                        <div class="row mt-1 justify-content-center">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'codigo_'+entidad">Código <span class="text-danger">*</span></label>
                                    <input type="text" :value="codigo" :id="'codigo_'+entidad" class="form-control form-control-sm text-center" 
                                    name="codigo" maxlength="3" minlength="3" @keypress="soloNumeros($event)">
                                </div>
                            </div>
                     
                            <div class="col-md-5 col-xs-12">
                                <div class="form-group">
                                    <label :for="'porcentaje_'+entidad">Margen (%) <span class="text-danger">*</span></label>
                                    <input type="number" :value="porcentaje" :id="'porcentaje_'+entidad" class="form-control form-control-sm text-center" 
                                    name="porcentaje" step="0.1" max="100" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" :id="'data-error-modal_'+entidad">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" @click="enviarFormModalGestion('modalGestionPorc',entidad,2)" :id="'btnEnvio_'+entidad" class="btn btn-success btn-sm">
                           <i class="mdi mdi-check-bold icon-size"></i> {{nombreBtn}}</button>
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
    name: 'Gestionprecioporc',
    mixins: [misMixins],
    data () {
        return {
            porcentaje: 0,
            codigo: '',
            // url: '/Cliente',
            token: this.$store.state.token,
            nombreBtn: 'Guardar',
            id: 0,
            entidad: 'gestion_porcentaje'
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalGestionPorc').modal('toggle')
            $('#modalGestionPorc').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (id,codigo, porcentaje) {
            let me = this
            document.getElementById('data-error-modal_'+me.entidad).innerHTML = ''
            $('#modalGestionPorc').css('z-index','-1')
            me.id = id
            me.codigo = codigo
            me.porcentaje = porcentaje
            $('#modalGestionPorc').modal({backdrop: 'static', show: true, keyboard: false})
            $('#modalGestionPorc').css('z-index', '1500')
            $('.modal-backdrop').css('z-index','1')
            $('#header-sec').css('z-index','1')
            $('#aside-sec').css('z-index','1')

                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        }
    },
    created () {
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