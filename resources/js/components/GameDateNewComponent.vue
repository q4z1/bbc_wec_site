<template>
    <div class="gamedate-new">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Date:</label>
                <el-date-picker v-model="date" type="date" format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                    :disabled-date="(d) => d < new Date(dateMin)" style="width:100%" />
            </div>
            <div class="col">
                <label class="form-label">Time:</label>
                <el-time-picker v-model="time" format="HH:mm:ss" value-format="HH:mm:ss" style="width:100%" />
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <el-select v-model="step" style="width:100%">
                    <el-option v-for="s in steps" :key="s.value" :label="s.text" :value="s.value" />
                </el-select>
            </div>
        </div>
        <div class="row mb-3" v-if="step === 0">
            <div class="col">
                <el-input v-model="title" placeholder="Enter a custom title" />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <el-button type="success" @click="createDate" style="width:100%">Submit</el-button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    emits: ['show-alert', 'update-dates', 'close-dialog'],
    data() {
        return {
            date: null,
            time: '19:30:00',
            dateMin: null,
            title: null,
            step: 1,
            steps: [
                { value: 1, text: 'Step 1' },
                { value: 2, text: 'Step 2' },
                { value: 3, text: 'Step 3' },
                { value: 4, text: 'Step 4' },
                { value: 0, text: 'custom' },
            ],
        };
    },
    mounted() {
        const d = new Date();
        this.dateMin = this.date = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    },
    methods: {
        createDate() {
            const data = new FormData();
            data.append('step', this.step);
            if (this.step === 0) data.append('title', this.title);
            data.append('date', this.date + ' ' + this.time);
            axios({ method: 'post', url: '/registration/date/new', data, headers: { 'Content-Type': 'application/json' } })
                .then((res) => {
                    if (res.data.success === true) {
                        this.showAlert('New Game successfully created.', 'success');
                        this.$emit('update-dates', res.data.dates);
                    } else {
                        this.showAlert(res.data.msg, 'danger');
                    }
                    this.$emit('close-dialog');
                })
                .catch((res) => {
                    console.log(res);
                    this.showAlert('Request Error.', 'danger');
                    this.$emit('close-dialog');
                });
        },
        showAlert(msg, variant, duration = 5) {
            this.$emit('show-alert', msg, variant, duration);
        },
    },
};
</script>
