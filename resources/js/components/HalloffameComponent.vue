<template>
  <div>
    <h3>Hall of Fame</h3>
    <div class="filter-row">
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="year" style="width:100%" @change="filter">
          <el-option v-for="y in yearRange" :key="y.value" :label="y.text" :value="y.value" />
        </el-select>
      </div>
    </div>

    <div v-loading="loading">
      <el-table :data="result" stripe style="width:100%" @row-click="showPlayer">
        <el-table-column prop="id" label="ID" width="90" />
        <el-table-column prop="nickname" label="Nickname" />
      </el-table>
    </div>
  </div>
</template>
<script>
export default {
  props: ['results', 'totals'],
  data() {
    return {
      result: [],
      year: 0,
      loading: false,
    };
  },
  computed: {
    yearRange() {
      const years = [{ value: 0, text: 'All-time' }];
      for (let i = 2020; i >= 2010; i--) years.push({ value: i, text: String(i) });
      return years;
    },
  },
  mounted() {
    this.result = this.results || [];
  },
  methods: {
    showPlayer(row) {
      window.location.href = '/player/' + encodeURIComponent(row.nickname);
    },
    filter() {
      this.loading = true;
      axios.post('/results/halloffame', { year: this.year }).then((res) => {
        if (res.data.success === true) {
          this.result = res.data.result;
        }
        this.loading = false;
      }).catch(() => { this.loading = false; });
    },
  },
};
</script>
<style scoped>
.filter-row {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.filter-cell { flex: 0 1 220px; }
</style>
