<template>
    <div class="content-wrapper" :id="'sec-local-mant_'+entidad">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-0">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/">Inicio</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/local">Local</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Local
                        </li>
                    </ol>
                    </div>
                </div>
                <h3 class="content-header-title mb-1 mt-0">{{accion}} Local</h3>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-8 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="mdi mdi-magnify-location"></i> Datos de Local</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarlocal">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">

                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_departamento_'+entidad">Departamento <span class="text-danger"> *</span></label>
                                                        <select name="select_departamento" class="custom-select custom-select-sm" 
                                                        :id="'select_departamento_'+entidad" @change="getProvincias">
                                                            <option value="" selected="" disabled="">SELECCIONE</option>
                                                            <option v-for="f in departamentos" :key="f.codigo" :value="f.codigo" :selected="departamentoId==f.codigo"> {{f.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_provincia_'+entidad">Provincia <span class="text-danger"> *</span></label>
                                                        <select name="select_provincia" @change="getDistritos" 
                                                            class="custom-select custom-select-sm" :id="'select_provincia_'+entidad">
                                                            <option value="" selected="" disabled="">SELECCIONE</option>
                                                            <option v-for="p in provincias" :key="p.codigo" :value="p.codigo" :selected="provinciaId==p.codigo"> {{p.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'select_distrito_'+entidad">Distrito <span class="text-danger"> *</span></label>
                                                        <select name="select_distrito" class="custom-select custom-select-sm" 
                                                            :id="'select_distrito_'+entidad">
                                                            <option value="" selected="" disabled="">SELECCIONE</option>
                                                            <option v-for="a in distritos" :key="a.codigo" :value="a.codigo" :selected="distritoId==a.codigo"> {{a.nombre}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                       <label :for="'direccion_'">Dirección <span class="text-danger"> *</span></label>
                                                       <input type="text" :id="'direccion_'+entidad" class="form-control form-control-sm" name="direccion" maxlength="255" @keypress="soloLetrasNumeros($event)">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 col-xs-4 mt-1">
                                                    <input type="radio" name="tipo" value="A" id="input-radio-14" :checked="tipoLocalId == 'A' || tipoLocalId == ''">
                                                    <label for="input-radio-14">Almacén</label>

                                                    <input type="radio" class="ml-3" name="tipo" value="T" id="input-radio-15" :checked="tipoLocalId == 'T'">
                                                    <label for="input-radio-15">Tienda</label>
                                                </div>

                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                       <label :for="'telefono_'+entidad">Teléfono <span class="text-danger"> *</span></label>
                                                       <input type="text" :id="'telefono_'+entidad" @keypress="soloNumeros($event)" 
                                                       class="form-control form-control-sm" name="telefono" maxlength="9" minlength="6" />
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-xs-12">
                                                    <div class="form-group">
                                                       <label :for="'serie_'+entidad">Serie <span class="text-danger"> *</span></label>
                                                       <input type="number" :readonly="bandSerie" :id="'serie_'+entidad" 
                                                        class="form-control form-control-sm text-center" name="serie" min="1" max="999">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12" :id="'data-error_'+entidad">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions ml-1">
                                            <button type="button" @click="enviarForm(url,entidad)" :id="'btnEnvio_'+entidad" 
                                            class="btn btn-success btn-sm">
                                                <i class="mdi mdi-check-bold icon-size"></i> Guardar
                                            </button>
                                            <button type="button" @click="atras(url)" class="btn btn-danger mr-1 btn-sm">
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
 name: 'MantenimientoLocal',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/local',
         departamentos: [],
         provincias: [],
         distritos: [],
         departamentoId: 0,
         provinciaId: 0,
         distritoId: 0,
         tipoLocalId: '',
         id: 0,
         bandSerie: false,
         entidad: 'local_mant'
     }
 },
 methods: {
    async cargarDatos () {
      let me = this
      me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
      document.getElementById('data-error_'+me.entidad).innerHTML = ''
      let departamentos = await axios.get('/departamentos')
      me.departamentos = departamentos.data.departamentos
      me.bandSerie = false
       
      if (me.id != 0) {
        let respuesta = await axios.get('/obtenerlocal/'+me.id)

        if (respuesta.data.estado) {
            me.accion = 'Editar'
            me.departamentoId = respuesta.data.local.idDepartamento
            me.getProvinciasA(me.departamentoId)
            me.provinciaId = respuesta.data.local.idProvincia
            me.getDistritosA(me.provinciaId)
            me.distritoId= respuesta.data.local.idDistrito
            me.tipoLocalId = respuesta.data.local.tipo
            me.bandSerie = true
            document.getElementById('direccion_'+me.entidad).value = respuesta.data.local.direccion
            document.getElementById('telefono_'+me.entidad).value = respuesta.data.local.telefono
            document.getElementById('serie_'+me.entidad).value = respuesta.data.local.serie
            
        } else {
            this.$router.replace(me.url)
        }
      } else {
        me.accion = 'Registrar'
        
        document.getElementById('select_departamento_'+me.entidad).value = ''
        document.getElementById('select_provincia_'+me.entidad).value = ''
        document.getElementById('select_distrito_'+me.entidad).value = ''
        document.getElementById('direccion_'+me.entidad).value = ''
        document.getElementById('telefono_'+me.entidad).value = ''
        me.departamentoId = 0
        me.provinciaId = 0
        me.distritoId = 0
        me.tipoLocalId = ''
      }
    },
    async getProvincias() {
      let me = this
      let dep = document.getElementById('select_departamento_'+me.entidad).value
      
      let provincias = await axios.get('/provincias/'+dep)
      me.provincias = provincias.data.provincias
        
      if (me.distritos.length > 0) {
            me.distritos = []
            var element = document.getElementById('select_distrito_'+me.entidad) 
            element.value = ''
      }
    },
    async getDistritos() {
      let me = this
      let prov = document.getElementById('select_provincia_'+me.entidad).value
      
      let distritos = await axios.get('/distritos/'+prov)
      me.distritos = distritos.data.distritos
    },

    async getProvinciasA(id) {
      let dep = id
      let me = this
      
      let provincias = await axios.get('/provincias/'+dep)
      me.provincias = provincias.data.provincias
        
      if (me.distritos.length > 0) {
            me.distritos = []
            var element = document.getElementById('select_distrito_'+me.entidad) 
            element.value = ''
      }
    },
    async getDistritosA(id) {
      let prov = id
      let me = this
      
      let distritos = await axios.get('/distritos/'+prov)
      me.distritos = distritos.data.distritos
    }
 },
 activated: async function () {
    let me = this
    me.isValidSession()
   
    this.$route.meta.auth = localStorage.getItem('autenticado')
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal 
    await me.cargarDatos()  
    // var element = document.getElementById('sec-local-mant_'+me.entidad)
    // element.classList.remove('d-none')
 },
 beforeDestroy: function () {
    // this.$store.state.mostrarModal = false 
    let me =this
    var element = document.getElementById('sec-local-mant_'+me.entidad)
    element.classList.add('d-none')
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