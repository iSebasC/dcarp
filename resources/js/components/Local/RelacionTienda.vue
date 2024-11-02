<template>
    <div class="content-wrapper" :id="'sec-local_'+entidad">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <div class="row breadcrumbs-top d-block">
                    <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/">Inicio</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link tag="a" to="/local">Local</router-link>
                        </li>
                        <li class="breadcrumb-item active">Relación de Tiendas y Almacenes
                        </li>
                    </ol>
                    </div>
                </div>
                <h3 class="content-header-title mb-0 mt-1">Relación de Tiendas y Almacenes</h3>
            </div>
        </div>
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row match-height">
                    <div class="col-md-12 col-xs-12">
                        <div class="card">
                            <div class="card-header pb-1">
                                <h4 class="card-title" id="basic-layout-form"><i class="mdi mdi-chandelier"></i> Datos de Relación de Tiendas y Almacenes <br>
                                <small>TIENDA: <strong v-text="local.direccion+' '+local.departamento"></strong></small></h4>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <form class="form" :id="'formEnv_'+entidad" method="POST" action="/guardarlocalrelacion">
                                        <input type="hidden" :value="this.id" name="id"/>
                                        <input type="hidden" :value="this.$store.state.token" name="_token">
                                        <input type="hidden" :value="arregloMarcados" name="arreglomarcados" id="arreglomarcados">
                      
                                        <div class="form-body">
                                            <div v-for="(item,index) in this.arreglo" :key="item.departamento.idDepartamento" :class="'row ' + (index>0?'mt-2':'')">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <h5 class="card-title">
                                                        <i class="lab la-buffer mr-1"></i>DEPARTAMENTO: <strong class="text-primary" v-text="item.departamento.departamento"></strong></h5>  
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-xs-12">
                                                    <div class="row">
                                                        <div v-for="almacen in item.almacenes" :key="almacen.id" class="col-md-6 col-xs-12">
                                                            <input class="mr-1" type="checkbox" :name="'chk_'+almacen.idRelacion" :id="'chk_'+entidad+'_'+almacen.idRelacion" @change="obtenerValor(almacen.idRelacion)" :checked="almacen.situacionRelacion=='S'" >
                                                            <label :for="'chk_'+entidad+'_'+almacen.idRelacion" v-text="almacen.direccion"></label>
                                                        </div>
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
 name: 'RelacionTienda',
 mixins: [misMixins],
 data () {
     return {
         url: '/local',
         id: 0,
         arreglo: [],
         arregloMarcados:'',
         local: {},
         entidad:'relacion_tienda'
     }
 },
 methods: {
    async cargarDatos () {
      let me = this
      let token = document.getElementById('tokenApp').value
      me.id = (this.$route.params.id == undefined?0:this.$route.params.id)
      let band= true
      document.getElementById('data-error_'+me.entidad).innerHTML = ''
      if (me.id != 0) {
        let response = await axios({
            method: 'post',
            url: '/obteneralmacen',
            data: {
                'id': me.id,
                '_token': token
            }
        })
      
        if (response.data.estado) {
            me.arreglo = response.data.arreglo
            me.arregloMarcados = response.data.arreglomarcados
            me.local   = response.data.local
        } else {
           band = false
        }
      } else {
          band = false
      }
      
      if (!band) {
        me.id = 0
        this.$router.replace(me.url)
      }
      
    //   console.log(me.arreglo)
    },
    obtenerValor (valor) {
        let me = this
        var e  = document.getElementById('chk_'+entidad+'_'+valor)
        let arregloT = me.arregloMarcados.split(',')
        if (e.checked) {
            arregloT.push(valor)
        } else {
            for (let i=0; i< arregloT.length; i++) {
                if (arregloT[i] == valor) {
                    arregloT.splice(i,1)
                }
            }
        }
        me.arregloMarcados = arregloT.join()
        // alert(e.checked)
        // console.log(me.arregloMarcados)
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
    var element = document.getElementById('sec-local_'+me.entidad)
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
     background-color: #182fd2;
     cursor: pointer;
}

</style>