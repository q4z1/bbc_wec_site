<template>
    <div>
        <h3>Ranking</h3>
        <b-row class="mb-3">
            <b-col>
                <b-form-select v-model="year" @change="filter" :options="yearRange"></b-form-select>
            </b-col>
            <b-col></b-col>
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
        },
        mounted() {
            this.current_year = this.year = new Date().getFullYear()
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
                        // 'score_month': s.score_month,
                        // 'score_year': s.score_year,
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
                axios.post('/results/ranking', {
                    year: this.year,
                })
                .then(response => {
                    if(response.data.success === true){
                        this.result = this.formatResult(response.data.stats)
                    }
                }, (error) => {
                    console.log(error)
                });
            },
        }
    }
</script>