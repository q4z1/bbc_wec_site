<script>
import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins
export default {
    extends: Bar,
    props: {
        chartData: null,
        options: null,
    },
    mixins: [reactiveProp],
    mounted () {
        this.renderChart(this.chartData, this.options)
    }
}
</script>
<style lang="scss" scoped>

</style>