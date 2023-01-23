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
                    <b-form-select :disabled="loading||alltime" v-model="year" @change="filter" :options="yearRange"></b-form-select>
                </b-overlay>
            </b-col>
            <b-col>
                <b-overlay
                :show="loading"
                rounded
                opacity="0.6"
                spinner-small
                spinner-variant="primary"
                class="d-inline-block"
                >
                    <b-form-select :disabled="loading||alltime||allyear" v-model="month" @change="filter" :options="monthRange"></b-form-select>
                </b-overlay>
            </b-col>
            <b-col>
                <b-overlay
                :show="loading"
                rounded
                opacity="0.6"
                spinner-small
                spinner-variant="primary"
                class="d-inline-block"
                >
                    <b-form-checkbox :disabled="loading||alltime" class="mt-2" @change="filter" v-model="allyear" switch>
                        All-Year
                    </b-form-checkbox>
                </b-overlay>
            </b-col>
            <b-col>
                <b-overlay
                :show="loading"
                rounded
                opacity="0.6"
                spinner-small
                spinner-variant="primary"
                class="d-inline-block"
                >
                    <b-form-checkbox :disabled="loading||allyear" class="mt-2" @change="filter" v-model="alltime" switch>
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
        </b-table>
        <b-row class="mb-3" v-if="avg_games">
          <b-col class="text-center font-italic">a={{ avg_games }}</b-col>
        </b-row>
    </div>
</template>
<script>
    export default {
        props: ['stats'],
        data() {
            return {
                renderTable: true,
                result: null,
                current_year: 0,
                year: 0,
                month: 0,
                loading: false,
                allyear: false,
                alltime: false,
                avg_games: 0,
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
            yearRange: function(){
                let years = []
                let now = this.current_year
                let past = 2012
                for(let i=now;i>=past;i--){
                    years.push({value: i, text: i})
                }
                return years
            },
            monthRange: function(){
                let months = []
                let monthText = []
                monthText[1] = "January"
                monthText[2] = "February"
                monthText[3] = "March"
                monthText[4] = "April"
                monthText[5] = "May"
                monthText[6] = "June"
                monthText[7] = "July"
                monthText[8] = "August"
                monthText[9] = "September"
                monthText[10] = "October"
                monthText[11] = "November"
                monthText[12] = "December"
                for(let i=1;i<=12;i++){
                    months.push({value: i, text: monthText[i]})
                }
                return months
            },
        },
        mounted() {
            this.current_year = this.year = new Date().getFullYear()
            this.month = new Date().getMonth() + 1
            this.result = this.formatResult(this.stats)
        },
        methods:{
            formatResult(stats){
                let stats_formatted = []
                let l = stats.length
                let i = 0
                for(i; i<l; i++){
                    let s = stats[i]
                    stats_formatted.push({
                        'position': i + 1,
                        'nickname': s.player.nickname,
                        'score': s.score,
                        'games': s.games
                    })
                }
                this.avg_games = (i > 0) ? stats[0].avg_games : 0
                return stats_formatted
            },
            showPlayer(item, index, event) {
                window.location.href = '/player/' + encodeURIComponent(item.nickname)
            },
            filter(){
                this.loading = true
                axios.post('/results/ranking', {
                    year: !this.alltime ? this.year : 0,
                    month: !this.alltime && !this.allyear ? this.month : 0
                })
                .then(response => {
                    if(response.data.success === true){
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
