<template>
  <div>
    <h3>Hall of Fame</h3>
    <div class="row mb-3">
      <div class="col-md-4">
        <el-select v-model="year" @change="filter" style="width:100%">
          <el-option label="All-time" :value="0" />
          <el-option v-for="y in years" :key="y" :label="String(y)" :value="y" />
        </el-select>
      </div>
    </div>
    <el-table v-if="result" :data="result" stripe hover style="width:100%" @row-click="showPlayer">
      <el-table-column v-for="col in columns" :key="col" :prop="col" :label="col">
        <template #default="scope">
          <span v-if="col === 'nickname'" v-html="scope.row.nickname"></span>
          <span v-else>{{ scope.row[col] }}</span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
export default {
  props: ['results', 'totals'],
  data() {
    return {
      result: null,
      year: 0,
      years: [2020,2019,2018,2017,2016,2015,2014,2013,2012,2011,2010],
    };
  },
  computed: {
    columns() {
      if (!this.result || !this.result.length) return [];
      return Object.keys(this.result[0]);
    },
  },
  mounted() {
    this.result = this.results;
  },
  methods: {
    showPlayer(row) {
      window.location.href = '/player/' + encodeURIComponent(row.nickname);
    },
    filter() {
      axios.post('/results/halloffame', { year: this.year }).then((res) => {
        if (res.data.success === true) this.result = res.data.result;
      });
    },
  },
};
</script>
