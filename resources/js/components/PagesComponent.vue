<template>
  <div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
      <h3 style="margin-bottom:0;">Pages</h3>
      <el-tooltip content="Add Page" placement="top">
        <el-icon style="color:#409eff;font-size:1.5rem;cursor:pointer" @click="newPage"><Plus /></el-icon>
      </el-tooltip>
    </div>

    <el-card v-if="npage" style="margin-bottom:0.75rem;">
      <page-edit-component :page="npage" @close-me="close" />
    </el-card>

    <el-table v-if="rpages && rpages.length" :data="rpages" stripe style="width:100%" row-key="id" :expand-row-keys="expandedKeys">
      <el-table-column prop="id" label="ID" width="60" />
      <el-table-column prop="title" label="Title" />
      <el-table-column prop="slug" label="Slug" />
      <el-table-column prop="order" label="Order" width="70" />
      <el-table-column label="Published" width="100">
        <template #default="scope">{{ scope.row.active == 1 ? 'yes' : 'no' }}</template>
      </el-table-column>
      <el-table-column label="" width="110">
        <template #default="scope">
          <div style="display:flex;gap:0.25rem;margin-right:0.5rem;">
            <el-button size="small" type="info" @click="toggleView(scope.row, false)"><el-icon><View /></el-icon></el-button>
            <el-button size="small" type="primary" @click="toggleView(scope.row, true)"><el-icon><Edit /></el-icon></el-button>
            <el-button v-if="scope.row.slug !== 'home' && arole === 's'" size="small" type="danger" @click="delConfirm(scope.row)"><el-icon><Delete /></el-icon></el-button>
          </div>
        </template>
      </el-table-column>
      <el-table-column type="expand" width="1" class-name="hidden-expand">
        <template #default="scope">
          <el-card>
            <page-edit-component v-if="editMode && expandedId === scope.row.id" :page="scope.row" @close-me="closeRow" />
            <page-component v-else-if="!editMode && expandedId === scope.row.id" :page="scope.row" />
          </el-card>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog append-to-body v-model="showDelete" :title="'Delete Page ' + (dpage ? dpage.title : '') + '?'" width="420px">
      Are you sure to delete <strong style="color:#e6a817;">{{ dpage && dpage.title }}</strong>?
      <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
      <template #footer>
        <el-button @click="showDelete = false">Cancel</el-button>
        <el-button type="danger" @click="doDeletePage">Yes, Delete!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus';
import PageEditComponent from './PageEditComponent.vue';
import PageComponent from './PageComponent.vue';
export default {
  components: { PageEditComponent, PageComponent },
  props: ['pages'],
  data() {
    return {
      rpages: [],
      npage: null,
      dpage: null,
      reason: '',
      showDelete: false,
      expandedId: null,
      expandedKeys: [],
      editMode: false,
      arole: window.arole,
    };
  },
  mounted() {
    this.rpages = JSON.parse(this.pages || '[]');
  },
  methods: {
    toggleView(row, edit) {
      const isOpen = this.expandedId === row.id && this.editMode === edit;
      if (isOpen) {
        this.expandedId = null;
        this.expandedKeys = [];
      } else {
        this.expandedId = row.id;
        this.editMode = edit;
        this.expandedKeys = [row.id];
      }
    },
    newPage() {
      this.npage = { slug: '', title: '', markdown: '', order: 0, active: 0 };
    },
    close() { window.location.reload(); },
    closeRow() { window.location.reload(); },
    delConfirm(p) { this.dpage = p; this.reason = ''; this.showDelete = true; },
    doDeletePage() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const fd = new FormData();
      fd.append('reason', this.reason);
      axios.post('/page/delete/' + this.dpage.id, fd).then((res) => {
        if (res.data.status === true) window.location.reload();
      });
    },
  },
};
</script>

<style scoped>
:deep(.hidden-expand .cell),
:deep(.el-table__expand-icon) {
    display: none !important;
}
:deep(.hidden-expand) {
    padding: 0 !important;
    width: 0 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}
</style>
