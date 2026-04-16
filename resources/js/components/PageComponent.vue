<template>
  <div>
    <h3>{{ page.title }}</h3>
    <el-row>
      <el-col><div v-html="renderedMarkdown"></div></el-col>
    </el-row>
  </div>
</template>
<script>
import { marked } from 'marked';
export default {
  props: ['page'],
  computed: {
    renderedMarkdown() {
      return marked(this.page?.markdown || '');
    },
  },
};
</script>
