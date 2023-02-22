<template>
    <div>
        <b-alert v-model="delete_fail" variant="danger">Something went wrong!</b-alert>
        <b-alert v-model="ticket_fail" variant="danger">Something went wrong!</b-alert>
        <b-alert v-model="ticket_success" variant="success">Tickets successfully edited!</b-alert>
        <b-row class="mt-6">
            <b-col>
                <h1>{{ player.nickname }}</h1>
                <b-row>
                    <b-col>
                        <b-row>
                            <b-col><strong>Total games:</strong></b-col>
                            <b-col>{{ stats.alltime.games }}</b-col>
                        </b-row>
                    </b-col>
                    <b-col class="d-none d-sm-block"></b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Season</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col>{{ stats.season.pos }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.season.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Points:</strong></b-col>
                            <b-col>{{ stats.season.points }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.season.score }}</b-col>
                        </b-row>
                    </b-col>
                    <b-col class="d-none d-sm-block"></b-col>
                </b-row>
                <b-row v-if="stats.season.games">
                    <b-col>
                        <div class="mb-3">
                            <strong>Results:</strong>
                            <b-link class="ml-1" v-if="stats.season.places[0]" @click="showSeasonStep(1)">Step 1</b-link>
                            <b-link class="ml-1" v-if="stats.season.places[1]" @click="showSeasonStep(2)">Step 2</b-link>
                            <b-link class="ml-1" v-if="stats.season.places[2]" @click="showSeasonStep(3)">Step 3</b-link>
                            <div class="mt-2 text-center" v-show="seasonS1"><strong>Step 1</strong></div>
                            <div class="mt-2 text-center" v-show="seasonS2"><strong>Step 2</strong></div>
                            <div class="mt-2 text-center" v-show="seasonS3"><strong>Step 3</strong></div>
                        </div>
                        <b-row v-if="stats.season.places[0]" v-show="seasonS1">
                            <b-col lg="3" class="mb-3" v-show="seasonBarS1">
                                <BarChart :chartData="stats.season.places[0]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="seasonPieS1">
                                <PieChart :chartData="stats.season.places[0]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.season.places[0])"
                                    @row-clicked="switchSeasonChartS1">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.season.places[1]" v-show="seasonS2">
                            <b-col lg="3" class="mb-3" v-show="seasonBarS2">
                                <BarChart :chartData="stats.season.places[1]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="seasonPieS2">
                                <PieChart :chartData="stats.season.places[1]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.season.places[1])"
                                    @row-clicked="switchSeasonChartS2">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.season.places[2]" v-show="seasonS3">
                            <b-col lg="3" class="mb-3" v-show="seasonBarS3">
                                <BarChart :chartData="stats.season.places[2]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="seasonPieS3">
                                <PieChart :chartData="stats.season.places[2]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.season.places[2])"
                                    @row-clicked="switchSeasonChartS3">
                                </b-table>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col class="d-none d-sm-block"></b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>All-Time</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col>{{ stats.alltime.pos }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.alltime.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Points:</strong></b-col>
                            <b-col>{{ stats.alltime.points }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.alltime.score }}</b-col>
                        </b-row>
                    </b-col>
                    <b-col class="d-none d-sm-block"></b-col>
                </b-row>
                <b-row v-if="stats.alltime.games">
                    <b-col>
                        <div class="mb-3">
                            <strong>Results:</strong>
                            <b-link class="ml-1" v-if="stats.alltime.places[0]" @click="showAlltimeStep(1)">Step 1</b-link>
                            <b-link class="ml-1" v-if="stats.alltime.places[1]" @click="showAlltimeStep(2)">Step 2</b-link>
                            <b-link class="ml-1" v-if="stats.alltime.places[2]" @click="showAlltimeStep(3)">Step 3</b-link>
                            <b-link class="ml-1" v-if="stats.alltime.places[3]" @click="showAlltimeStep(4)">Step 4</b-link>
                            <div class="mt-2 text-center" v-show="alltimeS1"><strong>Step 1</strong></div>
                            <div class="mt-2 text-center" v-show="alltimeS2"><strong>Step 2</strong></div>
                            <div class="mt-2 text-center" v-show="alltimeS3"><strong>Step 3</strong></div>
                            <div class="mt-2 text-center" v-show="alltimeS4"><strong>Step 4</strong></div>
                        </div>
                        <b-row v-if="stats.alltime.places[0]" v-show="alltimeS1">
                            <b-col lg="3" class="mb-3" v-show="alltimeBarS1">
                                <BarChart :chartData="stats.alltime.places[0]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="alltimePieS1">
                                <PieChart :chartData="stats.alltime.places[0]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.alltime.places[0])"
                                    @row-clicked="switchAlltimeChartS1">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.alltime.places[1]" v-show="alltimeS2">
                            <b-col lg="3" class="mb-3" v-show="alltimeBarS2">
                                <BarChart :chartData="stats.alltime.places[1]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="alltimePieS2">
                                <PieChart :chartData="stats.alltime.places[1]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.alltime.places[1])"
                                    @row-clicked="switchAlltimeChartS2">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.alltime.places[2]" v-show="alltimeS3">
                            <b-col lg="3" class="mb-3" v-show="alltimeBarS3">
                                <BarChart :chartData="stats.alltime.places[2]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="alltimePieS3">
                                <PieChart :chartData="stats.alltime.places[2]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.alltime.places[2])"
                                    @row-clicked="switchAlltimeChartS3">
                                </b-table>
                            </b-col>
                        </b-row>
                        <b-row v-if="stats.alltime.places[3]" v-show="alltimeS4">
                            <b-col lg="3" class="mb-3" v-show="alltimeBarS4">
                                <BarChart :chartData="stats.alltime.places[3]" :height="100"/>
                            </b-col>
                            <b-col lg="3" class="mb-3" v-show="alltimePieS4">
                                <PieChart :chartData="stats.alltime.places[3]" :height="100"/>
                            </b-col>
                            <b-col lg="9" class="mb-3">
                                <b-table fixed responsive striped hover borderless small
                                    :items="getPlacesFormatted(stats.alltime.places[3])"
                                    @row-clicked="switchAlltimeChartS4">
                                </b-table>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col class="d-none d-sm-block"></b-col>
                </b-row>
                <b-row class="pb-2">
                    <b-col md="4">
                        <strong>Tickets:</strong><br />
                    </b-col>
                    <b-col>
                        <b-row>
                            <b-col>
                                <strong class="text-success">Step 2:</strong>&nbsp;&nbsp;{{ s2 }}
                            </b-col>
                            <b-col>
                                <strong class="text-primary">Step 3:</strong>&nbsp;&nbsp;{{ s3 }}
                            </b-col>
                            <b-col>
                                <strong class="text-warning">Step 4:</strong>&nbsp;&nbsp;{{ s4 }}
                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row class="mt-5 ml-0 mb-3">
                    <b-col md="4">
                        <b-row class="w-75">
                            <b-button variant="warning" v-b-modal.tickets class="w-100" v-if="arole === 's'">Edit Tickets</b-button>
                        </b-row>
                        <b-row class="mt-2 w-75">
                            <b-button variant="danger" v-b-modal.delete class="w-100" v-if="arole === 's'">Delete Player</b-button>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
        <hr />
        <b-row>
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
        <b-row>
            <b-col>
                <h3>Games:</h3>
            </b-col>
        </b-row>
        <b-row class="mb-3">
            <b-col>
                <b-form-select :disabled="alltime" v-model="season_select" @change="filter()" :options="seasons"></b-form-select>
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
        <b-modal ref="delete" id="delete" :title="'Delete Player ' + player.nickname" ok-disabled hide-footer>
            Are you sure to delete player <strong class="text-warning">{{ player.nickname }}</strong>?<br />
            <strong class="text-danger">This cannot be undone!</strong>
            <b-button class="mt-3" variant="outline-info" block @click="$refs['delete'].hide()">Cancel</b-button>
            <b-button class="mt-2" variant="outline-danger" block @click="deletePlayer">Yes, Delete!</b-button>
        </b-modal>
        <b-modal ref="tickets" id="tickets" :title="'Edit Tickets of ' + player.nickname" ok-disabled hide-footer>
                <template #modal-header>
                    <h5>Edit Tickets of {{ player.nickname }}</h5>
                </template>
            <b-row>
                <b-col md="6">
                    <strong class="text-success">Step 2:</strong>
                </b-col>
                <b-col md="3">
                    <input class="form-control input-sm" v-model="s2" type="number">
                </b-col>
            </b-row>
            <b-row>
                <b-col md="6">
                    <strong class="text-primary">Step 3:</strong>
                </b-col>
                <b-col md="3">
                    <input class="form-control input-sm" v-model="s3" type="number">
                </b-col>
            </b-row>
            <b-row>
                <b-col md="6">
                    <strong class="text-warning">Step 4:</strong>
                </b-col>
                <b-col md="3">
                    <input class="form-control input-sm" v-model="s4" type="number">
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-button class="mt-3" variant="outline-info" block @click="revertTickets">Cancel</b-button>
                    <b-button class="mt-2" variant="outline-danger" block @click="editTickets">Ok</b-button>
                </b-col>
            </b-row>
        </b-modal>
    </div>
</template>
<script>
    export default {
        props: ['player', 'season', 'stats', 'awards'],
        data() {
            return {
                ticket_fail: false,
                ticket_success: false,
                delete_fail: false,
                s2: 0,
                s3: 0,
                s4: 0,
                gameTypes: [
                  { text: 'Step 1', value: 1 },
                  { text: 'Step 2', value: 2 },
                  { text: 'Step 3', value: 3 },
                  { text: 'Step 4', value: 4 },
                  { text: 'All', value: 0 }
                ],
                alltime: false,
                renderTable: true,
                result: null,
                season_select: null,
                type: 0, // all games
                page: 1, // we always start with page 1
                total: null,
                games: null,
                seasonS1: true,
                seasonBarS1: true,
                seasonPieS1: true,
                seasonS2: true,
                seasonBarS2: true,
                seasonPieS2: true,
                seasonS3: true,
                seasonBarS3: true,
                seasonPieS3: true,
                alltimeS1: true,
                alltimeBarS1: true,
                alltimePieS1: true,
                alltimeS2: true,
                alltimeBarS2: true,
                alltimePieS2: true,
                alltimeS3: true,
                alltimeBarS3: true,
                alltimePieS3: true,
                alltimeS4: true,
                alltimeBarS4: true,
                alltimePieS4: true,
                arole: window.arole,
            }
        },
        computed: {
            seasons: function(){
                let l = this.stats.seasons.length
                let s = []
                for(let i=0;i<l;i++){
                    s.push(
                        { value: this.stats.seasons[i], text:'Season ' + this.stats.seasons[i] }
                    )
                }
                return s
            }
        },
        mounted() {
            if(this.stats.season.places[0]) this.showSeasonStep(1)
            else if(this.stats.season.places[1]) this.showSeasonStep(2)
            else if(this.stats.season.places[2]) this.showSeasonStep(3)
            this.seasonPieS1 = this.seasonPieS2 = this.seasonPieS3 = false
            if(this.stats.alltime.places[0]) this.showAlltimeStep(1)
            else if(this.stats.alltime.places[1]) this.showAlltimeStep(2)
            else if(this.stats.alltime.places[2]) this.showAlltimeStep(3)
            else if(this.stats.alltime.places[3]) this.showAlltimeStep(4)
            this.alltimePieS1 = this.alltimePieS2 = this.alltimePieS3 = this.alltimePieS4 = false
            this.season_select = this.season
            this.s2 = this.player.s2_tickets
            this.s3 = this.player.s3_tickets
            this.s4 = this.player.s4_tickets
            this.filter()
        },
        methods: {
            showSeasonStep(num){
                this.seasonS1 = (num == 1) ? true : false
                this.seasonS2 = (num == 2) ? true : false
                this.seasonS3 = (num == 3) ? true : false
            },
            showAlltimeStep(num){
                this.alltimeS1 = (num == 1) ? true : false
                this.alltimeS2 = (num == 2) ? true : false
                this.alltimeS3 = (num == 3) ? true : false
                this.alltimeS4 = (num == 4) ? true : false
            },
            switchSeasonChartS1(item, index, event){
                this.seasonBarS1 = (index == 0) ? true : false
                this.seasonPieS1 = (index == 0) ? false : true
            },
            switchSeasonChartS2(item, index, event){
                this.seasonBarS2 = (index == 0) ? true : false
                this.seasonPieS2 = (index == 0) ? false : true
            },
            switchSeasonChartS3(item, index, event){
                this.seasonBarS3 = (index == 0) ? true : false
                this.seasonPieS3 = (index == 0) ? false : true
            },
            switchAlltimeChartS1(item, index, event){
                this.alltimeBarS1 = (index == 0) ? true : false
                this.alltimePieS1 = (index == 0) ? false : true
            },
            switchAlltimeChartS2(item, index, event){
                this.alltimeBarS2 = (index == 0) ? true : false
                this.alltimePieS2 = (index == 0) ? false : true
            },
            switchAlltimeChartS3(item, index, event){
                this.alltimeBarS3 = (index == 0) ? true : false
                this.alltimePieS3 = (index == 0) ? false : true
            },
            switchAlltimeChartS4(item, index, event){
                this.alltimeBarS4 = (index == 0) ? true : false
                this.alltimePieS4 = (index == 0) ? false : true
            },
            getPlacesFormatted(stats){
                let places = []
                let percentages = []
                let total = stats.reduce((sum, num) => sum + num, 0)
                for(let i=1;i<=10;i++){
                    places[i] = stats[i-1]
                    // approximate percentages (hiding decimals for 'optimal' display on all devices)
                    percentages[i] = ((places[i] / total) * 100).toFixed(/*1*/) + "%"
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
                window.open(window.location.origin + '/results/game/' + item.number, '_blank');
            },
            filter(page=1){
                this.page = page
                axios.post('/results/player/' + this.player.id, {
                    season: this.season_select,
                    alltime: this.alltime,
                    page: this.page,
                    type: this.type
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
                this.season_select = this.season
                this.alltime = false
                this.type = 0
                this.filter()
            },
            editTickets(){
                console.log("editTickets.")
                this.formatTicketNum()
                axios.post('/player/tickets/' + this.player.id, {
                    s2: this.s2,
                    s3: this.s3,
                    s4: this.s4
                })
                .then(response => {
                    if(response.data.success === true){
                        this.ticket_success = 4
                    }else{                       
                        this.s2 = this.player.s2_tickets
                        this.s3 = this.player.s3_tickets
                        this.s4 = this.player.s4_tickets
                        this.ticket_fail = 4
                    }
                    this.$refs['tickets'].hide()
                }, (error) => {
                    console.log(error)
                });
            },
            revertTickets(){
                this.s2 = this.player.s2_tickets
                this.s3 = this.player.s3_tickets
                this.s4 = this.player.s4_tickets
                this.$refs['tickets'].hide()
            },
            formatTicketNum(){
                console.log("formatting Num")
                if(this.s2 < 0) this.s2 = 0
                if(this.s2 > 10) this.s2 = 10
                if(this.s3 < 0) this.s3 = 0
                if(this.s3 > 10) this.s3 = 10 
                if(this.s4 < 0) this.s4 = 0
                if(this.s4 > 10) this.s4 = 10                 
            },
            deletePlayer(){
                console.log("deletePlayer.")
                axios.get('/players/delete/' + this.player.id)
                .then(response => {
                    if(response.data.success === true){
                        window.location.href = window.location.origin + "/players";
                    }else{
                        this.delete_fail = true
                        this.$refs['delete'].hide()
                    }
                }, (error) => {
                    console.log(error)
                });                
            }
        }
    }
</script>
<style lang="scss" scoped>
.awards img{
    width: 120px;
}
.table-responsive{
    cursor: pointer;
}
</style>
