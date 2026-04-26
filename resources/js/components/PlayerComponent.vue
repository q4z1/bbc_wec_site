<template>
    <div>
        <el-alert v-if="delete_fail" title="Something went wrong!" type="error" show-icon closable @close="delete_fail=false" style="margin-bottom:0.5rem;" />
        <el-alert v-if="ticket_fail" title="Something went wrong!" type="error" show-icon closable @close="ticket_fail=false" style="margin-bottom:0.5rem;" />
        <el-alert v-if="ticket_success" title="Tickets successfully edited!" type="success" show-icon closable @close="ticket_success=false" style="margin-bottom:0.5rem;" />
        <div style="margin-top:0.75rem;">
            <h1>{{ player.nickname }}</h1>
            <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Total games:</strong></div><div>{{ stats.alltime.games }}</div></div>

            <!-- Current Season -->
            <div style="margin-top:0.75rem;">
                <h5>Current Season</h5>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Place:</strong></div><div>{{ stats.season.pos }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Games:</strong></div><div>{{ stats.season.games }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Points:</strong></div><div>{{ stats.season.points }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Score:</strong></div><div>{{ stats.season.score }}</div></div>
            </div>
            <div v-if="stats.season.games" style="margin-top:0.5rem;">
                <div style="margin-bottom:0.75rem;">
                    <strong>Results:</strong>
                    <a style="margin-left:0.25rem;" v-if="stats.season.places[0]" @click.prevent="showSeasonStep(1)" href="#">Step 1</a>
                    <a style="margin-left:0.25rem;" v-if="stats.season.places[1]" @click.prevent="showSeasonStep(2)" href="#">Step 2</a>
                    <a style="margin-left:0.25rem;" v-if="stats.season.places[2]" @click.prevent="showSeasonStep(3)" href="#">Step 3</a>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="seasonS1"><strong>Step 1</strong></div>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="seasonS2"><strong>Step 2</strong></div>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="seasonS3"><strong>Step 3</strong></div>
                </div>
                <div v-if="stats.season.places[0]" v-show="seasonS1" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="seasonBarS1" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.season.places[0]" :height="100"/></div>
                    <div v-show="seasonPieS1" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.season.places[0]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.season.places[0])" stripe size="small" @row-click="(r,c,e)=>switchSeasonChartS1(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.season.places[0])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
                <div v-if="stats.season.places[1]" v-show="seasonS2" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="seasonBarS2" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.season.places[1]" :height="100"/></div>
                    <div v-show="seasonPieS2" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.season.places[1]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.season.places[1])" stripe size="small" @row-click="(r,c,e)=>switchSeasonChartS2(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.season.places[1])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
                <div v-if="stats.season.places[2]" v-show="seasonS3" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="seasonBarS3" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.season.places[2]" :height="100"/></div>
                    <div v-show="seasonPieS3" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.season.places[2]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.season.places[2])" stripe size="small" @row-click="(r,c,e)=>switchSeasonChartS3(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.season.places[2])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
            </div>

            <!-- All-Time -->
            <div style="margin-top:0.75rem;">
                <h5>All-Time</h5>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Place:</strong></div><div>{{ stats.alltime.pos }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Games:</strong></div><div>{{ stats.alltime.games }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Points:</strong></div><div>{{ stats.alltime.points }}</div></div>
                <div style="display:flex;gap:0.5rem;"><div style="min-width:160px;"><strong>Score:</strong></div><div>{{ stats.alltime.score }}</div></div>
            </div>
            <div v-if="stats.alltime.games" style="margin-top:0.5rem;">
                <div style="margin-bottom:0.75rem;">
                    <strong>Results:</strong>
                    <a style="margin-left:0.25rem;" v-if="stats.alltime.places[0]" @click.prevent="showAlltimeStep(1)" href="#">Step 1</a>
                    <a style="margin-left:0.25rem;" v-if="stats.alltime.places[1]" @click.prevent="showAlltimeStep(2)" href="#">Step 2</a>
                    <a style="margin-left:0.25rem;" v-if="stats.alltime.places[2]" @click.prevent="showAlltimeStep(3)" href="#">Step 3</a>
                    <a style="margin-left:0.25rem;" v-if="stats.alltime.places[3]" @click.prevent="showAlltimeStep(4)" href="#">Step 4</a>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="alltimeS1"><strong>Step 1</strong></div>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="alltimeS2"><strong>Step 2</strong></div>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="alltimeS3"><strong>Step 3</strong></div>
                    <div style="margin-top:0.5rem;text-align:center;" v-show="alltimeS4"><strong>Step 4</strong></div>
                </div>
                <div v-if="stats.alltime.places[0]" v-show="alltimeS1" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="alltimeBarS1" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.alltime.places[0]" :height="100"/></div>
                    <div v-show="alltimePieS1" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.alltime.places[0]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.alltime.places[0])" stripe size="small" @row-click="(r,c,e)=>switchAlltimeChartS1(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.alltime.places[0])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
                <div v-if="stats.alltime.places[1]" v-show="alltimeS2" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="alltimeBarS2" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.alltime.places[1]" :height="100"/></div>
                    <div v-show="alltimePieS2" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.alltime.places[1]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.alltime.places[1])" stripe size="small" @row-click="(r,c,e)=>switchAlltimeChartS2(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.alltime.places[1])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
                <div v-if="stats.alltime.places[2]" v-show="alltimeS3" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="alltimeBarS3" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.alltime.places[2]" :height="100"/></div>
                    <div v-show="alltimePieS3" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.alltime.places[2]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.alltime.places[2])" stripe size="small" @row-click="(r,c,e)=>switchAlltimeChartS3(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.alltime.places[2])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
                <div v-if="stats.alltime.places[3]" v-show="alltimeS4" style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <div v-show="alltimeBarS4" style="flex:0 1 200px;margin-bottom:0.75rem;"><BarChart :chartData="stats.alltime.places[3]" :height="100"/></div>
                    <div v-show="alltimePieS4" style="flex:0 1 200px;margin-bottom:0.75rem;"><PieChart :chartData="stats.alltime.places[3]" :height="100"/></div>
                    <div style="flex:1;min-width:250px;margin-bottom:0.75rem;"><el-table :data="getPlacesFormatted(stats.alltime.places[3])" stripe size="small" @row-click="(r,c,e)=>switchAlltimeChartS4(r,e)" style="width:100%"><el-table-column v-for="k in placesKeys(stats.alltime.places[3])" :key="k" :prop="k" :label="k" /></el-table></div>
                </div>
            </div>

            <!-- Tickets display -->
            <div style="display:flex;gap:0.5rem;align-items:center;padding-bottom:0.5rem;">
                <div style="min-width:120px;"><strong>Tickets:</strong></div>
                <div style="display:flex;gap:1rem;">
                    <div><strong style="color:#67c23a;">Step 2:</strong>&nbsp;&nbsp;{{ s2 }}</div>
                    <div><strong style="color:#409eff;">Step 3:</strong>&nbsp;&nbsp;{{ s3 }}</div>
                    <div><strong style="color:#e6a817;">Step 4:</strong>&nbsp;&nbsp;{{ s4 }}</div>
                </div>
            </div>

            <!-- Admin Buttons -->
            <div v-if="arole === 'a' || arole === 's'" style="margin-top:3rem;margin-bottom:0.75rem;display:flex;flex-direction:column;align-items:flex-start;gap:0.5rem;width:220px;">
                <el-button type="warning" @click="showTicketsDialog = true" style="width:100%">Edit Tickets</el-button>
                <el-button type="danger" @click="showDeleteDialog = true" style="width:100%;margin:0;">Delete Player</el-button>
            </div>
        </div>
        <hr />
        <div class="awards">
            <h3>Awards:</h3>
            <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                <div v-for="(award, key) in awards" :key="key" style="text-align:center;">
                    <div><img :src="award.filename" /></div>
                    <div>{{ award.title }}</div>
                </div>
            </div>
        </div>
        <hr />
        <h3>Games:</h3>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
            <div style="flex:1;min-width:150px;">
                <el-select :disabled="alltime" v-model="season_select" @change="filter()" style="width:100%">
                    <el-option v-for="s in seasons" :key="s.value" :label="s.text" :value="s.value" />
                </el-select>
            </div>
            <div style="flex:1;min-width:150px;">
                <el-select v-model="type" @change="filter()" style="width:100%">
                    <el-option v-for="t in gameTypes" :key="t.value" :label="t.text" :value="t.value" />
                </el-select>
            </div>
            <div style="flex:1;display:flex;align-items:center;min-width:150px;">
                <el-switch v-model="alltime" @change="filter()" />
                <span style="margin-left:0.5rem;">All-Time</span>
            </div>
            <div style="flex:1;display:flex;justify-content:flex-end;min-width:100px;">
                <el-button type="primary" @click="reset">Reset</el-button>
            </div>
        </div>
        <div>
            <el-table v-if="games" :data="games" stripe @row-click="showGame" style="width:100%;cursor:pointer">
                <el-table-column v-for="k in gameKeys" :key="k" :prop="k" :label="k">
                    <template #default="{ row }"><span v-html="row[k]"></span></template>
                </el-table-column>
            </el-table>
            <p v-else style="margin-top:1.5rem;">No games found for this period.</p>
            <el-pagination v-if="games && total > 10" v-model:current-page="page" :page-size="10" :total="total" layout="prev, pager, next" @current-change="filter" style="margin-top:0.75rem;" />
        </div>

        <!-- Delete Player Dialog -->
        <el-dialog append-to-body v-model="showDeleteDialog" :title="'Delete Player ' + player.nickname">
            Are you sure to delete player <strong style="color:#e6a817;">{{ player.nickname }}</strong>?<br />
            <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
            <strong style="color:#f56c6c;">This cannot be undone!</strong>
            <template #footer>
                <el-button @click="showDeleteDialog = false">Cancel</el-button>
                <el-button type="danger" @click="deletePlayer">Yes, Delete!</el-button>
            </template>
        </el-dialog>

        <!-- Edit Tickets Dialog -->
        <el-dialog append-to-body v-model="showTicketsDialog" :title="'Edit Tickets of ' + player.nickname">
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                <div style="flex:0 0 50%;"><strong style="color:#67c23a;">Step 2:</strong></div>
                <div style="flex:0 0 25%;"><el-input v-model="s2" type="number" /></div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                <div style="flex:0 0 50%;"><strong style="color:#409eff;">Step 3:</strong></div>
                <div style="flex:0 0 25%;"><el-input v-model="s3" type="number" /></div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                <div style="flex:0 0 50%;"><strong style="color:#e6a817;">Step 4:</strong></div>
                <div style="flex:0 0 25%;"><el-input v-model="s4" type="number" /></div>
            </div>
            <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
            <template #footer>
                <el-button @click="revertTickets">Cancel</el-button>
                <el-button type="danger" @click="editTickets">Ok</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
    props: ['player', 'season', 'stats', 'awards'],
    data() {
        return {
            ticket_fail: false, ticket_success: false, delete_fail: false,
            s2: 0, s3: 0, s4: 0,
            gameTypes: [{ text: 'Step 1', value: 1 },{ text: 'Step 2', value: 2 },{ text: 'Step 3', value: 3 },{ text: 'Step 4', value: 4 },{ text: 'All', value: 0 }],
            alltime: false, season_select: null, type: 0, page: 1, total: null, games: null,
            seasonS1: true, seasonBarS1: true, seasonPieS1: false,
            seasonS2: false, seasonBarS2: true, seasonPieS2: false,
            seasonS3: false, seasonBarS3: true, seasonPieS3: false,
            alltimeS1: true, alltimeBarS1: true, alltimePieS1: false,
            alltimeS2: false, alltimeBarS2: true, alltimePieS2: false,
            alltimeS3: false, alltimeBarS3: true, alltimePieS3: false,
            alltimeS4: false, alltimeBarS4: true, alltimePieS4: false,
            arole: window.arole, reason: '',
            showDeleteDialog: false, showTicketsDialog: false,
        };
    },
    computed: {
        seasons() {
            return this.stats.seasons.map(s => ({ value: s, text: 'Season ' + s }));
        },
        gameKeys() {
            return this.games && this.games.length ? Object.keys(this.games[0]) : [];
        },
    },
    mounted() {
        if (this.stats.season.places[0]) this.showSeasonStep(1);
        else if (this.stats.season.places[1]) this.showSeasonStep(2);
        else if (this.stats.season.places[2]) this.showSeasonStep(3);
        if (this.stats.alltime.places[0]) this.showAlltimeStep(1);
        else if (this.stats.alltime.places[1]) this.showAlltimeStep(2);
        else if (this.stats.alltime.places[2]) this.showAlltimeStep(3);
        else if (this.stats.alltime.places[3]) this.showAlltimeStep(4);
        this.season_select = this.season;
        this.s2 = this.player.s2_tickets;
        this.s3 = this.player.s3_tickets;
        this.s4 = this.player.s4_tickets;
        this.filter();
    },
    methods: {
        showSeasonStep(n) { this.seasonS1 = n===1; this.seasonS2 = n===2; this.seasonS3 = n===3; },
        showAlltimeStep(n) { this.alltimeS1 = n===1; this.alltimeS2 = n===2; this.alltimeS3 = n===3; this.alltimeS4 = n===4; },
        switchSeasonChartS1(item, e) { const i = this.getPlacesFormatted(this.stats.season.places[0]).indexOf(item); this.seasonBarS1 = i===0; this.seasonPieS1 = i!==0; },
        switchSeasonChartS2(item, e) { const i = this.getPlacesFormatted(this.stats.season.places[1]).indexOf(item); this.seasonBarS2 = i===0; this.seasonPieS2 = i!==0; },
        switchSeasonChartS3(item, e) { const i = this.getPlacesFormatted(this.stats.season.places[2]).indexOf(item); this.seasonBarS3 = i===0; this.seasonPieS3 = i!==0; },
        switchAlltimeChartS1(item, e) { const i = this.getPlacesFormatted(this.stats.alltime.places[0]).indexOf(item); this.alltimeBarS1 = i===0; this.alltimePieS1 = i!==0; },
        switchAlltimeChartS2(item, e) { const i = this.getPlacesFormatted(this.stats.alltime.places[1]).indexOf(item); this.alltimeBarS2 = i===0; this.alltimePieS2 = i!==0; },
        switchAlltimeChartS3(item, e) { const i = this.getPlacesFormatted(this.stats.alltime.places[2]).indexOf(item); this.alltimeBarS3 = i===0; this.alltimePieS3 = i!==0; },
        switchAlltimeChartS4(item, e) { const i = this.getPlacesFormatted(this.stats.alltime.places[3]).indexOf(item); this.alltimeBarS4 = i===0; this.alltimePieS4 = i!==0; },
        placesKeys(data) { return this.getPlacesFormatted(data).length ? Object.keys(this.getPlacesFormatted(data)[0]) : []; },
        getPlacesFormatted(stats) {
            const total = stats.reduce((sum, num) => sum + num, 0);
            return [
                stats.reduce((acc, v, i) => { acc[i+1+'.']=v; return acc; }, {}),
                stats.reduce((acc, v, i) => { acc[i+1+'.']=total > 0 ? ((v/total)*100).toFixed()+'%' : '0%'; return acc; }, {}),
            ];
        },
        formatResult(result) {
            return result.map(entry => {
                const newEntry = {...entry};
                for (let i=1;i<=10;i++) {
                    if (entry['p'+i] !== null && entry['p'+i] == this.player.nickname) {
                        newEntry['p'+i] = '<strong style="color:#409eff;">' + entry['p'+i] + '</strong>';
                    }
                }
                this.gameTypes.forEach(t => { if (t.value == entry.type) newEntry.type = t.text; });
                return newEntry;
            });
        },
        showGame(row) { window.open(window.location.origin + '/results/game/' + row.number, '_blank'); },
        filter(page=1) {
            this.page = page;
            axios.post('/results/player/' + this.player.id, { season: this.season_select, alltime: this.alltime, page: this.page, type: this.type })
                .then(response => {
                    if (response.data.success === true) { this.games = this.formatResult(response.data.result); this.total = response.data.total; }
                }, (error) => { console.log(error); });
        },
        reset() { this.season_select = this.season; this.alltime = false; this.type = 0; this.filter(); },
        editTickets() {
            if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
            this.formatTicketNum();
            axios.post('/player/tickets/' + this.player.id, { s2: this.s2, s3: this.s3, s4: this.s4, reason: this.reason })
                .then(response => {
                    if (response.data.success === true) { this.ticket_success = true; ElMessage({ message: 'Tickets successfully edited!', type: 'success' }); }
                    else { this.s2 = this.player.s2_tickets; this.s3 = this.player.s3_tickets; this.s4 = this.player.s4_tickets; this.ticket_fail = true; }
                    this.showTicketsDialog = false;
                }, (error) => { console.log(error); });
        },
        revertTickets() { this.s2 = this.player.s2_tickets; this.s3 = this.player.s3_tickets; this.s4 = this.player.s4_tickets; this.showTicketsDialog = false; },
        formatTicketNum() {
            this.s2 = Math.min(10, Math.max(0, this.s2));
            this.s3 = Math.min(10, Math.max(0, this.s3));
            this.s4 = Math.min(10, Math.max(0, this.s4));
        },
        deletePlayer() {
            if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
            axios.post('/players/delete/' + this.player.id, { reason: this.reason })
                .then(response => {
                    if (response.data.success === true) { ElMessage({ message: 'Player deleted!', type: 'success' }); window.location.href = window.location.origin + '/players'; }
                    else { this.delete_fail = true; this.showDeleteDialog = false; }
                }, (error) => { console.log(error); });
        },
    },
};
</script>
<style lang="scss" scoped>
.awards img { width: 120px; }
.el-table { cursor: pointer; }
</style>
