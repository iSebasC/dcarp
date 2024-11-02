<template>
  <div id="app" class="main-wrapper">
    <!-- PRE-LOADER -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <router-view></router-view>
    <div id="main-wrapper">
      <router-view name="headrender" />
      <router-view name="menurender" />

      <div class="page-wrapper">
        <!-- <div class="page-breadcrumb">
          <div class="row">
              <div class="col-5 align-self-center">
                  <h4 class="page-title">Basic Initialisation</h4>
                  <div class="d-flex align-items-center">
                      <nav aria-label="breadcrumb">
                          <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="#">Home</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Library</li>
                          </ol>
                      </nav>
                  </div>
              </div>
              <div class="col-7 align-self-center">
                <div class="d-flex no-block justify-content-end align-items-center">
                  <div class="m-r-10">
                    <div class="lastmonth"></div>
                  </div>
                  <div class=""><small>LAST MONTH</small>
                    <h4 class="text-info m-b-0 font-medium">$58,256</h4>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <div class="container-fluid"> 
            <keep-alive>
              <router-view name="render" />
            </keep-alive>
          </div>
          <footer class="footer text-center">
            Todos los derechos reservados. Diseñado y Desarrollado por <a href="https://dboxperu.com">Dbox Peru</a>.
          </footer>
      </div>
    </div>
  </div>
</template>
<script>
import {misMixins} from './mixins/mixin.js'

export default {
  name: 'App',
  data () {
    return { 
      authorized: false
    }
  },
  mixins: [misMixins],
  components: {
  },
  methods: {
    isLogeado () {
      let me = this
      me.authorized = localStorage.getItem('autenticado')
    }
  },
  updated() {

  },
  activated () {
    let me = this
    me.isLogeado()
  },
  mounted () {
    // let me = this;
    // me.importarScript('/js/assets/template/app.min.js')
    // me.importarScript('/js/libs/perfect-scrollbar.jquery.min.js')
    // me.importarScript('/js/assets/template/app.init.light-sidebar.js')
    // me.importarScript('/js/assets/template/app-style-switcher.js')
    // me.importarScript('/js/libs/sparkline.js')
    // me.importarScript('/js/assets/template/sidebarmenu.js')
    // me.importarScript('/js/assets/template/custom.min.js')
    // me.capturarMenu()
    // console.log('Renderizando...')
    
    // if (this.$store.state.autorizado === false) {
    //   this.$router.push('/')
    // }

    // setTimeout(me.forzarCierre, 60000)
    // 1800000
    // let me = this
    // localStorage.setItem('autenticado', this.$store.state.autorizado)
    // me.logeado = this.$store.state.autorizado
  }
}
</script>
<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter, .fade-leave-to {
  opacity: 1;
}
</style>
