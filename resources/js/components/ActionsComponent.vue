<template>
  <div>
    <h3>Action-Log</h3>
    <template v-if="result && result.length">
      <el-pagination
        v-model:current-page="page"
        :page-size="50"
        :total="total"
        layout="prev, pager, next"
        @current-change="filter"
        class="mb-2"
      />
      <el-table :data="result" stripe border style="width:100%">
        <el-table-column v-for="col in columns" :key="col" :prop="col" :label="col" />
      </el-table>
      <el-pagination
        v-model:current-page="page"
        :page-size="50"
        :total="total"
        layout="prev, pager, next"
        class="mt-2"
        @current-change="filter"
      />
    </template>
    <p v-else class="mt-4">No Action-Log entries found!</p>
  </div>
</template>
<script>
export default {
  props: ['actions', 'totals'],
  data() {
    return {
      result: null,
      page: 1,
      total: 0,
    };
  },
  computed: {
    columns() {
      if (!this.result || !this.result.length) return [];
      return Object.keys(this.result[0]);
    },
  },
  mounted() {
    this.result = this.actions;
    this.total = this.totals;
  },
  methods: {
    filter(page = 1) {
      this.page = page;
      axios.post('/actions', { page: this.page }).then((res) => {
        if (res.data.success === true) {
          this.result = res.data.result;
          this.total = res.data.total;
        }
      });
    },
  },
};
</script>
