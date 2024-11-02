<template>
    <div class="modal fade" id="modalDetallesI" tabindex="-1" role="dialog" aria-labelledby="modalDetallesILabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesILabel">Detalles de Documento: <strong v-text="this.$attrs.documento"></strong></h5>
                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" :value="id" name="id">
                    <div class="table-responsive px-1" id="tabla-detalle" style="height:350px; overflow-y:scroll;">
                        <table class="table table-striped mb-0">
                            <thead>
                              <tr class="table-warning">
                                <th class="text-left">#</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-right">Precio Compra</th>
                                <th class="text-right">Precio Venta</th>
                                <th class="text-right">Sub Total</th>
                              </tr>
                            </thead>
                            <tbody style="height:250px;">
                                <tr v-if="!detalles.length">
                                  <td class="text-left text-danger" colspan="5"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="p in detalles" :key="p.id">
                                    <td class="text-left" v-text="(p.item<10?'0'+p.item:p.item)" ></td>
                                    <td class="text-center" v-text="p.cantidad"></td>
                                    <td class="text-center" v-text="p.descripcion"></td>
                                    <td class="text-right" v-text="p.preciocompra"></td>
                                    <td class="text-right" v-text="p.precioventa"></td>
                                    <td class="text-right" v-text="p.subTotal"></td>
                                </tr>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>TOTAL</th>
                                    <th class="text-right" v-text=" (moneda=='D'?' $ ':' S/ ') + total"></th>
                                  </tr>
                            </tfoot>
                          </table>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'

export default {
    name: 'DetallesMov',
    mixins: [misMixins],
    data () {
        return {
            id: this.$attrs.idguia,
            documento: this.$attrs.documento,
            detalles: [],
            token: this.$store.state.token,
            total: 0,
            moneda: '',
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalDetallesI').modal('toggle')
            $('#modalDetallesI').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (attr) {
            $('#modalDetallesI').css('z-index','-1')
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
                let response = await axios({
                method: 'post',
                    url: '/getdetallesguia/'+me.id,
                    data: {
                        '_token': me.token
                    }
                })
                me.detalles = response.data.detalles
                me.total = response.data.total
                me.moneda = response.data.tipo
                
                // me.arreglo2 = response.data.encabezados
                // me.opciones = response.data.opciones02
                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                $('#modalDetallesI').modal({backdrop: 'static', show: true, keyboard: false })
                $('#modalDetallesI').css('z-index', '1500')
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