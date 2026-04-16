<template>
  <div>
    <h3>Results</h3>
    <div class="row mb-3 g-2">
      <div class="col" v-loading="loading">
        <el-select v-model="season_select" :disabled="alltime || loading" @change="filter()" style="width:100%">
          <el-option v-for="s in seasons" :key="s.value" :label="s.label" :value="s.value" />
        </el-select>
      </div>
      <div class="col" v-loading="loading">
        <el-select v-model="type" :disabled="loading" @change="filter()" style="width:100%">
          <el-option v-for="t in gameTypes" :key="t.value" :label="t.text" :value="t.value" />
        </el-select>
      </div>
      <div class="col d-flex align-items-center">
        <el-checkbox v-model="alltime" :disabled="loading" @change="filter()">All-Time</el-checkbox>
      </div>
      <div class="col text-end">
        <el-button type="primary" :disabled="loading" @click="reset">Reset</el-button>
      </div>
    </div>
    <template v-if="result && result.length">
      <el-pagination v-model:current-page="page" :page-size="10" :total="total"
        layout="prev, pager, next" @current-change="filter" class="mb-2" />
      <el-table :data="result" stripe hover style="width:100%" @row-click="showGame">
        <el-table-column v-for="col in columns" :key="col" :prop="col" :label="col" />
      </el-table>
      <el-pagination v-model:current-page="page" :page-size="10" :total="total"
        layout="prev, pager, next" class="mt-2" @current-change="filter" />
    </template>
    <p v-else class="mt-4">No games found for this period.</p>
  </div>
</template>
<script>
export default {
  props: ['results', 'totals', 'season', 'allseasons'],
  data() {
    return {
      alltime: false,
      result: null,
      season_select: null,
      type: 0,
      page: 1,
      total: 0,
      loading: false,
      gameTypes: [
        { text: 'Step 1', value: 1 },
        { text: 'Step 2', value: 2 },
        { text: 'Step 3', value: 3 },
        { text: 'Step 4', value: 4 },
        { text: 'All', value: 0 },
      ],
    };
  },
  computed: {
    seasons() {
      return (this.allseasons || []).map((s) => ({ value: s, label: 'Season ' + s }));
    },
    columns() {
      if (!this.result || !this.result.length) return [];
      return Object.keys(this.result[0]);
    },
  },
  mounted() {
    this.season_select = this.season;
    this.result = this.formatResult(this.results || []);
    this.total = this.totals;
  },
  methods: {
    formatResult(result) {
      return result.map((entry) => {
        this.gameTypes.forEach((t) => { if (t.value == entry.type) entry.type = t.text; });
        return entry;
      });
    },
    showGame(row) {
      window.location.href = '/results/game/' + row.number;
    },
    filter(page = 1) {
      this.page = typeof page === 'number' ? page : 1;
      this.loading = true;
      axios.post('/results', {
        season: this.season_select, page: this.page,
        type: this.type, alltime: this.alltime,
      }).then((res) => {
        if (res.data.success === true) {
          this.result = this.formatResult(res.data.result);
          this.total = res.data.total;
        }
      }).finally(() => {
        this.loading = false;
      });
    },
    reset() {
      this.type = 0; this.alltime = false; this.season_select = this.season;
      this.filter();
    },
  },
};
</script>
