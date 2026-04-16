<template>
    <div>
        <el-card style="margin-bottom:0.5rem;">
            <template #header><strong>Botfiles</strong></template>
            <div v-if="show">
                <div style="margin-bottom:0.75rem;">
                    <label class="form-label">BotFile:</label>
                    <el-select v-model="selected" @change="fetchBotFile" placeholder="Please select a Botfile" style="width:100%">
                        <el-option v-for="f in files" :key="f" :label="f" :value="f" />
                    </el-select>
                </div>
                <div v-if="content">
                    <label class="form-label">Content:</label>
                    <el-input type="textarea" v-model="content" :rows="20" />
                    <div style="padding-top:0.5rem;">
                        <el-button type="primary" @click="saveConfirm">Save File</el-button>
                        <el-button type="danger" @click="onReset">Cancel</el-button>
                    </div>
                </div>
            </div>
        </el-card>
        <el-dialog append-to-body v-model="showSaveDialog" :title="'Save Botfile ' + selected + '?'">
            Are you sure to save <strong style="color:#e6a817;">{{ selected }}</strong>?<br />
            <div style="margin-top:0.75rem;">
                <el-input v-model="reason" placeholder="Enter a reason" />
            </div>
            <template #footer>
                <el-button @click="showSaveDialog = false">Cancel</el-button>
                <el-button type="danger" @click="doSave">Yes, Save!</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
    props: ['files'],
    data() {
        return {
            selected: null,
            content: null,
            show: true,
            reason: '',
            showSaveDialog: false,
        };
    },
    methods: {
        saveConfirm() {
            this.showSaveDialog = true;
        },
        fetchBotFile() {
            if (!this.selected) return;
            this.content = null;
            axios({ method: 'post', url: '/botfiles/get', data: { file: this.selected }, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.status) { this.content = response.data.msg; }
                    else { ElMessage({ message: response.data.msg, type: 'error' }); }
                })
                .catch(() => { ElMessage({ message: 'Fetching BotFile failed!', type: 'error' }); });
        },
        doSave() {
            if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
            axios({ method: 'post', url: '/botfiles/update', data: { file: this.selected, content: this.content, reason: this.reason }, headers: { 'Content-Type': 'application/json' } })
                .then(response => {
                    if (response.data.status) {
                        ElMessage({ message: response.data.msg, type: 'success' });
                        window.location.href = window.location.origin + '/botfiles';
                    } else { ElMessage({ message: response.data.msg, type: 'error' }); }
                })
                .catch(() => { ElMessage({ message: 'Error saving BotFile!', type: 'error' }); });
            this.content = null;
            this.selected = null;
            this.showSaveDialog = false;
            this.show = false;
            this.$nextTick(() => { this.show = true; });
        },
        onReset() {
            this.content = null;
            this.selected = null;
            this.show = false;
            this.$nextTick(() => { this.show = true; });
        },
    },
};
</script>
