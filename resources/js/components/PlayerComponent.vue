<template>
  <div>
    <div class="player-head">
      <div class="player-main">
        <h2>{{ player.nickname }}</h2>
        <div class="stat-line"><strong>Total games:</strong><span>{{ stats.alltime.games }}</span></div>

        <!-- Aktueller Monat -->
        <div class="stat-block">
          <h5>Current Month</h5>
          <div class="stat-line"><strong>Place:</strong><span>{{ stats.month.pos }}</span></div>
          <div class="stat-line"><strong>Games:</strong><span>{{ stats.month.games }}</span></div>
          <div class="stat-line"><strong>Score:</strong><span>{{ stats.month.score }}</span></div>
        </div>

        <div v-if="stats.month.games" class="stat-block">
          <div class="results-label"><strong>Results:</strong></div>
          <div class="chart-row">
            <div v-show="monthBar" class="chart-cell"><BarChart :chartData="stats.month.places" /></div>
            <div v-show="monthPie" class="chart-cell"><PieChart :chartData="stats.month.places" /></div>
            <div class="places-cell">
              <el-table :data="getPlacesFormatted(stats.month)" stripe size="small" style="width:100%"
                        @row-click="switchMonthChart">
                <el-table-column prop="label" label="" width="90" />
                <el-table-column v-for="k in PLACE_KEYS" :key="k" :prop="k" :label="k" />
              </el-table>
            </div>
          </div>
        </div>

        <!-- Jahr / All-Time -->
        <div class="stat-block">
          <h5 v-show="statsYear">Current Year<span class="sep">/</span><el-link type="primary" @click="showStats(0)">All-Time</el-link></h5>
          <h5 v-show="statsAlltime">All-Time<span class="sep">/</span><el-link type="primary" @click="showStats(1)">Current Year</el-link></h5>
          <div class="stat-line"><strong>Place:</strong><span>{{ statsYear ? stats.year.pos : stats.alltime.pos }}</span></div>
          <div class="stat-line"><strong>Games:</strong><span>{{ statsYear ? stats.year.games : stats.alltime.games }}</span></div>
          <div class="stat-line"><strong>Score:</strong><span>{{ statsYear ? stats.year.score : stats.alltime.score }}</span></div>
        </div>

        <div class="stat-block">
          <div class="results-label"><strong>Results:</strong></div>
          <div v-if="stats.year.games" v-show="statsYear" class="chart-row">
            <div v-show="yearBar" class="chart-cell"><BarChart :chartData="stats.year.places" /></div>
            <div v-show="yearPie" class="chart-cell"><PieChart :chartData="stats.year.places" /></div>
            <div class="places-cell">
              <el-table :data="getPlacesFormatted(stats.year)" stripe size="small" style="width:100%"
                        @row-click="switchYearChart">
                <el-table-column prop="label" label="" width="90" />
                <el-table-column v-for="k in PLACE_KEYS" :key="k" :prop="k" :label="k" />
              </el-table>
            </div>
          </div>
          <div v-if="stats.alltime.games" v-show="statsAlltime" class="chart-row">
            <div v-show="alltimeBar" class="chart-cell"><BarChart :chartData="stats.alltime.places" /></div>
            <div v-show="alltimePie" class="chart-cell"><PieChart :chartData="stats.alltime.places" /></div>
            <div class="places-cell">
              <el-table :data="getPlacesFormatted(stats.alltime)" stripe size="small" style="width:100%"
                        @row-click="switchAlltimeChart">
                <el-table-column prop="label" label="" width="90" />
                <el-table-column v-for="k in PLACE_KEYS" :key="k" :prop="k" :label="k" />
              </el-table>
            </div>
          </div>
        </div>
      </div>

      <div class="awards">
        <h3>Awards:</h3>
        <div class="awards-grid">
          <div v-for="(award, key) in awards" :key="key" class="award-item">
            <img :src="award.filename" :alt="award.title" />
            <div>{{ award.title }}</div>
          </div>
        </div>
      </div>
    </div>

    <hr />
    <h3>Games:</h3>
    <div class="filter-row">
      <div class="filter-cell">
        <el-select v-model="year" :disabled="alltime" style="width:100%" @change="filter()">
          <el-option v-for="y in yearRange" :key="y.value" :label="y.text" :value="y.value" />
        </el-select>
      </div>
      <div class="filter-cell">
        <el-select v-model="month" :disabled="alltime" style="width:100%" @change="filter()">
          <el-option v-for="m in monthRange" :key="m.value" :label="m.text" :value="m.value" />
        </el-select>
      </div>
      <div class="filter-cell">
        <el-select v-model="type" style="width:100%" @change="filter()">
          <el-option v-for="t in gameTypes" :key="t.value" :label="t.text" :value="t.value" />
        </el-select>
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-switch v-model="alltime" active-text="All-Time" @change="filter()" />
      </div>
      <div class="filter-cell filter-cell--switch">
        <el-button type="primary" @click="reset">Reset</el-button>
      </div>
    </div>

    <template v-if="games && games.length">
      <div v-loading="loading">
        <el-table :data="games" stripe style="width:100%" @row-click="showGame">
          <el-table-column prop="wec" label="Wec" width="90" fixed />
          <el-table-column prop="type" label="Type" width="100" />
          <el-table-column prop="started" label="Started" width="170" />
          <el-table-column v-for="i in 10" :key="i" :prop="'p' + i" :label="'P' + i" min-width="130">
            <template #default="scope">
              <strong v-if="scope.row['p' + i] === player.nickname" class="own-nick">{{ scope.row['p' + i] }}</strong>
              <span v-else>{{ scope.row['p' + i] }}</span>
            </template>
          </el-table-column>
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
import BarChart from './BarChart.vue';
import PieChart from './PieChart.vue';

const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

const PLACE_KEYS = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th'];

const GAME_TYPES = [
  { value: 1, text: 'regular' },
  { value: 5, text: 'monthly' },
  { value: 6, text: 'yearly' },
  { value: 0, text: 'all' },
];

export default {
  components: { BarChart, PieChart },
  props: ['player', 'stats', 'awards'],
  data() {
    return {
      PLACE_KEYS,
      alltime: false,
      loading: false,
      current_year: null,
      current_month: null,
      year: null,
      month: null,
      total: 0,
      games: [],
      type: 0,
      page: 1,
      monthBar: true,
      monthPie: false,
      yearBar: true,
      yearPie: false,
      alltimeBar: true,
      alltimePie: false,
      statsYear: true,
      statsAlltime: false,
      gameTypes: GAME_TYPES,
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
    this.showStats(this.stats.year.games ? 1 : 0);
    this.current_year = this.year = new Date().getFullYear();
    this.current_month = this.month = new Date().getMonth() + 1;
    this.filter();
  },
  methods: {
    showStats(type) {
      this.statsYear = type == 1;
      this.statsAlltime = type != 1;
    },
    // Erste Zeile schaltet auf das Balken-, zweite auf das Tortendiagramm.
    switchMonthChart(row) { this.monthBar = row.label === 'Games'; this.monthPie = !this.monthBar; },
    switchYearChart(row) { this.yearBar = row.label === 'Games'; this.yearPie = !this.yearBar; },
    switchAlltimeChart(row) { this.alltimeBar = row.label === 'Games'; this.alltimePie = !this.alltimeBar; },

    getPlacesFormatted(stats) {
      const counts = { label: 'Games' };
      const percentages = { label: 'Percent' };
      PLACE_KEYS.forEach((key, i) => {
        const value = stats.places[i];
        counts[key] = value;
        // Nachkommastellen werden bewusst weggelassen, damit die Tabelle auch
        // auf schmalen Displays in eine Zeile passt.
        percentages[key] = stats.games ? ((value / stats.games) * 100).toFixed() + '%' : '0%';
      });
      return [counts, percentages];
    },

    formatResult(result) {
      return (result || []).map((entry) => {
        const t = GAME_TYPES.find((type) => type.value == entry.type);
        return { ...entry, type: t ? t.text : entry.type };
      });
    },

    showGame(row) {
      window.open(window.location.origin + '/results/game/' + row.wec, '_blank');
    },

    filter(page = 1) {
      this.page = page;
      this.loading = true;
      axios.post('/results/player/' + this.player.id, {
        year: this.year,
        month: this.month,
        page: this.page,
        type: this.type,
        alltime: this.alltime,
      }).then((res) => {
        if (res.data.success === true) {
          this.games = this.formatResult(res.data.result);
          this.total = res.data.total;
        }
        this.loading = false;
      }).catch(() => { this.loading = false; });
    },

    reset() {
      this.year = this.current_year;
      this.month = this.current_month;
      this.alltime = false;
      this.type = 0;
      this.filter();
    },
  },
};
</script>
<style scoped>
.player-head {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 1rem;
}
.player-main { flex: 1 1 480px; min-width: 0; }

.stat-line {
  display: flex;
  gap: 0.5rem;
}
.stat-line strong { min-width: 140px; }

.stat-block { margin-top: 1rem; }
.results-label { margin-bottom: 0.75rem; }
.sep { margin: 0 0.5rem; }

.chart-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.chart-cell {
  flex: 0 1 220px;
  height: 160px;
  position: relative;
}
.places-cell { flex: 1 1 320px; min-width: 0; }

.own-nick { color: var(--el-color-primary); }

.awards { flex: 0 1 280px; }
.awards-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.award-item { text-align: center; }
.award-item img { width: 120px; }

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
