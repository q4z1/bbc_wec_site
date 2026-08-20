<template>
  <div>
    <el-card>
      <template #header><strong>Game Upload</strong></template>

      <div class="form-item">
        <label>Game Type:</label>
        <el-select v-model="form.gametype" style="width:100%">
          <el-option v-for="t in types" :key="t.value" :label="t.text" :value="t.value" />
        </el-select>
      </div>
      <div class="form-item">
        <label>Log-URL:</label>
        <el-input v-model="form.loglink" placeholder="https://pokerth.net/gamelog?pdb=1234567890abcdef&game_id=1" />
      </div>
      <div class="form-item">
        <label>Game-Number:</label>
        <el-input v-model="form.gameno" placeholder="123456" />
      </div>
      <div class="form-item">
        <label>Date/Time:</label>
        <div class="datetime-row">
          <el-date-picker v-model="form.date" type="date" value-format="YYYY-MM-DD" style="width:100%" />
          <el-time-picker v-model="form.time" value-format="HH:mm:ss" style="width:100%" />
        </div>
      </div>

      <el-button type="primary" :loading="busy" @click="onSubmit">Submit</el-button>
      <el-button type="danger" @click="onReset">Reset</el-button>
    </el-card>

    <el-dialog append-to-body v-model="showPreview" title="Data correct?" width="720px" @close="hideModal">
      <h4 style="color:var(--el-color-success)">Game #{{ form.gameno }} &ndash; {{ game_type }}</h4>
      <h5 style="color:var(--el-color-primary)">{{ prettyDateTime }}</h5>

      <el-table :data="gameOverview" stripe style="width:100%">
        <el-table-column prop="pos" label="Pos" width="70" />
        <el-table-column prop="player" label="Player" min-width="150" />
        <el-table-column prop="hand" label="Hand" width="80" />
        <el-table-column label="Eliminated by/Wins with" min-width="240">
          <!-- Kartensymbole kommen als HTML aus der Log-Auswertung. -->
          <template #default="scope"><span v-html="scope.row.html"></span></template>
        </el-table-column>
      </el-table>

      <template #footer>
        <el-button @click="hideModal">Cancel</el-button>
        <el-button type="success" :loading="busy" @click="onSubmit">Ok &ndash; Upload!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
  props: ['last'],
  data() {
    return {
      form: {
        loglink: '',
        gametype: 1,
        gameno: null,
        // Erster Durchlauf holt nur die Vorschau; der zweite laedt wirklich hoch.
        preview: true,
        date: new Date().toISOString().slice(0, 10),
        time: '22:00:00',
      },
      game_type: '',
      types: [{ text: 'Regular', value: 1 }, { text: 'Monthly', value: 5 }, { text: 'Yearly', value: 6 }],
      game: null,
      showPreview: false,
      busy: false,
    };
  },
  computed: {
    gameOverview() {
      if (this.game === null) return [];
      const overview = [];
      for (let i = 0; i < this.game[0].length; i++) {
        overview.push({
          pos: this.game[2][i],
          player: this.game[1][i],
          hand: this.game[3][i],
          html: this.game[7][i][0],
        });
      }
      return overview;
    },
    prettyDateTime() {
      const d = new Date(this.form.date + ' ' + this.form.time);
      return isNaN(d) ? this.form.date + ' ' + this.form.time : d.toLocaleString();
    },
  },
  mounted() {
    if (!isNaN(this.last)) this.form.gameno = this.last;
  },
  methods: {
    onSubmit() {
      this.busy = true;
      this.showPreview = false;
      axios.post('/upload/game', this.form, {
        headers: { 'Content-Type': 'application/json' },
      }).then((res) => {
        this.busy = false;
        if (res.data.status) {
          if (this.form.preview) {
            this.game = res.data.msg;
            const t = this.types.find((type) => type.value == this.form.gametype);
            this.game_type = t ? t.text : '';
            this.showPreview = true;
          } else {
            ElMessage({ message: 'Game succesfully uploaded!', type: 'success' });
            setTimeout(() => { window.location.href = '/results/game/' + this.form.gameno; }, 1500);
          }
          this.form.preview = !this.form.preview;
        } else {
          ElMessage({ message: res.data.msg, type: 'error' });
          this.resetForm();
        }
      }).catch(() => {
        this.busy = false;
        this.game = null;
        this.form.preview = true;
        ElMessage({ message: 'Game upload failed!', type: 'error' });
      });
    },
    resetForm() {
      this.form.loglink = '';
      this.form.gametype = 1;
      this.form.gameno = null;
      this.form.preview = true;
      this.game = null;
    },
    onReset() {
      this.resetForm();
    },
    hideModal() {
      this.showPreview = false;
      // Zurueck in den Vorschau-Modus, damit ein erneutes Submit nicht direkt
      // hochlaedt.
      this.form.preview = true;
    },
  },
};
</script>
<style scoped>
.form-item { margin-bottom: 1rem; }
.form-item > label {
  display: block;
  margin-bottom: 0.35rem;
  font-weight: 600;
}

.datetime-row {
  display: flex;
  gap: 0.5rem;
}
</style>
