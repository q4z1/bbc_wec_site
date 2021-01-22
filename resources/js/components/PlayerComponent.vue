<template>
    <div>
        <b-row class="mt-3">
            <b-col>
                <h3>{{ player.nickname }}</h3>
                <b-row>
                    <b-col><strong>Total games:</strong></b-col>
                    <b-col>{{ stats.length }}</b-col>
                </b-row>
            </b-col>
            <b-col>
                <h5>Awards:</h5>
            </b-col>
        </b-row>
    </div>
</template>
<script>
    export default {
        props: ['player', 'stats'],
        data() {
            return {
                totalPoints: 0,
                currentMonthPoints: 0,
                currentYearPoints: 0,
                types: [{ text: 'regular', value: 1 }, { text: 'monthly', value: 5 }, { text: 'yearly', value: 6 }],
            }
        },
        computed: {

        },
        created() {
            this.init()
        },
        methods: {
            init(){
                this.stats.forEach(points => {
                    console.log(points)
                })
            }
        }
    }
</script>
<style lang="scss" scoped>

</style>