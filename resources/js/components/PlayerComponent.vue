<template>
    <div>
        <b-row class="mt-3">
            <b-col>
                <h2>{{ player.nickname }}</h2>
                <b-row>
                    <b-col><strong>Total games:</strong></b-col>
                    <b-col>{{ stats.alltime.games }}</b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Month</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col>{{ stats.month.pos }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.month.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.month.score }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row v-if="stats.month.games">
                    <b-col>
                        <div class="mb-3"><strong>Results:</strong></div>
                        <b-row>
                            <b-col lg="3" class="mb-3" v-show="monthBar">
                                <BarChart :chartData="stats.month.places" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="monthPie">
                                <PieChart :chartData="stats.month.places" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.month)"
                                    @row-clicked="switchMonthChart">
                                </b-table>
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5 v-show="statsYear">Current Year<span class="ml-2 mr-2">/</span><b-link @click="showStats(0)">All-Time</b-link></h5>
                        <h5 v-show="statsAlltime">All-Time<span class="ml-2 mr-2">/</span><b-link @click="showStats(1)">Current Year</b-link></h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col v-show="statsYear">{{ stats.year.pos }}</b-col>
                            <b-col v-show="statsAlltime">{{ stats.alltime.pos }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col v-show="statsYear">{{ stats.year.games }}</b-col>
                            <b-col v-show="statsAlltime">{{ stats.alltime.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col v-show="statsYear">{{ stats.year.score }}</b-col>
                            <b-col v-show="statsAlltime">{{ stats.alltime.score }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <div class="mb-3"><strong>Results:</strong></div>
                        <b-row v-if="stats.year.games" v-show="statsYear">
                            <b-col lg="3" class="mb-3" v-show="yearBar">
                                <BarChart :chartData="stats.year.places" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="yearPie">
                                <PieChart :chartData="stats.year.places" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.year)"
                                    @row-clicked="switchYearChart">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.alltime.games" v-show="statsAlltime">
                            <b-col lg="3" class="mb-3" v-show="alltimeBar">
                                <BarChart :chartData="stats.alltime.places" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="alltimePie">
                                <PieChart :chartData="stats.alltime.places" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.alltime)"
                                    @row-clicked="switchAlltimeChart">
                                </b-table>
                            </b-col>
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
                <b-form-select :disabled="alltime" v-model="year" @change="filter()" :options="yearRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select :disabled="alltime" v-model="month" @change="filter()" :options="monthRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select v-model="type" @change="filter()" :options="gameTypes"></b-form-select>
            </b-col>
            <b-col>
                <b-form-checkbox class="mt-2" @change="filter()" v-model="alltime" switch>
                    All-Time
                </b-form-checkbox>
            </b-col>
            <b-col class="text-right">
                <b-button variant="primary" @click="reset">Reset</b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table responsive striped hover
                    v-if="games"
                    id="games_table"
                    :items="games"
                    @row-clicked="showGame"
                >
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
                <b-pagination
                    v-if="games"
                    v-model="page"
                    :total-rows="total"
                    :per-page="10"
                    aria-controls="games_table"
                    @page-click="paginate"
                ></b-pagination>
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
                type: 0,
                page: 1,
                monthBar: true,
                monthPie: true,
                yearBar: true,
                yearPie: true,
                alltimeBar: true,
                alltimePie: true,
                statsYear: true,
                statsAlltime: true,
                gameTypes: [
                    { value: 1, text:'regular' },
                    { value: 5, text:'monthly' },
                    { value: 6, text:'yearly' },
                    { value: 0, text:'all' }
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
            if(this.stats.year.games) this.showStats(1)
            else this.showStats(0)
            this.monthPie = this.yearPie = this.alltimePie = false
            this.current_year = this.year = new Date().getFullYear()
            this.current_month = this.month = new Date().getMonth() + 1
            this.filter()
        },
        methods: {
            showStats(type){
                this.statsYear = (type == 1) ? true : false
                this.statsAlltime = (type == 1) ? false : true
            },
            switchMonthChart(item, index, event) {
                this.monthBar = (index == 0) ? true : false
                this.monthPie = (index == 0) ? false : true
            },
            switchYearChart(item, index, event) {
                this.yearBar = (index == 0) ? true : false
                this.yearPie = (index == 0) ? false : true
            },
            switchAlltimeChart(item, index, event){
                this.alltimeBar = (index == 0) ? true : false
                this.alltimePie = (index == 0) ? false : true
            },
            getPlacesFormatted(stats){
                let places = []
                let percentages = []
                for(let i=1;i<=10;i++){
                    places[i] = stats.places[i-1]
                    // approximate percentages (hiding decimals for 'optimal' display on all devices)
                    percentages[i] = ((places[i] / stats.games) * 100).toFixed(/*1*/) + "%"
                }
                return [places, percentages]
            },
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
                window.open(window.location.origin + '/results/game/' + item.wec, '_blank')
            },
            filter(page=1){
                this.page = page
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
                this.filter(page)
            },
            reset(){
                this.year = this.current_year
                this.month = this.current_month
                this.alltime = false
                this.type = 0
                this.filter()
            },
        }
    }
</script>
<style lang="scss" scoped>
.awards img{
    width: 120px;
}
</style>
