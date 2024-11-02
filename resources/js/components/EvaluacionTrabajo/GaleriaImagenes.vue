<template>
    <div
        class="modal fade"
        id="modalGaleria"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalGaleriaLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form
                    class="form"
                    id="formEnv2"
                    method="POST"
                    action="/guardaravance"
                >
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGaleriaLabel">
                            Fotos & Videos de Orden:
                            <strong v-text="servicio"></strong>
                            <br />
                            <small
                                v-text="' Placa del Vehículo: ' + placa"
                            ></small>
                        </h5>
                        <button
                            type="button"
                            class="close"
                            @click="cerrarModal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" :value="id" />
                        <input type="hidden" name="_token" :value="token" />
                        <input
                            type="hidden"
                            name="listMultimedia"
                            :value="fileLoading.join(',')"
                        />

                        <div class="row">
                            <div class="col-md-12">
                                <vue-dropzone
                                    ref="miDropzone"
                                    id="dropzone"
                                    @vdropzone-file-added="archivoAgregado"
                                    @vdropzone-success="archivoAgregado02"
                                    @vdropzone-removed-file="archivoRemovido"
                                    @vdropzone-complete="archivoCargado"
                                    :options="dropzoneOptions"
                                ></vue-dropzone>
                            </div>

                            <div class="col-md-12 mt-1">
                                <div class="form-group">
                                    <label for="observaciones"
                                        >Observaciones
                                        <span class="text-success"
                                            >*</span
                                        ></label
                                    >
                                    <textarea
                                        id="observaciones"
                                        class="form-control form-control-sm no-resize"
                                        name="observaciones"
                                        rows="4"
                                        :value="observaciones"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="data-error2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            @click="
                                enviarFormModalAR(
                                    'modalGaleria',
                                    'formEnv2',
                                    'data-error2'
                                )
                            "
                            class="btn btn-success btn-sm"
                            id="btnEnvio"
                        >
                            <i class="mdi mdi-check-bold icon-size"></i>
                            Guardar
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            @click="cerrarModal"
                        >
                            <i class="mdi mdi-close icon-size"></i> Cerrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { misMixins } from "../../mixins/mixin.js";

import vue2Dropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";
// import {_} from 'vue-underscore'

export default {
    name: "DetallesAsignacion",
    mixins: [misMixins],
    components: {
        vueDropzone: vue2Dropzone,
    },
    data() {
        return {
            id: "",
            servicio: "",
            placa: "",
            token: this.$store.state.token,
            observaciones: "",
            contador: 0,
            dropzoneOptions: {
                url: "/cargarmultimedia/" + this.id,
                paramName: "imagen",
                acceptedFiles: "image/*,video/*",
                addRemoveLinks: true,
                // autoProcessQueue: false,
                // timeout: 180000,
                // uploadMultiple: true,
                thumbnailWidth: 280,
                maxFilesize: 15,
                maxFiles: 5,
                headers: { "X-CSRF-TOKEN": this.$store.state.token },
                dictDefaultMessage: "Arrastra Las Fotos & Videos Aquí",
                dictRemoveFile: "Eliminar Archivo",
                duplicateCheck: true,
            },
            fileLoading: [],
            contEliminar: 0,
        };
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalGaleria').modal('toggle')
            $('#modalGaleria').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal(id, servicio, placa) {
            $("#modalGaleria").css("z-index", "-1");
            let me = this;
            me.id = id;
            this.$refs.miDropzone.setOption(
                "url",
                "/cargarmultimedia/" + me.id
            );
            me.servicio = servicio;
            me.placa = placa;
            me.observaciones = "";
            document.getElementById("data-error2").innerHTML = "";
            me.contEliminar = 0;
            this.$refs.miDropzone.removeAllFiles();
            me.contEliminar++;
            // this.$nextTick(() => {
            // });
            // this.$refs.miDropzone.reset()
            // this.$refs.miDropzone.setOption('removeType', 'server')
            await me.cargarMultimedia();
            // console.log(me.fileLoading)

            for (const i in me.fileLoading) {
                const element = me.fileLoading[i];
                this.$refs.miDropzone.manuallyAddFile(element.img, element.url);
            }

            $("#modalGaleria").modal({ backdrop: "static", show: true, keyboard: false });
            $("#modalGaleria").css("z-index", "1500");
            $('#header-sec').css('z-index','1')
            $('#aside-sec').css('z-index','1')     
        },
        async archivoAgregado02(file) {
            // console.log('Ok')
            let response = file.xhr.response;
            // console.log(response)
            if (!file.manuallyAdded) {
                // Cargar Los Archivos
                this.$refs.miDropzone.removeFile(file);
                let element = JSON.parse(response);
                let a = {
                    size: element["tamanio"],
                    name: element["nombre"],
                    type: element["tipo"] == "I" ? "image/" : "video/",
                };
                let url = element["url"];

                this.$refs.miDropzone.manuallyAddFile(a, url);
            }
        },
        archivoCargado(file, error, xhr) {
            // console.log('Agregado por', file)
            // this.$refs.miDropzone.removeFile(file)
            // this.$refs.miDropzone.removeAllFiles(true)
        },
        async archivoAgregado(file, error, xhr) {
            // console.log(xhr)
            // console.log(file)
            // let me = this
            // const formData = new FormData()
            // console.log('tipo:',file.type)
            // formData.append('_token', me.token)
            // if (file.type == 'video/') {
            //     formData.append('imagen', file)
            // } else {
            // formData.append('imagen', file)
            // }
            // let response = await axios({
            //     method: 'post',
            //     url: '/cargarimagen/'+me.id,
            //     data: formData,
            //     cache: false,
            //     headers: { 'Content-Type': 'multipart/form-data,application/octet-stream' }
            // })
            // if (!file.manuallyAdded) {
            //     // Cargar Los Archivos
            //     this.$refs.miDropzone.removeFile(file)
            //     // await me.cargarMultimedia()
            //     // console.log(me.fileLoading)
            //     // for (const i in me.fileLoading) {
            //     //     const element = me.fileLoading[i];
            //     let element = response.data
            //     let a = {size: element.tamanio, name: element.nombre, type: element.tipo == 'I'?'image/':'video/'}
            //     let url = element.url
            //     this.$refs.miDropzone.manuallyAddFile(a, url)
            // }
        },
        async archivoRemovido(file, error, xhr) {
            // alert('Ok')
            // console.log(file, error, xhr)
            let me = this;
            // console.log(me.contEliminar)
            if (me.contEliminar > 0) {
                const formData = new FormData();

                formData.append("_token", me.token);
                formData.append("imagen", file.name);
                // console.log(file)
                let response = await axios({
                    method: "post",
                    url: "/eliminarimagen/" + me.id,
                    data: formData,
                    headers: { "Content-Type": "multipart/form-data" },
                });
                // this.$refs.miDropzone.removeAllFiles(true)
                // await me.cargarMultimedia()
                // for (const i in me.fileLoading) {
                //     const element = me.fileLoading[i];
                //     this.$refs.miDropzone.manuallyAddFile(element.img, element.url);
                // }
            }
        },
        async cargarMultimedia() {
            let me = this;
            me.fileLoading = [];
            let response = await axios({
                method: "get",
                url: "/getmultimedia/" + me.id,
            });

            let arreglo = response.data.arreglo;
            arreglo.forEach((element) => {
                let a = {
                    size: element.tamanio,
                    name: element.nombre,
                    type: element.tipo == "I" ? "image/" : "video/",
                };
                let url = element.url;
                me.fileLoading.push({ img: a, url: url });
                // this.$refs.miDropzone.manuallyAddFile(a, url);
            });

            me.observaciones = response.data.observaciones;
        },
    },
    created() {
        let me = this;
        me.fileLoading = [];
    },
    activated: function () {
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    },
    deactivated: function () {},
    mounted() {},
    beforeDestroy: function () {
        let me = this;
        me.fileLoading = [];
    },
};
</script>
<style scoped>
.modal-header {
    border-bottom: 1px solid #f2f2f2;
}
.modal-footer {
    border-top: 1px solid #f2f2f2;
}
thead {
    position: sticky;
    top: 0;
}
tfoot {
    position: sticky;
    bottom: 0;
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
    border-radius: 30%;
    vertical-align: middle;
    position: relative;
    top: -2px;
}

/* appearance for checked radiobutton */
input[type="checkbox"]:checked {
    background-color: teal;
    cursor: pointer;
}
table {
    cursor: pointer;
}
.no-resize {
    resize: none;
}
</style>
