<template>
  <div>
    <h3>Results</h3>
    <b-row class="mb-3">
      <b-col>
        <b-form-select
          :disabled="alltime"
          v-model="season_select"
          @change="filter()"
          :options="seasons"
        ></b-form-select>
      </b-col>
      <b-col>
        <b-form-select
          v-model="type"
          @change="filter()"
          :options="gameTypes"
        ></b-form-select>
      </b-col>
      <b-col>
        <b-form-checkbox class="mt-2" @change="filter()" v-model="alltime" switch>
          All-Time
        </b-form-checkbox>
      </b-col>
      <b-col class="text-right">
        <b-button variant="primary" @click="reset">Reset</b-button>
      </b-col>
    </b-row>
    <b-pagination
      v-if="result"
      v-model="page"
      :total-rows="total"
      :per-page="10"
      aria-controls="results_table"
      @page-click="paginate"
    ></b-pagination>
    <b-table
      striped
      hover
      v-if="result"
      id="results_table"
      :items="result"
      @row-clicked="showGame"
    ></b-table>
    <p v-else class="mt-4">No games found for this period.</p>
    <b-pagination
      v-if="result"
      v-model="page"
      :total-rows="total"
      :per-page="10"
      aria-controls="results_table"
      @page-click="paginate"
    ></b-pagination>
  </div>
</template>
<script>
export default {
  props: ["results", "totals", "season", "allseasons"],
  data() {
    return {
      alltime: false,
      renderTable: true,
      result: null,
      season_select: null,
      type: 1, // regular games
      page: 1, // we always start with page 1
      total: null,
      gameTypes: [
        { text: "Step 1", value: 1 },
        { text: "Step 2", value: 2 },
        { text: "Step 3", value: 3 },
        { text: "Step 4", value: 4 },
      ],
    };
  },
  computed: {
    seasons: function () {
      let l = this.allseasons.length;
      let s = [];
      for (let i = 0; i < l; i++) {
        s.push({
          value: this.allseasons[i],
          text: "Season " + this.allseasons[i],
        });
      }
      return s;
    },
  },
  mounted() {
    this.season_select = this.season;
    this.result = this.formatResult(this.results);
    this.total = this.totals;
  },
  created() {
    for (let i = 0; i < this.results.length; i++) {
      this.gameTypes.map((type) => {
        if (type.value == this.results[i].type)
          this.results[i].type = type.text;
      });
    }
  },
  methods: {
    formatResult(result) {
      let results = result.map((entry) => {
        let newEntry = entry;
        let element = document.createElement("div");
        for (let i = 1; i <= 10; i++) {
          if (entry["p" + i] !== null) {
            newEntry["p" + i] = entry["p" + i];
          }
        }
        this.gameTypes.map((type) => {
          if (type.value == entry.type) entry.type = type.text;
        });
        return newEntry;
      });
      return results;
    },
    showGame(item, index, event) {
      window.location.href = "/results/game/" + item.number;
    },
    filter(page=1) {
      this.page = page;
      axios
        .post("/results", {
          season: this.season_select,
          page: this.page,
          type: this.type,
          alltime: this.alltime
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
      this.type = 1;
      this.alltime = false;
      this.season_select = this.season;
      this.filter();
    },
  },
};
</script>