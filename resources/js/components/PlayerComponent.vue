<template>
    <div>
        <b-row class="mt-3">
            <b-col>
                <h2>{{ player.nickname }}</h2>
                <b-row>
                    <b-col><strong>Total games:</strong></b-col>
                    <b-col>{{ stats.games_alltime }}</b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Month</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col></b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.month.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.score_month }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <h5>Current Year</h5>
                        <b-row>
                            <b-col><strong>Place:</strong></b-col>
                            <b-col></b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Games:</strong></b-col>
                            <b-col>{{ stats.year.games }}</b-col>
                        </b-row>
                        <b-row>
                            <b-col><strong>Score:</strong></b-col>
                            <b-col>{{ stats.score_year }}</b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col class="awards">
                <h3>Awards:</h3>
                <b-row>
                    <b-col v-for="(award, key) in awards" :key="key">
                        <b-row>
                            <b-col class="text-center"><img :src="award.filename" /></b-col>
                        </b-row>
                        <b-row class="text-center">
                            <b-col>{{ award.title }}</b-col>
                        </b-row>
                        
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>
<script>
    export default {
        props: ['player', 'stats', 'awards'],
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

            }
        }
    }
</script>
<style lang="scss" scoped>
.awards img{
    width: 120px;
}
</style>