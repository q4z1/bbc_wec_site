<template>
    <div>
        <h3>Results</h3>
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
        <b-pagination
            v-if="result"
            v-model="page"
            :total-rows="total"
            :per-page="10"
            aria-controls="results_table"
            @page-click="paginate"
        ></b-pagination>
        <b-table striped hover 
            v-if="result"
            id="results_table"
            :items="result"
            @row-clicked="showGame"
        ></b-table>
        <p v-else class="mt-4">No games found.</p>
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
                renderTable: true,
                result: null,
                year: null,
                month: null,
                type: 1, // regular games
                page: 1, // we always start with page 1
                total: null,
                types: [{ text: 'Regular', value: 1 }, { text: 'Monthly', value: 5 }, { text: 'Yearly', value: 6 }],
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
                    { value: 1, text:'regular' },
                    { value: 5, text:'monthly' },
                    { value: 6, text:'yearly' },
                ]
            },
        },
        mounted() {
            this.year = new Date().getFullYear() // initially current year
            this.month = new Date().getMonth() + 1 // initially current month

            // ajax call => result.data into this.results

            this.result = this.formatResult(this.results)
            this.total = this.totals
        },
        created(){
            for(let i=0;i<this.results.length;i++){
                this.results[i].type = this.gameTypes[i].text
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
                    // this.types.map(typ => { 
                    //     if(parseInt(typ.value) === parseInt(entry.type)) newEntry.type = typ.text 
                    //     // else if(typ.text.toLowerCase()  == entry.type.toLowerCase()) newEntry.type = typ.text 
                    // })
                    console.log(newEntry)
                    return newEntry
                })
                return results
            },
            showGame(item, index, event) {
                window.location.href = '/results/game/' + item.number
            },
            filter(){
                axios.post('/results', {
                    year: this.year,
                    month: this.month,
                    page: this.page,
                    type: this.type
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
            }
        }
    }
</script>