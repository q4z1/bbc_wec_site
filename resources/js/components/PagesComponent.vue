<template>
  <div>
    <b-row>
      <b-col><h3>Pages</h3></b-col>
      <b-col class="text-right text-primary h1">
        <span v-b-tooltip.hover title="Add Page">
          <b-icon-plus
            @click="newPage"
          ></b-icon-plus>
        </span>
      </b-col>
    </b-row>
    <b-card v-if="npage">
      <page-edit-component
        v-if="npage"
        :page="npage"
        @close-me="close"
      ></page-edit-component>
    </b-card>
    <b-row class="mb-3">
      <b-col>
        <b-table
          v-if="rpages && rpages.length"
          striped
          hover
          :items="rpages"
          :fields="fields"
        >
          <template #cell(active)="data">
            <div v-if="data.item.active == 1">yes</div>
            <div v-else>no</div>
          </template>
          <template #cell(actions)="data">
            <b-row>
              <b-col class="text-right">
                <b-button
                  size="sm"
                  variant="info"
                  v-b-tooltip.hover
                  title="Show Page"
                  @click="toggleDetails(data, false)"
                  ><b-icon-eye-fill></b-icon-eye-fill
                ></b-button>
                <b-button
                  size="sm"
                  variant="primary"
                  v-b-tooltip.hover
                  title="Edit Page"
                  @click="toggleDetails(data, true)"
                  ><b-icon-pencil-fill></b-icon-pencil-fill
                ></b-button>
                <b-button
                  v-if="data.item.slug !== 'home'"
                  size="sm"
                  variant="danger"
                  v-b-tooltip.hover
                  title="Delete Page"
                  @click="deletePage(data, true)"
                  ><b-icon-trash-fill></b-icon-trash-fill
                ></b-button>
              </b-col>
            </b-row>
          </template>
          <template #row-details="data">
            <b-card>
              <page-edit-component
                v-if="edit"
                :page="data.item"
                @close-me="close"
                :data="data"
              ></page-edit-component>
              <page-component v-if="!edit" :page="data.item"></page-component>
            </b-card>
          </template>
        </b-table>
      </b-col>
    </b-row>
  </div>
</template>
<script>
import PageEditComponent from "./PageEditComponent.vue";
export default {
  props: ["pages"],
  components: {
    PageEditComponent,
  },
  data() {
    return {
      pedit: null,
      edit: false,
      npage: null,
      rpages: null,
      fields: [
        {
          key: "id",
          sortable: false,
        },
        {
          key: "title",
          sortable: false,
        },
        {
          key: "slug",
          sortable: false,
        },
        {
          key: "order",
          sortable: false,
        },
        {
          key: "active",
          label: "Published",
          sortable: false,
        },
        {
          key: "actions",
          label: "",
          sortable: false,
        },
      ],
    };
  },
  mounted() {
    this.rpages = JSON.parse(this.pages);
  },
  methods: {
    toggleDetails(data, edit) {
      let show = data.detailsShowing;
      if (show && edit != this.edit) {
        this.edit = edit;
      } else if (!show && edit != this.edit) {
        this.edit = edit;
        data.toggleDetails();
      } else if (edit == this.edit) {
        data.toggleDetails();
      }
    },
    newPage() {
      this.npage = {
        slug: "",
        title: "",
        markdown: "",
        order: 0,
        active: 0,
      };
    },
    close(data) {
      this.rpages = JSON.parse(this.pages);
      this.npage = null;
      if (typeof data !== "undefined") data.toggleDetails();
    },
    deletePage(data, edit) {
      axios
        .delete("/page/" + data.item.id)
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
  },
};
</script>