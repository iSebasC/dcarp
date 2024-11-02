<template>
    <div class="modal fade" id="modalCheck" tabindex="-1" role="dialog" aria-labelledby="modalCheckLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalCheckLabel">CheckList de Inventario</h5>
                <button type="button" class="close" @click="cerrarModal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive px-1" id="tabla-inv" style="height:300px; overflow-y:scroll;">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Ok, Buen Estado</th>
                                    <th class="text-center">Falta, Mal Estado</th>
                                </tr>
                            </thead>
                            <tbody v-for="(item, index) in detalles" :key="index">
                                <tr>
                                    <td colspan="3"><h6><strong v-text="item.header.nombre"></strong></h6></td>
                                </tr>
                                <tr v-for="(item2, index2) in item.body" :key="index2">
                                    <td><h6 v-text="item2.nombre"></h6></td>
                                    <td class="text-center">
                                        <input class="mr-1 chkB" @change="obtenerValor(item2.id,'S')" type="radio" :name="'chkSituacion_'+item2.id" :id="'chkBueno_'+item2.id">
                                    </td>
                                    <td class="text-center">
                                        <input class="mr-1 chkM" @change="obtenerValor(item2.id, 'N')" type="radio" :name="'chkSituacion_'+item2.id" :id="'chkMalo_'+item2.id">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                  
                    <div class="row mt-3 mx-auto">
                        <div class="col-md-5 ml-2 col-xs-12">
                            <div class="form-group">
                                <label for="dropzone" class="col-form-label">Multimedia <span class="text-danger">*</span></label>
                                <vue-dropzone ref="miDropzone" 
                                    id="dropzone" 
                                    @vdropzone-file-added="archivoAgregado"
                                    @vdropzone-success="archivoAgregado02"
                                    @vdropzone-removed-file="archivoRemovido"
                                    @vdropzone-complete="archivoCargado"
                                    :options="dropzoneOptions">
                                </vue-dropzone>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="observaciones" class="col-form-label">Observaciones <span class="text-success">*</span></label>
                                <textarea id="observaciones" rows="7" class="form-control form-control-sm no-resize" name="observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 col-xs-12 ml-xs-2 ml-lg-3">
                            <label for="draw-canvas" class="col-form-label">Firma del Cliente <span class="text-danger">*</span></label>
                            <br>
                            <canvas id="draw-canvas" width="300" height="250">
                                No tienes un buen navegador.
                            </canvas>
                        </div>
                  
                        <div class="col-md-3 mt-3 ml-xs-0 ml-5">
                            <img v-if="verImagen" id="draw-image" class="ml-md-5" :src="firma != null?firma.url:''" alt="Tu Imagen aparecera Aqui!"/>
                            <input type="hidden" id="draw-dataUrl" />
                        </div>
                  
                    </div>
                    <div class="row">
                        <div class="col-md-3 ml-3">
                            <!--<input type="button" class="button" id="draw-submitBtn" value="Crear Imagen"></input>-->
                            <button type="button" class="btn btn-sm btn-danger" id="draw-clearBtn">Borrar Firma</button>
                            <button type="button" style="display:none;" id="btn-generar"></button>
                            <!--
                                    <label>Color</label>
                                    <input type="color" id="color">
                                    <label>Tamaño Puntero</label>
                                    <input type="range" id="puntero" min="1" default="1" max="5" width="10%">
                            -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8" id="errorMsgs">

                        </div>
                    </div>

                    <!--<div class="row">
                        <div class="col-md-12">
                            <textarea id="draw-dataUrl" class="form-control" rows="5">Para los que saben que es esto:</textarea>
                        </div>
                    </div>
                    <br/>
                    <div class="contenedor">
                        <div class="col-md-12">
                            <img id="draw-image" src="" alt="Tu Imagen aparecera Aqui!"/>
                        </div>
                    </div>-->
                    
                    <div class="modal-footer">
                        <button type="button" id="btnGuardar"  @click="guardarCampos()" class="btn btn-sm btn-success">
                            <i class="mdi mdi-check-bold icon-size"></i> Guardar
                        </button>
                       
                        <button type="button" class="btn btn-danger btn-sm" @click="cerrarModal">
                            <i class="mdi mdi-close icon-size"></i> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
    name: 'CheckListInventario',
    mixins: [misMixins],
    components: {
      vueDropzone: vue2Dropzone
    },
    data () {
        return {
            detalles: [],
            firmaDigital: '',
            token: this.$store.state.token,
            contEliminar: 0,
            fileLoading: [],
            arrRptas: [],
            cantElementos: 0,
            marcados: [],
            firma: {},
            verImagen: false,
            dropzoneOptions: {
                url: '/cargartemporal',
                paramName: 'imagen',
                acceptedFiles:'image/*,video/*',
                addRemoveLinks: true,
                // autoProcessQueue: false,
                // timeout: 180000,
                // uploadMultiple: true,
                thumbnailWidth: 200,
                maxFilesize: 2,
                maxFiles:5,
                headers: { 'X-CSRF-TOKEN': this.$store.state.token },
                dictDefaultMessage:'Arrastra Las Fotos & Videos Aquí',
                dictRemoveFile: 'Eliminar Archivo',
                duplicateCheck: true
            },
        }
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();
            $('#modalCheck').modal('toggle')
            $('#modalCheck').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        obtenerValor (id, valor) {
            let me = this
            let band = false
            if (me.arrRptas != null) {
                me.arrRptas.forEach ( element => {
                    if (element.id == id) {
                        band = true
                        element.valor = valor
                    }
                })
            } else {
                me.arrRptas = []
            }

            if (!band) {
                me.arrRptas.push({'id': id, 'valor': valor})
            }
        },
        async archivoAgregado02 (file) { 
            let response = file.xhr.response 
            if (!file.manuallyAdded) {
                // Cargar Los Archivos
                this.$refs.miDropzone.removeFile(file)
                let element = JSON.parse(response)
                let a = {size: element['tamanio'], name: element['nombre'], type: element['tipo'] == 'I'?'image/':'video/'}
                let url = element['url']
                
                this.$refs.miDropzone.manuallyAddFile(a, url)
            }
        },
        archivoCargado (file, error, xhr) {
        },
        async archivoAgregado (file,  error, xhr) {
        }, 
        async archivoRemovido (file, error, xhr) {
            // alert('Ok')
            // console.log(file, error, xhr)
            let me = this
            // console.log(me.contEliminar)
            if (me.contEliminar > 0) {
                const formData = new FormData();
                
                formData.append('_token', me.token)
                formData.append('imagen', file.name)
                // console.log(file)
                let response = await axios({
                    method: 'post',
                    url: '/eliminartemporal',
                    data: formData,
                    headers: {'Content-Type': 'multipart/form-data' }
                })
            }
        },
     
        async showModal (attr) {
            $('#modalCheck').css('z-index','-1')
            let me = this
            me.firma = null
            me.detalles = []
            me.cantElementos = 0
            document.getElementById('draw-clearBtn').click()
            // me.id = attr
            // alert(me.id)
            // me.nombre = attr2
            // $('#texto').text(me.id)
            // alert (me.id)
            // me.mostrarModal = ne.isVisible()

            // alert('....' + me.id)
            // if (me.id !== 'undefined') {
                let response = await axios({
                method: 'post',
                    url: '/getcheckinventario',
                    data: {
                        '_token': me.token
                    }
                })
                me.detalles = response.data.detalles
                me.marcados = JSON.parse(response.data.rptas);
                me.marcados = JSON.parse(localStorage.getItem('rptas_marcadas'))


                // console.log('Arr: ', JSON.parse(response.data.rptas));

                me.detalles.forEach( element => {
                    me.cantElementos += element.cantSubs
                })
                // console.log(me.cantElementos)
                me.contEliminar = 0
                this.$refs.miDropzone.removeAllFiles()
                me.contEliminar++
            
                await me.cargarMultimedia()
                // console.log(me.fileLoading)

                for (const i in me.fileLoading) {
                    const element = me.fileLoading[i];
                    this.$refs.miDropzone.manuallyAddFile(element.img, element.url);
                }

                var drawImage = document.getElementById("draw-image");
                me.verImagen = false
                // console.log(me.firma)
                if (me.firma.url != null) {
                    me.verImagen = true
                    // drawImage.setAttribute()
                }
                // console.log(me.marcardos)
                if (me.marcados != '[]') {
                    me.marcarPorDefault()
                }
                // me.arreglo2 = response.data.encabezados
                // me.opciones = response.data.opciones02
                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                $('#modalCheck').modal({backdrop: 'static', show: true, keyboard: false })
                $('#modalCheck').css('z-index', '1500')
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')       
                // $('.modal-backdrop').css('z-index','1')
            // }
                  // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        async cargarMultimedia () {
            let me = this
            me.fileLoading = []
            let response = await axios({
                method: 'get',
                url: '/gettemporal'
            })
            
            let arreglo = response.data.arreglo
            arreglo.forEach(element => {
                let a = {size: element.tamanio, name: element.nombre, type: element.tipo =='I'?'image/':'video/'}
                let url = element.url
                me.fileLoading.push({'img': a, 'url': url })
               // this.$refs.miDropzone.manuallyAddFile(a, url);
            })

            me.firma = response.data.firma
        },
        marcarPorDefault () {
            let me = this
            // console.log(me.marcados);
            me.arrRptas = me.marcados
            console.log('arrRptas', me.arrRptas);

            if (me.arrRptas != null) {
                me.arrRptas.forEach (element => {
                    if (element.valor == 'S') {
                        document.getElementById('chkBueno_'+element.id).checked = true
                    } else {
                        document.getElementById('chkMalo_'+element.id).checked = true
                    }
                })
            }
        },
        async guardarCampos () {
            let me = this
            let icon = '<i class="mdi mdi-check-bold icon-size"></i>'
            let observacion = document.getElementById('observaciones').value
            // alert(url)
            let button = document.getElementById(`btnGuardar`)
            button.innerHTML = icon + ' Cargando...'
            button.disabled = true
           
            // alert('Ok')
            document.getElementById('btn-generar').click()
            me.firmaDigital = document.getElementById('draw-dataUrl').value
            // console.log('firma', this.firmaDigital)
            // console.log(this.arrRptas);
            // let cantidad = me.arrRptas.length;
            let cantidad = (me.arrRptas != null?me.arrRptas.length:0)
            // console.log("cantidad: "+cantidad+" total: "+me.cantElementos);
            // this.arrRptas.forEach(element => {
            //     cantidad++
            // })
            // alert(this.cantElementos + '  -   '+cantidad)
            let fdefault = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAdYAAAD6CAYAAAAcA2ajAAAKHUlEQVR4Xu3VMQ0AAAzDsJU/6ZHI6QHoYU3KzhEgQIAAAQKZwLIlQwQIECBAgMAJqycgQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQICKsfIECAAAECoYCwhpimCBAgQICAsPoBAgQIECAQCghriGmKAAECBAgIqx8gQIAAAQKhgLCGmKYIECBAgICw+gECBAgQIBAKCGuIaYoAAQIECAirHyBAgAABAqGAsIaYpggQIECAgLD6AQIECBAgEAoIa4hpigABAgQIPObGAPuo15IPAAAAAElFTkSuQmCC'

            if (me.cantElementos == cantidad) {
                if (me.firmaDigital != fdefault) { 
                    let response = await axios({
                        method: 'post',
                        url: '/guardarfirma', 
                        data: {
                            'firma': me.firmaDigital,
                            'rptas': JSON.stringify(me.arrRptas),
                            'observacion': observacion,
                            '_token': me.token 
                        },
                        cache: false
                    })

                    if (response.data.estado) {
                        window.setTimeout( function () {
                            $('#modalCheck').modal('hide')
                            $('body').removeClass('modal-open')
                            localStorage.setItem('rptas_marcadas', JSON.stringify(me.arrRptas))
                            document.getElementById(`listItemCheckList`).value= localStorage.getItem('rptas_marcadas')
                            $('.modal-backdrop').remove()
                        }, 1000)
                    } else {
                        document.getElementById('errorMsgs').innerHTML =  '<div style = "margin-top:10px; margin-left:42px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Indique Firma del Cliente</strong></a></div ></div>'
                    }
                } else {
                    document.getElementById('errorMsgs').innerHTML =  '<div style = "margin-top:10px; margin-left:42px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Indique Firma del Cliente</strong></a></div ></div>'
                }
            } else {
                document.getElementById('errorMsgs').innerHTML =  '<div style = "margin-top:10px; margin-left:42px;" > <div style="background-color: #ffdbdf !important;" class="alert alert-danger text-left alert-dismissible" role="alert"><button type = "button" class="close" data-dismiss = "alert">&times;</button><strong>Marque todos los Campos del CheckList, le faltan '+ parseInt(this.cantElementos-cantidad)+' Respuestas</strong></a></div ></div>'
            }

            button.disabled = false
            button.innerHTML = icon + ' Guardar'
    
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
        /*
            El siguiente codigo en JS Contiene mucho codigo
            de las siguietes 3 fuentes:
            https://stipaltamar.github.io/dibujoCanvas/
            https://developer.mozilla.org/samples/domref/touchevents.html - https://developer.mozilla.org/es/docs/DOM/Touch_events
            http://bencentra.com/canvas/signature/signature.html - https://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
        */

        (function() { // Comenzamos una funcion auto-ejecutable

	    // Obtenenemos un intervalo regular(Tiempo) en la pamtalla
        window.requestAnimFrame = (function (callback) {
            return window.requestAnimationFrame ||
                    window.webkitRequestAnimationFrame ||
                    window.mozRequestAnimationFrame ||
                    window.oRequestAnimationFrame ||
                    window.msRequestAnimaitonFrame ||
                    function (callback) {
                        window.setTimeout(callback, 1000/60);
                      // Retrasa la ejecucion de la funcion para mejorar la experiencia
                    };
            })();

            // Traemos el canvas mediante el id del elemento html
            var canvas = document.getElementById("draw-canvas");
            var ctx = canvas.getContext("2d");


            // Mandamos llamar a los Elementos interactivos de la Interfaz HTML
            var drawText = document.getElementById("draw-dataUrl");
            var drawImage = document.getElementById("draw-image");
            var clearBtn = document.getElementById("draw-clearBtn");
            var submitBtn = document.getElementById("btn-generar");
            clearBtn.addEventListener("click", function (e) {
                // Definimos que pasa cuando el boton draw-clearBtn es pulsado
                clearCanvas();
                // drawImage.src = ""
            }, false);
                // Definimos que pasa cuando el boton draw-submitBtn es pulsado
            submitBtn.addEventListener("click", function (e) {
                // let cantidad = 0
                // this.arrRptas.forEach(element => {
                //     cantidad++
                // })
                document.getElementById('errorMsgs').innerHTML = ''
                var dataUrl = canvas.toDataURL('image/png')
                // var output = img.replace(/^data:image\/(png|jpg);base64,/, "");

                drawText.value = dataUrl
                // console.log('firma Dig', this.firmaDigital)
                    // drawText.innerHTML = dataUrl;
                    // drawImage.setAttribute("src", dataUrl);
            }, false);

            // Activamos MouseEvent para nuestra pagina
            var drawing = false;
            var mousePos = { x:0, y:0 };
            var lastPos = mousePos;
            canvas.addEventListener("mousedown", function (e)
            {
            /*
            Mas alla de solo llamar a una funcion, usamos function (e){...}
            para mas versatilidad cuando ocurre un evento
            */
                var tint = '#060D7F'//document.getElementById("color");
                var punta = '2'//document.getElementById("puntero");
                  
                // var tint = document.getElementById("color");
                // var punta = document.getElementById("puntero");
                // console.log(e);
                drawing = true;
                lastPos = getMousePos(canvas, e);
            }, false);
            canvas.addEventListener("mouseup", function (e)
            {
               drawing = false;
            }, false);
            canvas.addEventListener("mousemove", function (e)
            {
                mousePos = getMousePos(canvas, e);
            }, false);

            // Activamos touchEvent para nuestra pagina
            canvas.addEventListener("touchstart", function (e) {
                mousePos = getTouchPos(canvas, e);
            // console.log(mousePos);
            e.preventDefault(); // Prevent scrolling when touching the canvas
                var touch = e.touches[0];
                var mouseEvent = new MouseEvent("mousedown", {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }, false);
            canvas.addEventListener("touchend", function (e) {
            e.preventDefault(); // Prevent scrolling when touching the canvas
                var mouseEvent = new MouseEvent("mouseup", {});
                canvas.dispatchEvent(mouseEvent);
            }, false);
        canvas.addEventListener("touchleave", function (e) {
            // Realiza el mismo proceso que touchend en caso de que el dedo se deslice fuera del canvas
            e.preventDefault(); // Prevent scrolling when touching the canvas
            var mouseEvent = new MouseEvent("mouseup", {});
            canvas.dispatchEvent(mouseEvent);
        }, false);
            canvas.addEventListener("touchmove", function (e) {
            e.preventDefault(); // Prevent scrolling when touching the canvas
                var touch = e.touches[0];
                var mouseEvent = new MouseEvent("mousemove", {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }, false);

            // Get the position of the mouse relative to the canvas
            function getMousePos(canvasDom, mouseEvent) {
                var rect = canvasDom.getBoundingClientRect();
                /*
                Devuelve el tamaño de un elemento y su posición relativa respecto
                a la ventana de visualización (viewport).
                */
                return {
                    x: mouseEvent.clientX - rect.left,
                    y: mouseEvent.clientY - rect.top
                };
            }

            // Get the position of a touch relative to the canvas
            function getTouchPos(canvasDom, touchEvent) {
                var rect = canvasDom.getBoundingClientRect();
                // console.log(touchEvent);
                /*
                Devuelve el tamaño de un elemento y su posición relativa respecto
                a la ventana de visualización (viewport).
                */
                return {
                    x: touchEvent.touches[0].clientX - rect.left, // Popiedad de todo evento Touch
                    y: touchEvent.touches[0].clientY - rect.top
                };
            }

            // Draw to the canvas
            function renderCanvas() {
                if (drawing) {
                    var tint = '#060D7F'//document.getElementById("color");
                    var punta = '2'//document.getElementById("puntero");
                    ctx.strokeStyle = tint;
                    ctx.beginPath();
                    ctx.moveTo(lastPos.x, lastPos.y);
                    ctx.lineTo(mousePos.x, mousePos.y);
                    // console.log(punta);
                    ctx.lineWidth = punta;
                    ctx.stroke();
                    ctx.closePath();
                    lastPos = mousePos;
                }
            }

            function clearCanvas() {
                canvas.width = canvas.width;
            }

            // Allow for animation
            (function drawLoop () {
                requestAnimFrame(drawLoop);
                renderCanvas();
            })();

        })();

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
@media (min-width: 576px) {
    #tabla-inv {
        overflow-x:scroll;
    }
}

@media (min-width: 768px) {
    #tabla-inv {
        overflow-x:hidden;
    }
}

@media (min-width: 992px) { 
    #tabla-inv {
        overflow-x:hidden;
    }
}

@media (min-width: 1200px) { 
    #tabla-inv {
        overflow-x:hidden;
    }
}

.modal-header { 
    border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
    border-top: 1px solid #f2f2f2;
}
.no-resize {
    resize: none;
}
.chkB:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: #d1d3d1;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
    cursor:pointer;
}

.chkB:checked:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: green;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
}
.chkM:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: #d1d3d1;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
    cursor:pointer;
}

.chkM:checked:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    top: -2px;
    left: -1px;
    position: relative;
    background-color: red;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
}

#draw-canvas {
  border: 2px dotted #0E5CB9;
  border-radius: 5px;
  cursor: crosshair;
}

</style>