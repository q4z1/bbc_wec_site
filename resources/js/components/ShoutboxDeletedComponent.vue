<template>
    <div class="container-fluid players">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h3>Deleted SB-Messages</h3>
                <div class="card">
                    <div class="card-body" id="vtable">
                        <b-pagination
                          v-if="result"
                          v-model="page"
                          :total-rows="total"
                          :per-page="50"
                          aria-controls="results_table"
                          @page-click="paginate"
                        ></b-pagination>
                        <b-table responsive striped hover
                          v-if="result"
                          id="results_table"
                          :items="result"
                          :fields="fields"
                          @row-clicked="showPost"
                        >
                        <template #cell(custom_column)="data">
                            <svg :id="'icon' + data.item.id" @click="undelete(data.item)" @mouseover="hover(data.item)" @mouseleave="unhover(data.item)" fill="#839496" xmlns="http://www.w3.org/2000/svg" 
                            	 width="36px" height="36px" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve">
                            <g>
                            	<path d="M41.5,20h-31C9.7,20,9,20.7,9,21.5V45c0,2.8,2.2,5,5,5h24c2.8,0,5-2.2,5-5V21.5C43,20.7,42.3,20,41.5,20z
                            		 M26,46v-4c3.3,0,6-2.7,6-6s-2.7-6-6-6c-1.6,0-3.1,0.7-4.2,1.8c0.9,0.9,1.8,1.8,2.4,2.4c0.3,0.3,0.1,0.9-0.4,0.9h-7.3
                            		c-0.3,0-0.5-0.2-0.5-0.5v-7.3c0-0.4,0.5-0.7,0.9-0.4c0.5,0.5,1.3,1.3,2.1,2.1c1.9-1.8,4.4-2.9,7.1-2.9c5.5,0,10,4.5,10,10
                            		S31.5,46,26,46z"/>
                            	<path d="M45.5,10H33V6c0-2.2-1.8-4-4-4h-6c-2.2,0-4,1.8-4,4v4H6.5C5.7,10,5,10.7,5,11.5v3C5,15.3,5.7,16,6.5,16h39
                            		c0.8,0,1.5-0.7,1.5-1.5v-3C47,10.7,46.3,10,45.5,10z M29,10h-6V7c0-0.6,0.4-1,1-1h4c0.6,0,1,0.4,1,1V10z"/>
                            </g>
                            </svg>
                        </template>
                      </b-table>
                        <p v-else class="mt-4">No deletedShoutbox Messages found.</p>
                        <b-pagination
                          v-if="result"
                          v-model="page"
                          :total-rows="total"
                          :per-page="50"
                          aria-controls="results_table"
                          @page-click="paginate"
                        ></b-pagination>
                    </div>
                </div>
            </div>
        </div>
        <b-modal id="modal-preview" :title="'Are you sure to undelete this Post #' + this.undel_id + '?'" hide-footer>
          <b-row class="mt-3">
                <div class="col-md-12" style="font-weight: bolder">From: {{ undel_nickname }}</div>
            </b-row>
            <b-row class="mt-3">
                <div class="col-md-12">{{ undel_message }}</div>
            </b-row>
            <b-row class="mt-3">
                <div class="col-md-12"><b-form-input v-model="reason" placeholder="Enter a reason"></b-form-input></div>
            </b-row>            
            <b-row class="mt-3">
                <b-col><b-button variant="outline-success" block @click="doUndelete(undel_id)">
                    Ok - Undelete!&nbsp;<b-icon-hand-thumbs-up></b-icon-hand-thumbs-up>
                </b-button></b-col>
                <b-col><b-button variant="outline-secondary" block @click="hideModal">
                    Cancel
                </b-button></b-col>
            </b-row>
        </b-modal>
    </div>
</template>
<script>
  export default {
    props: ['user'],
      components: {

      },
      data: function() { 
          return {
            fields: [{
                  key: "id",
                  label: "#"
                },
                {
                  key: "fp",
                  label: "Fingerprint"
                },
                {
                  key: "ip",
                  label: "IP"
                },
                {
                  key: "nickname",
                  label: "From"
                },
                {
                  key: "message",
                  label: "Message"
                },
                {
                  key: "created_at",
                  label: "Created"
                },
                {
                  key: "custom_column",
                  label: "Action"
                }
              ],
              queryInfo: false,
              result: false,
              arole: window.arole,
              undel_message: "",
              undel_nickname: "",
              reason: "",
              undel_id: 0 
          }
      },
      mounted() {
        this.filter()
      },
      methods: {
          formatResult(result) {
            let results = result.map((entry) => {
              let newEntry = entry;

              return newEntry;
            });
            return results;
          },
          undelete(item) {
            let id = item.id
            this.undel_message = item.message
            this.undel_nickname = item.nickname
            this.undel_id = item.id
            this.$bvModal.show('modal-preview')
            console.log("undelete " + id + "?");
          },
          doUndelete(id) {
            console.log("undelete " + id + "!");
            this.$bvModal.hide('modal-preview')
            axios
              .post("/sbdel", {
                sbmsg: id,
                reason: this.reason
                // type: this.type,
              })
              .then(
                (response) => {
                  if (response.data.success === true) {
                    console.log("id " + id + " undeleted.")
                    this.$bvToast.toast("Sucess.", {
                        title: 'SB-Message #' + id + ' undeleted!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'success',
                    })
                    this.filter()
                  }else{
                    console.log(response.data.reason)
                    this.$bvToast.toast(response.data.reason, {
                        title: 'Error!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'alert',
                    })
                  }
                },
                (error) => {
                  this.$bvToast.toast("undefined", {
                        title: 'Error!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'alert',
                    })
                  console.log(error);
                }
              );
          },
          hover(item) {
            let el = document.getElementById("icon" + item.id);
            el.setAttribute("fill", "#438c54")
            // console.log(el);
          },
          unhover(item) {
            let el = document.getElementById("icon" + item.id);
            el.setAttribute("fill", "#839496")
            // console.log(el);
          },
          filter(page=1) {
            this.page = page;
            axios
              .post("/sbdel/filter", {
                page: this.page,
                // type: this.type,
              })
              .then(
                (response) => {
                  if (response.data.success === true) {
                    this.result = this.formatResult(response.data.data);
                    this.total = response.data.total;
                    //console.log(this.total, this.result)
                  }else{
                    console.log(response.data.reason)
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
          hideModal() {
            this.$bvModal.hide('modal-preview')
          },
      }
  }
</script>
<style scoped>
    .card{
        background-color: inherit;
    }

    .row .pagination{
        display: flex;
    }
    .formular{
        margin-left: 0.9em;
    }
    .page-link {
        height: calc(1.4em + 0.75rem + 2px);
        position: relative;
        display: block;
        padding: .5rem 1.5rem;
        margin-left: -1px;
        line-height: calc(1.4em + 0.75rem + 2px);
        vertical-align: middle;
    }
</style>
<style>
    .el-table th, .el-table tr {
        background-color: inherit!important;
    }
    .el-table, .el-table__expanded-cell {
        background-color: inherit!important;
    }

    .el-table--striped .el-table__body tr.el-table__row--striped td {
        background: inherit!important;
    }

    .el-pagination{
        margin-top: 15px;
    }

    .el-pagination button, .el-pagination button:disabled, .el-dialog, .el-pager li, .el-pagination .btn-next, .el-pagination .btn-prev {
        background: inherit;
        color: #888888!important;
    }

    .el-dialog, .el-pager li{
        color: #888888!important;
    }

    .el-table * {
        color: #888888!important;
    }

    .sc-table, .el-table, .el-table tr td, .el-table tr th, .el-table table{
        border: none;
    }

    .el-table tr td, .el-table tr th{
        cursor: pointer;
    }

    .el-table tbody tr.el-table__row:hover, .el-table tbody tr.el-table__row--striped:hover{
        background-color: #eeeeee!important;
    }

    .el-table thead tr th{
        border-bottom: 1px solid #555555;
    }

    .el-table--border::after, .el-table--group::after, .el-table::before {
        background-color: transparent;
    }
</style>
