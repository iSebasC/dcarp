<template>
    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLabel">Detalles de Reclamo: <strong v-text="nroReclamo"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label :for="`fecha_${entidad}`">Fecha </label>
                            <input type="date" name="" :id="`fecha_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                        <div class="col-md-3">
                            <label :for="`doc_cliente_${entidad}`">Doc. Cliente </label>
                            <input type="text" name="" :id="`doc_cliente_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                        <div class="col-md-6">
                            <label :for="`cliente_${entidad}`">Cliente</label>
                            <input type="text" name="" :id="`cliente_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label :for="`orden_${entidad}`">Orden</label>
                            <input type="text" name="" :id="`orden_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                        <div class="col-md-3">
                            <label :for="`cita_${entidad}`">Cita</label>
                            <input type="text" name="" :id="`cita_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                        <div class="col-md-2">
                            <label :for="`grado_${entidad}`">Grado</label>
                            <input type="text" name="" :id="`grado_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                        <div class="col-md-4">
                            <label :for="`area_destino_${entidad}`">Área Destino</label>
                            <input type="text" name="" :id="`area_destino_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-2">
                            <label :for="`placa_${entidad}`">Placa</label>
                            <input type="text" name="" :id="`placa_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>

                        <div class="col-md-6">
                            <label :for="`asignado_a_${entidad}`">Asignado a</label>
                            <input type="text" name="" :id="`asignado_a_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>

                        <div class="col-md-4">
                            <label :for="`registrado_el_${entidad}`">Registrado el</label>
                            <input type="text" name="" :id="`registrado_el_${entidad}`" class="form-control form-control-sm" readonly="">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label :for="`reclamo_${entidad}`">Reclamo</label>
                            <textarea name="" :id="`reclamo_${entidad}`" class="form-control form-control-sm no-resize" readonly="" rows="10" columns="10"></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label :for="`solucion_${entidad}`">Solución</label>
                            <textarea name="" :id="`solucion_${entidad}`" class="form-control form-control-sm no-resize" readonly="" rows="10" columns="10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                        <i class="mdi mdi-close icon-size"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'

export default {
    name: 'Detalles',
    mixins: [misMixins],
    data () {
        return {
            id: this.$attrs.idventa,
            nroReclamo: '',
            token: this.$store.state.token,
            entidad: 'preview_reclamo'
        }
    },
    methods: {
        cerrarModal () {
            $('#modalDetalles').modal('toggle')
            $('#modalDetalles').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (attr) {
            $('#modalDetalles').css('z-index','-1')
            let me = this
            me.id = attr
            if (me.id > 0) {
                let response = await axios.get(`/reclamo/ver/${me.id}`);
                let reclamo = response.data.reclamo
                if (reclamo != null) {
                    this.nroReclamo = reclamo.nroReclamo;
                    document.getElementById(`fecha_${this.entidad}`).value = reclamo.fecha;
                    document.getElementById(`doc_cliente_${this.entidad}`).value = reclamo.doc;
                    document.getElementById(`cliente_${this.entidad}`).value = reclamo.cliente;
                    document.getElementById(`orden_${this.entidad}`).value = reclamo.orden;
                    document.getElementById(`cita_${this.entidad}`).value = reclamo.cita;
                    document.getElementById(`grado_${this.entidad}`).value = reclamo.gradoText;
                    document.getElementById(`area_destino_${this.entidad}`).value = reclamo.areaDestino;
                    document.getElementById(`asignado_a_${this.entidad}`).value = reclamo.asignadoA;
                    document.getElementById(`reclamo_${this.entidad}`).value = reclamo.reclamo;
                    document.getElementById(`solucion_${this.entidad}`).value = reclamo.solucion;
                    
                    document.getElementById(`placa_${this.entidad}`).value = reclamo.placa;
                    document.getElementById(`registrado_el_${this.entidad}`).value = reclamo.fechaR;
                 
                    $('#modalDetalles').modal({backdrop: 'static', show: true, keyboard: false})
                    $('#modalDetalles').css('z-index', '1500')
                    $(".modal-backdrop").css("z-index", "1");
                    $('#header-sec').css('z-index','1')
                    $('#aside-sec').css('z-index','1')         

                }
            }
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