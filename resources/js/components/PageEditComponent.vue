<template>
  <div v-if="pedit">
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;">
      <div style="flex:1;min-width:280px;">
        <div v-if="pedit.slug !== 'home'" style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
          <div style="flex:1;"><el-input v-model="pedit.title" placeholder="Title" size="small" /></div>
          <div style="flex:1;"><el-input v-model="pedit.slug" placeholder="Slug" size="small" /></div>
          <div style="display:flex;align-items:center;gap:0.5rem;">
            <el-tooltip content="Order"><el-icon style="font-size:1.5rem;"><arrow-down /></el-icon></el-tooltip>
            <el-input-number v-model="pedit.order" :min="0" size="small" style="width:80px" />
            <el-tooltip content="Published">
              <el-icon v-if="pedit.active == 0" style="font-size:1.5rem;color:#909399;cursor:pointer" @click="pedit.active = 1"><Hide /></el-icon>
              <el-icon v-else style="font-size:1.5rem;color:#409eff;cursor:pointer" @click="pedit.active = 0"><View /></el-icon>
            </el-tooltip>
          </div>
        </div>
      </div>
      <div style="flex:1;min-width:280px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span><strong>Title:</strong> {{ pedit.title }}</span>
          <span><strong>Slug:</strong> <a :href="'/page/'+pedit.slug" target="_blank">/page/{{ pedit.slug }}</a></span>
          <div>
            <el-button size="small" type="success" @click="saveConfirm">Save</el-button>
            <el-button size="small" type="warning" @click="$emit('close-me', data)"><el-icon><CircleClose /></el-icon></el-button>
          </div>
        </div>
      </div>
    </div>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
      <div style="flex:1;min-width:280px;">
        <el-input type="textarea" v-model="pedit.markdown" :rows="24" placeholder="Markdown content..." />
      </div>
      <div style="flex:1;min-width:280px;">
        <div style="border:1px solid var(--el-border-color);border-radius:4px;padding:0.5rem;overflow:auto;height:590px;" v-html="renderedMarkdown"></div>
      </div>
    </div>

    <el-dialog append-to-body v-model="showSave" :title="'Save Page ' + pedit.title + '?'" width="420px">
      Are you sure to save <strong style="color:#e6a817;">{{ pedit.title }}</strong>?
      <div style="margin-top:0.75rem;"><el-input v-model="reason" placeholder="Enter a reason" /></div>
      <template #footer>
        <el-button @click="showSave = false">Cancel</el-button>
        <el-button type="primary" @click="save">Yes, Save!</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import { marked } from 'marked';
import { ElMessage } from 'element-plus';
export default {
  props: ['page', 'data'],
  emits: ['close-me'],
  data() {
    return { pedit: null, reason: '', showSave: false };
  },
  computed: {
    renderedMarkdown() {
      return marked(this.pedit?.markdown || '');
    },
  },
  mounted() {
    this.pedit = { ...this.page };
  },
  methods: {
    saveConfirm() { this.showSave = true; },
    save() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      ['order','active','markdown','title','slug'].forEach((k) => data.append(k, this.pedit[k]));
      data.append('reason', this.reason);
      if (this.pedit.id) data.append('id', this.pedit.id);
      axios.post('/page', data).then((res) => {
        if (res.data.status === true) window.location.reload();
      });
    },
  },
};
</script>
