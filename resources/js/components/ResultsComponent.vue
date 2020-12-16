<template>
    <div>
        <b-dropdown id="dropdown-1" text="Year" class="m-md-2">
            <b-dropdown-item>2020</b-dropdown-item>
            <b-dropdown-item>2019</b-dropdown-item>
            <b-dropdown-item>2018</b-dropdown-item>
            <b-dropdown-item>2017</b-dropdown-item>
            <b-dropdown-item>2016</b-dropdown-item>
            <b-dropdown-item>2015</b-dropdown-item>
            <b-dropdown-item>2014</b-dropdown-item>
            <b-dropdown-item>2013</b-dropdown-item>
            <b-dropdown-item>2012</b-dropdown-item>
            <b-dropdown-item>2011</b-dropdown-item>
            <b-dropdown-item>2010</b-dropdown-item>
        </b-dropdown>
        <b-dropdown id="dropdown-1" text="Month" class="m-md-2">
            <b-dropdown-item>January</b-dropdown-item>
            <b-dropdown-item>February</b-dropdown-item>
            <b-dropdown-item>March</b-dropdown-item>
            <b-dropdown-item>April</b-dropdown-item>
            <b-dropdown-item>May</b-dropdown-item>
            <b-dropdown-item>June</b-dropdown-item>
            <b-dropdown-item>July</b-dropdown-item>
            <b-dropdown-item>August</b-dropdown-item>
            <b-dropdown-item>September</b-dropdown-item>
            <b-dropdown-item>October</b-dropdown-item>
            <b-dropdown-item>November</b-dropdown-item>
            <b-dropdown-item>December</b-dropdown-item>
        </b-dropdown>
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
            }
        }
    }
</script>