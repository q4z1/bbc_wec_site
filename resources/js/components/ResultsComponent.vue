<template>
    <div>
        <b-row v-if="arole !== ''" class="mb-3">
            <b-col>
                <h3>Results</h3>
            </b-col>
            <b-col class="col-gameno">
                <b-input-group append="#">
                    <b-form-input v-model="gameno" @input="searchGame" placeholder="Wec"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <h3 v-else>Results</h3>
        <b-row class="mb-3">
            <b-col>
                <b-form-select :disabled="searching||alltime" v-model="year" @change="filter()" :options="yearRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select :disabled="searching||alltime" v-model="month" @change="filter()" :options="monthRange"></b-form-select>
            </b-col>
            <b-col>
                <b-form-select :disabled="searching" v-model="type" @change="filter()" :options="gameTypes"></b-form-select>
            </b-col>
            <b-col>
                <b-form-checkbox :disabled="searching" class="mt-2" @change="filter()" v-model="alltime" switch>
                    All-Time
                </b-form-checkbox>
            </b-col>
            <b-col class="text-right">
                <b-button variant="primary" @click="reset">Reset</b-button>
            </b-col>
        </b-row>
        <b-pagination
            v-if="result"
            v-model="page"
            :total-rows="total"
            :per-page="10"
            aria-controls="results_table"
            @page-click="paginate"
        ></b-pagination>
        <b-table responsive striped hover
            v-if="result"
            id="results_table"
            :items="result"
            @row-clicked="showGame"
        ></b-table>
        <p v-else class="mt-4">No games found for this period.</p>
        <b-pagination
            v-if="result"
            v-model="page"
            :total-rows="total"
            :per-page="10"
            aria-controls="results_table"
            @page-click="paginate"
        ></b-pagination>   
    </div>
</template>
<script>
    export default {
        props: ['results', 'totals'],
        data() {
            return {
                alltime: false,
                renderTable: true,
                result: null,
                current_year: null,
                current_month: null,
                year: null,
                month: null,
                type: 0, // all games
                page: 1, // we always start with page 1
                total: null,
                gameTypes: [
                    { value: 1, text:'regular' },
                    { value: 5, text:'monthly' },
                    { value: 6, text:'yearly' },
                    { value: 0, text:'all' }
                ],
                gameno: null,
                searching: false,
                arole: window.arole,
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
            this.result = this.formatResult(this.results)
            this.total = this.totals
        },
        created(){
            for(let i=0;i<this.results.length;i++){
                this.gameTypes.map(type => {
                        if(type.value ==  this.results[i].type) this.results[i].type = type.text
                    }
                )
            }
        },
        methods:{
            formatResult(result){
                let results = result.map(entry => {
                    let newEntry = entry
                    let element = document.createElement('div');
                    for(let i=1;i<=10;i++){
                        if(entry['p'+i] !== null){
                            newEntry['p'+i] = entry['p'+i]
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
                window.location.href = '/results/game/' + item.wec
            },
            filter(page=1){
                this.page = page
                axios.post('/results', {
                    year: this.year,
                    month: this.month,
                    page: this.page,
                    type: this.type,
                    alltime: this.alltime
                })
                .then(response => {
                    if(response.data.success === true){
                        this.result = this.formatResult(response.data.result)
                        this.total = response.data.total
                    }
                }, (error) => {
                    console.log(error)
                });
            },
            searchGame(){
                if(this.gameno.length){
                    this.searching = true
                    axios.post('/results', {
                        gameno: this.gameno
                    })
                    .then(response => {
                        if(response.data.success === true){
                            this.result = this.formatResult(response.data.result)
                            this.total = response.data.total
                            this.page = 1
                        }
                    }, (error) => {
                        console.log(error)
                    });
                }else{
                    this.searching = false
                    this.filter()
                }
            },
            paginate(bvEvt, page){
                bvEvt.preventDefault()
                this.filter(page)
            },
            reset(){
                this.year = this.current_year
                this.month = this.current_month
                this.alltime = false
                this.searching = false
                this.gameno = null
                this.type = 0
                this.filter()
            }
        }
    }
</script>
<style lang="scss" scoped>
.col-gameno{
    max-width: 135px;
}
</style>
