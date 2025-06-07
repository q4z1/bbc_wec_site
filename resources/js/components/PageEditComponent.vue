<template>
  <div>
    <div class="page" v-if="pedit">
      <b-row>
        <b-col>
          <b-row class="mb-2">
            <b-col sm="4" v-if="pedit.slug !== 'home'">
              <b-form-input
                id="title"
                placeholder="Title"
                v-model="pedit.title"
                size="sm"
              ></b-form-input>
            </b-col>
            <b-col sm="3" v-if="pedit.slug !== 'home'">
              <b-form-input
                id="slug"
                placeholder="Slug"
                v-model="pedit.slug"
                size="sm"
              ></b-form-input>
            </b-col>
            <b-col sm="5" v-if="pedit.slug !== 'home'">
              <b-row>
                <b-col sm="7">
                  <b-input-group>
                    <b-input-group-prepend>
                      <b-icon-sort-numeric-up class="h3 text-default"></b-icon-sort-numeric-up>
                    </b-input-group-prepend>
                    <b-form-input
                      id="active"
                      v-model="pedit.order"
                      type="number"
                      size="sm"
                      style="max-width: 50px"
                    ></b-form-input>
                  </b-input-group>
                </b-col>
                <b-col sm="3">
                  <div v-b-tooltip.hover
                      title="Published">
                    <b-icon-eye-slash-fill
                      v-if="pedit.active == 0"
                      class="text-default h3"
                      @click="pedit.active = 1"
                    ></b-icon-eye-slash-fill>
                    <b-icon-eye-fill
                      v-if="pedit.active == 1"
                      class="text-primary h3"
                      @click="pedit.active = 0"
                    ></b-icon-eye-fill>
                  </div>
                </b-col>
              </b-row>
            </b-col>
          </b-row>
        </b-col>
        <b-col>
          <b-row>
            <b-col
              ><strong>Title:</strong
              ><span class="ml-2" style="font-size: larger">{{
                pedit.title
              }}</span></b-col
            >
            <b-col
              ><strong>Slug:</strong
              ><a
                class="ml-2"
                v-b-tooltip.hover
                :title="'https://bbc.pokerth.net/page/' + pedit.slug"
                :href="'/page/' + pedit.slug"
                target="_blank"
                >{{ "/page/" + pedit.slug }}</a
              ></b-col
            >
            <b-col class="text-right">
              <div>
                <b-button
                  size="sm"
                  @click="saveConfirm"
                  v-b-tooltip.hover
                  title="Save & Close"
                  variant="success"
                  >Save</b-button
                >
                <b-button
                  size="sm"
                  @click="reset"
                  variant="warning"
                  v-b-tooltip.hover
                  title="Reset & Close"
                  ><b-icon-x-circle-fill></b-icon-x-circle-fill
                ></b-button>
              </div>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <b-row>
        <b-col>
          <b-form-textarea
            id="textarea"
            v-model="pedit.markdown"
            placeholder="Enter something..."
            rows="24"
            max-rows="24"
          ></b-form-textarea>
        </b-col>
        <b-col>
          <vue-markdown
            class="border rounded p-2"
            :source="pedit.markdown"
            :html="true"
          ></vue-markdown>
        </b-col>
      </b-row>
    </div>
    <b-modal ref="save" id="save" :title="'Save Page ' + pedit.title + '?'" ok-disabled hide-footer>
        Are you sure to save Page <strong class="text-warning">{{ pedit.title }}</strong>?<br />
        <b-row class="mt-3">
            <div class="col-md-12"><b-form-input v-model="reason" placeholder="Enter a reason"></b-form-input></div>
        </b-row> 
        <b-button class="mt-3" variant="outline-info" block @click="$refs['delete'].hide()">Cancel</b-button>
        <b-button class="mt-2" variant="outline-danger" block @click="save">Yes, Save!</b-button>
    </b-modal>
  </div>
</template>
<script>
import VueMarkdown from "vue-markdown";
export default {
  props: ["page", "data"],
  components: {
    VueMarkdown,
  },
  data() {
    return {
      pedit: null,
      reason: "",
    };
  },
  mounted() {
    this.pedit = this.page;
  },
  methods: {
    saveConfirm(){
      this.$nextTick(() => {
          this.$refs['save'].show()
      })
    },
    save() {
      if(this.reason === ""){
        this.$bvToast.toast("Please enter a reason!", {
                      title: 'Error!',
                      autoHideDelay: 3000,
                      appendToast: true,
                      variant: 'danger',
                  })
        return false;
      }
      let data = new FormData();
      data.append("order", this.pedit.order);
      data.append("active", this.pedit.active);
      data.append("markdown", this.pedit.markdown);
      data.append("title", this.pedit.title);
      data.append("slug", this.pedit.slug);
      data.append("reason", this.reason);
      if (typeof this.pedit.id !== "undefined")
        data.append("id", this.pedit.id);
      console.log("save", data);
      axios.post("/page", data)
        .then(function (response) {
          if (response.data.status && response.data.status === true) {
            window.location.reload();
          } else {
            console.log(response.data.msg);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response);
        });
    },
    reset() {
      this.$emit("close-me", this.data);
    },
  },
};
</script>
<style lang="scss" scoped>
texarea, div.border.rounded{
  height: 590px;
}
div.border.rounded{
  overflow-y: scroll;
}
</style>