<template>
    <div>
        <el-card class="mb-2 mt-2">
            <template #header><strong>Edit Game</strong></template>
            <div class="row">
                <div class="col-12 mb-2">
                    <label class="form-label">Game-Number:</label>
                    <el-input v-model="form.gameno" />
                    <label class="form-label mt-2">Date/Time:</label>
                    <div class="row">
                        <div class="col">
                            <el-date-picker v-model="form.date" type="date" format="YYYY-MM-DD" value-format="YYYY-MM-DD" style="width:100%" />
                        </div>
                        <div class="col">
                            <el-time-picker v-model="form.time" format="HH:mm:ss" value-format="HH:mm:ss" style="width:100%" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <el-input v-model="form.reason" placeholder="Enter a reason" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <el-button type="primary" @click="$emit('back')">Cancel</el-button>
                    </div>
                    <div class="col text-end">
                        <el-button type="success" @click="saveGame">Save</el-button>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
    props: ['game'],
    emits: ['back', 'update'],
    data() {
        return {
            form: {
                gametype: this.game.type,
                gameno: this.game.number,
                date: this.game.started.slice(0, 10),
                time: this.game.started.slice(11, 19),
                reason: '',
            },
        };
    },
    methods: {
        saveGame() {
            if (!this.form.reason) {
                ElMessage({ message: 'Please enter a reason!', type: 'error' });
                return;
            }
            axios({ method: 'post', url: '/update/game/' + this.game.number, data: this.form, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.status) {
                        ElMessage({ message: response.data.msg, type: 'success' });
                        this.$emit('update');
                        this.$emit('back');
                    } else {
                        ElMessage({ message: response.data.msg, type: 'error' });
                    }
                })
                .catch(() => { ElMessage({ message: 'Game saving failed!', type: 'error' }); });
        },
    },
};
</script>
<style lang="scss" scoped>
label { font-size: 16px; font-weight: bold; }
</style>
