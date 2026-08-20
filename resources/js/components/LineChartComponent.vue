<template>
  <div style="position:relative;height:300px;">
    <Line v-if="chartData" :data="chartData" :options="options" />
  </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  LineElement, PointElement, CategoryScale, LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

export default {
  components: { Line },
  props: {
    chartData: Object,
    options: Object,
  },
};
</script>
