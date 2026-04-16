<template>
  <div class="container-fluid players">
    <div class="card">
      <div class="card-body">
        <el-row :gutter="20" class="mb-3" align="middle">
          <el-col :lg="4" :md="6" :sm="8" :xs="12">
            <el-input v-model="search" placeholder="Username" @input="loadData" clearable />
          </el-col>
          <el-col :lg="4" :md="6" :sm="8" :xs="12" v-if="arole === 'a' || arole === 's'">
            <el-switch v-model="showNew" active-text="New Regs" @change="loadData" />
          </el-col>
        </el-row>
        <div v-loading="loading">
          <el-table :data="data" stripe border style="width:100%"
            :default-sort="{ prop: 'nickname', order: 'ascending' }"
            @sort-change="handleSortChange"
            @row-click="handleRowClick">
            <el-table-column prop="nickname" label="Nickname" sortable="custom" />
            <el-table-column prop="s2_tickets" label="Step2 Tickets" sortable="custom" />
            <el-table-column prop="s3_tickets" label="Step3 Tickets" sortable="custom" />
            <el-table-column prop="s4_tickets" label="Step4 Tickets" sortable="custom" />
          </el-table>
          <el-pagination
            v-model:current-page="page"
            v-model:page-size="pageSize"
            :page-sizes="[10, 25, 50]"
            :total="total"
            layout="sizes, prev, pager, next"
            class="mt-3"
            @current-change="loadData"
            @size-change="loadData"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      data: [],
      loading: false,
      total: 0,
      page: 1,
      pageSize: 10,
      search: '',
      showNew: false,
      sortProp: 'nickname',
      sortOrder: 'ascending',
      arole: window.arole,
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      this.loading = true;
      const res = await axios.post('/players', {
        page: this.page, pageSize: this.pageSize,
        filters: { value: this.search, new: this.showNew },
        sort: { prop: this.sortProp, order: this.sortOrder },
      });
      this.data = res.data.data;
      this.total = res.data.total;
      this.loading = false;
    },
    handleSortChange({ prop, order }) {
      this.sortProp = prop; this.sortOrder = order; this.loadData();
    },
    handleRowClick(row) {
      window.location.href = window.location.origin + '/player/' + encodeURIComponent(row.nickname);
    },
  },
};
</script>
