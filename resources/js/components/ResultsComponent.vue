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
        </b-row>
        <b-pagination
            v-model="page"
            :total-rows="total"
            :per-page="10"
            aria-controls="results_table"
            @page-click="paginate"
        ></b-pagination>
        <b-table striped hover 
            id="results_table"
            :items="result"
            @row-clicked="showGame"
        ></b-table>
        <b-pagination
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
            }
        },
        computed: {
            yearRange: function(){
                let years = []
                let now = this.year
                let past = 2010
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
        methods:{
            formatResult(result){
                let results = result.map(entry => {
                    console.log(entry)
                    let newEntry = entry
                    let element = document.createElement('div');
                    for(let i=1;i<=10;i++){
                        if(entry['p'+i] !== null){
                            let str = entry['p'+i]
                            if(str && typeof str === 'string') {
                                // strip script/html tags
                                str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                                str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                                element.innerHTML = str;
                                str = element.textContent;
                                element.textContent = '';
                            }
                            newEntry['p'+i] = str
                        } 
                        console.log(newEntry['p'+i])
                    }

                    return newEntry
                })

                // @TODO: replace special html chars
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
            }
        }
    }
</script>