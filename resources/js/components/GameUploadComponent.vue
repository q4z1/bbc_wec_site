<template>
    <div>
        <el-card style="margin-bottom:0.5rem;">
            <template #header><strong>Game Upload</strong></template>
            <div v-if="show">
                <div style="margin-bottom:0.75rem;">
                    <label class="form-label">Game Type:</label>
                    <el-select v-model="form.gametype" style="width:100%">
                        <el-option v-for="t in types" :key="t.value" :label="t.text" :value="t.value" />
                    </el-select>
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label class="form-label">Log-URL:</label>
                    <el-input v-model="form.loglink" placeholder="https://pokerth.net/gamelog?pdb=..." />
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label class="form-label">Game-Number:</label>
                    <el-input v-model="form.gameno" placeholder="123456" />
                </div>
                <div style="margin-bottom:0.75rem;">
                    <label class="form-label">Date/Time:</label>
                    <div style="display:flex;gap:0.5rem;">
                        <div style="flex:1;">
                            <el-date-picker v-model="form.date" type="date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" style="width:100%" />
                        </div>
                        <div style="flex:1;">
                            <el-time-picker v-model="form.time" format="HH:mm:ss" value-format="HH:mm:ss" style="width:100%" />
                        </div>
                    </div>
                </div>
                <el-button type="primary" @click="onSubmit">Submit</el-button>
                <el-button type="danger" @click="onReset">Reset</el-button>
            </div>
        </el-card>

        <el-dialog append-to-body v-model="showPreview" title="Data correct?">
            <h4 style="color:#67c23a;">Game #{{ form.gameno }} - {{ game_type }}</h4>
            <h5 style="color:#409eff;" v-if="form.date && form.time">{{ new Date(form.date + 'T' + form.time).toLocaleString() }}</h5>
            <el-table v-if="gameOverview" :data="gameOverview" stripe style="width:100%">
                <el-table-column prop="Pos" label="Pos" width="60" />
                <el-table-column prop="Player" label="Player" />
                <el-table-column prop="Hand" label="Hand" />
                <el-table-column label="Eliminated by/Wins with">
                    <template #default="{ row }">
                        <span v-html="row.html"></span>
                    </template>
                </el-table-column>
            </el-table>
            <template #footer>
                <el-button type="success" @click="onSubmit">Ok - Upload!</el-button>
                <el-button @click="hideModal">Cancel</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
    props: ['last'],
    data() {
        return {
            form: {
                loglink: '',
                gametype: 1,
                gameno: null,
                preview: true,
                date: new Date().toISOString().slice(0, 10),
                time: '22:00:00',
            },
            game_type: '',
            types: [{ text: 'Step 1', value: 1 }, { text: 'Step 2', value: 2 }, { text: 'Step 3', value: 3 }, { text: 'Step 4', value: 4 }],
            game: null,
            show: true,
            showPreview: false,
        };
    },
    computed: {
        gameOverview() {
            if (!this.game) return null;
            return this.game[0].map((_, i) => ({
                'Pos': this.game[2][i],
                'Player': this.game[1][i],
                'Hand': this.game[3][i],
                html: this.game[7][i][0],
            }));
        },
    },
    mounted() {
        if (!isNaN(this.last)) this.form.gameno = this.last;
    },
    methods: {
        onSubmit() {
            this.showPreview = false;
            axios({ method: 'post', url: '/upload/game', data: this.form, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.status) {
                        if (this.form.preview) {
                            this.game = response.data.msg;
                            this.types.forEach(t => { if (t.value == this.form.gametype) this.game_type = t.text; });
                            this.showPreview = true;
                        } else {
                            ElMessage({ message: 'Game successfully uploaded!', type: 'success' });
                            window.setTimeout(() => { window.location.href = window.location.origin + '/results/game/' + this.form.gameno; }, 1500);
                        }
                        this.form.preview = !this.form.preview;
                    } else {
                        ElMessage({ message: response.data.msg, type: 'error' });
                        this.form.loglink = '';
                        this.form.gametype = 1;
                        this.form.gameno = null;
                        this.form.preview = true;
                        this.game = null;
                    }
                })
                .catch(() => { this.game = null; this.form.preview = true; ElMessage({ message: 'Game upload failed!', type: 'error' }); });
        },
        onReset() {
            this.form.loglink = '';
            this.form.gametype = 1;
            this.form.gameno = null;
            this.form.preview = true;
            this.game = null;
            this.show = false;
            this.$nextTick(() => { this.show = true; });
        },
        hideModal() {
            this.showPreview = false;
            this.form.preview = true;
        },
    },
};
</script>
