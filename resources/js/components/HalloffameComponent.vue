<template>
    <div>
        <h3>Hall of Fame</h3>
        <b-row class="mb-3">
            <b-col >
                <b-form-select v-model="year" @change="filter">
                    <option value="0" selected="selected">All-time</option>                  
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
        props: ['results', 'totals'],
        data() {
            return {
                renderTable: true,
                result: null,
                year: 0,
                player: null,
            }
        },
        mounted() {
            console.log('Halloffame mounted.')

            this.result = this.results
        },
        methods:{
            showPlayer(item, index, event) {
                window.location.href = '/player/' + encodeURIComponent(item.nickname)
            },
            filter(){
                axios.post('/results/halloffame', {
                    year: this.year,
                    month: this.month,
                    page: this.page,
                    type: this.type
                })
                .then(response => {
                    if(response.data.success === true){
                        this.result = response.data.result
                    }
                }, (error) => {
                    console.log(error)
                });
            },
        }
    }
</script>