<template>
  <el-card>
    <div v-if="previewSrc" style="text-align:center;margin-bottom:1rem;">
      <img :src="previewSrc" :alt="title" style="max-width:175px" />
    </div>
    <div style="margin-bottom:1rem;">
      <label class="award-label">Image File:</label>
      <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/gif" @change="genPreview" />
    </div>
    <div style="margin-bottom:1rem;">
      <label class="award-label">Title:</label>
      <el-input v-model="title" />
    </div>
    <el-button type="primary" @click="doUpload">Upload</el-button>
    <el-button type="danger" @click="doReset">Reset</el-button>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
  emits: ['update-awards', 'close-dialog'],
  data() {
    return { award_file: null, title: null, previewSrc: null };
  },
  methods: {
    doUpload() {
      if (!this.award_file || !this.title) {
        ElMessage({ message: 'Please choose an image and enter a title.', type: 'error' });
        return;
      }
      const data = new FormData();
      data.append('title', this.title);
      data.append('award', this.award_file);
      axios.post('/awards/upload', data).then((res) => {
        if (res.data.success === true) {
          this.$emit('update-awards', res.data.awards);
          this.doReset();
          this.$emit('close-dialog');
        } else {
          ElMessage({ message: 'Upload failed.', type: 'error' });
        }
      });
    },
    doReset() {
      this.award_file = null;
      this.title = null;
      this.previewSrc = null;
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
    },
    genPreview(e) {
      const file = e.target.files[0];
      if (!file) { this.award_file = null; this.previewSrc = null; return; }
      this.award_file = file;
      const reader = new FileReader();
      reader.onload = (ev) => { this.previewSrc = ev.target.result; };
      reader.readAsDataURL(file);
    },
  },
};
</script>
<style scoped>
.award-label {
  display: block;
  margin-bottom: 0.35rem;
  font-size: 0.875rem;
  color: var(--el-text-color-regular);
}
</style>
