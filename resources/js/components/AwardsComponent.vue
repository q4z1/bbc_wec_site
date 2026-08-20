<template>
  <div class="awards">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
      <h3 style="margin:0;">Awards</h3>
      <el-button type="primary" @click="showUpload = true"><el-icon><Plus /></el-icon>&nbsp;Upload New Award</el-button>
    </div>

    <el-table :data="award_items" style="width:100%">
      <el-table-column prop="id" label="ID" width="80" sortable />
      <el-table-column label="Award" width="200">
        <template #default="scope">
          <img :src="scope.row.award" :alt="scope.row.title" class="award-img" />
        </template>
      </el-table-column>
      <el-table-column prop="title" label="Title" sortable />
      <el-table-column label="Actions" width="130">
        <template #default="scope">
          <el-icon class="award-action" style="color:var(--el-color-warning)" @click="editAward(scope.row)"><EditPen /></el-icon>
          <el-icon class="award-action" style="color:var(--el-color-primary)" @click="assignAward(scope.row)"><User /></el-icon>
          <el-icon class="award-action" style="color:var(--el-color-danger)" @click="openDelete(scope.row)"><Delete /></el-icon>
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
      <div v-if="award" style="text-align:center;">
        <img :src="award.award" :alt="award.title" class="award-img" />
        <div style="margin-top:0.5rem;">{{ award.title }}</div>
      </div>
      <hr />
      <div style="text-align:center;color:var(--el-color-danger);font-weight:600;">
        Are you sure to delete this award?
      </div>
      <template #footer>
        <el-button @click="showDelete = false">No</el-button>
        <el-button type="danger" @click="handleYes">Yes &ndash; Delete!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';
import AwardAssignComponent from './AwardAssignComponent.vue';
import AwardEditComponent from './AwardEditComponent.vue';
import AwardUploadComponent from './AwardUploadComponent.vue';

export default {
  components: { AwardUploadComponent, AwardAssignComponent, AwardEditComponent },
  props: ['awards', 'players'],
  data() {
    return {
      award_items: [],
      award: null,
      showUpload: false,
      showAssign: false,
      showEdit: false,
      showDelete: false,
    };
  },
  mounted() {
    this.updateAwards(this.awards || []);
  },
  methods: {
    updateAwards(awards) {
      this.award_items = (awards || []).map((item) => ({
        id: item.id,
        award: item.filename,
        title: item.title,
      }));
    },
    editAward(item) { this.award = item; this.showEdit = true; },
    assignAward(item) { this.award = item; this.showAssign = true; },
    openDelete(item) { this.award = item; this.showDelete = true; },
    handleYes() {
      axios.get('/awards/delete/' + this.award.id).then((res) => {
        if (res.data.success === true) {
          this.updateAwards(res.data.awards);
          this.showDelete = false;
          ElMessage({ message: 'Award deleted!', type: 'success' });
        } else {
          ElMessage({ message: 'Deleting the award failed.', type: 'error' });
        }
      });
    },
  },
};
</script>
<style scoped>
.award-img { width: 175px; }

.award-action {
  cursor: pointer;
  margin-right: 0.5rem;
  filter: brightness(80%);
}
.award-action:hover { filter: brightness(100%); }
.award-action:last-child { margin-right: 0; }
</style>
