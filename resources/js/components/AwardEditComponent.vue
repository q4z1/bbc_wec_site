<template>
  <el-card>
    <div v-if="award" class="text-center mb-3">
      <div class="position-relative d-inline-block">
        <el-icon v-if="!replace" class="text-warning position-absolute" style="bottom:8px;right:8px;cursor:pointer;font-size:1.4rem" @click="replace=true"><EditPen /></el-icon>
        <img :src="award_preview" :alt="title" style="max-width:175px" />
      </div>
      <div class="text-primary mt-2">{{ title }}</div>
    </div>
    <el-input v-model="reason" placeholder="Enter a reason" class="mb-3" />
    <template v-if="replace">
      <div class="mb-3">
        <label class="form-label">Image File:</label>
        <input type="file" class="form-control" accept="image/jpeg,image/png,image/gif" @change="genPreview" />
      </div>
    </template>
    <div class="mb-3">
      <label class="form-label">Title:</label>
      <el-input v-model="title" />
    </div>
    <el-button type="primary" @click="doUpload">Submit</el-button>
    <el-button type="danger" @click="$emit('close-dialog')">Cancel</el-button>
  </el-card>
</template>
<script>
import { ElMessage } from 'element-plus';
export default {
  props: ['award'],
  emits: ['update-awards', 'close-dialog'],
  data() {
    return { award_file: null, title: null, award_preview: null, replace: false, reason: '' };
  },
  mounted() {
    this.title = this.award.title;
    this.award_preview = this.award.award;
  },
  methods: {
    doUpload() {
      if (!this.reason) { ElMessage({ message: 'Please enter a reason!', type: 'error' }); return; }
      const data = new FormData();
      data.append('title', this.title);
      data.append('award', this.award_file);
      data.append('reason', this.reason);
      axios.post('/awards/edit/' + this.award.id, data).then((res) => {
        if (res.data.success) {
          this.$emit('update-awards', res.data.awards);
          this.$emit('close-dialog');
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
