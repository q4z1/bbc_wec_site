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
                    <b-form-select :disabled="loading" v-model="year" @change="filter" :options="yearRange"></b-form-select>
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
                    <b-form-select :disabled="loading" v-model="month" @change="filter" :options="monthRange"></b-form-select>
                </b-overlay>
            </b-col>
            <b-col></b-col>
        </b-row>

        <b-table striped hover 
            id="results_table"
            :items="result"
            @row-clicked="showPlayer"
        >
            <template #cell(nickname)="data">
                <span v-html="data.value"></span>
            </template>
        </b-table>
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
            }
        },
        computed: {
            yearRange: function(){
                let years = []
                years.push({value: '0', text: 'All-Time'})
                let now = this.current_year
                let past = 2012
                for(let i=now;i>=past;i--){
                    years.push({value: i, text: i})
                }
                return years
            },
            monthRange: function(){
                let months = []
                for(let i=1;i<=12;i++){
                    months.push({value: i, text: i})
                }
                return months
            },
        },
        mounted() {
            this.current_year = this.year = new Date().getFullYear()
            this.current_month = new Date().getMonth() + 1
            this.result = this.formatResult(this.stats)
        },
        methods:{
            formatResult(stats){
                // console.log('formatResult')
                let stats_formatted = []
                let l = stats.length
                for(let i=0; i<l; i++){
                    let s = stats[i]
                    stats_formatted.push({
                        'position': i + 1,
                        // 'player_id': s.player.id,
                        'nickname': s.player.nickname,
                        'score': s.score_year,
                        'games': s.year.games
                    })
                }
                return stats_formatted
            },
            showPlayer(item, index, event) {
                window.location.href = '/player/' + item.nickname
            },
            filter(){
                this.loading = true
                axios.post('/results/ranking', {
                    year: this.year,
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