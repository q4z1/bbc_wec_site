<template>
    <div class="gamedate-new">
        <b-row>
            <b-col>
                <label for="date">Date:</label>
                <b-form-datepicker id="date" v-model="date" class="mb-3" :min="dateMin"></b-form-datepicker>
            </b-col>
            <b-col>
                <label for="time">Time:</label>
                <b-form-timepicker id="time" v-model="time" class="mb-3"></b-form-timepicker>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-form-select v-model="step" :options="steps" class="mb-3"></b-form-select>
            </b-col>
        </b-row>
        <b-row v-if="step === 0">
            <b-col>
              <b-form-input id="input-title" v-model="title" trim  class="mb-3" placeholder="Enter a custom title"></b-form-input>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-button @click="createDate" variant="success" class="w-100">Submit</b-button>
            </b-col>
        </b-row>
    </div>
</template>
<script>
export default {
    data() {
        return {
            date: null,
            time: null,
            dateMin: null,
            time: '19:30:00',
            title: null,
            step: 1,
            steps: [
                { value: 1, text: 'Step 1' },
                { value: 2, text: 'Step 2' },
                { value: 3, text: 'Step 3' },
                { value: 4, text: 'Step 4' },
                { value: 0, text: 'custom' },
            ],
        }
    },
    computed: {
        test: function () {
            let test = null
            return test
        },
    },
    mounted() {
        let d = new Date()
        this.dateMin = this.date = d.getFullYear() + '-'  + (d.getMonth() + 1) + '-' + d.getDate() 
    },
    methods:{
        createDate(){
            let data = new FormData()
            data.append('step', this.step)
            if(this.step === 0) data.append('title', this.title)
            data.append('date', this.date + ' ' + this.time)
            axios({
                method: "post",
                url: "/registration/date/new",
                data: data,
                headers: { "Content-Type": "application/json" },
            })
            .then( (res) => {
                if(res.data.success === true){
                    this.showAlert('New Game successfuly created.', 'success')
                    this.$emit('update-dates', res.data.dates)
                }
                else{
                    this.showAlert(res.data.msg, 'danger')
                }
                this.$bvModal.hide('modal-newdate')
            })
            .catch( (res) => {
                console.log(res);
                this.showAlert('Request Error.', 'danger')
                this.$bvModal.hide('modal-newdate')
            });
        },
        showAlert(msg, variant, duration=5){
            this.$emit('show-alert', msg, variant, duration)
        },
    }
}
</script>
<style lang="scss" scoped>

</style>