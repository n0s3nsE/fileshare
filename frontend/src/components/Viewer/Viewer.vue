<template>
  <div>
    <img :src="view_api_url + '/' + param_id" />
  </div>
</template>
<script>
export default {
  props: ["param"],
  data() {
    return {
      param_id: null,
      view_api_url: "http://127.0.0.1:8000/api/preview",
    };
  },
  created() {
    this.param_id = this.param.split("&")[0].split("=")[1];
  },
};
</script>
