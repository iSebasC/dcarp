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
                            <router-link tag="a" to="/producto">Productos</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Producto
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Producto</h4>
            </div>
        </div>
        <div class="content-body">
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height justify-content-md-center">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="la la-project-diagram"></i> Datos del Producto</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body justify-content-md-center">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarproducto">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-3 col-xs-12 " >
                                                    <div class="form-group ml-1">
                                                       <label :for="'codigo_interno_'+entidad">Código <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'codigo_interno_'+entidad" class="form-control form-control-sm" name="codigo_interno" maxlength="50" @keypress="soloLetrasNumeros($event)">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ml-1">
                                                    <div class="form-group">
                                                        <label :for="'select_tipo_'+entidad">Tipo de Producto <span class="text-danger">*</span></label>
                                                        <select name="select_tipoProducto" class="custom-select custom-select-sm" :id="'select_tipo_'+entidad" @change="capturarTipoProducto">
                                                            <!--<option value="" selected="" disabled="">Seleccione</option>-->
                                                            <option v-for="f in tipoProductos" :selected="f.id==tipoProductoId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <!--<option value="otro">Indique Otro</option>-->
                                                        </select>
                                                    </div>
                                                </div>
 
                                                <div class="col-md-3 col-xs-12 " >
                                                    <div class="form-group ml-1">
                                                       <label :for="'codigo_sunat_'+entidad">Código Sunat <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'codigo_sunat_'+entidad" class="form-control form-control-sm" name="codigo_sunat" maxlength="15" @keypress="soloNumeros($event)">
                                                    </div>
                                                </div>
                                                 <div class="col-md-3 col-xs-12 ocultar" id="codigo_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'codigo_'+entidad">Código del Proveedor <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'codigo_'+entidad" class="form-control form-control-sm" name="codigo" maxlength="15" @keypress="soloNumeros($event)">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-12 ocultar" id="nombre_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'nombre_'+entidad">Nombre <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'nombre_'+entidad" class="form-control form-control-sm" name="nombre" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 col-xs-12 ocultar" id="nombrebateria_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'nombre_bateria_'+entidad">Nombre <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'nombre_bateria_'+entidad" class="form-control form-control-sm" name="nombrebateria" maxlength="255">
                                                    </div>
                                                </div>
                                      
                                                <div class="col-md-4 col-xs-12 ocultar" id="nombrellanta_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'nombre_llanta_'+entidad">Nombre <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'nombre_llanta_'+entidad" class="form-control form-control-sm" name="nombrellanta" maxlength="255">
                                                    </div>
                                                </div>
                                      
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" id="marcarepuesto_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_marca_repuesto_'+entidad">Marca <span class="text-danger">*</span></label>
                                                        <select name="select_marcarepuesto" class="custom-select custom-select-sm" :id="'select_marca_repuesto_'+entidad" @change="capturarMarca">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in marcas" :selected="f.id==marcaRepuestoId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otra</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="marca_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'marca_'+entidad">Marca (Nueva) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'marca_'+entidad" class="form-control form-control-sm" name="marca" maxlength="255">
                                                    </div>
                                                </div>

                                                 <div class="col-md-3 col-xs-12 ocultar ml-1" id="selectmarcaauto_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_marca_auto_'+entidad">Marca de Auto <span class="text-danger">*</span></label>
                                                        <select name="select_marcaauto" class="custom-select custom-select-sm" :id="'select_marca_auto_'+entidad" @change="capturarMarcaAuto">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in marcasauto" :selected="f.id==marcaAutoId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otra</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" id="marcaauto_ocultar">
                                                    <div class="form-group">
                                                       <label :for="'marca_auto_'+entidad">Marca de Auto (Nuevo) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'marca_auto_'+entidad" class="form-control form-control-sm" name="marcaauto" maxlength="255">
                                                    </div>
                                                </div>

                                                <!--<div class="col-md-3 col-xs-12 ml-1 ocultar" id="selectmodeloauto_ocultar">
                                                    <div class="form-group">
                                                        <label for="select_modeloauto">Modelo de Auto <span class="text-danger">*</span></label>
                                                        <select name="select_modeloauto" class="custom-select" id="select_modeloauto" @change="capturarModeloAuto">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in modelos" :selected="f.modelo==modeloId" :key="f.modelo" :value="f.modelo"> {{f.modelo}}</option>
                                                            <option value="otro">Indique Otro</option>
                                                        </select>
                                                    </div>
                                                </div>-->

                                                <div class="col-md-3 col-xs-12 ocultar ml-1" id="modeloauto_ocultar">
                                                    <div class="form-group">
                                                       <label :for="'modelo_auto_'+entidad">Modelo de Auto <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'modelo_auto_'+entidad" class="form-control form-control-sm" name="modeloauto" maxlength="255">
                                                    </div>
                                                </div>

                                                 <div class="col-md-4 col-xs-12 ml-1 ocultar" id="sistemaauto_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_sistema_auto_'+entidad">Sistema del Auto <span class="text-danger">*</span></label>
                                                        <select name="select_sistemaauto" class="custom-select custom-select-sm" :id="'select_sistema_auto_'+entidad" @change="capturarSistemeAuto">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in sistemas" :selected="f.id==sistemaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" 
                                                   id="marcallanta_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_marca_llanta_'+entidad">Marca <span class="text-danger">*</span></label>
                                                        <select name="select_marcallanta" class="custom-select custom-select-sm" :id="'select_marca_llanta_'+entidad" @change="capturarMarcaLlanta">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in marcasllanta" :selected="f.id==marcaLlantaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otro</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="marcallanta02_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'marca_llanta_'+entidad">Marca (Nueva) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'marca_llanta_'+entidad" class="form-control form-control-sm" name="marcallanta" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" id="modelollanta_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_modelo_llanta_'+entidad">Modelo <span class="text-danger">*</span></label>
                                                        <select name="select_modelollanta" class="custom-select custom-select-sm" 
                                                            :id="'select_modelo_llanta_'+entidad" @change="capturarModeloLlanta">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in modelosllantas" :selected="f.id==modeloLlantaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otro</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="modelollanta02_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'modelo_llanta_'+entidad">Modelo (Nuevo) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'modelo_llanta_'+entidad" class="form-control form-control-sm" 
                                                       name="modelollanta" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12 ml-1 ocultar" id="tipollanta_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_tipo_llanta_'+entidad">Tipo de Llanta <span class="text-danger">*</span></label>
                                                        <select name="select_tipollanta" class="custom-select custom-select-sm" 
                                                        :id="'select_tipo_llanta_'+entidad">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in tiposLlantas" :selected="f.id==tipoLlantaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="medidallanta_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'medida_llanta_'+entidad">Medida <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'medida_llanta_'+entidad" class="form-control form-control-sm" 
                                                       name="medidallanta" maxlength="255">
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <div class="row">
                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" 
                                                   id="marcabateria_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_marca_bateria_'+entidad">Marca <span class="text-danger">*</span></label>
                                                        <select name="select_marcabateria" class="custom-select custom-select-sm" 
                                                        :id="'select_marca_bateria_'+entidad" @change="capturarMarcaBateria">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in marcasBateria" :selected="f.id==marcaBateriaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otro</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="marcabateria02_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'marca_bateria_'+entidad">Marca (Nueva) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'marca_bateria_'+entidad" class="form-control form-control-sm" 
                                                       name="marcabateria" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ml-1 ocultar" id="modelobateria_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_modelo_bateria_'+entidad">Modelo <span class="text-danger">*</span></label>
                                                        <select name="select_modelobateria" class="custom-select custom-select-sm" :id="'select_modelo_bateria_'+entidad" @change="capturarModeloBateria">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in modelosBateria" :selected="f.id==modeloBateriaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                            <option value="otro">Indique Otro</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12 ocultar" id="modelobateria02_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'modelo_bateria_'+entidad">Modelo (Nuevo) <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'modelo_bateria_'+entidad" class="form-control form-control-sm" name="modelobateria" maxlength="255">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 col-xs-12 ml-1 ocultar" id="nroplacabateria_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_nroplaca_'+entidad">Nro de Placa <span class="text-danger">*</span></label>
                                                        <select name="select_nroplaca" class="custom-select custom-select-sm" :id="'select_nroplaca_'+entidad">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in numeroPlaca" :selected="f.id==placaBateriaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 col-xs-12 ml-1 ocultar" id ="tipobateria_ocultar">
                                                    <div class="form-group">
                                                        <label :for="'select_tipo_bateria_'+entidad">Tipo de Batería <span class="text-danger">*</span></label>
                                                        <select name="select_tipobateria" class="custom-select custom-select-sm" :id="'select_tipo_bateria_'+entidad">
                                                            <option value="" selected="" disabled="">Seleccione</option>
                                                            <option v-for="f in tipoBateria" :selected="f.id==tipoBateriaId" :key="f.id" :value="f.id"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 col-xs-12 ocultar" id="nombreinsumo_ocultar">
                                                    <div class="form-group ml-1">
                                                       <label :for="'nombre_insumo_'+entidad">Nombre <span class="text-danger">*</span></label>
                                                       <input type="text" :id="'nombre_insumo_'+entidad" class="form-control form-control-sm" name="nombreinsumo" maxlength="255">
                                                    </div>
                                                </div>
                                           
                                                <!-- <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-1">
                                                       <label :for="'precio_'+entidad">Precio <span class="text-danger">*</span></label>
                                                       <input type="number" step="0.01" min="0" :id="'precio_'+entidad" class="form-control form-control-sm text-center" name="precio">
                                                    </div>
                                                </div> -->
                                           
                                                <div class="col-md-2 col-xs-12">
                                                    <div class="form-group ml-1">
                                                       <label :for="'stock_minimo_'+entidad">Stock Mínimo <span class="text-danger">*</span></label>
                                                       <input type="number" step="1" min="1" value="1" :id="'stock_minimo_'+entidad" class="form-control form-control-sm text-center" name="stockminimo">
                                                    </div>
                                                </div>
                                           
                                            </div>
                                            
                          
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12" :id="'data-error_'+entidad">
                                            </div>
                                        </div>
                                    

                                        <div class="form-actions ml-1">
                                            <button type="button" class="btn btn-sm btn-success" :id="'btnEnvio_'+entidad" 
                                            @click="enviarForm(url, entidad)">
                                                <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                            </button>
                                            <button type="button" @click="atras(url)" class="btn btn-sm btn-danger mr-1">
                                                <i class="mdi mdi-close icon-size"></i> Cancelar
                                            </button>
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
 name: 'MantenimientoProducto',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/producto',
         marcasauto: [],
         tipoProductos: [
            {id:'A',  nombre: 'Accesorios/Repuestos'},
            {id:'B',  nombre: 'Baterías'},
            {id:'LL', nombre: 'Neumáticos'},
            {id:'M',  nombre: 'Muelles'},
            {id:'I',  nombre: 'Insumos'},
         ],
         tiposLlantas: [
            {id:'AT',  nombre: 'AT'},
            {id:'HT',  nombre: 'HT'},
            {id:'MT',  nombre: 'MT'},
            {id:'AY',  nombre: 'AY'},            
            {id:'Camión',  nombre: 'Camión'},
            {id:'Convencional',  nombre: 'Convencional'},
         ],
         sistemas: [],
         marcas: [],
         marcasllanta: [],
         modelosllantas: [],
         modelos: [],
         modeloId: '',
         sistemaId:'',
         tipoLlantaId: '',
         marcaRepuestoId: '',
         marcaAutoId: '',
         modeloLlantaId: '',
         marcaLlantaId: '',
         tipoProductoId: 'A',
         id:0,
         numeroPlacaBateriaId: '',
         tipoBateria: [
            {id:'PR', nombre: 'Profesional'},
            {id:'AD', nombre: 'Alto Desempeño'},
            {id:'PL', nombre: 'Platinum'}
         ],
         tipoBateriaId: '',
         numeroPlaca: [
            {id:'5', nombre: '5'},
            {id:'7', nombre: '7'},
            {id:'9', nombre: '9'},
            {id:'10', nombre: '10'},
            {id:'11', nombre: '11'},
            {id:'13', nombre: '13'},
            {id:'15', nombre: '15'},
            {id:'17', nombre: '17'},
            {id:'19', nombre: '19'},
            {id:'21', nombre: '21'},
            {id:'23', nombre: '23'},
            {id:'25', nombre: '25'},
            {id:'27', nombre: '27'},
            {id:'33', nombre: '33'},
            {id:'1215', nombre: '1215'},
            {id:'1217', nombre: '1217'},
            {id:'1219', nombre: '1219'},   
            {id:'HL-09 AD', nombre: 'HL-09 AD'},
            {id:'HL-11 AD CC ', nombre: 'HL-11 AD CC '},
            {id:'FF-09 AD A3 NOR', nombre: 'FF-09 AD A3 NOR'},
            {id:'FF-11 A3 AD', nombre: 'FF-11 A3 AD'},
            {id:'FF-13 AD P/D', nombre: 'FF-13 AD P/D'},
            {id:'W-13 AD', nombre: 'W-13 AD'},
            {id:'S-1215EM AD', nombre: 'S-1215EM AD'},
            {id:'SD-13 AD CC', nombre: 'SD-13 AD CC'},
            {id:'V-13 INV AD', nombre: 'V-13 INV AD'},
            {id:'V-13 AD', nombre: 'V-13 AD'},
            {id:'FH-1215 INV AD', nombre: 'FH-1215 INV AD'},
            {id:'FH-1215 AD', nombre: 'FH-1215 AD'},
         ],
         modelosBateria:[],
         modeloBateriaId: '',
         marcasBateria: [],
         marcaBateriaId: '',
         placaBateriaId: '',
         entidad: 'producto_mant'
     }
 },
 methods: {
    async cargarDatos () {
      let me = this
      me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
      document.getElementById(`data-error_${me.entidad}`).innerHTML = ''
     
      let marcas = await axios.get('/marcasrepuestos')
      me.marcas = marcas.data.marcas
      
      let marcasllanta = await axios.get('/marcasllanta')
      me.marcasllanta = marcasllanta.data.marcasllanta

      let modelosllanta = await axios.get('/modelosllanta')
      me.modelosllantas = modelosllanta.data.modelosllanta
    
      let marcasauto = await axios.get('/marcasauto')
      me.marcasauto = marcasauto.data.marcasauto
  
      let sistemas = await axios.get('/sistemasauto')
      me.sistemas = sistemas.data.sistemas

      let modelosbateria = await axios.get('/modelosbateria')
      me.modelosBateria = modelosbateria.data.modelos

      let marcasbateria = await axios.get('/marcasbateria')
      me.marcasBateria = marcasbateria.data.marcas


      if (me.id != 0) {
        let respuesta = await axios.get('/obtenerproducto/'+me.id)

        if (respuesta.data.estado) {
            me.accion = 'Editar'
            document.getElementById('codigo_interno_'+ me.entidad).value = respuesta.data.producto.codInterno
            document.getElementById('codigo_'+me.entidad).value = respuesta.data.producto.codProveedor
            document.getElementById('nombre_'+me.entidad).value = respuesta.data.producto.nombre
            document.getElementById('nombre_llanta_'+me.entidad).value = respuesta.data.producto.nombre
            document.getElementById('nombre_bateria_'+me.entidad).value = respuesta.data.producto.nombre
            // document.getElementById('precio_'+me.entidad).value = respuesta.data.producto.precio
            document.getElementById('medida_llanta_'+me.entidad).value = respuesta.data.producto.medida
            document.getElementById('stock_minimo_'+me.entidad).value = respuesta.data.producto.stockMinimo
            document.getElementById('nombre_insumo_'+me.entidad).value = respuesta.data.producto.nombre
            document.getElementById('modelo_auto_'+me.entidad).value = respuesta.data.producto.modelo
            document.getElementById('codigo_sunat_'+me.entidad).value=respuesta.data.producto.codSunat
            
            me.tipoProductoId = respuesta.data.producto.tipoProducto
            me.marcaRepuestoId = respuesta.data.producto.idMarca
            me.marcaAutoId = respuesta.data.producto.idMarcaAuto
            me.sistemaId = respuesta.data.producto.idSistema
            me.marcaLlantaId = respuesta.data.producto.idMarcaLlanta
            me.modeloLlantaId = respuesta.data.producto.idModeloLlanta
            me.tipoLlantaId = respuesta.data.producto.tipoLlanta
            me.modeloBateriaId = respuesta.data.producto.idModeloBateria
            me.marcaBateriaId = respuesta.data.producto.idMarcaBateria
            me.placaBateriaId = respuesta.data.producto.placaBateria
            me.tipoBateriaId = respuesta.data.producto.tipoBateria
            document.getElementById('select_marca_auto_'+me.entidad).value = respuesta.data.producto.idMarcaAuto
            me.capturarTipoProducto02()
        } else {
            this.$router.replace(me.url)
        }
      } else {
        me.accion = 'Registrar'
        me.tipoProductoId = 'A'
        me.marcaRepuestoId = ''
        me.marcaAutoId = ''
        me.modeloId = ''
        me.sistemaId = ''
        me.marcaLlantaId = ''
        me.modeloLlantaId = ''
        me.tipoLlantaId = ''
        me.modeloBateriaId = ''
        me.marcaBateriaId = ''
        me.placaBateriaId = ''
        me.tipoBateriaId = ''
         
        me.tipoProductoId='A'
        me.bandEditar = false
        document.getElementById('codigo_interno_'+me.entidad).value = ''
        document.getElementById('codigo_'+me.entidad).value = ''
        document.getElementById('codigo_sunat_'+me.entidad).value=''
        document.getElementById('nombre_'+me.entidad).value = ''
        // document.getElementById('precio_'+me.entidad).value = ''
        document.getElementById('medida_llanta_'+me.entidad).value = ''
        document.getElementById('stock_minimo_'+me.entidad).value = '1'
        document.getElementById('nombre_insumo_'+me.entidad).value = ''

        document.getElementById('select_marca_repuesto_'+me.entidad).value = ''
        document.getElementById('select_marca_auto_'+me.entidad).value = ''
        // document.getElementById('select_modeloauto').value = ''
        document.getElementById('select_modelo_llanta_'+me.entidad).value = ''
        document.getElementById('select_sistema_auto_'+me.entidad).value = ''
        document.getElementById('modelo_auto_'+me.entidad).value = ''
        me.capturarTipoProducto()
      }
     
      
    },
    capturarSistemeAuto () {
        let me = this
        // let val = document.getElementById('select_acabado_'+me.entidad).value
        // let element =  document.getElementById('acabado_ocultar')
        // if (val == 'otro') {
        //     element.classList.remove('ocultar')
        //     document.getElementById('acabado_'+me.entidad).focus()
        // } else {
        //     element.classList.add('ocultar')
        //     document.getElementById('chk_espesor').focus()
        // }
    },
    async capturarMarcaAuto () {
        let me = this
        let val = document.getElementById('select_marca_auto_'+me.entidad).value
        let element = document.getElementById('marcaauto_ocultar')
        // let element2 = document.getElementById('modeloauto_ocultar')
        // let element3 = document.getElementById('selectmodeloauto_ocultar')
        
        if (val == 'otro') {
            element.classList.remove('ocultar')
            // element2.classList.remove('ocultar')
            // element3.classList.add('ocultar')
            
            document.getElementById('marca_auto_'+me.entidad).focus()
        } else {
            // let modelos = await axios.get('/modelosauto/'+val)
            // me.modelos = modelos.data.modelos
           
            // document.getElementById('select_modelo_auto_'+me.entidad).value = '' // alert(me.modelos.length)
            
            element.classList.add('ocultar')
            // element2.classList.add('ocultar')
            // element3.classList.remove('ocultar')
            document.getElementById('select_marca_auto_'+me.entidad).focus()
        }
    },
    async capturarMarcaBateria () {
        let me = this
        let val = document.getElementById('select_marca_bateria_'+me.entidad).value
        
        let element = document.getElementById('marcabateria02_ocultar')
        
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_bateria_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('select_marca_bateria_'+me.entidad).focus()
        } 
    },
    capturarModeloBateria () {
        let me = this
        let val = document.getElementById('select_modelo_bateria_'+me.entidad).value
        let element = document.getElementById('modelobateria02_ocultar')
        
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('modelo_bateria_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('select_modelo_bateria_'+me.entidad).focus()
        }
    },
    capturarModeloAuto () {
        let me = this
        let val = document.getElementById('select_modelo_auto_'+me.entidad).value
        let element = document.getElementById('modeloauto_ocultar')
        
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('modelo_auto_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('select_modelo_auto_'+me.entidad).focus()
        }
    },
    capturarTipoProducto () {
        let me = this
        let val = document.getElementById('select_tipo_'+me.entidad).value
        me.tipoProductoId = val
        document.getElementById('select_marca_repuesto_'+me.entidad).value = ''
        document.getElementById('select_marca_llanta_'+me.entidad).value = ''
        document.getElementById('select_modelo_llanta_'+me.entidad).value = ''
    
        me.capturarMarca()
        me.capturarMarcaLlanta()
        me.capturarModeloLlanta()
        
        if (me.tipoProductoId == 'A') {
            document.getElementById('codigo_ocultar').classList.remove('ocultar')
            document.getElementById('nombre_ocultar').classList.remove('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.remove('ocultar')
            // document.getElementById('marcaauto_ocultar').classList.remove('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.remove('ocultar')
            
            document.getElementById('modeloauto_ocultar').classList.remove('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.remove('ocultar')
            
            document.getElementById('sistemaauto_ocultar').classList.remove('ocultar')
            document.getElementById('codigo_'+me.entidad).focus()

            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')

            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')

            // PARA BATERIAS
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
       
        } else if (me.tipoProductoId == 'LL'){
            document.getElementById('nombrellanta_ocultar').classList.remove('ocultar')
            document.getElementById('marcallanta_ocultar').classList.remove('ocultar')
            document.getElementById('modelollanta_ocultar').classList.remove('ocultar')
            document.getElementById('tipollanta_ocultar').classList.remove('ocultar')
            document.getElementById('medidallanta_ocultar').classList.remove('ocultar')
            
            document.getElementById('nombre_llanta_'+me.entidad).focus()

            // document.getElementById('modeloauto_ocultar').classList.remove('ocultar')
            // document.getElementById('sistemaauto_ocultar').classList.remove('ocultar')
            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
       
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')
            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')
        
            // PARA BATERIAS
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
       
        } else if (me.tipoProductoId == 'B'){
            document.getElementById('nombrebateria_ocultar').classList.remove('ocultar')
            document.getElementById('marcabateria_ocultar').classList.remove('ocultar')
            document.getElementById('modelobateria_ocultar').classList.remove('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.remove('ocultar')
            document.getElementById('tipobateria_ocultar').classList.remove('ocultar')
          
            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')
       
            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
      
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
       
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')

            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')
       
            document.getElementById('nombre_bateria_'+me.entidad).focus()
         
        } else {
            document.getElementById('nombreinsumo_ocultar').classList.remove('ocultar')
            document.getElementById('nombre_insumo_'+me.entidad).focus()
         
            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
      
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
        
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
        
          // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
       
           
            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')
        
             // PARA BATERIAS
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
        }

        document.getElementById('codigo_sunat_'+me.entidad).focus()
    },
    capturarTipoProducto02 () {
        let me = this
        let val = document.getElementById('select_tipo_'+me.entidad).value
        // me.capturarMarca()
        // me.capturarMarcaAuto()
        // me.capturarMarcaLlanta()
        // me.capturarModeloLlanta()

        if (me.tipoProductoId == 'A') {
            document.getElementById('codigo_ocultar').classList.remove('ocultar')
            document.getElementById('nombre_ocultar').classList.remove('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.remove('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.remove('ocultar')
            // document.getElementById('marcaauto_ocultar').classList.remove('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.remove('ocultar')
            document.getElementById('modeloauto_ocultar').classList.remove('ocultar')
            document.getElementById('sistemaauto_ocultar').classList.remove('ocultar')
            document.getElementById('codigo_'+me.entidad).focus()

            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')

            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')

            // PARA BATERIAS
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
          
            
        } else if (me.tipoProductoId == 'LL'){
            document.getElementById('nombrellanta_ocultar').classList.remove('ocultar')
            document.getElementById('marcallanta_ocultar').classList.remove('ocultar')
            document.getElementById('modelollanta_ocultar').classList.remove('ocultar')
            document.getElementById('tipollanta_ocultar').classList.remove('ocultar')
            document.getElementById('medidallanta_ocultar').classList.remove('ocultar')
            
            document.getElementById('nombre_llanta_'+me.entidad).focus()

            // document.getElementById('modeloauto_ocultar').classList.remove('ocultar')
            // document.getElementById('sistemaauto_ocultar').classList.remove('ocultar')
            

            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
            
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')

            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')

            // PARA BATERIAS
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
     
        } else if (me.tipoProductoId == 'B'){
            // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
            document.getElementById('nombreinsumo_ocultar').classList.add('ocultar')
            
            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')

            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')

            document.getElementById('marcabateria_ocultar').classList.remove('ocultar')
            document.getElementById('modelobateria_ocultar').classList.remove('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.remove('ocultar')
            document.getElementById('tipobateria_ocultar').classList.remove('ocultar')
            document.getElementById('nombrebateria_ocultar').classList.remove('ocultar')
           
            document.getElementById('nombre_bateria_'+me.entidad).focus()
         
        } else {
            document.getElementById('nombreinsumo_ocultar').classList.remove('ocultar')
            document.getElementById('nombre_insumo_'+me.entidad).focus()
         
            document.getElementById('codigo_ocultar').classList.add('ocultar')
            document.getElementById('nombre_ocultar').classList.add('ocultar')
            document.getElementById('marcarepuesto_ocultar').classList.add('ocultar')
            document.getElementById('marcaauto_ocultar').classList.add('ocultar')
            document.getElementById('modeloauto_ocultar').classList.add('ocultar')
            document.getElementById('sistemaauto_ocultar').classList.add('ocultar')

            document.getElementById('marcallanta_ocultar').classList.add('ocultar')
            document.getElementById('modelollanta_ocultar').classList.add('ocultar')
            document.getElementById('tipollanta_ocultar').classList.add('ocultar')
            document.getElementById('medidallanta_ocultar').classList.add('ocultar')
            document.getElementById('selectmarcaauto_ocultar').classList.add('ocultar')
            // document.getElementById('selectmodeloauto_ocultar').classList.add('ocultar')
            
            // PARA BATERIAS
            document.getElementById('marcabateria_ocultar').classList.add('ocultar')
            document.getElementById('modelobateria_ocultar').classList.add('ocultar')
            document.getElementById('nroplacabateria_ocultar').classList.add('ocultar')
            document.getElementById('tipobateria_ocultar').classList.add('ocultar')
            document.getElementById('nombrellanta_ocultar').classList.add('ocultar')
            document.getElementById('nombrebateria_ocultar').classList.add('ocultar')
        }
        document.getElementById('codigo_sunat_'+me.entidad).focus()

    },
    capturarMarca () {
        let me = this
        let val = document.getElementById('select_marca_repuesto_'+me.entidad).value
        let element = document.getElementById('marca_ocultar')
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            document.getElementById('select_marca_repuesto_'+me.entidad).focus()
        }
    },
    capturarMarcaLlanta () {
        let me = this
        let val = document.getElementById('select_marca_llanta_'+me.entidad).value
        let element = document.getElementById('marcallanta02_ocultar')
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('marca_llanta_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            // document.getElementById('select_marcallanta').focus()
        }
    },
    capturarModeloLlanta () {
        let me = this
        let val = document.getElementById('select_modelo_llanta_'+me.entidad).value
        let element = document.getElementById('modelollanta02_ocultar')
        if (val == 'otro') {
            element.classList.remove('ocultar')
            document.getElementById('modelo_llanta_'+me.entidad).focus()
        } else {
            element.classList.add('ocultar')
            // document.getElementById('select_marcallanta').focus()
        }
    },
    copiar (valor,val2) {
        let me = this
        if (valor == 1) {
            let valor = ''
            if (val2 == '') {
                let valorS = document.getElementById('select_unidad_medida_'+me.entidad).value
                let text = valorS.split('@');
                valor=text[1]
            } else {
                valor = val2
            }
            document.getElementById('txt-unidad').innerHTML = valor
        }else{    
            document.getElementById('txt-unidad').innerHTML = document.getElementById('unidadMedida').value
        }
    },
    copiar02(e) {
        let key = window.Event ? e.which : e.keyCode
        let tecla = String.fromCharCode(key)
        // alert(tecla)
        document.getElementById('txt-unidad').innerHTML = document.getElementById('unidadMedida').value
    } 
 },
 activated: async function () {
    let me = this
    me.isValidSession()
   
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')

    me.cargarDatos()  
 },
 beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
 }
}
</script>
<style scoped>
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

</style>