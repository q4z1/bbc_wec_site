<template>
    <div>
        <h1>Results ?</h1>
        <b-table striped hover :items="results"></b-table>
    </div>
</template>
<script>
    export default {
        props: ['results'],
        mounted() {
            console.log('Results mounted.')
            console.log(this.results)


            // ajax call => result.data into this.results
        }
    }
</script>