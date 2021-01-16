<template>
    <div>
        <b-container>
            <b-row class="mt-3">
                <b-col>
                    <h3>Basic data</h3>
                    <b-row>
                        <b-col><strong>Game Number:</strong></b-col>
                        <b-col>{{ game.number }}</b-col>
                    </b-row>
                    <b-row>
                        <b-col><strong>Number of Players:</strong></b-col>
                        <b-col>{{ game.stats['player_list'][0].length }}</b-col>
                    </b-row>
                    <b-row>
                        <b-col><strong>Winner:</strong></b-col>
                        <b-col>{{ game.stats['player_list'][1][0] }}</b-col>
                    </b-row>
                    <b-row>
                        <b-col><strong>Hands:</strong></b-col>
                        <b-col>{{ game.stats['player_list'][3][0] }}</b-col>
                    </b-row>
                    <b-row class="mt-5 ml-0">
                        <b-col>
                            <b-row class="w-75">
                                <b-button variant="warning" v-b-modal.bbcode class="w-100">Show BB Code for Forum Post</b-button>
                            </b-row>
                            <b-row class="mt-2 w-75">
                                <b-button variant="info" v-b-modal.edit class="w-100">Edit Game</b-button>
                            </b-row>
                            <b-row class="mt-2 w-75">
                                <b-button variant="danger" v-b-modal.delete class="w-100">Delete Game</b-button>
                            </b-row>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col>
                    <h3>Ranking</h3>
                    <b-table striped hover :items="ranking">
                        <template #cell(_)="data">
                            <span v-html="data.value"></span>
                        </template>
                    </b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Hand Cash</h3>
                    <line-chart-component :chart-data="datacollection1" :options="options1"></line-chart-component>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Pot Size</h3>
                    <bar-chart-component :chart-data="datacollection2" :options="options2"></bar-chart-component>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Most hands played</h3>
                    <b-table striped hover :items="most_hands"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Best hands</h3>
                    <b-table striped hover :items="best_hands"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Most wins</h3>
                    <b-table striped hover :items="most_wins"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Highest wins</h3>
                    <b-table striped hover :items="highest_wins"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Longest wins</h3>
                    <b-table striped hover :items="longest_wins"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Longest losses</h3>
                    <b-table striped hover :items="longest_losses"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Most bets/raises</h3>
                    <b-table striped hover :items="most_bets"></b-table>
                </b-col>
            </b-row>
            <b-row class="mt-3">
                <b-col>
                    <h3>Most all in</h3>
                    <b-table striped hover :items="most_bingo"></b-table>
                </b-col>
            </b-row>
        </b-container>
        <b-modal id="bbcode" title="Forum BB Code" :cancel-disabled="true" v-model="show_bb">
            <b-form-textarea
                id="bbcode_content"
                v-model="bbcode"
                rows="10"
                max-rows="12"
            ></b-form-textarea>
            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        size="sm"
                        class="float-right"
                        @click="show_bb=false"
                    >Close</b-button>
                    <b-button
                        variant="warning"
                        size="sm"
                        class="float-right mr-2"
                        @click="bb2clipboard"
                        title="Copy to Clipboard"
                    ><b-icon-clipboard-plus></b-icon-clipboard-plus></b-button>
                </div>
            </template>
        </b-modal>
        <b-modal id="edit" title="Edit Game">
            <game-edit-component :game="game"></game-edit-component>
        </b-modal>
        <b-modal id="delete" title="Delete Game">
            <h1>Sure to delete?</h1>
        </b-modal>
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
            basic_data: null,
            ranking: null,
            bbcode: null,
            show_bb: false,
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
            // console.log(this.game.stats.hand_cash)
            let labels1 = []
            for(let i=1;i<=this.game.stats.hand_cash[0].length;i++){
                labels1.push("Hand: " + i);
            }
            let datasets1 = []
            console.log(this.game.stats.player_list[0].length)
            try{
                for(let index in this.game.stats.hand_cash){
                    if(parseInt(index) >= this.game.stats.player_list[0].length) break;
                    let hand = this.game.stats.hand_cash[index]
                    let data = []
                    for(let j=0;j<=hand.length;j++){
                        data.push(Number(hand[j]));
                    }
                    let set = {
                        label: this.game.stats.player_list[1][this.game.stats.player_list[0].indexOf((parseInt(index) + 1).toString())],
                        borderColor: colors[parseInt(index)],
                        data: data
                    }
                    datasets1.push(set);
                }
            }catch(e){
                console.log(e)
            }
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
                responsive: true,
                maintainAspectRatio: false
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
                responsive: true,
                maintainAspectRatio: false
            }
            // console.log(this.game.stats['most hands played'])
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
            this.ranking = []
            for(let i=0;i<this.game.stats['player_list'][0].length;i++){
                let eliminated = this.game.stats['player_list'][7][i][0]
                if(typeof eliminated !== 'undefined'){
                    if(eliminated.indexOf('[') == -1){
                        eliminated = 'eliminated by ' + eliminated
                    }else{
                        eliminated = 'wins with ' + eliminated
                    }
                }else{
                    eliminated = ''
                }
                this.ranking.push(
                    {
                        pos: i+1,
                        player: this.game.stats['player_list'][1][i],
                        hand: this.game.stats['player_list'][3][i],
                        _: eliminated
                    }
                )
            }

            this.bbcode = '[indent][img]/media/kunena/attachments/30607/Logo-WECUP_small1.jpg[/img][/indent]\n'
            this.bbcode += '[hr][b][size=6][color=black]♣ [/color][color=darkred]♥[/color][color=black] ♠[/color][color=darkred] ♦ [/color][/size][size=3][color=goldenrod][font=Palatino Linotype]'
            this.bbcode += 'WeCUP #' + this.game.number + ' - ' + new Date(Date.parse(this.game.started.replace(/[-]/g,'/'))).toLocaleString().replace(',', '')
            this.bbcode += '[/font][/color][/size][size=6][color=darkred] ♦ [/color][color=black]♠ [/color][color=darkred] ♥[/color]'
            this.bbcode += '[color=black] ♣[/color][/size][/b][br]'
            for(let i=0;i<this.game.stats['player_list'][0].length;i++){
                let eliminated = this.game.stats['player_list'][7][i][0]
                if(typeof eliminated !== 'undefined'){
                    if(eliminated.indexOf('[') == -1){
                        eliminated = 'eliminated by ' + eliminated
                    }else{
                        eliminated = 'wins with ' + eliminated
                    }
                }else{
                    eliminated = ''
                }
                try{
                    if(i === 0){
                        this.bbcode += '[indent][color=goldenrod]1. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' wins with ' + this.game.stats.player_list[7][0][0].replace(/(<([^>]+)>)/gi, "") + '[/color]' + "\n"
                    }else if(i === 1){
                        this.bbcode += '[color=silver]2. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + ' [/color]' + "\n"
                    }else if(i === 2){
                        this.bbcode += '[color=darkred]3. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + ' [/color]' + "\n"
                    }else if(i === 3){
                        this.bbcode += '[color=black]4. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + "\n"
                    }else if(typeof this.game.stats.player_list[7][i-1] !== 'undefined'){
                        this.bbcode += (i+1) + '. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + "\n"
                    }  
                }catch(e){
                    console.log(e)
                } 
            }
            this.bbcode += '[/color][/indent]'
            this.bbcode += '[br][indent][color=darkred][size=4] Congratulations to [b]' + this.game.stats.result[1].player + '[/b][/size][/color][/indent]'
            this.bbcode += '[hr][size=2][url=https://www.pokerth.net/log-file-analysis/?ID=' + this.game.pdb.replace('.pdb', '') + '&UniqueGameID=' + this.game.unique_game_id + '][color=darkred]Log-Analysis[/color][/url]'
            this.bbcode += '[color=black] of WeCup [font=Arial Narrow]#' + this.game.number + '#' + new Date(Date.parse(this.game.started.replace(/[-]/g,'/'))).toLocaleString().replace(', ', '#')
            for(let i=0;i<=10;i++){
                if(typeof this.game.stats.player_list[1][i] !== 'undefined')
                    this.bbcode += '#' + this.game.stats.player_list[1][i]
                else if (i== 7)
                    this.bbcode += '#disco_dummy'
            }
            this.bbcode += '[/font][/color][/size]'
            this.bbcode += '[size=2][url=https://www.pokerth.net/community/forum/wec/14084-wecup-ranking-2021#48770][color=darkred][br]Ranking[/color][/url][color=black] of WeCup[/color][/size][hr]'
        },
        async copy(s) {
            await navigator.clipboard.writeText(s);
            this.$bvToast.toast(`You can paste the BB Code into forum now.`, {
                title: 'BB Code copied to clipboard.',
                autoHideDelay: 2000,
                appendToast: true,
                variant: 'success',
            })
        },
        bb2clipboard() {
            this.copy(window.document.getElementById("bbcode_content").value)
        }
    },
    mounted(){
        this.init()
    },
}
</script>
<style lang="scss">
    tbody tr{
        cursor: pointer;
    }
</style>