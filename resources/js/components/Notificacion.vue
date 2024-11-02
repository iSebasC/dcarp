<template>
    <div class="modal fade" id="modalNotificacion" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form :action="url" method="POST" :id="`form_${entidad}`" class="form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNotificacionLabel">Crear Notificación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label :for="`titulo_${entidad}`">Título <span class="text-danger">*</span> </label>
                                <input type="text" name="titulo" :id="`titulo_${entidad}`" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label :for="`mensaje_${entidad}`">Mensaje <span class="text-danger">*</span></label>
                                <textarea name="mensaje" :id="`mensaje_${entidad}`" class="form-control form-control-sm no-resize" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label :for="`fecha_inicio_${entidad}`">F. Inicio <span class="text-danger">*</span> </label>
                                <input type="date" name="fecha_inicio" :id="`fecha_inicio_${entidad}`" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-3">
                                <label :for="`fecha_fin_${entidad}`">F. Fin <span class="text-danger">*</span> </label>
                                <input type="date" name="fecha_fin" :id="`fecha_fin_${entidad}`" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 ml-3">
                        <div :id="`data-error_${this.entidad}`">

                        </div>
                    </div>
                
                    <div class="modal-footer">
                        <input type="hidden" name="id" :value="id">
                        <input type="hidden" name="tipo" :value="tipo">
                        <button type="button" @click="crearNotificacion"
                            :id="'btnEnvio_'+entidad" class="btn btn-sm btn-success">
                            <i class="mdi mdi-check-bold icon-size"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                            <i class="mdi mdi-close icon-size"></i> Cerrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../mixins/mixin.js'

export default {
    name: 'CrearNotificacion',
    mixins: [misMixins],
    data () {
        return {
            id: 0,
            tipo: '',
            token: this.$store.state.token,
            entidad: 'crear_notificacion',
            url: '/guardarnotificacion'
        }
    },
    methods: {
        cerrarModal () {
            $('#modalNotificacion').modal('toggle')
            $('#modalNotificacion').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (attr, type) {
            $('#modalNotificacion').css('z-index','-1')
            let me = this
            me.id = attr
            me.tipo = type
            if (me.id > 0) {
                var form = document.getElementById(`form_${me.entidad}`);
                form.reset();
                document.getElementById('fecha_inicio_'+me.entidad).value = me.obtenerFechaActual()
                document.getElementById('fecha_fin_'+me.entidad).value = me.obtenerFechaActual()
     
                $('#modalNotificacion').modal({backdrop: 'static', show: true, keyboard: false})
                $('#modalNotificacion').css('z-index', '1500')
                $(".modal-backdrop").css("z-index", "1");
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')         
            }
         
        },
        async crearNotificacion() {
            let me = this;
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>';
            // alert(url)
            let button = document.getElementById(`btnEnvio_${me.entidad}`);
            button.innerHTML = icon + " Cargando...";
            button.disabled = true;
            var form = document.getElementById(`form_${me.entidad}`);
            let response = null;
            let data = me.serializeForm(form);
            
            response = await axios({
                method: form.method,
                url: form.action,
                data: data,
            });
            
            var arreglo = response.data.errores;
            let cadena_errors = "";
            Object.values(arreglo).forEach((val) => {
                cadena_errors += val + ", ";
            });

            if (response.data.estado == false) {
                document.getElementById("data-error_" + me.entidad).innerHTML =
                    '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' +
                    cadena_errors.slice(0, -2) +
                    "</a></div ></div >";
            } else {
                document.getElementById("data-error_" + me.entidad).innerHTML =
                    '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss="alert">&times;</button><strong>' +
                    cadena_errors.slice(0, -1) +
                    "</strong></a></div ></div>";
                me.cerrarModal();
            }
            button.disabled = false;
            button.innerHTML = icon + " Guardar";
        }
    },
    created () {
        let me  = this
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