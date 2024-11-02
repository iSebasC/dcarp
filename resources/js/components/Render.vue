<template>
  <div class="effect aside-bright mainnav-sm mainnav-fixed aside-float aside-fixed page-fixedbar navbar-fixed">
    <Header />
    <Menu />
  </div>
</template>
<script>
import Header from './Header'
import Menu from './Menu'
// import Inicio from '../components/Inicio'
import {misMixins} from '../mixins/mixin.js'

export default {
  name: 'Render',
  data () {
    return {
      // nombreF: '',
      // logeado: this.$attrs.estado,
      // principal: this.$attrs.principal,
      // secundario: this.$attrs.secundario,
      // usuario: this.$attrs.usuario
    }
  },
  mixins: [misMixins],
  components: {
    Header,
    Menu
  },
  updated() {

  },
  methods: {

  },
  activated: function () {
    // let me = this
    // me.logeado = localStorage.getItem('')
  },
  mounted () {
    let me = this
    // me.renderFondo(true)
  
    // let me = this
    // me.capturarMenu()
    // console.log('Renderizando...')
    // let tipoPage = document.getElementById('tipoPage').value
    // if (tipoPage == 'login') {
    //     document.body.style.backgroundImage = "url('https://images.pexels.com/photos/3815750/pexels-photo-3815750.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940')"
    //     document.body.style.backgroundSize = "100% 100%"
    //     document.body.style.backgroundRepeat="no-repeat"
    // } else {
      // window.setTimeout(function () {
      //   document.body.style.backgroundColor = "#F2F2F2"
      // },100);
    
    // }
   
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
body {
  background-color: #f2f2f2;
}
</style>
