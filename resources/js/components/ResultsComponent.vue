<template>
    <div>
        <b-row>
            <b-col>
                <b-form-select v-model="year" @change="filter">                   
                    <option value="2020">2020</option>
                    <option value="2019">2019</option>

                </b-form-select>
            </b-col>
            <b-col>
                <b-form-select v-model="month" @change="filter">                   
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="11">November</option>
                </b-form-select>
            </b-col>
        </b-row>

        <b-table striped hover 
            :items="result"
            @row-clicked="showGame"></b-table>
            
    </div>
</template>
<script>
    export default {
        props: ['results'],
        data() {
            return {
                result: null,
                year: null,
                month: null,
                type: 1, // regular games
                page: 1, // we always start with page 1
            }
        },
        mounted() {
            console.log('Results mounted.')

            this.year = new Date().getFullYear() // initially current year
            this.month = new Date().getMonth() + 1 // initially current month

            // ajax call => result.data into this.results
            this.result = this.results
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
                .then((response) => {
                    console.log(response)
                    if(response.data.success)
                        this.result = response.data.result
                }, (error) => {
                    console.log(error)
                });
            },
            paginate(bvEvt, page){
                this.page = page
                this.filter()
            }
        }
    }
</script>