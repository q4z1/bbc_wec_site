<script>
import { Bar } from "vue-chartjs";

export default {
    extends: Bar,
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
                    data: this.chartData,
                    barPercentage: 1,
                    categoryPercentage: 1,
                }],
            },
            {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            display: false
                        },
                        gridLines: {
                            display: true,
                            drawOnChartArea: false,
                            drawTicks: false
                        }
                    }],
                    yAxes: [{
                        display: false,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
            }
        );
    }
};
</script>
