<script>
import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins
import {misMixins} from '../mixins/mixin.js'

export default {
  extends: Line,
  mixins: [reactiveProp],
  props: ['options'],
  watch: {
    'options': {
      handler(newOption, oldOption) {
        this.renderChart(this.chartData, this.options)
      },
      deep: true
    }
  },
  mounted () {
    this.renderChart(this.chartData, this.options)
  }
}
</script>