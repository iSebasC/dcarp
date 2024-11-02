<template>
    <div class="modal fade" id="modalIgv" tabindex="-1" role="dialog" aria-labelledby="modalIgvLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form class="form" :id="'formEnvModal_'+entidad" method="POST" action="/guardarconfiguracion">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalIgvLabel">Configuración de Igv</h5>
                        <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <div class="row mt-1 justify-content-center">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'porcentaje_'+entidad">Igv <span class="text-danger">*</span></label>
                                    <input type="text" :value="`${igv} %`" :id="'porcentaje_'+entidad" class="form-control form-control-sm text-center" name="igv" step="0.01" disabled="">
                                </div>
                            </div>
                 
                            <div class="col-md-5 col-xs-12">
                                <div class="form-group">
                                    <label :for="'mostrar_'+entidad">Mostrar <span class="text-danger">*</span></label>
                                    <select name="mostrar" class="custom-select custom-select-sm" :id="'mostrar_'+entidad">
                                        <option value="" selected="" disabled="">Seleccione</option>
                                        <option v-for="f in mostrar" :key="f.id" :value="f.id" :selected="mostrarId==f.id"> {{f.nombre}}</option>
                                    </select>
                                </div>
                            </div>
                 
                        </div>
                                  
                        <div class="row mt-1 justify-content-center">
                            <div class="col-md-9 col-xs-12">
                                <div class="form-group">
                                    <label :for="'fecha_'+entidad">Útima Actualización <span class="text-danger">*</span></label>
                                    <input type="text" :value="fecha" :id="'fecha_'+entidad" class="form-control form-control-sm text-center" name="fecha" step="0.001" readOnly="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" :id="'data-error-modal_'+entidad">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                           <i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" :id="'btnEnvio_'+entidad" 
                       @click="enviarFormModalGestion('modalIgv',entidad, 1)" class="btn btn-success btn-sm">
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
    name: 'ConfiguracionIgv',
    mixins: [misMixins],
    data () {
        return {
            igv: 0,
            mostrarId: 'S',
            fecha:'',
            // url: '/Cliente',
            token: this.$store.state.token,
            nombreBtn: 'Guardar',
            bandCampo: false,
            entidad: 'config_igv',
            mostrar: [
                {id:'S', nombre: 'Si'},
                {id:'N', nombre: 'No'}
            ]
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalIgv').modal('toggle')
            $('#modalIgv').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal () {
            let me = this
            $('#modalIgv').css('z-index','-1')
            if (me.documento != '') {
                let response = await axios({
                    method: 'post',
                    url: '/getconfiguracion',
                    data: {
                        '_token': me.token
                    }
                })
                if (response.data.estado) {
                    me.igv = response.data.configuracion.igv
                    me.mostrarId = response.data.configuracion.mostrar
                    me.fecha  = response.data.configuracion.fechaUlt
                    me.nombreBtn = 'Actualizar'
                } else {
                    me.igv = 0
                    me.mostrarId = 'S'
                    me.fecha = ''
                    me.nombreBtn = 'Guardar'
                }

                document.getElementById('data-error-modal_'+me.entidad).innerHTML = ''
                // document.getElementById('precio').value = me.precio
                
                $('#modalIgv').modal({backdrop: 'static', show: true, keyboard: false})
                $('#modalIgv').css('z-index', '1500')
                $('.modal-backdrop').css('z-index','1')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')

            }
                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
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