<template>
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center img-login">
        <div class="auth-box on-sidebar">
            <div id="loginform">
                <div class="logo">
                    <span class="db"><img src="/images/logo-carpio.png" width="90" alt="logo" /></span>
                    <h5 class="font-medium m-b-10 m-t-20">Iniciar Sesión</h5>
                </div>
                <!-- Form -->
                <div class="row">
                    <div class="col-12">
                        <form class="form-horizontal m-t-20" id="loginform" action="/login" method="POST" onsubmit="return false">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-lg" id="user" name="user" placeholder="Usuario" aria-label="Usuario" @keyup.enter="logear" required="" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" class="form-control form-control-lg" name="pass" id="pass" placeholder="Contraseña" aria-label="Contraseña" @keyup.enter="logear" required="" aria-describedby="basic-addon1">
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1">Recordarme</label>
                                        <!-- <a href="javascript:void(0)" id="to-recover" class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-xs-12 p-b-20">
                                    <button class="btn btn-block btn-lg btn-info" @click="logear" type="button" id="btnLogin">Iniciar Sesión</button>
                                </div>
                            </div>
                            <div class="row">
                              <div id="error-message">
                              </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                                    <div class="social">
                                        <a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip" title="" data-original-title="Login with Facebook"> <i aria-hidden="true" class="fab  fa-facebook"></i> </a>
                                        <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="" data-original-title="Login with Google"> <i aria-hidden="true" class="fab  fa-google-plus"></i> </a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-b-0 m-t-10">
                                <div class="col-sm-12 text-center">
                                    Don't have an account? <a href="authentication-register1.html" class="text-info m-l-5"><b>Sign Up</b></a>
                                </div>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
            <!-- <div id="recoverform">
                <div class="logo">
                    <span class="db"><img src="/images/logo-carpio.png" alt="logo" /></span>
                    <h5 class="font-medium m-b-20">Recover Password</h5>
                    <span>Enter your Email and instructions will be sent to you!</span>
                </div>
                <div class="row m-t-20">
                  <form class="col-12" action="index.html">
                    <div class="form-group row">
                      <div class="col-12">
                        <input class="form-control form-control-lg" type="email" required="" placeholder="Username">
                      </div>
                    </div>
                    <div class="row m-t-20">
                      <div class="col-12">
                        <button class="btn btn-block btn-lg btn-danger" type="submit" name="action">Reset</button>
                      </div>
                    </div>
                  </form>
                </div>
            </div> -->
        </div>
    </div>


    <!-- <div class="app-content container center-layout mt-2">
      <div class="content-wrapper">
        <div class="content-body mt-5">
            <section class="row flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-lg-4 col-md-8 col-10 box-shadow-2 p-0">
                        <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                            <div class="card-header border-0 pb-0">
                                <div class="card-title text-center">
                                    <img src="/images/logo-carpio.png" style="width:40%;" alt="branding logo">
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>Iniciar Sesión</span></h6>
                            </div>
                            <div class="card-content">
                              <div class="card-body">
                                <form class="form-horizontal" id="formLogin" action="/login" method="POST" onsubmit="return false">
                                  <fieldset class="form-group position-relative has-icon-left">
                                      <input type="text" name="user" class="form-control" id="user" placeholder="Usuario"  @keyup.enter="logear" required>
                                      <div class="form-control-position">
                                          <i class="la la-user"></i>
                                      </div>
                                  </fieldset>
                                  <fieldset class="form-group position-relative has-icon-left">
                                      <input type="password" name="pass" id="pass" class="form-control" placeholder=" Contraseña"  @keyup.enter="logear" required>
                                      <div class="form-control-position">
                                          <i class="la la-key"></i>
                                      </div>
                                  </fieldset>
                                  <div class="form-group row">
                                      <div class="col-sm-6 col-12 text-center text-sm-left pr-0">
                                        <fieldset>
                                            <input type="checkbox" id="remember-me" class="chk-remember cursor-pointer">
                                            <label for="remember-me"> Recordarme</label>
                                        </fieldset>
                                      </div>
                                  </div>
                                  <button type="button" @click="logear" class="btn btn-outline-info btn-block" id="btnLogin">Iniciar Sesión</button>

                                  <div id="error-message">
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
    </div> -->
</template>

<script>
import axios from 'axios'
import {misMixins} from '../mixins/mixin.js'
export default {
  name: 'Login',
  data () {
    return {
      usuario: '',
      pass: '',
      html: 'Iniciar Sesión'
    }
  },
  mixins: [misMixins],
  methods: {
    async logear () {
      let me = this
      me.bloquearButton(`btnLogin`)
      this.$store.state.token = document.getElementById('tokenApp').value 
      localStorage.setItem('token', this.$store.state.token)
      //document.querySelector('meta[name="csrf-token"]').content
      /*toastr.info('Cargando... Espere, Por Favor ', 'Mensaje del Sistema', {
          closeMethod: 'fadeOut',
          closeButton: true,
          showEasing: 'swing',
          closeEasing: 'linear',
          preventDuplicates: true,
          progressBar: true,
          newestOnTop: false,
          positionClass: 'toast-top-right'
        }) */

        let response = await axios({
          method: 'post',
          url: '/login',
          data: {
            'user': document.getElementById('user').value,
            'pass': document.getElementById('pass').value,
            '_token': localStorage.getItem('token')
          }
        })
        // eslint-disable-next-line no-unused-vars
        // console.log(response, response.data)
        if (response.data.estado === true) {
          // eslint-disable-next-line no-undef
          /*toastr.success('Operación Exitosa ', 'Mensaje del Sistema', {
            closeMethod: 'fadeOut',
            closeButton: true,
            showEasing: 'swing',
            closeEasing: 'linear',
            preventDuplicates: true,
            progressBar: true,
            newestOnTop: false,
            positionClass: 'toast-top-right'
          }) */
          this.$store.state.autorizado = response.data.estado
          this.$store.state.usuario = JSON.stringify(response.data.usuario)
          // this.$store.state.colaborador = response.data.colaborador
          // console.log(Array.from(response.data.menuP))
          this.$store.state.menuP = JSON.stringify(response.data.menuP)
          // this.$store.state.menu2 = response.data.menu2
          
          if (localStorage) {
              localStorage.setItem('autenticado', this.$store.state.autorizado)
              localStorage.setItem('usuario', this.$store.state.usuario)
              // localStorage.setItem('colaborador', this.$store.state.colaborador)
              localStorage.setItem('principal', this.$store.state.menuP)
              localStorage.setItem('menuPath', JSON.stringify(response.data.menuPath))
              // localStorage.setItem('menu2', this.$store.state.menu2)
              // console.log('Principal', localStorage.getItem('principal'))
              this.$router.replace(`/${response.data.redireccionar}`)
              // console.log('redirect: ', response.data.redireccionar)
              
              // me.importarScript('http://127.0.0.1:8086/js/assets/core/app-menu.min.js')
              // me.importarScript('http://127.0.0.1:8086/js/assets/core/app.min.js')
              // window.setTimeout( function () {
              //   me.initAnimation()
              // }, 1000)
              // window.setTimeOut(,1000)
            // localStorage.setItem('')
          } else {
            localStorage.clear()
            alert('Actualice su Navegador Por Favor....');
          }
          
          // window.location.reload()
          // this.$router.replace('/inicio')
          // me.limpiarCampos()
     
        } else {
          /* toastr.error(response.data.msjeError, 'Mensaje del Sistema', {
            closeButton: false,
            debug: false,
            newestOnTop: false,
            progressBar: true,
            positionClass: 'toast-bottom-left',
            preventDuplicates: false,
            onclick: null,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '5000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
          }) */
          localStorage.setItem('autenticado', false)
          
          document.getElementById('error-message').innerHTML = '<div style="margin-top:25px;"><div style="background-color: #ffdbdf !important;" class="alert alert-danger text-center" role="alert">'+response.data.error +'</a></div></div>'
        }
        me.desbloquearButton('btnLogin', this.html)
    },
    limpiarCampos () {
      document.getElementById('user').value = ''
      document.getElementById('pass').value = ''
    },
    validarCampos () {
      // let me = this
      // eslint-disable-next-line no-undef
    }
  },
  activated: async function () {
    localStorage.setItem('permitido',true)
    this.$store.state.mostrarModal = false
    // console.log('estoy aquie ')
  },
  beforeDestroy: function () {
    this.$store.state.mostrarModal = false 
// console.log('beforedestroy')
  },
  deactivated: function () {
    // eslint-disable-next-line no-undef
    // $('.sec_login').addClass('d-none')
// console.log('deactivated')

  },
  updated () {
    // let me = this
    // me.validarCampos()
  },
  mounted () {
    let me = this
    me.renderFondo(false);
    // window.setTimeout(function () {
    // },100);
  
    // eslint-disable-next-line no-undef
    
    document.getElementById('user').focus()
    me.validarCampos()
    me.initLogin();
  }
}
// $(".preloader").fadeOut();
</script>
<style scoped>
  .img-login {
    background:url("https://images.pexels.com/photos/210182/pexels-photo-210182.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940 100% 100% no-repeat center center");
  }
</style>
<!-- <style scoped>
  .page-fixedbar-container{
    overflow:hidden !important;
  }
  #page-content{
     padding-top:0 !important;
  }
  .page-fixedbar-content{
      overflow:hidden !important;
  }
  .nano-content{
      overflow:hidden !important;
  }
  .mar-top{
    margin-top: 0 !important;
  }
  .pad-all {
    padding-right: 30px;
  }
  .panel {
      padding:10px 0;
      -webkit-box-shadow: 0px 19px 38px -20px rgba(107,84,84,1) !important;
      -moz-box-shadow: 0px 19px 38px -20px rgba(107,84,84,1) !important;
      box-shadow: 0px 19px 38px -20px rgba(107,84,84,1) !important;
  }
</style> -->
