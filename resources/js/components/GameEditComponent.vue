<template>
  <el-card class="game-edit">
    <template #header><strong>Edit Game</strong></template>

    <div class="edit-grid">
      <div class="edit-col">
        <div class="form-item">
          <label>Game-Number:</label>
          <el-input v-model="form.gameno" />
        </div>
        <div class="form-item">
          <label>Game Type:</label>
          <el-select v-model="form.gametype" style="width:100%">
            <el-option v-for="t in types" :key="t.value" :label="t.text" :value="t.value" />
          </el-select>
        </div>
        <div class="form-item">
          <label>Date/Time:</label>
          <div class="datetime-row">
            <el-date-picker v-model="form.date" type="date" value-format="YYYY-MM-DD" style="width:100%" />
            <el-time-picker v-model="form.time" value-format="HH:mm:ss" style="width:100%" />
          </div>
        </div>
      </div>

      <div class="edit-col">
        <div class="form-item">
          <label>Player:</label>
          <div v-for="(player, index) in form.player" :key="index" class="player-row">
            <span class="player-no">{{ index + 1 }}.</span>
            <el-input v-model="form.player[index]" size="small" :disabled="form.disco_dummy[index] === 1" />
            <el-tooltip content="Disco Dummy" placement="top">
              <el-checkbox :model-value="form.disco_dummy[index] === 1"
                           @change="(checked) => discoDummy(index, checked)" />
            </el-tooltip>
          </div>
        </div>
      </div>
    </div>

    <div class="edit-actions">
      <el-button type="primary" @click="$emit('back')">Cancel</el-button>
      <el-button type="success" @click="saveGame">Save</el-button>
    </div>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
  props: ['game'],
  emits: ['back', 'update'],
  data() {
    return {
      types: [{ text: 'Regular', value: 1 }, { text: 'Monthly', value: 5 }, { text: 'Yearly', value: 6 }],
      form: {
        gametype: this.game.type,
        gameno: this.game.number,
        date: this.game.started.slice(0, 10),
        time: this.game.started.slice(11),
        disco_dummy: [],
        player: [],
      },
    };
  },
  created() {
    for (let i = 1; i <= 10; i++) {
      const player = this.game['pos' + i];
      this.form.disco_dummy.push(player === 'disco_dummy' ? 1 : 0);
      this.form.player.push(player);
    }
  },
  methods: {
    // Frueher lief das ueber DOM-Traversal vom Checkbox-Element aus; der Index
    // reicht voellig, um Namen und Flag zu setzen.
    discoDummy(index, checked) {
      if (checked) {
        this.form.disco_dummy[index] = 1;
        this.form.player[index] = 'disco_dummy';
      } else {
        this.form.disco_dummy[index] = 0;
        const original = this.game.stats.player_list[1][index];
        this.form.player[index] = (typeof original === 'undefined') ? '' : original;
      }
    },
    saveGame() {
      axios.post('/update/game/' + this.game.number, this.form, {
        headers: { 'Content-Type': 'application/json' },
      }).then((res) => {
        if (res.data.status) {
          ElMessage({ message: res.data.msg, type: 'success' });
          this.$emit('update', this.form);
          this.$emit('back');
        } else {
          ElMessage({ message: res.data.msg, type: 'error' });
        }
      }).catch(() => {
        ElMessage({ message: 'Game saving failed!', type: 'error' });
      });
    },
  },
};
</script>
<style scoped>
.edit-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
}
.edit-col { flex: 1 1 320px; min-width: 0; }

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

.player-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.35rem;
}
.player-no {
  width: 1.75rem;
  font-weight: 600;
  text-align: right;
}

.edit-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 1rem;
}
</style>
