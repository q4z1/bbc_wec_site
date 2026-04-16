<template>
  <div>
    <div v-if="game && !edit">
      <div style="display:flex;gap:1rem;margin-top:0.75rem;">
        <div style="flex:1;min-width:0;">
          <h3>Basic data</h3>
          <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Game Number:</strong></div><div>#{{ game.number }}</div></div>
          <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Date/Time:</strong></div><div>{{ game.started }}</div></div>
          <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Type:</strong></div><div>{{ type }}</div></div>
          <div style="display:flex;gap:0.5rem;">
            <div style="min-width:160px;"><strong>Winner:</strong></div>
            <div><a :href="'/player/' + encodeURIComponent(game.stats['player_list'][1][0])">{{ game.stats["player_list"][1][0] }}</a></div>
          </div>
          <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Number of Players:</strong></div><div>{{ game.stats["player_list"][0].length }}</div></div>
          <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Hands:</strong></div><div>{{ game.stats["player_list"][3][0] }}</div></div>
          <div style="margin-top:3rem;margin-bottom:0.75rem;">
            <div v-if="arole !== ''" style="width:75%;margin-bottom:0.5rem;">
              <el-button type="warning" @click="showBbDialog = true" style="width:100%">Get BB Code</el-button>
            </div>
            <div v-if="arole === 'a' || arole === 's'" style="width:75%;margin-bottom:0.5rem;">
              <el-button type="info" @click="edit = true" style="width:100%">Edit Game</el-button>
            </div>
            <div v-if="arole === 'a' || arole === 's'" style="width:75%;">
              <el-button type="danger" @click="showDeleteDialog = true" style="width:100%">Delete Game</el-button>
            </div>
          </div>
        </div>
        <div style="flex:1;min-width:0;">
          <h3>Ranking</h3>
          <el-table :data="ranking" stripe @row-click="rowClick" style="cursor:pointer;width:100%">
            <el-table-column prop="pos" label="Pos" width="60" />
            <el-table-column prop="player" label="Player" />
            <el-table-column prop="hand" label="Hand" />
            <el-table-column label="">
              <template #default="{ row }"><span v-html="row._"></span></template>
            </el-table-column>
          </el-table>
        </div>
      </div>
      <div style="margin-top:0.75rem;">
        <h3>Hand Cash</h3>
        <line-chart-component :chart-data="datacollection1" :options="options1" />
      </div>
      <div style="margin-top:0.75rem;">
        <h3>Pot Size</h3>
        <bar-chart-component :chart-data="datacollection2" :options="options2" />
      </div>
      <div style="margin-top:0.75rem;"><h3>Most hands played</h3><el-table :data="most_hands" stripe style="width:100%"><el-table-column v-for="k in most_hand_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Best hands</h3><el-table :data="best_hands" stripe style="width:100%"><el-table-column v-for="k in best_hand_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Most wins</h3><el-table :data="most_wins" stripe style="width:100%"><el-table-column v-for="k in most_win_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Highest wins</h3><el-table :data="highest_wins" stripe style="width:100%"><el-table-column v-for="k in highest_win_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Longest wins</h3><el-table :data="longest_wins" stripe style="width:100%"><el-table-column v-for="k in longest_win_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Longest losses</h3><el-table :data="longest_losses" stripe style="width:100%"><el-table-column v-for="k in longest_loss_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Most bets/raises</h3><el-table :data="most_bets" stripe style="width:100%"><el-table-column v-for="k in most_bet_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.75rem;"><h3>Most all in</h3><el-table :data="most_bingo" stripe style="width:100%"><el-table-column v-for="k in most_bingo_keys" :key="k" :prop="k" :label="k" /></el-table></div>
      <div style="margin-top:0.5rem;"><small>*) percental value: absolute value in relation to hands played<br />**) percental value: number of hands with at least one bet/raise in relation to all hands played</small></div>
    </div>
    <div v-else-if="game">
      <game-edit-component :game="game" @back="back" @update="update" />
    </div>

    <!-- BB Code Dialog -->
    <el-dialog append-to-body v-if="bbcode" v-model="showBbDialog" title="Forum BB Code" width="700px">
      <el-input type="textarea" v-model="bbcode" :rows="10" id="bbcode_content" />
      <template #footer>
        <el-button @click="showBbDialog = false">Close</el-button>
        <el-button type="warning" @click="bb2clipboard" title="Copy to Clipboard">
          <el-icon><CopyDocument /></el-icon>
        </el-button>
      </template>
    </el-dialog>

    <!-- Delete Dialog -->
    <el-dialog append-to-body v-if="game" v-model="showDeleteDialog" title="Delete Game">
      <p>Are you sure to delete game #{{ game.number }}?</p>
      <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
      <template #footer>
        <el-button @click="showDeleteDialog = false">Cancel</el-button>
        <el-button type="danger" @click="deleteGame">Delete</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
  props: ['game'],
  data() {
    return {
      reason: '',
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
      ranking: null,
      bbcode: null,
      showBbDialog: false,
      showDeleteDialog: false,
      edit: false,
      eGame: this.game,
      type: 1,
      types: [{ text: 'Step 1', value: 1 }, { text: 'Step 2', value: 2 }, { text: 'Step 3', value: 3 }, { text: 'Step 4', value: 4 }],
      arole: window.arole,
      tickets: false,
    };
  },
  computed: {
    most_hand_keys() { return this.most_hands && this.most_hands.length ? Object.keys(this.most_hands[0]) : []; },
    best_hand_keys() { return this.best_hands && this.best_hands.length ? Object.keys(this.best_hands[0]) : []; },
    most_win_keys() { return this.most_wins && this.most_wins.length ? Object.keys(this.most_wins[0]) : []; },
    highest_win_keys() { return this.highest_wins && this.highest_wins.length ? Object.keys(this.highest_wins[0]) : []; },
    longest_win_keys() { return this.longest_wins && this.longest_wins.length ? Object.keys(this.longest_wins[0]) : []; },
    longest_loss_keys() { return this.longest_losses && this.longest_losses.length ? Object.keys(this.longest_losses[0]) : []; },
    most_bet_keys() { return this.most_bets && this.most_bets.length ? Object.keys(this.most_bets[0]) : []; },
    most_bingo_keys() { return this.most_bingo && this.most_bingo.length ? Object.keys(this.most_bingo[0]) : []; },
  },
  methods: {
    init() {
      const colors = ['rgba(86,226,137,1)','rgba(104,226,86,1)','rgba(174,226,86,1)','rgba(226,184,86,1)','rgba(226,137,86,1)','rgba(226,84,104,1)','rgba(226,86,174,1)','rgba(207,86,226,1)','rgba(138,86,226,1)','rgba(86,104,226,1)'];
      const labels1 = [];
      for (let i = 1; i <= this.game.stats.hand_cash[0].length; i++) labels1.push(i === 1 ? 'Hand: ' + i : i);
      const datasets1 = [];
      try {
        for (let index in this.game.stats.hand_cash) {
          if (parseInt(index) >= this.game.stats.player_list[0].length) break;
          const hand = this.game.stats.hand_cash[index];
          datasets1.push({
            label: this.game.stats.player_list[1][this.game.stats.player_list[0].indexOf(parseInt(index) + 1)],
            borderColor: colors[parseInt(index)],
            data: Array.from(hand, Number),
          });
        }
      } catch (e) { console.log(e); }
      this.datacollection1 = { labels: labels1, datasets: datasets1 };
      // pot size
      const data2 = this.game.stats.pot_size[0].map(v => 100000 - Number(v));
      this.datacollection2 = { labels: labels1.slice(0, data2.length), datasets: [{ borderColor: colors[0], data: data2, label: 'Pot Size' }] };
      // Chart.js v4 options (y axis flat, not yAxes array)
      this.options1 = { scales: { y: { beginAtZero: true, min: 0 } }, responsive: true, maintainAspectRatio: false };
      this.options2 = { scales: { y: { beginAtZero: true, min: 0 } }, plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false };

      // stats tables
      this.most_hands = [];
      for (let i = 0; i < this.game.stats['most hands played'][0].length; i++) {
        this.most_hands.push({ pos: i+1, player: this.game.stats['most hands played'][1][i], count: Math.round(this.game.stats['most hands played'][4][i])+'% ('+this.game.stats['most hands played'][2][i]+'/'+this.game.stats['most hands played'][3][i]+' hands)', '_10_to_7_player': Math.round(this.game.stats['most hands played'][7][i])+'% ('+this.game.stats['most hands played'][5][i]+'/'+this.game.stats['most hands played'][6][i]+')', '_6_to_4_player': Math.round(this.game.stats['most hands played'][10][i])+'% ('+this.game.stats['most hands played'][8][i]+'/'+this.game.stats['most hands played'][9][i]+')', '_3_to_1_player': Math.round(this.game.stats['most hands played'][13][i])+'% ('+this.game.stats['most hands played'][11][i]+'/'+this.game.stats['most hands played'][12][i]+')' });
      }
      this.best_hands = [];
      for (let i = 0; i < this.game.stats['best hands'][0].length; i++) {
        this.best_hands.push({ pos: i+1, cards: this.game.stats['best hands'][2][i], player: this.game.stats['best hands'][1][i], hand: this.game.stats['best hands'][3][i], result: this.game.stats['best hands'][4][i] });
      }
      this.most_wins = [];
      for (let i = 0; i < this.game.stats['most wins'][0].length; i++) {
        this.most_wins.push({ pos: i+1, player: this.game.stats['most wins'][1][i], 'count *': this.game.stats['most wins'][2][i]+' ('+Math.round(this.game.stats['most wins'][3][i])+'%)', highest: '$'+this.game.stats['most wins'][4][i] });
      }
      this.highest_wins = [];
      for (let i = 0; i < this.game.stats['highest wins'][0].length; i++) {
        this.highest_wins.push({ pos: i+1, amount: '$'+this.game.stats['highest wins'][4][i], player: this.game.stats['highest wins'][1][i], hand: this.game.stats['highest wins'][2][i]+(this.game.stats['highest wins'][3][i]?' (side pot)':'') });
      }
      this.longest_wins = [];
      for (let i = 0; i < 10; i++) {
        this.longest_wins.push({ pos: i+1, duration: this.game.stats['longest series of wins'][2][i], player: this.game.stats['longest series of wins'][1][i], hands: this.game.stats['longest series of wins'][3][i]+'-'+this.game.stats['longest series of wins'][4][i], total_gain: this.game.stats['longest series of wins'][5][i] });
      }
      this.longest_losses = [];
      for (let i = 0; i < 10; i++) {
        this.longest_losses.push({ pos: i+1, duration: this.game.stats['longest series of losses'][2][i], player: this.game.stats['longest series of losses'][1][i], hands: this.game.stats['longest series of losses'][3][i]+'-'+this.game.stats['longest series of losses'][4][i], total_loss: '$'+this.game.stats['longest series of losses'][5][i] });
      }
      this.most_bets = [];
      for (let i = 0; i < this.game.stats['most bet/raise'][0].length; i++) {
        this.most_bets.push({ pos: i+1, player: this.game.stats['most bet/raise'][1][i], 'Count **': this.game.stats['most bet/raise'][2][i]+' ('+Math.round(this.game.stats['most bet/raise'][4][i])+'%)' });
      }
      this.most_bingo = [];
      for (let i = 0; i < this.game.stats['most all in'][0].length; i++) {
        this.most_bingo.push({ pos: i+1, player: this.game.stats['most all in'][1][i], total_count: this.game.stats['most all in'][2][i]+' ('+Math.round(this.game.stats['most all in'][3][i])+'%)', in_preflop: this.game.stats['most all in'][4][i], first_5_hands: this.game.stats['most all in'][5][i], total_won: this.game.stats['most all in'][6][i] });
      }
      this.ranking = [];
      for (let i = 0; i < this.game.stats['player_list'][0].length; i++) {
        let eliminated = this.game.stats['player_list'][7][i][0];
        if (typeof eliminated !== 'undefined') {
          eliminated = eliminated.indexOf('[') === -1 ? 'eliminated by ' + eliminated : 'wins with ' + eliminated;
        } else { eliminated = ''; }
        this.ranking.push({ pos: i+1, player: this.game.stats['player_list'][1][i], hand: this.game.stats['player_list'][3][i], _: eliminated });
      }
      this.types.forEach(t => { if (t.value == this.game.type) this.type = t.text; });
      // build bbcode
      this.bbcode = '[indent][img]/images/bbc_logo.png[/img][/indent]\n';
      this.bbcode += '[hr][b][size=185][color=black]♣ [/color][color=darkred]♥[/color][color=black] ♠[/color][color=darkred] ♦ [/color][/size][size=200][color=goldenrod][font=Palatino Linotype]';
      this.bbcode += 'BBC Step' + this.game.type + ' # ' + this.game.number + ' - ' + this.game.started;
      this.bbcode += '[/font][/color][/size][size=185][color=darkred] ♦ [/color][color=black]♠ [/color][color=darkred] ♥[/color][color=black] ♣[/color][/size][/b][br][br]';
      for (let i = 0; i < this.game.stats['player_list'][0].length; i++) {
        let eliminated = this.game.stats['player_list'][7][i][0];
        if (typeof eliminated !== 'undefined') {
          eliminated = eliminated.indexOf('[') === -1 ? 'eliminated by ' + eliminated : 'wins with ' + eliminated;
        } else { eliminated = ''; }
        try {
          if (i === 0) { this.bbcode += '[indent][color=goldenrod]1. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' wins with ' + this.game.stats.player_list[7][0][0].replace(/(<([^>]+)>)/gi,'') + '[/color]\n'; }
          else if (i === 1) { this.bbcode += '[color=silver]2. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + ' [/color]\n'; }
          else if (i === 2) { this.bbcode += '[color=#cd7f32]3. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + ' [/color]\n'; }
          else if (i === 3) { this.bbcode += '4. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + '\n'; }
          else if (typeof this.game.stats.player_list[7][i-1] !== 'undefined') { this.bbcode += (i+1) + '. ' + this.game.stats['player_list'][1][i] + '  ' + this.game.stats['player_list'][3][i] + ' ' + eliminated + '\n'; }
        } catch (e) { console.log(e); }
      }
      this.bbcode += '[/indent]';
      this.bbcode += '[br][indent][color=goldenrod][size=150] Congratulations to [b]' + this.game.stats['player_list'][1][0] + ' & ' + this.game.stats['player_list'][1][1] + '[/b][/size][/color][/indent]';
      this.bbcode += '[br][indent][size=150][color=silver] Bravo ' + this.game.stats['player_list'][1][2] + '[/size][/indent]';
      this.bbcode += '[hr][size=200][url=https://bbc.pokerth.net/results/game/' + this.game.number + '][color=brightred][indent]Game Results[/color][/url]';
    },
    rowClick(row) {
      window.location.href = window.location.origin + '/player/' + encodeURIComponent(row.player);
    },
    update() {
      window.location.href = window.location.href;
    },
    async copy(s) {
      await navigator.clipboard.writeText(s);
      ElMessage({ message: 'BB Code copied to clipboard.', type: 'success' });
    },
    bb2clipboard() { this.copy(this.bbcode); },
    back() { window.location.href = window.location.href; },
    deleteGame() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      data.append('tickets', this.tickets);
      data.append('reason', this.reason);
      axios({ method: 'post', url: '/delete/game/' + this.game.number, data, headers: { 'Content-Type': 'application/json' } })
        .then((response) => {
          if (response.data.status) { ElMessage({ message: response.data.msg, type: 'success' }); window.location.href = '/results'; }
          else { ElMessage({ message: response.data.msg, type: 'error' }); }
        })
        .catch(() => { ElMessage({ message: 'Game deletion failed!', type: 'error' }); });
    },
  },
  mounted() { this.init(); },
};
</script>
<style lang="scss" scoped>
.el-table { cursor: pointer; overflow-x: auto; }
</style>
