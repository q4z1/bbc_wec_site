<template>
  <el-card>
    <div v-if="preview" class="text-center mb-3">
      <img :id="'preview'" :src="previewSrc" alt="preview" style="max-width:175px" />
    </div>
    <div class="mb-3">
      <label class="form-label">Image File:</label>
      <input type="file" class="form-control" accept="image/jpeg,image/png,image/gif"
        @change="genPreview" ref="fileInput" />
    </div>
    <div class="mb-3">
      <label class="form-label">Title:</label>
      <el-input v-model="title" />
    </div>
    <el-button type="primary" @click="doUpload">Upload</el-button>
    <el-button type="danger" @click="doReset">Reset</el-button>
  </el-card>
</template>
<script>
export default {
  emits: ['update-awards', 'close-dialog'],
  data() {
    return { award_file: null, title: null, preview: false, previewSrc: null };
  },
  methods: {
    doUpload() {
      if (!this.award_file || !this.title) return;
      const data = new FormData();
      data.append('title', this.title);
      data.append('award', this.award_file);
      axios.post('/awards/upload', data).then((res) => {
        if (res.data.success) {
          this.$emit('update-awards', res.data.awards);
          this.doReset();
          this.$emit('close-dialog');
        }
      });
    },
    doReset() {
      this.award_file = null; this.title = null; this.preview = false; this.previewSrc = null;
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
    },
    genPreview(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.award_file = file;
      const reader = new FileReader();
      reader.onload = (ev) => { this.previewSrc = ev.target.result; this.preview = true; };
      reader.readAsDataURL(file);
    },
  },
};
</script>
