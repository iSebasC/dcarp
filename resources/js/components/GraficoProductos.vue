<script>
import { Pie, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins
import {misMixins} from '../mixins/mixin.js'
import axios from 'axios'

export default {
  extends: Pie,
  mixins: [mixins.reactiveProp],
  props: ['chartData','options'],
  data () {
    return {
      gradient: null,
      gradient2: null,
      dataproducts: {},
      optionsproducts: {},
      labels: [],
      datos: []
    }
  },
  methods: {
    generarNumero(){
      let letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"]
      let numero = (Math.random()*15).toFixed(0)
  
    	return letras[numero]
    },
    colorHex(){
      let coolor = ""
    	for (let i=0; i<6; i++) {
		    coolor = coolor + this.generarNumero() 
	    }
	    return "#" + coolor
    },
    async cargarDatos () {
        let response = await axios.get('/reporteproducto')
        this.labels = response.data.labels
        this.datos = response.data.datos

        let cantidad = this.labels.length
        let colors = []
        for(let j=0; j< cantidad; j++) {
          colors.push(this.colorHex())
        }

        // this.gradient = this.$refs.canvas
        // .getContext("2d")
        // .createLinearGradient(0, 0, 0, 450);
        // this.gradient2 = this.$refs.canvas
        // .getContext("2d")
        // .createLinearGradient(0, 0, 0, 450);

        // this.gradient.addColorStop(0, "rgba(255, 0,0, 0.5)");
        // this.gradient.addColorStop(0.5, "rgba(255, 0, 0, 0.25)");
        // this.gradient.addColorStop(1, "rgba(255, 0, 0, 0)");

        // this.gradient2.addColorStop(0, "rgba(4, 69, 207, 0.9)");
        // this.gradient2.addColorStop(0.5, "rgba(4, 69, 207, 0.25)");
        // this.gradient2.addColorStop(1, "rgba(4, 69, 207, 0)");

        this.dataproducts = {
            labels: this.labels,
            datasets: [
                {
                    // backgroundColor: [this.gradient, this.gradient2, "#00D8FF"],
                  backgroundColor: colors,
                  data: this.datos
                }
            ]
        }

        this.optionsproducts = {
            responsive: true, 
            maintainAspectRatio: false
        }

        this.renderChart(this.dataproducts, this.optionsproducts)
    }
  },
  async mounted () {
    this.cargarDatos()
  },
  activated: async function () {
    await this.cargarDatos()
  }
}
</script>