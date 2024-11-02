<template>
    <div class="cronometro">
        <h6 class="" v-text="getHora"></h6>
        <!-- <input type="text" class="form-control form-control-sm" name="" v-model="getHora"> -->
    </div>
</template>
<script>

import {misMixins} from '../../mixins/mixin.js'

export default {
    name: 'DetallesAsignacion',
    mixins: [misMixins],
    data () {
        return {
            hora: 0,
            minutos: 0,
            segundos: 0, 
            tiempo: 0,
            restante: 0    
        }
    },
    methods: {

        actualizar () {
            
            // let f_act = new Date(Date.now())
            let me = this
            me.tiempo++
            me.hora = 0
            if (me.tiempo >= 3600) {
                me.hora = Math.trunc(me.tiempo/3600)
            }

            me.restante = (me.tiempo - me.hora * 3600 )
            me.minutos = Math.trunc( me.restante /60)
            me.segundos = me.restante - me.minutos * 60

            me.hora = me.hora < 10? `0${me.hora}`:me.hora
            me.minutos = me.minutos < 10? `0${me.minutos}`:me.minutos
            me.segundos = me.segundos < 10? `0${me.segundos}`:me.segundos            
      
            // me.hora  = f_act.getHours()
            // me.minutos = f_act.getMinutes()
            // me.segundos = f_act.getSeconds()

            // // console.log('seg', me.segundos);
        },
        inicializar () {
            setInterval(this.actualizar,1000);
        }
    },
    computed: {
        getHora () {
            return `${this.hora}:${this.minutos}:${this.segundos}`
        }
    },
    created () {
        this.inicializar()
        // setInterval(this.actualizar())
    },
    activated: function () {
        // this.inicializar()
    }
}
</script>