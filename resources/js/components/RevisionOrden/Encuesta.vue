<template>
    <div class="modal fade" id="modalEncuestaO" tabindex="-1" role="dialog" aria-labelledby="modalEncuestaOLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalEncuestaOLabel">Encuesta de Atención para el Cliente</h5>
                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarencuesta">
                        <div class="row">
                            <div class="col-md-4 ml-2">
                                <h6><mark>N° de Orden: </mark><strong v-text="documento"></strong></h6>
                                <h6><mark>Placa: </mark><strong v-text="placa"></strong></h6>
                                <h6><mark>Cliente: </mark><strong v-text="cliente"></strong></h6>
                                <h6><mark>Teléfono: </mark><strong v-text="telefono"></strong></h6>
                                <h6><mark>Correo Electrónico: </mark><strong v-text="correo_electronico"></strong></h6>
                                <h6><mark>Marca/Modelo: </mark><strong v-text="marcamodelo"></strong></h6>
                                <h6><mark>Kilometraje: </mark><strong v-text="kilometraje"></strong></h6>
                                <h6><mark>Vin: </mark><strong v-text="vin"></strong></h6>
                                <input type="hidden" name="idOrden" :value="id">
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label :for="'status_' + entidad" class="col-sm-3 m-4px col-form-label pr-0 mr-0 pt-0">Estado <span class="text-danger">*</span> </label>
                                    <div class="col-sm-4">
                                        <select class="custom-select custom-select-sm"
                                            :id="'status_' + entidad" name="estado">
                                            <option value="" disabled="" selected=""> Seleccione </option>
                                            <option v-for="(item, index) in arrRptas" :key="index"
                                            :value="item.value"  v-text="item.label"></option>
                                        </select>
                                    </div>
                                </div>
                           </div>
                        </div>

                        <div class="row">
                            <span class="text-info pl-2 ml-1 mb-2 col-form-label" :id="'loading_'+entidad">
                                <Loader/></span>
                        
                            <div class="col-md-10 col-xs-12">
                                <div v-for="(item, index) in preguntas" :key="index" :class="'row d-flex justify-content-center '+(index==0?'my-2':'mb-1')">
                                    <div class="col-md-5 col-xs-6 mt-1">
                                        <h5 v-text="item.nombre" class="title-estrella"></h5>
                                    </div>
                                    <div class="col-md-4 col-xs-6">
                                        <div v-if="item.conRespuesta=='1'">
                                            <p class="clasificacion-estrellas d-flex justify-content-between" @click="capturarPuntuacion">
                                                <input :id="'estrellasP5_'+item.id" type="radio" :name="'estrellasP_'+item.id" value="5"><label :id="'labelEstrellasP5_'+item.id" :for="'estrellasP5_'+item.id" class="label-estrella">&#9733;</label>
                                                <input :id="'estrellasP4_'+item.id" type="radio" :name="'estrellasP_'+item.id" value="4"><label :id="'labelEstrellasP4_'+item.id" :for="'estrellasP4_'+item.id" class="label-estrella">&#9733;</label>
                                                <input :id="'estrellasP3_'+item.id" type="radio" :name="'estrellasP_'+item.id" value="3"><label :id="'labelEstrellasP3_'+item.id" :for="'estrellasP3_'+item.id" class="label-estrella">&#9733;</label>
                                                <input :id="'estrellasP2_'+item.id" type="radio" :name="'estrellasP_'+item.id" value="2"><label :id="'labelEstrellasP2_'+item.id" :for="'estrellasP2_'+item.id" class="label-estrella">&#9733;</label>
                                                <input :id="'estrellasP1_'+item.id" type="radio" :name="'estrellasP_'+item.id" value="1"><label :id="'labelEstrellasP1_'+item.id" :for="'estrellasP1_'+item.id" class="label-estrella">&#9733;</label>
                                            </p>
                                        </div>
                                        <div class="form-group" v-else>
                                            <textarea id="observaciones_encuesta" rows="4" class="form-control form-control-sm no-resize" name="observaciones_encuesta"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      
                        <div class="row">
                            <div class="col-md-8" id="errorMsgs04">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" :id="`btnGuardar_${entidad}`" @click="guardarCamposEncuesta()" class="btn btn-sm btn-success">
                                <i class="mdi mdi-check-bold icon-size"></i> Guardar
                            </button>
                        
                            <button type="button" class="btn btn-danger btn-sm" 
                            @click="cerrarModal"><i class="mdi mdi-close icon-size"></i> Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import Loader from "../Loader";

export default {
    name: 'EncuestaCliente',
    mixins: [misMixins],
    components: {
        Loader
    },
    data () {
        return {
            preguntas: [],
            id: 0,
            cliente: '',
            telefono: '',
            correo_electronico: '',
            placa: '',
            documento: '',
            entidad: 'encuesta_cliente',
            marcamodelo: '',
            kilometraje: '',
            vin: '',
            arrRptas: [
                { value: 'C', label: 'Contestó' },
                { value: 'NC', label: 'No Contestó' },
                { value: 'A', label: 'Apagado' },
            ]
        }
    },
    methods: {
        async getPreguntas () {
            let me = this
            let response = await axios.get('/getpreguntasencuesta')
            if (response.data.estado) {
                me.preguntas = response.data.preguntas
            }
        },
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalEncuestaO').modal('toggle')
            $('#modalEncuestaO').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal (id, documento, cliente, placa, correo, telefono, marcamodelo, kilometraje, vin) {
            let me = this
            let el = document.getElementById(`loading_${me.entidad}`)
            el.classList.remove('d-none')
            document.getElementById('status_'+me.entidad).value = '';
            me.id = id
            me.documento = documento
            me.cliente = cliente
            me.placa = placa
            me.correo_electronico = correo
            me.telefono = telefono
            me.marcamodelo = marcamodelo
            me.kilometraje = kilometraje
            me.vin = vin
            me.preguntas = []
            
            $('#modalEncuestaO').css('z-index','-1')
            document.getElementById('errorMsgs04').innerHTML = ''
            window.setTimeout( function () {
                me.getPreguntas()
                el.classList.add('d-none')
            }, 500)
            // me.arreglo2 = response.data.encabezados
            // me.opciones = response.data.opciones02
            // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
            $('#modalEncuestaO').modal({backdrop: 'static', show: true, keyboard: false})
            $('#modalEncuestaO').css('z-index', '1500')
            $('#header-sec').css('z-index','1')
            $('#aside-sec').css('z-index','1')         
        },
        capturarPuntuacion (e) {
            let me = this
            if (e.target && e.target.tagName == 'LABEL') {
                const nameInput = e.target.control.id
                // console.log('target', e.target.control);
                me.resetBeforeLabels(nameInput)
            }
        },
        resetBeforeLabels (nameInput) {
            let v_i_nameInput = nameInput.split('_')
            let nro_pregunta = v_i_nameInput[0].split('P')

            let nro_seleccionado = parseInt(nro_pregunta[1])
            let id_seleccionado = v_i_nameInput[1]
           
           
            let label_color = 'grey'
            let input_check = false

            // console.log('nro_seleccionado', nro_seleccionado)
            // console.log('color_act', color_act)
            // console.log('label_color', label_color)

            let color_act  = document.getElementById(`labelEstrellasP${nro_seleccionado}_${id_seleccionado}`).style.color

            if (color_act === 'grey') {
                label_color = 'orange'
                input_check = true
            }

            if (label_color === 'grey') {
                for (let i = nro_seleccionado+1; i <= 5; i++) {
                    document.getElementById(`labelEstrellasP${i}_${id_seleccionado}`).style.setProperty('color',label_color)
                    document.getElementById(`estrellasP${i}_${id_seleccionado}`).checked = input_check
                }
            } else {
                for (let i = 1; i <= nro_seleccionado; i++) {
                    document.getElementById(`labelEstrellasP${i}_${id_seleccionado}`).style.setProperty('color',label_color)
                    document.getElementById(`estrellasP${i}_${id_seleccionado}`).checked = input_check
                }
            }
        },
        async guardarCamposEncuesta () {
            let me = this
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>'
            // alert(url)
            let button = document.getElementById(`btnGuardar_${me.entidad}`)
            button.innerHTML = icon + ' Cargando...'
            button.disabled = true
            var form = document.getElementById(`formEnv_${me.entidad}`)
            let data = me.serializeForm(form)
            // console.log('data',data)
            // alert(form.action+'-'+form.method)
            // let formData = new FormData(form)
            // console.log("form", form);
            // console.log('DATA',formData)
            let response = await axios({
                method: form.method,
                url: form.action,
                data: data
            })

            var arreglo = response.data.errores
            let cadena_errors = '';
            Object.values(arreglo).forEach(val => {
                cadena_errors += val + ', '
            })

            if (response.data.estado == false) {
                document.getElementById('errorMsgs04').innerHTML = '<div style = "margin-top:10px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Corregir los Siguientes Errores:</strong> ' + cadena_errors.slice(0, -2) + '</a></div ></div >'
            } else {
                document.getElementById('errorMsgs04').innerHTML = '<div style = "margin-top:10px;"><div class="alert alert-success text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss="alert">&times;</button><strong>' + cadena_errors.slice(0, -1) + '</strong></a></div ></div>'
                this.$parent.$emit('listarOrdenesFinalizadas')
                window.setTimeout( function () {
                    // $('#modalEncuestaO').modal('hide')
                    $('body').removeClass('modal-open')
                    $('.modal-backdrop').remove()
                    $('#modalEncuestaO').modal('toggle')
                    $('#modalEncuestaO').css('z-index', '-1')
                    $('#header-sec').css('z-index','')
                    $('#aside-sec').css('z-index','')        
                }, 1000)
                           
            }
            button.disabled = false
            button.innerHTML = icon + ' Guardar'
           

        }
    
    }
}
</script>
<style scoped>
.modal-header { 
    border-bottom: 1px solid #f2f2f2;
}
.modal {
    overflow: hidden;
}
.modal-content {
    height: 71vh;
    overflow-y: auto;
    overflow-x: hidden;
}
.modal-footer {
    border-top: 1px solid #f2f2f2;
}
.no-resize {
    resize: none;
}
.title-estrella {
    font-weight: 500;
    /* font-size: 14px; */
}
.label-estrella { 
    color:grey;
    font-size: 27px;
    cursor: pointer;
}
input[type = "radio"]{ 
    display:none;
}
.clasificacion-estrellas{
    direction: rtl;/* right to left */
    unicode-bidi: bidi-override;/* bidi de bidireccional */
}
.label-estrella:hover{color:orange;}
.label-estrella:hover ~ .label-estrella{color:orange;}
input[type = "radio"]:checked ~ .label-estrella{color:orange;}
</style>