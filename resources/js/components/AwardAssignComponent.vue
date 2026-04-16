<template>
  <el-card>
    <div v-if="award" class="text-center mb-3">
      <img :src="award_preview" :alt="award.title" style="max-width:175px" />
      <div class="text-primary mt-2">{{ award.title }}</div>
    </div>
    <hr />
    <div v-if="assignments && assignments.length" class="mb-3">
      <div v-for="(player, key) in assignments" :key="key" class="d-flex justify-content-between align-items-center py-1 border-bottom">
        <span>{{ player.nickname }}</span>
        <el-icon class="text-success" style="cursor:pointer" @click="unAssign(player.id)"><CircleCheckFilled /></el-icon>
      </div>
    </div>
    <hr />
    <div class="mb-3">
      <label class="form-label">Player:</label>
      <el-autocomplete v-model="p_input" :fetch-suggestions="queryPlayers"
        placeholder="Type a nickname" @select="onSelectPlayer" style="width:100%" clearable />
    </div>
    <el-input v-model="reason" placeholder="Enter a reason" class="mb-3" />
    <el-button type="primary" @click="doSubmit">Submit</el-button>
    <el-button type="danger" @click="doReset">Reset</el-button>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
  props: ['award', 'players'],
  emits: ['close-dialog'],
  data() {
    return { award_preview: null, assignments: [], ass_o: [], p_input: '', reason: '' };
  },
  mounted() {
    this.award_preview = this.award.award;
    this.getAssignments();
  },
  methods: {
    getAssignments() {
      axios.get('/awards/assignments/' + this.award.id).then((res) => {
        if (res.data.success) { this.ass_o = this.assignments = res.data.assignments; }
      });
    },
    queryPlayers(query, cb) {
      const results = (this.players || [])
        .filter((p) => p.nickname.toLowerCase().includes((query || '').toLowerCase()))
        .map((p) => ({ value: p.nickname, id: p.id }));
      cb(results);
    },
    onSelectPlayer(item) {
      const exists = this.assignments.some((a) => a.id === item.id);
      if (!exists) this.assignments.push({ nickname: item.value, id: item.id });
      this.p_input = '';
    },
    unAssign(player_id) {
      this.assignments = this.assignments.filter((a) => a.id !== player_id);
    },
    doSubmit() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      this.assignments.forEach((a) => data.append('player[]', a.id));
      data.append('reason', this.reason);
      axios.post('/awards/assign/' + this.award.id, data).then((res) => {
        if (res.data.success) this.$emit('close-dialog');
      });
    },
    doReset() { this.assignments = [...this.ass_o]; },
  },
};
</script>
