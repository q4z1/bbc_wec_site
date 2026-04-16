<template>
  <div class="users">
    <h5>Users</h5>
    <el-table striped :data="tusers" style="width:100%">
      <el-table-column prop="id" label="ID" width="60" sortable />
      <el-table-column prop="name" label="Name" sortable />
      <el-table-column prop="role" label="Role" sortable />
      <el-table-column label="Actions" width="100">
        <template #default="scope">
          <el-icon style="color:#e6a817;margin-right:0.5rem;cursor:pointer" @click="openEdit(scope.row.id)"><Edit /></el-icon>
          <el-icon style="color:#f56c6c;cursor:pointer" @click="openDelete(scope.row.id)"><Delete /></el-icon>
        </template>
      </el-table-column>
    </el-table>

    <!-- Delete Dialog -->
    <el-dialog append-to-body v-model="showDelete" :title="user ? 'Delete User ' + user.name : 'Delete'" width="400px">
      Are you sure to delete <strong style="color:#e6a817;">{{ user && user.name }}</strong>?
      <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
      <template #footer>
        <el-button @click="showDelete = false">Cancel</el-button>
        <el-button type="danger" @click="doDelete">Yes, Delete!</el-button>
      </template>
    </el-dialog>

    <!-- Edit Dialog -->
    <el-dialog append-to-body v-model="showEdit" :title="user ? 'Edit ' + user.name + '\'s role' : 'Edit'" width="400px">
      <el-select v-model="role" style="width:100%">
        <el-option v-for="o in roleOpts" :key="o.value" :label="o.text" :value="o.value" />
      </el-select>
      <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
      <template #footer>
        <el-button @click="showEdit = false">Cancel</el-button>
        <el-button type="primary" @click="doUpdate">Update User!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
  props: ['users'],
  data() {
    return {
      user_id: null,
      tusers: [],
      user: null,
      roles: { s: 'Super Admin', a: 'Admin', u: 'User' },
      roleOpts: [
        { value: 'u', text: 'User' },
        { value: 'a', text: 'Admin' },
        { value: 's', text: 'Super Admin' },
      ],
      role: null,
      reason: '',
      showDelete: false,
      showEdit: false,
    };
  },
  mounted() {
    this.tusers = this.formatUsers(this.users || []);
  },
  methods: {
    formatUsers(users) {
      return users.map((u) => ({ id: u.id, name: u.name, role: this.roles[u.role] }));
    },
    openEdit(id) {
      this.user_id = id;
      this.user = this.users.find((u) => u.id == id);
      this.role = this.user.role;
      this.reason = '';
      this.showEdit = true;
    },
    openDelete(id) {
      this.user_id = id;
      this.user = this.users.find((u) => u.id == id);
      this.reason = '';
      this.showDelete = true;
    },
    doUpdate() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      data.append('role', this.role); data.append('reason', this.reason);
      axios.post('/user/update/' + this.user_id, data).then((res) => {
        if (res.data.success) { this.tusers = this.formatUsers(res.data.users); this.showEdit = false; }
      });
    },
    doDelete() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      data.append('reason', this.reason);
      axios.post('/user/delete/' + this.user_id, data).then((res) => {
        if (res.data.success) {
          this.tusers = this.tusers.filter((u) => u.id != this.user_id);
          this.showDelete = false;
        }
      });
    },
  },
};
</script>
