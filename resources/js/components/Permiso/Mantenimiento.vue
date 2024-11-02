<template>
    <div class="content-wrapper" :id="'sec-permiso_'+entidad">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/">Inicio</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/permisos">Tipo de Usuario</router-link>
                        </li>
                        <li class="breadcrumb-item active">{{accion}} Tipo de Usuario
                        </li>
                    </ol>
                    </div>
                </div>
                <h4 class="content-header-title mb-0 mt-1">{{accion}} Tipo de Usuario</h4>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-10 col-xs-12">
                        <div class="card">
                            <!-- <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="la la-sitemap"></i> Datos de Tipo de Usuario</h4>
                            </div> -->
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarpermiso">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-4 col-xs-12">
                                                    <div class="form-group">
                                                        <label :for="'nombres_'+entidad">Nombres <span class="text-danger"> *</span></label>
                                                        <input type="text" :id="'nombres_'+entidad" class="form-control form-control-sm" 
                                                        name="nombres" maxlength="255" @keypress="soloLetras($event)">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                       <label :for="'descripcion_'+entidad">Descripción <span class="text-success"> *</span></label>
                                                       <input type="text" :id="'descripcion_'+entidad" @keypress="soloLetras($event)" class="form-control form-control-sm" name="descripcion" maxlength="255">
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
                                                class="btn btn-sm btn-success">
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
 name: 'MantenimientoPermiso',
 mixins: [misMixins],
 data () {
     return {
         accion: 'Registrar',
         url: '/permisos',
         departamentos: [],
         provincias: [],
         distritos: [],
         id: 0,
         entidad: 'permiso_mant'
     }
 },
 methods: {
    async cargarDatos () {
        let me = this
        me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
        document.getElementById('data-error_'+me.entidad).innerHTML = ''
        if (me.id != 0) {
           let respuesta = await axios.get('/obtenerpermiso/'+me.id)

           if (respuesta.data.estado) {
               me.accion = 'Editar'
               document.getElementById('nombres_'+me.entidad).value = respuesta.data.permiso.nombre
               document.getElementById('descripcion_'+me.entidad).value = respuesta.data.permiso.descripcion
           } else {
              this.$router.replace(me.url)
           }
        } else {
            me.accion = 'Registrar'
            document.getElementById('nombres_'+me.entidad).value = ''
            document.getElementById('descripcion_'+me.entidad).value = ''
        }
        // alert(me.urlenviar)
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
    let me = this
    var element = document.getElementById('sec-permiso_'+me.entidad)
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