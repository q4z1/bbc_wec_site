<template>
  <div v-html="renderedMarkdown"></div>
</template>

<script>
import { marked } from 'marked';
export default {
  props: ['markdown'],
  computed: {
    renderedMarkdown() {
      return marked(this.markdown || '');
    },
  },
};
</script>
