<template>
    <div>
        <b-row class="mt-3">
            <b-col>
                <h2>{{ player.nickname }}</h2>
                <b-row>
                    <b-col><strong>Total games:</strong></b-col>
                    <b-col>{{ stats.games_alltime }}</b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Month</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col></b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.month.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.score_month }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Year</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col></b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.year.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.score_year }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="awards">
                <h3>Awards:</h3>
                <b-row>
                    <b-col v-for="(award, key) in awards" :key="key">
                        <b-row>
                            <b-col class="text-center"><img :src="award.filename" /></b-col>
                        </b-row>
                        <b-row class="text-center">
                            <b-col>{{ award.title }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
        <hr />
        <b-row class="mb-3">
            <b-col>
                <h3>Games:</h3>
            </b-col>
        </b-row>
        <b-row class="mb-3">
            <b-col>
                <b-form-select :disabled="alltime" v-model="year" @change="filter" :options="yearRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select :disabled="alltime" v-model="month" @change="filter" :options="monthRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select v-model="type" @change="filter" :options="gameTypes"></b-form-select>
            </b-col>
            <b-col>
                <b-form-checkbox class="mt-2" @change="filter" v-model="alltime" switch>
                    All-Time
                </b-form-checkbox>
            </b-col>
            <b-col class="text-right">
                <b-button variant="primary" @click="reset">Reset</b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table striped hover v-if="games" id="games_table" :items="games" @row-clicked="showGame">
                    <template #cell(p1)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p2)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p3)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p4)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p5)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p6)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p7)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p8)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p9)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(p10)="data">
                        <span v-html="data.value"></span>
                    </template>
                </b-table>
                <p v-else class="mt-4">No games found for this period.</p>
                <b-pagination v-if="games" v-model="page" :total-rows="total" :per-page="10" aria-controls="games_table" @page-click="paginate"></b-pagination>
            </b-col>
        </b-row>
    </div>
</template>
<script>
    export default {
        props: ['player', 'stats', 'awards'],
        data() {
            return {
                alltime: false,
                renderTable: true,
                result: null,
                current_year: null,
                current_month: null,
                year: null,
                month: null,
                total: null,
                games: null,
                type: 1,
                page: 1,
                gameTypes: [{value: 1, text:'regular'}, {value: 5, text:'monthly'}, {value: 6, text:'yearly'}],
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
            this.current_month = this.month = new Date().getMonth() + 1
            this.filter()
        },
        methods: {
            formatResult(result){
                let results = result.map(entry => {
                    let newEntry = entry
                    for(let i=1;i<=10;i++){
                        if(entry['p'+i] !== null){
                            if(entry['p'+i] == this.player.nickname){
                                newEntry['p'+i] = '<strong class="text-primary">' + entry['p'+i] + '</span>'
                            }else{
                                newEntry['p'+i] = entry['p'+i]
                            }
                        }
                    }
                    this.gameTypes.map(type => {
                            if(type.value ==  entry.type) entry.type = type.text
                        }
                    )
                    return newEntry
                })
                return results
            },
            showGame(item, index, event) {
                window.open(window.location.origin + '/results/game/' + item.number, '_blank')
            },
            filter(newFilter=true){
                if(newFilter) this.page = 1
                axios.post('/results/player/' + this.player.id, {
                    year: this.year,
                    month: this.month,
                    page: this.page,
                    type: this.type,
                    alltime: this.alltime
                })
                .then(response => {
                    if(response.data.success === true){
                        this.games = this.formatResult(response.data.result)
                        this.total = response.data.total
                    }
                }, (error) => {
                    console.log(error)
                });
            },
            paginate(bvEvt, page){
                bvEvt.preventDefault()
                this.page = page
                this.filter(false)
            },
            reset(){
                this.year = this.current_year
                this.month = this.current_month
                this.alltime = false
                this.type = 1
                this.filter()
            },
        }
    }
</script>
<style lang="scss">
    table#games_table{
        tbody{
            tr{
                cursor: pointer;
            }
        }
    }
</style>
<style lang="scss" scoped>
.awards img{
    width: 120px;
}
</style>