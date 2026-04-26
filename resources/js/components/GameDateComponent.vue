<template>
    <div class="game-date">
        <el-alert v-if="alert" :title="alertMsg" :type="alertType" show-icon closable @close="alert = false" class="mb-3" />
        <div v-for="table in tables" :key="table">
            <strong class="text-success"><u>Table {{ table }}</u></strong>
            <div class="row" v-if="showT">
                <div class="col">
                    <el-table v-if="regs[table]" :data="regs[table]" stripe size="small" style="width:100%">
                        <el-table-column prop="pos" label="Pos" width="60" />
                        <el-table-column label="Nickname">
                            <template #default="{ row }">
                                <span v-html="row.nickname"></span>
                            </template>
                        </el-table-column>
                        <el-table-column v-if="s_date && s_date.step > 1" prop="tickets" label="Tickets" width="80" />
                        <el-table-column v-if="arole === 's'" prop="ip" label="IP" />
                        <el-table-column v-if="arole === 'a' || arole === 's'" prop="fp" label="Fingerprint" />
                        <el-table-column v-if="showAction" label="" width="50">
                            <template #default="{ row }">
                                <el-icon v-if="parseInt(row.action) > 0" class="text-danger"
                                    style="cursor:pointer" @click="deleteReg(row.action)">
                                    <Delete />
                                </el-icon>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
            </div>
        </div>
        <hr />
        <div class="row" v-if="arole !== 'a' && arole !== 's'">
            <div class="col">
                <registration-new-component @show-alert="showAlert" @update-dates="updateDates"
                    v-if="s_date && !old" :date="s_date" :fp="fp" />
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-10">
                <registration-new-component @show-alert="showAlert" @update-dates="updateDates"
                    v-if="s_date && !old" :date="s_date" :fp="fp" />
            </div>
            <div class="col-2 d-flex justify-content-end">
                <el-button v-if="!old" type="danger" @click="deleteDate">
                    <el-icon><Delete /></el-icon>
                </el-button>
            </div>
        </div>
    </div>
</template>
<script>
import RegistrationNewComponent from './RegistrationNewComponent.vue';
export default {
    components: { RegistrationNewComponent },
    props: ['date', 'utcDate', 'fp'],
    emits: ['show-alert', 'update-dates', 'close-dialog'],
    data() {
        return {
            regs: [],
            s_date: null,
            old: null,
            arole: window.arole,
            alertType: 'danger',
            alertMsg: '',
            alert: false,
            showT: true,
            tables: 1,
        };
    },
    computed: {
        showAction() {
            return ['u', 'a', 's'].indexOf(this.arole) !== -1 && !this.old;
        },
        alertTypeEl() {
            const map = { danger: 'error', success: 'success', warning: 'warning', info: 'info' };
            return map[this.alertType] || 'error';
        },
    },
    mounted() {
        this.s_date = this.date;
        const gameUTC = new Date(this.utcDate).valueOf();
        const offset = new Date().getTimezoneOffset();
        const localUTC = Date.now() + offset * 60 * 1000;
        this.old = localUTC > gameUTC;
        this.tables = Math.ceil(this.date.regs.length / 10);
        let k = 1;
        for (let i = 0; i < this.date.regs.length; i += 10) {
            this.regs[k] = this.formatRegs(this.date.regs.slice(i, i + 10));
            k++;
        }
    },
    methods: {
        formatRegs(regs) {
            return regs.map((reg, i) => {
                let nick = 'Player deleted', admin = false, n = 0, owner = false, tickets = 'n/a';
                if (reg.player !== null) {
                    nick = reg.player.nickname;
                    admin = reg.player.admin;
                    n = parseInt(reg.player.new);
                    owner = reg.player.owner;
                    if (this.date.step === 2) tickets = reg.player.s2_tickets;
                    else if (this.date.step === 3) tickets = reg.player.s3_tickets;
                    else if (this.date.step === 4) tickets = reg.player.s4_tickets;
                }
                return {
                    id: reg.id,
                    pos: i + 1,
                    nickname: nick + (admin ? ' <sup class="text-danger">Admin</sup>' : (n === 1 ? ' <sup class="text-warning">New</sup>' : '')),
                    ip: reg.ip || 'n/a',
                    fp: reg.fp || 'n/a',
                    action: owner ? reg.id : 0,
                    tickets,
                };
            });
        },
        showAlert(msg, variant, duration = 5) {
            this.$emit('show-alert', msg, variant, duration);
        },
        showAlert2(msg, variant, duration = 5) {
            const map = { danger: 'error', success: 'success', warning: 'warning', info: 'info' };
            this.alertType = map[variant] || 'error';
            this.alertMsg = msg;
            this.alert = true;
        },
        deleteReg(id) {
            axios.get('/registration/delete/' + id)
                .then((res) => {
                    if (res.data.success === true) {
                        this.showAlert2('Registration successfully deleted.', 'success');
                        this.s_date.regs = this.s_date.regs.filter(r => r.id !== id);
                        this.tables = Math.ceil(this.s_date.regs.length / 10);
                        this.regs = [];
                        let k = 1;
                        for (let i = 0; i < this.s_date.regs.length; i += 10) {
                            this.regs[k] = this.formatRegs(this.s_date.regs.slice(i, i + 10));
                            k++;
                        }
                        this.$emit('update-dates', res.data.dates);
                    } else {
                        this.showAlert(res.data.msg, 'danger');
                    }
                })
                .catch((res) => { console.log(res); this.showAlert('Request Error.', 'danger'); });
        },
        deleteDate() {
            axios.get('/registration/date/delete/' + this.date.id)
                .then((res) => {
                    if (res.data.success === true) {
                        this.showAlert('Game successfully deleted.', 'success');
                        this.$emit('update-dates', res.data.dates);
                    } else { this.showAlert(res.data.msg, 'danger'); }
                    this.$emit('close-dialog');
                })
                .catch((res) => { console.log(res); this.showAlert('Request Error.', 'danger'); this.$emit('close-dialog'); });
        },
        updateDates(dates) {
            this.showAlert('Successfully registered.', 'success');
            this.$emit('close-dialog');
            this.$emit('update-dates', dates);
        },
    },
};
</script>
<style lang="scss" scoped>
.el-icon { cursor: pointer; }

</style>
