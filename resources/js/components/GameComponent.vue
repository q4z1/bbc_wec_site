<template>
    <div>
        <b-container class="bv-example-row">
            <b-row>
                <b-col>
                    <h3>Hand Cash</h3>
                    <line-chart-component :chart-data="datacollection1" :options="options1"></line-chart-component>
                </b-col>
                <b-col>
                    <h3>Pot Size</h3>
                    <bar-chart-component :chart-data="datacollection2" :options="options2"></bar-chart-component>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h3>Most hands played</h3>
                    <b-table striped hover :items="most_hands"></b-table>
                </b-col>
                <b-col>
                    <h3>Best hands</h3>
                    <b-table striped hover :items="best_hands"></b-table>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h3>Most wins</h3>
                    <b-table striped hover :items="most_wins"></b-table>
                </b-col>
                <b-col>
                    <h3>Highest wins</h3>
                    <b-table striped hover :items="highest_wins"></b-table>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h3>Longest wins</h3>
                    <b-table striped hover :items="longest_wins"></b-table>
                </b-col>
                <b-col>
                    <h3>Longest losses</h3>
                    <b-table striped hover :items="longest_losses"></b-table>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <h3>Most bets/raises</h3>
                    <b-table striped hover :items="most_bets"></b-table>
                </b-col>
                <b-col>
                    <h3>Most all in</h3>
                    <b-table striped hover :items="most_bingo"></b-table>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>
<script>
export default {
    props: ['game'],
    data () {
        return {
            datacollection1: null,
            datacollection2: null,
            options1: null,
            options2: null,
            most_hands: null,
            best_hands: null,
            most_wins: null,
            highest_wins: null,
            longest_wins: null,
            longest_losses: null,
            most_bets: null,
            most_bingo: null,
        }
    },
    methods:{
        init(){
            let colors = [
                'rgba(86, 226, 137, 1.0)',
                'rgba(104, 226, 86, 1.0)',
                'rgba(174, 226, 86, 1.0)',
                'rgba(226, 297, 86, 1.0)',
                'rgba(226, 137, 86, 1.0)',
                'rgba(226, 84, 104, 1.0)',
                'rgba(226, 86, 174, 1.0)',
                'rgba(207, 86, 226, 1.0)',
                'rgba(138, 86, 226, 1.0)',
                'rgba(86, 104, 226, 1.0)'
            ]
            // hand cash
            let labels1 = []
            for(let i=1;i<=this.game.stats.hand_cash[0].length;i++){
                labels1.push("Hand: " + i);
            }
            let datasets1 = []
            for(let i=0;i<this.game.stats.player_list[0].length;i++){
                let data = []
                for(let j=0;j<=this.game.stats.hand_cash[i].length;j++){
                    data.push(Number(this.game.stats.hand_cash[i][j]));
                }
                let set = {
                    label: this.game.stats.player_list[1][i],
                    borderColor: colors[i],
                    data: data
                }
                datasets1.push(set);
            }
            // console.log(datasets1)
            this.datacollection1 = {
                labels: labels1,
                datasets: datasets1
            }
            // pot size
            let labels2 = []
            let data2 = []
            for(let i=0;i<this.game.stats.pot_size[0].length;i++){
                data2.push(100000 - Number(this.game.stats.pot_size[0][i]));
                labels2.push(labels1[i])
            }
            let set2 =[{
                borderColor: colors[0],
                data: data2,
                label: 'Poz Size'
            }]
            this.datacollection2 = {
                labels: labels2,
                datasets: set2
            },
            // options
            this.options1 = {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: 0
                        }
                    }]
                },
                // legend: {
                //     display: false
                // },
            },
            this.options2 = {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: 0
                        }
                    }]
                },
                legend: {
                    display: false
                },
            }
            console.log(this.game.stats['most hands played'])
            this.most_hands = []
            for(let i=0;i<this.game.stats['most hands played'][0].length;i++){
                this.most_hands.push(
                    {
                        pos: i+1,
                        player: this.game.stats['most hands played'][1][i],
                        count: Math.round(this.game.stats['most hands played'][4][i]) +
                                '% (' + this.game.stats['most hands played'][2][i] + '/' + this.game.stats['most hands played'][3][i] + ' hands)',
                        _10_to_7_player: Math.round(this.game.stats['most hands played'][7][i]) +
                                '% (' + this.game.stats['most hands played'][5][i] + '/' + this.game.stats['most hands played'][6][i] + ')',
                        _6_to_4_player: Math.round(this.game.stats['most hands played'][10][i]) +
                                '% (' + this.game.stats['most hands played'][8][i] + '/' + this.game.stats['most hands played'][9][i] + ')',
                        _3_to_1_player: Math.round(this.game.stats['most hands played'][13][i]) +
                                '% (' + this.game.stats['most hands played'][11][i] + '/' + this.game.stats['most hands played'][12][i] + ')',
                    }
                )
            }
            this.best_hands = []
            for(let i=0;i<this.game.stats['best hands'][0].length;i++){
                this.best_hands.push(
                    {
                        pos: i+1,
                        cards: this.game.stats['best hands'][2][i],
                        player: this.game.stats['best hands'][1][i],
                        hand: this.game.stats['best hands'][3][i],
                        result: this.game.stats['best hands'][4][i]
                    }
                )
            }
            this.most_wins = []
            for(let i=0;i<this.game.stats['most wins'][0].length;i++){
                this.most_wins.push(
                    {
                        pos: i+1,
                        player: this.game.stats['most wins'][1][i],
                        'count *': this.game.stats['most wins'][2][i] + ' (' + Math.round(this.game.stats['most wins'][3][i]) + '%)',
                        highest: '$' + this.game.stats['most wins'][4][i]
                    }
                )
            }
            this.highest_wins = []
            for(let i=0;i<this.game.stats['highest wins'][0].length;i++){
                this.highest_wins.push(
                    {
                        pos: i+1,
                        amount: '$' + this.game.stats['highest wins'][4][i],
                        player: this.game.stats['highest wins'][1][i],
                        hand: this.game.stats['highest wins'][2][i] + ((this.game.stats['highest wins'][3][i]) ? ' (side pot)' : ''),
                    }
                )
            }
            this.longest_wins = []
            for(let i=0;i<10;i++){
                this.longest_wins.push(
                    {
                        pos: i+1,
                        duration: this.game.stats['longest series of wins'][2][i],
                        player: this.game.stats['longest series of wins'][1][i],
                        hands: this.game.stats['longest series of wins'][3][i] + '-' + this.game.stats['longest series of wins'][4][i],
                        total_gain: this.game.stats['longest series of wins'][5][i],
                    }
                )
            }
            this.longest_losses = []
            for(let i=0;i<10;i++){
                this.longest_losses.push(
                    {
                        pos: i+1,
                        duration: this.game.stats['longest series of losses'][2][i],
                        player: this.game.stats['longest series of losses'][1][i],
                        hands: this.game.stats['longest series of losses'][3][i] + '-' + this.game.stats['longest series of losses'][4][i],
                        total_loss: '$' + this.game.stats['longest series of losses'][5][i],
                    }
                )
            }
            this.most_bets = []
            for(let i=0;i<this.game.stats['most bet/raise'][0].length;i++){
                this.most_bets.push(
                    {
                        pos: i+1,
                        player: this.game.stats['most bet/raise'][1][i],
                        'Count **': this.game.stats['most bet/raise'][2][i] + ' (' + Math.round(this.game.stats['most bet/raise'][4][i]) + '%)',
                    }
                )
            }
            this.most_bingo = []
            for(let i=0;i<this.game.stats['most all in'][0].length;i++){
                this.most_bingo.push(
                    {
                        pos: i+1,
                        player: this.game.stats['most all in'][1][i],
                        total_count: this.game.stats['most all in'][2][i] + ' (' + Math.round(this.game.stats['most all in'][3][i]) + '%)',
                        in_preflop: this.game.stats['most all in'][4][i],
                        first_5_hands: this.game.stats['most all in'][5][i],
                        total_won: this.game.stats['most all in'][6][i],
                    }
                )
            }
        }
    },
    mounted(){
        this.init()
    },
}
</script>
<style lang="sass" scoped>

</style>