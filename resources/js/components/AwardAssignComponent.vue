<template>
  <el-card>
    <div v-if="award" style="text-align:center;margin-bottom:1rem;">
      <img :src="award_preview" :alt="award.title" style="max-width:175px" />
      <div style="color:var(--el-color-primary);margin-top:0.5rem;">{{ award.title }}</div>
    </div>
    <hr />
    <div v-if="assignments && assignments.length" style="margin-bottom:1rem;">
      <div v-for="player in assignments" :key="player.id" class="assign-row">
        <span>{{ player.nickname }}</span>
        <el-icon style="color:var(--el-color-success);cursor:pointer" @click="unAssign(player.id)"><CircleCheckFilled /></el-icon>
      </div>
    </div>
    <hr v-if="assignments && assignments.length" />
    <div style="margin-bottom:1rem;">
      <label class="assign-label">Player:</label>
      <el-autocomplete
        v-model="p_input"
        :fetch-suggestions="queryPlayers"
        placeholder="Type a nickname"
        style="width:100%"
        clearable
        @select="onSelectPlayer" />
    </div>
    <el-button type="primary" @click="doSubmit">Submit</el-button>
    <el-button type="danger" @click="doReset">Reset</el-button>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
  props: ['award', 'players'],
  emits: ['close-dialog'],
  data() {
    return { award_preview: null, assignments: [], ass_o: [], p_input: '' };
  },
  mounted() {
    this.award_preview = this.award.award;
    this.getAssignments();
  },
  methods: {
    getAssignments() {
      axios.get('/awards/assignments/' + this.award.id).then((res) => {
        if (res.data.success === true) {
          this.assignments = res.data.assignments || [];
          this.ass_o = [...this.assignments];
        }
      });
    },
    queryPlayers(query, cb) {
      const q = (query || '').toLowerCase();
      cb((this.players || [])
        .filter((p) => p.nickname.toLowerCase().includes(q))
        .slice(0, 20)
        .map((p) => ({ value: p.nickname, id: p.id })));
    },
    onSelectPlayer(item) {
      if (!this.assignments.some((a) => a.id === item.id)) {
        this.assignments.push({ nickname: item.value, id: item.id });
      }
      this.p_input = '';
    },
    unAssign(player_id) {
      this.assignments = this.assignments.filter((a) => a.id !== player_id);
    },
    doSubmit() {
      const data = new FormData();
      this.assignments.forEach((a) => data.append('player[]', a.id));
      axios.post('/awards/assign/' + this.award.id, data).then((res) => {
        if (res.data.success === true) {
          this.$emit('close-dialog');
          ElMessage({ message: 'Assignments saved.', type: 'success' });
        }
      });
    },
    doReset() {
      this.assignments = [...this.ass_o];
      this.p_input = '';
    },
  },
};
</script>
<style scoped>
.assign-label {
  display: block;
  margin-bottom: 0.35rem;
  font-size: 0.875rem;
  color: var(--el-text-color-regular);
}

.assign-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.35rem 0;
  border-bottom: 1px solid var(--el-border-color-lighter);
  font-size: 0.9em;
}
</style>
