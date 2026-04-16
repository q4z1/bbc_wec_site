<template>
  <Pie :data="computedData" :options="chartOptions" />
</template>

<script>
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const COLORS = [
  'rgba(75,168,0,1)', 'rgba(120,187,0,1)', 'rgba(165,205,0,1)',
  'rgba(210,224,0,1)', 'rgba(255,242,0,1)', 'rgba(255,193,0,1)',
  'rgba(255,145,0,1)', 'rgba(255,97,0,1)',  'rgba(255,48,0,1)',
  'rgba(255,0,0,1)',
];

export default {
  components: { Pie },
  props: { chartData: Array },
  computed: {
    computedData() {
      return {
        labels: ['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th'],
        datasets: [{
          backgroundColor: COLORS,
          data: this.chartData || [],
        }],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label(ctx) {
                const values = ctx.dataset.data;
                const sum = values.reduce((a, b) => a + b, 0);
                const pct = ((values[ctx.dataIndex] / sum) * 100).toFixed(1) + '%';
                return ctx.label + ': ' + pct;
              },
            },
          },
        },
      };
    },
  },
};
</script>
