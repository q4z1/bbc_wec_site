<template>
  <div>
    <div v-if="!edit">
      <div class="game-top">
        <div class="game-basic">
          <h3>Basic data</h3>
          <div class="stat-line"><strong>Game Number:</strong><span>#{{ game.number }}</span></div>
          <div class="stat-line"><strong>Date/Time:</strong><span>{{ game.started }}</span></div>
          <div class="stat-line"><strong>Type:</strong><span>{{ type }}</span></div>
          <div class="stat-line">
            <strong>Winner:</strong>
            <span>
              <a :href="'/player/' + encodeURIComponent(winner)" :title="winner">{{ winner }}</a>
            </span>
          </div>
          <div class="stat-line"><strong>Number of Players:</strong><span>{{ game.stats['player_list'][0].length }}</span></div>
          <div class="stat-line"><strong>Hands:</strong><span>{{ game.stats['player_list'][3][0] }}</span></div>

          <div class="game-actions">
            <el-button v-if="arole !== ''" type="warning" @click="show_bb = true">Get BB Code</el-button>
            <el-button v-if="arole === 's'" type="info" @click="edit = true">Edit Game</el-button>
            <el-button v-if="arole === 's'" type="danger" @click="show_delete = true">Delete Game</el-button>
          </div>
        </div>

        <div class="game-ranking">
          <h3>Ranking</h3>
          <el-table :data="ranking" stripe style="width:100%" @row-click="rowClick">
            <el-table-column prop="pos" label="Pos" width="70" />
            <el-table-column prop="player" label="Player" min-width="140" />
            <el-table-column prop="hand" label="Hand" width="80" />
            <el-table-column label="" min-width="220">
              <!-- Enthaelt die Kartensymbole als HTML aus der Log-Auswertung. -->
              <template #default="scope"><span v-html="scope.row._"></span></template>
            </el-table-column>
          </el-table>
        </div>
      </div>

      <div class="game-section">
        <h3>Hand Cash</h3>
        <line-chart-component :chart-data="datacollection1" :options="options1" />
      </div>
      <div class="game-section">
        <h3>Pot Size</h3>
        <bar-chart-component :chart-data="datacollection2" :options="options2" />
      </div>

      <div v-for="section in sections" :key="section.title" class="game-section">
        <h3>{{ section.title }}</h3>
        <el-table :data="section.rows" stripe style="width:100%">
          <el-table-column v-for="col in section.columns" :key="col.prop"
                           :prop="col.prop" :label="col.label"
                           :width="col.width" :min-width="col.minWidth" />
        </el-table>
      </div>

      <p class="game-footnotes">
        <small>
          *) percental value: absolute value in relation to hands played<br />
          **) percental value: number of hands with at least one bet/raise in relation to all hands played
        </small>
      </p>
    </div>

    <div v-else>
      <game-edit-component :game="game" @back="back" @update="update" />
    </div>

    <el-dialog append-to-body v-model="show_bb" title="Forum BB Code" width="700px">
      <el-input id="bbcode_content" v-model="bbcode" type="textarea" :rows="12" />
      <template #footer>
        <el-button type="warning" @click="bb2clipboard">
          <el-icon><DocumentCopy /></el-icon>&nbsp;Copy to Clipboard
        </el-button>
        <el-button type="primary" @click="show_bb = false">Close</el-button>
      </template>
    </el-dialog>

    <el-dialog append-to-body v-model="show_delete" title="Delete Game" width="420px">
      <div>Are you sure to delete game #{{ game.number }}?</div>
      <template #footer>
        <el-button @click="show_delete = false">Cancel</el-button>
        <el-button type="danger" @click="deleteGame">Delete</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

const SERIES_COLORS = [
  'rgba(86, 226, 137, 1.0)',
  'rgba(104, 226, 86, 1.0)',
  'rgba(174, 226, 86, 1.0)',
  'rgba(226, 297, 86, 1.0)',
  'rgba(226, 137, 86, 1.0)',
  'rgba(226, 84, 104, 1.0)',
  'rgba(226, 86, 174, 1.0)',
  'rgba(207, 86, 226, 1.0)',
  'rgba(138, 86, 226, 1.0)',
  'rgba(86, 104, 226, 1.0)',
];

const TYPES = [
  { text: 'regular', value: 1 },
  { text: 'monthly', value: 5 },
  { text: 'yearly', value: 6 },
];

export default {
  props: ['game'],
  data() {
    return {
      datacollection1: null,
      datacollection2: null,
      options1: null,
      options2: null,
      sections: [],
      ranking: [],
      bbcode: '',
      show_bb: false,
      show_delete: false,
      edit: false,
      eGame: this.game,
      type: 1,
      arole: window.arole,
    };
  },
  computed: {
    winner() {
      return this.game.stats.player_list[1][0];
    },
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      this.buildCharts();
      this.buildSections();
      this.buildRanking();

      const t = TYPES.find((typ) => typ.value == this.game.type);
      if (t) this.type = t.text;

      this.bbcode = this.buildBbCode();
    },

    buildCharts() {
      const stats = this.game.stats;

      // Hand Cash
      const labels1 = [];
      for (let i = 1; i <= stats.hand_cash[0].length; i++) {
        labels1.push(i === 1 ? 'Hand: ' + i : i);
      }
      const datasets1 = [];
      try {
        for (const index in stats.hand_cash) {
          if (parseInt(index) >= stats.player_list[0].length) break;
          const hand = stats.hand_cash[index];
          const data = [];
          for (let j = 0; j <= hand.length; j++) data.push(Number(hand[j]));
          datasets1.push({
            label: stats.player_list[1][stats.player_list[0].indexOf(parseInt(index) + 1)],
            borderColor: SERIES_COLORS[parseInt(index)],
            data,
          });
        }
      } catch (e) {
        console.log(e);
      }
      this.datacollection1 = { labels: labels1, datasets: datasets1 };

      // Pot Size
      const labels2 = [];
      const data2 = [];
      for (let i = 0; i < stats.pot_size[0].length; i++) {
        data2.push(100000 - Number(stats.pot_size[0][i]));
        labels2.push(labels1[i]);
      }
      this.datacollection2 = {
        labels: labels2,
        datasets: [{ borderColor: SERIES_COLORS[0], data: data2, label: 'Pot Size' }],
      };

      // Chart.js 4 erwartet die Achsen als Objekt, nicht mehr als xAxes/yAxes-Array.
      this.options1 = {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, min: 0 } },
      };
      this.options2 = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, min: 0 } },
      };
    },

    buildSections() {
      const s = this.game.stats;
      const pct = (v) => Math.round(v);

      const mostHands = [];
      for (let i = 0; i < s['most hands played'][0].length; i++) {
        mostHands.push({
          pos: i + 1,
          player: s['most hands played'][1][i],
          count: pct(s['most hands played'][4][i]) + '% (' + s['most hands played'][2][i] + '/' + s['most hands played'][3][i] + ' hands)',
          p10_7: pct(s['most hands played'][7][i]) + '% (' + s['most hands played'][5][i] + '/' + s['most hands played'][6][i] + ')',
          p6_4: pct(s['most hands played'][10][i]) + '% (' + s['most hands played'][8][i] + '/' + s['most hands played'][9][i] + ')',
          p3_1: pct(s['most hands played'][13][i]) + '% (' + s['most hands played'][11][i] + '/' + s['most hands played'][12][i] + ')',
        });
      }

      const bestHands = [];
      for (let i = 0; i < s['best hands'][0].length; i++) {
        bestHands.push({
          pos: i + 1,
          cards: s['best hands'][2][i],
          player: s['best hands'][1][i],
          hand: s['best hands'][3][i],
          result: s['best hands'][4][i],
        });
      }

      const mostWins = [];
      for (let i = 0; i < s['most wins'][0].length; i++) {
        mostWins.push({
          pos: i + 1,
          player: s['most wins'][1][i],
          count: s['most wins'][2][i] + ' (' + pct(s['most wins'][3][i]) + '%)',
          highest: '$' + s['most wins'][4][i],
        });
      }

      const highestWins = [];
      for (let i = 0; i < s['highest wins'][0].length; i++) {
        highestWins.push({
          pos: i + 1,
          amount: '$' + s['highest wins'][4][i],
          player: s['highest wins'][1][i],
          hand: s['highest wins'][2][i] + (s['highest wins'][3][i] ? ' (side pot)' : ''),
        });
      }

      const longestWins = [];
      const longestLosses = [];
      for (let i = 0; i < 10; i++) {
        longestWins.push({
          pos: i + 1,
          duration: s['longest series of wins'][2][i],
          player: s['longest series of wins'][1][i],
          hands: s['longest series of wins'][3][i] + '-' + s['longest series of wins'][4][i],
          total_gain: s['longest series of wins'][5][i],
        });
        longestLosses.push({
          pos: i + 1,
          duration: s['longest series of losses'][2][i],
          player: s['longest series of losses'][1][i],
          hands: s['longest series of losses'][3][i] + '-' + s['longest series of losses'][4][i],
          total_loss: '$' + s['longest series of losses'][5][i],
        });
      }

      const mostBets = [];
      for (let i = 0; i < s['most bet/raise'][0].length; i++) {
        mostBets.push({
          pos: i + 1,
          player: s['most bet/raise'][1][i],
          count: s['most bet/raise'][2][i] + ' (' + pct(s['most bet/raise'][4][i]) + '%)',
        });
      }

      const mostBingo = [];
      for (let i = 0; i < s['most all in'][0].length; i++) {
        mostBingo.push({
          pos: i + 1,
          player: s['most all in'][1][i],
          total_count: s['most all in'][2][i] + ' (' + pct(s['most all in'][3][i]) + '%)',
          in_preflop: s['most all in'][4][i],
          first_5_hands: s['most all in'][5][i],
          total_won: s['most all in'][6][i],
        });
      }

      const POS = { prop: 'pos', label: 'Pos', width: 70 };
      const PLAYER = { prop: 'player', label: 'Player', minWidth: 140 };

      this.sections = [
        { title: 'Most hands played', rows: mostHands, columns: [
          POS, PLAYER,
          { prop: 'count', label: 'Count', minWidth: 170 },
          { prop: 'p10_7', label: '10 to 7 Player', minWidth: 140 },
          { prop: 'p6_4', label: '6 to 4 Player', minWidth: 140 },
          { prop: 'p3_1', label: '3 to 1 Player', minWidth: 140 },
        ] },
        { title: 'Best hands', rows: bestHands, columns: [
          POS,
          { prop: 'cards', label: 'Cards', minWidth: 220 },
          PLAYER,
          { prop: 'hand', label: 'Hand', width: 90 },
          { prop: 'result', label: 'Result', minWidth: 110 },
        ] },
        { title: 'Most wins', rows: mostWins, columns: [
          POS, PLAYER,
          { prop: 'count', label: 'Count *', minWidth: 130 },
          { prop: 'highest', label: 'Highest', minWidth: 110 },
        ] },
        { title: 'Highest wins', rows: highestWins, columns: [
          POS,
          { prop: 'amount', label: 'Amount', minWidth: 110 },
          PLAYER,
          { prop: 'hand', label: 'Hand', minWidth: 130 },
        ] },
        { title: 'Longest wins', rows: longestWins, columns: [
          POS,
          { prop: 'duration', label: 'Duration', width: 100 },
          PLAYER,
          { prop: 'hands', label: 'Hands', minWidth: 110 },
          { prop: 'total_gain', label: 'Total Gain', minWidth: 120 },
        ] },
        { title: 'Longest losses', rows: longestLosses, columns: [
          POS,
          { prop: 'duration', label: 'Duration', width: 100 },
          PLAYER,
          { prop: 'hands', label: 'Hands', minWidth: 110 },
          { prop: 'total_loss', label: 'Total Loss', minWidth: 120 },
        ] },
        { title: 'Most bets/raises', rows: mostBets, columns: [
          POS, PLAYER,
          { prop: 'count', label: 'Count **', minWidth: 130 },
        ] },
        { title: 'Most all in', rows: mostBingo, columns: [
          POS, PLAYER,
          { prop: 'total_count', label: 'Total Count', minWidth: 130 },
          { prop: 'in_preflop', label: 'In Preflop', minWidth: 110 },
          { prop: 'first_5_hands', label: 'First 5 Hands', minWidth: 130 },
          { prop: 'total_won', label: 'Total Won', minWidth: 110 },
        ] },
      ];
    },

    // Der letzte Eintrag je Spieler ist entweder die Hand, mit der er gewonnen
    // hat, oder der Spieler, der ihn eliminiert hat.
    eliminationText(i) {
      const entry = this.game.stats['player_list'][7][i][0];
      if (typeof entry === 'undefined') return '';
      return entry.indexOf('[') === -1 ? 'eliminated by ' + entry : 'wins with ' + entry;
    },

    buildRanking() {
      const s = this.game.stats;
      this.ranking = [];
      for (let i = 0; i < s['player_list'][0].length; i++) {
        this.ranking.push({
          pos: i + 1,
          player: s['player_list'][1][i],
          hand: s['player_list'][3][i],
          _: this.eliminationText(i),
        });
      }
    },

    buildBbCode() {
      const s = this.game.stats;
      let bb = '[indent][img]/images/Logo-WECUP_small.jpg[/img][/indent]\n';
      bb += '[hr][b][size=85][color=black]♣ [/color][color=darkred]♥[/color][color=black] ♠[/color][color=darkred] ♦ [/color][/size][size=150][color=goldenrod][font=Palatino Linotype]';
      bb += 'WeCUP #' + this.game.number + ' - ' + this.game.started.replace(':00', '');
      bb += '[/font][/color][/size][size=85][color=darkred] ♦ [/color][color=black]♠ [/color][color=darkred] ♥[/color]';
      bb += '[color=black] ♣[/color][/size][/b][br][br]';

      for (let i = 0; i < s['player_list'][0].length; i++) {
        const eliminated = this.eliminationText(i);
        const name = s['player_list'][1][i];
        const hand = s['player_list'][3][i];
        try {
          if (i === 0) {
            bb += '[indent][color=goldenrod]1. ' + name + '  ' + hand + ' wins with '
                + s.player_list[7][0][0].replace(/(<([^>]+)>)/gi, '') + '[/color]\n';
          } else if (i === 1) {
            bb += '[color=silver]2. ' + name + '  ' + hand + ' ' + eliminated + ' [/color]\n';
          } else if (i === 2) {
            bb += '[color=#cd7f32]3. ' + name + '  ' + hand + ' ' + eliminated + ' [/color]\n';
          } else if (i === 3) {
            bb += '4. ' + name + '  ' + hand + ' ' + eliminated + '\n';
          } else if (typeof s.player_list[7][i - 1] !== 'undefined') {
            bb += (i + 1) + '. ' + name + '  ' + hand + ' ' + eliminated + '\n';
          }
        } catch (e) {
          console.log(e);
        }
      }
      bb += '[/indent]';
      bb += '[br][indent][color=darkred][size=150] Congratulations to [b]' + s['player_list'][1][0] + '[/b][/size][/color][/indent]';
      bb += '[hr][size=85][url=https://wec.pokerth.net/results/game/' + this.game.number + '][color=darkred]Log-Analysis[/color][/url]';
      bb += ' of WeCup [font=Arial Narrow]#' + this.game.number + '#' + this.game.started.replace(' ', '#').replace(':00', '');
      for (let i = 0; i <= 10; i++) {
        if (typeof s.player_list[1][i] !== 'undefined') {
          bb += '#' + (s.player_list[1][i] == this.game['pos' + (i + 1)] ? s.player_list[1][i] : this.game['pos' + (i + 1)]);
        } else if (i > 5 && i < 7) {
          bb += '#disco_dummy';
        }
      }
      bb += '[/font][/size][br]';
      bb += '[size=85][url=https://www.pokerth.net/viewtopic.php?f=19&t=25][color=darkred][br]Ranking[/url] of WeCup[/color][/size][hr]';
      return bb;
    },

    rowClick(row) {
      window.location.href = window.location.origin + '/player/' + encodeURIComponent(row.player);
    },
    update(game) {
      this.eGame = game;
    },
    async bb2clipboard() {
      try {
        await navigator.clipboard.writeText(this.bbcode);
        ElMessage({ message: 'BB Code copied to clipboard.', type: 'success' });
      } catch (e) {
        ElMessage({ message: 'Copying to the clipboard failed.', type: 'error' });
      }
    },
    back() {
      window.location.reload();
    },
    deleteGame() {
      axios.get('/delete/game/' + this.game.number).then((res) => {
        if (res.data.status) {
          ElMessage({ message: res.data.msg, type: 'success' });
          window.location.href = '/results';
        } else {
          ElMessage({ message: res.data.msg, type: 'error' });
        }
      }).catch(() => {
        ElMessage({ message: 'Game deletion failed!', type: 'error' });
      });
    },
  },
};
</script>
<style scoped>
.game-top {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-top: 1rem;
}
.game-basic { flex: 1 1 360px; min-width: 0; }
.game-ranking { flex: 1 1 480px; min-width: 0; }

.stat-line { display: flex; gap: 0.5rem; }
.stat-line strong { min-width: 170px; }

.game-actions {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5rem;
  width: 220px;
}
.game-actions .el-button { width: 100%; margin: 0; }

.game-section { margin-top: 1.5rem; }
.game-footnotes { margin-top: 1rem; }
</style>
