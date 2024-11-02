<template>
    <div class="modal fade" id="modalPermiso" tabindex="-1" role="dialog" aria-labelledby="modalPermisoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarpermisousuario">
                    <div class="modal-header">
                    <h5 class="modal-title" id="modalPermisoLabel">Permisos del Tipo de Usuario: <strong v-text="this.$attrs.tipousuario"></strong></h5>
                    <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" :value="id" name="id">
                        <input type="hidden" :value="this.$store.state.token" name="_token">
                        <input type="hidden" :value="opciones" name="opciones" id="opciones">
                        <div class="row">
                            <div class="col-md-12">
                                <h5><i class="mdi mdi-vector-bezier mr-1"></i><strong>Opciones Generales</strong></h5>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div v-for="(item, index) in arreglo2" :key="index" class="col-md-4 col-xs-12">
                                        <input class="mr-1" @change="obtenerValor(item.menu_p.id)" type="checkbox" :name="'chk_'+item.menu_p.id" :id="'chk_'+item.menu_p.id" 
                                        :checked="(item.menu_p.estado=='S'?'checked':'')" :disabled="item.menu_p.nombre=='Inicio'">
                                        <label :for="'chk_'+item.menu_p.id" v-text="item.menu_p.nombre"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div v-for="item in arreglo" class="row mt-1" :key="item.menu_p.id">
                            <div class="col-md-12">
                                <h5><i class="mdi mdi-vector-bezier mr-1"></i><strong v-text="item.menu_p.nombre"></strong></h5>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div v-for="(item2,index2) in item.menu_s" :key="index2" class="col-md-4 col-xs-12">
                                        <input class="mr-1" @change="obtenerValor(item2.id)" type="checkbox" :name="'chk_'+item2.id" 
                                        :id="'chk_'+item2.id" :checked="(item2.estado=='S'?'checked':'')" >
                                        <label :for="'chk_'+item2.id" v-text="item2.nombre"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" :id="'data-error_'+entidad">
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                           <i class="mdi mdi-close icon-size"></i> Cancelar</button>
                       
                       <button type="button" @click="enviarFormM('modalPermiso', entidad)" 
                       :id="'btnEnvio_'+entidad" class="btn btn-success btn-sm" ><i class="mdi mdi-check-bold icon-size"></i> Guardar</button>
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
    name: 'AdminPermisos',
    mixins: [misMixins],
    data () {
        return {
            id: this.$attrs.idmodal,
            nombre: this.$attrs.tipousuario,
            arreglo: [],
            arreglo2: [],
            opciones: '',
            url: '/permisos',
            token: this.$store.state.token,
            entidad: 'act-permisos'
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalPermiso').modal('toggle')
            $('#modalPermiso').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (attr) {
            // $('#header-sec').css('z-index', '1')
            // $('#aside-sec').css('z-index', '1')
            
            // $('#modalPermiso').css('z-index', '-1')
            let me = this
            me.id = attr
            document.getElementById('data-error_'+me.entidad).innerHTML = ''
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
                    url: '/opciones/'+me.id,
                    data: {
                        '_token': me.token
                    }
                })
                
                me.arreglo = response.data.opciones
                me.arreglo2 = response.data.encabezados
                me.opciones = response.data.opciones02
                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                $('#modalPermiso').modal({backdrop: 'static', show: true,  keyboard: false})
                $('#modalPermiso').css('z-index', '1500')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')

                $('.modal-backdrop').css('z-index','1')
            
            }
                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        obtenerValor (valor) {
            let me = this
            var e  = document.getElementById('chk_'+valor)
            let arreglo = me.opciones.split(',')
            if (e.checked) {
                arreglo.push(valor)
            } else {
                for (let i=0; i< arreglo.length; i++) {
                    if (arreglo[i] == valor) {
                        arreglo.splice(i,1)
                    }
                }
            }
            me.opciones = arreglo.join()
            // alert(e.checked)
            // console.log(me.opciones)
        }
    },
    created () {
        let me  = this
        me.arreglo = []
        me.arreglo2 = []
        me.opciones = ''
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
        me.arreglo = []
        // console.log(me.arreglo)
        me.arreglo2 = []
        me.opciones = ''
        // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
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