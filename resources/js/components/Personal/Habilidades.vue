<template>
    <div
        class="modal fade"
        id="modalHabilidades"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalHabilidadesLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form
                    class="form"
                    :id="'formEnv_' + entidad"
                    method="POST"
                    action="/guardarhabilidades"
                >
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHabilidadesLabel">
                            Habilidades del Personal:
                            <strong v-text="this.$attrs.personal"></strong>
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
                    <div class="modal-body overflow-details h-78">
                        <input type="hidden" :value="id" name="id" />
                        <input
                            type="hidden"
                            :value="this.$store.state.token"
                            name="_token"
                        />
                        <input
                            type="hidden"
                            :value="opciones"
                            name="opciones"
                            id="opciones"
                        />
                        <div
                            v-for="(item, index) in arreglo"
                            :class="'row ' + (index != 0 ? 'mt-1' : '')"
                            :key="item.categoria.id"
                        >
                            <div class="col-md-12">
                                <h5>
                                    <i class="mdi mdi-vector-bezier mr-1"></i
                                    ><strong
                                        v-text="item.categoria.nombre"
                                    ></strong>
                                </h5>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div
                                        v-for="(
                                            item2, index2
                                        ) in item.servicios"
                                        :key="index2"
                                        class="col-md-4 col-xs-12"
                                    >
                                        <input
                                            class="mr-1 my-auto"
                                            @change="obtenerValor(item2.id)"
                                            type="checkbox"
                                            :name="'chk_' + item2.id"
                                            :id="
                                                'chk_' +
                                                entidad +
                                                '_' +
                                                item2.id
                                            "
                                            :checked="
                                                item2.estado == 'S'
                                                    ? 'checked'
                                                    : ''
                                            "
                                        />
                                        <label
                                            class="col-10"
                                            :for="
                                                'chk_' +
                                                entidad +
                                                '_' +
                                                item2.id
                                            "
                                            ><small
                                                v-text="item2.servicio"
                                            ></small
                                        ></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="col-md-12"
                            :id="'data-error_' + entidad"
                        ></div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            @click="cerrarModal"
                        >
                            <i class="mdi mdi-close icon-size"></i> Cancelar
                        </button>

                        <button
                            type="button"
                            @click="enviarFormM('modalHabilidades', entidad)"
                            :id="'btnEnvio_' + entidad"
                            class="btn btn-success btn-sm"
                        >
                            <i class="mdi mdi-check-bold icon-size"></i>
                            Guardar
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

export default {
    name: "HabilidadPersonal",
    mixins: [misMixins],
    data() {
        return {
            id: this.$attrs.idmodal,
            nombre: this.$attrs.personal,
            arreglo: [],
            opciones: "",
            url: "/personal",
            token: this.$store.state.token,
            entidad: "habilidad",
        };
    },
    methods: {
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalHabilidades').modal('toggle')
            $('#modalHabilidades').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal(attr) {
            let me = this;
            document.getElementById("data-error_" + me.entidad).innerHTML = "";
            $("#modalHabilidades").css("z-index", "-1");
            me.id = attr;
            me.arreglo = [];
            me.opciones = "";
            if (me.id > 0) {
                let response = await axios({
                    method: "post",
                    url: "/habilidades/" + me.id,
                    data: {
                        _token: me.token,
                    },
                });

                me.arreglo = response.data.opciones;
                me.opciones = response.data.marcadas;
                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                $("#modalHabilidades").modal({
                    backdrop: "static",
                    show: true,
                    keyboard: false
                });
                $("#modalHabilidades").css("z-index", "1500");
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')

                $(".modal-backdrop").css("z-index", "1");
            }
            // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        obtenerValor(valor) {
            let me = this;
            var e = document.getElementById("chk_" + me.entidad + "_" + valor);
            let arreglo = me.opciones.split(",");
            if (e.checked) {
                arreglo.push(valor);
            } else {
                for (let i = 0; i < arreglo.length; i++) {
                    if (arreglo[i] == valor) {
                        arreglo.splice(i, 1);
                    }
                }
            }
            me.opciones = arreglo.join();
            // alert(e.checked)
            // console.log(me.opciones)
        },
    },
    created() {
        let me = this;
        me.arreglo = [];
        me.opciones = "";
    },
    activated: function () {
        this.$store.state.mostrarModal = !this.$store.state.mostrarModal;
    },
    deactivated: function () {},
    mounted() {},
    beforeDestroy: function () {
        let me = this;
        me.arreglo = [];
        me.opciones = "";
        // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
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
.h-78 {
    height: 78vh !important;
}
.overflow-details {
    overflow-y: auto !important;
    overflow-x: hidden !important;
}
</style>
