<template>
    <div class="register">
        <b-row v-if="alert">
            <b-col>
                <b-alert
                    :show="alert"
                    dismissible
                    :variant="alertVar"
                    @dismissed="alert = false"
                >
                {{ alertMsg }}
                </b-alert>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="d-flex justify-content-center"><b-button v-b-toggle.collapse-reg variant="primary" class=" w-100">Register</b-button></b-col>
        </b-row>
        <div class="row">
            <div class="col">
                <b-collapse id="collapse-reg" class="mt-3" v-model="showReg">
                    <b-card>
                        <b-form-group
                        id="fieldset-1"
                        label="Enter your PokerTH nickname:"
                        label-for="input-1"
                        >
                            <b-form-input id="input-1" v-model="nickname" trim></b-form-input>
                        </b-form-group>
                        <b-button variant="success" @click="register">Submit</b-button>
                    </b-card>
                </b-collapse>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['date', 'fp'],
    data() {
        return {
            nickname: null,
            showReg: false,
            alertVar: 'danger',
            alertMsg: '',
            alert: false
        }
    },
    methods: {
        register(){
            let data = new FormData()
            data.append('fp', this.fp)
            data.append('nickname', this.nickname)
            axios({
                method: "post",
                url: "/registration/register/" + this.date.id,
                data: data,
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then( (res) => {
                if(res.data.success === true){
                    this.alertMgs = 'Successfully registered.'
                    this.alertVar = 'success'
                    this.$emit('update-dates', res.data.dates)
                }
                else{
                    this.showAlert(res.data.msg, 'danger')
                }
                this.showReg = false
                this.nickname = null
            })
            .catch( (res) => {
                console.log(res);
                this.showAlert('Request Error.', 'danger')
                this.showReg = false
                this.nickname = null
            });
        },
        showAlert(msg, variant, duration=5){
            this.alertVar = variant
            this.alertMsg = msg
            this.alert = duration
        },
    },
}
</script>