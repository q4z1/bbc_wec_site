<template>
  <div>
    <h3>Deleted SB-Messages</h3>
    <template v-if="result && result.length">
      <el-pagination v-model:current-page="page" :page-size="50" :total="total"
        layout="prev, pager, next" class="mb-2" @current-change="filter" />
      <el-table :data="result" stripe style="width:100%">
        <el-table-column prop="id" label="#" width="60" />
        <el-table-column prop="fp" label="Fingerprint" />
        <el-table-column prop="nickname" label="From" />
        <el-table-column prop="message" label="Message" />
        <el-table-column prop="created_at" label="Created" />
        <el-table-column label="Action" width="70">
          <template #default="scope">
            <svg :id="'icon'+scope.row.id" @click="openUndelete(scope.row)"
              @mouseover="scope.row._hover=true" @mouseleave="scope.row._hover=false"
              :fill="scope.row._hover ? '#438c54' : '#839496'"
              xmlns="http://www.w3.org/2000/svg" width="36px" height="36px" viewBox="0 0 52 52"
              style="cursor:pointer">
              <g><path d="M41.5,20h-31C9.7,20,9,20.7,9,21.5V45c0,2.8,2.2,5,5,5h24c2.8,0,5-2.2,5-5V21.5C43,20.7,42.3,20,41.5,20z M26,46v-4c3.3,0,6-2.7,6-6s-2.7-6-6-6c-1.6,0-3.1,0.7-4.2,1.8c0.9,0.9,1.8,1.8,2.4,2.4c0.3,0.3,0.1,0.9-0.4,0.9h-7.3c-0.3,0-0.5-0.2-0.5-0.5v-7.3c0-0.4,0.5-0.7,0.9-0.4c0.5,0.5,1.3,1.3,2.1,2.1c1.9-1.8,4.4-2.9,7.1-2.9c5.5,0,10,4.5,10,10S31.5,46,26,46z"/><path d="M45.5,10H33V6c0-2.2-1.8-4-4-4h-6c-2.2,0-4,1.8-4,4v4H6.5C5.7,10,5,10.7,5,11.5v3C5,15.3,5.7,16,6.5,16h39c0.8,0,1.5-0.7,1.5-1.5v-3C47,10.7,46.3,10,45.5,10z M29,10h-6V7c0-0.6,0.4-1,1-1h4c0.6,0,1,0.4,1,1V10z"/></g>
            </svg>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination v-model:current-page="page" :page-size="50" :total="total"
        layout="prev, pager, next" class="mt-2" @current-change="filter" />
    </template>
    <p v-else style="margin-top:1.5rem;">No deleted Shoutbox Messages found.</p>

    <el-dialog append-to-body v-model="showUndelete" :title="'Undelete Post #' + undel_id + '?'" width="500px">
      <div style="font-weight:600;margin-bottom:0.5rem;">From: {{ undel_nickname }}</div>
      <div style="margin-bottom:0.75rem;">{{ undel_message }}</div>
      <el-input v-model="reason" placeholder="Enter a reason" />
      <template #footer>
        <el-button @click="showUndelete = false">Cancel</el-button>
        <el-button type="success" @click="doUndelete">Ok – Undelete!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
  data() {
    return {
      result: [],
      page: 1,
      total: 0,
      showUndelete: false,
      undel_id: 0,
      undel_message: '',
      undel_nickname: '',
      reason: '',
    };
  },
  mounted() { this.filter(); },
  methods: {
    filter(page = 1) {
      this.page = typeof page === 'number' ? page : 1;
      axios.post('/sbdel/filter', { page: this.page }).then((res) => {
        if (res.data.success) { this.result = res.data.data; this.total = res.data.total; }
      });
    },
    openUndelete(item) {
      this.undel_id = item.id;
      this.undel_message = item.message;
      this.undel_nickname = item.nickname;
      this.reason = '';
      this.showUndelete = true;
    },
    doUndelete() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      this.showUndelete = false;
      axios.post('/sbdel', { sbmsg: this.undel_id, reason: this.reason }).then((res) => {
        if (res.data.success) {
          ElMessage({ message: 'SB-Message #' + this.undel_id + ' undeleted!', type: 'success' });
          this.filter();
        } else {
          ElMessage({ message: res.data.reason || 'Error', type: 'error' });
        }
      });
    },
  },
};
</script>
