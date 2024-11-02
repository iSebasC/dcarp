<template>
   <header class="topbar" id="header-sec">
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
          <div class="navbar-header">
              <!-- This is for the sidebar toggle which is visible on mobile only -->
              <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
              <!-- ============================================================== -->
              <!-- Logo -->
              <!-- ============================================================== -->
              <router-link class="navbar-brand" to="/" target="_blank">
                  <!-- Logo icon -->
                  <b class="logo-icon">
                      <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                      <!-- Dark Logo icon -->
                      <img src="/images/carpio_logo.png" width="32" alt="homepage" class="dark-logo" />
                      <!-- Light Logo icon -->
                      <img src="/images/carpio_logo.png" width="32" alt="homepage" class="light-logo" />
                  </b>
                  <!--End Logo icon -->
                  <!-- Logo text -->
                  <span class="logo-text">
                        <!-- dark Logo text -->
                        <img src="/images/carpio_text.png"  width="100" alt="homepage" class="dark-logo" />
                        <!-- Light Logo text -->    
                        <img src="/images/carpio_text.png"  width="100" class="light-logo" alt="homepage" />
                  </span>
              </router-link>
              <!-- ============================================================== -->
              <!-- End Logo -->
              <!-- ============================================================== -->
              <!-- ============================================================== -->
              <!-- Toggle which is visible on mobile only -->
              <!-- ============================================================== -->
              <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
          </div>
          <!-- ============================================================== -->
          <!-- End Logo -->
          <!-- ============================================================== -->
          <div class="navbar-collapse collapse" id="navbarSupportedContent">
              <!-- ============================================================== -->
              <!-- toggle and nav items -->
              <!-- ============================================================== -->
              <ul class="navbar-nav float-left mr-auto">
                  <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                  <!-- ============================================================== -->
                  <!-- mega menu -->
                  <!-- ============================================================== -->
                 
                  <!-- ============================================================== -->
                  <!-- End mega menu -->
                  <!-- ============================================================== -->
                  <!-- ============================================================== -->
                  <!-- create new -->
                  <!-- ============================================================== -->
                  <!-- <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-none d-md-block">Create New <i class="fa fa-angle-down"></i></span>
                        <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>   
                      </a>
                      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                          <a class="dropdown-item" href="#">Action</a>
                          <a class="dropdown-item" href="#">Another action</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">Something else here</a>
                      </div>
                  </li> -->
                  <!-- ============================================================== -->
                  <!-- Search -->
                  <!-- ============================================================== -->
                  <!-- <li class="nav-item search-box"> <a class="nav-link waves-effect waves-dark" href="javascript:void(0)"><i class="ti-search"></i></a>
                      <form class="app-search position-absolute">
                          <input type="text" class="form-control" placeholder="Search &amp; enter"> <a class="srh-btn"><i class="ti-close"></i></a>
                      </form>
                  </li> -->
              </ul>
              <!-- ============================================================== -->
              <!-- Right side toggle and nav items -->
              <!-- ============================================================== -->
              <ul class="navbar-nav float-right">
                  <!-- ============================================================== -->
                  <!-- create new -->
                  <!-- ============================================================== -->
                  <!-- <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right  animated bounceInDown" aria-labelledby="navbarDropdown2">
                          <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-us"></i> English</a>
                          <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-fr"></i> French</a>
                          <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-es"></i> Spanish</a>
                          <a class="dropdown-item" href="#"><i class="flag-icon flag-icon-de"></i> German</a>
                      </div>
                  </li> -->
                  <!-- ============================================================== -->
                  <!-- Comment -->
                  <!-- ============================================================== -->
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-bell font-24"></i>
                          
                      </a>
                      <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown">
                          <span class="with-arrow"><span class="bg-primary"></span></span>
                          <ul class="list-style-none">
                              <li>
                                  <div class="drop-title bg-primary text-white">
                                      <h4 class="m-b-0 m-t-5" id="numNotificaciones"> Nuevas</h4>
                                      <span class="font-light">Notificaciones</span>
                                  </div>
                              </li>
                              <li>
                                  <div class="message-center notifications" id="renderNotificaciones">
                                      <!-- <a href="javascript:void(0)" class="message-item">
                                          <span class="btn btn-danger btn-circle"><i class="fa fa-link"></i></span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Luanch Admin</h5> <span class="mail-desc">Just see the my new admin!</span> <span class="time">9:30 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="btn btn-success btn-circle"><i class="ti-calendar"></i></span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Event today</h5> <span class="mail-desc">Just a reminder that you have event</span> <span class="time">9:10 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="btn btn-info btn-circle"><i class="ti-settings"></i></span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Settings</h5> <span class="mail-desc">You can customize this template as you want</span> <span class="time">9:08 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="btn btn-primary btn-circle"><i class="ti-user"></i></span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                      </a> -->
                                  </div>
                              </li>
                              <!-- <li>
                                  <a class="nav-link text-center m-b-5 text-dark" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                              </li> -->
                          </ul>
                      </div>
                  </li>
                  <!-- ============================================================== -->
                  <!-- End Comment -->
                  <!-- ============================================================== -->
                  <!-- ============================================================== -->
                  <!-- Messages -->
                  <!-- ============================================================== -->
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" id="2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="font-24 mdi mdi-comment-processing"></i>
                          
                      </a>
                      <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown" aria-labelledby="2">
                          <span class="with-arrow"><span class="bg-danger"></span></span>
                          <ul class="list-style-none">
                              <li>
                                  <div class="drop-title text-white bg-danger">
                                      <h4 class="m-b-0 m-t-5" id="numMensajes">Nuevas</h4>
                                      <span class="font-light">Mensajes</span>
                                  </div>
                              </li>
                              <li>
                                  <div class="message-center message-body" id="renderMensajes">
                                      <!-- <a href="javascript:void(0)" class="message-item">
                                          <span class="user-img"> <img src="/images/users/1.jpg" alt="user" class="rounded-circle"> <span class="profile-status online pull-right"></span> </span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:30 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="user-img"> <img src="/images/users/2.jpg" alt="user" class="rounded-circle"> <span class="profile-status busy pull-right"></span> </span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="user-img"> <img src="/images/users/3.jpg" alt="user" class="rounded-circle"> <span class="profile-status away pull-right"></span> </span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span> <span class="time">9:08 AM</span> </div>
                                      </a>
                                      <a href="javascript:void(0)" class="message-item">
                                          <span class="user-img"> <img src="/images/users/4.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                          <div class="mail-contnet">
                                              <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                      </a> -->
                                  </div>
                              </li>
                              <!-- <li>
                                  <a class="nav-link text-center link text-dark" href="javascript:void(0);"> <b>See all e-Mails</b> <i class="fa fa-angle-right"></i> </a>
                              </li> -->
                          </ul>
                      </div>
                  </li>
                  <!-- ============================================================== -->
                  <!-- End Messages -->
                  <!-- ============================================================== -->
                  <!-- ============================================================== -->
                  <!-- User profile and search -->
                  <!-- ============================================================== -->
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="/images/users/1.jpg" alt="user" class="rounded-circle" width="31"></a>
                      <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                          <span class="with-arrow"><span class="bg-primary"></span></span>
                          <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
                              <div class=""><img src="/images/users/1.jpg" :alt="usuario['usuario']" class="img-circle" width="60"></div>
                              <div class="m-l-10">
                                  <h4 class="m-b-0" v-text="usuario['usuario']"></h4>
                                  <p class=" m-b-0" v-text="usuario['correoElectronico']"></p>
                              </div>
                          </div>
                          <!-- <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user m-r-5 m-l-5"></i> My Profile</a> -->
                          <!-- <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet m-r-5 m-l-5"></i> My Balance</a>
                          <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email m-r-5 m-l-5"></i> Inbox</a> -->
                          <!-- <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="javascript:void(0)"><i class="ti-settings m-r-5 m-l-5"></i> Account Setting</a> -->
                          <!-- <div class="dropdown-divider"></div> -->
                          <a class="dropdown-item" href="javascript:void(0)" @click="salir"><i class="fa fa-power-off m-r-5 m-l-5"></i> Cerrar Sesión</a>
                          <!-- <div class="dropdown-divider"></div>
                          <div class="p-l-30 p-10"><a href="javascript:void(0)" class="btn btn-sm btn-success btn-rounded">View Profile</a></div> -->
                      </div>
                  </li>
                  <!-- ============================================================== -->
                  <!-- User profile and search -->
                  <!-- ============================================================== -->
              </ul>
          </div>
      </nav>
    </header>
</template>
<script>

import axios from 'axios'
import {misMixins} from '../mixins/mixin.js'
export default {
  name: 'Header',
  mixins: [misMixins],
  data () {
    return {
      usuario: [],
      primeraCarga: 0
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
  activated: async function () {
    // let me = this;
    // me.importarScript('/js/assets/template/app.min.js')
    // me.importarScript('/js/libs/perfect-scrollbar.jquery.min.js')
    // me.importarScript('/js/assets/template/app.init.light-sidebar.js')
    // me.importarScript('/js/assets/template/app-style-switcher.js')
    // me.importarScript('/js/libs/sparkline.js')
    // me.importarScript('/js/assets/template/sidebarmenu.js')
    // me.importarScript('/js/assets/template/custom.min.js')
    console.log('activated...')
  },
  async mounted () {
    let me =this
    me.usuario = JSON.parse(localStorage.getItem('usuario'))
    // me.renderFondo(true)
    // setTimeout(() => {
      // me.deleteScript('app')
      // me.deleteScript('jquery')
      // me.deleteScript('app-init')
      // me.deleteScript('app-style')
      // me.deleteScript('sparkline')
      // me.deleteScript('sidebarmenu')
      // me.deleteScript('custom')

      me.importarScript('/js/libs/perfect-scrollbar.jquery.min.js', 'jquery')
      me.importarScript('/js/assets/template/app.min.js','app')
      me.importarScript('/js/libs/sparkline.js','sparkline')   
      me.importarScript('/js/assets/template/app-style-switcher.js', 'app-style')
      me.importarScript('/js/assets/template/sidebarmenu.js','sidebarmenu')
      me.importarScript('/js/assets/template/custom.min.js','custom')
    // },2000, me);
  
    me.importarScript('/js/assets/template/app.init.light-sidebar.js','app-init')
    await me.loadFirstNotifications();
    console.log('mounted..')
  },
  destoyed () {
    let me  = this
   
    console.log('destroy...')
  }
}
</script>

<style scoped>
/* .header-navbar .navbar-header .navbar-brand {
    padding-top: .31875rem;
    margin-right: 0;
}
.header-navbar .navbar-header .navbar-brand .brand-logo {
    width:52% !important;
}
.la, .las {
    font-size:22px;
} 

@media (max-width: 767.98px) {
  .header-navbar .navbar-header .navbar-brand {
      position: absolute;
      left: 61%;
      top: 0;
      transform: translate(-50%,0);
  }
} */

</style>