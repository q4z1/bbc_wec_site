<template>
  <div style="position:relative;height:300px;">
    <Bar v-if="chartData" :data="chartData" :options="options" />
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  BarElement, CategoryScale, LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
  components: { Bar },
  props: {
    chartData: Object,
    options: Object,
  },
};
</script>
