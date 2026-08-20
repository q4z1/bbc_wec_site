<template>
  <div>
    <h3>Ranking</h3>
    <div class="filter-row">
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="year" :disabled="loading || alltime" style="width:100%" @change="filter">
          <el-option v-for="y in yearRange" :key="y.value" :label="y.text" :value="y.value" />
        </el-select>
      </div>
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="month" :disabled="loading || alltime || allyear" style="width:100%" @change="filter">
          <el-option v-for="m in monthRange" :key="m.value" :label="m.text" :value="m.value" />
        </el-select>
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-switch v-model="allyear" :disabled="loading || alltime" active-text="All-Year" @change="filter" />
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-switch v-model="alltime" :disabled="loading || allyear" active-text="All-Time" @change="filter" />
      </div>
    </div>

    <div v-loading="loading">
      <el-table :data="result" stripe style="width:100%" @row-click="showPlayer">
        <el-table-column prop="position" label="#" width="70" />
        <el-table-column prop="nickname" label="Nickname" sortable />
        <el-table-column prop="score" label="Score" sortable />
        <el-table-column prop="games" label="Games" sortable />
      </el-table>
    </div>

    <div v-if="avg_games" class="ranking-avg">a={{ avg_games }}</div>
  </div>
</template>
<script>
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

export default {
  props: ['stats', 'stats_year', 'stats_month'],
  data() {
    return {
      result: [],
      year: 0,
      month: 0,
      loading: false,
      allyear: false,
      alltime: false,
      avg_games: 0,
    };
  },
  computed: {
    yearRange() {
      const years = [];
      for (let i = new Date().getFullYear(); i >= 2012; i--) years.push({ value: i, text: String(i) });
      return years;
    },
    monthRange() {
      return MONTHS.map((text, i) => ({ value: i + 1, text }));
    },
  },
  mounted() {
    this.year = new Date().getFullYear();
    this.month = new Date().getMonth() + 1;
    if (this.stats_year) {
      this.year = this.stats_year;
      if (this.stats_month) this.month = this.stats_month;
      else this.allyear = true;
    } else {
      this.alltime = true;
    }
    this.result = this.formatResult(this.stats || []);
  },
  methods: {
    formatResult(stats) {
      this.avg_games = stats.length > 0 ? stats[0].avg_games : 0;
      return stats.map((s, i) => ({
        position: i + 1,
        nickname: s.nickname,
        score: s.score,
        games: s.games,
      }));
    },
    showPlayer(row) {
      window.location.href = '/player/' + encodeURIComponent(row.nickname);
    },
    filter() {
      this.loading = true;
      axios.post('/results/ranking', {
        year: !this.alltime ? this.year : 0,
        month: (!this.alltime && !this.allyear) ? this.month : 0,
      }).then((res) => {
        if (res.data.success === true) {
          this.result = this.formatResult(res.data.stats);
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
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.filter-cell { flex: 1 1 180px; min-width: 150px; }
.filter-cell--switch { flex: 0 0 auto; min-width: 0; }

.ranking-avg {
  margin-top: 0.5rem;
  text-align: center;
  font-style: italic;
}
</style>
