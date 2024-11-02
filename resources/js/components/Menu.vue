<template>
  <aside class="left-sidebar" id="aside-sec">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li>
                            <!-- User Profile-->
                            <div class="user-profile d-flex no-block dropdown mt-3">
                                <div class="user-pic"><img src="/images/users/1.jpg" alt="users" class="rounded-circle" width="40" /></div>
                                <div class="user-content hide-menu ml-2">
                                    <a href="javascript:void(0)" class="" id="Userdd" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <h5 class="mb-0 user-name font-medium"> {{ usuario['usuario'] }} <i class="fa fa-angle-down"></i></h5>
                                        <span class="op-5 user-email" v-text="usuario['correoElectronico']"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="Userdd">
                                        <!-- <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user mr-1 ml-1"></i> My Profile</a>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet mr-1 ml-1"></i> My Balance</a>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email mr-1 ml-1"></i> Inbox</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-settings mr-1 ml-1"></i> Account Setting</a> -->
                                        <!-- <div class="dropdown-divider"></div> -->
                                        <a class="dropdown-item" href="javascript:void(0)" @click="salir"><i class="fa fa-power-off mr-1 ml-1"></i> Cerrar Sesión</a>
                                    </div>
                                </div>
                            </div>
                            <!-- End User Profile-->
                        </li>
                        <!-- <li class="p-15 mt-2"><a href="javascript:void(0)" class="btn btn-block create-btn text-white no-block d-flex align-items-center"><i class="fa fa-plus-square"></i> <span class="hide-menu ml-1">Create New</span> </a></li> -->
                        <!-- User Profile-->
                        <li class="sidebar-item" v-for="opcion in this.menuP" 
                            :key="opcion.menu_p.id" :id="opcion.menu_p.idHtml" 
                            >
                            <router-link :to="(opcion.menu_p.accion == null?'/#':opcion.menu_p.accion)" tag="a" 
                                :class="(opcion.cantSubs>0?'sidebar-link has-arrow waves-effect waves-dark':'sidebar-link waves-effect waves-dark sidebar-link')"
                                aria-expanded="false">
                                <i :class="opcion.menu_p.icono"></i>
                                <span class="hide-menu" v-text="opcion.menu_p.nombre "></span>
                            </router-link>
                            <ul aria-expanded="false" class="collapse  first-level" v-if="opcion.cantSubs > 0">
                                <li v-for="(opcion2, index) in opcion.menu_s" :key="index" class="sidebar-item">
                                    <router-link :to="opcion2.accion" tag="a" class="sidebar-link">
                                        <i :class="opcion2.icono"></i>
                                        <span class="hide-menu" v-text="opcion2.nombre"></span>
                                    </router-link>
                                </li>
                            </ul>       
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
</template>

<script>
import {misMixins} from '../mixins/mixin.js'
export default {
  name: 'Menu',
  mixins: [misMixins], 
  data () {
      return {
        menuP: [],
        usuario: []
      }
  },
  methods: {
    async salir () {
      localStorage.clear();
      this.$router.replace("/");

      // let rpta = await axios.get('/logout')

      // if (rpta.data == 'Ok') {
      //   localStorage.clear()
      //   this.$router.push('/')
      // }
    }
  },
  activated: function () {
    let me = this
  },
  mounted () {
    let me = this
    me.usuario = JSON.parse(localStorage.getItem('usuario'))
    // me.renderFondo(true)
    // console.log(localStorage.getItem('principal'))
    me.menuP = JSON.parse(localStorage.getItem('principal'))
    // console.log(me.menuP);
    //   console.log('ALERT', me.menuP)
  }
}
</script>
<style scoped>
/* .vertical-overlay-menu .main-menu .navigation li.has-sub > a:not(.mm-next)::after {
    content: "\f107";
    font-family: FontAwesome;
    font-size: 1rem;
    display: inline-block;
    position: absolute;
    top: 13px;
    transform: rotate(0);
    transition: -webkit-transform .2s ease-in-out;
    right: 34px;
}

.vertical-overlay-menu .main-menu .navigation li.open > a:not(.mm-next)::after {
    transform: rotate(90deg);
} */
</style>