<template>
  <div>
    <h3>Ranking</h3>
    <b-row class="mb-3">
      <b-col>
        <b-overlay
        :show="loading"
        rounded
        opacity="0.6"
        spinner-small
        spinner-variant="primary"
        class="d-inline-block"
        >
          <b-form-select :disabled="loading||alltime" v-model="season_select" @change="filter" :options="seasons"></b-form-select>
        </b-overlay>
      </b-col>
      <b-col></b-col>
      <b-col class="text-right">
        <b-overlay
        :show="loading"
        rounded
        opacity="0.6"
        spinner-small
        spinner-variant="primary"
        class="d-inline-block"
        >
          <b-form-checkbox :disabled="loading" class="mt-2" @change="filter" v-model="alltime" switch>
            All-Time
          </b-form-checkbox>
        </b-overlay>
      </b-col>
    </b-row>
    <b-table responsive striped hover
      id="results_table"
      :items="result"
      :fields="fields"
      @row-clicked="showPlayer"
    >
      <template #cell(nickname)="data">
        <span v-html="data.value"></span>
      </template>
      <template #cell(games)="data" v-if="step1_visible">
        <span v-if="data.value < 40" class="text-danger" v-html="data.value"></span>
        <span v-else class="text-success" v-html="data.value"></span>
      </template>
      <template #cell(step1)="data" v-if="step1_visible">
        <span v-if="data.value < 20" class="text-danger" v-html="data.value"></span>
        <span v-else class="text-success" v-html="data.value"></span>
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
      loading: false,
      alltime: false,
      step1_visible: false,
      fields: [{
          key: 'position'
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
    seasons: function () {
      let l = this.allseasons.length
      let s = []
      for (let i = 0; i < l; i++) {
        s.push(
          { value: this.allseasons[i], text: 'Season ' + this.allseasons[i] }
        )
      }
      return s
    }
  },
  mounted() {
    this.season_select = this.season
    this.result = this.formatResult(this.results)
  },
  methods: {
    showPlayer(item, index, event) {
      window.location.href = '/player/' + encodeURIComponent(item.nickname)
    },
    formatResult(stats) {
      let stats_formatted = []
      let l = stats.length
      if((this.season_select > 8 && !this.alltime) && !this.step1_visible){
        this.fields.push({key: 'step1', sortable: true})
        this.step1_visible = true
      }else if((this.season_select <= 8 || this.alltime) && this.step1_visible){
        this.fields.pop()
        this.step1_visible = false
      }
      for (let i = 0; i < l; i++) {
        let s = stats[i]
        stats_formatted.push({
          'position': i + 1,
          'nickname': s.nickname,
          'score': s.score,
          'games': s.games,
          'step1': s.step1
        })
      }
      return stats_formatted
    },
    filter() {
      this.loading = true
      axios.post('/results/ranking', {
        season: (!this.alltime) ? this.season_select : 0
      })
        .then(response => {
          if (response.data.success === true) {
            this.result = this.formatResult(response.data.stats)
            this.loading = false
          }
        }, (error) => {
          console.log(error)
          this.loading = true
        });
    },
  }
}
</script>
