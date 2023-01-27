<script>
import { Pie } from "vue-chartjs";

export default {
    extends: Pie,
    props: {
        chartData: null,
    },
    mounted() {
        this.renderChart(
            {
                labels: ['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'],
                datasets: [{
                    backgroundColor: [
                        'rgba(75, 168, 0, 1.0)',
                        'rgba(120, 187, 0, 1.0)',
                        'rgba(165, 205, 0, 1.0)',
                        'rgba(210, 224, 0, 1.0)',
                        'rgba(255, 242, 0, 1.0)',
                        'rgba(255, 193, 0, 1.0)',
                        'rgba(255, 145, 0, 1.0)',
                        'rgba(255, 97, 0, 1.0)',
                        'rgba(255, 48, 0, 1.0)',
                        'rgba(255, 0, 0, 1.0)'
                    ],
                    data: this.chartData
                }],
            },
            {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let values = data.datasets[tooltipItem.datasetIndex].data
                            let sum = values.reduce((sum, num) => { return sum + num }, 0)
                            let percent = ((values[tooltipItem.index] / sum) * 100).toFixed(1) + '%'
                            return data.labels[tooltipItem.index] + ': ' + percent
                        }
                    }
                }
            }
        );
    }
};
</script>
