<template>
  <div>
    <h3>{{ page.title }}</h3>
    <el-row>
      <el-col><div v-html="renderedMarkdown" @click="onContentClick" style="cursor:default;" class="lightbox-content"></div></el-col>
    </el-row>
    <el-image-viewer
      v-if="lightboxVisible"
      :url-list="lightboxUrls"
      :initial-index="lightboxIndex"
      @close="closeLightbox"
    />
  </div>
</template>
<script>
import { marked } from 'marked';
import { ElImageViewer } from 'element-plus';
import imageLightbox from '../mixins/imageLightbox';
export default {
  components: { ElImageViewer },
  mixins: [imageLightbox],
  props: ['page'],
  computed: {
    renderedMarkdown() {
      return marked(this.page?.markdown || '');
    },
  },
};
</script>

<style scoped>
.lightbox-content :deep(img) {
  cursor: zoom-in;
}
</style>
