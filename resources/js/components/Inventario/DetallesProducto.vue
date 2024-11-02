<template>
    <div class="modal fade" id="modalDetallesI" tabindex="-1" role="dialog" aria-labelledby="modalDetallesILabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesILabel">
                    <strong>Producto:</strong> <span v-text="producto"></span><br>
                    <small><strong>Almacén:</strong> <span v-text="almacen"></span></small><br>
                    <small><strong>Stock Actual:</strong> <span v-text="stock"></span></small>
                </h5>
                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group row">
                                <label :for="'buscar_'+entidad" class="col-sm-1 m-4px col-form-label pr-0 mr-0 pt-0">Buscar:</label>
                                <div class="col-sm-4">
                                    <input type="search" v-model="bqproducto" placeholder="buscar" name="descProducto" class="form-control form-control-sm" :id="'buscar_'+entidad">
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="text-info pl-2 ml-1 mb-2 hidden col-form-label" :id="'loading_'+entidad"><Loader /></span>
                    <div class="table-responsive px-2 d-none" :id="'tabla_'+entidad"  style="height:250px;">
                        <table class="table table-striped mb-0">
                            <thead style="position:relative;">
                              <tr>
                                <th class="text-left">#</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">T. Documento</th>
                                <th class="text-center">Referencia</th>
                                <th class="text-center">Entrada</th>
                                <th class="text-center">Salida</th>
                                <th class="text-center">Costo</th>
                                <th class="text-center">Moneda Costo</th>
                                <th class="text-center">Cliente/Proveedor</th>
                                <th class="text-center">Registrado</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!detallesComputed.length">
                                  <td class="text-left text-danger" colspan="5"><strong>No se Encontraron Resultados en su Búsqueda</strong></td>
                                </tr>

                                <tr v-for="(p,index) in detallesComputed" :key="index" :class="(p.estado=='E'?'table-danger':'')">
                                    <td class="text-left" v-text="((index+1)<10?'0'+(index+1):(index+1))" ></td>
                                    <td class="text-center"><span class="badge badge-pill badge-primary" v-text="p.fecha"></span></td>
                                    <td class="text-center"><strong v-text="p.tipoDoc"></strong></td>
                                    <td class="text-center" v-text="p.documento"></td>
                                    <td class="text-center"><span class="text-success" v-text="(p.movimiento=='ENTRADA'?p.cantidad:'')"></span></td>
                                    <td class="text-center"><span class="text-danger"  v-text="(p.movimiento=='SALIDA'?p.cantidad:'')"></span></td>
                                    <td class="text-center" v-text="mostrarParam(p,1)"></td>
                                    <td class="text-center"><mark v-text="(mostrarParam(p,0)=='D'?'DÓLARES':'SOLES')"></mark></td>
                                   <td class="text-center" v-text="p.persona"></td>
                                    <td class="text-center"><span class="badge badge-pill badge-info" v-text="p.fechaReg"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="exportar()" class="btn btn-success btn-sm">
                        <i class="mdi mdi-file-excel icon-size"></i> Exportar
                    </button>
                     
                    <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Loader from '../Loader'

export default {
    name: 'DetallesMov',
    mixins: [misMixins],
    data() {
        return {
            idalmacen: 0,
            detalles: [],
            token: this.$store.state.token,
            total: 0,
            entidad: 'modalDetalleProducto',
            producto: '',
            bqproducto: '',
            almacen: '',
            stock: 0,
            idprod: 0,
        };
    },
    computed: {
        detallesComputed() {
            return this.detalles.filter(item => {
                return item.fecha.includes(this.bqproducto.toLowerCase()) ||
                    item.tipoDoc.toLowerCase().includes(this.bqproducto.toLowerCase()) ||
                    item.documento.toLowerCase().includes(this.bqproducto.toLowerCase()) ||
                    // item.cantidad.toLowerCase().includes(this.bqproducto.toLowerCase()) || 
                    item.persona.toLowerCase().includes(this.bqproducto.toLowerCase()) ||
                    item.fechaReg.toLowerCase().includes(this.bqproducto.toLowerCase());
            });
        },
    },
    methods: {
        cerrarModal() {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();
            $('#modalDetallesI').modal('toggle');
            $('#modalDetallesI').css('z-index', '-1');
            $('#header-sec').css('z-index', '');
            $('#aside-sec').css('z-index', '');
        },
        async showModal(idprod, descripcion, idalmacen) {
            $('#modalDetallesI').css('z-index', '-1');
            let me = this;
            me.producto = descripcion;
            me.idprod = idprod;
            me.idalmacen = idalmacen;
            // alert(me.id)
            // me.nombre = attr2
            // $('#texto').text(me.id)
            // alert (me.id)
            // me.mostrarModal = ne.isVisible()
            // alert('....' + me.id)
            // if (me.id !== 'undefined') {
            // if (me.id > 0) {
            let response = await axios({
                method: 'post',
                url: '/getmovimientosES',
                data: {
                    '_token': me.token,
                    'idprod': me.idprod,
                    'idalmacen': me.idalmacen
                }
            });
            // me.arreglo2 = response.data.encabezados
            // me.opciones = response.data.opciones02
            // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
            $('#modalDetallesI').modal({ backdrop: 'static', show: true, keyboard: false });
            $('#modalDetallesI').css('z-index', '1500');
            $('#header-sec').css('z-index', '1');
            $('#aside-sec').css('z-index', '1');
            me.detalles = response.data.detalles;
            me.stock = response.data.stock;
            me.almacen = response.data.direccion;
            me.renderTabla(me.entidad);
            // $('.modal-backdrop').css('z-index','1')
            // }
            // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        exportar() {
            window.open(`/repensa/excel?idalmacen=${this.idalmacen}&idprod=${this.idprod}&prod=${this.producto}`, '_blank');
        },
        mostrarParam(obj, inc) {
            let param = '';
            if (obj.det_compra != null) {
                param = obj.det_compra;
            }
            if (obj.det_guia != null) {
                param = obj.det_guia;
            }
            let valor = param.split('-');
            return valor[inc];
        },
    },
    created() {
        let me = this;
        me.detalles = [];
    },
    activated: function () {
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    },
    deactivated: function () {
    },
    mounted() {
    },
    beforeDestroy: function () {
        let me = this;
        me.detalles = [];
        // this.$store.state.mostrarModal = false
        // this.$store.state.mostrarModal = false //!this.$store.state.mostrarModal
    },
    components: { Loader }
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