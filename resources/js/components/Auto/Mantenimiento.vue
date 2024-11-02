<template>
    <div class="content-wrapper" :id="'sec_'+entidad">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/">Inicio</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/auto">Autos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Auto
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Auto</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height justify-content-md-center">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="mdi mdi-car-search-outline"></i> Datos de Auto</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body justify-content-md-center">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" enctype="multipart/form-data" action="/guardarauto">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <!-- <label for="tipo" class="ml-3">Tipo de Carrocería <span class="text-danger">*</span></label>
                                                <div class="col-md-4 col-xs-4 mt-3" id="tipo">
                                                    <input type="radio" name="tipo" :checked="(tipo=='L' || tipo=='')" value="L" id="input-radio-genM" @change="cargarMarcas('L')">
                                                    <label for="input-radio-genM" class="pb-1">Liviano</label>

                                                    <input type="radio" class="ml-3" name="tipo" :checked="tipo == 'CC'" value="CC" id="input-radio-genF" @change="cargarMarcas('CC')">
                                                    <label for="input-radio-genF" class="pb-1">Camiones y Comerciales</label>
                                                </div>
                                           -->
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'codproveedor_'+entidad">Código Proveedor <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'codproveedor_'+entidad" class="form-control form-control-sm" 
                                                       name="codproveedor" maxlength="20" @keyup="aMayusculas($event)">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_marca_'+entidad">Marca <span class="text-danger">*</span></label>
                                                        <select name="select_marca" class="custom-select custom-select-sm" :id="'select_marca_'+entidad" @change="capturarMarca">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in marcas" :selected="f.id==marcaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otra</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" :id="'marca_ocultar_'+entidad">
                                                    <div class="form-group">
                                                       <label :for="'marca_'+entidad">Marca <small>(Nueva)</small> <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'marca_'+entidad" class="form-control form-control-sm" name="marca" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_modelo_'+entidad">Modelo <span class="text-danger">*</span></label>
                                                        <select name="select_modelo" class="custom-select custom-select-sm" :id="'select_modelo_'+entidad" @change="capturarModelo">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in modelos" :selected="f.id==modeloId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otra</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" :id="'modelo_ocultar_'+entidad">
                                                    <div class="form-group ml-2">
                                                       <label :for="'modelo_'+entidad">Modelo <small>(Nueva)</small> <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'modelo_'+entidad" class="form-control form-control-sm" name="modelo" maxlength="255">
                                                    </div>
                                                </div>
                                            </div>
                                          
                                            <div class="row">
                                                <!-- <div class="col-md-3 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'modelo_'+entidad">Modelo <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'modelo_'+entidad" class="form-control form-control-sm" name="modelo" maxlength="255">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'año_'+entidad">Año <span class="text-danger">*</span></label>
                                                       <input type="number" value="2020" min="2010" :id="'año_'+entidad" class="form-control form-control-sm text-center" name="anio" maxlength="255" max="2050">
                                                    </div>
                                                </div>
                                                 -->
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'version_'+entidad">Versión <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'version_'+entidad" class="form-control form-control-sm" name="version" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'transmision_'+entidad">Transmisión <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'transmision_'+entidad" class="form-control form-control-sm" name="transmision" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_linea_'+entidad">Línea <span class="text-danger">*</span></label>
                                                        <select name="linea" class="custom-select custom-select-sm" :id="'select_linea_'+entidad">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in lineas" :selected="f.id==lineaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                              
                                            <div class="row">
                                                <div class="col-md-10 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'descripcion_'+entidad">Descripción <span class="text-danger">*</span></label>
                                                       <textarea :id="'descripcion_'+entidad" class="form-control form-control-sm no-resize" rows="8" name="descripcion"></textarea>
                                                    </div>
                                                </div>
                                                
                                                <!-- <div class="col-md-3 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'color_'+entidad">Color <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'color_'+entidad" class="form-control form-control-sm" name="color" value="Blanco">
                                                    </div>
                                                </div> -->

                                                <!-- <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-2">
                                                       <label :for="'precio_'+entidad">Precio <span class="text-danger">*</span></label>
                                                       <input type="number" min="1" step="0.01" :id="'precio_'+entidad" class="form-control form-control-sm text-center" name="precio">
                                                    </div>
                                                </div> -->
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="input-group mb-3 ml-2">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Ficha Técnica</span>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" :id="'ficha_'+entidad" name="ficha"  class="custom-file-input" accept="application/pdf"
                                                            @change="updateFileFicha">
                                                            <label class="custom-file-label" :for="'ficha_'+entidad">Subir Ficha</label>
                                                        </div>
                                                    </div>
                                                    <h6 class="text-info ml-2" :id="'file-text-ficha_'+entidad"></h6>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Imagen Referencial</span>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" :id="'imagen_'+entidad" name="imagen"  class="custom-file-input" accept="image/*"
                                                            @change="updateFileImage">
                                                            <label class="custom-file-label" :for="'imagen_'+entidad">Subir Foto</label>
                                                        </div>
                                                    </div>
                                                    <h6 class="text-info ml-2" :id="'file-text-image_'+entidad"></h6>
                                               
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" :id="'data-error_'+entidad">
                                                </div>
                                            </div>
                                        
                                            <div class="form-actions ml-2">
                                                <button type="button" class="btn btn-sm btn-success" :id="'btnEnvio_'+entidad" 
                                                    @click="enviarForm(url, entidad)">
                                                    <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                                </button>
                                                <button type="button" @click="atras(url)" class="btn btn-sm btn-danger mr-1">
                                                    <i class="mdi mdi-close icon-size"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>
                                   </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import axios from 'axios'
import {misMixins} from '../../mixins/mixin.js'
export default {
 name: 'MantenimientoAuto',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/auto',
         marcas: [],
         modelos: [],
         acabados: [],
         unidadMedidas: [],
         tipoProductos: [],
         lineas: [
            {id: 'L', nombre: 'Ligeros'},
            {id: 'P', nombre: 'Pesados'}
         ],
         lineaId: 0,
         marcaId: 0,
         modeloId: 0,
         unidadMedidadId: 0,
         acabadoId: 0,
         tipoProductoId: 0,
         id:0,
         tipo: '',
         entidad: 'auto-mant'
     }
 },
 methods: {
    updateFileFicha ($event) {
        let me = this;
        document.getElementById(`file-text-ficha_${me.entidad}`).innerHTML = `Ficha Adjunta: ${$event.target.files[0].name}`;
    },
    updateFileImage($event) {
        let me = this;
        document.getElementById(`file-text-image_${me.entidad}`).innerHTML = `Imagen de Ref. Adjunta: ${$event.target.files[0].name}`;
    },
    loadFiles (refAuto) {
        let me = this;
        if (refAuto.urlFicha != null) {
            document.getElementById(`file-text-ficha_${me.entidad}`).innerHTML = `Ficha Adjunta: <a class="btn btn-outline-danger btn-sm" href="/storage${refAuto.urlFicha}" target="_blank">Ver Ficha</a>`;
        }

        if (refAuto.urlImagen != null) {
            document.getElementById(`file-text-image_${me.entidad}`).innerHTML = `Imagen Ref. Adjunta: <a  class="btn btn-outline-danger btn-sm" href="/storage${refAuto.urlImagen}" target="_blank">Ver Imagen</a>`;
        }
    },
    async cargarDatos () {
      let me = this
      me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
      document.getElementById('data-error_'+me.entidad).innerHTML = ''
     
      if (me.id != 0) {
        let respuesta = await axios.get('/obtenerauto/'+me.id)

        if (respuesta.data.estado) {
            me.accion = 'Editar'
            // me.tipo = respuesta.data.auto.tipoAuto
            // me.cargarMarcas(me.tipo)
            document.getElementById('select_marca_'+me.entidad).value = respuesta.data.auto.marcaId
            // document.getElementById('año_'+me.entidad).value = respuesta.data.auto.anio
            document.getElementById('select_modelo_'+me.entidad).value = respuesta.data.auto.modeloId
            document.getElementById('version_'+me.entidad).value = respuesta.data.auto.version
            document.getElementById('transmision_'+me.entidad).value = respuesta.data.auto.transmision
            document.getElementById('descripcion_'+me.entidad).value = respuesta.data.auto.descripcion
            document.getElementById('codproveedor_'+me.entidad).value = respuesta.data.auto.codproveedor
            document.getElementById('select_linea_'+me.entidad).value = respuesta.data.auto.linea
           
            me.loadFiles(respuesta.data.auto);
            // document.getElementById('ficha_'+me.entidad).value = null
        } else {
            this.$router.replace(me.url)
        }
      } else {
        me.accion = 'Registrar'
        
        document.getElementById('select_marca_'+me.entidad).value = ''
        document.getElementById('select_modelo_'+me.entidad).value = ''
        document.getElementById('select_linea_'+me.entidad).value = ''

        // document.getElementById('año_'+me.entidad).value = '2020'
        // document.getElementById('modelo_'+me.entidad).value = ''
        document.getElementById('version_'+me.entidad).value = ''
        document.getElementById('descripcion_'+me.entidad).value = ''
        document.getElementById('transmision_'+me.entidad).value = ''
        // document.getElementById('color_'+me.entidad).value = 'Blanco'
        document.getElementById('codproveedor_'+me.entidad).value = ''
        // me.tipo = ''
        // me.cargarMarcas('L')
      }
      me.cargarMarcas()
      me.cargarModelos()
      me.capturarMarca()
      me.capturarModelo();
      document.getElementById('codproveedor_'+me.entidad).focus()
    },
    async cargarMarcas () {
        let me = this
        let marcas = await axios.get('/marcasautosmod')
        me.marcas = marcas.data.marcas
    },
    async cargarModelos () {
        let me = this
        let modelos = await axios.get('/modelosautos')
        me.modelos = modelos.data.modelosauto
    },
    capturarMarca () {
        let me = this
        let val = document.getElementById('select_marca_'+me.entidad).value
        let element =  document.getElementById('marca_ocultar_'+me.entidad)
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('select_modelo_'+me.entidad).focus()
        }
    },
    capturarModelo () {
        let me = this
        let val = document.getElementById('select_modelo_'+me.entidad).value
        let element =  document.getElementById('modelo_ocultar_'+me.entidad)
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('modelo_'+me.entidad).focus();
        } else {
            element.classList.add('ocultar')
            document.getElementById('version_'+me.entidad).focus()
        }
    }
 },
 activated: async function () {
    let me = this
    me.isValidSession()
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    me.cargarDatos()  
 },
 beforeDestroy: function () {
    let me  = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
 }
}
</script>
<style scoped>
.custom-file-label::after {
  bottom: 0;
  z-index: 3;
  display: block;
  height: 2.0625rem;
  content: "...";
  background-color: #f8f9fa;
  border-left: inherit;
  border-radius: 0 2px 2px 0;
}
.ocultar {
    display:none;
}
select {
    cursor: pointer;
}
.txt-unidad{
    float:right;
    position: relative;
    margin-top:-29px;
    margin-right: 10px;
    cursor: pointer;
    font-style: italic;
}
.no-resize {
    resize: none;
}

input[type="radio"] {
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
     border-radius: 50%;
}

/* appearance for checked radiobutton */
input[type="radio"]:checked {
     background-color: #182fd2;
     cursor: pointer;
}


</style>