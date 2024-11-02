<template>
  <div class="content-wrapper" :id="`sec_${entidad}`">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <div class="row breadcrumbs-top d-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb pb-0">
              <li class="breadcrumb-item mb-0">
                <router-link tag="a" to="/" class="">Inicio</router-link>
              </li>
              <li class="breadcrumb-item mb-0 active">Citas</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body"> <!-- Default styling start -->
      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                      <li class="nav-item">
                        <a class="nav-link card-title" href="javascript:void(0);"><strong> Mis Citas</strong> </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-info" title="Crear Cita" style="margin-top:6px;" href="javascript:void(0)" @click="agregar">Nuevo</a>
                      </li>
                      <li class="nav-item">
                        <a class="mx-1 nav-link btn btn-sm btn-success" title="Actualizar" style="margin-top:5px;" href="javascript:void(0)" @click="cargarDatos()">
                          <i class="mdi mdi-refresh-auto icon-size"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                 
                  <div class="card-content collapse show pb-2">
                    <div class="demo-app">
                      <FullCalendar class="demo-app-calendar" ref="fullCalendar" :options="calendarOptions"></FullCalendar>
                    </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <MantenimientoCita ref="mantenimientocita" />

  </div>
</template>

<script>
import axios from 'axios'
import '@fullcalendar/core/vdom'
import {misMixins} from '../../mixins/mixin.js'
// import { INITIAL_EVENTS, createEventId } from '../../mixins/initCalendar.js'

import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listWeek from '@fullcalendar/list'

import interactionPlugin from '@fullcalendar/interaction'
// import langEsp from '@fullcalendar/core/locales/es-us'

import MantenimientoCita from './Mantenimiento'


export default {
  name: 'Cita',
  mixins: [misMixins],
  components: {
    FullCalendar,
    MantenimientoCita
  },
  data() {
    return {
      idCita: 0,
      paramFin: '',
      entidad:'cita',
      token: this.$store.state.token,
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin, // needed for dateClick
          listWeek
        ],
        contentHeight: 780,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        initialView: 'dayGridMonth',
        noEventsText:'No hay eventos para mostrar',
        initialEvents: [],
       // alternatively, use the `events` setting to fetch from a feed
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        moreLinkContent: function (args) {
          return `+${args.num} Más`;
        },
        weekends: true,
        // lang: 'es',
        locale: 'es-us',
        buttonText: {
            year: 'Año',
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Agenda'
        },
        slotLabelFormat:{
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        },//se visualizara de esta manera 01:00 AM en la columna de horas
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        },
        slotLabelInterval : '00:30:00',
        viewSubSlotLabel : true,
        timeFormat: 'H(:mm)',
        allDayText: 'Todo el día',
        select: this.crear,
        eventClick: this.handleEventClick,
        eventsSet: this.handleEvents,
        // events: this.eventos
        /* you can update a remote database when these fire:
        eventAdd:
        eventChange:
        eventRemove:
        */
      },
    }
  },
  computed: {
  },
  methods: {
    crear(selectInfo) {
      let me = this
      // console.log(selectinfo)
      // console.log('Api', calendarApi.removeAllEventSources())
      me.generarCita(0,selectInfo)
     
      // let title = prompt('Please enter a new title for your event')
      // let calendarApi = selectInfo.view.calendar

      // calendarApi.unselect() // clear date selection

      // if (title) {
      //   calendarApi.addEvent({
      //     id: createEventId(),
      //     title,
      //     start: selectInfo.startStr,
      //     end: selectInfo.endStr,
      //     allDay: selectInfo.allDay
      //   })
      // }
    },
    handleEventClick(clickInfo) {
      let me = this
      let id = clickInfo.event.id
      // console.log(clickInfo)
      let calendarApi= this.$refs.fullCalendar.getApi()
      me.generarCita(id,calendarApi)
     
      // if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
      //   clickInfo.event.remove()
      // }
    },
    handleEvents(events) {
      this.currentEvents = events
    },
    handleDateClick(arg) {
      let me = this
      me.generarCita(0)
      // if (confirm('Would you like to add an event to ' + arg.dateStr + ' ?')) {
      //   this.calendarEvents.push({ // add new event data
      //     title: 'New Event',
      //     start: arg.date,
      //     allDay: arg.allDay
      //   })
      // }
    },
    generarCita(id,object) {
      let me = this
      me.isValidSession()
      me.idCita = id
      this.$store.state.mostrarModal = !this.$store.state.mostrarModal
      me.$refs.mantenimientocita.showModal(id,object)
    },
    agregar () {
      let me = this
      let calendarApi= this.$refs.fullCalendar.getApi()
      me.generarCita(0,calendarApi)
    },
    async buscarCitas() {
      let me = this
      
      let response = await axios({
        method: 'post',
        url: '/cita'
      })

      // // this.calendarEvents.push({ // add new event data
      // //   title: 'New Event',
      // //   start: arg.date,
      // //   allDay: arg.allDay
      // // })

      let currentEvents = response.data.citas
      // // console.log(me.currentEvents)
      // // console.log(arrCitas)
      // // alert('Ok')
      // // let arreglo = []
      // // me.currentEvents.map(function(value, key) {
      // //   alert(value)
      // // })
      let arreglo = []
      for(let i=0; i< currentEvents.length; i++) {
        const e = currentEvents[i]
        arreglo.push(e)
        // this.calendarEvents.push({ // add new event data
        //   title: e.title,
        //   start: e.start,
        //   end: e.end,
        //   allDay: e.allDay
        // })
        // console.log(e)
        // me.currentEvents.push(e)
        // console.log(e)
      }

      me.eventos = arreglo
      // console.log(me.currentEvents)
      // me.currentEvents = arreglo

      // console.log(me.currentEvents)
      // arrCitas.forEach(element => {

      // })
      // me.currentEvents = response.data.citas 
      // me.inventarios = response.data.inventarios
      // me.total = response.data.cantidad
      // me.pageActual = response.data.page
      // me.opciones = response.data.paginador
      // me.inicio = response.data.inicio
      // me.fin = response.data.fin
      // me.paramInicio = response.data.paramInicio
      // me.paramFin = response.data.paramFin
      
      // var el2 = document.getElementById('tabla-inventario')
      // el2.classList.remove('d-none')
    
      // alert(filtro)
    },
    async cargarcitas () {
      let response = await axios.get('/cargarcitas')

      let currentEvents = response.data.citas
      var events = []
      for(let i=0; i< currentEvents.length; i++) {
          const e = currentEvents[i]
          events.push({
              id: e.id,
              title: e.title,
              start: e.start,
              end: e.end,
              allDay: e.allDay,
              backgroundColor: e.color,
              textColor: e.textColor
          })
      }
      return events
    },
    async cargarDatos () {
      let me = this
      let calendarApi= this.$refs.fullCalendar.getApi()
      calendarApi.removeAllEventSources()
      let arr = await me.cargarcitas()
      calendarApi.addEventSource(arr)
      calendarApi.refetchEvents()
    }
  },
  created() {
    this.$on('buscarCitas', function () {
        this.cargarcitas()
    })
    this.$on('loadCitasInit', function () {
        this.cargarDatos()
    })
        
  },
  activated: async function () {
    let me = this
    me.isValidSession()
    this.$store.state.mostrarModal = !this.$store.state.mostrarModal
    this.$route.meta.auth = localStorage.getItem('autenticado')
    window.setTimeout( function () {
      me.cargarDatos()
    }, 1000)
    
    // onMounted(() => {
    //   setTimeout(function () {
    //     window.dispatchEvent(new Event('resize'));
    //   }, 1);
    // });
    // me.generarModal(0,'')
  },
  mounted () {
    // setTimeout(function () {
      // window.dispatchEvent(new Event('resize'));
      // console.log('mounted Cita....')
    // }, 1);
  },
  beforeDestroy: function () {
    let me = this
    var element = document.getElementById('sec_'+me.entidad)
    element.classList.add('d-none')
    // this.$store.state.mostrarModal = false 
  },
  deactivated: function () {
    // this.$store.state.mostrarModal = false 
      // var element = document.getElementById('sec-local')
    // element.classList.add('d-none')
  }
}
</script>
<style scoped>
 select {
   cursor: pointer;
 }
 .demo-app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
.demo-app-top {
  margin: 0 0 3em;
}
.demo-app-calendar {
  margin: 0 auto;
  max-width: 1000px;
}
b {
  /* used for event dates/times */
  margin-right: 3px;
}
ul {
  margin: 0;
  padding: 0 0 0 1.5em;
}

li {
  margin: 1.5em 0;
  padding: 0;
}
/* .fc .fc-view-harness {
  position: initial;
} */
</style>