<template>
  <div>
    <div class="results-head">
      <h3>Results</h3>
      <div v-if="arole !== ''" class="results-gameno">
        <el-input v-model="gameno" placeholder="Wec" clearable @input="searchGame">
          <template #append>#</template>
        </el-input>
      </div>
    </div>

    <div class="filter-row">
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="year" :disabled="searching || alltime" style="width:100%" @change="filter()">
          <el-option v-for="y in yearRange" :key="y.value" :label="y.text" :value="y.value" />
        </el-select>
      </div>
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="month" :disabled="searching || alltime" style="width:100%" @change="filter()">
          <el-option v-for="m in monthRange" :key="m.value" :label="m.text" :value="m.value" />
        </el-select>
      </div>
      <div class="filter-cell" v-loading="loading">
        <el-select v-model="type" :disabled="searching" style="width:100%" @change="filter()">
          <el-option v-for="t in gameTypes" :key="t.value" :label="t.text" :value="t.value" />
        </el-select>
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-switch v-model="alltime" :disabled="searching" active-text="All-Time" @change="filter()" />
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-button type="primary" @click="reset">Reset</el-button>
      </div>
    </div>

    <template v-if="result && result.length">
      <el-pagination
        v-model:current-page="page"
        :page-size="10"
        :total="total"
        layout="prev, pager, next"
        background
        style="margin-bottom:0.75rem;"
        @current-change="filter" />

      <div v-loading="loading">
        <el-table :data="result" stripe style="width:100%" @row-click="showGame">
          <el-table-column prop="wec" label="Wec" width="90" fixed />
          <el-table-column prop="type" label="Type" width="100" />
          <el-table-column prop="started" label="Started" width="170" />
          <el-table-column v-for="i in 10" :key="i" :prop="'p' + i" :label="'P' + i" min-width="130" />
        </el-table>
      </div>

      <el-pagination
        v-model:current-page="page"
        :page-size="10"
        :total="total"
        layout="prev, pager, next"
        background
        @current-change="filter" />
    </template>
    <p v-else style="margin-top:1.5rem;">No games found for this period.</p>
  </div>
</template>
<script>
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

const GAME_TYPES = [
  { value: 1, text: 'regular' },
  { value: 5, text: 'monthly' },
  { value: 6, text: 'yearly' },
  { value: 0, text: 'all' },
];

export default {
  props: ['results', 'totals'],
  data() {
    return {
      alltime: false,
      loading: false,
      result: [],
      current_year: null,
      current_month: null,
      year: null,
      month: null,
      type: 0, // alle Spiele
      page: 1,
      total: 0,
      gameTypes: GAME_TYPES,
      gameno: '',
      searching: false,
      arole: window.arole,
    };
  },
  computed: {
    yearRange() {
      const years = [];
      for (let i = this.current_year; i >= 2012; i--) years.push({ value: i, text: String(i) });
      return years;
    },
    monthRange() {
      return MONTHS.map((text, i) => ({ value: i + 1, text }));
    },
  },
  mounted() {
    this.current_year = this.year = new Date().getFullYear();
    this.current_month = this.month = new Date().getMonth() + 1;
    this.result = this.formatResult(this.results || []);
    this.total = this.totals || 0;
  },
  methods: {
    // Der Typ kommt als Zahl aus der Datenbank und wird fuer die Anzeige durch
    // seinen Namen ersetzt.
    formatResult(result) {
      return (result || []).map((entry) => {
        const t = GAME_TYPES.find((type) => type.value == entry.type);
        return { ...entry, type: t ? t.text : entry.type };
      });
    },
    showGame(row) {
      window.location.href = '/results/game/' + row.wec;
    },
    filter(page = 1) {
      this.page = page;
      this.loading = true;
      axios.post('/results', {
        year: this.year,
        month: this.month,
        page: this.page,
        type: this.type,
        alltime: this.alltime,
      }).then((res) => {
        if (res.data.success === true) {
          this.result = this.formatResult(res.data.result);
          this.total = res.data.total;
        }
        this.loading = false;
      }).catch(() => { this.loading = false; });
    },
    searchGame() {
      if (this.gameno && String(this.gameno).length) {
        this.searching = true;
        this.loading = true;
        axios.post('/results', { gameno: this.gameno }).then((res) => {
          if (res.data.success === true) {
            this.result = this.formatResult(res.data.result);
            this.total = res.data.total;
            this.page = 1;
          }
          this.loading = false;
        }).catch(() => { this.loading = false; });
      } else {
        this.searching = false;
        this.filter();
      }
    },
    reset() {
      this.year = this.current_year;
      this.month = this.current_month;
      this.alltime = false;
      this.searching = false;
      this.gameno = '';
      this.type = 0;
      this.filter();
    },
  },
};
</script>
<style scoped>
.results-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}
.results-head h3 { margin: 0; }
.results-gameno { max-width: 160px; }

.filter-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.filter-cell { flex: 1 1 160px; min-width: 140px; }
.filter-cell--switch { flex: 0 0 auto; min-width: 0; }
</style>
