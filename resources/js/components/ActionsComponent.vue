<template>
  <div>
    <h3>Action-Log</h3>
    <b-pagination
      v-if="result"
      v-model="page"
      :total-rows="total"
      :per-page="10"
      aria-controls="actions_table"
      @page-click="paginate"
    ></b-pagination>
    <b-table responsive striped hover
      v-if="result"
      id="actions_table"
      :items="result"
    ></b-table>
    <p v-else class="mt-4">No Action-Log entries found!</p>
    <b-pagination
      v-if="result"
      v-model="page"
      :total-rows="total"
      :per-page="10"
      aria-controls="actions_table"
      @page-click="paginate"
    ></b-pagination>
  </div>
</template>
<script>
export default {
  props: ["actions", "totals", "season", "allseasons"],
  data() {
    return {
      alltime: false,
      renderTable: true,
      result: null,
      page: 1, // we always start with page 1
      total: null,
    };
  },
  mounted() {
    this.result = this.formatResult(this.actions);
    this.total = this.totals;
  },
  created() {

  },
  methods: {
    formatResult(result) {
      let actions = result;
      return actions;
    },
    filter(page=1) {
      this.page = page;
      axios
        .post("/actions", {
          page: this.page,
        })
        .then(
          (response) => {
            if (response.data.success === true) {
              this.result = this.formatResult(response.data.result);
              this.total = response.data.total;
            }
          },
          (error) => {
            console.log(error);
          }
        );
    },
    paginate(bvEvt, page) {
      bvEvt.preventDefault();
      this.filter(page);
    },
    reset() {
      this.filter();
    },
  },
};
</script>
