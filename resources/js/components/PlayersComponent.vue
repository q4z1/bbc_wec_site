<template>
  <div>
    <h3>Players</h3>
    <div class="players-search">
      <el-input v-model="player" placeholder="Nickname" clearable @input="searchPlayer">
        <template #prepend>@</template>
      </el-input>
    </div>

    <template v-if="result && result.length">
      <div v-loading="loading">
        <el-table :data="result" stripe style="width:100%" @row-click="showPlayer">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column prop="nickname" label="Nickname" />
        </el-table>
      </div>
      <el-pagination
        v-model:current-page="page"
        :page-size="10"
        :total="tot"
        layout="prev, pager, next"
        background
        @current-change="paginate" />
    </template>
    <p v-else style="margin-top:1.5rem;">No players found.</p>
  </div>
</template>
<script>
export default {
  props: ['players', 'total'],
  data() {
    return {
      result: [],
      page: 1,
      tot: 0,
      player: '',
      loading: false,
    };
  },
  mounted() {
    this.result = this.formatResult(this.players || []);
    this.tot = this.total || 0;
  },
  methods: {
    formatResult(result) {
      return (result || []).map((entry) => ({
        id: entry.id,
        avatar: entry.avatar,
        nickname: entry.nickname,
      }));
    },
    load(page, nickname) {
      this.loading = true;
      const payload = { page };
      if (nickname) payload.nickname = nickname;
      axios.post('/players', payload).then((res) => {
        if (res.data.success === true) {
          this.result = this.formatResult(res.data.players);
          this.tot = res.data.total;
          this.page = page;
        }
        this.loading = false;
      }).catch(() => { this.loading = false; });
    },
    searchPlayer() {
      if (this.player && this.player.length > 1) {
        this.load(1, this.player);
      } else {
        // Zurueck auf die vom Server mitgelieferte erste Seite
        this.result = this.formatResult(this.players || []);
        this.tot = this.total || 0;
        this.page = 1;
      }
    },
    paginate(page) {
      this.load(page, (this.player && this.player.length > 1) ? this.player : null);
    },
    showPlayer(row) {
      window.open(window.location.origin + '/player/' + encodeURIComponent(row.nickname), '_blank');
    },
  },
};
</script>
<style scoped>
.players-search {
  max-width: 240px;
  margin-bottom: 1rem;
}
</style>
