<template>
  <el-card>
    <div v-if="award" style="text-align:center;margin-bottom:1rem;">
      <div class="award-preview">
        <el-icon v-if="!replace" class="award-replace" @click="replace = true"><EditPen /></el-icon>
        <img :src="award_preview" :alt="title" />
      </div>
      <div style="color:var(--el-color-primary);margin-top:0.5rem;">{{ title }}</div>
    </div>
    <hr />
    <div v-if="replace" style="margin-bottom:1rem;">
      <label class="award-label">Image File:</label>
      <input type="file" accept="image/jpeg,image/png,image/gif" @change="genPreview" />
    </div>
    <div style="margin-bottom:1rem;">
      <label class="award-label">Title:</label>
      <el-input v-model="title" />
    </div>
    <el-button type="primary" @click="doUpload">Submit</el-button>
    <el-button type="danger" @click="$emit('close-dialog')">Cancel</el-button>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus/es/components/message/index';

export default {
  props: ['award'],
  emits: ['update-awards', 'close-dialog'],
  data() {
    return { award_file: null, title: null, award_preview: null, replace: false };
  },
  mounted() {
    this.title = this.award.title;
    this.award_preview = this.award.award;
  },
  methods: {
    doUpload() {
      const data = new FormData();
      data.append('title', this.title);
      // Nur mitschicken, wenn wirklich ein neues Bild gewaehlt wurde - sonst
      // wuerde der Controller die vorhandene Datei mit "null" ueberschreiben.
      if (this.award_file) data.append('award', this.award_file);
      axios.post('/awards/edit/' + this.award.id, data).then((res) => {
        if (res.data.success === true) {
          this.$emit('update-awards', res.data.awards);
          this.$emit('close-dialog');
        } else {
          ElMessage({ message: 'Saving the award failed.', type: 'error' });
        }
      });
    },
    genPreview(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.award_file = file;
      const reader = new FileReader();
      reader.onload = (ev) => { this.award_preview = ev.target.result; };
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

.award-preview {
  position: relative;
  display: inline-block;
}
.award-preview img {
  display: block;
  max-width: 175px;
  height: auto;
}
.award-replace {
  position: absolute;
  bottom: 8px;
  right: 8px;
  cursor: pointer;
  font-size: 1.4rem;
  color: var(--el-color-warning);
  filter: brightness(80%);
}
.award-replace:hover { filter: brightness(100%); }
</style>
