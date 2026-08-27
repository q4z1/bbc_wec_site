<template>
  <div>
    <div v-html="renderedMarkdown" @click="onContentClick" class="lightbox-content"></div>
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
  props: ['markdown'],
  computed: {
    renderedMarkdown() {
      return marked(this.markdown || '');
    },
  },
};
</script>

<style scoped>
.lightbox-content :deep(img) {
  cursor: zoom-in;
}
</style>
