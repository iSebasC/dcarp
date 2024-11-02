<template>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <div class="col-lg-4 col-md-8 col-10 p-0 mt-3 bg-white border-2">
                        <div class="card-header bg-transparent border-0">
                            <h2 class="error-code text-center mb-2">500</h2>
                            <h3 class="text-uppercase text-center">Usted no tiene permiso para acceder a esta sección del Sistema</h3>
                        </div>
                        <!-- <div class="card-content"> -->
                            <!-- <div class="row py-2"> -->
                                <!-- <div class="col-12 col-sm-6 col-md-6 mb-1">
                                    <a href="index-2.html" class="btn btn-primary btn-block"><i class="la la-home"></i> Iniciar Sesión</a>
                                </div> -->
                                <!-- <div class="col-12 col-sm-6 col-md-6 mb-1">
                                    <a href="search-website.html" class="btn btn-danger btn-block"><i class="ft-search"></i>  Search</a>
                                </div> -->
                            <!-- </div>
                        </div> -->
                        <div class="card-footer bg-transparent">
                            <div class="row">
                                <!-- <p class="text-muted text-center col-12 pb-1">© <span class="year"></span> <a href="#">Admin </a>Carpio <i class="ft-heart pink"> </i> by <a href="http://themeforest.net/user/pixinvent/portfolio" target="_blank">PIXINVENT</a></p> -->
                                <div class="col-10 text-center my-2 mx-auto" >
                                    <button class="btn btn-primary btn-block" @click="irAlInicio()"><i class="mdi mdi-home-circle-outline"></i> Ir al Inicio</button>
                                </div>
                                <!-- <div class="col-12 text-center">
                                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-facebook"><span class="la la-facebook"></span></a>
                                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-twitter"><span class="la la-twitter"></span></a>
                                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-linkedin"><span class="la la-linkedin font-medium-4"></span></a>
                                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-github"><span class="la la-github font-medium-4"></span></a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
<script>
import { misMixins } from '../mixins/mixin';

export default {
    name: "error500",
    mixins: [misMixins],
    methods: {
       irAlInicio() {
        localStorage.clear();
        this.$router.replace("/");
       } 
    },
    async mounted() {
        this.initPages();
    }
}
</script>
<style scoped>
.error-code{font-size:9rem; margin-top: 2rem;}


</style>