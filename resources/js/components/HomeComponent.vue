<template>
  <div>
    <vue-markdown :html="true" :source="markdown"></vue-markdown>
  </div>
</template>
<script>
import VueMarkdown from 'vue-markdown'
export default {
  components: {
    VueMarkdown,
  },
  props: ['markdown'],
  mounted() {},
};
</script>