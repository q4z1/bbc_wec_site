<template>
    <div>
        <b-alert v-model="delete_fail" variant="danger">Something went wrong!</b-alert>
        <b-alert v-model="ticket_fail" variant="danger">Something went wrong!</b-alert>
        <b-alert v-model="ticket_success" variant="success">Tickets successfully edited!</b-alert>
        <b-row class="mt-6">
            <b-col>
                <h1>{{ player.nickname }}</h1>
                <b-row>
                    <b-col md="4"><strong>Total games:</strong></b-col>
                    <b-col>{{ stats.length }}</b-col>
                </b-row>
                <b-row>
                    <b-col md="4"><strong>Current Season rank:</strong></b-col>
                    <b-col>to come</b-col>
                </b-row>
                <b-row>
                    <b-col md="4"><strong>All-time rank:</strong></b-col>
                    <b-col>to come</b-col>
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
            <b-col>
                <h3>Awards:</h3>
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
                <b-form-select v-model="year" @change="filter" :options="yearRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select v-model="month" @change="filter">                   
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">Jun</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </b-form-select>
            </b-col>
            <b-col>
                <b-form-select v-model="type" @change="filter" :options="gameTypes"></b-form-select>
            </b-col>
            <b-col class="text-right">
                <b-button variant="warning" @click="reset">Reset</b-button>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table striped hover 
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
                <p v-else class="mt-4">No games found.</p>
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
        props: ['player', 'stats'],
        data() {
            return {
                ticket_fail: false,
                ticket_success: false,
                delete_fail: false,
                s2: 0,
                s3: 0,
                s4: 0,
                totalPoints: 0,
                currentMonthPoints: 0,
                currentYearPoints: 0,
                types: [{ text: 'Step 1', value: 1 }, { text: 'Step 2', value: 2 }, { text: 'Step 3', value: 3 }, { text: 'Step 4', value: 4 }],
                renderTable: true,
                result: null,
                year: null,
                month: null,
                type: 1, // regular games
                page: 1, // we always start with page 1
                total: null,
                games: null,
                arole: window.arole,
            }
        },
        computed: {
            yearRange: function(){
                let years = []
                let now = this.year
                let past = 2012
                for(let i=now;i>=past;i--){
                    years.push({value: i, text: i})
                }
                return years
            },
            gameTypes: function(){
                return [
                    { value: 1, text:'Step 1' },
                    { value: 2, text:'Step 2' },
                    { value: 3, text:'Step 3' },
                    { value: 4, text:'Step 4' },
                ]
            },
        },
        mounted() {
            this.year = new Date().getFullYear() // initially current year
            this.month = new Date().getMonth() + 1 // initially current month
            this.type = 1
            this.s2 = this.player.s2_tickets
            this.s3 = this.player.s3_tickets
            this.s4 = this.player.s4_tickets
            this.filter()
        },
        methods: {
            formatResult(result){
                let results = result.map(entry => {
                    let newEntry = entry
                    for(let i=1;i<=10;i++){
                        if(entry['p'+i] !== null){
                            if(entry['p'+i] == this.player.nickname){
                                newEntry['p'+i] = '<strong class="text-success">' + entry['p'+i] + '</span>'
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
            filter(){
                axios.post('/results/games/' + this.player.id, {
                    year: this.year,
                    month: this.month,
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
                this.page = page
                this.filter()
            },
            reset(){
                this.year = new Date().getFullYear() // current year
                this.month = new Date().getMonth() + 1 // current month
                this.type = 1
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
<style lang="scss">
    table#games_table{
        tbody{
            tr{
                cursor: pointer;
            }
        } 
    } 
</style>