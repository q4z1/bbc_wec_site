<template>
  <div class="awards">
    <div style="margin-bottom:0.75rem;">
      <el-button type="primary" @click="showUpload = true"><el-icon><Plus /></el-icon>&nbsp;Upload New Award</el-button>
    </div>
    <el-table :data="award_items" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" sortable />
      <el-table-column label="Award" width="200">
        <template #default="scope">
          <img :src="scope.row.award" style="width:175px" />
        </template>
      </el-table-column>
      <el-table-column prop="title" label="Title" sortable />
      <el-table-column label="Actions" width="130">
        <template #default="scope">
          <el-icon style="color:#409eff;margin-right:0.5rem;cursor:pointer" @click="editAward(scope.row)"><Edit /></el-icon>
          <el-icon style="color:#67c23a;margin-right:0.5rem;cursor:pointer" @click="assignAward(scope.row)"><User /></el-icon>
          <el-icon style="color:#f56c6c;cursor:pointer" @click="openDelete(scope.row)"><Delete /></el-icon>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog append-to-body v-model="showUpload" title="Upload Award" width="600px">
      <award-upload-component @update-awards="updateAwards" @close-dialog="showUpload = false" />
    </el-dialog>

    <el-dialog append-to-body v-model="showAssign" title="Assign Award" width="600px">
      <award-assign-component v-if="award && showAssign" :award="award" :players="players" @close-dialog="showAssign = false" />
    </el-dialog>

    <el-dialog append-to-body v-model="showEdit" title="Edit Award" width="600px">
      <award-edit-component v-if="award && showEdit" :award="award" @update-awards="updateAwards" @close-dialog="showEdit = false" />
    </el-dialog>

    <el-dialog append-to-body v-model="showDelete" title="Delete Award" width="420px">
      <div style="text-align:center;color:#f56c6c;font-weight:600;margin-bottom:0.75rem;">Are you sure to delete this award?</div>
      <el-input v-model="reason" placeholder="Enter a reason" />
      <template #footer>
        <el-button @click="showDelete = false">Cancel</el-button>
        <el-button type="danger" @click="handleYes">Yes – Delete!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus';
import AwardAssignComponent from './AwardAssignComponent.vue';
import AwardEditComponent from './AwardEditComponent.vue';
import AwardUploadComponent from './AwardUploadComponent.vue';
export default {
  components: { AwardUploadComponent, AwardAssignComponent, AwardEditComponent },
  props: ['awards'],
  data() {
    return {
      award_items: [],
      award: null,
      players: null,
      reason: '',
      showUpload: false,
      showAssign: false,
      showEdit: false,
      showDelete: false,
    };
  },
  mounted() {
    this.updateAwards(this.awards || []);
    this.fetchPlayers();
  },
  methods: {
    fetchPlayers() {
      axios.get('/players/list').then((res) => { this.players = res.data; });
    },
    updateAwards(awards) {
      this.award_items = awards.map((item) => ({ id: item.id, award: item.filename, title: item.title }));
    },
    editAward(item) { this.award = item; this.showEdit = true; },
    assignAward(item) { this.award = item; this.showAssign = true; },
    openDelete(item) { this.award = item; this.reason = ''; this.showDelete = true; },
    handleYes() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      axios.post('/awards/delete/' + this.award.id, { reason: this.reason }).then((res) => {
        if (res.data.success) {
          this.updateAwards(res.data.awards);
          this.showDelete = false;
          ElMessage({ message: 'Award deleted!', type: 'success' });
        }
      });
    },
  },
};
</script>
<style scoped>
img { width: 175px; }
</style>
