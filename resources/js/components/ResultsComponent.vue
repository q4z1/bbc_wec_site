<template>
    <div>
        <h1>Results ?</h1>
        <b-table striped hover 
            :items="results"
            @row-clicked="showGame"></b-table>
    </div>
</template>
<script>
    export default {
        props: ['results'],
        mounted() {
            console.log('Results mounted.')
            console.log(this.results)


            // ajax call => result.data into this.results
        },
        methods:{
            showGame(item, index, event) {
                window.location.href = '/results/game/' + item.id
            }
        }
    }
</script>