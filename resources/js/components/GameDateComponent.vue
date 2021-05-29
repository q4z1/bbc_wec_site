<template>
    <div class="game-date">
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
            <b-col class="col" v-if="showT">
                <b-table responsive v-if="regs" :fields="fields" :items="regs" borderless striped>
                    <template #cell(pos)="data">
                        <span class="del" v-html="data.value"></span>
                    </template>
                    <template #cell(nickname)="data">
                        <span v-html="data.value"></span>
                    </template>
                    <template #cell(action)="data">
                        <span class="text-danger float-right" @click="deleteReg" :data-id="data.value"><b-icon-trash-fill></b-icon-trash-fill></span>
                    </template>
                </b-table>
            </b-col>
        </b-row>
        <hr />
        <b-row v-if="arole !== 's'">
            <b-col>
                <registration-new-component @show-alert="showAlert" @update-dates="updateDates" v-if="s_date && !old" :date="s_date" :fp="fp"></registration-new-component>
            </b-col>
        </b-row>
        <b-row v-else>
            <b-col sm="10">
                <registration-new-component @show-alert="showAlert" @update-dates="updateDates" v-if="s_date && !old" :date="s_date" :fp="fp"></registration-new-component>
            </b-col>
            <b-col sm="2" class="d-flex justify-content-end">
                <!-- <b-button v-if="!old" variant="warning" @click="editDate" class="mr-1"><b-icon-pencil-fill></b-icon-pencil-fill></b-button> -->
                <b-button v-if="!old" variant="danger" @click="deleteDate"><b-icon-trash-fill></b-icon-trash-fill></b-button>
            </b-col>
        </b-row>
    </div>
</template>
<script>
import RegistrationNewComponent from './RegistrationNewComponent.vue'
export default {
    components: { RegistrationNewComponent },
    props: ['date', 'fp'],
    data() {
        return {
            regs: null,
            fields: [{key: 'pos'},{key: 'nickname'}],
            s_date: null,
            old: (Date.now() - 15 * 60 * 60) > new Date(this.date.date).valueOf(),
            arole: window.arole,
            alertVar: 'danger',
            alertMsg: '',
            alert: false,
            showT: true,
        }
    },
    computed: {
        test: function () {
                let test = null
                return test
        },
    },
    mounted() {
        this.s_date = this.date
        if(window.arole === 's' && !this.old){
            this.fields.push(
                {key: 'ip', label: 'IP', sortable: true},
                {key: 'fp', label: 'Fingerprint', sortable: true},
                {key: 'action', label: ''}
            )
        }
        this.regs = this.formatRegs(this.date.regs)
    },
    methods:{
        formatRegs(regs){
            let new_regs = []
            for(let i in regs){
                let reg = regs[i]
                new_regs.push({
                    id: reg.id,
                    pos: parseInt(i) + 1,
                    nickname: reg.player.nickname + ((reg.player.admin) ? ' <sup class="text-danger">Admin</sup>' : ((parseInt(reg.player.new) === 1) ? ' <sup class="text-warning">New</sup>' : '')),
                    ip: (reg.ip) ? reg.ip : 'n/a',
                    fp: (reg.fp) ? reg.fp : 'n/a',
                    action: reg.id
                })
            }
            return new_regs
        },
        showAlert(msg, variant, duration=5){
            this.$emit('show-alert', msg, variant, duration)
        },
        showAlert2(msg, variant, duration=5){
            // console.log('showAlert()', msg, variant, duration)
            this.alertVar = variant
            this.alertMsg = msg
            this.alert = duration
        },
        deleteReg(evt){
            let el = $(evt.target)
            let id = $(el).data('id')
            if(typeof id === 'undefined'){
                while(typeof id === 'undefined'){
                    el = $(el).parent()
                    id = $(el).data('id')
                }
            }
            axios.get('/registration/delete/' + id)
            .then( (res) => {
                if(res.data.success === true){
                    this.showAlert2('Registration successfuly deleted.', 'success')
                    for(let i in this.s_date.regs){
                        if(this.s_date.regs[i].id === id){
                            this.s_date.regs.splice(i, 1) 
                        }
                    }
                    this.regs = this.formatRegs(this.s_date.regs)
                    this.$emit('update-dates', res.data.dates)
                }
                else{
                    this.showAlert(res.data.msg, 'danger')
                }
            })
            .catch( (res) => {
                console.log(res);
                this.showAlert('Request Error.', 'danger')
            })
        },
        deleteDate(){
            axios.get('/registration/date/delete/' + this.date.id)
            .then( (res) => {
                if(res.data.success === true){
                    this.showAlert('Game successfuly deleted.', 'success')
                    this.$emit('update-dates', res.data.dates)
                }
                else{
                    this.showAlert(res.data.msg, 'danger')
                }
                this.$bvModal.hide('modal-date')
            })
            .catch( (res) => {
                console.log(res);
                this.showAlert('Request Error.', 'danger')
                this.$bvModal.hide('modal-date')
            })
        },
        editDate(){

        },
        updateDate(){
            let data = new FormData()
            data.append('step', this.s_date.step)
            data.append('date', this.s_date.date)
            axios({
                method: "post",
                url: "/registration/date/update/" + this.date.id,
                data: data,
                headers: { "Content-Type": "multipart/form-data" },
            })
            .then( (res) => {
                if(res.data.success === true){
                    this.showAlert('Game successfuly updated.', 'success')
                    this.$emit('update-dates', res.data.dates)
                }
                else{
                    this.showAlert(res.data.msg, 'danger')
                }
                this.$bvModal.hide('modal-date')
            })
            .catch( (res) => {
                console.log(res);
                this.showAlert('Request Error.', 'danger')
                this.$bvModal.hide('modal-date')
            })
        },
        updateDates(dates){
            for(let i in dates){
                if(dates[i].id === this.date.id){
                    this.regs = this.formatRegs(dates[i].regs)
                } 
            }
            this.$emit('update-dates', dates)
        }
    }
}
</script>
<style lang="scss">
.b-icon{
    cursor: pointer;
}
.fc-event{
    color: var(--warning);
    .fc-daygrid-event-dot{
        border-color: var(--warning);
    }
}
</style>