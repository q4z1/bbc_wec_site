<template>
  <div>
    <h3>Ranking</h3>
    <div class="row mb-3 g-2">
      <div class="col" v-loading="loading">
        <el-select v-model="season_select" :disabled="loading || alltime" @change="filter" style="width:100%">
          <el-option v-for="s in seasons" :key="s.value" :label="s.text" :value="s.value" />
        </el-select>
      </div>
      <div class="col-auto d-flex align-items-center">
        <el-checkbox v-model="alltime" :disabled="loading" @change="filter">All-Time</el-checkbox>
      </div>
    </div>
    <div v-loading="loading">
      <el-table :data="result" stripe style="width:100%" @row-click="showPlayer">
        <el-table-column prop="position" label="#" width="60" />
        <el-table-column prop="nickname" label="Nickname">
          <template #default="scope"><span v-html="scope.row.nickname"></span></template>
        </el-table-column>
        <el-table-column prop="score" label="Score" sortable />
        <el-table-column prop="games" label="Games" sortable>
          <template #default="scope">
            <span :class="(season_select == 9 && !alltime && scope.row.games < 40) ? 'text-danger' : 'text-success'">{{ scope.row.games }}</span>
          </template>
        </el-table-column>
        <el-table-column v-if="step1_visible" prop="step1" label="Step1" sortable>
          <template #default="scope">
            <span :class="shouldHighlightStep1(scope.row) ? 'text-danger' : 'text-success'">{{ scope.row.step1 }}</span>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div v-if="avg_games" class="text-center fst-italic mt-2">a={{ avg_games }}</div>
  </div>
</template>
<script>
export default {
  props: ['results', 'season', 'allseasons'],
  data() {
    return {
      season_select: null,
      result: null,
      loading: false,
      alltime: false,
      step1_visible: false,
      avg_games: 0,
    };
  },
  computed: {
    seasons() {
      return (this.allseasons || []).map((s) => ({ value: s, text: 'Season ' + s }));
    },
  },
  mounted() {
    this.season_select = this.season;
    this.result = this.formatResult(this.results || []);
  },
  methods: {
    showPlayer(row) {
      window.location.href = '/player/' + encodeURIComponent(row.nickname);
    },
    shouldHighlightStep1(row) {
      return (this.season_select == 9 && row.step1 < 20) || (this.season_select == 10 && row.step1 < 45);
    },
    formatResult(stats) {
      this.step1_visible = (this.season_select == 9 || this.season_select == 10) && !this.alltime;
      let games = 0;
      const result = stats.map((s, i) => {
        games += s.games;
        return { position: i + 1, nickname: s.nickname, score: s.score, games: s.games, step1: s.step1 };
      });
      this.avg_games = stats.length > 0 ? (games / stats.length).toFixed() : 0;
      return result;
    },
    filter() {
      this.loading = true;
      axios.post('/results/ranking', { season: !this.alltime ? this.season_select : 0 })
        .then((res) => {
          if (res.data.success === true) {
            this.result = this.formatResult(res.data.stats);
            this.loading = false;
          }
        });
    },
  },
};
</script>
