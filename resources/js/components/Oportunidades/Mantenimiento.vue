<template>
    <div
        class="modal fade"
        id="modalOportunidad"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalOportunidadLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form
                    class="form"
                    :id="'formEnv_' + entidad"
                    method="POST"
                    action="/guardaroportunidad"
                >
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalOportunidadLabel">
                            {{textTitle}}
                            <!-- <strong v-text="this.$attrs.personal"></strong> -->
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
                        <input type="hidden" :value="idprospecto" name="prospectoId" />
                        <input type="hidden" :value="id" name="id" />
                        <input
                            type="hidden" :value="this.$store.state.token"  name="_token" >
                        <div class="row mt-1">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label :for="'concepto_' + entidad"
                                        >Concepto <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="text"
                                        :id="'concepto_' + entidad"
                                        class="form-control form-control-sm"
                                        name="concepto"
                                        maxlength="255"
                                        readonly=""
                                        :value="concepto"
                                    />
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label :for="'cliente_' + entidad"
                                        >Cliente <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="text"
                                        :id="'cliente_' + entidad"
                                        class="form-control form-control-sm"
                                        name="cliente"
                                        maxlength="255"
                                        readonly=""
                                        :value="cliente"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'select_linea_'+entidad">Línea <span class="text-danger">*</span></label>
                                    <select name="linea" class="custom-select custom-select-sm" 
                                        :id="'select_linea_'+entidad" disabled="">
                                        <option value="" selected="" disabled="">Seleccione</option>
                                        <option v-for="f in lineas" :selected="f.id==lineaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'monto_' + entidad"
                                        >Monto <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="number"
                                        :id="'monto_' + entidad"
                                        class="form-control form-control-sm"
                                        name="monto"
                                        step="0.01"
                                    />
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'select_moneda_'+entidad">Moneda <span class="text-danger">*</span></label>
                                    <select name="moneda" class="custom-select custom-select-sm" 
                                        :id="'select_moneda_'+entidad">
                                        <option value="" selected="" disabled="">Seleccione</option>
                                        <option v-for="f in monedas" :selected="f.id==monedaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group">
                                    <label :for="'select_certeza_'+entidad">Certeza <span class="text-danger">*</span></label>
                                    <select name="certeza" class="custom-select custom-select-sm" 
                                        :id="'select_certeza_'+entidad">
                                        <option value="" selected="" disabled="">Seleccione</option>
                                        <option v-for="f in certezas" :selected="f.id==certezaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'fecha_cierre_' + entidad"
                                        >Fecha Estimada de Cierre <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="date"
                                        :id="'fecha_cierre_' + entidad"
                                        class="form-control form-control-sm"
                                        name="fecha_cierre"
                                    />
                                </div>
                            </div>

                            <div class="col-md-8 col-xs-12">
                                <div class="form-group ml-2">
                                    <label :for="'obsequio_'+entidad">Obsequios <span class="text-danger">*</span></label>
                                    <vue-typeahead-bootstrap
                                    :id="'obsequio_'+entidad"
                                    :ieCloseFix="false"
                                    inputClass="form-control-sm input_obsequios"
                                    placeholder="Presione Enter para agregar"
                                    v-model="query"
                                    :data="obsequios"
                                    :serializer="(item) => item.obsequio"
                                    @hit="selectObsequio = $event"
                                    @input="buscarObsequios" 
                                    @keyup.delete="eliminaObsequio">
                                    </vue-typeahead-bootstrap>
                                </div>
                                
                                <div class="my-0 ml-2 mb-4">
                                    <a v-for="(f, index) in arrObsequios"
                                        :key="index" class="text-muted ml-0 mr-3">
                                        <span class="text-info">
                                            <strong v-text="f.obsequio"></strong>
                                        </span>
                                        <a href="javascript:void(0)" class="text-danger" 
                                            @click="eliminar(f.id)">
                                            <strong>x</strong>
                                        </a>
                                    </a>
                                </div>
                                <input type="hidden" name="arrObsequios" :value="arrObsequios.map(objeto => objeto.id)">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group">
                                    <label :for="'select_fase_'+entidad">Fase <span class="text-danger">*</span></label>
                                    <select name="fase" class="custom-select custom-select-sm" 
                                        :id="'select_fase_'+entidad" :disabled="disabledFase">
                                        <option value="" selected="" disabled="">Seleccione</option>
                                        <option v-for="f in fases" :key="f.id" :value="f.id" :selected="f.id==faseId" > {{f.nombre}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 col-xs-12">
                                <div class="form-group ml-2">
                                    <label :for="'adicional_'+entidad">Adicionales <span class="text-danger">*</span></label>
                                    <vue-typeahead-bootstrap
                                    :id="'adicional_'+entidad"
                                    :ieCloseFix="false"
                                    inputClass="form-control-sm input_adicionales"
                                    placeholder="Presione Enter para agregar"
                                    v-model="query2"
                                    :data="adicionales"
                                    :serializer="(item) => item.adicional"
                                    @hit="selectAdicional = $event"
                                    @input="buscarAdicionales" 
                                    @keyup.delete="eliminaAdicional">
                                    </vue-typeahead-bootstrap>
                                </div>
                                
                                <div class="my-0 ml-2 mb-4">
                                    <a v-for="(f, index) in arrAdicionales"
                                        :key="index" class="text-muted ml-0 mr-3">
                                        <span class="text-info">
                                            <strong v-text="f.adicional"></strong>
                                        </span>
                                        <a href="javascript:void(0)" class="text-danger" 
                                            @click="eliminarA(f.id)">
                                            <strong>x</strong>
                                        </a>
                                    </a>
                                </div>
                                <input type="hidden" name="arrAdicionales" :value="arrAdicionales.map(objeto => objeto.id)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" :id="'data-error_' + entidad"></div>
                        </div>
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
                            @click="enviarFormM('modalOportunidad', entidad)"
                            :id="'btnEnvio_' + entidad"
                            class="btn btn-success btn-sm"
                        >
                            <i class="mdi mdi-check-bold icon-size"></i>
                            <span v-text="textOperacion"></span>
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
    name: "MantenimientoOportunidad",
    mixins: [misMixins],
    data() {
        return {
            idprospecto: 0,
            id: 0,
            concepto: '',
            cliente: '',
            url: "/personal",
            token: this.$store.state.token,
            disabledFase: true,
            lineas: [
                {id: 'L', nombre: 'Ligeros'},
                {id: 'P', nombre: 'Pesados'}
            ],
            monedas: [
                {id:'PEN', nombre: 'Soles'},
                {id: 'USD', nombre:'Dólares'}
            ],
            certezas: [
                { id:'P', nombre: '10% Poco Interesado' },
                { id:'M', nombre: '50% Medio Interesado' },
                { id:'I', nombre: '90% Muy Interesado' }
            ],
            fases:[
                { id:'C', nombre: 'En Cotización' },
                { id:'N', nombre: 'En Negociación' },
                { id:'I', nombre: 'Pago Inicial' }      
            ],
            lineaId: '',
            faseId: 'N',
            monedaId: 'USD',
            certezaId: '',
            entidad: "mant_oportunidad",
            query: '',
            query2: '',
            selectObsequio: null,
            selectAdicional: null,
            obsequios:[],
            adicionales: [],
            arrObsequios: [],
            arrAdicionales: [],
            textOperacion: 'Guardar',
            textTitle: ''
        };
    },
    watch: {
        query() {
            let me=this
            if(me.selectObsequio==null){
                // me.autoId = 0
                // me.marcaId = 0
                // me.modeloId = 0
                // me.marcaAuto = ''
                // me.modeloAuto = ''
            }
        },
        selectObsequio: function() {
            let me = this;
            if (me.selectObsequio != null) {
                let nvoArreglo = me.arrObsequios.filter(objeto => objeto.id == me.selectObsequio.id);
                
                if (nvoArreglo.length == 0) {
                    me.arrObsequios.push(me.selectObsequio);
                    me.query = '';
                    me.selectObsequio = null;
                }
                document.getElementsByClassName('input_obsequios')[0].focus();
                // console.log('arrObsequios', me.arrObsequios)
                // me.autoId = me.selectObsequio.id
                // me.marcaAuto = me.selectObsequio.marca
                // me.modeloAuto = me.selectObsequio.modelo

                // me.marcaId = me.selectObsequio.marcaId
                // me.modeloId = me.selectObsequio.modeloId
            } 
        },
        query2() {
            let me=this
            if(me.selectAdicional==null){
            
            }
        },
        selectAdicional: function() {
            let me = this;
            if (me.selectAdicional != null) {
                let nvoArreglo = me.arrAdicionales.filter(objeto => objeto.id == me.selectAdicional.id);
                
                if (nvoArreglo.length == 0) {
                    me.arrAdicionales.push(me.selectAdicional);
                    me.query2 = '';
                    me.selectAdicional = null;
                }
                document.getElementsByClassName('input_adicionales')[0].focus();
            } 
        },
    },
    methods: {
        async buscarObsequios () {
            let me = this;
            
            let response = await axios({
                method: "POST",
                url: '/buscarobsequios',
                data: {
                    query: me.query
                },
            });
            me.obsequios = response.data.obsequios;
        },
        async buscarAdicionales () {
            let me = this;
            
            let response = await axios({
                method: "POST",
                url: '/buscaradicionales',
                data: {
                    query: me.query2
                },
            });
            me.adicionales = response.data.adicionales;
        },
        eliminaObsequio (){
            this.selectObsequio=null
        },
        eliminaAdicional (){
            this.selectAdicional=null
        },
        cerrarModal () {
            // $('.fade').remove();
            // $('body').removeClass('modal-open');
            // $('.modal-backdrop').remove();

            $('#modalOportunidad').modal('toggle')
            $('#modalOportunidad').css('z-index', '-1')
            $('#header-sec').css('z-index','')
            $('#aside-sec').css('z-index','')         
        },
        async showModal(idprospecto, id, accionId='C') {
            let me = this;
            document.getElementById("data-error_" + me.entidad).innerHTML = "";
            me.idprospecto = idprospecto;
            me.id = id;
            me.arrObsequios = [];
            me.arrAdicionales = [];
            if (accionId == 'E') {
                me.textTitle = "Editar Oportunidad";
            } else {
                me.textTitle = "Convertir a Oportunidad";
            }

            if (me.idprospecto > 0 || me.id > 0) {
                if (me.idprospecto > 0 && me.id == 0) {
                    let respuesta = await axios.get('/obtenerprospecto/'+me.idprospecto)
                    if (respuesta.data.estado) {
                        let prospecto = respuesta.data.prospecto;
                        me.disabledFase = true;
                        me.concepto = `${prospecto.marca} ${prospecto.modelo}`;
                        me.cliente  = `${prospecto.tipodocumento == 'RUC'? prospecto.razonSocial: prospecto.apellidos +' ' + prospecto.nombres }`; 
                        me.lineaId = prospecto.linea;
                        me.faseId = 'N';
                        me.monedaId = 'USD';
                        me.certezaId = '';
                        me.textOperacion = 'Guardar';
                        document.getElementById('fecha_cierre_'+me.entidad).value = '';
                        document.getElementById('monto_'+me.entidad).value = '';
                   
                    }
                } else {
                    let respuesta = await axios.get('/obteneroportunidad/'+me.id)
                    if (respuesta.data.estado) {
                        let oportunidad = respuesta.data.oportunidad;
                        me.arrObsequios = respuesta.data.obsequios;
                        me.arrAdicionales = respuesta.data.adicionales;

                        me.disabledFase = false;
                        me.concepto = oportunidad.concepto;
                        me.cliente  = oportunidad.cliente; 
                        me.lineaId  = oportunidad.linea;
                        me.faseId   = oportunidad.fase;
                        me.certezaId = oportunidad.certeza;
                        me.monedaId = oportunidad.moneda;
                        me.textOperacion = 'Actualizar';
                       
                        document.getElementById('fecha_cierre_'+me.entidad).value = oportunidad.fechaCierre;
                        document.getElementById('monto_'+me.entidad).value = oportunidad.monto;

                
                    }
                }

                // this.$store.state.mostrarModal = !this.$store.state.mostrarModal
                $("#modalOportunidad").modal({
                    backdrop: "static",
                    show: true,
                    keyboard: false
                });
                $("#modalOportunidad").css("z-index", "1500");
                $('#header-sec').css('z-index','1')
                $('#aside-sec').css('z-index','1')

                $(".modal-backdrop").css("z-index", "1");
            }
            // }
            // $('#exampleModal').on('shown.bs.modal', function () {
            //     $('#myInput').trigger('focus')
            // })
        },
        eliminar (id) {
            let me = this;
            let nvoArreglo = me.arrObsequios.filter(objeto => objeto.id != id);
            // console.log('nvoArreglo', nvoArreglo);
            me.arrObsequios = nvoArreglo;
        },
        eliminarA (id) {
            let me = this;
            let nvoArreglo = me.arrAdicionales.filter(objeto => objeto.id != id);
            // console.log('nvoArreglo', nvoArreglo);
            me.arrAdicionales = nvoArreglo;
        },
    },
    created() {
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
