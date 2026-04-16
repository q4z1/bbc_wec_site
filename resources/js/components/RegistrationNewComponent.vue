<template>
    <div class="register">
        <el-alert v-if="alert" :title="alertMsg" :type="alertTypeEl" show-icon closable @close="alert = false" class="mb-3" />
        <div v-if="date.id != 6493" class="d-flex justify-content-center mb-2">
            <el-button type="primary" @click="showReg = !showReg" style="width:100%">Register</el-button>
        </div>
        <div v-if="showReg" class="mt-3">
            <el-card>
                <div class="mb-3">
                    <label class="form-label">Enter your PokerTH nickname:</label>
                    <el-input v-model="nickname" />
                </div>
                <el-button type="success" @click="register">Submit</el-button>
            </el-card>
        </div>
    </div>
</template>
<script>
export default {
    props: ['date', 'fp'],
    emits: ['show-alert', 'update-dates'],
    data() {
        return {
            nickname: null,
            showReg: false,
            alertVar: 'danger',
            alertMsg: '',
            alert: false,
        };
    },
    computed: {
        alertTypeEl() {
            const map = { danger: 'error', success: 'success', warning: 'warning', info: 'info' };
            return map[this.alertVar] || 'error';
        },
    },
    methods: {
        register() {
            const data = new FormData();
            data.append('fp', this.fp);
            data.append('nickname', this.nickname);
            axios({ method: 'post', url: '/registration/register/' + this.date.id, data, headers: { 'Content-Type': 'application/json' } })
                .then((res) => {
                    if (res.data.success === true) {
                        this.alertMsg = 'Successfully registered.';
                        this.alertVar = 'success';
                        this.alert = true;
                        this.$emit('update-dates', res.data.dates);
                    } else {
                        this.showAlert(res.data.msg, 'danger');
                    }
                    this.showReg = false;
                    this.nickname = null;
                })
                .catch((res) => {
                    console.log(res);
                    this.showAlert('Request Error.', 'danger');
                    this.showReg = false;
                    this.nickname = null;
                });
        },
        showAlert(msg, variant, duration = 5) {
            this.alertVar = variant;
            this.alertMsg = msg;
            this.alert = duration;
        },
    },
};
</script>
