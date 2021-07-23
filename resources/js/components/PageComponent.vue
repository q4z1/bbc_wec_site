<template>
  <div>
    <h3>{{ page.title }}</h3>
    <el-row>
      <el-col><vue-markdown :html="true">{{ page.markdown }}</vue-markdown></el-col>
    </el-row>
  </div>
</template>
<script>
import VueMarkdown from 'vue-markdown'
export default {
  props: ["page"],
  components: {
    VueMarkdown,
  },
  data() {
    return {

    };
  },
  mounted() {},
};
</script>