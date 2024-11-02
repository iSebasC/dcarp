<template>
    <div class="modal fade" id="modalNota" tabindex="-1" role="dialog" aria-labelledby="modalNotaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalNotaLabel">Anulación del Documento: <strong v-text="this.$attrs.documento"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" :value="id" name="id">
                    <h5 class="ml-2">Proveedor: <strong v-text="this.$attrs.proveedor"></strong></h5>
                    <div class="table-responsive px-1" id="tabla-detallenota" style="height:300px; overflow-y:scroll;">
                        <table class="table table-striped mb-0">
                            <thead>
                              <tr class="table-warning">
                                <th class="text-left">#</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-right">Precio Compra</th>
                                <th class="text-right">Precio Venta</th>
                                <th class="text-right">Sub Total</th>
                                <th class="text-center"></th>
                              </tr>
                            </thead>
                            <tbody style="height:250px;">
                                <tr v-if="!detalles.length">
                                    <td class="text-left text-danger" colspan="7">
                                        <strong>No se Encontraron Resultados en su Búsqueda</strong>
                                    </td>
                                </tr>

                                <tr v-for="p in detalles" :key="p.id" :class="p.estado=='0'?'table-danger':''">
                                    <td class="text-left" v-text="(p.item<10?'0'+p.item:p.item)" ></td>
                                    <td class="text-center" v-text="p.cantidad"></td>
                                    <td class="text-center" v-text="p.descripcion"></td>
                                    <td class="text-right" v-text="p.preciocompra"></td>
                                    <td class="text-right" v-text="p.precioventa"></td>
                                    <td class="text-right" v-text="p.subTotal"></td>
                                    <td class="pt-1">
                                        <a v-if="p.estado!='0'" href="javascript:void(0)" class="btn-danger btn-sm"
                                         @click="eliminar(p.id)" title="Eliminar">
                                            <i class="mdi mdi-minus-thick icon-size"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>TOTAL S/ </th>
                                    <th class="text-right" v-text="total"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr />
                    <div class="row ml-2">
                        <div class="col-md-3 col-xs-12">
                            <div class="form-group">
                                <label for="select_operacion">Tipo de Operación <span class="text-danger">*</span></label>
                                <select class="custom-select" name="tipooperacion" id="select_operacion" @change="tipoOperacion">
                                    <option value="" disabled="" selected="">Tipo de Operación</option>
                                    <option v-for="f in tipos" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2 col-xs-12" v-if="notacredito">
                            <div class="form-group">
                                <label for="docRef">Doc. de Referencia <span class="text-danger">*</span></label>
                                <input type="text" v-focus-on-create id="docRef" class="form-control" name="docRef" placeholder="1-395" maxlength="15">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="guardarN()" class="btn btn-success">
                        <i class="mdi mdi-check-bold"></i> Guardar
                    </button>
                    
                    <button type="button" class="btn btn-danger btn-md" data-dismiss="modal"><i class="mdi mdi-close"></i> Cerrar</button>
                </div>
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
            tipos: [
                {'id': 'A', 'nombre': 'Anulación'},
                {'id': 'NC', 'nombre': 'Nota de Crédito'}
            ],
            token: this.$store.state.token,
            total: 0,
            notacredito: false,
            listEliminaciones: []
        }
    },
    methods: {
        tipoOperacion () {
            let me  = this
            me.notacredito = false
            let s = document.getElementById('select_operacion').value
            if (s == 'NC') {
                me.notacredito = true
            }
        },
        async guardarN () {
            let me = this
            let doc = document.getElementById('docRef')
            let val = ''
            if (doc != null) {
                val = doc.value
            }

            let s = document.getElementById('select_operacion').value
           
            let response = await axios({
                method: 'post',
                url: '/darbaja/'+me.id,
                method: 'POST',
                data: {
                    '_token': me.token,
                    'listEliminacion': me.listEliminaciones.join(','),
                    'docRef': val,
                    'tipo': s
                }
            })
            
            var arreglo = response.data.errores
            let cadena_errors = '';
            Object.values(arreglo).forEach(val => {
                cadena_errors += val + ', '
            })

            if (response.data.estado) {
                document.getElementById('error').innerHTML =  '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' + cadena_errors.slice(0, -1) + '</strong></a></div ></div>'
        
                window.setTimeout( function () {
                    $('#modalNota').modal('hide')
                    $('body').removeClass('modal-open')
                    $('.modal-backdrop').remove()
                }, 1000)
            } else {
                document.getElementById('error').innerHTML =  '<div style = "margin-top:10px;"><div class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>' + cadena_errors.slice(0, -1) + '</strong></a></div ></div>'
        
            }
        },
        async showModal (attr) {
            $('#modalNota').css('z-index','-1')
            document.getElementById('error').innerHTML = ''
            
            let me = this
            me.id = attr
            if (me.id > 0) {
                let response = await axios({
                method: 'post',
                    url: '/getdetallescompra/'+me.id,
                    data: {
                        '_token': me.token
                    }
                })
                me.detalles = response.data.detalles
                me.total = response.data.total
             
                $('#modalNota').modal({backdrop: 'static', show: true})
                $('#modalNota').css('z-index', '1500')
            }
        },
        calcularTotal () {
            let me = this
            let acum = 0
            me.detalles.forEach(element => {
                if (element.cantidad != '' && element.preciocompra != '') {
                    acum+=element.cantidad * element.preciocompra
                }
            })
            me.total = (parseFloat(acum)*1000)/1000
            me.total = Math.round(me.total*100)/100
        },
        eliminar (id) {
            let me = this
            for(let i=0; i< me.detalles.length; i++) {
                let element = me.detalles[i]
                if (element.id == id) {
                    me.detalles.splice(i,1)
                    me.listEliminaciones.push(id)
                }
            }
            me.calcularTotal()
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