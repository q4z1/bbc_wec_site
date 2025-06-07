<template>
    <div>
        <b-card title="Botfiles" class="mb-2">
            <b-card-text>
                <b-form v-if="show">
                    <b-form-group id="input-group-1" label="BotFile:" label-for="input-1">
                        <b-form-select
                        id="input-1"
                        v-model="selected"
                        :options="files"
                        @change="fetchBotFile">
                        </b-form-select>
                    </b-form-group>
                    <b-form-group id="input-group-2" label="Content:" label-for="input-2" v-if="content">
                      <b-form-textarea
                        id="textarea"
                        v-model="content"
                        rows="20"
                        max-rows="20"
                      ></b-form-textarea>
                      <div class="pt-2">
                        <b-button variant="primary" @click="saveConfirm">Save File</b-button>&nbsp;
                        <b-button variant="danger" @click="onReset">Cancel</b-button>
                      </div>
                    </b-form-group>
                </b-form>
            </b-card-text>
        </b-card>
    <b-modal ref="save" id="save" :title="'Save Botfile ' + selected + '?'" ok-disabled hide-footer>
        Are you sure to save Page <strong class="text-warning">{{ selected }}</strong>?<br />
        <b-row class="mt-3">
            <div class="col-md-12"><b-form-input v-model="reason" placeholder="Enter a reason"></b-form-input></div>
        </b-row> 
        <b-button class="mt-3" variant="outline-info" block @click="$refs['save'].hide()">Cancel</b-button>
        <b-button class="mt-2" variant="outline-danger" block @click="doSave">Yes, Save!</b-button>
    </b-modal>
    </div>
</template>
<script>
export default {
    props: ['files'],
    data() {
        return {
            selected: "Please select a Botfile",
            content: null,
            arole: window.arole,
            show: true,
            reason: ""
        }
    },
    mounted() {

    },
    created() {

    },
    methods: {
        saveConfirm(){
          this.$nextTick(() => {
              this.$refs['save'].show()
          })
        },
        fetchBotFile(){
          if(this.selected == "Please select a Botfile") return
          this.content = null;
          axios({
                method: 'post',
                url: '/botfiles/get',
                data: { file: this.selected },
                headers: {'Content-Type': 'application/json' }
                })
                .then(response => {
                    if(response.data.status){
                      this.content = response.data.msg
                    }else{
                      this.$bvToast.toast(response, {
                        title: response.data.msg,
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'danger',
                    })
                    }
                })
                .catch(response => {
                    this.$bvToast.toast(response, {
                        title: 'Fetching BotFile failed!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'danger',
                    })
                });
        },
        doSave() {
            if(this.reason === ""){
              this.$bvToast.toast("Please enter a reason!", {
                            title: 'Error!',
                            autoHideDelay: 3000,
                            appendToast: true,
                            variant: 'danger',
                        })
              return false;
            }
            axios({
                method: 'post',
                url: '/botfiles/update',
                data: { file: this.selected, content: this.content, reason: this.reason },
                headers: {'Content-Type': 'application/json' }
                })
                .then(response => {
                  if(response.data.status){
                        this.$bvToast.toast(response.data.msg, {
                            title: 'Success!',
                            autoHideDelay: 5000,
                            appendToast: true,
                            variant: 'success',
                        })
                        window.location.href = window.location.origin + "/botfiles";
                    }else{
                        this.$bvToast.toast(response.data.msg, {
                            title: 'Error!',
                            autoHideDelay: 5000,
                            appendToast: true,
                            variant: 'danger',
                        })
                    }
                })
                .catch(response => {
                    this.$bvToast.toast(response.data.msg, {
                        title: 'Error!',
                        autoHideDelay: 5000,
                        appendToast: true,
                        variant: 'danger',
                    })
                 })
                this.content = null
                this.selected = "Please select a Botfile"
                this.show = false
                this.$nextTick(() => {
                    this.show = true
                })
        },
        onReset(evt) {
            // Reset our form values
            // Trick to reset/clear native browser form validation state
            this.content = null
            this.selected = "Please select a Botfile"
            this.show = false
            this.$nextTick(() => {
                this.show = true
            })
        },
        hideModal() {
            this.$bvModal.hide('modal-preview')
            this.form.preview = true
        },
    }
}
</script>
<style lang="scss" scoped>

</style>