<template>
    <div>
        <h3>Results</h3>
        <b-row class="mb-3">
            <b-col>
                <b-form-select v-model="year" @change="filter">                   
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>
                    <option value="2018">2018</option>
                    <option value="2017">2017</option>
                    <option value="2016">2016</option>
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                    <option value="2013">2013</option>
                    <option value="2012">2012</option>
                    <option value="2011">2011</option>
                    <option value="2010">2010</option>
                </b-form-select>
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
                <b-form-select v-model="type" @change="filter">                   
                    <option value="1">Type 1</option>
                    <option value="2">Type 2</option>
                    <option value="3">Type 3</option>
                    <option value="4">Type 4</option>
                    <option value="5">Type 5</option>
                </b-form-select>
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
        mounted() {
            console.log('Results mounted.')

            this.year = new Date().getFullYear() // initially current year
            this.month = new Date().getMonth() + 1 // initially current month

            // ajax call => result.data into this.results
            this.result = this.results
            this.total = this.totals
        },
        methods:{
            showGame(item, index, event) {
                window.location.href = '/results/game/' + item.id
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
                        this.result = response.data.result
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