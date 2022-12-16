<template>
  <div>
    <h3>Ranking</h3>
    <b-row class="mb-3">
      <b-col>
        <b-form-select v-model="season_select" @change="filter" :options="seasons"></b-form-select>
      </b-col>
      <b-col></b-col>
      <b-col></b-col>
    </b-row>
    <b-table striped hover id="results_table" :items="result" :fields="fields" @row-clicked="showPlayer" v-if="show">
      <template #cell(nickname)="data">
        <span v-html="data.value"></span>
      </template>
    </b-table>
  </div>
</template>
<script>
export default {
  props: ['results', 'season', 'allseasons'],
  data() {
    return {
      season_select: null,
      renderTable: true,
      result: null,
      player: null,
      show: true,
      fields: [{
          key: 'position',
          sortable: true
        },
        {
          key: 'nickname',
          sortable: true
        },
        {
          key: 'score',
          sortable: true
        },
        {
          key: 'games',
          sortable: true
        }
      ],
    }
  },
  computed: {
    gameTypes: function () {
      return [
        { value: 1, text: 'Step 1' },
        { value: 2, text: 'Step 2' },
        { value: 3, text: 'Step 3' },
        { value: 4, text: 'Step 4' },
      ]
    },
    seasons: function () {
      let l = this.allseasons.length
      let s = []
      // s.push({ value: 0, text: 'All-Time'})
      for (let i = 0; i < l; i++) {
        s.push(
          { value: this.allseasons[i].id, text: 'Season ' + this.allseasons[i].id }
        )
      }
      return s
    }
  },
  mounted() {
    // console.log('Ranking mounted.')
    this.season_select = this.season
    this.result = this.formatResult(this.results)
  },
  methods: {
    showPlayer(item, index, event) {
      window.location.href = '/player/' + encodeURIComponent(item.nickname)
    },
    formatResult(stats) {
      // console.log('formatResult')
      let stats_formatted = []
      let l = stats.length
      for (let i = 0; i < l; i++) {
        let s = stats[i]
        stats_formatted.push({
          'position': i + 1,
          'nickname': s.player.nickname,
          'score': s.score_season,
          'games': s.games_season
        })
      }
      return stats_formatted
    },
    filter() {
      axios.post('/results/ranking', {
        season: this.season_select,
        page: this.page,
        type: this.type
      })
        .then(response => {
          if (response.data.success === true) {
            this.result = this.formatResult(response.data.stats)
            this.show = false
            this.$nextTick(() => {
              this.show = true
            })
          }
        }, (error) => {
          console.log(error)
        });
    },
  }
}
</script>